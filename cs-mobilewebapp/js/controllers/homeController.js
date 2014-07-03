//This controller handles the front page pulling the top lap time for today
clubSpeedOnlineApp.controller('homeController', function($scope,ClubSpeedJSONService) {
    $scope.spinnerActive = 1;
    ClubSpeedJSONService.getFastestLapTimes_Day(1).success(function (data) {
        if (data.fastest.length == 0)
        {
            $scope.output_lap_time = 0;
        }
        else
        {
            $scope.output_lap_time = 1;
            $scope.lap_time = data.fastest[0].lap_time;
            $scope.nickname = data.fastest[0].nickname;
        }
        $scope.spinnerActive = 0;
    });
});