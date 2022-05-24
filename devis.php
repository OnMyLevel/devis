<?php
// ------------DEVIS------------

//require_once('bdd.php');
session_start();

$afficheFormulaireInfoPerso = 1 ;
@$afficheFormulaireFormules1 = 0 ;
@$afficheFormulaireFormules2345 = 0 ;
@$affichePrix = 0;

if(isset($_POST['valider']) && $_POST['formule'] == "Active"){

$_SESSION['formuleChoisie'] = $_POST['formule'] ;
$_SESSION['nom'] = $_POST['nom'];
$_SESSION['prenom'] = $_POST['prenom'];
$_SESSION['tel'] = $_POST['tel'];
$_SESSION['mail'] = $_POST['mail'];
//affichage du formulaire pour formule Active
@$afficheFormulaireInfoPerso = 0;
@$afficheFormulaireFormules1 = 1;

}


if(isset($_POST['valider']) && $_POST['formule'] != "Active"){
$_SESSION['formuleChoisie'] = $_POST['formule'] ;
$_SESSION['nom'] = $_POST['nom'];
$_SESSION['prenom'] = $_POST['prenom'];
$_SESSION['tel'] = $_POST['tel'];
$_SESSION['mail'] = $_POST['mail'];
//affichage du formulaire pour les autres formules
@$afficheFormulaireInfoPerso = 0;
@$afficheFormulaireFormules2345 = 1;

}


