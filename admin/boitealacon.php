<?
require("../config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

if ($Submit == 'Enregistrer')
{
mysql_query("UPDATE config SET valeur='".addslashes($boitealacon)."' WHERE nom='boitealacon'") or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());
$ok = 1;
}
?>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?
$requete  = "SELECT * FROM config WHERE nom='boitealacon'";
$req = mysql_query($requete) or die('Erreur SQL !<br/>'.$requete.'<br/>'.mysql_error());
$disp = mysql_fetch_array($req);
?>
<form name="form1" method="post" action="">
  <textarea name="boitealacon" id="boitealacon" style="width:100%;height:150px;"><? echo $disp[valeur]; ?></textarea>
  <input type="submit" name="Submit" value="Enregistrer">
  <?
  if ($ok == 1)
  {
  ?>
  <strong><font color="#FF0000" size="1" face="Verdana, Arial, Helvetica, sans-serif">Enregistrement effectu&eacute;</font></strong> 
  <?
  }
  ?>
</form>
