<?php

//@@@@@@@@@@ Replace with connection config file/details @@@@@@@@@@
require_once('../app/bootstrap.php');
//@@@@@@@@@@ Replace with connection config file/details @@@@@@@@@@


//Get Destination Data
//NOTE : As per discussion, I am pulling the first available link from each location
$rs_getDestinations = $indidb->prepare("SELECT url,anchor_text FROM pyt.destinations WHERE id IN ( SELECT min(id) FROM pyt.destinations GROUP BY anchor_text)");
$rs_getDestinations->execute();

//Get Vacation Data
$rs_getVacations = $indidb->prepare("SELECT url,anchor_text FROM pyt.vacations");
$rs_getVacations->execute();

//Get Itenerary Catalogue Labels
$rs_getItineraryLabels = $indidb->prepare("SELECT DISTINCT CASE WHEN anchor_text RLIKE '^[0-9]' THEN '0-9' ELSE LEFT(anchor_text,1) END AS first_letter FROM pyt.itineraries ORDER BY first_letter");
$rs_getItineraryLabels->execute();

//Fetch Itenerary catalogue labels as array for looping
$itinerary_label_array = $rs_getItineraryLabels->fetchall(PDO::FETCH_ASSOC);

//Get Itinerary Data
$rs_getItineraries = $indidb->prepare("SELECT CASE WHEN anchor_text RLIKE '^[0-9]' THEN '0-9' ELSE LEFT(anchor_text,1) END AS first_letter, url, anchor_text FROM pyt.itineraries ORDER BY first_letter, anchor_text");
$rs_getItineraries->execute();
$row_count_itineraries = $rs_getItineraries->rowCount();

//Fetch Itinerary data as array for looping
$itinerary_data = $rs_getItineraries->fetchall(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <title>Pickyourtrail Sitemap</title>
  </head>
  <body>
    
    <div class="container">

      <h3 class="my-5">Pickyourtrail Sitemap</h3>

      <p><strong>Destinations</strong></p>

      <div class="row border-top py-3 mb-4">
        <?php while($row_getDestinations = $rs_getDestinations->fetch(PDO::FETCH_ASSOC)) { ?>
        <div class="col-6 col-sm-4 col-md-3"><a href="<?php echo $row_getDestinations['url']?>"><?php echo implode('-', array_map('ucfirst', explode('-', $row_getDestinations['anchor_text'])))?></a></div> 
        <?php } ?>
        <!-- @@@@@ NOTE @@@@@ : Setting location names to Uppercase first letter. Ideally this should be done directly in the DB -->

      </div>

      <p><strong>Themed Vacations</strong></p>
      <div class="row border-top py-3 mb-4">
        <?php while($row_getVacations = $rs_getVacations->fetch(PDO::FETCH_ASSOC)) { ?>
        <div class="col-6 col-sm-4 col-md-3"><a href="<?php echo $row_getVacations['url']?>"><?php echo $row_getVacations['anchor_text']?></a></div> 
        <?php } ?>
        <!-- @@@@@ NOTE @@@@@ : Here I have changed the full caps text in the DB directly to make it look better. -->

      </div>

      <p class="mt-5"><strong>Showing all <?php echo $row_count_itineraries?> Destinations</strong></p>

      <p class="border-top border-bottom py-3 text-center font-weight-bold">
        <?php foreach($itinerary_label_array as $key1 => $value1) { ?>
        <a href="#<?php echo ucfirst($value1['first_letter'])?>" class="text-dark p-2"><?php echo ucfirst($value1['first_letter'])?></a> 
      <?php } ?>
      </p>


        <div class="row py-3 smaller-20">
          <?php foreach($itinerary_label_array as $key2 => $value2) {?>
          <div class="col-12 col-md-2 font-weight-bold"><a name="<?php echo $value2['first_letter']?>"><?php echo $value2['first_letter']?></a></div>
          <div class="col-12 col-md-10">
            <?php foreach($itinerary_data as $key3 => $value3) {?>
              <?php if($value3['first_letter'] == $value2['first_letter']) {?>
              <div class="row">
                <div class="col-12 col-sm-6 pb-3"><a href="<?php echo $value3['url']?>" class="text-info"><?php echo $value3['anchor_text']?></a></div>
              </div>
              <?php } ?> <!-- IF -->
            <?php } ?> <!-- Loop -->
          </div>
        <?php } ?>
        </div>
      




    </div><!--/.container-->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>