if(isset($_POST['affichePrix'])){
	$_SESSION['dep']=$_POST['dep'];
	$_SESSION['ari']=$_POST['ari'];
	$_SESSION['date'] = $_POST['date'];
	$affichePrix = 1;

if($_SESSION['formuleChoisie'] == "Active"){
@$afficheFormulaireInfoPerso = 0;
@$afficheFormulaireFormules2345 = 0;


@$prixKM = $leprix['prixKM']; // le prix sera variable en fonction de l'admin
@$prixVehicule = $leprix['prixVehicule']; // le prix sera variable en fonction de l'admin
@$prixService = $leprix['prixService'] ; // le prix sera variable en fonction de l'admin

//on créé la fonction
function calculer_distance($adresse1,$adresse2) {
$adresse1 = str_replace(" ", "+", $adresse1); //adresse de départ
$adresse2 = str_replace(" ", "+", $adresse2); //adresse d'arrivée
$url = 'https://maps.googleapis.com/maps/api/directions/xml?origin='.$adresse1.'&destination='.$adresse2.'&key=AIzaSyD_Ygw4nw_-Nwyv9JEtnmt8T6rpaGwFRIE'; //on créé l'url



//on lance une requete aupres de google map avec l'url créée
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
$xml = curl_exec($ch);

//on réccupère les infos
$charger_googlemap = simplexml_load_string($xml);
$distance = $charger_googlemap->route->leg->distance->value;

//si l'info est récupérée, on calcule la distance
if ($charger_googlemap->status == "OK") {
$distance = $distance/1000;
$distance = number_format($distance, 2, '.', ' ');

return $distance;
}
else {
//si l'info n'est pas récupérée, on lui attribu 0
return "0";
}
}
//si le bouton calculer est lancé, on récupère les informations du formulaire et on lance la fonction

$dep = $_POST['dep'];
$ari = $_POST['ari'];



@$ladistance = (calculer_distance($dep,$ari)*2);
//calcul du prix total si l'utilisateur choisi la formule Active
$prix = $ladistance*$prixKM + $prixService + $prixVehicule ;
$phraseDistance = "la distance entre " .$dep. " et ".$ari." est de ".calculer_distance($dep,$ari)."KM" ;
}





//sinon, si l'utilisateur choisi une autre formule...

 if($_SESSION['formuleChoisie'] != "Active"){
//on créé la fonction
function calculer_distance($adresse1,$adresse2) {
$adresse1 = str_replace(" ", "+", $adresse1); //adresse de départ
$adresse2 = str_replace(" ", "+", $adresse2); //adresse d'arrivée
$url = 'https://maps.googleapis.com/maps/api/directions/xml?origin='.$adresse1.'&destination='.$adresse2.'&key=AIzaSyD_Ygw4nw_-Nwyv9JEtnmt8T6rpaGwFRIE'; //on créé l'url



//on lance une requete aupres de google map avec l'url créée
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
$xml = curl_exec($ch);

//on réccupère les infos
$charger_googlemap = simplexml_load_string($xml);
$distance = $charger_googlemap->route->leg->distance->value;

//si l'info est récupérée, on calcule la distance
if ($charger_googlemap->status == "OK") {
$distance = $distance/1000;
$distance = number_format($distance, 2, '.', ' ');

return $distance;
}
else {
//si l'info n'est pas récupérée, on lui attribu 0
return "0";
}
}
//si le bouton calculer est lancé, on récupère les informations du formulaire et on lance la fonction

$dep = $_SESSION['dep'];
$ari = $_SESSION['ari'];

@$afficheFormulaireInfoPerso = 0;
@$afficheFormulaireFormules2345 = 0;
@$ladistance = (calculer_distance($dep,$ari)*2);
@$volumeTotal = htmlspecialchars($_POST['volume']);
@$etagesDepart = htmlspecialchars($_POST['etagesDepart']);
@$etagesArrive = htmlspecialchars($_POST['etagesArrive']);

$_SESSION['volume'] = $volumeTotal;
$_SESSION['type'] = $_POST['type'];
$_SESSION['etagesDep'] = $_POST['etagesDepart'];
$_SESSION['etagesAri'] = $_POST['etagesArrive'];
$_SESSION['ascenseurDepart'] = $_POST['ascenseurDepart'];
$_SESSION['ascenseurArrive'] = $_POST['ascenseurArrive'];

//-------------------FIXAGE DES PRIX----------------------\\

@$prixM3 = $leprix['prixM3']; // le prix sera variable en fonction de l'admin
@$prixKM = $leprix['prixKM']; // le prix sera variable en fonction de l'admin
@$prixEtage = $leprix['prixEtage']; // le prix sera variable en fonction de l'admin
@$pourcentageMaisonMaison = $leprix['pourcentageMaisonMaison']; // le prix sera variable en fonction de l'admin
@$pourcentageMaisonAppartement = $leprix['pourcentageMaisonAppartement']; // le prix sera variable en fonction de l'admin
@$pourcentageAppartementAppartement = $leprix['pourcentageAppartementAppartement']; // le prix sera variable en fonction de l'admin
@$pourcentageAscenseurOui = $leprix['pourcentageAscenseurOui'] ; // le prix sera variable en fonction de l'admin
@$pourcentageAscenseurNon = $leprix['pourcentageAscenseurNon'] ; // le prix sera variable en fonction de l'admin
@$prixFormuleSimple = $leprix['prixFormuleSimple'] ; // le prix sera variable en fonction de l'admin
@$prixFormuleEco = $leprix['prixFormuleEco'] ; // le prix sera variable en fonction de l'admin
@$prixFormuleZen = $leprix['prixFormuleZen'] ; // le prix sera variable en fonction de l'admin
@$prixFormuleLuxe = $leprix['prixFormuleLuxe'] ; // le prix sera variable en fonction de l'admin

//----- CA CONTINUE.... ------\\
@$prixVolume = ($volumeTotal * $prixM3) ;
@$prixMaisonMaison = ($prixVolume * $pourcentageMaisonMaison/100);
@$prixMaisonAppartement = ($prixVolume * $pourcentageMaisonAppartement/100);
@$prixAppartementAppartement = ($prixVolume * $pourcentageAppartementAppartement/100);

if($_SESSION['formuleChoisie'] == "Simple")
{
$prixService = $prixFormuleSimple ; 
}

if($_SESSION['formuleChoisie'] == "Eco")
{
$prixService = $prixFormuleEco ; 
}

if($_SESSION['formuleChoisie'] == "Zen")
{
$prixService = $prixFormuleZen ; 
}

if($_SESSION['formuleChoisie'] == "Luxe")
{
$prixService = $prixFormuleLuxe ; 
}



if($_POST['type'] == "AppartMaison"){

$prixType = $prixMaisonAppartement;

}

if($_POST['type'] == "MaisonMaison"){

$prixType = $prixMaisonMaison;

}

if($_POST['type'] == "AppartAppart"){

$prixType = $prixAppartementAppartement;

}

if($_POST['ascenseurDepart'] == "oui"){
	@$ascenseurDepart = $prixVolume * ($pourcentageAscenseurOui / 100);
}

if($_POST['ascenseurDepart'] == "non"){
	@$ascenseurDepart = $prixVolume * ($pourcentageAscenseurNon / 100);
}

if($_POST['ascenseurArrive'] == "oui"){
	@$ascenseurArrive = $prixVolume * ($pourcentageAscenseurOui / 100);
}

if($_POST['ascenseurArrive'] == "non"){
	@$ascenseurArrive = $prixVolume * ($pourcentageAscenseurNon / 100);
}

//----------------FIN DES FIXAGE ET CALCUL DES PRIX--------------------\\

//calcul du prix total ( autres formules )


	$prix = ($prixKM * $ladistance) + $prixVolume + ($prixEtage * $etagesDepart) + ($prixEtage * $etagesArrive) + @$prixType + $ascenseurDepart + $ascenseurArrive + $prixService;


	$phraseDistance = "la distance entre " .$dep. " et ".$ari." est de ".calculer_distance($dep,$ari)."KM" ;

	$_SESSION['date'] = $_POST['date'];

}
}
 ?>
