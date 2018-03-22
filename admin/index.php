<?php  
if(!isset($_GET['menu']) && !isset($_GET['page']))
	include_once('log.php');

if(!isset($_GET['page']) || (isset($_GET['page']) && $_GET['page'] == 'index'))
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>SoftBB - Administration</title>
		<link rel="stylesheet" href="./install.css" type="text/css" />
	</head>';
$img = 'style="width:100px;"';

if(!isset($_GET['menu']) && !isset($_GET['page'])){
	echo ' 
	<FRAMESET rows="105,*" frameborder="no" scrolling="no" framespacing="0" name="frame">	
		<FRAME name="sommaire" target="page" src="index.php?menu=" scrolling="auto">
		<FRAME name="page" src="index.php?page=index" scrolling="auto">
	</FRAMESET>';
}
elseif(isset($_GET['menu']))
	echo '
	<div style="width:100%; background-color:white; height:100px; background-image:URL(\'fondtitre.jpg\'); margin-top:0px; border-bottom:2px solid #AFAFAF;">
		<div style="float:left; margin-right:9px;  background-image:URL(\'fondtitre.jpg\'); background-repeat:repeat-x;">
			<a href="index.php?page=index&index" target="page">
				<img src="./install.jpg" alt="SoftBB - Administration" style="border:none; padding-right:20px;" />
			</a><br />
			<div style="margin-left:20px;">
				<a href="./index.php?page=info_update" target="page" title="Vérifie les mises à jour des mods disponibles. Pour celà, vous devez avoir installé le mod se_tenir_a_jour"><img src="../img/moderation/annonce.gif" style="width:11px;" /> Vérifier mises à jour</a><br />
				<a href="./index.php?page=voirforum" target="page" title="Appercu du forum tout en restant dans l\'administration !"><img src="../img/moderation/synchroniser.gif" style="width:11px;" /> Apercu du forum</a> | <a href="../index.php" target="frame" title="Sortir de l\'administration"><img src="../img/moderation/suppr_sujet.gif" style="width:11px;" /> Quiter admin</a><br />
			</div>
		</div>
			<div style="white-space:nowrap; margin-left:280px; text-align:center;">
				<a href="./index.php?page=conf_forum" target="page"><img '.$img.' src="../img/design/admin_forums.png" border="0" alt="img" title="Ici vous allez pouvoir configurer les différents forums, autrement dit, les différentes catégories et forums respectifs. Vous allez pouvoir créer, modifier, déplacer vos forums, ainsi qu\'ajouter des permissions particulières." /></a>
				<a href="./index.php?page=gest_opt" target="page"><img '.$img.' src="../img/design/admin_options.png" border="0" alt="img" title="Vous allez pouvoir modifier les options courantes de votre forum dans cette catégorie." /></a>
				<a href="./index.php?page=gest_group" target="page"><img '.$img.' src="../img/design/admin_groupe.png" border="0" alt="img" title="Les groupes permettent de gerer des autorisations pour différents forums de manière individuelle pour les utilisateurs. Il sagit d\'une liste de personnes faisants partie d\'un groupe, en tant que membre du groupe ou que chef de ce groupe. Vis à vis de cela, un groupe peut être attaché à un forum et ainsi autoriser ou non des membres à acceder au forum." /></a>
				<a href="./index.php?page=gest_emotes" target="page"><img '.$img.' src="../img/design/admin_smiley.png" border="0" alt="img" title="Vous allez pouvoir modifier les émoticons ainsi que les rangs que vous fournirez à vos membres." /></a>
				<a href="./index.php?page=mods_index" style="cursor:pointer;" target="page"><img '.$img.' src="../img/design/admin_mod.png" border="0" alt="img" title="L\'indispensable gestionnaire de mod ! Il vous permet d\'améliorer votre forum en quelques clics, avec toujours la grantie de revenir en arrière." /></a>
				<a href="./index.php?page=gest_opt_mod" target="page"><img '.$img.' src="../img/design/admin_mod_opt.png" border="0" alt="img" title="Panneau qui ouvre les options des mods qui se servent du mod optionsmods_et_adminplus" /></a>
			</div>
	</div>';

elseif(isset($_GET['page']) && $_GET['page'] != 'index'){
	if($_GET['page'] == 'voirforum')
		echo '<iframe src="../index.php" width="100%" height="100%" frameborder="0"></iframe>';
	elseif(file_exists($_GET['page'].'.php'))
		include($_GET['page'].'.php');
}
else{
	echo '<div id="install">
	<div id="right">Administration du forum</div>
	<div class="clear"></div>
	Pour naviguer dans l\'administration,<br />
	utilisez les logo dans la barre de menu ci-dessus';

}

?>

</div>
</body>
</html><!--
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
<p><a href="info_update.php">Vérifier l'état des versions des composants.</a></p><p>Bonjour et bienvenue dans l'administration de votre forum. Cette section vous permet de gerer les diff&eacute;rents forums, permissions, groupes. Ainsi que de modifier les donn&eacute;es courantes de la configuration de votre forum.</p>
	<p>Lorsque vous avez termin&eacute; vos modifications, vous pouvez vous rendre sur <a href="../index.php">l'index du forum</a></p>
	
	<h1>Configurer vos forums</h1>
	
	<p>Ici vous allez pouvoir configurer les diff&eacute;rents forums, autrement dit, les diff&eacute;rentes cat&eacute;gories et forums respectifs. Vous allez pouvoir cr&eacute;er, modifier, d&eacute;placer vos forums, ainsi qu'ajouter des permissions particuli&egrave;res.</p>
	<p><a href="./conf_forum.php">Atteindre la configuration de vos forums</a></p>
	
	<h1>Gerer vos groupes </h1>
	<p>Les groupes permettent de gerer des autorisations pour diff&eacute;rents forums de mani&egrave;re individuelle pour les utilisateurs. Il sagit d'une liste de personnes faisants partie d'un groupe, en tant que membre du groupe ou que chef de ce groupe. Vis &agrave; vis de cela, un groupe peut &ecirc;tre attach&eacute; &agrave; un forum et ainsi autoriser ou non des membres &agrave; acceder au forum.</p>
	<p><a href="./gest_group.php">Atteindre la gestion de vos groupes  </a></p>
	<h1>Options du forum  </h1>
	<p>Vous allez pouvoir modifier les options courantes de votre forum dans cette cat&eacute;gorie. </p>
	<p><a href="./gest_opt.php">Atteindre la gestion des options </a></p>
	<h1>Gerer les &eacute;moticons et les rangs  </h1>
	<p>Vous allez pouvoir modifier les &eacute;moticons ainsi que les rangs que vous fournirez &agrave; vos membres. </p>
	<p><a href="./gest_rang.php">Atteindre la gestion des rangs </a></p>
	<p><a href="./gest_emotes.php">Atteindre la gestion des &eacute;motes (smilies) </a></p>
</div>
</body>
</html>
-->