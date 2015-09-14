/*
    Given a JSON object containing a race's details in the following format:
 {
     "race": {
     "id": 9128,a bunch other details
     "racers": [
         {"id": 1016239, a bunch of other details},
         {...a bunch of other racers}
     ],
     "laps": [
         {"id": 523719,...a bunch of other details},
         {...a bunch of other laps}
     ]
  ,... more fields
 }

 This function converts and formats the lap data to a Google Chart friendly format:

 [ [Lap,RacerName1,RacerName2,RacerName3,...,RacerNameN],
   [1,30.23,29.33,26.77,...,32.85],
   [2,32.21,32.34,26.87,...,null], //A null lap means a racer ended the race before that lap
   ... and so on ]

 */
function convertRaceDetailsToGoogleChartFormat(jsonData, strings)
{
    //This helper class defines a Racer and his lap times for a race
    function Racer(id,nickname)
    {
        this.id = id;
        this.nickname = nickname;
        this.laptimes = new Array();
    }

    var racers = new Array();

    //For each racer in the JSON object, create a new Racer instance
    for (var i=0; i < jsonData.race.racers.length; i++)
    {
        var currentRacer = jsonData.race.racers[i];
        racers[currentRacer.id] = new Racer(currentRacer.id, currentRacer.nickname);
    }

    var totalNumberOfLaps = 0;

    //For each non-zero lap, record its entry and details in the appropriate racer object
    for (var i=0; i < jsonData.race.laps.length; i++)
    {
        var currentLap = jsonData.race.laps[i];
        if (currentLap.lap_number != 0 && typeof racers[currentLap.racer_id] !== "undefined")
        {
            racers[currentLap.racer_id].laptimes[currentLap.lap_number] = currentLap.lap_time;
            if (parseInt(currentLap.lap_number) > parseInt(totalNumberOfLaps)) //Find out the greatest number of laps
            {
                totalNumberOfLaps = currentLap.lap_number;
            }
        }
    }

    var googleChartData = new Array();

    var labelsArray = new Array();
    labelsArray.push(strings['str_lapNumber']);

    //Populate the labels array with the racer names
    for(var racerID in racers)
    {
        if (racers.hasOwnProperty(racerID))
        {
            labelsArray.push(racers[racerID].nickname);
        }
    }

    //Insert the labels array into the final data set
    googleChartData.push(labelsArray);

    //For every lap that occurred, create the next array of data for the Google Chart data set
    for(var i = 1; i <= totalNumberOfLaps; i++)
    {
        var currentLapArray = new Array();
        currentLapArray.push(i); //Lap Number
        for(var racerID in racers) //For every racer
        {
            if (racers.hasOwnProperty(racerID))
            {
                if (i in racers[racerID].laptimes) //If the racer reached that lap number
                {
                    currentLapArray.push(racers[racerID].laptimes[i]); //Add their lap time to the array
                }
                else
                {
                    currentLapArray.push(null); //Otherwise, write a null lap time.
                }
            }
        }
        googleChartData.push(currentLapArray); //Once populated add the lap times for the current lap to the Google Chart data set
    }
    return googleChartData;
}

//Given chart data in the appropriate 2-dimensional array format, and a div to draw to, this function displays a
//Line Chart derived from the given data.
function drawChart(chartData,divToDrawTo,strings)
{
    var data = new google.visualization.DataTable();
    for(var i=0; i < chartData[0].length; i++)
    {
        data.addColumn('number',chartData[0][i]);
    }
    chartData.shift();

    data.addRows(chartData);

    var seriesOptions = {};
    var options = {
        hAxis: {title: strings['str_lapNumber'],  titleTextStyle: {color: 'white'}, textStyle:{color: 'white'}, gridlines:{color:'#333'}, format: '0' },
        vAxis: {title: strings['str_lapTime'], titleTextStyle: {color: 'white'}, textStyle:{color: 'white'}, gridlines:{color:'#333'}, viewWindowMode: 'maximized'},
        titleTextStyle: {color: 'white'},
        backgroundColor: 'black',
        legend: {textStyle: {color: 'white'}, position: 'top', maxLines: 3},
        series: seriesOptions
    };

    var chart = new google.visualization.LineChart(document.getElementById(divToDrawTo));

    // The following section adds the ability to hide specific racers from the chart by clicking their label
    var columnsToDisplay = [];
    var columnsToStartOutDisplaying = [];
    for (var i = 0; i < data.getNumberOfColumns(); i++) //Have all columns shown by default
    {
        columnsToStartOutDisplaying.push(i);
    }

    for (var i = 0; i < data.getNumberOfColumns(); i++) //For each column of data
    {
        if (i == 0) //If it's the Lap Number column
        {
            columnsToDisplay.push(i); //Display it
        }
        else if (columnsToStartOutDisplaying.indexOf(i) != -1) //If the column is in the list of columns to display
        {
            columnsToDisplay.push(i); //Display it
        }
        else //Otherwise, hide the column
        {
            columnsToDisplay[i] =
            {
                label: data.getColumnLabel(i),
                type: data.getColumnType(i),
                calc: function() { return null; }
            };
        }
        if (i > 0) //For every legitimate column
        {
            seriesOptions[i-1] = {}; //Set it to have default series options
            if (columnsToStartOutDisplaying.indexOf(i) == -1) //If the column isn't displayed by default
            {
                if(typeof(seriesOptions[i-1].color) !== 'undefined') //Backup its original color
                {
                    seriesOptions[i-1].originalColor = seriesOptions[i-1].color;
                }
                seriesOptions[i-1].color="#BBBBBB"; //Then switch off its color
            }
        }
    }

    //This function is called whenever a click occurs on the chart - if a label is clicked, that data is hidden.
    function showOrHideData()
    {
        var selection = chart.getSelection();
        if (selection.length > 0) //If something was just selected
        {
            if (selection[0].row == null) //If an item in the legend itself was clicked
            {
                var selectedColumn = selection[0].column;
                if (columnsToDisplay[selectedColumn] == selectedColumn) //If the clicked racer is currently being displayed
                {
                    //Hide the racer
                    columnsToDisplay[selectedColumn] =
                    {
                        label: data.getColumnLabel(selectedColumn),
                        type: data.getColumnType(selectedColumn),
                        calc: function() { return null; }
                    };

                    //Switch off the label color
                    seriesOptions[selectedColumn-1].color = "#BBBBBB";
                }
                else //Otherwise, if the clicked racer wasn't being displayed
                {
                    columnsToDisplay[selectedColumn] = selectedColumn; //Show the racer
                    seriesOptions[selectedColumn-1].color = null; //Switch the color back to its original
                }
                var view = new google.visualization.DataView(data);
                view.setColumns(columnsToDisplay);
                chart.draw(view, options);
            }
        }
    }

    google.visualization.events.addListener(chart, 'select', showOrHideData);
    var view = new google.visualization.DataView(data);
    view.setColumns(columnsToDisplay);

    chart.draw(view, options);
}