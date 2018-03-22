<?php
/***************************************************************************
 *
 *   SoftBB - Forum de discussion 
 *   Version : 0.1
 *
 *   copyright            : (C) 2005 J�r�my Dombier [Belgium]
 *   email                : satapi@gmail.com
 *   site-web             : http://softbb.be/
 *
 *   Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou 
 *   le modifier au titre des clauses de la Licence Publique G�n�rale GNU, 
 *   telle que publi�e par la Free Software Foundation ; soit la version 2 de 
 *   la Licence, ou (� votre discr�tion) une version ult�rieure quelconque. 
 *   Ce programme est distribu� dans l'espoir qu'il sera utile, mais 
 *   SANS AUCUNE GARANTIE ; sans m�me une garantie implicite de COMMERCIABILITE 
 *   ou DE CONFORMITE A UNE UTILISATION PARTICULIERE. Voir la Licence Publique 
 *   G�n�rale GNU pour plus de d�tails. Vous devriez avoir re�u un exemplaire 
 *   de la Licence Publique G�n�rale GNU avec ce programme ; si ce n'est pas le 
 *   cas, �crivez � la Free Software Foundation Inc., 
 *   51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 ***************************************************************************/
 
if(!defined('IN_SOFTBB')) exit('Not in SoftBB');
?>
<table class="texte_base_gras" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr align="center">
		<td height="29" class="titreforum">Groupe - Erreur</td>
	</tr>
	<tr>
		<td align="center" class="cadre_clair" style="padding:30px"> 
		<?php 
		if(isset($_GET['type']))
		{
			if($_GET['type'] == "membreban")
			{
				echo'<p>Ce membre n\'a pas valid� son compte, ou il a �t� banni. (ou il n\'existe simplement pas)</p>';
			}
			if($_GET['type'] == "deja")
			{
				echo'<p>Ce membre fait d�j� partie de ce groupe</p>';
			}
		}
		echo '<p><a href="index.php?page=affgroupe&amp;groupe='.$_GET['retour'].'">Retour &agrave; la page d\'affichage du groupe</a></p>';
		?>
		</td>
	</tr>
</table>          