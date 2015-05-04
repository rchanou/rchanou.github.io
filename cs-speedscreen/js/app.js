// Creation of the angular app
var speedscreenApp = angular.module('speedscreen', ['ngRoute','speedscreen.services']);

// Routing for single page design
speedscreenApp.config(function($routeProvider) {
    $routeProvider
        .when('/', {templateUrl : 'pages/speedscreen.html'})
        .when('/:channel_id', {templateUrl : 'pages/speedscreen.html'})
        .when('/:channel_id/:channel_options', {templateUrl : 'pages/speedscreen.html'});
});