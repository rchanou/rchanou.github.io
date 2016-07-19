<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script language="javascript">
<?php
/*foreach($_REQUEST as $key => $value) {
  echo "var " . $key . " = " . $value . ";\n";
}*/
echo "var original = " . $_REQUEST['teams'] . ";\n";
?>

var teams     = original[0];
var customers = original[1];

$(document).ready(function() {
  teams.forEach(function(team, teamIndex) {
    var html = '';
    html += ('<h1>' + (customers[teamIndex] && customers[teamIndex].racerName ? customers[teamIndex].racerName : '') + '</h1>');
    html += ('<table class="table"><thead><tr><th>Pos</th><th>Best Lap</th><th>Driver</th></tr></thead><tbody>');
    team.drivers.forEach(function(driver) {
      var lapTime = (driver.originalData.bestLapTime) ? parseInt(driver.originalData.bestLapTime)/1000 : '';
      html += '<tr scope="row"><td>'+driver.startingPosition+'</td><td>'+lapTime+'</td><td>'+(driver.originalData.name || 'Customer ID: ' + driver.participantId) +'</td></tr>';
    });
    html += '</tbody></table><div class="page-break"></div>';
    $('#container').append(html);
  });
});
</script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous"><style type="text/css">
@media print {
    .page-break { page-break-after: always; }
}
</style>
</head>
<body>
<div id="container"></div>
</body>
</html>