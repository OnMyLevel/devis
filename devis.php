<?php
	// ------------ DEVIS ------------ //
	session_start();
	$afficheFormulaireInfoPerso = 1 ;
	@$afficheFormulaireFormules1 = 0 ;
	@$afficheFormulaireFormules2345 = 0 ;
	@$affichePrix = 0;
	$_SESSION['vehicule']='basique';
	$_SESSION['velectrique']='NON'; 

	function write_object_to_console($data) {
		$console = 'console.log(' . json_encode($data) . ');';
		$console = sprintf('<script>%s</script>', $console);
		echo $console;
	}

	function write_to_console($data) {
		$console = $data;
		if (is_array($console))
			$console = implode(',', $console);   
		echo "<script>console.log('Console: " . $console . "' );</script>";
	}

	function uniqidReal($lenght = 13) {
		// uniqid gives 13 chars, but you could adjust it to your needs.
		if (function_exists("random_bytes")) {
			$bytes = random_bytes(ceil($lenght / 2));
		} elseif (function_exists("openssl_random_pseudo_bytes")) {
			$bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
		} else {
			throw new Exception("no cryptographically secure random function available");
		}
		return substr(bin2hex($bytes), 0, $lenght);
	}

	if(isset($_POST['valider']) && $_POST['formule'] != "Active"){

		$_SESSION['formuleChoisie'] = $_POST['formule'] ;
		$_SESSION['nom'] = $_POST['nom'];
		$_SESSION['prenom'] = $_POST['prenom'];
		$_SESSION['tel'] = $_POST['tel'];
		$_SESSION['mail'] = $_POST['mail'];
		$_SESSION['entrepriseName'] = $_POST['entrepriseName'];
		$_SESSION['entreprise'] = $_POST['entreprise'];
		$_SESSION['vehicule'] = $_POST['vehicule'];
		$_SESSION['velectrique'] = $_POST['velectrique'];

		if(!isset($_POST['numeroChassis'])){
			$_SESSION['textPlaqueDimmatriculation']= "Numéro de plaque: -------------------------";
			$_SESSION['textNumeroChassis']="Numéro de chassis: ".$_POST['numeroChassis']." ";
		} else {
			write_to_console($_POST['plaqueDimmatriculation']);
			$_SESSION['textNumeroChassis']= "Numéro de chassis: -------------------------";
			$_SESSION['textPlaqueDimmatriculation']="Numéro de plaque: ".$_POST['plaqueDimmatriculation'].' ';
		}

		//affichage du formulaire pour les autres formules
		@$afficheFormulaireInfoPerso = 0;
		@$afficheFormulaireFormules2345 = 1;
	}
		
	if(isset($_POST['affichePrix'])){

			if (isset($_POST['dep']) && isset($_POST['ari'])) {
				$_SESSION['dep']=$_POST['dep'];
				$_SESSION['ari']=$_POST['ari'];
			}

			if (isset($_POST['dateDepart']) && isset($_POST['dateArrivee'])) {
				$_SESSION['dateD'] = $_POST['dateDepart'];
				$_SESSION['dateA'] = $_POST['dateArrivee'];	
			}
			$_SESSION['lavage'] = $_POST['lavage'];
			$_SESSION['presentation'] = $_POST['presentation'];
			$_SESSION['gare'] = $_POST['gare'];
			$affichePrix = 1;

		//sinon, si l'utilisateur choisi une autre formule...
 		if($_SESSION['formuleChoisie'] != "Active"){

			 //si le bouton calculer est lancé, on récupère les informations du formulaire et on lance la fonction
			$dep = $_SESSION['dep'];
			$ari = $_SESSION['ari'];
			$presentation = $_SESSION['presentation'];
			$lavage = $_SESSION['lavage'];
			$gare = $_SESSION['gare'];
			@$typeVehicule = $_SESSION['vehicule'];

			@$afficheFormulaireInfoPerso = 0;
			@$afficheFormulaireFormules2345 = 0;
			@$prixKm=0;

			//on créé la fonction
			function calculer_distance($adresse1,$adresse2) {
				$adresse1 = str_replace(" ", "+", $adresse1); //adresse de départ
				$adresse2 = str_replace(" ", "+", $adresse2); //adresse d'arrivée
				$url = 'https://maps.googleapis.com/maps/api/directions/xml?origin='.$adresse1.'&destination='.$adresse2.'&key=AIzaSyAuWuheVITrx-U6I18n0RI0P2J0ZgmNwcE'; //on créé l'url
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
					$distanceFormat = number_format($distance, 2, '.', ' ');
					$_SESSION['distanceFormat']=$distanceFormat;
					return $distance;
				} else {
					//si l'info n'est pas récupérée, on lui attribu 0
					return 0;
				}
			}

			function calcule_prix($distance,$typeVehicule) {
				// ------------ TARIFS ------------//
				@$tarifs = array(
					// tarifs basique   
					"basique" => array(
						"cinquantekm" => 90, // Tarif pour une distance totale inférieure ou égale à 50km (HT)
						"supKm" => 0.60, // Tarif du Kilomètre Suppélementaire (HT)
					),
					// tarifs 9m3
					"neufMcube" => array(
						"cinquantekm" => 95, // Tarif pour une distance totale inférieure ou égale à 50km (HT)
						"supKm" => 0.65, // Tarif du Kilomètre Suppélementaire (HT)
					),
					// tarifs 12m3
					"douzeMcube" => array(
						"cinquantekm" => 100, // Tarif pour une distance totale inférieure ou égale à 50km (HT)
						"supKm" => 0.70, // Tarif du Kilomètre Suppélementaire (HT)
					),
					// tarifs 15m3
					"quinzeMcube" => array(
						"cinquantekm" => 105, // Tarif pour une distance totale inférieure ou égale à 50km (HT)
						"supKm" => 0.75, // Tarif du Kilomètre Suppélementaire (HT)
					),
					// tarifs 20m3
					"vingtMcube" => array(
						"cinquantekm" => 105, // Tarif pour une distance totale inférieure ou égale à 50km (HT)
						"supKm" => 0.80, // Tarif du Kilomètre Suppélementaire (HT)
					),
					// tarifs 25m3
					"vingtCinqMcube" => array(
						"cinquantekm" => 115, // Tarif pour une distance totale inférieure ou égale à 50km (HT)
						"supKm" => 0.85, // Tarif du Kilomètre Suppélementaire (HT)
					),
					// tarifs 30m3
					"trenteMcube" => array(
						"cinquantekm" => 120, // Tarif pour une distance totale inférieure ou égale à 50km (HT)
						"supKm" => 0.90, // Tarif du Kilomètre Suppélementaire (HT)
					)
				);

				if($distance>50.0){
					$prixKm = ($distance - 50.0) * $tarifs[$typeVehicule]['supKm'];
					$prixKm = $prixKm + $tarifs[$typeVehicule]['cinquantekm'];
				} else {
					$prixKm = $tarifs[$typeVehicule]['cinquantekm'];
				}
				return round($prixKm,2);
			}

			function calcule_options($prixKM){
				@$prixFinal=$prixKM;
				if($_SESSION['presentation']=='OUI'){
					$_SESSION['presentation']='OUI';
					$prixFinal=$prixFinal+20;
				} 
				else {
					$_SESSION['presentation']='NON';
				}
				if($_SESSION['lavage']=='OUI'){
					$_SESSION['lavage']='OUI';
					$prixFinal=$prixFinal+20;
				}
				else {
					$_SESSION['lavage']='NON';
				}
				if($_SESSION['gare']=='OUI'){
					$_SESSION['gare']='OUI';
					$prixFinal=$prixFinal+20;
				}else{
					$_SESSION['gare']='NON';
				}
				if($_SESSION['velectrique']=='OUI'){
					$_SESSION['velectrique']='OUI';
					$prixFinal=$prixFinal+100;
				}else{
					$_SESSION['velectrique']='NON';
				}
				$_SESSION['prixTotal']=$prixFinal;
				return $prixFinal;
			}

			function sous_24h($dateD){
				$cDate = strtotime(date($dateD));
				if($cDate <= (time() + 86400)){
					$_SESSION['prixDouble'] = 'OUI';
					return true;
				}
				else
				{	
					$_SESSION['prixDouble'] = 'NON';
					return false; 
				}
			}

			function prixAvecTaxe($prix){
				@$prixTTC=0;
				if($_SESSION['entreprise']=='OUI'){
					$prixTTC=round($prix+$prix * (20/100),2); 
				}
				return $prixTTC;
			}

			//----------------FIN DES FIXAGE ET CALCUL DES PRIX--------------------\\
			//calcul du prix total ( autres formules )

			@$ladistance = (calculer_distance($dep,$ari));
			@$prixKm=calcule_prix($ladistance,$typeVehicule);
			@$prixTotal=0;
			@$prixTotalTTC=0;
			@$textPrixTTC='';
			@$mailPrixTTC='';
			$_SESSION['textDateD']=date("Y/d/m H:i",strtotime($_SESSION['dateD']));  
			$_SESSION['textDateA']=date("Y/d/m H:i",strtotime($_SESSION['dateA'])); 

			if(sous_24h($_SESSION['dateD'])==true){
				$prixTotal = calcule_options($prixKm+100);
				$prixTotalTTC = prixAvecTaxe($prixTotal);
				$_SESSION['prix_total']=$prixTotal;
			}else{
				$prixTotal = calcule_options($prixKm);
				$prixTotalTTC = prixAvecTaxe($prixTotal);
				$_SESSION['prix_total']=$prixTotal;
			}
			
			if($_SESSION['entreprise']=='OUI' && isset($_SESSION['entrepriseName'])){
				$textPrixTTC = '<li class="d-flex justify-content-between">
									<span>Total HT</span>
									<strong>'.$prixTotal.' €</strong>
								</li>
								<li class="d-flex justify-content-between">
									<span>Total TTC</span>
									<strong>'.$prixTotalTTC.' €</strong>
								</li>';
				$_SESSION['mailPrix']= "Prix total HT: ".$prixTotal." Euros <br> 
							   			Prix total avec TTC: "." ".$prixTotalTTC." Euros";
				$nomEntreprise="Nom de l'entreprise: ".$_SESSION['entrepriseName']."<br>";
			}else{
				$textPrixTTC = '<li class="d-flex justify-content-between">
									<span>Total HT</span>
									<strong>'.$prixTotal.' €</strong>
								</li>';
				$_SESSION['mailPrix']= "Prix total HT: "." ".$prixTotal." Euros";
				$nomEntreprise="-------------------------";
			}
			@$phraseDistance = " <strong> Adresse de départ </strong>" .$dep. "|  <strong> Adresse d'arrivée </strong>".$ari." ";
		}
 	}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Convoyage Incygne">
    <meta name="author" content="Convoyage Incygne">
    <meta name="keywords" content="Convoyage Incygne">
    <!-- Title Page-->
    <title>Convoyage Incygne</title>
    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i"
        rel="stylesheet">
    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">
    <!-- Main CSS-->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href='https://fonts.googleapis.com/css?family=Bitter' rel='stylesheet' type='text/css'>
    <style type="text/css">
    @import url('https://fonts.googleapis.com/css2?family=Merriweather+Sans:wght@800&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Oswald&display=swap');

    body {
        font-size: 16px;
        font-family: 'Oswald', sans-serif;
    }

    .container {
        width: 100%;
    }

    .form-style-10 {
        width: 100%;
        max-width: 100%;
        padding: 30px;
        margin-top: 10%;
        background: #FFF;
        border-radius: 10px;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        box-shadow: 0px 0px 18px rgba(0, 0, 0, 0.13);
        -moz-box-shadow: 0px 0px 18px rgba(0, 0, 0, 0.13);
        -webkit-box-shadow: 0px 0px 18px rgba(0, 0, 0, 0.13);
    }

    @media screen and (min-width: 490px) {
        .form-style-10 {
            width: 100% !important;
            margin: 2px !important;
            line-height: 250%;
        }
    }

    .form-style-10 .inner-wrap {

        background-color: #Ffffff;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .form-style-10 h1>span {
        display: block;
        margin-top: 2px;
        font: 13px 'Merriweather Sans', sans-serif;
    }

    .form-style-10 label {
        display: block;
        font: 16px 'Oswald', sans-serif;
        color: #ff4c00;
        margin-bottom: 15px;
        padding-top: 1em !important;
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
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border: 1px solid #fff;
        box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.33);
        -moz-box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.33);
        -webkit-box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.33);
    }

    .form-style-10 .section {
        font: normal 12px 'Merriweather Sans', sans-serif !important;
        background-color: #000000;
        text-align: center;
        color: #FFF;
        margin-bottom: 1em;
        margin-top: 1em;


        margin-bottom: 1em;
        padding-top: 1em;
        padding-bottom: 1em;
        border-radius: 6px;
        border-width: 0px !important;
        border-color: #ff4c00;
        border-radius: 100px;
        letter-spacing: 5px;
        font-size: 18px;
        font-weight: 900 !important;
        font-style: italic !important;
        text-transform: uppercase !important;
        background-color: #ff4c00;
    }

    .form-style-10 .section span {
        background-color: #ff4c00;
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
    .form-style-10 input[type="submit"],
    .form-style-10 input[class="submit"] {
        background-color: #ff4c00;
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
    .form-style-10 input[type="submit"]:hover,
    .form-style-10 input[class="submit"]:hover {
        background-color: #F4B400;
        -moz-box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.28);
        -webkit-box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.28);
        box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.28);
    }

    .form-style-10 .privacy-policy {
        float: right;
        width: 250px;
        font: 12px 'Merriweather Sans', sans-serif;
        color: #4D4D4D;
        margin-top: 10px;
        text-align: right;
    }

    .list-group {
        border: none !important;
    }

    #etape1 {
        font: normal 20px 'Merriweather Sans', sans-serif;
        color: white;

        background-color: <?php if($afficheFormulaireInfoPerso==1) {
            echo "#ff4c00";
        }

        else {
            echo "#262d3f";
        }

        ;
        ?>;
        padding: 20px;
        border-radius: 6px;
    }

    <?php if($afficheFormulaireInfoPerso !=1) {
        echo "@media screen and (max-width: 992px){
#etape1 {
            display: none;
        }
    }

    ";}
