<?php 


include_once('../info.php');
if($rang != 2)
	die('Reconnectez vous en administrateur');


umask(0);
$root = "../";
$backup_folder = "#BACKUP#";
$mods_folder = "#MODS#";
$history_doc = $root.$backup_folder."/history.txt";

 
// fonction pour récupérer les informations sur 
function getInstalledModInfo(){
	global $history_doc;
	$history = contenuFichier($history_doc);
	$depart = 0; $nb=0;
	while(preg_match("#(\d+) ([A-Za-z\d_]+) : ((\*?[a-zA-Z\.\_/\]+, )*)\#DEP:(( [A-Za-z\d_]+)*)#", $history, $matches, PREG_OFFSET_CAPTURE, $depart)){
	//                 1   1 2            2   34                    4 3      56             6 5
		$depart = $matches[0][1] + strlen($matches[0][0]);
		$date[$nb] = $matches[1][0];
		$nom[$nb] = $matches[2][0];
		$pages[$nb] = $matches[3][0];
		$pages[$nb] = explode(", ", $pages[$nb]);
		$dep[$nb] = $matches[5][0];
		$dep[$nb] = explode(" ", $dep[$nb]);
		$nb++;
	}
	return array($date, $nom, $pages, $dep);
}


// cette fonction rends l'intégrité du contenu d'unfichier spécifié
function contenuFichier($file){				
	$buff = "";
	if(file_exists($file)){
		$fichier = fopen($file, "a+");
		while(!feof($fichier)){
			$buff .= fgets($fichier, 4096);
		}
		fclose($fichier);
		return $buff;
	}
	else
		return false;
}
function menageAdmin(){		// fait le ménage des .sbbmod qui ont été uploadés dans admin provisoirement (en cas d'arrêt violent par exemple)
	$ouverture = opendir("./");
	while($file = readdir($ouverture)){
		if(preg_match('/.+\.sbbmod$/', $file)){
			echo 'Suppression de '.$file.' temporaire à l\'installation. ';
			unlink('./'.$file);
		}
	}
}

function parseModName($string){
	for($i=0; $i< strlen($string); $i++)		// on enlève les caractères autre que a-zA-Z_
		if(preg_match('/[a-zA-Z_]/', $string{$i}))
			$nom .= $string{$i};
	return $nom;
}
function modDejaInstalle($nom_mod){
	global $history_doc;
	$contenu = contenuFichier($history_doc);
	if(preg_match('/\d{10,11} '.$nom_mod.'/', $contenu))
		return true;
	else
		return false;
}

function desinstallerMod($dossier, $errinstall){
	global $backup_folder, $history_doc;
	
	if($errinstall)			// début
		echo '#### ----- DESINSTALLATION DU MOD EN COURS D\'INSTALLATION ----- ####<br />';
	else{
		// on trouve la ligne du fichier de history.txt et on la supprime
		echo '#### ------------------- DESINSTALLATION DU MOD ---------------- ####<br />';
		$histo = contenuFichier($history_doc);
		if(preg_match('/^([\d\D]*)('.$dossier.'.*\n)([\d\D]*)$/', $histo, $mat)){
			// on supprimer la ligne de l'historique
			unlink($history_doc);
			$file = fopen($history_doc, "a+");
			fputs($file, $mat[1].$mat[3]);
			fclose($file);
			
			//on supprime les fichiers ajoutés avec le * devant
			preg_match_all('/\*([a-zA-Z\_\/\.]+),/', $mat[2], $mat2);		// du style , *includes/forum.php, 
			for($i=0; $i<count($mat2[1]); $i++){
				if(file_exists('../'.$mat2[1][$i])){
					unlink('../'.$mat2[1][$i]);
					echo '<font color="green">Suppression de '.$mat2[1][$i].'</font><br />';
				}
				else
					echo '<font color="red">Suppression de '.$mat2[1][$i].' impossible</font><br />';
			}	
			echo '<font color="green">Ligne de description supprimée</font><br />';
		}
		else{
			echo '<font color="red">Ligne de description d\'installation non trouvé</font>';	
			return ;
		}
		
		
	}
							// remplacement des fichiers
	if(file_exists('../'.$backup_folder.'/'.$dossier.'/')){
		echo 'Dossier trouvé (../'.$backup_folder.'/'.$dossier.'/)<br />';
		cancelit('../'.$backup_folder.'/'.$dossier.'/', '../');
		echo '#### --------------- FIN DE LA DESINSTALLATION ---------------- ####<br />';
	}
	else
		echo '<font color="red">Echec lors de la désinstallation : le <a href="(../'.$backup_folder.'/'.$dossier.'/">dossier de backup</a> n\existe PAS !!!</font>';
}

	// pour restaurer es fichiers récursivement
	function cancelit($dossier, $to){
		if ($handle = opendir($dossier)){
			while (false !== ($file = readdir($handle))){
				if ($file != '.' && $file != '..'){
					if(is_file($dossier.$file)){
						if(file_exists($to.$file))			// on supprime d'abord le nouveau
							unlink($to.$file);
						rename($dossier.$file, $to.$file);	// bouge le fichier de backup à l'ancienne position
						echo '> > Le fichier <b>'.$to.$file.'</b> a été restauré<br />';
					}
					elseif (is_dir($dossier.$file)){		// on rappelle la fonction cancel dans le dossier
						if(!file_exists($to.$file))			// on créé le dossier s'il n'existait pas (bizare...)
							mkdir($to.$file);
						cancelit($dossier.$file.'/', $to.$file.'/');
					}
				}
			}
			closedir($handle);
		}
		rmdir($dossier);	// on peut supprimer le dossier de backup
		echo '> > > Suppression du dossier de backup ('.$dossier.')<br />';
	}
	function creerHtaccess($chemin){
		$htacc = fopen($chemin, "a+");
		fputs($htacc, "deny from all");
		fclose($htacc);
		echo $chemin.' créé';
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>SoftBB - Administration</title>
	<link rel="stylesheet" href="./install.css" type="text/css" />
	<style type="text/css">
		body{
			font-family: Verdana, Arial, Helvetica, sans-serif;
		}
		#menu{
			margin-top:10px;
			height:18px;
			overflow:hidden;
			border-top:1px solid grey;
			border-bottom:1px solid grey;
			background-color:white;	
			padding:7px;
			padding-left:100px;
			text-align:center;
		}
		.sousmenu{
			margin-right:20px;
			border:1px solid #A7A7A7;
			padding:2px 10px 2px 10px;
			min-width:160px;
			cursor:pointer;
		}
		.sousmenu:hover{
			margin-right:20px;
			border:1px solid #A7A7A7;
			padding:2px 10px 2px 10px;
			min-width:160px;
			cursor:pointer;
			background-color:#D2D2D2;
		}
		.titr{
			font-weight:bold;
			color:black;
			margin-left:30px;
		}
		.box{
			width:95%; 
			height:350px; 
			border:1px dashed grey; 
			margin:auto; 
			color:black; 
			padding:15px;
			overflow:auto;
		}
		.txtareaHalf{
			width:95%;
			height:160px;
		}
		.txtareaFull{
			
		}
		.doubleTab{
			border:1px solid black;
			width:100%;
		}
		.doubleTab td{
			width:50%;
		}
		label{
			margin-left:25px; 
			float:left; 
			width:240px; 
			margin-right:8px; 
			font-weight:bold;
			text-align:right;
		}
		.label_in{
			margin-left:3px; 
			color:green;
			width:124px;
			text-align:left;
		}
		select{
			width:168px;
		}
		
		.status{
			border:1px dotted grey;
		}
		.status td{
			border-left:1px dotted grey;
			border-bottom:1px dotted grey;
			padding:3px;
		}
		.tr_titre{
			background-color:#CFD3D5;
			text-align:center;
		}
		
		.desinstallation{
			font-family: Courier, monospace;
			width:90%;
			background-color:#FFD3D4;
			padding:8px;
			margin:auto;
			border:1px dashed grey;
			margin-top:5px;
		}
		.installation{
			font-family: Courier, monospace;
			width:90%;
			background-color:#DBE8E1;
			padding:8px;
			margin:auto;
			border:1px dashed grey;
			margin-top:5px;
		}
		
	</style>
