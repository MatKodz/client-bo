
<?php
if( isset($_POST['envoi'])) {
  $erreur = false;
  foreach ($_POST as $value) {
    if (empty($value))
    $erreur = true;
  }

  if( isset($_POST['nom']) && !preg_match('/^[a-zA-ZÀ-ÿ\s]{2,}$/',$_POST['nom']) )
 {
   $Vnom = false;
 }
   else {
     $Vnom = true;
   }

   if ( isset($_POST['monmotdepasse']) && preg_match("/^(?=.*[A-Z])(?=.*\d)([0-9a-zA-Z]+)$/", $_POST['monmotdepasse'] ) ) {
     $mdp = true;
   }
   else $mdp = false;


  if (!$erreur and ($Vnom == true) and ($mdp == true))

     {

         require "connect-bdd.php";


          // echo '<div class="container"><div class="row"><div class="col-sm"><p class="alert alert-success">Nous avons bien pris en compte vos coordonnées</p></div>';

          foreach ($_POST as $key => $value) {

            $coordonnees[$key]=$value;

          }

         $requete_enregistrement = "INSERT INTO customer_list (CUS_lastname, CUS_firstname, CUS_email, CUS_password, CUS_phone, CUS_address, CUS_zipcode, CUS_town, CUS_commuting, CUS_info, CUS_register)
         VALUES (:nom, :prenom, :courriel, MD5(:mdp), :telephone, :adresse, :codepostal, :ville, :deplacement, :information, NOW())";

         $sth = $conn->prepare($requete_enregistrement, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

         $sth->execute(array(':nom' => $coordonnees['nom'],
                        ':prenom' => $coordonnees['prenom'],
                        ':courriel' => $coordonnees['monmail'],
                        ':mdp' => $coordonnees['monmotdepasse'],
                        ':telephone' => $coordonnees['telephone'],
                        ':adresse' => $coordonnees['adresse'],
                        ':codepostal' => $coordonnees['codepostal'],
                        ':ville' => $coordonnees['ville'],
                        ':deplacement' => $coordonnees['locomotion'],
                        ':information' => $coordonnees['info'],
                      ));

                      echo '<div class="col-sm-12"><h2>Merci</h2>Les coordonnées ont été intégrées à nos fichiers client. Merci de votre confiance et à bientôt.</div><div></div>';

                      $form_fadeOut =  "<script> form_hidden(); </script>";

                      $conn = NULL;

   }
}





?>


<!DOCTYPE html>
<html lang="fr">
<head>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- en-tete du document -->

  <title>Inscription Client</title>

  <meta charset="UTF-8">

  <script type="text/javascript">

  function form_hidden() {
    document.getElementById('form').style.display = "none";
    document.querySelector(".jumbotron").style.display = "none";
  };

  </script>

  <style>

  body {font-family: 'Avenir Next', sans-serif;}

  #form {padding: 2%; width: 100%;}

  input, textarea {font-size: 80%; padding: 5px; margin: 20px 0; border: 1px solid #e6e6e6; width: 200px; transition: width ease-in-out 0.4s;}

  textarea { width: 80%; height: 100px;}

  input[type="radio"]{ width: 10px; visibility: hidden;}

  input[type="radio"] + label { width: auto; border: 1px purple solid; background: #fafafa; color : purple; padding: 7px;}

  input[type="radio"]:checked + label { background: purple; color: white; }

  input:focus {box-shadow: 2px 2px 6px #666;}

  /*input:valid {color: #111; border: 2px solid #3E9E00;}*/

  input.wrong {color: #bb1100; box-shadow: 2px 2px 3px #bb1100;}

  input:required { border-left: 2px solid orange;}

  .does-exist {

  }


    label { font-size: 16px; width: 280px; display: inline-block;}

    input + span, textarea + span, label + span { font-size: 80%; color: red; padding: 3px; border-bottom: 3px solid red; display: inline-block; margin: 0 10px;}

    .col- { border-bottom: 4px solid grey; padding: 2%; margin: 2% 0; background: #fafafa;}

  </style>

</head>

<body>

<div class="container">

  <div class="jumbotron mt-2">

    <h1 class="text-dark h1">Devenir client</h1>

  </div>

</div>

<div class="container">

  <div class="row">

    <?php if (isset($msg))
    echo '<p class="alert alert-danger does-exist">'.  $msg . '</p>';
    ?>

    <form name="formulaire" id="form" method="post" action="">
    <h3>S'inscrire comme client </h3>
    <p> Tous les champs sont obligatoires</p>

  <div class="col-">
    <label id="lanom" for="Votre nom">Votre nom</label>
    <input type="text" size="25" name="nom" id="lanom" placeholder="Votre nom ici" value="<?php if((isset($_POST['nom']))) { echo $_POST['nom'];}?>">
        <?php
        if( isset($_POST['nom']) && empty($_POST['nom']) )
        echo "<span>Le nom est manquant</span>";
         else if( isset($_POST['nom']) && !preg_match('/^[a-zA-ZÀ-ÿ\s]{2,}$/',$_POST['nom']) )
        {
          echo "<span class=\"alert alert-warning\">Minimum 2 lettres. Les caractères spéciaux ne sont pas autorisés</span>";
        }
        ?>
  </div>

  <div class="col-">
    <label id="laprenom" for="Votre prénom">Votre prénom</label>
    <input type="text" size="25" name="prenom" id="laprenom" placeholder="Votre prénom ici" value="<?php if((isset($_POST['prenom']))) { echo $_POST['prenom'];}?>">
        <?php if((isset($_POST['prenom'])) && empty(trim($_POST['prenom']) ) ) { echo "<span>Le prénom est manquant</span>";} ?>
  </div>

  <div class="col-  <?php if(isset($mail_class)) echo $mail_class; ?>">
  <label id="lamail" for="Votre email">Votre email</label>
  <input type="email" id="lamail" size="30" name="monmail" placeholder="Votre mail ici" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" value="<?php if((isset($_POST['monmail']))) { echo $_POST['monmail'];}?>">
      <?php
      if( isset($_POST['monmail']) && empty($_POST['monmail']) ) {
        echo "<span>Le mail est manquant</span>";
      }
      else if( isset($_POST['monmotdepasse']) && !filter_var($_POST['monmail'], FILTER_VALIDATE_EMAIL) )
      { echo "<span>Email invalide</span>";}
      ?>

  </div>

  <div class="col-">
  <label id="pwd" for="Votre mot de passe">Votre mot de passe</label>
  <input type="password" id="pwd" size="20" name="monmotdepasse" value="<?php if((isset($_POST['monmotdepasse']))) { echo $_POST['monmotdepasse'];}?>">
      <?php if( isset($_POST['monmotdepasse']) && empty($_POST['monmotdepasse']) )
        echo "<span>Le mot de passe est requis</span>";
        else if ( isset($_POST['monmotdepasse']) && !preg_match("/^(?=.*[A-Z])(?=.*\d)([0-9a-zA-Z]+)$/", $_POST['monmotdepasse'] ) )
        {
          echo '<p class="alert alert-warning">Mot de passe non conforme (une Maj, une Min, un chiffre minimum)</p>';
        }

?>
  </div>


  <div class="col-">
    <label id="lanum" for="Votre numéro de téléphone">Votre numéro de téléphone</label>
    <input type="text" size="25" name="telephone" id="lanum" placeholder="Votre numéro de téléphone ici" pattern="[0-9]{10}" value="<?php if((isset($_POST['telephone']))) { echo $_POST['telephone'];}?>">
        <?php if((isset($_POST['telephone'])) && empty($_POST['telephone']) ) { echo "<span>Le numéro de téléphone est manquant</span>";} ?>
  </div>


  <div class="col-">
    <label id="laadresse" for="Votre adresse">Votre adresse</label>
    <input type="text" size="25" name="adresse" id="laadresse" placeholder="Votre adresse" value="<?php if((isset($_POST['adresse']))) { echo $_POST['adresse'];}?>">
        <?php if((isset($_POST['adresse'])) && empty($_POST['adresse']) ) { echo "<span>L'adresse est manquante</span>";} ?>
  </div>


  <div class="col-">
    <label id="lacp" for="Votre CP">Votre code postal</label>
    <input type="text" size="5" name="codepostal" id="lacp" placeholder="Votre CP" value="<?php if((isset($_POST['codepostal']))) { echo $_POST['codepostal'];}?>">
        <?php if((isset($_POST['codepostal'])) && empty($_POST['codepostal']) ) { echo "<span>Le CP est manquant</span>";} ?>
  </div>



  <div class="col-">
  <label id="laville" for="Votre ville">Votre ville de résidence</label>
  <input type="text" size="25" name="ville" id="laville" placeholder="Votre ville ici" value="<?php if((isset($_POST['ville']))) { echo $_POST['ville'];}?>">
      <?php if((isset($_POST['ville'])) && $_POST['ville'] == "") { echo "<span>La ville est manquante</span>";} ?>
  </div>

  <div class="col-">
  <label>Votre mode de déplacement</label>
  <input type="radio" name="locomotion" value="car" id="voit" <?php if(isset($_POST['locomotion']) && $_POST['locomotion'] == 'car') echo "checked" ?>><label for="voit">Voiture</label>
  <input type="radio" name="locomotion" value="bike" id="vel" <?php if(isset($_POST['locomotion']) && $_POST['locomotion'] == 'bike') echo "checked" ?>> <label for="vel">Vélo</label>
  <input type="radio" name="locomotion" value="taxi" id="tax" <?php if(isset($_POST['locomotion']) && $_POST['locomotion'] == 'taxi') echo "checked" ?>> <label for="tax">Taxi</label>
  <input type="radio" name="locomotion" value="plane" id="avi" <?php if(isset($_POST['locomotion']) && $_POST['locomotion'] == 'plane') echo "checked" ?>> <label for="avi">Avion</label>
      <?php
      if( isset($_POST['envoi']) && !isset($_POST['locomotion']) ) { echo "<span>Le moyen de transport est manquant</span>";} ?>
  </div>

  <div class="col-">
    <label id="info" for="Vos informations">Informations complémentaires</label>
    <textarea name="info" maxlength="500"><?php if(isset($_POST['info'])) echo trim($_POST['info']); ?></textarea>
        <?php if((isset($_POST['info'])) && $_POST['info']=="") { echo "<span><br>Le message est vide</span>";} ?>
  </div>

  <div class="col-12 text-center">
    <input type="submit" name="envoi" value="Envoyer les informations" class="btn btn-success">
    <button type="reset" value="Reinit" class="btn btn-light">Recommencer </button>
  </div>

  </form>
</div>
</div>
<?php
if (isset($form_fadeOut)) echo $form_fadeOut;
?>


</body>

</html>
