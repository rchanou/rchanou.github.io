//This controller is used for the race summary page, and handles Google Graph creation and displaying of tables
clubSpeedOnlineApp.controller('raceSummaryController', function($scope, $rootScope, $routeParams, $location, ClubSpeedJSONService) {

    //This function routes to the racer profile page for the given racer_id
    $scope.go = function ( racer_id ) {
        $location.path( '/racersearch/' + racer_id );
    };

    $scope.spinnerActive = 1;
    ClubSpeedJSONService.getRaceDetails($routeParams.race_id).success(function (data) {
        $scope.jsonData = data;
        $scope.notEnoughRaceData = 0;
        var shouldUseGoogleCharts = (typeof config == "undefined" || typeof config.removeGoogleCharts == "undefined" || !config.removeGoogleCharts);
        if (data.race.laps !== undefined && shouldUseGoogleCharts) //If lap data was recorded, produce Google graphs
        {
            $scope.chartData = convertRaceDetailsToGoogleChartFormat(data,$rootScope.strings);
            google.setOnLoadCallback(drawChart($scope.chartData,'chart_div',$rootScope.strings));
        }
        else
        {
            $scope.notEnoughRaceData = 1;
        }
        if (data.scoreboard === undefined) //If the race never concluded, let the view know
        {
            $scope.raceConcluded = 0;
        }
        else
        {
            $scope.raceConcluded = 1;
        }
        $scope.spinnerActive = 0;
    });
});