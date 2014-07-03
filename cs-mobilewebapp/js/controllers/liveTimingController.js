//This controller is responsible for fetching and presenting data related to Live Timing.
clubSpeedOnlineApp.controller('liveTimingController', function($scope, $routeParams, $location, ClubSpeedJSONService) {
    //This function routes to the racer profile page for the given racer_id
    $scope.go = function ( racer_id ) {
        $location.path( '/racersearch/' + racer_id );
    };

    //This function routes to the desired live timing data given in desiredTable
    $scope.goToLiveTiming = function ( desiredTable ) {
        if (desiredTable == 'liveScoreboard')
        {
            $location.path('/livescoreboard');
        }
        else
        {
            $location.path( '/livetiming/' + desiredTable );
        }
    };

    $scope.spinnerActive = 1;

    //If Top RPM Scores is desired, switch to that mode and fetch that data
    if ($routeParams.desiredTable == "topRPMScores")
    {
        $scope.tableType = "topRPMTable";
        $scope.tableCaption = "Top RPM Scores";
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
        ClubSpeedJSONService.getFastestLapTimes_Month().success(function (data) {
            $scope.jsonData = data;
            $scope.spinnerActive = 0;
        });

    }
    //If Fastest Times (Week) is desired, switch to that mode and fetch that data
    else if ($routeParams.desiredTable == "fastestTimeByWeek")
    {
        $scope.tableType = "fastestTimesTable";
        $scope.tableCaption = "Fastest Times This Week";
        ClubSpeedJSONService.getFastestLapTimes_Week().success(function (data) {
            $scope.jsonData = data;
            $scope.spinnerActive = 0;
        });

    }
    //If Fastest Times (Day) is desired, switch to that mode and fetch that data
    else if ($routeParams.desiredTable == "fastestTimeByDay")
    {
        $scope.tableType = "fastestTimesTable";
        $scope.tableCaption = "Fastest Times Today";
        ClubSpeedJSONService.getFastestLapTimes_Day().success(function (data) {
            $scope.jsonData = data;
            $scope.spinnerActive = 0;
        });

    }

});