<!DOCTYPE html>
<html lang="fr">
    <head>

    	<link href='https://fonts.googleapis.com/css?family=Bitter' rel='stylesheet' type='text/css'>
<style type="text/css">
body{
	background-color:#FFF;
}
.form-style-10{
	width:100%;
	max-width:100%;
	padding:30px;
	
	background: #FFF; 
	border-radius: 10px;
	-webkit-border-radius:10px;
	-moz-border-radius: 10px;
	box-shadow: 0px 0px 18px rgba(0, 0, 0, 0.13);
	-moz-box-shadow: 0px 0px 18px rgba(0, 0, 0, 0.13);
	-webkit-box-shadow: 0px 0px 18px rgba(0, 0, 0, 0.13);
}
@media screen and (min-width: 490px){
	.form-style-10 {
		width:100% !important;
		margin: 2px !important;
	}
	}
.form-style-10 .inner-wrap{
	padding: 30px;
	background-color: #F8F8F8;
	border-radius: 6px;
	margin-bottom: 15px;
}

.form-style-10 h1 > span{
	display: block;
	margin-top: 2px;
	font: 13px Arial, sans-serif;
}
.form-style-10 label{
	display: block;
	font: 16px Arial, sans-serif;
	color: #Ff6666;
	margin-bottom: 15px;
}
.form-style-10 input[type="text"],
.form-style-10 input[type="date"],
.form-style-10 input[type="datetime"],
.form-style-10 input[type="email"],
.form-style-10 input[type="number"],
.form-style-10 input[type="search"],
.form-style-10 input[type="time"],
.form-style-10 input[type="url"],
.form-style-10 input[type="password"],
.form-style-10 textarea,
.form-style-10 select {
	display: block;
	box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	width: 100%;
	padding: 8px;
	border-radius: 6px;
	-webkit-border-radius:6px;
	-moz-border-radius:6px;
	border: 1px solid #fff;
	box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.33);
	-moz-box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.33);
	-webkit-box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.33);
}

.form-style-10 .section{
	font: normal 20px 'Arial', sans-serif;
	color: #Ff6666 ;
	margin-bottom: 10px;
}
.form-style-10 .section span {
	background-color: #Ff6666;
	padding: 5px 10px 5px 10px;
	position: absolute;
	border-radius: 50%;
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	border: 4px solid #fff;
	font-size: 14px;
	margin-left: -45px;
	color: #fff;
	margin-top: -3px;
}
.form-style-10 input[type="button"], 
.form-style-10 input[type="submit"]{
	background-color: #Ff6666;
	padding: 8px 20px 8px 20px;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	color: #fff;
	text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.12);
	font: normal 34px 'Arial', sans-serif;
	-moz-box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.17);
	-webkit-box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.17);
	box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.17);
	border: 1px solid #257C9E;
	font-size: 15px;
}
.form-style-10 input[type="button"]:hover, 
.form-style-10 input[type="submit"]:hover{
	background-color:#00CC99;
	-moz-box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.28);
	-webkit-box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.28);
	box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.28);
}
.form-style-10 .privacy-policy{
	float: right;
	width: 250px;
	font: 12px Arial, sans-serif;
	color: #4D4D4D;
	margin-top: 10px;
	text-align: right;
}

