<?php include_once('log.php'); ?>
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
	
	<p><a href="index.php">Atteindre l'index de l'administration du forum.</a></p>
	
	<h1>Enregistrement réalisé avec succès </h1><?php
	
function addslashes2 ($chaine) {
	return str_replace("'","\'",str_replace("\\","\\\\",$chaine));
}
	
$insert = '<?php
// Ce fichier contient les options des mods qui implémentes le mod "options_pour_tous"
// ajouter ici données
$bbcss_code_titre = \''.addslashes($_POST['bbcss_code_titre']).'\';
$bbcss_code = \''.addslashes($_POST['bbcss_code']).'\';
$bbcss_quote_global = \''.addslashes($_POST['bbcss_quote_global']).'\';
$bbcss_quote_titre = \''.addslashes($_POST['bbcss_quote_titre']).'\';
$bbcss_quote = \''.addslashes($_POST['bbcss_quote']).'\';
$bbcss_shell_titre = \''.addslashes($_POST['bbcss_shell_titre']).'\';
$bbcss_shell = \''.addslashes($_POST['bbcss_shell']).'\';
$bbcss_flottant = \''.addslashes($_POST['bbcss_flottant']).'\';
$bbcss_spoil = \''.addslashes($_POST['bbcss_spoil']).'\';
$bbcss_list = \''.addslashes($_POST['bbcss_list']).'\';
$bbcss_puce = \''.addslashes($_POST['bbcss_puce']).'\';
$bbcss_img = \''.addslashes($_POST['bbcss_img']).'\';

?>';

$fp = fopen('../info_options_mod.php','w+');
fseek($fp,0);
fputs($fp,$insert);
fclose($fp);
	
?>
	
	<p>Les options des mods sont maintenant modifiées </p>
	<p><a href="gest_opt_mod.php">Atteindre la configuration des options </a></p>
</div>
</body>
</html>