?>.rond1 {
        font: normal 20px 'Merriweather Sans', sans-serif;
        background-color: #ff4c00;
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

    #etape2 {
        font: normal 20px 'Merriweather Sans', sans-serif;
        color: white;

        background-color: <?php if($afficheFormulaireFormules1==1 OR $afficheFormulaireFormules2345==1) {
            echo "#ff4c00";
        }

        else {
            echo "#262d3f";
        }

        ;
        ?>;
        padding: 20px;
        border-radius: 6px;
    }

    .divider {
        background: #212529;
        margin-top: 12px;
        margin-bottom: 20px;
    }

    <?php if($afficheFormulaireFormules1==1 OR $afficheFormulaireFormules2345==1) {
        echo "@media screen and (max-width: 992px){
#etape2 {
            display: block;
        }
    }

    ";

    }

    else {
        echo "@media screen and (max-width: 992px) {
#etape2 {
            display: none;
        }
    }

    ";		

    }

    ?>.rond2 {
        font: normal 20px 'Merriweather Sans', sans-serif;
        background-color: #ff4c00;
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

    #etape3 {
        font: normal 20px 'Merriweather Sans', sans-serif;
        color: white;

        background-color: <?php if($affichePrix==1) {
            echo "#ff4c00";
        }

        else {
            echo "#262d3f";
        }

        ;
        ?>;
        padding: 20px;
        border-radius: 6px;
    }

    <?php if($affichePrix !=1) {
        echo "@media screen and (max-width: 992px){
#etape3 {
            display: none;
        }
    }

    ";

    }

    ?>.rond3 {
        font: normal 20px 'Merriweather Sans', sans-serif;
        background-color: #ff4c00;
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

    .radio {
        color: black;
    }

    .form-style-10 input[type="button"],
    .form-style-10 input[type="submit"],
    .form-style-10 input[class="submit"] {
        border-width: 0px !important;
        border-color: #ff4c00;
        border-radius: 100px;
        letter-spacing: 5px;
        font-size: 18px;
        font-weight: 900 !important;
        font-style: italic !important;
        text-transform: uppercase !important;
        background-color: #ff4c00;
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
        -webkit-appearance: none;
        font-size: 16px;
        font-family: 'Oswald', sans-serif;
        color: #d1d1d1;
        background-color: #fff;
        width: 100%;
        font-weight: 400;
        border-width: 0;
        border-radius: 3px;
    }

    .rounded {
        border-radius: -246.75rem !important;
    }

    .form-control {
        display: block;
        width: 100%;
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.9em;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #bbb;
        border-radius: 0.25rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        font: normal 1em 'Merriweather Sans', sans-serif;
    }

    .form-style-10 .section {
        font: normal 20px 'Merriweather Sans', sans-serif;
        background-color: #000000;
        text-align: center;
        color: #FFF;
        margin-bottom: 10px;
    }

    .divider {
        background: #71b8ff;
        margin-top: 12px;
        margin-bottom: 20px;
    }

    #main-footer {
        background-color: #222;
    }

    .input-group>.custom-file,
    .input-group>.custom-select,
    .input-group>.form-control,
    .input-group>.form-control-plaintext {
        position: relative;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        width: 1%;
        margin-bottom: 0;
        font-size: 16px;
        font-family: 'Oswald', sans-serif;
        color: #212529;
    }

    .form-control {
        display: block;
        width: 100%;
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.9em;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #bbb;
        border-radius: 0.25rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        font: normal 1em 'Merriweather Sans', sans-serif;
        font-size: 16px;
        font-family: 'Oswald', sans-serif;
        color: #212529;
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
        -webkit-appearance: none;
        font-size: 16px;
        font-family: 'Oswald', sans-serif;
        color: #d1d1d1;
        background-color: #fff;
        width: 100%;
        font-weight: 400;
        border-width: 0;
        border-radius: 3px;
        font-size: 16px;
        font-family: 'Oswald', sans-serif;
        color: #000000;
    }
    </style>
    <script src="https://kit.fontawesome.com/3e8af49a76.js" crossorigin="anonymous"></script>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/datepicker/moment.min.js"></script>
    <script src="vendor/datepicker/daterangepicker.js"></script>
    <!-- Main JS-->
    <script src="js2/global.js"></script>
    <title> Devis en ligne - Incygne</title>
    <!--<link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" /> -->
    <!-- Font Awesome icons (free version)-->
    <!-- autocompletion adresseArrives -->
    <script
        src="https://maps.googleapis.com/maps/api/js?&v=3&libraries=places&key=AIzaSyAuWuheVITrx-U6I18n0RI0P2J0ZgmNwcE">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script src="./dist/jquery.geocomplete.js"></script>
    <script src="./dist/logger.js"></script>
    <script>
    $(function() {
        $(".geocomplete").geocomplete({
            details: ".details",
            detailsScope: '.location',
            types: ["geocode", "establishment"],
            country: 'fr'
        });

        $(".find").click(function() {
            $(this).parents(".location").find(".geocomplete").trigger("geocode");
        });
    });
    </script>
    </script>
    </meta>