.list-group{
	border : none !important;

}

#etape1{
	font: normal 20px 'Arial', sans-serif;
	color:white;
	background-color:<?php if($afficheFormulaireInfoPerso == 1) {echo "#Ff6666";}else{echo "#00CC99";}; ?> ;
	padding: 20px;
	border-radius: 6px;
}


<?php if($afficheFormulaireInfoPerso != 1) {echo"@media screen and (max-width: 992px){
	#etape1 {
display:none;
	}
	}";}?>
.rond1{
	font: normal 20px 'Arial', sans-serif;
	background-color: #Ff6666;
	padding: 5px 10px 5px 10px;
	position: absolute;
	border-radius: 50%;
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	border: 4px solid #fff;
	font-size: 14px;
	margin-left: -45px;
	color: #fff;
	margin-top: -3px;

}

#etape2{
	font: normal 20px 'Arial', sans-serif;
	color:white;
	background-color: <?php if($afficheFormulaireFormules1 == 1 OR $afficheFormulaireFormules2345==1) {echo "#Ff6666";}else{echo "#00CC99";}; ?>;
	padding: 20px;
border-radius: 6px;
}

.divider{
	background:#BEBEBE;
	margin-top:12px;
	margin-bottom:20px;
	
}

<?php

 if($afficheFormulaireFormules1 == 1 OR $afficheFormulaireFormules2345 == 1 ){
 echo "@media screen and (max-width: 992px){
	#etape2 {
display:block;
	}
	}";}else{echo "@media screen and (max-width: 992px){#etape2 {
display:none;
	}}";}

	?>
.rond2{
font: normal 20px 'Arial', sans-serif;
	background-color: #Ff6666;
	padding: 5px 10px 5px 10px;
	position: absolute;
	border-radius: 50%;
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	border: 4px solid #fff;
	font-size: 14px;
	margin-left: -45px;
	color: #fff;
	margin-top: -3px;
}
#etape3{
	font: normal 20px 'Arial', sans-serif;
	color:white;
	background-color: <?php if($affichePrix == 1) {echo "#Ff6666";}else{echo "#00CC99";}; ?>;
	padding: 20px;
	border-radius: 6px;
}

<?php if($affichePrix != 1) {echo "@media screen and (max-width: 992px){
	#etape3 {
display:none;
	}
	}";}?>
.rond3{
	font: normal 20px 'Arial', sans-serif;
	background-color: #Ff6666;
	padding: 5px 10px 5px 10px;
	position: absolute;
	border-radius: 50%;
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	border: 4px solid #fff;
	font-size: 14px;
	margin-left: -45px;
	color: #fff;
	margin-top: -3px;
}

</style>
<script src="https://kit.fontawesome.com/3e8af49a76.js" crossorigin="anonymous"></script>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title> Devis en ligne - Trans IMJ</title>
        <!--<link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" /> -->
        <!-- Font Awesome icons (free version)-->
   
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/style.css" rel="stylesheet" />

  <!-- autocompletion adresseArrives -->
  <script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=places&language=fr&key=AIzaSyD_Ygw4nw_-Nwyv9JEtnmt8T6rpaGwFRIE"></script>
  <script>
  	document.addEventListener('DOMContentLoaded', function() {
    autocomplete = new google.maps.places.Autocomplete(
       (document.getElementById('origin')),
       { types: ['geocode'], componentRestrictions: {country: 'fr'} }
    );
}, false);

  </script>
  <script>
  	document.addEventListener('DOMContentLoaded', function() {
    autocomplete = new google.maps.places.Autocomplete(
       (document.getElementById('ari')),
       { types: ['geocode'], componentRestrictions: {country: 'fr'} }
    );
}, false);

  </script>
    </head>
    <body>
        <!-- Navigation-->
<section class="page-section" id="devis">
<div  class="container">

<div id="chrono" style="width:100%; height:20%;">
	<div class="row">
		<div class="col-md-12 col-lg-4">
	<div id="etape1"><div class="rond1">1 </div>Coordonnés et formule</div>
</div>
<div class="col-md-12 col-lg-4">
	<div id="etape2"><div class="rond2">2 </div>vous y êtes presque</div>
</div>
<div class="col-md-12 col-lg-4">
	<div id="etape3"><div class="rond3">3 </div>Estimation du prix</div>
