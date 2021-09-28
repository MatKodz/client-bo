<!DOCTYPE html>
<html lang="fr">
<head>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">  <meta charset="utf-8">


<!-- en-tete du document -->

  <title>Liste Client</title>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script type="text/javascript">

  </script>

  <style>

  body {font-family: 'Avenir Next', sans-serif;}


    .col- { border-bottom: 4px solid grey; padding: 2%; margin: 2% 0; background: #fafafa;}
    .client { border: 10px solid #fff; padding: 15px; background: #fcfcfc;}
    .client p { margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #fff;}
    .row div:nth-of-type(2n){
      background: #efefef;
    }

    .row div.client:last-of-type {
      background-color: white;
    }

  </style>

</head>

<body>

<div class="container">

  <div class="jumbotron mt-2">

    <h1 class="text-dark">Liste des clients enregistrés</h1>
    <a href="ajout-client.php" class="btn btn-success">Ajouter un client</a>
    <a href="../client-prive/session.php" class="btn btn-success">Accéder à l'espace privé</a>

  </div>

</div>

<div class="container">
  <div class="row">
    <div class="col-sm">
    <h4>Classé par date d'inscription par défaut (la plus récente) </h4>
  </div>
  </div>

  <?php

require_once('connect-bdd.php');

function format_tel($tel) {
  $init = 0;
  $nb = strlen($tel);
  $final = [];
  for ($init; $init <= $nb; $init++) {
    $final[] = $tel[$init];
    if( $init > 0 && ($init % 2) )
    $final[] = " ";
  }
  return implode("",$final);
}

// connexion à la bdd

  $requete_fl = "SELECT SUBSTR(CUS_lastname,1,1) AS FL FROM customer_list GROUP BY FL";

  $res_l = $conn->query($requete_fl);

  ?>
  <nav aria-label="Lettre client">
  <ul class="pagination">

  <?php

  function display_selected($letter) {
    if ( isset($_GET['filterby']) && strtolower($letter) == strtolower($_GET['filterby']) ) {
      return "active";
    }
  }

  while ($lettre = $res_l->fetch()) {
     echo '<li class="page-item '. display_selected($lettre['FL']) . '"><a class="page-link" href="'.$_SERVER['SCRIPT_NAME'].'?filterby='.$lettre['FL'].'">' . $lettre['FL'] .'</a></li>';
   }
   echo '<li class="page-item"><a class="page-link" href="'. $_SERVER['PHP_SELF'].'">Tout afficher</a></li>';

   ?>

     </ul>
    </nav>
    <hr>

   <div class="row">

     <?php

     //debut

  $filter = isset($_GET['filterby']) && is_string($_GET['filterby']) && preg_match('/^\w{1}$/',$_GET['filterby']) ? " WHERE CUS_lastname LIKE '". $_GET['filterby'] . "%'" : "";

  $requete_aff = "SELECT CUS_id, CUS_lastname, CUS_firstname, CUS_email, CUS_phone, CUS_address, CUS_zipcode, CUS_town, CUS_commuting, CUS_info, DATE_FORMAT(CUS_register, '%a %d-%m-%Y') AS date_inscription FROM customer_list $filter ORDER BY CUS_lastname";

    // fin

  $req = "SELECT CUS_id, CUS_lastname, CUS_firstname, CUS_email, CUS_phone, CUS_address, CUS_zipcode, CUS_town, CUS_commuting, CUS_info, DATE_FORMAT(CUS_register, '%d-%m-%Y') AS date_inscription FROM customer_list ORDER BY CUS_register DESC";

  $req_prep = "SELECT CUS_id, CUS_lastname, CUS_firstname, CUS_email, CUS_phone, CUS_address, CUS_zipcode, CUS_town, CUS_commuting, CUS_info, DATE_FORMAT(CUS_register, '%d-%m-%Y') AS date_inscription FROM customer_list WHERE CUS_lastname LIKE ? ORDER BY CUS_register DESC";

  if (isset($_GET['filterby'])) {
    $fb = trim($_GET['filterby']);
    if (is_string($fb) && preg_match('/^\w{1}$/',$fb)) {
      $sth = $conn->prepare($req_prep);
      $sth->bindValue(1,"$fb%");
      $sth->execute();
    }
    else {
       echo '<div class="col-12"><p class="alert alert-danger">Aucune correspondance trouvée sur ' . strip_tags($fb) .'</p></div>';
       $sth = $conn->query($req);
    }
  }
  else {
    $sth = $conn->query($req);
  }

  $jeu = $sth->fetchAll();

  if(!$jeu) echo '<div class="col-12"><p class="alert alert-danger">Aucune correspondance trouvée sur : ' . strip_tags($fb) .'</p></div>';


  foreach($jeu as $client) {

    echo '<div class="col-xs col-sm-6 col-lg-4 client">';
    echo '<h3 class="text-primary">'.strtoupper($client['CUS_lastname']).' '.$client['CUS_firstname']. " <small>(" . $client['CUS_id'] . ")</small>". '</h4>';
    echo '<p><b>Inscrit le </b> <span class="badge bagde-pill badge-secondary font-weight-normal">'.$client['date_inscription'].'</span></p>';
    echo '<p><b><a href="mailto:'.$client['CUS_email'].'">'.$client['CUS_email'].'</a></b></p>';
    echo '<p><b>Tél : </b>'. format_tel( $client['CUS_phone'] ) .'</p>';
    echo '<p><b>Location:</b> '.$client['CUS_address']. "  - " .$client['CUS_zipcode'] . " " .  $client['CUS_town']. '</p>';
    echo '<p><b>Moyen de transport:</b> '.$client['CUS_commuting'].'</p>';
    echo '<p><b>Info complémentaires:</b> '.$client['CUS_info'].'</p>';
    echo '<form action="modifier-client.php" method="post" name="update"><button type="submit" class="btn btn-primary btn-outline-primary my-4 mx-auto d-block">Modifier</button><input type="hidden" name="idcust" value = "'.$client['CUS_id'].'"></form>';
    echo '</div>';
  }

  $conn = NULL;

  ?>

  <div class="col-xs col-sm-6 col-lg-4 client d-flex align-items-center justify-content-center">
    <h4 class=""><a href="ajout-client.php"> (+) Ajouter un client (+) </a></h4>
  </div>

  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col">
      <hr>
      <p>Liste des clients par ordre alphabétique. Filtrage possible.</p>
    </div>
  </div>
</div>


</body>

</html>
