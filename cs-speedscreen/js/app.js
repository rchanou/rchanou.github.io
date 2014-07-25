// Creation of the Speed Screen Demo App
var speedScreenDemoApp = angular.module('speedScreenDemoApp', ['ngRoute','ui.bootstrap','speedScreenDemoApp.services','angularMoment']);

// Routing for single page design
speedScreenDemoApp.config(function($routeProvider) {
    $routeProvider
        .when('/', {templateUrl : 'pages/channel.html', controller  : 'channelController'})
        .when('/:channel_id', {templateUrl : 'pages/channel.html', controller  : 'channelController'})
        .when('/:channel_id/:channel_options', {templateUrl : 'pages/channel.html', controller  : 'channelController'});
});

speedScreenDemoApp.filter('formatHeatNumber', function() {
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