</div>
</div>
</div>
<form action="" method="POST">
<?php if($afficheFormulaireInfoPerso == 1) :?>

<div class="form-style-10">
    <div class="divider section" align="center" >Informations</div>
    <div class="inner-wrap">
    	<div class="row">
    		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
              	<label>
					<input type="text" required name="nom" placeholder="Nom" class="form-control  rounded">
			  	</label>
			</div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<label>
				<input type="text" required name="prenom" placeholder="Prénom" class="form-control  rounded">
			</label>
		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label>
				<input type="text" required name="entreprise" placeholder="Nom entreprise" class="form-control  rounded">
			</label>
		</div>
	<div class="row">
	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        	<div class="input-group mb-3">
                     <div class="input-group-prepend"><span class="input-group-text" aria-label="phone"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-telephone" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
</svg></span></div><input type="tel" required id="tel" name="tel" placeholder="Tel. 06 00 00 00 00" class="form-control "></div></div>

		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		<div class="input-group mb-3">
                     <div class="input-group-prepend"><span class="input-group-text" aria-label="arobase">@</span></div>
 					 <input type="mail" required name="mail" placeholder="example@gmail.com" class="form-control "></div>
 			   </div>
 	</div>
 </div>
 		<div class="divider section" align="center" >Choix de segment</div>
 		<div class="inner-wrap">
        <label><select name="formule" required class="form-control rounded">
			<option value="Active">Berline</option>
			<option value="Simple">Sitadine</option>
			<option value="Eco">Sportive</option>
			<option value="Zen">SUV</option>
			<option value="Luxe">Monospace</option>
		</select></label></div>
    <div class="button-section">
     <input type="submit" style="width:100%; margin:0;" name="valider" value="Continuer" class="btn btn-lg btn-primary "/>
    </div>

<?php endif; ?>
<?php if($afficheFormulaireFormules1 == 1) :?>

<div class="row">
<div class="col-sm-12 col-xs-12 col-md-12">
<div class="form-style-10">

    <div class="section"><span>1</span>Formule Active+ , très bon choix.</div>
    <div class="inner-wrap">
    	<div class="row">
    	<div class="col-md-6">
    		<div class="divider" align="center" style="padding-top:12px; padding-bottom:1px;"><label for="origin"><i class="fas fa-map-marker-alt"></i> Lieu de chargement</label></div>
 	<input type="text" id="origin" required name="dep" style="height:50px; margin-bottom:10px;" placeholder="adresse, n° de voie,  Code postal, Commune" class="form-control  rounded">
 </div>
 <br />
<div class="col-md-6">
	<div class="divider" align="center" style="padding-top:12px; padding-bottom:1px;"><label for="ari"><i class="fas fa-map-marker-alt"></i> Lieu de déchargement</label></div>
 	<input type="text" id="ari" required name="ari" style="height:50px;" placeholder="adresse, n° de voie,  Code postal, Commune" class="form-control  rounded">
 </div>
</div>
</div>
<div class="section"><span>2</span>quand voulez-vous déménager ?</div>
    <div class="inner-wrap">
    	<div class="row">
    		<div class="col-md-6">
    			<input type="date" name="date" class="form-control">
    		</div>
    	</div>
	</div>

 	<br>

    <div class="button-section">
     <input type="submit" style="width:100%; margin:0;" name="affichePrix" value="Je calcule mon tarif !" class="btn btn-primary "/>
    </div>
</div>
</div>
</div>
</div>
	<?php endif; ?>
<?php if($afficheFormulaireFormules2345==1) :?>

<div class="form-style-10">

    <div class="section"><span>1</span>calcul de votre devis</div>
    <div class="inner-wrap">
    	<div class="row">
    	<div class="col-md-6">
    		<label for="origin"><i class="fas fa-map-marker-alt"></i> Lieu de chargement</label>
 	<input type="text" id="origin" required name="dep" style="height:50px; margin-bottom:10px;" placeholder="adresse, n° de voie,  Code postal, Commune" class="form-control  rounded">
 </div>
 <br />
<div class="col-md-6">
	<label for="ari"><i class="fas fa-map-marker-alt"></i> Lieu de déchargement</label>
 	<input type="text" id="ari" required name="ari" style="height:50px;" placeholder="adresse, n° de voie,  Code postal, Commune" class="form-control  rounded">
 </div>