</head>
<body>
<?php
	

	function dossiersExists(){		// vérifie si les dossiers de backup et de création de mods existent 
		global $root, $backup_folder, $mods_folder;
		$return = true;
		if(!file_exists($root.$backup_folder)){
			mkdir($root.$backup_folder);
			creerHtaccess($root.$backup_folder.'/.htaccess');
			$return = false;
		}
		if(!file_exists($root.$mods_folder)){
			mkdir($root.$mods_folder);
			creerHtaccess($root.$mods_folder.'/.htaccess');
			$return = false;
		}
		return $return;
	}
	function fichiersModifies($chaine){
		$depart = 0;	
		preg_match_all('/##=> (DANS):\s*([\w\/_\.]+)/', $chaine, $matches, PREG_OFFSET_CAPTURE, $depart);
			for($i=0; $i<count($matches[2]); $i++)
				$list[$depart++]= $matches[2][$i][0];
		return $list;
	}
	function fichiersCrees($chaine){
		$depart = 0;	
		preg_match_all('/##=> (NOM):\s*([\w\/_\.]+)/', $chaine, $matches, PREG_OFFSET_CAPTURE, $depart);
			for($i=0; $i<count($matches[2]); $i++)
				$list[$depart++]= $matches[2][$i][0];
		return $list;
	}
	
	
	///////////////////////////////////////////////////////
	// fonction pour le traitement de la procédure d'installation des mod
		function parseStringForRegex($string){
			preg_match_all('/([0-9a-zA-Z])|([\s\S])/', $string, $mat);
				for($i=0; $i<count($mat[1]); $i++){
					if(!empty($mat[1][$i]))					// caractère normal
						$new .= $mat[1][$i];
					elseif(!empty($mat[2][$i])){
						if(preg_match('/\s/', $mat[2][$i]))	// espace quelconque
							$new .= '\\s*';
						else 								// tout autres caractères parsé \
							$new .= '\\'.$mat[2][$i];	
					}
					else
						$new .= $mat[0][$i];
				}
			return $new;
		}
		
	function modifFichier($file, $trouver, $ajout, $mode){
		if(!file_exists('../'.$file))
			return '---> <font color="red">Le fichier à modifier n\'existe PAS <font size="2">(<i>../'.$file.'</i>)</font></font><br />';
		$contenu = contenuFichier('../'.$file);
		$parsedTrouver = parseStringForRegex($trouver);			// pour mettre dans une regex sans Unknow modifiers
		preg_match_all('/'.$parsedTrouver.'/', $contenu, $mat);
		$occ = count($mat[0]);
		switch($occ){
			case 0 : return 'La séquence de caractères pour placer l\'ajout n\'a pas été trouvée (<i>'.$trouver.'</i>) :('; break;
			case 1 : 	// OK on peut ajouter (avant/ apres) ou retirer !
				if(preg_match('/^([\d\D]*)'.$parsedTrouver.'([\d\D]*)$/', $contenu, $mat)){
					if($mode == "AJOUTER APRES")
						$new = $mat[1].$trouver.$ajout.$mat[2];
					elseif($mode == "AJOUTER AVANT")
						$new = $mat[1].$ajout.$trouver.$mat[2];
					elseif($mode == "SUPPRIMER SELECTION")
						$new = $mat[1].$mat[2];
					elseif($mode == "REMPLACER SELECTION")
						$new = $mat[1].$ajout.$mat[2];
					else
						return 'mode de traitement ('.$mode.') inconnu';
					//return htmlentities($new);
					// on supprime le fichier cible et on le réécrit avec le nouveau contenu
					if(!unlink('../'.$file))
						return 'Impossible de supprimer l\'ancien fichier';
					$newfile = fopen('../'.$file, "a+");
					if(!fputs($newfile, $new))
						return 'Erreur lors de la création du fichier (1)';
					fclose($newfile);
					return 'ok';
				}
				else
					return 'Erreure de logique interne non sensée arriver, prévenir le programmeur';
				break;
			default : return 'Il y a plusieurs occurences trouvées pour l\'ajout (='.$occ.') l\'ajout ne peut être effectué'; break;
		}
		return 'Erreur : pas de compte-rendu de la modification';
	}
	function dependanceSatisfaite($nom){
		global $history_doc;
		$contenu = contenuFichier($history_doc);
		if(preg_match('/\d+ '.$nom.'/', $contenu))
			return true;
		else
			return false;
	}
	function ajout_fichier($file, $contenu){
		
		$newfile = fopen('../'.$file, "a+");
		if(!fputs($newfile, $contenu))
			return 'Erreur lors de la création du fichier (2)';
		fclose($newfile);
		return 'ok';
	}
	
	if(!dossiersExists())
		echo '<div style="color:red; padding:10px; margin:auto; width:50%;">C\'est la première fois que vous venez sur cette page, les dossier '.$backup_folder.' et '.$mods_folder.' ont été, créés... <b>ne les supprimez pas !</b></div>';

?><div id="menu">
	<span class="sousmenu" onclick="Javascript:location.href='mods_index.php';">Informations</span>
	<span class="sousmenu" onclick="Javascript:location.href='mods_index.php?p=gestion';">GESTION DES MODS</span>
	<span class="sousmenu" onclick="Javascript:location.href='mods_index.php?p=generer';">Générer un mod</span>
