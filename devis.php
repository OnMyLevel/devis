<?php
	
	// ------------ DEVIS ------------ //
	session_start();
	$afficheFormulaireInfoPerso = 1 ;
	@$afficheFormulaireFormules1 = 0 ;
	@$afficheFormulaireFormules2345 = 0 ;
	@$affichePrix = 0;
	$_SESSION['vehicule']='basique';

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

	if(isset($_POST['valider']) && $_POST['formule'] != "Active"){

		$_SESSION['formuleChoisie'] = $_POST['formule'] ;
		$_SESSION['nom'] = $_POST['nom'];
		$_SESSION['prenom'] = $_POST['prenom'];
		$_SESSION['tel'] = $_POST['tel'];
		$_SESSION['mail'] = $_POST['mail'];
		$_SESSION['entreprise'] = $_POST['entreprise'];
		$_SESSION['vehicule'] = $_POST['vehicule'];

		//affichage du formulaire pour les autres formules
		@$afficheFormulaireInfoPerso = 0;
		@$afficheFormulaireFormules2345 = 1;
	}
		
	if(isset($_POST['affichePrix'])){

			$_SESSION['dep']=$_POST['dep'];
			$_SESSION['ari']=$_POST['ari'];
			$_SESSION['dateD'] = $_POST['dateDepart'];
			$_SESSION['lavage'] = $_POST['lavage'];
			$_SESSION['presentation'] = $_POST['presentation'];
			$affichePrix = 1;

		if($_SESSION['formuleChoisie'] == "Active"){
			write_to_console("MAUVAIS FORMULE");
   		}

		//sinon, si l'utilisateur choisi une autre formule...
 		if($_SESSION['formuleChoisie'] != "Active"){

			 //si le bouton calculer est lancé, on récupère les informations du formulaire et on lance la fonction
			$dep = $_SESSION['dep'];
			$ari = $_SESSION['ari'];
			$presentation = $_SESSION['presentation'];
			$lavage = $_SESSION['lavage'];
			@$typeVehicule = $_SESSION['vehicule'];

			@$afficheFormulaireInfoPerso = 0;
			@$afficheFormulaireFormules2345 = 0;
			@$prixKm=0;

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
				} else {
					//si l'info n'est pas récupérée, on lui attribu 0
					return "0";
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
				return $prixKm;
			}
            
			function sous_24h($dateD){
				$cDate = strtotime(date($dateD));
				if($cDate <= (time() + 86400)){
					write_to_console('oui');
					$_SESSION['prixDouble'] = 'OUI';
					return true;
				}
				else
				{	
					write_to_console('Non');
					$_SESSION['prixDouble'] = 'NON';
					return false; 
				}
			}

			function calcule_options($prixKM){
				@$prixFinal=$prixKM;
				if($_SESSION['presentation']){
					$_SESSION['presentation']='OUI';
					$prixFinal=$prixFinal+20;
				} 
				else {
					$_SESSION['presentation']='NON';
				}
				if($_SESSION['lavage']){
					$_SESSION['lavage']='OUI';
					$prixFinal=$prixFinal+20;
				}
				else {
					$_SESSION['lavage']='NON';
				}
				return $prixFinal;
			}

			function gare_a_5km($prixKM){

			}

			//----------------FIN DES FIXAGE ET CALCUL DES PRIX--------------------\\
			//calcul du prix total ( autres formules )

			@$ladistance = (calculer_distance($dep,$ari)*2);
			@$prixKm=calcule_prix($ladistance,$typeVehicule);
			@$prixTotal=0;
			if(sous_24h($_SESSION['dateD'])==true){
				write_to_console('sous_24h');
				$prixTotal = calcule_options($prixKm*2);
			}else{
				$prixTotal = calcule_options($prixKm);
			}
			@$phraseDistance = "La distance entre " .$dep. " et ".$ari." est de ".$ladistance."KM" ;
		}
 	}
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<link href='https://fonts.googleapis.com/css?family=Bitter' rel='stylesheet' type='text/css'>
		<style type="text/css">
			body {
				background-color: #FFF;
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
				}
			}

			.form-style-10 .inner-wrap {
				padding: 30px;
				background-color: #F8F8F8;
				border-radius: 6px;
				margin-bottom: 15px;
			}

			.form-style-10 h1>span {
				display: block;
				margin-top: 2px;
				font: 13px Arial, sans-serif;
			}

			.form-style-10 label {
				display: block;
				font: 16px Arial, sans-serif;
				color: #DB4437;
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
				-webkit-border-radius: 6px;
				-moz-border-radius: 6px;
				border: 1px solid #fff;
				box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.33);
				-moz-box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.33);
				-webkit-box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.33);
			}

			.form-style-10 .section {
				font: normal 20px 'Arial', sans-serif;
				background-color: #000000;
				text-align:center;
				color: #FFF;
				margin-bottom: 10px;
			}

			.form-style-10 .section span {
				background-color: #DB4437;
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
			.form-style-10 input[type="submit"] {
				background-color: #DB4437;
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
			.form-style-10 input[type="submit"]:hover {
				background-color: #F4B400;
				-moz-box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.28);
				-webkit-box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.28);
				box-shadow: inset 0px 2px 2px 0px rgba(255, 255, 255, 0.28);
			}

			.form-style-10 .privacy-policy {
				float: right;
				width: 250px;
				font: 12px Arial, sans-serif;
				color: #4D4D4D;
				margin-top: 10px;
				text-align: right;
			}

			.list-group {
				border: none !important;
			}

			#etape1 {
				font: normal 20px 'Arial', sans-serif;
				color: white;
				background-color:
								<?php 
									if($afficheFormulaireInfoPerso==1) {
										echo "#DB4437";
									}
									else {
										echo "#F4B400";
									};
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
			?>

			.rond1 {
				font: normal 20px 'Arial', sans-serif;
				background-color: #DB4437;
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
				font: normal 20px 'Arial', sans-serif;
				color: white;
				background-color:
								<?php 
									if($afficheFormulaireFormules1==1 OR $afficheFormulaireFormules2345==1) {
										echo "#DB4437";
									} else {
										echo "#F4B400";
									};
								?>;
				padding: 20px;
				border-radius: 6px;
			}
			.divider {
				background: #212529;
				margin-top: 12px;
				margin-bottom: 20px;
			}

			<?php 
				if($afficheFormulaireFormules1==1 OR $afficheFormulaireFormules2345==1) {
					echo "@media screen and (max-width: 992px){
							#etape2 {
								display: block;
							}
						}
					";
				} else {
					echo "@media screen and (max-width: 992px) {
							#etape2 {
								display: none;
						}
					}";		
				}
			?>

			.rond2 {
				font: normal 20px 'Arial', sans-serif;
				background-color: #DB4437;
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
				font: normal 20px 'Arial', sans-serif;
				color: white;
				background-color:
								<?php 
									if($affichePrix==1) {
										echo "#DB4437";
									} else {
										echo "#F4B400";
									};
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
			?>

			.rond3 {
				font: normal 20px 'Arial', sans-serif;
				background-color: #DB4437;
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
		<title> Devis en ligne - Incygne</title>
		<!--<link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" /> -->
		<!-- Font Awesome icons (free version)-->
		<!-- Core theme CSS (includes Bootstrap)-->
		<link href="css/style.css" rel="stylesheet" />
		<!-- autocompletion adresseArrives -->
		<script type="text/javascript"
			src="https://maps.google.com/maps/api/js?libraries=places&language=fr&key=AIzaSyD_Ygw4nw_-Nwyv9JEtnmt8T6rpaGwFRIE">
		</script>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				autocomplete = new google.maps.places.Autocomplete(
					(document.getElementById('origin')), {
						types: ['geocode'],
						componentRestrictions: {
							country: 'fr'
						}
					}
				);
			}, false);
		</script>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				autocomplete = new google.maps.places.Autocomplete(
					(document.getElementById('ari')), {
						types: ['geocode'],
						componentRestrictions: {
							country: 'fr'
						}
					}
				);
			}, false);
		</script>
	</head>
	<br/>
	<body>
		<!-- Navigation-->
		<section class="page-section" id="devis">
			<div class="container">
				<div id="chrono" style="width:100%; height:20%;">
					<div class="row">
						<div class="col-md-12 col-lg-4">
							<div id="etape1">
								<div class="rond1">1 </div>Coordonnés et formule</div>
						</div>
						<div class="col-md-12 col-lg-4">
							<div id="etape2">
								<div class="rond2">2 </div>Vous y êtes presque</div>
						</div>
						<div class="col-md-12 col-lg-4">
							<div id="etape3">
								<div class="rond3">3 </div>Estimation du prix</div>
						</div>
					</div>
				</div>
				<br>
				<form action="" method="POST">
					<?php if($afficheFormulaireInfoPerso == 1) :?>
						<div class="form-style-10">
							<div class="divider section" align="center">Informations général </div>
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
										<input type="text" required name="entreprise" placeholder="Nom entreprise"
											class="form-control  rounded">
									</label>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
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
										<div class="input-group mb-3">
											<div class="input-group-prepend"><span class="input-group-text"
													aria-label="arobase">@</span></div>
											<input type="mail" required name="mail" placeholder="example@gmail.com"
												class="form-control ">
										</div>
									</div>
								</div>
							</div>
							<div class="divider section" align="center">Choix de catégorie</div>
							<div class="inner-wrap">
								<label>
									<select name="vehicule" required class="form-control rounded">
										<option value="basique">Basique (Citadine, Berline, Sportive, SUV, Monospace, 3m3, 6m3)</option>
										<option value="9m3">9m3</option>
										<option value="12m2">12m3</option>
										<option value="15m2">15m3</option>
										<option value="20m3">20m3</option>
										<option value="25m3">25m3</option>
										<option value="30m3">30m3</option>
									</select>
								</label>
							</div>
							<div class="button-section">
								<input type="submit" style="width:100%; margin:0;" name="valider" value="Continuer"
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
						<div class="form-style-10">
							<div class="section">Informations pratique</div>
							<div class="inner-wrap">
								<div class="row">
									<div class="col-md-6">
										<label for="origin">
											<i class="fas fa-map-marker-alt"></i>
											 Lieu de départ
										</label>
										<input type="text" id="origin" required name="dep"
											style="height:50px; margin-bottom:10px;"
											placeholder="adresse, n° de voie,  Code postal, Commune"
											class="form-control  rounded">
									</div>
									<br />
									<div class="col-md-6">
										<label for="ari">
											<i class="fas fa-map-marker-alt"></i> 
											Lieu d'arrivée
										</label>
										<input type="text" id="ari" required name="ari" style="height:50px;"
											placeholder="adresse, n° de voie,  Code postal, Commune"
											class="form-control  rounded">
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<label for="type">
											<i class="fa-solid fa-person-chalkboard"></i>
											<input type="checkbox" name="presentation" value="presentation"> 
											Présentation
										</label>
									</div>
									<div class="col-md-6">
										<label for="type">
											<i class="fa-solid fa-car-wash"></i>
											<input type="checkbox" name="lavage" value="lavage">
											 Lavage
										</label>
									</div>
								</div>
							<div class="section">Plannification du convoyage</div>
							<div class="inner-wrap">
								<div class="row">
									<div class="col-md-12">
									<label><i class="fas fa-ellipsis-v"></i> Date et Heure de départ</label>
										<label>
											<input type="datetime-local" name="dateDepart" class="form-control">
										</label>
									</div>
								</div>
							</div>
							<div class="button-section">
									<div class="row">
										<div class="col-md-6 col-xs-12 col-sm-6">
											<label><input style="width:100%; background-color: #DB4437;" type="submit"
													href="devis.php" name="retour" value="Retour"
													class="btn btn-danger" /></label>
										</div>
										<br />
										<div class="col-md-6 col-xs-12 col-sm-6">
											<label><input type="submit" style="width:100%; background-color: #F4B400;"
													name="affichePrix" value="Je calcule mon tarif !"
													class="btn btn-lg btn-primary " />
											</label>

										</div>
									</div>
								</div>
						</div>
					<?php endif; ?>
					<?php 
						if(isset($_POST['affichePrix'])){
							@$recapDevis='  <br><div class="row">
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
									<h6 class="my-0">Date de convoyage</h6>
								</div>
								<span class="text-muted">'.date($_SESSION['dateD']).'</span>
								</li>
								<li class="d-flex justify-content-between lh-condensed">
								<div>
									<h6 class="my-0">Distance</h6>
									<small class="text-muted">Total en Km</small>
								</div>
								<span class="text-muted">'.$ladistance.'</span>
								</li>
								<li class="d-flex justify-content-between lh-condensed">
								<div>
									<h6 class="my-0">Type de vehicule </h6>
									<small class="text-muted">Catégorie</small>
								</div>
								<span class="text-muted">'.$typeVehicule.'€</span>
								</li>
								<li class="d-flex justify-content-between lh-condensed">
								<div>
									<h6 class="my-0">Tarif pour une distance inférieure ou égale à 50km </h6>
									<small class="text-muted">(HT)</small>
								</div>
								<span class="text-muted">'.$prixKm.'€</span>
								</li>
								<li class="d-flex justify-content-between lh-condensed">
								<div>
									<h6 class="my-0">Lavage</h6>
									<small class="text-muted">20€</small>
								</div>
								<span class="text-muted">'.$_SESSION['lavage'].'</span>
								</li>
								<li class="d-flex justify-content-between lh-condensed">
								<div>
									<h6 class="my-0">Presentation</h6>
									<small class="text-muted">20€</small>
								</div>
								<span class="text-muted">'.$_SESSION['presentation'].'</span>
								</li>
								<li class="d-flex justify-content-between lh-condensed">
								<div>
									<h6 class="my-0">Demande sous 24h</h6>
									<small class="text-muted">Prix location vehicule x2 </small>
								</div>
								<span class="text-muted"> '.$_SESSION['prixDouble'].'</span>
								</li>
								<li class="d-flex justify-content-between">
								<span>Total</span>
								<strong>'.$prixTotal.'€</strong>
								</li>
							</ul>
							</div>
							</div>
						</div>' ;
							$affichePrix = 1;
							echo $recapDevis.'
							<div align="center">
							<a href="https://herculis.banceparis.fr/devis.php">
								<button type="button" value="Retour" class="btn btn-danger">Retour</button></a>
							';
							$to = "merilb78@gmail.com";
							$from = "DEVIS@yncigne.com ";
							ini_set("SMTP","smtp.gmail.com");
							$subject 	= "Trans-IMJ.com - Devis de ".$_SESSION['nom']." ".$_SESSION['nom'];
							$mail_Data = "";
							$mail_Data .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> <html xmlns:v="urn:schemas-microsoft-com:vml">';
							$mail_Data .= "<head> \n";
							$mail_Data .= '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
							$mail_Data .= '<meta name="viewport" content="width=device-width; initial scale=1.0; maximum-scale=1.0;">';
							$mail_Data .= "<title> Atd1.fr </title> \n";
							$mail_Data .= "</head> \n";
							$mail_Data .= "<body> \n";
							$mail_Data .= "<br>";
							$mail_Data .= "<label><b>Formule choisie : ACTIVE <span style=\"color:#Ff6666;\">*</span></b></label> ";
							$mail_Data .= "<br>";
							$mail_Data .= " informations Client : ";
							$mail_Data .= $_SESSION['nom'] ." | ".$_SESSION['prenom'];
							$mail_Data .= "<br />";
							$mail_Data .= $_SESSION['tel'];
							$mail_Data .= "<br>";
							$mail_Data .= $_SESSION['mail'];
							$mail_Data .= "<br>" ;
							$mail_Data .= " informations sur Convoyage : ";
							$mail_Data .= $_SESSION['nom'] ." | ".$_SESSION['prenom'];
							$mail_Data .= "<br />";
							$mail_Data .= $_SESSION['tel'];
							$mail_Data .= "<br>";
							$mail_Data .= $_SESSION['mail'];
							$mail_Data .= "<br>" ;				
							$mail_Data .= "total : ";				
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
				</div>
			</div>
		</form>
</div>
</section>
</body>
</html>