</div>
    	<div class="row">
    	<div class="col-md-3">
    		<label for="volume"><i class="fas fa-cube"></i> Volume</label>
 	<input type="number" step=".01" id="volume" required name="volume" style="height:50px; margin-bottom:10px;" class="form-control  rounded">
 </div>
 
 <div class="col-md-3">
 	<label>&nbsp;</label> <a href="calculVolume.php"><button type="button" style="height:50px; margin-bottom:10px; width:100%; color:white; background-color: #00cc99 ;" class="btn" value="Calculer mon volume">Calculer mon volume</button></a>
 </div>
<div class="col-md-6">
	<label for="type"><i class="fas fa-building"></i> Type </label>
	<label><select name="type" id="type" style="height:50px; margin-bottom:10px;" class="form-control  rounded">
				<option value="MaisonMaison">Maison/Maison</option>
				<option value="MaisonAppart">Maison/appartement</option>
				<option value="AppartMaison">Appartement/Maison</option>
				<option value="AppartAppart">Appartement/Appartement</option>
				</select>
		</label>
 </div>
</div>
    	<div class="row">
    	<div class="col-md-6">
    	<label><i class="fas fa-ellipsis-v"></i> Etages au départ</label>
    	<label><input type="number" required  name="etagesDepart" min="0" class="form-control  rounded"></label>
 		</div>

 <br />
<div class="col-md-6">
	<label for="ari"><i class="fas fa-ellipsis-v"></i> Etages à l'arrivée</label>
 	<label><input type="number" required  name="etagesArrive" min="0" class="form-control  rounded"></label>
 </div>
</div> 

<div class="row">
 			<div class="col-xs-6 col-md-6 col-sm-6">
<label>Ascenseur (départ) </label> 
<label><select required name="ascenseurDepart" class="form-control  rounded">
	<option value="non">Non</option>
	<option value="oui">Oui</option>
</select></label>
</div>

<div class="col-xs-6 col-md-6 col-sm-6">
<label>Ascenseur (arrivée)  </label>
<label><select required name="ascenseurArrive" class="form-control  rounded">
	<option value="non">Non</option>
	<option value="oui">Oui</option>
</select></label>
</div>
</div>
</div>
<div class="section"><span>2</span>quand voulez-vous déménager ?</div>
    <div class="inner-wrap">
    	<div class="row">
    		<div class="col-md-6">
    			<label><input type="date" name="date" class="form-control"></label>
    		</div>
    	</div>
<div class="button-section">
	<div class="row">
		<div class="col-md-6 col-xs-12 col-sm-6">
			<label><input style="width:100%; background-color: #Ff6666;" type="submit"  href="devis.php" name="retour" value="Retour" class="btn btn-danger"/></label>
     </div>
     <br/>
    <div class="col-md-6 col-xs-12 col-sm-6">
    	<label><input type="submit" style="width:100%; background-color: #00CC99;" name="affichePrix" value="Je calcule mon tarif !"  class="btn btn-lg btn-primary "/>
    </label>

	</div>
</div>
</div>
</div>
</div>	
 <?php endif; ?>
