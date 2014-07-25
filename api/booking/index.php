<?php require('library.inc.php'); ?>

<?php require('header.inc.php'); ?>

<?php
// Get upcoming heat types
$uri = $apiUrl . '/races/upcoming_heat_types.json?key=' . $apiKey;
$response = \Httpful\Request::get($uri)->send();
?>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4">
          <h2>Available Heat Types:</h2>
          <ul>
          <?php foreach($response->body->heatTypes as $type): ?>
          	<li>#<?php echo $type->heat_type_id; ?> <?php echo $type->name; ?></li>
          <?php endforeach ?>
          </ul>
        </div>
        <div class="col-md-4">
          <h2>Heading</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
       </div>
        <div class="col-md-4">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; Company 2014</p>
      </footer>
    </div> <!-- /container -->

<?php require('footer.inc.php'); ?>