</head>
<br />

<body>
    <section class="page-wrapper bg-gra-03 p-t-45 p-b-50" id="devis">
        <div class="container">
            <!-- Navigation-->
            <nav>
                <div id="chrono" style="width:100%;">
                    <div class="row">
                        <div class="col-md-12 col-lg-4">
                            <div id="etape1">
                                <div class="rond1">1 </div>Coordonnées client
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4">
                            <div id="etape2">
                                <div class="rond2">2 </div>Infos sur le convoyage
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4">
                            <div id="etape3">
                                <div class="rond3">3 </div>Estimation du devis
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            <br>
            <!-- Formulaire de devis-->
            <form action="" method="POST">
                <?php if($afficheFormulaireInfoPerso == 1) :?>
                <!-- Partie 1-->
                <div class="form-style-10">
                    <div class="divider section" text-align: center;>Informations générales </div>
                    <div class="inner-wrap">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <input type="text" required name="nom" placeholder="Nom" class="form-control  rounded">
                                </label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <input type="text" required name="prenom" placeholder="Prénom"
                                    class="form-control  rounded">
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <label>
                                    <i class='fas fa-user-tie'></i>
                                    Êtes-vous un professionnel ?
                                </label>
                                <label>
                                    <select name="entreprise" required class="form-control rounded">
                                        <option value="NON" selected="selected">Non</option>
                                        <option value="OUI">Oui</option>
                                    </select>
                                </label>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <label>
                                    <i class='fa fa-briefcase'></i>
                                    La raison sociale de votre entreprise ?
                                </label>
                                <label>
                                    <input type="text" name="entrepriseName" placeholder="Nom entreprise"
                                        class="form-control  rounded">
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>
                                    <i class='fa fa-phone'></i>
                                    Votre numéro de téléphone
                                </label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend"><span class="input-group-text"
                                            aria-label="phone"><svg width="1em" height="1em" viewBox="0 0 16 16"
                                                class="bi bi-telephone" fill="currentColor"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                                            </svg></span></div><input type="tel" required id="tel" name="tel"
                                        placeholder="Tel. 06 00 00 00 00" class="form-control ">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>
                                    <i class='fa fa-envelope-open'></i>
                                    Votre adresse courriel
                                </label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend"><span class="input-group-text"
                                            aria-label="arobase">@</span></div>
                                    <input type="mail" required name="mail" placeholder="example@gmail.com"
                                        class="form-control ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="divider section" text-align: center;>Choix de catégorie</div>
                    <div class="inner-wrap">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>
                                    <i class="fas fa-car-battery"></i>
                                    La voiture est électrique ?
                                </label>
                                <select name="velectrique" required class="form-control rounded">
                                    <option value="OUI">Oui</option>
                                    <option value="NON" selected="selected">Non</option>
                                </select>

                                </label>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <label>
                                    <i class="fas fa fa-car"></i>
                                    Type de véhicule
                                </label>
                                <select name="vehicule" required class="form-control rounded">
                                    <option value="basique">Basique (Citadine, Berline, Sportive, SUV, Monospace, 3m3,
                                        6m3)</option>
                                    <option value="9m3">9m3</option>
                                    <option value="12m2">12m3</option>
                                    <option value="15m2">15m3</option>
                                    <option value="20m3">20m3</option>
                                    <option value="25m3">25m3</option>
                                    <option value="30m3">30m3</option>
                                </select>
                                </label>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                <label>
                                    <input type="text" name="plaqueDimmatriculation"
                                        placeholder="Plaque d'immatriculation" class="form-control  rounded">
                                </label>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="text-align:center;">
                                OU
                            </div>
                            <div class=" col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                <label>
                                    <input type="number" name="numeroChassis" placeholder="Numéro de chasssis"
                                        class="form-control  rounded">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="button-section">
                        <input type="submit" style="width:100%; margin:0;" name="valider" value="Continuer "
                            class="btn btn-lg btn-primary " />
                    </div>
                    <?php endif; ?>
                    <?php if($afficheFormulaireFormules1 == 1) :?>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col-md-12">
                            <div class="form-style-10">
                                <label> ERREUR DANS LE DEVIS <label>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($afficheFormulaireFormules2345==1) :?>
                    <!-- Partie 2 -->
                    <div class="form-style-10">
                        <div class="section">Informations pratiques</div>
                        <div class="inner-wrap">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>
                                        <i class="fas fa-map-marker-alt"></i>
                                        Lieu de départ
                                    </label>
                                    <input type="text" class="geocomplete" required name="dep"
                                        style="height:50px; margin-bottom:10px;"
                                        placeholder="adresse, n° de voie,  Code postal, Commune"
                                        class="form-control  rounded">
                                </div>
                                <br />
                                <div class="col-md-6">
                                    <label>
                                        <i class="fas fa-map-marker-alt"></i>
                                        Lieu d'arrivée
                                    </label>
                                    <input type="text" class="geocomplete" required name="ari" style="height:50px;"
                                        placeholder="adresse, n° de voie,  Code postal, Commune"
                                        class="form-control  rounded">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        <i class="fas fa-commenting"></i>
                                        Vous souhaitez une présentation ?
                                    </label>
                                    <label>
                                        <input class="radio" type="radio" name="presentation" value="OUI" required> Oui
                                        <input class="radio" type="radio" name="presentation" value="NON"> Non
                                    </label>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label>
                                        <i class="fas fa-hands-wash"></i>
                                        Souhaitez-vous l'option de lavage ?
                                    </label>
                                    <label>
                                        <input class="radio" type="radio" name="lavage" value="OUI" required> Oui
                                        <input class="radio" type="radio" name="lavage" value="NON"> Non
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label>
                                        <i class="fas fa-train"></i>
                                        Gare à moins de 5 kilomètres ?
                                    </label>
                                    <label>
                                        <input class="radio" type="radio" name="gare" value="OUI" required> Oui
                                        <input class="radio" type="radio" name="gare" value="NON"> Non
                                    </label>
                                </div>
                            </div>
                            <div class="section">Plannification du convoyage</div>
                            <div class="inner-wrap">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>
                                            <i class="fas fa-calendar"></i>
                                            Date et heure de départ
                                        </label>
                                        <label>
                                            <input name="dateDepart" type="datetime-local" class="form-control">
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label>
                                            <i class="fas fa-calendar"></i>
                                            Date et heure d'arrivée
                                        </label>
                                        <label>
                                            <input type="datetime-local" name="dateArrivee" class="form-control">
                                        </label>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="info" style="color:black;">
                                            * Attention si la date et l'heure de départ, est dans les 24 heures, 100€
                                            seront ajouter sur le prix final.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="button-section">
                                <div class="row">
                                    <br />
                                    <div class="col-md-12 col-xs-12 col-sm-6">
                                        <label>
                                            <input type="submit" style="width:100%; background-color: #ff4c00;"
                                                name="affichePrix" value="Je calcule mon tarif"
                                                class="btn btn-lg btn-primary " />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php 
							if(isset($_POST['affichePrix'])){
								$_SESSION['uniqueId']=uniqidReal();
								@$recapDevis='
								<div class="form-style-10">
									<div class="row" style="align:center; width:100%;">
									<div class="col-sm-12 col-xs-12 col-md-12">
									<H3>
										<span class="text-muted">Recapitulatif de votre devis</span>
										<br>
										<span class="text-muted">Réference: '.$_SESSION['uniqueId'].'</span>
									</h3>
									</div>
									<div class="col-sm-12 col-xs-12 col-md-12">
									<ul class="list-group mb-3">
									<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Adresse de départ</h6>
										</div>
										<span class="text-muted">'.$dep.'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Adresse d\'arrivée</h6>
										</div>
										<span class="text-muted">'.$ari.'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Dates départ</h6>
										</div>
										<span class="text-muted">'.$_SESSION['textDateD'].'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Dates d\'arrivée</h6>
										</div>
										<span class="text-muted">'.$_SESSION['textDateA'].'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Distance</h6>
											<small class="text-muted">Distance total en Km</small>
										</div>
										<span class="text-muted">'.$_SESSION['distanceFormat'].'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Type de vehicule </h6>
											<small class="text-muted">Catégorie</small>
										</div>
										<span class="text-muted">'.$typeVehicule.'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Tarif sur la distance </h6>
											<small class="text-muted">(HT)</small>
										</div>
										<span class="text-muted">'.$prixKm.'€</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Lavage</h6>
											<small class="text-muted">Prix Final +20€</small>
										</div>
										<span class="text-muted">'.$_SESSION['lavage'].'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Presentation</h6>
											<small class="text-muted">Prix final+20€</small>
										</div>
										<span class="text-muted">'.$_SESSION['presentation'].'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Gare à proximité</h6>
											<small class="text-muted">Prix final+20€</small>
										</div>
										<span class="text-muted">'.$_SESSION['gare'].'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Véhicule electrique</h6>
											<small class="text-muted">Prix du véhicule +100€</small>
										</div>
										<span class="text-muted">'.$_SESSION['velectrique'].'</span>
										</li>
										<li class="d-flex justify-content-between lh-condensed">
										<div>
											<h6 class="my-0">Demande sous 24h</h6>
											<small class="text-muted">Tarif du kimlométrage +100 </small>
										</div>
										<span class="text-muted"> '.$_SESSION['prixDouble'].'</span>
										'.$textPrixTTC.'
									</ul>
									</div>
									</div>
									<div class="button-section">
										<div class="row">
										<div class="col-sm-12 col-xs-12 col-md-12">
												<label>
													<a href="https://www.convoyage-incygne.fr">
														<input style=" align:center; width:100%; background-color: black;" type="submit" name="envoyer" value="Commander la mission" class="btn btn-danger" />
													</a>
													</label>
											</div>
											<div class="col-sm-12 col-xs-12 col-md-12">
												<label>
												<a href="https://www.convoyage-incygne.fr/devis.php">
													<input style=" align:center; width:100%; background-color: #F4B400;" type="submit" name="recommencer" value="recommencer" class="btn btn-danger" />
												</a>
												</label>
											</div>
										</div>
									</div>
								</div>' ;
								$affichePrix = 1;
								$_SESSION['affichePrix']=$affichePrix;
								echo $recapDevis.'';
							}
						?>
                        <?php 
						@$CR_Mail = FALSE;
						if($_POST['envoyer']=="Commander la mission" && $_SESSION['affichePrix']==1){
								$to = "convoyage@incygne.fr;".$_SESSION['mail']."";
								$from = "devis@convoyage-incygne.com";
								ini_set("SMTP","smtp.gmail.com");
								$subject = "convoyage-incygne.com - devis de ".$_SESSION['nom']." ".$_SESSION['nom'];
								$mail_Data = "";
								$mail_Data .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> <html xmlns:v="urn:schemas-microsoft-com:vml">';
								$mail_Data .= "<head> \n";
								$mail_Data .= '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
								$mail_Data .= '<meta name="viewport" content="width=device-width; initial scale=1.0; maximum-scale=1.0;">';
								$mail_Data .= "<title> Atd1.fr </title> \n";
								$mail_Data .= "</head> \n";
								$mail_Data .= "<body> \n";
								$mail_Data .= "Référence du devis: ".$_SESSION['uniqueId'];
								$mail_Data .= "<br>";
								$mail_Data .= "Information sur client ";
								$mail_Data .= "<br>";
								$mail_Data.="".$nomEntreprise;
								$mail_Data .= "Nom et prénom: ".$_SESSION['nom'] ." ".$_SESSION['prenom'];
								$mail_Data .= "<br>";
								$mail_Data .= "Numéro de téléphone: ".$_SESSION['tel'];
								$mail_Data .= "<br>";
								$mail_Data .= "Adresse mail: ".$_SESSION['mail'];
								$mail_Data .= "<br>" ;
								$mail_Data .= " Information sur le convoyage : ";
								$mail_Data .= "<br>";
								$mail_Data .= "Distance total : ".$_SESSION['distanceTotal']." KM";
								$mail_Data .= "<br>";
								$mail_Data .= "Type véhicule : ".$_SESSION['vehicule'];
								$mail_Data .= "<br>";
								$mail_Data .= "Date de départ : "." ".$_SESSION['textDateD'];
								$mail_Data .= "<br>";
								$mail_Data .= "Date d'arrivée: "." ".$_SESSION['textDateA'];
								$mail_Data .= "<br>";
								$mail_Data .= "Information sur les options ";
								$mail_Data .= "<br>";
								$mail_Data .= "Adresse de départ : "." ".$_SESSION['dep'];
								$mail_Data .= "<br>";
								$mail_Data .= "Adresse d'arrivée: "." ".$_SESSION['ari'];
								$mail_Data .= "<br>";
								$mail_Data .= "Gare à proximité : "." ".$_SESSION['gare'];
								$mail_Data .= "<br>";
								$mail_Data .= "Lavage : "." ".$_SESSION['lavage'];
								$mail_Data .= "<br>";
								$mail_Data .= "Presentation : "."  ".$_SESSION['presentation'];
								$mail_Data .= "<br>";
								$mail_Data .= "Sous 24h00: "." ".$_SESSION['prixDouble'];
								$mail_Data .= "<br>";
								$mail_Data .= "".$_SESSION['textPlaqueDimmatriculation'];
								$mail_Data .= "<br>";
								$mail_Data .= "".$_SESSION['textNumeroChassis'];
								$mail_Data .= "<br>";			
								$mail_Data .= "".$_SESSION['mailPrix'];					
								$mail_Data .="<p> Email envoyé automatiquement depuis le site convoyage-incygne.com </p>";				
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
								$CR_Mail = @mail ($to,utf8_decode($subject), utf8_decode($mail_Data), $headers);
								$affichePrix=0;
								$_SESSION['affichePrix']=0;
								$_POST['envoyer']= '';
							 }
							?>
            </form>
        </div>
    </section>
    <br>
</body>

</html>