<?php 
if(isset($_POST['affichePrix'])&&$_SESSION['formuleChoisie']=="Active"){
	$affichePrix = 1;

echo '  <br><div class="row">
    <div class="col-md-6 offset-3 order-md-2 mb-4">
      <H3>
        <span class="text-muted">RECAPITULATIF</span>
      </h3>
        
    	<span class="text-muted">'.$phraseDistance.'</span>
      </div>
      <div class="col-md-6 offset-3 order-md-2 mb-4">
      <ul class="list-group mb-3">
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Distance (aller/retour)</h6>
            <small class="text-muted">ici et la</small>
          </div>
          <span class="text-muted">'.$ladistance.' km</span>
        </li>
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Prix du Service</h6>
            <small class="text-muted">texte ici</small>
          </div>
          <span class="text-muted">'.$prixService.'€</span>
        </li>
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Prix du Vehicule</h6>
            <small class="text-muted">Bref voila</small>
          </div>
          <span class="text-muted">'.$prixVehicule.'€</span>
        </li>
        
        <li class="d-flex justify-content-between">
          <span>Total</span>
          <strong>'.$prix.'€</strong>
        </li>
      </ul></div></div>
       <div align="center"><a href="https://trans-imj.com/devis.php"><button type="button" value="Retour" class="btn btn-danger">Retour</button></a>
  </div>' ;
				$to 		= "contact@trans-imj.com";
  				
				$from		= "DEVIS@transIMJ.com ";
				ini_set("SMTP","smtp.gmail.com");


				$subject 	= "Trans-IMJ.com - Devis de ".$_SESSION['nom'] ;

				$mail_Data = "";
				$mail_Data .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:v="urn:schemas-microsoft-com:vml">';
				$mail_Data .= "<head> \n";
				$mail_Data .= '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
				$mail_Data .= '<meta name="viewport" content="width=device-width; initial scale=1.0; maximum-scale=1.0;">';
				$mail_Data .= "<title> Atd1.fr </title> \n";
				$mail_Data .= "</head> \n";
				$mail_Data .= "<body> \n";
				$mail_Data .= "<br>";
				$mail_Data .= "<label><b>Formule choisie : ACTIVE <span style=\"color:#Ff6666;\">*</span></b></label> ";
		
				$mail_Data .= "<br>";
				$mail_Data .= "Date prévue : ";
				$mail_Data .= $_SESSION['date'];
		
				$mail_Data .= "<br>";
				$mail_Data .= "adresse de départ : ";
				$mail_Data .= $_SESSION['dep'];
				$mail_Data .= '<br>';
				$mail_Data .="<label><b>adresse Arrivée : </label> ";
				$mail_Data .= $_SESSION['ari'];
				$mail_Data .= "<br>";
				$mail_Data .= " informations Client : ";
				$mail_Data .= $_SESSION['nom'] ." | ".$_SESSION['prenom'];
				$mail_Data .= "<br />";
				$mail_Data .= $_SESSION['tel'];
				$mail_Data .= "<br>";
				$mail_Data .= $_SESSION['mail'];
				$mail_Data .= "<br>" ;				
				$mail_Data .= "total : ";
				$mail_Data .= $prix;
				$mail_Data .="<p> Email envoyé automatiquement depuis le site trans-imj.com </p>";				
				$mail_Data .= "<br> \n";
				$mail_Data .= "</body> \n";
				$mail_Data .= "</HTML> \n";

				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
				$headers .= 'Content-Transfer-Encoding: 8bit'."\r\n";
				$headers .= "From: $from  \n";
				$headers .= "Disposition-Notification-To: $from  \n";

				// Message de Priorité haute
				$headers .= "X-Priority: 1  \n";
				$headers .= "X-MSMail-Priority: High \n";
				$CR_Mail = TRUE;

				// 
				$CR_Mail = @mail ($to,utf8_decode($subject), utf8_decode($mail_Data), $headers);

}
if(isset($_POST['affichePrix'])&&$_SESSION['formuleChoisie']!="Active"){
	$affichePrix = 1;
echo '  <br>
<div class="row">
    <div class="col-md-6 offset-3 order-md-2 mb-4">
      <H3>
        <span class="text-muted">RECAPITULATIF</span>
      </h3>
        
    	<span class="text-muted">'.$phraseDistance.'</span>
      </div>
      <div class="col-md-6 offset-3 order-md-2 mb-4">
      <ul class="list-group mb-3">
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Adresse de départ : </h6>
            
          </div>
          <span class="text-muted">'.$_SESSION["dep"].'</span>
        </li>
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">adresse d\'arrivée : </h6>
            
          </div>
          <span class="text-muted">'.$_SESSION["ari"].'</span>
        </li>
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Volume à déménager : </h6>
            
          </div>
          <span class="text-muted">'.$_SESSION["volume"].' m3</span>
        </li>
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Type de déménagement : </h6>
            
          </div>
          <span class="text-muted">'.$_SESSION["type"].'</span>
        </li>
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Ascenseur au départ : </h6>
            
          </div>
          <span class="text-muted">'.$_SESSION["ascenseurDepart"].'</span>
        </li>
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Ascenseur à l\'arrivée : </h6>
            
          </div>
          <span class="text-muted">'.$_SESSION["ascenseurArrive"].'</span>
        </li>
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Nombre d\'étages au départ: </h6>
            
          </div>
          <span class="text-muted">'.$_SESSION["etagesDep"].' étages.</span>
        </li>
        <li class="d-flex justify-content-between lh-condensed">
          <div>
            <h6 class="my-0">Nombre d\'étages à l\'arrivée : </h6>
            
          </div>
          <span class="text-muted">'.$_SESSION["etagesAri"].' étages.</span>
        </li>
        <li class="d-flex justify-content-between">
          <span>Total</span>
          <strong style="font-size:20px;">'.$prix.'€</strong>
        </li>
      </ul></div></div>
      <div align="right">
      <div align="center"><a href="https://trans-imj.com/devis.php"><button type="button" value="Retour" class="btn btn-danger">Retour</button></a></div>
  </div>
  ' ;
				$to 		= "contact@trans-imj.com";
 
				$from		= "DEVIS@transIMJ.com ";
				ini_set("SMTP","smtp.gmail.com");


				$subject 	= "Trans-IMJ.com - Devis de ".$_SESSION['nom'] ;

				$mail_Data = "";
				$mail_Data .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:v="urn:schemas-microsoft-com:vml">';
				$mail_Data .= "<head> \n";
				$mail_Data .= '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
				$mail_Data .= '<meta name="viewport" content="width=device-width; initial scale=1.0; maximum-scale=1.0;">';
				$mail_Data .= "<title> Atd1.fr </title> \n";
				$mail_Data .= "</head> \n";
				$mail_Data .= "<body> \n";
				$mail_Data .= "<br>";
				$mail_Data .= "<label><b>Formule choisie : ".$_SESSION['formuleChoisie']." <span style=\"color:#Ff6666;\">*</span></b></label> ";
				$mail_Data .= "<br>";
				$mail_Data .= "Date prévue : ";
				$mail_Data .= $_SESSION['date'];
				$mail_Data .= "<br>";
				$mail_Data .= "adresse de départ : ";
				$mail_Data .= $_SESSION['dep'];
				$mail_Data .= '<br>';
				$mail_Data .="<label><b>adresse Arrivée : </label> ";
				$mail_Data .= $_SESSION['ari'];
				$mail_Data .= "<br>";
				$mail_Data .= "volume à transporter : ";
				$mail_Data .= $_SESSION['volume'] . "m3";
				$mail_Data .= "<br/>";
				$mail_Data .= "Nombre étages au départ : ";
				$mail_Data .= $_SESSION['etagesDep'];
				$mail_Data .= "<br/>";
				$mail_Data .= "Nombre étages arrivée ";
				$mail_Data .= $_SESSION['etagesAri'] ;
				$mail_Data .= "<br/>";
				$mail_Data .= "Ascenseur au depart : ";
				$mail_Data .= $_SESSION['ascenseurDepart'] ;
				$mail_Data .= "<br/>";
				$mail_Data .= "ascenseur à l'arrivee : ";
				$mail_Data .= $_SESSION['ascenseurArrive'];
				$mail_Data .= "<br/><br/><br/><br/>";
				$mail_Data .= " informations Client : ";
				$mail_Data .= $_SESSION['nom'] ." | ".$_SESSION['prenom'];
				$mail_Data .= "<br />";
				$mail_Data .= $_SESSION['tel'];
				$mail_Data .= "<br>";
				$mail_Data .= $_SESSION['mail'];
				$mail_Data .= "<br>" ;				
				$mail_Data .= "total : ";
				$mail_Data .= $prix;
				$mail_Data .="<p> Email envoyé automatiquement depuis le site trans-imj.com </p>";				
				$mail_Data .= "<br> \n";
				$mail_Data .= "</body> \n";
				$mail_Data .= "</HTML> \n";

				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
				$headers .= 'Content-Transfer-Encoding: 8bit'."\r\n";
				$headers .= "From: $from  \n";
				$headers .= "Disposition-Notification-To: $from  \n";

				// Message de Priorité haute
				$headers .= "X-Priority: 1  \n";
				$headers .= "X-MSMail-Priority: High \n";
				$CR_Mail = TRUE;

				// 
				$CR_Mail = @mail ($to,utf8_decode($subject), utf8_decode($mail_Data), $headers);
}
?>
	<!--<input style="float: right;"type="submit" name="soumettre" value="Soumettre le devis" class="btn btn-success"/>-->
	</div>
	</div>
</form>
</div>
</section>
</body>
</html>