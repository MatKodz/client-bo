<!DOCTYPE html>
<html lang="fr">
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<!-- en-tete du document -->
  <title>Liste Client</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Avenir Next', sans-serif;
    }
    .col- { border-bottom: 4px solid grey; padding: 2%; margin: 2% 0; background: #fafafa;}
    .client { border: 10px solid #fff; padding: 15px; background: #fcfcfc;}
    .client p { margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #fff;}
    .row div:nth-of-type(2n){ background: #efefef;}
    .row div.client:last-of-type {background-color: white;}
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

function format_tel($tel) {
  $pattern = '/\d{2}/';
  return preg_replace($pattern,"$0 $1 $2 $3 $4 $5",$tel);
}
// fonction permettant de formater les numéros de téléphone au format 0X XX XX XX XX

function display_selected($letter) {
  if ( isset($_GET['filterby']) && strtolower($letter) == strtolower($_GET['filterby']) ) {
    return "active";
  }
}
// fonction permettant d'afficher la lettre qui a été cliqué
?>

  <nav aria-label="Lettre client">
  <ul class="pagination">

  <?php

  require_once('connect-bdd.php');
  // établir la connexion avec la BDD

  $requete_fl = "SELECT SUBSTR(CUS_lastname,1,1) AS FL FROM customer_list GROUP BY FL";
  // requête affichant la première lettre UNE fois de chaque nom présetn dans la base

  $res_l = $conn->query($requete_fl);

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

  $req_all = "SELECT CUS_id, CUS_lastname, CUS_firstname, CUS_email, CUS_phone, CUS_address, CUS_zipcode, CUS_town, CUS_commuting, CUS_info, DATE_FORMAT(CUS_register, '%d-%m-%Y') AS date_inscription FROM customer_list ORDER BY CUS_register DESC";

  $req_filtered = "SELECT CUS_id, CUS_lastname, CUS_firstname, CUS_email, CUS_phone, CUS_address, CUS_zipcode, CUS_town, CUS_commuting, CUS_info, DATE_FORMAT(CUS_register, '%d-%m-%Y') AS date_inscription FROM customer_list WHERE CUS_lastname LIKE ? ORDER BY CUS_register DESC";

  if (isset($_GET['filterby'])) {
    // vérification de la présence d'un filtre
    $fb = trim($_GET['filterby']);
    if ( !is_numeric($fb) && preg_match('/^\w{1}$/',$fb)) {
      // le filtre doit être une chaîne de 1 caractère
      $sth = $conn->prepare($req_filtered);
      $sth->bindValue(1,"$fb%");
      $sth->execute();
    }
    else {
      // si filtre non conforme, on affiche tout par défaut
       echo '<div class="col-12"><p class="alert alert-danger">Aucune correspondance trouvée sur ' . strip_tags($fb) .'</p></div>';
       $sth = $conn->query($req_all);
    }
  }
  else {
    $sth = $conn->query($req_all);
  }

  $jeu = $sth->fetchAll();
  // on génère un array contenant le jeu de résultats

  if(!$jeu)
  echo '<div class="col-12"><p class="alert alert-danger">Aucune correspondance trouvée sur : ' . strip_tags($fb) .'</p></div>';
  // si l'utilisateur réalise une recherche sur une lettre qui ne génère pas de résulat, on affiche un message
  else {
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
  }

  $conn = NULL;
  // fermer la connexion à la BDD

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
