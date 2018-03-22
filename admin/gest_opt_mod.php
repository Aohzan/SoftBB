<?php 
	include_once('log.php'); 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>SoftBB - Administration</title>
	<link rel="stylesheet" href="./install.css" type="text/css" />
	<script language="JavaScript" type="text/JavaScript">
		function decision(message, url){
			if(confirm(message)) location.href = url;
		}
	</script>
	<style>
		legend{	font-size:1.2em;	display:block;	font-weight:bold;	margin-left:29px; 	letter-spacing:4px;	}
	</style>
</head>
<body>
<div id="titre"><img src="./install.jpg" alt="SoftBB - Administration" /></div>
<div id="install">
	<div id="right">Administration du forum</div>
	<div class="clear"></div>	
	<p><a href="index.php">Atteindre l'index de l'administration du forum.</a></p>
	<h1>Gestion des options</h1>
<?php

include('../info_options.php');

echo '
<form name="form1" method="post" action="save_opt_mod.php">
	<!-- placer form apres -->
<fieldset>
	<legend>Mod bbcode_extension : style</legend>
		Vous devez entrer du css, ce code sera ajouté à celui par défaut : <br />
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br />
		<input type="text" name="bbcss_quote_global" value="'.stripslashes($bbcss_quote_global).'" style="width:400px;" /> : Style de la zone de citation (global) <img src="../img/bbcode/citation.png" /><br />
		<input type="text" name="bbcss_quote_titre" value="'.stripslashes($bbcss_quote_titre).'" style="width:400px;" /> : Style de la zone de citation (titre) <img src="../img/bbcode/citation.png" /><br />
		<input type="text" name="bbcss_quote" value="'.stripslashes($bbcss_quote).'" style="width:400px;" /> : Style de la zone de citation (text) <img src="../img/bbcode/citation.png" /><br />
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br />
		<input type="text" name="bbcss_code_titre" value="'.stripslashes($bbcss_code_titre).'" style="width:400px;" /> : Titre zone de code <img src="../img/bbcode/code.png" /><br />
		<input type="text" name="bbcss_code" value="'.stripslashes($bbcss_code).'" style="width:400px;" /> : Style de la zone de code <img src="../img/bbcode/code.png" /><br />
		<input type="text" name="bbcss_shell_titre" value="'.stripslashes($bbcss_shell_titre).'" style="width:400px;" /> : Text présentation console <img src="../img/bbcode/codeconsole.png" /><br />
		<input type="text" name="bbcss_shell" value="'.stripslashes($bbcss_shell).'" style="width:400px;" /> : Style de la zone de code console <img src="../img/bbcode/codeconsole.png" /><br />
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br />
		<input type="text" name="bbcss_flottant" value="'.stripslashes($bbcss_flottant).'" style="width:400px;" /> : Style des flottants <img src="../img/bbcode/flottant_gauche.png" /><img src="../img/bbcode/flottant_droit.png" /><br />
		<input type="text" name="bbcss_spoil" value="'.stripslashes($bbcss_spoil).'" style="width:400px;" /> : Style des spoils <img src="../img/bbcode/spoil.png" /><br />
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br />
		<input type="text" name="bbcss_list" value="'.stripslashes($bbcss_list).'" style="width:400px;" /> : Style des listes <img src="../img/bbcode/liste.png" /><br />
		<input type="text" name="bbcss_puce" value="'.stripslashes($bbcss_puce).'" style="width:400px;" /> : Style des puces <img src="../img/bbcode/liste.png" /><br />
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br />
		<input type="text" name="bbcss_img" value="'.stripslashes($bbcss_img).'" style="width:400px;" /> : Style des images <img src="../img/bbcode/image.png" /><br />
</fieldset>
<p><input type="submit" name="Submit" value="Enregistrer les options" class="bouton" /></p>
</form>';
?>
</div>
</body>
</html>
