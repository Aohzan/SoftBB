<?php

set_magic_quotes_runtime(0);
ini_set("register_globals","off"); 
include('info_bdd.php');
include('info.php');
include('info_options.php');
$db = mysql_connect($host,$user,$mdpbdd);
mysql_select_db($bdd,$db);


	$sql = 'SELECT nbr,titre FROM '.$prefixtable.'post WHERE id2 = '.$_GET['post'];
	$req = mysql_query($sql)  or die('Erreur SQL !'.$sql.'<br />'.mysql_error());
	$data = mysql_fetch_assoc($req); 
	
	$sql2 = 'SELECT id2 FROM '.$prefixtable.'post WHERE idsa = '.$_GET['post'].' order by tmppost DESC ';
	$req2 = mysql_query($sql2)  or die('Erreur SQL !'.$sql.'<br />'.mysql_error());
	$data2 = mysql_fetch_assoc($req2); 

header('Location: index.php?page=post&ids='.$_GET['post'].'&pg='.(ceil(($data['nbr']+1)/$postparpageaff)-1).'#'.$data2['id2']);

?>