</div>
<div id="install">
<?php
	$regexEntete = '/#\*#\*# NOM du mod : ([a-zA-Z_]+)\s*#\*#\*# AUTEUR : (\w+)\s*#\*#\*# DESCRIPTION : ([\d\D]*)\s*#\*#\*# DEPENDANCE1 : (\w*)\s*#\*#\*# DEPENDANCE2 : (\w*)/';
	/////////////////////////////////////////////////////////////////////////////////////////
	// INSTALLATION DUN MOD	///////////////////////////////////
	if($_POST['send'] && move_uploaded_file($_FILES['fichier']['tmp_name'], './'.$_FILES['fichier']['name']))
	{
		$fichier = $_FILES['fichier']['name'];
		$contenu = stripslashes(contenuFichier($fichier));
		//echo $contenu;
		
		if(preg_match($regexEntete, $contenu, $match))
		{
			// on coppie le fichier dans le dossier des mods s'il n'y est pas
			if(!file_exists($root.$mods_folder.'/'.$_FILES['fichier']['name']) && file_exists($_FILES['fichier']['name']))
				copy($_FILES['fichier']['name'], $root.$mods_folder.'/'.$_FILES['fichier']['name']);
			
			echo '<div id="right">Installation de "'.$match[1].'"</div><div class="clear"><br />
			Vous êtes sur le point d\'installer un module, nous vous conseillons de l\installer <b>en local</b> puis de copier 
			les fichiers sur votre serveur en ligne en suite<br />
			Nous vous conseillons de n\'<b>installer que des mods qui ne viennent que de softbb.net</b> pour des mesures de sécurité !<br />
			<br />
			<b>Nom du Mod :</b> <font color="green">'.$match[1].'</font><br />
			<b>Auteur :</b> <font color="green">'.$match[2].'</font><br /><br />
			<b>Description : </b><font color="green">'.bbcode(nl2br($match[3])).'</font><br /><br />
			<b>Dépendances : </b>';
			if(!empty($match[4]) || !empty($match[5])){
				echo '<font color="red">'.$match[4].', '.$match[5].' </font><br />';
				for($o=4; $o<=5; $o++){		// on, vérifie les dépendancess
					if(!empty($match[$o]) && !dependanceSatisfaite($match[$o])){
						echo '<b>vous devez installer la dépendance avant d\'installer ce mod ! </b></font><br />';
						$annulinstal = true;
					}
					else if(!empty($match[$o]))
						echo '<font color="green">La dépendance '.$match[$o].' est satisfaite</font><br />';
				}
			}
			
			else
				echo '<font color="green">Aucune</font><br />';
			
			$list1 = fichiersModifies($contenu);
			echo '<br /><font color="blue">Ce mod va modifier les fichiers suivants qui seront sauvegardés tels qu\'il sont 
			maintenant : </font><br />';
			
			$pt = 0;
			$tabf = array();
			for($i=0; $i<count($list1); $i++)
			{
				if(in_array($list1[$i], $tabf)){
					$key = array_search($list1[$i], $tabf);
					$tabc[$key]++;
				}
				else{
					$tabf[$pt] = $list1[$i];
					$tabc[$pt++] = 1;
				}
			}
			for($i=0; $i<count($tabf); $i++){
				echo '- '.$list1[$i].' <font color="grey">('.$tabc[$i].' modification';
				if($tabc[$i] != 1)
					echo 's';
				echo ')</font><br />';
			}
			
			
			$list2 = fichiersCrees($contenu);
			if(count($list2) > 0){
				echo '<font color="blue">Ce mod aura besoin d\'ajouter ces fichiers : </font><br />';
				for($i=0; $i<count($list2); $i++)
					echo '- '.$list2[$i].'<br />';
			}
			
			echo '<br ><font color="red">Voulez-vous installer ce mod ?</font><br />
			<form action="mods_index.php" name="install" method="post">
			<input type="hidden" name="fichier" value="'.$fichier.'" />
			<table style="margin:auto;" cellspacing="2">
			    <tr>';
			    if(!$annulinstal)
			        echo '<td style="padding-right:20px;"><input type="submit" name="oui" value="OUI, poursuivre" /></td>';
			        echo '<td><input type="submit" name="non" value="ANNULER l\'installation" /></td>
			    </tr>
			</table>
			</form>';
		}
		else
			echo 'L\'en-tête du fichier n\'a pas été compris<br />';
		
	}
	elseif(isset($_POST['fichier'])){			// deuxième partie de l'installation
		if(isset($_POST['non'])){
			menageAdmin();
			echo '<br />L\'installation n\'a pas eu lieu.<br ><br >';
		}
		else{
			if(file_exists($_POST['fichier']))
			{
				$installok = true;
				$contenu = stripslashes(contenuFichier($_POST['fichier']));
				echo '<div id="right">Installation de "'.$_POST['fichier'].'"</div><div class="clear"><br /><br />';	
				$tmstmp = time();
				
				umask(0);
				mkdir($root.$backup_folder.'/'.$tmstmp.'/', 0777);	// création du dossier principal
				
				// on va commencer par faire la sauvegarde
				$liste_a_sauvegarde = fichiersModifies($contenu);
				$liste_new = fichiersCrees($contenu);
				function creer_arbo($arbo, $ou){		// fonction pour créer dossiers en récursif
					if(preg_match('#(.+?)/#', $arbo, $mat)){
						if(!file_exists($ou.$mat[0]))
							mkdir($ou.$mat[0], 0777);
						$arbo = substr($arbo, strlen($mat[0]), strlen($arbo));
						creer_arbo($arbo, $ou.$mat[0]); 
					}
				}
				
				$tabf = array();	$tfi = 0;		// pour ne pas répéter copie
				for($i=0; $i<count($liste_a_sauvegarde); $i++){
					// on crée d'abord les dossier récursivement s'il n'existent pas
					creer_arbo($liste_a_sauvegarde[$i], $root.$backup_folder.'/'.$tmstmp.'/');
					if(!in_array($liste_a_sauvegarde[$i], $tabf)){
						$tabf[$tfi++] = $liste_a_sauvegarde[$i];
						if(!@copy($root.$liste_a_sauvegarde[$i], $root.$backup_folder.'/'.$tmstmp.'/'.$liste_a_sauvegarde[$i]))
							die('<font color="red">Le fichier '.$liste_a_sauvegarde[$i].' n\'a pas pu être copié</font>');
						else
							echo '<font color="green">Le fichier '.$root.$liste_a_sauvegarde[$i].' a été copié</font><br />';
					}	
				}
				
				// maintenant on peut COMMENCER LES MODIFICATIONS
				//echo nl2br($contenu).'<br /><br />';
				echo '<div class="installation">';
				preg_match_all('/#\*#\*# DEBUT ETAPE (\d+)[\d\D]*#\*#\*# FIN ETAPE (\d+) #\*#\*#/U', $contenu, $match1);
				
				for($i=0; $i<count($match1[0]); $i++){
					if($i == 0 && modDejaInstalle(str_replace('.sbbmod', '', $_POST['fichier']))){				// on revérifie si le mod n'a pas déjà été installé
						echo '<font color="red">Le mod a déjà été installé, vous ne pouvez pas le réinstaller une deuxième fois</font>';
						$installok = false;
						break;
					}
					echo '-> Trouvé : <b>étape n°'.$match1[1][$i].'</b><br />';
					
					// en cas de modification AJOUTER APRES
					if(preg_match('/##=> DANS:\s+([a-zA-Z0-9\/\._]+)\s+##=> TROUVER:\s+([\d\D]*)\s+##=> AJOUTER ((?:APRES)|(?:AVANT)):\s+([\d\D]*)\s#\*#\*# FIN ETAPE/', $match1[0][$i], $proc))
					{
						echo '---> <font color="blue">procédure d\'ajout (après) dans ('.$proc[1].')</font><br />';
						// echo 'dans : '.htmlentities($proc[1]). '<br />TROUVER: '.htmlentities($proc[2]).'<br />AJOUTER APRES: '.htmlentities($proc[3]);
						$etat = modifFichier($proc[1], $proc[2], $proc[4], 'AJOUTER '.$proc[3]);
						if($etat != 'ok'){
							$installok = false;
							echo '------- <font color="red">Ajout impossible : '.$etat.'</font><br />';
							break;
						}
						else
							echo '------- <font color="green">Ajout réussi !</font><br />';
						
					}
					// en cas de modification SUPPRIMER SELECTION
					elseif(preg_match('/##=> DANS:\s+([a-zA-Z0-9\/\._]+)\s+##=> TROUVER:\s+([\d\D]*)\s+##=> (SUPPRIMER SELECTION)\s+#\*#\*# FIN ETAPE/', $match1[0][$i], $proc))
					{
						echo '---> <font color="blue">procédure d\'ajout (après) dans ('.$proc[1].')</font><br />';
						$etat = modifFichier($proc[1], $proc[2], 'rien à ajouter ^^', $proc[3]);
						if($etat != 'ok'){
							$installok = false;
							echo '------- <font color="red">Suppression impossible : '.$etat.'</font><br />';
							break;
						}
						else
							echo '------- <font color="green">Suppression réussie !</font><br />';
						
					}
					// en cas de modification AJOUTER FICHIER
					elseif(preg_match('/##=> AJOUTER FICHIER:\s+([\d\D]+)\s+##=> NOM:\s+([a-zA-Z0-9\/\._]*)\s+#\*#\*# FIN ETAPE/', $match1[0][$i], $proc))
					{
						echo '---> <font color="blue">procédure de création de fichier ('.$proc[2].')</font><br />';
						//echo 'DANS : '.htmlentities($proc[2]). '<br />CONTENU: '.htmlentities($proc[1]).'<br />';
						$etat = ajout_fichier($proc[2], $proc[1]);
						if($etat != 'ok'){
							$installok = false;
							echo '------- <font color="red">Création du nouveau fichier impossible : '.$etat.'</font><br />';
							break;
						}
						else
							echo '------- <font color="green">Création du fichier réussi !</font><br />';
						
					}
					else
					{
						echo '<font color="red">L\'étape n\'a pas pu être syntaxiquement comprise par l\'expression régulière, l\'installation va être annulée</font><br />';
						$installok = false;
						break;
					}
				}
				echo '</div>';
				
				
				/////////////////////////////////////////////////////////
				// et on met la ligne dans le registre
				if(!$installok){
					echo '<br /><br /><font color="blue">L\'installation n\'ayant pas pu se faire correctement, les fichiers d\'origine doivent être restaurés<br />
					Vueillez montrer le rapport çi dessus à la communautée et déduire d\'où vient l\'erreur</font><br />
					<div class="desinstallation">';
					desinstallerMod($tmstmp, true);
					echo '</div>';
				}
				else{
					if(preg_match($regexEntete, $contenu, $match))
					{
						// type   	1121234567 nom_mod : profils.php, info.php, #DEP: mod1
						// 			6758575 nom_mod2 : profils.php, info.php, #DEP: mod1 mod2

						$opened_file = fopen($history_doc, "a+");
						$buff .= $tmstmp.' '.$match[1].' : ';
						
						for($i=0; $i<count($liste_a_sauvegarde); $i++)
							$buff .= $liste_a_sauvegarde[$i].', ';
						for($i=0; $i<count($liste_new); $i++)
							$buff .= '*'.$liste_new[$i].', ';
						$buff .= '#DEP: '.trim($match[4].' '.$match[5]).'
';
						fputs($opened_file, $buff);
						echo '<br /><br /><font color="green">> Ajout de la ligne au registre</font><br />';;	
						fclose($opened_file);
					}
					else
					{
						echo '<font color="red">Le registre n\'a pas pu être mis à jour car le fichier .ssbmod est invalide, le mod doit immédiatement être 
						désinstallé sinon il ne pourra plus l\'être plus tard</font>';
						desinstallerMod($tmstmp, true);
					}
					
				}
				echo '<br /><a href="mods_index.php?p=gestion">Retour au panneau de control</a>';
					
			}
			else
				echo 'Le fichier d\'installation '.$_POST['fichier'].' est introuvable';
		}
	}
	
	// fin de l'installation
	//////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////////////
	// DESINSTALLATION DUN MOD
	elseif(isset($_GET['desinstaller']) && is_numeric($_GET['desinstaller'])){
		if(isset($_GET['continue'])){
			// on va d'abord désinstaller les mods dans l''ordre (on suit l'url, à priori le webmaster ne bidouille pas inconsciemment les url)
			$nb = 0;
			while(isset($_GET['name'.$nb]) && isset($_GET['time'.$nb]) && is_numeric($_GET['time'.$nb])){
				if($nb == 0)
					echo '<h1>Préliminaires : désinstallation des composants reliés</h1>';
				echo '<h2>Désinstallation de '.$_GET['name'.$nb].'</h2>';
				desinstallerMod($_GET['time'.$nb++], false);
			}
			
			echo '<h1>Désinstallation du composant voulu</h1>';
			if(file_exists('../'.$backup_folder.'/'.$_GET['desinstaller'].'/')){
				echo 'Le mod va être désinstallé';
				echo '<div class="desinstallation">';
				desinstallerMod($_GET['desinstaller'], false);
				echo '</div>';
			}
			else
				echo 'Le dossier de backup n\'existe pas, vous ne pouvez pas désinstaller ce mod';
			
			$nb = 0;
			while(isset($_GET['name'.$nb]) && isset($_GET['time'.$nb]) && is_numeric($_GET['time'.$nb])){
				if($nb == 0)
					echo '<h1>Résinstallation des composants reliés</h1>';
				echo '<h2>Résinstallation de '.$_GET['name'.$nb].'</h2>';
				
				++$nb;
			}
			
		}
		else{
			// on récupère les infos dans le fichier history (nom, date, pages modifiées, dépendances)
			
			$infos = getInstalledModInfo();
			$date = $infos[0];
			$nom = $infos[1];
			$pages = $infos[2];
			$dep = $infos[3];
			$nbmods = count($date);
			/*
			 * INFORMATION SUR LA DESINSTALLATION DUN FICHIER AYANT ETE MODIFIÉ ULTÉRIEUREMENT PAR UN AUTRE MOD
			 * Tous les mods modifiants un des fichiers qui a été modifié par un mod après celui çi doit être
			 * désinstallé (par le simple fait que la restauration se fait ar des backup et qu'il ne faut pas les
			 * mélanger dans le temps)
			 * On va donc vérfier cette condition en partant de la fin de l'history jusqu'au mod concerné
			 * Les mods dépendants du mod désinatallé doivent aussi être désinstalles
			*/
			$rg_mod_desinstall = array_search($_GET['desinstaller'], $date);
			$adesinstaller = array();
			echo 'Vous allez supprimer le mod <b>'.$nom[$rg_mod_desinstall].'</b> <br />';
			$u=0; 		// retenir le rang d'un mod à désinstaller
			$o=0; 		// retenir position dans le tableau
			for($i=$nbmods-1; $i>$rg_mod_desinstall; $i--)
			{
				// on va vérifier pour chaques pages au rang $rg_mod_desinstall si y'en a pas une au mod de rang $i
				for($w=0; $w<count($pages[$i]); $w++)
				{
					if(
						(	in_array($pages[$i][$w], $pages[$rg_mod_desinstall])
								|| in_array($pages[$i][$w], str_replace('*', '', $pages[$rg_mod_desinstall]))
						)
							&& !preg_match('/#/', $pages[$i][$w])
						){
						$others .= '&amp;name'.$o.'='.$nom[$i].'&amp;time'.$o++.'='.$date[$i];
						$adesinstaller[$u] = $nom[$i];
						$u++;
						break; // il en suffit d'un pour que le mod soit désinstallé
					}
				}
			}
			if(count($adesinstaller) == 0)
				echo 'Ce mod n\'empiète sur aucun mod <i>(pas besoin de désinstaller un autre mod)</i><br />';
			else{
				echo '<b><font color="red">ATTENTION !</font></b> Ce mod doit être désinstallé 
						après avoir désinstallé les composants suivant :<li>';
				for($i=0;  $i<count($adesinstaller); $i++)
				{
					echo '<ol>'.$adesinstaller[$i].' <i>[';
					if(file_exists('../'.$mods_folder.'/'.$adesinstaller[$i].'.sbbmod'))
						echo '<font color="green">Ce mod pourra être réinstallé</font>';
					else
						echo '<font color="red">Ce mod ne pourra être réinstallé, placez le fichier d\'installation dans '.$mods_folder.'</font>';
					echo ']</i></ol>';
				
				}
				echo '</li>';
			}
			echo '<a href="mods_index.php?desinstaller='.$_GET['desinstaller'].'&amp;continue='.$others.'">continuer</a> ? ou <a href="mods_index.php?p=gestion">Retour au panneau de gestion</a>';
		}
	}
	elseif(isset($_GET['p']) && $_GET['p'] == "generer")
	{
		if(isset($_POST['envoyer']))
		{
			
			
			/* Le fichier .sbbmod aura cette allure 
			 * 
			 * FICHIER D'EXTENSION DE FORUM SOFTBB
			 * Ce fichier .sbbmod est un fichier paramétré par son auteur pour améliorer votre forum, pour plus de renseignements et savoir comment l'exécuter, rendez-vous sur http://www.softbb.net/forum/index.php?page=post&ids=1381
			 * 
			 * 
			 * #*#*#*#*#*# I N F O S #*#*#*#*#*#
			 * 
			 * #*#*# NOM du mod : xxxx
			 * #*#*# AUTEUR : xxxx
			 * #*#*# DESCRIPTION : xxxx
			 * #*#*# DEPENDANCE1 : xxxx
			 * #*#*# DEPENDANCE2 : xxxx
			 * 
			 * 
			 * 
			 * 
			 * #*#*#*#*#*# E X E C U T I O N #*#*#*#*#*#
			 * 
			 * #*#*# DEBUT ETAPE 1 #*#*#
			 * 		##=> DANS:
			 * 		##=> TROUVER:
			 * xxxxxxxxxx
			 * xxxxxxxxxx
			 * 		##=> AJOUTER AVANT:
			 * xxxxxxxxxx
			 * xxxxxxxxxx
			 * 
			 * #*#*# FIN ETAPE 1 #*#*#
			 * 
			 * 
			 * 
			 * 
			 * #*#*# DEBUT ETAPE 2 #*#*#
			 * 		##=>AJOUTER FICHIER:
			 * xxxxxxxxxx
			 * xxxxxxxxxx
			 * 		##=>NOM:
			 * xxxxxxxxxx
			 * 
			 * #*#*# FIN ETAPE 2 #*#*#
			 * 
			 */
			echo "Début de l'écriture<br />";
			
		
			$nom = parseModName($_POST['nom']);
			$description = $_POST['description'];
			$pseudo = $_POST['pseudo'];
			$dependance1 = parseModName($_POST['dependance1']);
			$dependance2 = parseModName($_POST['dependance2']);
			
			if(empty($nom))
				$nom = "noname";
			if(empty($description))
				$description = "nodescription";
			if(empty($pseudo))
				$pseudo = "noauthor";
			
			$c_debut = "\nFICHIER D'EXTENSION DE FORUM SOFTBB\nCe fichier .sbbmod est un fichier paramétré par son auteur pour améliorer votre forum, pour plus de renseignements et savoir comment l'exécuter, rendez-vous sur http://www.softbb.net/forum/index.php?page=post&ids=1381\n\n\n";
			$c_info = "#*#*#*#*#*# I N F O S #*#*#*#*#*#\n#*#*# NOM du mod : ".$nom."\n#*#*# AUTEUR : ".$pseudo."\n#*#*# DESCRIPTION : ".$description."\n#*#*# DEPENDANCE1 : ".$dependance1."\n#*#*# DEPENDANCE2 : ".$dependance2."\n#*#*# VERSION : 1\n\n\n\n\n\n#*#*#*#*#*# E X E C U T I O N #*#*#*#*#*#";

			$contenu = $c_debut.$c_info;
			$nb=1;

			
			$saut = 0;
			$etape = 0;
			while(1==1){
				if(isset($_POST['sel'.$nb]) && $_POST['sel'.$nb] != "-------"){
					$contenu .= "\n\n#*#*# DEBUT ETAPE ".$nb." #*#*#\n";
					if($_POST['sel'.$nb] == "TROUVER"){
						$contenu .= "\t##=> DANS:\n".$_POST['page'.$nb]."\n";
						$contenu .= "\t##=> TROUVER:\n".$_POST['txtarea'.$nb]."\n";
						
						if($_POST['sel2'.$nb] == "AJOUTER AVANT")
							$contenu .= "\t##=> AJOUTER AVANT:\n".$_POST['txtarea2'.$nb]."\n";
						elseif($_POST['sel2'.$nb] == "AJOUTER APRES")
							$contenu .= "\t##=> AJOUTER APRES:\n".$_POST['txtarea2'.$nb]."\n";
						else
							$contenu .= "\t##=> SUPPRIMER SELECTION\n\n";
						
						
					}
					elseif($_POST['sel'.$nb] == "AJOUTER FICHIER")
						$contenu .= "\t##=> AJOUTER FICHIER:\n".$_POST['txtarea'.$nb]."\n\n\t##=> NOM:\n".$_POST['nvFichier'.$nb]."\n";
						
					$contenu .= "#*#*# FIN ETAPE ".$nb." #*#*#\n\n";
					$saut = 0;	
				}
				else{
					if(++$saut > 10) 
						break;
				}
				$nb++;
			}
			
			if(file_exists("../".$mods_folder."/".$nom.".sbbmod"))
				unlink("../".$mods_folder."/".$nom.".sbbmod");
			$monfichier = fopen("../".$mods_folder."/".$nom.".sbbmod", "a+");
			fputs($monfichier, $contenu);
			fclose($monfichier);
			
			echo '<textarea style="width:650px; height:400px; margin:20px;">'.$contenu.'</textarea>';
			echo "Fin de l'écriture";
		}
		else
		{
?>

			<script type="text/javascript">
			var nb = 0;
			var desc1 = 'Copiez le code à rechercher (Attention : une seule occurence possible !)';
			var desc2 = 'Nom du fichier avec le CHEMIN depuis la racine du forum ! ( ex : includes/nouvellepage.php )';
			var desc3 = 'Ajoutez l\'intégralité du nouveau fichier';
			var desc4 = 'Copiez le code à ajouter (un marquage en commentaire sera placé entre)';
			var desc5 = 'Entrez le chemin de la page concernée (ex : includes/faq.php)';
			var desc = new Array(desc1, desc2, desc3, desc4, desc5);
			
			function remplir(id, x, tmp1, tmp2, tmp3){		// pour retourner ce que contient le formulaire selon le choix
				var find = '';
				var tab = '<table class="doubleTab"><tr><td><label for="sel'+id+'" class="label_in">Action initiale '+id+' :</label><select name="sel'+id+'" onChange="javascript:compl('+id+', null, null, null);" id="sel'+id+'"><option name="rien">-------</option><option name="0" selected="selected">TROUVER</option><option name="1">AJOUTER FICHIER</option></select></td>';
				
				if(x == 0)					// champ initiale avec choix --------- par défaut en attente de choix
					return '<div class="action" id="act'+id+'" value="88"><label for="sel'+id+'" class="label_in">Action initiale '+id+' : </label><select name="sel" onChange="javascript:compl('+id+', null, null, null);" id="sel'+id+'"><option name="rien">-------</option><option name="0" >TROUVER</option><option name="1">AJOUTER FICHIER</option></select></div><br />';
				else if(x == 1)				// choix TROUVER mais rien après
					return tab+'<td><label for="sel2'+id+'" class="label_in">Puis : </label><select name="sel2'+id+'" onChange="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, null, document.getElementById(\'page'+id+'\').value);" id="sel2'+id+'"><option name="rien" selected="selected">-------</option><option name="5">AJOUTER AVANT</option><option name="6">AJOUTER APRES</option><option name="7">SUPPRIMER SELECTION</option></select></td></tr>				<tr><td><textarea class="txtareaHalf" name="txtarea'+id+'" name="txtarea'+id+'" id="txtarea'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, null, null);" onFocus="cleanup(this)">'+choisirContenu(tmp1, desc1)+'</textarea></td><td></td></tr><tr><td colspan="2"><label for="page'+id+'" class="label_in">Page concernée:</label><input type="text" style="width:450px;" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, null, document.getElementById(\'page'+id+'\').value);" onFocus="Javascript:cleanup(this)" name="page'+id+'" id="page'+id+'" value="'+choisirContenu(tmp3, desc5)+'" /></td></tr></table>';
				else if(x == 2)				// choix AJOUTER FICHIER
					return '<label for="sel'+id+'" class="label_in">Action initiale '+id+' : </label><select name="sel'+id+'" onChange="javascript:compl('+id+', null, null, null);" id="sel'+id+'"><option name="rien">-------</option><option name="0">TROUVER</option><option name="1" selected="selected">AJOUTER FICHIER</option></select><br /><input type="text" name="nvFichier'+id+'" id="nvFichier'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'nvFichier'+id+'\').value, document.getElementById(\'txtarea'+id+'\').value, null);" onFocus="cleanup(this)" style="width:90%; background-color:#F0EFA4; color:red;" value="'+choisirContenu(tmp1, desc2)+'" /><textarea class="txtareaFull" name="txtarea'+id+'" id="txtarea'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'nvFichier'+id+'\').value, document.getElementById(\'txtarea'+id+'\').value, null);" onFocus="cleanup(this)">'+choisirContenu(tmp2, desc3)+'</textarea>';
				//////////////  Deuxième partie ///////////////
				else if(x == 3)				// choix AJOUTER AVANT
					return tab+'<td><label for="sel2'+id+'" class="label_in">Puis : </label><select name="sel2'+id+'" onChange="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, document.getElementById(\'txtarea2'+id+'\').value, document.getElementById(\'page'+id+'\').value);" id="sel2'+id+'"><option name="rien">-------</option><option name="5" selected="selected">AJOUTER AVANT</option><option name="6">AJOUTER APRES</option><option name="7">SUPPRIMER SELECTION</option></select></td></tr>				<tr><td><textarea class="txtareaHalf" name="txtarea'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, document.getElementById(\'txtarea2'+id+'\').value, document.getElementById(\'page'+id+'\').value);" id="txtarea'+id+'" onFocus="cleanup(this)">'+choisirContenu(tmp1, desc1)+'</textarea></td><td><textarea class="txtareaHalf" name="txtarea2'+id+'" id="txtarea2'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, document.getElementById(\'txtarea2'+id+'\').value, document.getElementById(\'page'+id+'\').value);" onFocus="cleanup(this)">'+choisirContenu(tmp2, desc4)+'</textarea></td></tr><tr><td colspan="2"><label for="page'+id+'" class="label_in">Page concernée:</label><input type="text" style="width:450px;" onFocus="Javascript:cleanup(this)" name="page'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, document.getElementById(\'txtarea2'+id+'\').value, document.getElementById(\'page'+id+'\').value);" id="page'+id+'" value="'+choisirContenu(tmp3, desc5)+'" /></td></tr></table>';
				else if(x == 4)				// choix AJOUTER APRES
					return tab+'<td><label for="sel2'+id+'" class="label_in">Puis : </label><select name="sel2'+id+'" onChange="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, document.getElementById(\'txtarea2'+id+'\').value, document.getElementById(\'page'+id+'\').value);" id="sel2'+id+'"><option name="rien">-------</option><option name="5">AJOUTER AVANT</option><option name="6" selected="selected">AJOUTER APRES</option><option name="7">SUPPRIMER SELECTION</option></select></td></tr>				<tr><td><textarea class="txtareaHalf" name="txtarea'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, document.getElementById(\'txtarea2'+id+'\').value, document.getElementById(\'page'+id+'\').value);" id="txtarea'+id+'" onFocus="cleanup(this)">'+choisirContenu(tmp1, desc1)+'</textarea></td><td><textarea class="txtareaHalf" name="txtarea2'+id+'" id="txtarea2'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, document.getElementById(\'txtarea2'+id+'\').value, document.getElementById(\'page'+id+'\').value);" onFocus="cleanup(this)">'+choisirContenu(tmp2, desc4)+'</textarea></td></tr><tr><td colspan="2"><label for="page'+id+'" class="label_in">Page concernée:</label><input type="text" style="width:450px;" onFocus="Javascript:cleanup(this)" name="page'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, document.getElementById(\'txtarea2'+id+'\').value, document.getElementById(\'page'+id+'\').value);" id="page'+id+'" value="'+choisirContenu(tmp3, desc5)+'" /></td></tr></table>';
				else if(x == 5)				// choix SUPPRIMER
					return tab+'<td><label for="sel2'+id+'" class="label_in">Puis : </label><select name="sel2'+id+'" onChange="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, null, document.getElementById(\'page'+id+'\').value);" id="sel2'+id+'"><option name="rien">-------</option><option name="5">AJOUTER AVANT</option><option name="6">AJOUTER APRES</option><option name="7" selected="selected">SUPPRIMER SELECTION</option></select></td></tr>				<tr><td><textarea class="txtareaHalf" name="txtarea'+id+'" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, null, document.getElementById(\'page'+id+'\').value);" id="txtarea'+id+'" onFocus="cleanup(this)">'+choisirContenu(tmp1, desc1)+'</textarea></td><td></td></tr><tr><td colspan="2"><label for="page'+id+'" class="label_in">Page concernée:</label><input type="text" style="width:450px;" onBlur="javascript:compl('+id+', document.getElementById(\'txtarea'+id+'\').value, null, document.getElementById(\'page'+id+'\').value);" onFocus="Javascript:cleanup(this)" name="page'+id+'" id="page'+id+'" value="'+choisirContenu(tmp3, desc5)+'" /></td></tr></table>';
				return 'error';
			}
				function cleanup(txtarea){
					for(i=0; i<desc.length; i++)
						if(txtarea.value == desc[i])
							txtarea.value='';
				}
				function choisirContenu(temp, desc){		// pour remettre la phrase explicative si l'utilisateur n'a pas encore rempli le champ
					if(temp == null)
						return desc;
					else
						return temp;
				}
				
			function ajout(){
				nb++;
				document.getElementById('actions').innerHTML += remplir(nb, 0, null, null, null);
			}
			
			function compl(n, tmp1, tmp2, tmp3){
				if(tmp1 == null || document.getElementById('sel'+n).value == "AJOUTER FICHIER"){
					if(document.getElementById('sel'+n).value == "-------")
						document.getElementById('act'+n).innerHTML = remplir(n, 0, null, null, null);
					else if(document.getElementById('sel'+n).value == "TROUVER")
						document.getElementById('act'+n).innerHTML = remplir(n, 1, null, null, null);
					else{
						document.getElementById('act'+n).innerHTML = remplir(n, 2, tmp1, tmp2, null);
					}
				}
				else{	// on es en deuxième phase
					if(document.getElementById('sel2'+n).value == "-------")
						document.getElementById('act'+n).innerHTML = remplir(n, 1, tmp1, tmp2, tmp3);
					else if(document.getElementById('sel2'+n).value == "AJOUTER AVANT")
						document.getElementById('act'+n).innerHTML = remplir(n, 3, tmp1, tmp2, tmp3);
					else if(document.getElementById('sel2'+n).value == "AJOUTER APRES")
						document.getElementById('act'+n).innerHTML = remplir(n, 4, tmp1, tmp2, tmp3);
					else if(document.getElementById('sel2'+n).value == "SUPPRIMER SELECTION")
						document.getElementById('act'+n).innerHTML = remplir(n, 5, tmp1, tmp2, tmp3);
				}	
			}
			
			
			</script>	
			
			<div id="right">Gestion des mods [générer]</div>
			<div class="clear"><br />
			<div style="float:right; padding-left:10px; width:400px; color:red;">
				<img src="http://213.186.33.2/~weboufpa/softBB/images/distant/ne_fonctionne_pas_avec_IE.jpg" border="0" style="float:right; padding-left:10px;" />
				Attention, ce formulaire (en javascript) ne fonctionne pas correctement sous Internet Explorer, 
				<span style="font-size:0.8em;">(je n'ai rien contre ce navigateur mais je n'ai pas le temps de réadapter le script !)</span><br />
				<span style="font-size:0.9em; color:green;">Vueillez utiliser Opera, Firefox, Safari, Chrome ou autres, merci de votre compréhension.</span>
			</div>
			<span style="color:blue;">Merci de partager votre savoir-faire, la communautée de SoftBB vous le rendra !<br />
			Ce formulaire vas vous permettre de créer un fichier de modification séquentiel pour adapter votre script</span><br /><br />
			<div class="clear"><br />
			<form name="generer" action="mods_index.php?p=generer" method="post">
			<p>
				<label for="nom">Nom de votre mod : </label><input type="text" name="nom" id="nom" value="" /><span style="float:right; width:230px; font-size:0.8em; color:grey;">Attention ! Seuls les caractères de <b>a-z</b>, de <b>A-Z</b> et <b>_</b> sont acceptés, les autres seront supprimés !</span><br />
				<label for="description">Description du mod : </label><input type="text" name="description" id="description" value="" /><br />
				<label for="pseudo">Votre pseudo SoftBB.net : </label><input type="text" name="pseudo" id="pseudo" value="" /><span style="float:right; width:230px; font-size:0.8em; color:grey;">Un <b>pseudo exact</b> est requis, il sera vérifié à l'import</span><br /><br />
				<span style="float:right; width:230px; font-size:0.8em; color:grey;">Indiquez ici le/ les <b>noms exacts des mods (sans le .sbbmod)</b> qui doivent être installés pour que votre mod fonctionne</span>
				<label for="dependance1">Dépendance 1 : </label><input type="text" name="dependance1" id="dependance1" value="" /><br />
				<label for="dependance2">Dépendance 2 : </label><input type="text" name="dependance2" id="dependance2" value="" /><br /><br />
				<input type="button" name="add" value="Ajouter une nouvelle étape" onClick="javascript:ajout()" style="float:left;  margin:10px; padding:10px 3px 10px 3px;" /> 
				<span style="color:green; width:440px;">Les étapes vides ne seront pas prisent en compte, pas d'inquiétudes si vous en ajoutez trop ;)<br />Le fichier créé sera éditable à la main et logique</i><br /><br /></span>
				<div id="actions" class="box"></div>
				<input type="submit" name="envoyer" value="VALIDER" style="margin-left:30%; padding:10px 100px 10px 100px; margin-top:20px; font-size:1.3em;" />
			</p>
			</form>
			
			
			<script type="text/javascript">
				for(u=0; u<1; u++)
					ajout();		// on peut ajouter seulement après avoir afficher le html
			</script><?php
		}
	}
	elseif(isset($_GET['p']) && $_GET['p'] == "gestion")
	{
		
		menageAdmin();
		
		echo '<div id="right">Gestion des mods [ajout/ suppr]</div>
		<div class="clear"><br />
		Ce panneau va vous permettre de gérer vos mods mais avant tout voici quelques explications sur leur fonctionnement :<br />
		<div style="margin-left:15px; color:#285216; margin-top:4px;">A chaques mods ajoutés, une sauvegarde est créé de tous les fichiers modifiés 
		même des nouveaux fichiers <i>(dans le fameux dossier '.$backup_folder.')</i><br />
		A chaques mod installé, un dossier avec le timestamp de modifiaction est créé comprenant tous les fichiers tels qu\'ils étaient avant la modification ; 
		il est donc possible de revenir en rarrière SI<br />
		Le programme va vérifier tout de suite l\'intégrité des sauvegardes.</div>
		
		<br />Pas d\'inquiétudes, tout est automatique !<br />
		
		
		<form enctype="multipart/form-data" name="install" action="mods_index.php?p=gestion" method="post">
			<p>
				<fieldset style="width:550px; margin:auto; border:1px dotted grey;">
					<legend style="color:green; text-transform:uppercase; letter-spacing:1.1em;"> Installer un mod </legend>
					<input type="file" name="fichier" style="width:450px;" />
					<input type="submit" name="send" value="Installer !" />	
				</fieldset>
			</p>
		</form><br />';
		
		echo '<div class="titr">Rapport des installations et sauvegardes</div>';
		
		// on récupère les infos dans le fichier history (nom, date, pages modifiées, dépendances)
		$infos = getInstalledModInfo();
		$date = $infos[0];
		$nom = $infos[1];
		$pages = $infos[2];
		$dep = $infos[3];
		
		echo '<br />
		<table class="status" style="width:99%; margin:auto;" cellspacing="0">
		    <tr class="tr_titre">
		        <td>Nom</td>
		        <td>Installé le</td>
		        <td>dépendance</td>
		        <td>fichiers</td>
		        <td>options</td>
		    </tr>';
		if(count($date) == 0)
			echo '<tr>
			<td colspan="5" style="text-align:center;padding:8px; color:red;">
				Aucun mod n\'a été installé</td>
			</tr>';
		else{
			for($i=0; $i<count($date); $i++){
				
				if(!file_exists('../'.$backup_folder.'/'.$date[$i]))
					$dossier_introuvable = true;
				else
					$dossier_introuvable = false;
				
				
				echo '<tr';
					if($dossier_introuvable)
						echo ' style="background-color:#FFD1D6;"';
						
				echo '><td>'.str_replace('_', ' ', $nom[$i]).'</td>
				<td>'.date('d/m/Y \à h:i', $date[$i]).'</td>
				<td>';
				
				if(preg_match("/#DEP:( [a-zA-Z\d\_]+)+/", $pages[$i][2], $mat1)){
					preg_match_all('/ [a-zA-Z\d\_]+/', $mat1[0], $dependances);
						for($o=0; $o<count($dependances[0]); $o++){
							if(in_array(trim($dependances[0][$o]), $nom))
								echo '<font color="green">'.$dependances[0][$o].'</font><br />';
							else
								echo '<font color="red">'.$dependances[0][$o].'</font><br />';
						}
				}
				else
					echo '<font color="green">Aucune</font>';
				
				echo '</td>
				<td>';
				if($dossier_introuvable)
					echo '<b><a href="../'.$backup_folder.'/'.$date[$i].'/" title="Le mod ne peut être désinstallé si vous avez perdu le dossier de sauvegarde !!">Dossier introuvable</a></b><br />';
				else
				{	
					$tabf = array(); 	$pt=0;
					for($u=0; $u<count($pages[$i]); $u++){
						if(!preg_match("/#DEP/", $pages[$i][$u]))
						{
							if(!in_array($pages[$i][$u], $tabf)){
								if($u != 0)
									echo '<br />';
									$tab =array_count_values($pages[$i]);
								if(substr($pages[$i][$u],0,1) != '*' && !file_exists('../'.$backup_folder.'/'.$date[$i].'/'.$pages[$i][$u])) 
									echo '<span style="color:red;" title="Le fichier est introuvable, il est impossible  de le restaurer en cas de désinstallation">'.$pages[$i][$u].' ('.$tab[$pages[$i][$u]].')</span>';
								else{								
									echo '<span style="color:green;" title="Le fichier pourra être restauré tel qu\'il était avant">'.$pages[$i][$u].' ('.$tab[$pages[$i][$u]].')</span>';
								}
								$tabf[$pt++] = $pages[$i][$u];
							}
						}
					}
				}
				echo '</td>
				<td><a href="mods_index.php?desinstaller='.$date[$i].'">Désinstaller</a></td>
				</td></tr>';
			}
		}
		
		    
		
		echo '</table>
		<br /><br /><br />';

		
	}
	else
	{
?>
		<div id="right">Gestion des mods [infos]</div>
		
		<div class="clear"><br />
		<a href="http://www.softbb.net/mod.html"><img src="http://213.186.33.2/~weboufpa/softBB/images/softbb_mod_icon.png" alt="" style="border:6px solid #BDBDBD; float:right; padding:2px; margin:10px;" /></a>
		<span style="float:right; padding:15px; cursor:pointer; background-color:#E7E7E7;" onclick="document.location = 'http://www.softbb.net/mod.html'">
			<a href="http://www.softbb.net/mod.html">Page de téléchargement<br />des modules complémentaires</a>
		</span>
		<font color="blue">Bienvenue sur votre panneau de gestion des mods pour SoftBB  !</font><br />
		<font color="grey">Laissez vous guider par les trois liens en haut</font>
		<br /><br />
		<div class="titr">Avant tout</div>
		Ce module est développé par <b>patate_violente</b> <a href="http://www.post-prod-fr.com">[site web]</a> pour la communautée SoftBB, 
		dans la but d'installer facilement les modules de la communautée.
		<br /><br />
		<div class="titr">La réversibilité</div>
		En exécutant des mods créés par la communautée et approuvé par les modérateurs de SoftBB, il ne peut y avoir
		<font color="green"><b>aucun dégâts irréversibles</b></font> sur votre forum <font color="red">SAUF si vous supprimez le dossier <?php echo $backup_folder; ?></font> à la racine 
		de votre dossier forum.<br />
		<font color="green">Il vous est <b>possible d'installer les mods directement sur votre forum en ligne</b> depuis la version 0.9</font> : un .htaccess est placé dans les deux dossiers de stockage.
		<br /><br />
		<div class="titr">La sécurité</div>
		La fiabilité des mods offerts par la communautée de SoftBB n'est <font color="red">pas 100% assurée</font> mais est <font color="green">globalement interprété par la communautée 
		et les administrateurs du projet</font>, voir sur la page de téléchargement.<br />
		<font color="green"><i>S'agissant généralement de mise à jour ou nouvelles fonctionnalités minimes, les failles seront normalement très limités</i>, 
		vous pouvez disposer de ce système en toutes tranquilité ;)</font>
		<br /><br />
		<div class="titr">Mais en cas de mise à jour de softBB ?</div>
		<font color="green">SoftBB ne sera mis à jour qu'un prochaine fois en version 1.0</font>.<br />
		<font color="red">Les mod pour SoftBB 0.1 ne seront pour la plupart non compatibles pour SoftBB 1.0</font>.<br />
		Les mods seront divisés en deux versions pour toutes les versions de SoftBB existantes ! Avant d'installer
		un mod vérifiez bien qu'il soit pour votre version.<br /><br />
		
		<font color="blue">Merci de votre confiance,</font><br />
		> <a href="http://www.softbb.net/">Site Internet de SoftBB</a>.<br />
		> <a href="http://www.softbb.net/mod.html">Page de téléchargement des modules complémetaires</a>.
		</div>
		
		
<?php
	}
?>
</div>
</div>

<div id="menu">
<b><i>SoftBB_Gestimods</i> v0.9 officielle</b> par <a href="http://www.post-prod-fr.com" target="blank">patate_violente</a>, réservé aux utilisateurs de SoftBB
</div>
</body>
</html>
