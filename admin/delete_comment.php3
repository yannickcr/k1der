<?
include "secu.php";?><?
 require("config.inc.php3");

 $db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
 mysql_select_db("$dbbase",$db) or Die("Base Down !");

 $rqt = mysql_query("SELECT * FROM mynewscomments WHERE id='$NumComment'");
 $rst = mysql_num_rows($rqt);

 if($rst==0)
 {
  ?>
  <script language="Javascript">
  alert('Désolé, il n\'y a pas de commentaire portant ce numéro !');
  history.back();
  </script>
  <?
 }
 else
 {
  $rqt = mysql_query("DELETE FROM $TBL_COMMENTAIRES WHERE id='$NumComment'");
  if(!$rqt)
  {
   ?>
   <script language="Javascript">
   alert('Désolé, la suppression à échouée, veuillez ré-essayer !');
   history.back();
   </script>
   <?
  }
  else
  {
   ?>
   <script language="Javascript">
   alert('Suppression du commentaire n°<? echo $NumComment; ?> effectuée !');
   window.location='index.php?page=admin';
   </script>
   <?
  }
 }
?>