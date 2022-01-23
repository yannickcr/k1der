<?
require("config.inc.php3");

$db = @mysql_connect("$dbhost", "$dblogi", "$dbpass") OR DIE("<br><br><center><font color=red face=arial size=2><b>Désolé, la Base est Down !</b></font></center>");
@mysql_select_db("$dbbase",$db) OR DIE;

$req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS WHERE id='$id_news'");

?>
<body text="#FFFFFF" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
<div align="center">
  <table border="0" cellpadding="0" cellspacing="0" width="500">
    <tr><td>
<?
$i=0;
$id         = mysql_result($req,$i,"id");
$titre      = stripslashes(trim(mysql_result($req,$i,"titre")));
$date       = mysql_result($req,$i,"date");
$heure      = mysql_result($req,$i,"heure");
$signature  = stripslashes(trim(mysql_result($req,$i,"signature")));
$email_sign = mysql_result($req,$i,"email_sign");
$url_source = trim(mysql_result($req,$i,"url_source"));
$news       = stripslashes(trim(mysql_result($req,$i,"news")));
$heure      = str_replace(":","h",$heure);
?>

<script language="JavaScript">
function SendNews(data){
window.open('send_news.php3?news='+data,'Envoyer','toolbar=0,location=0,directories=0,menuBar=0,scrollbars=1,resizable=0,width=420,height=350,left=0,top=0');
}

function PrintNews(data){
window.open('print_news.php3?news='+data,'Imprimer','toolbar=0,location=0,directories=0,menuBar=0,scrollbars=1,resizable=0,width=420,height=350,left=0,top=0');
}

function AddComment(data){
window.open(data,'Sondage','toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=1,width=430,height=335,left=0,right=0');
}
</script>

<?
#==-=- Gestion de la ville de référence pour le fuseau horraire -=-==-=#
if($ville!=""){ $ville = "- $ville"; }else{ $ville = ""; }

if($nom_source==""){ $titre_sour = ""; }else{ $titre_sour = "$SourceTitle : ";}

########################################################################
# Les 2 variables ci-dessous affichent les icones : "Imprimer la news" #
# et : "envoyer cette news à un ami". Pour les désactiver, il vous     #
# suffit de les passer en commentaire en plaçant un dièse devant "#"   #
########################################################################
$print = "<a href=\"Javascript:PrintNews('$id')\"><img src=\"images/imprimante.gif\" border=\"0\" alt=\"Imprimer l'info\"></a>&nbsp;&nbsp;";
$friend = "<a href=\"Javascript:SendNews('$id')\"><img src=\"images/email.gif\" border=\"0\" alt=\"Envoyer cette info à un ami\"></a>&nbsp;";
?>


        <table border="0" cellpadding="0" cellspacing="0" width="500" bgcolor="white">
          <tr><td>

              <table border="0" cellpadding="1" cellspacing="0" width="500" bgcolor="#000000">
                <tr><td width="100%">

<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
<tr><td width="100%"><div align="center">

                          <table border="0" cellpadding="3" cellspacing="0" width="500">
<?
if ($url_source =="k1d")
	{
		$ico = "images/icoeuf.gif";
	}
elseif ($url_source =="hl")
	{
		$ico = "images/hlico.gif";
	}
elseif ($url_source =="cs")
	{
		$ico = "images/csico.gif";
	}
?>
                            <tr>
                              <td bgcolor="<? echo $bgcolor_haut ?>" class=m9 background="../images/fond.gif"> 

                                <img src="<? echo $ico ?>" align="absmiddle"><font style="<? echo $TitreNews ?>"><a name="id_news_<? echo $id ?>"> 
                                <? echo $titre ?></font>
                                <font style="<? echo $DateNews ?>"> - 
                                <? echo "$date @ $heure $ville" ?>
                                </font></td>
                              <td bgcolor="<? echo $bgcolor_haut ?>" class=m8 background="../images/fond.gif">
                                <p align="right">
                                  <? echo "$print $friend" ?>
                              </td>
</tr>
<tr><td colspan="2" bgcolor="<? echo $bgcolor_corp ?>"><font style="<? echo $CorpsNews; ?>"><? echo "<p style=\"text-align: justify\">$news</p>"; ?></font>

</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>
</div>

<?
#-=-=-=-=-=-=-=- COMMENTAIRES -=-=-=-=-=-=-=-#
$reqCOMMENT = mysql_query("SELECT * FROM $TBL_COMMENTAIRES WHERE id_news='$id_news' ORDER BY id DESC");
$resCOMMENT = mysql_num_rows($reqCOMMENT);
?>

<script language="Javascript">
function Comments(data){
window.open(data,'Sondage','toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=1,width=430,height=335,left=0,right=0');
}
</script>

<br>
<div align="center">
  <center>
    <table width="180" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#FFFFFF">
      <tr> 
        <td valign="middle" bgcolor="#DE0200" bordercolor="#000000"> 
          <div align="center"><a href="Javascript:Comments('ajouter.php3?id_news=<? echo $id_news; ?>')"> 
            <font style="<? echo $TitreNews; ?>">Ajouter un commentaire</font> 
            </a></div>
        </td>
      </tr>
    </table>
    <br>
    <br>
    <table border="0" cellspacing="0" cellpadding="0" width="500" bgcolor="#000000">
      <tr>
      <td width="100%">
        <div align="center">
            <table border="0" cellspacing="1" width="500" cellpadding="3">
              <?
if($resCOMMENT!=0)
{
 $i=0;
 WHILE($i!=$resCOMMENT)
 {
  $id          = mysql_result($reqCOMMENT,$i,"id");
  $date        = mysql_result($reqCOMMENT,$i,"date");
  $heure       = mysql_result($reqCOMMENT,$i,"heure");
  $pseudo      = stripslashes(trim(mysql_result($reqCOMMENT,$i,"pseudo")));
  $commentaire = stripslashes(trim(mysql_result($reqCOMMENT,$i,"commentaire")));
  $date        = ereg_replace('^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})$','\\3/\\2/\\1', $date);
?>
              <tr>
                <td width="100%" bgcolor="<? echo $bgcolor_haut; ?>" background="../images/fond.gif"><font style="<? echo $Comment ?>"><b> 
                  <? echo $pseudo; ?>
                  </b></font><font style="<? echo $Comment ?>"> 
                  <? echo " - $date à $heure - <i>($id)<i>"; ?>
                  </font></td>
            </tr>
            <tr>
              <td width="100%" bgcolor="<? echo $bgcolor_corp; ?>"><font style="<? echo $Comment2 ?>"><? echo "<blockquote><p style=\"text-align: justify\">$commentaire</p></blockquote>"; ?></font></td>
            </tr>
<?
  $i++;
 }
}
else  // aucun commentaire
{
?>
            <tr>
              <td width="100%" bgcolor="<? echo $bgcolor_corp; ?>" align="center"><font style="<? echo $Comment ?>"><b>Aucun commentaires</b></font></td>
            </tr>
<?
}
?>
          </table>
        </div>
      </td>
    </tr>
  </table>
  </center>
</div>