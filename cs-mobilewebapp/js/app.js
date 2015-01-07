	// Creation of the Club Speed Online Angular App
	var clubSpeedOnlineApp = angular.module('clubSpeedOnlineApp', ['ngRoute','ui.bootstrap','clubSpeedOnlineApp.services','angularMoment']);

	// Routing for single page design
	clubSpeedOnlineApp.config(function($routeProvider) {
		$routeProvider
			.when('/', {redirectTo: '/livetiming/fastestTimeByWeek'})
			.when('/racersearch', {templateUrl : 'pages/racersearch.html'})
			.when('/racersearch/:racer_id', {templateUrl : 'pages/racersearch.html'})
			.when('/racesummary/:race_id', {templateUrl : 'pages/racesummary.html'})
			.when('/livetiming', {templateUrl : 'pages/livetiming.html'})
			.when('/livetiming/:desiredTable', {templateUrl : 'pages/livetiming.html'})
            .when('/livetiming/:desiredTable/:desiredTrack', {templateUrl : 'pages/livetiming.html'})
            .when('/livescoreboard', {templateUrl : 'pages/livescoreboard.html'})
            .when('/livescoreboard/:desiredTrack', {templateUrl : 'pages/livescoreboard.html'})
			;
	});

    clubSpeedOnlineApp.filter('orderObjectBy', function() {
        return function(items, field, reverse) {
            var filtered = [];
            angular.forEach(items, function(item) {
                filtered.push(item);
            });
            filtered.sort(function (a, b) {
                return (parseInt(a[field]) > parseInt(b[field])) ? 1 : ((parseInt(a[field]) < parseInt(b[field])) ? -1 : 0);
            });
            if(reverse) filtered.reverse();
            return filtered;
        };
    });

    clubSpeedOnlineApp.filter('formatLapTime', function() {
        return function(lapTime)
        {
            lapTime = parseFloat(lapTime);
            var minutes = Math.floor(lapTime / 60);
            var seconds = pad(Math.floor(lapTime % 60),2);
            var milliseconds = parseInt((lapTime - parseInt(lapTime))*1000);


            var convertedLapTime = "";
            if (minutes > 0)
            {
                convertedLapTime += minutes + ":";
            }
            convertedLapTime += seconds + "." + milliseconds;
            return convertedLapTime;

            function pad(num, size) {
                var s = num+"";
                while (s.length < size) s = "0" + s;
                return s;
            }
        }
    });

// Footer controller

    //Just controls whether or not the scoreboard is enabled for the app - defaults to false
    clubSpeedOnlineApp.controller('footerController', function($scope) {
        if (typeof config !== "undefined")
        {
            $scope.enableScoreboard = defaultFor(config.enableScoreboard, false);
        }
        else //Backwards compatibility for installs that never had a config.js created
        {
            $scope.enableScoreboard = false;
        }
    });

// ####################################################
// ### Miscellaneous AngularJS Behavior Adjustments ###
// ####################################################

    clubSpeedOnlineApp.factory('globalVars', function() {
        var globalVars = {};
        var scoreboardUpdateTimeout = null;

        globalVars.setScoreboardUpdateTimeout = function(newScoreboardUpdateTimeout) { scoreboardUpdateTimeout = newScoreboardUpdateTimeout; }
        globalVars.resetScoreboardUpdateTimeout = function()
        {
            if (scoreboardUpdateTimeout !== null)
            {
                clearTimeout(scoreboardUpdateTimeout);
                scoreboardUpdateTimeout = null;
            }
        }

        return globalVars;
    });

//Scroll to the top of the page whenever a route is changed, and also stop any scoreboards from being polled
clubSpeedOnlineApp.run(function($rootScope, $location, $anchorScroll,globalVars) {
    $rootScope.$on('$routeChangeSuccess', function(newRoute, oldRoute) {
        $anchorScroll();
        globalVars.resetScoreboardUpdateTimeout();
    });

});

$( document ).ready(function() {
	//If the user is on a touch device, hide the bottom footer and live timing header whenever the mobile keyboard pops up.
	if (Modernizr.touch) {
		$(document)
		.on('focus', 'input', function(e) {
		$('#bottomfooter').addClass('fixfixed');
		})
		.on('blur', 'input', function(e) {
		$('#bottomfooter').removeClass('fixfixed');
        window.scrollTo(0,0);
		})
        .on('focus','#livetimingselector', function(e) {
            $('#bottomfooter').addClass('fixfixed');
        })
        .on('focusout','#livetimingselector', function(e) {
            $('#bottomfooter').removeClass('fixfixed');
                window.scrollTo(0,0);
        })
        .on('change','#livetimingselector', function(e) {
            $('#bottomfooter').removeClass('fixfixed');
                window.scrollTo(0,0);
        });
	}

	//Close the main navigation menu when someone clicks on a top navbar link
	$('.nav a').on('click', function(){
        if ($(".navbar-toggle").is(":visible"))
        {
		    $(".navbar-toggle").click();
        }
	});

    //Close the main navigation menu when someone clicks on a bottom button link
    $('#bottomfooter a').on('click', function(){
        if ($(".navbar-collapse").hasClass("in"))
        {
            $(".navbar-toggle").click();
        }
    });
 
});


