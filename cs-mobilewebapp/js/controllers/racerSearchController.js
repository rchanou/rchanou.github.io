//This controller is responsible for fetching and displaying data to be shown on a racer search and summary page.
clubSpeedOnlineApp.controller('racerSearchController', function($scope, $routeParams, $location, $compile, ClubSpeedJSONService) {

    //This function routes to the race summary page for the given heat_id
    $scope.go = function ( heat_id ) {
        $location.path( '/racesummary/' + heat_id );
    };

    //This function routes to the racer profile page for the given racer_id
    $scope.goToRacer = function ( racer_id ) {
        $location.path( '/racersearch/' + racer_id );
    };

    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    /*
     $('input').keyup(function() {
     delay(function(){
     alert('Time elapsed!');
     }, 1000 );
     });
     */
    //Once three characters are typed into the input text box, a search for the racer is initiated
    $scope.searchForRacer = function()
    {
        delay(
                function()
                {
                    searchTriggerLength = 3;
                    searchingFor = $('#racerSearchInput').val();

                    if(searchingFor.length >= searchTriggerLength)
                    {

                        $scope.spinnerActive = 1;

                        ClubSpeedJSONService.searchForRacer(searchingFor).success(function(data) {
                            $scope.jsonDataRacers = data;
                            $scope.spinnerActive = 0;
                        });
                    }
                }, 1000
            );

    }

    //If the page is being visited with a racer_id as a parameter, switch to showing the racer profile page instead
    //of the racer search page, and fetch the appropriate data. Otherwise, show the racer search table.
    if ($routeParams.racer_id != null)
    {
        $scope.spinnerActive = 1;

        $scope.tableType = "racerInfoTable";
        $scope.tableCaption = "Racer Information";
        ClubSpeedJSONService.getRacerInfo($routeParams.racer_id).success(function (data) {
            $scope.jsonData = data;
        });
        $scope.tableCaptionHeats = "Past Heats";
        ClubSpeedJSONService.getPastRaces($routeParams.racer_id).success(function (data) {
            $scope.jsonDataHeats = data;
            $scope.spinnerActive = 0;
        });
    }
    else
    {
        $scope.tableType = "racerSearchTable";
    }
});