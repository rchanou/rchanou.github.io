//This controller is responsible for fetching and presenting data related to Live Timing.
clubSpeedOnlineApp.controller('liveTimingController', function($scope, $routeParams, $location, ClubSpeedJSONService) {
    $scope.spinnerActive = 1;

    ClubSpeedJSONService.getSettings().success(function (data) {
        for(var i = 0; i < Object.size(data.settings); i++)
        {
            if ((data.settings[i].name == 'defaultTrack'))
            {
                $scope.defaultTrack = data.settings[i].value;
                break;
            }
        }

        if (typeof config !== "undefined") //Check if the default track is overridden in the config file
        {
            if (typeof config.forceDefaultTrackIgnoreAdminPanel !== "undefined")
            {
                $scope.defaultTrack = config.forceDefaultTrackIgnoreAdminPanel;
            }
        }

        $scope.currentTrackId = defaultFor($routeParams.desiredTrack,defaultFor($scope.defaultTrack,1));
        $routeParams.desiredTable = defaultFor($routeParams.desiredTable,"fastestTimeByWeek");

        ClubSpeedJSONService.getTracks().success(function (data) {
            $scope.tracks = data.tracks;

            for(var i = 0; i < Object.size($scope.tracks); i++)
            {
                if ($scope.tracks[i].id == $scope.currentTrackId)
                {
                    $scope.currentTrackName = $scope.tracks[i].name;
                    break;
                }
            }

        });

        //If Top RPM Scores is desired, switch to that mode and fetch that data
        if ($routeParams.desiredTable == "topRPMScores")
        {
            $scope.tableType = "topRPMTable";
            $scope.tableCaption = "Top ProSkill Scores";
            ClubSpeedJSONService.getTopRPMScores().success(function (data) {
                $scope.jsonData = data;
                $scope.spinnerActive = 0;
            });
        }
        //If Fastest Times (Month) is desired, switch to that mode and fetch that data
        else if ($routeParams.desiredTable == "fastestTimeByMonth")
        {
            $scope.tableType = "fastestTimesTable";
            $scope.tableCaption = "Fastest Times This Month";
            ClubSpeedJSONService.getFastestLapTimes_Month($scope.currentTrackId).success(function (data) {
                $scope.jsonData = data;
                $scope.spinnerActive = 0;
            });

        }
        //If Fastest Times (Week) is desired, switch to that mode and fetch that data
        else if ($routeParams.desiredTable == "fastestTimeByWeek")
        {
            $scope.tableType = "fastestTimesTable";
            $scope.tableCaption = "Fastest Times This Week";
            ClubSpeedJSONService.getFastestLapTimes_Week($scope.currentTrackId).success(function (data) {
                $scope.jsonData = data;
                $scope.spinnerActive = 0;
            });

        }
        //If Fastest Times (Day) is desired, switch to that mode and fetch that data
        else if ($routeParams.desiredTable == "fastestTimeByDay")
        {
            $scope.tableType = "fastestTimesTable";
            $scope.tableCaption = "Fastest Times Today";
            ClubSpeedJSONService.getFastestLapTimes_Day($scope.currentTrackId).success(function (data) {
                $scope.jsonData = data;
                $scope.spinnerActive = 0;
            });

        }

    });

    //Used to route to specific racer page - happens when a racer is clicked
    $scope.go = function ( racer_id ) {
        $location.path( '/racersearch/' + racer_id );
    };

    //Used to route to a specific live timing table
    $scope.goToLiveTiming = function ( desiredTable ) {
        $location.path( '/livetiming/' + desiredTable + '/' + $scope.currentTrackId );
    };

    //Used to route to a specific track
    $scope.goToLiveTimingTrack = function ( desiredTrack ) {
        $location.path( '/livetiming/' + $routeParams.desiredTable + '/' + desiredTrack );
    };

    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };
});