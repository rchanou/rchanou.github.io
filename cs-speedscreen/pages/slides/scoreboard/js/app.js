var scoreboardApp = angular.module('scoreboardApp', ['ngRoute', 'scoreboardApp.services']);

scoreboardApp.config(function($routeProvider) {
    $routeProvider
        .when('/', {templateUrl: 'pages/scoreboard.html'})
        .when('/:track_id', {templateUrl: 'pages/scoreboard.html'})
        .when('/:track_id/:theme', {templateUrl: 'pages/scoreboard.html'});
});

scoreboardApp.filter('formatHeatNumber', function() {
    return function(heatNumber)
    {
        if (typeof heatNumber == "undefined")
        {
            return "";
        }
        heatNumber = '00' + heatNumber.toString();
        return heatNumber.slice(-2);
    }
});