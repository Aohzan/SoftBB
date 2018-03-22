<?php 
include_once('log.php');
$url_site = "http://213.186.33.2/~weboufpa/softBB/";

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
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>SoftBB - Administration</title>
<link rel="stylesheet" href="./install.css" type="text/css" />
</head>
<body>
<div id="titre"><img src="./install.jpg" alt="SoftBB - Administration" /></div>
<div id="install">
	<div id="right">Administration du forum</div>
	<div class="clear"></div>
<h1>Informations sur les mises à jour</h1>
<p><a href="index.php">Atteindre l'index de l'administration du forum.</a></p>
<div style="margin-left:30px; border:1px solid #C6C6C6; padding-left:10px; padding-bottom:10px;">
	<h2>Mise à niveau des mods</h2>
	<?php
			// images
			if(file_exists('../img/design/forum_pb_resolu.png'))
				$imgok = '<img src="../img/design/forum_pb_resolu.png" alt="" border="0" />';
			else
				$imgok = '<img src="'.$url_site.'forum/img/design/forum_pb_resolu.png" alt="" border="0" />';
			if(file_exists('../img/design/forum_pb_resolu.png'))
				$imgnok = '<img src="../img/design/enlever_resolu.png" alt="" border="0" />';
			else
				$imgnok = '<img src="'.$url_site.'forum/img/design/enlever_resolu.png" alt="" border="0" />';
			
		
		if(file_exists('../#BACKUP#/history.txt'))
		{
			$history = contenuFichier('../#BACKUP#/history.txt');
			preg_match_all('/[0-9]+ ([a-zA-Z_]+) :/', $history, $match);
			for($i=0; $i<count($match[1]); $i++){
				echo '- <b>'.$match[1][$i].' :</b> ';
				$lu = file($url_site.'info_update.php?mod='.$match[1][$i]);
				if(empty($lu[0]) || $lu[0] == 'error')
					echo '<font color="red">Le serveur distant est non joignable ou vous n\'êtes pas connecté à internet</font>'.$imgnok;
				else
				{
					if(file_exists('../#MODS#/'.$match[1][$i].'.sbbmod'))
					{
						$install = contenuFichier('../#MODS#/'.$match[1][$i].'.sbbmod');
						if(preg_match('/#\*#\*# VERSION : ([0-9]+)/', $install, $mat)){
							if($lu[0] <= $mat[1])
								echo '<font color="green">Votre version est à jour (version '.$lu[0].')</font>'.$imgok;
							else if(!empty($lu[0]))
								echo '<font color="red">Le serveur est injoignable. Votre version : '.$mat[1].'</font>';
							else
								echo '<font color="red">Vous n\'êtes pas à jour ! Version possédée : '.$mat[1].' | version en ligne : '.$lu[0].'</font>'.$imgnok;
						}
						else
							echo '<font color="red">Impossible de déterminer le numéro de version dans le fichier d\'installation</font>'.$imgnok;
					}
					else
						echo '<font color="red">Impossible de déterminer votre version : le fichier d\'installation est introuvable</font>'.$imgnok;
				}
				echo '<br />';
			}
		}
		else
			echo 'Le fichier history.txt du dossier BACKUP n\'existe pas';

	///////////////////////////////////////////////////////////
	echo '<h2>Mise à niveau de SoftBB</h2>';
	$index = contenuFichier('../index.php');
	echo '<b>Votre version : </b>';
	if(preg_match('/\[ *(?:Copyright)* SoftBB v([0-9\.]+) *\]/', $index, $mat)){
		echo '<font color="green">'.$mat[1].'</font>';
		$vuse = $mat[1];
	}
	else
		echo '<font color="red">Impossible à déterminer</font>';

	echo '<br /><b>La dernière version disponible : </b>';
	$vmax = file($url_site.'/info_update.php?sbbtopversion=');
	echo $vmax[0];
	if(!empty($vmax) && $vmax[0] != 'error'){
		if($vmax[0] == $vuse)
			echo '<br /><font color="green">Vous êtes à jour</font>'.$imgok;
		else
			echo '<br /><font color="orange">Vous pourriez vous mettre à jour</font>'.$imgnok;
	}

	echo '<br /><br />Tenez vous à jour par <a href="http://www.softbb.net">le site officiel</a> ; <br />
	<i>pour mettre à jour un mod, désinstallez l\'ancien puis réinstallez le nouveau.</i>';
	?>
</div>
<p><a href="index.php">Atteindre l'index de l'administration du forum.</a></p>
</div>
</body>
</html>

