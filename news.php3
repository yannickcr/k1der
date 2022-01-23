<?
require("config.inc.php3");
$date2now = date("d/m/Y");

$db = @mysql_connect("$dbhost", "$dblogi", "$dbpass") OR DIE("<br><br><center><font color=red face=arial size=2><b>Désolé, la Base est Down ...</b></font></center>");
@mysql_select_db("$dbbase",$db) OR DIE("<br><br><center><font color=red face=arial size=2><b>Désolé, la Base n'est pas accessible ...</b></font></center>");

$req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS ORDER BY id DESC LIMIT 0, 5");
$res = MYSQL_NUM_ROWS($req);

#==-=- Gestion de la ville de référence pour le fuseau horraire -=-==-=#
if($ville!=""){ $ville = "- $ville"; }else{ $ville = ""; }

?> <font style="<? echo $DateNews ?>"> </font><font style="<? echo $DateNews ?>"> 
</font><table border="0" cellpadding="0" cellspacing="0" width="532" align="center"> 
<tr><td> 
<?
$i=0;
WHILE($i!=$res)
{
$id         = mysql_result($req,$i,"id");
$titre      = stripslashes(trim(mysql_result($req,$i,"titre")));
$date       = mysql_result($req,$i,"date");
$heure      = mysql_result($req,$i,"heure");
$signature  = stripslashes(trim(mysql_result($req,$i,"signature")));
$email_sign = mysql_result($req,$i,"email_sign");
$news       = stripslashes(trim(mysql_result($req,$i,"news")));
$heure      = str_replace(":","h",$heure);
$source     = mysql_result($req,$i,"source");
$nom_source = mysql_result($req,$i,"nom_source");
$url_source = trim(mysql_result($req,$i,"url_source"));
$image      = mysql_result($req,$i,"image");
$path_image = trim(mysql_result($req,$i,"path_image"));
$url_image  = trim(mysql_result($req,$i,"url_image"));



if($i==0){ $titre = "<a name=\"TOP\">$titre</a>"; }
?>
<script language="JavaScript">
function SendNews(data){
window.open('send_news.php3?news='+data,'Envoyer','toolbar=0,location=0,directories=0,menuBar=1,scrollbars=1,resizable=0,width=420,height=350,left=0,top=0');
}

function PrintNews(data){
window.open('print_news.php3?news='+data,'Imprimer','toolbar=0,location=0,directories=0,menuBar=0,scrollbars=1,resizable=0,width=620,height=450,left=0,top=0');
}

function AddComment(data){
window.open(data,'Sondage','toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=1,width=430,height=335,left=0,right=0');
}
</script> 
<?
if($nom_source==""){ $titre_sour = ""; }else{ $titre_sour = "$SourceTitle : ";}


/*-=-=-=-=-=- ICONES =-=-=-=-=-=-*/
if($ICONE_PRINT=='oui'){
$print = "<a href=\"Javascript:PrintNews('$id')\"><img src=\"images/imprimante.gif\" border=\"0\" alt=\"Imprimer l'info\"></a>&nbsp;&nbsp;";
}else{ $print = ""; }

if($ICONE_SEND=='oui'){
$friend = "<a href=\"Javascript:SendNews('$id')\"><img src=\"images/email.gif\" border=\"0\" alt=\"Envoyer cette info à un ami\"></a>&nbsp;";
}else{ $friend = ""; }


#=-=-=-=-=-=-=-=-=- Gestion de l'image de droite -=-=-=-=-=-=-=-=-=-=-=#
if($image!='non')
{
$IMAGE = "<a href=\"$url_image\" target=\"_blank\">
<img src=\"$path_image\" border=\"0\" width=\"120\" height=\"90\" align=\"right\"></a>";
}
else{ $IMAGE = ""; }

?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="white"> 
<tr><td align="CENTER" width="32" valign="MIDDLE" height="7"><font style="<? echo $DateNews ?>"> 
<?
if ($date == $date2now)
	{
	echo "<img src='images/new.gif' align='absmiddle'>";
	}
?>
</font></td><td align="RIGHT" width="500" rowspan="3"> <table border="0" cellpadding="1" cellspacing="0" width="500" bgcolor="#000000"> 
<tr><td width="100%"> <table border="0" cellpadding="0" cellspacing="0" width="500" bgcolor="white"> 
<tr><td width="100%"><div align="center"> <table border="0" cellpadding="3" cellspacing="0" width="500"> 
<tr background="../images/fond.gif"> 
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
<td bgcolor="<? echo $bgcolor_haut ?>" class=m9 background="images/fond.gif"><img src="<? echo $ico ?>" align="absmiddle"><font style="<? echo $TitreNews ?>"><a name="id_news_<? echo $id ?>"> 
<? echo $titre ?></font>
<font style="<? echo $DateNews ?>"> - 
<? echo "$date @ $heure $ville"; ?>
</font></td><td bgcolor="<? echo $bgcolor_haut ?>" class=m8 background="images/fond.gif"> 
<p align="right"> 
<? echo "$print $friend"; ?>
</td></tr> <tr>
                            <td colspan="2" bgcolor="<? echo $bgcolor_corp ?>"> 
                              <table border="0" cellpadding="0" cellspacing="0" width="500"> <tr> <td width="100%"><font style="<? echo $CorpsNews; ?>"> 
<? echo "$IMAGE <p style=\"text-align: justify\">$news</p>"; ?>
</font></td></tr> <tr> <td width="100%" height="15">&nbsp;</td></tr> <tr> 
                                  <td width="100%" align="right"> 
                                    <?
#=-=-=-=- Comptage des commentaires pour la news -=-=-=-=-=-=-=-=-=-=-=-=-=-#
$reqCOMMENT = mysql_query("SELECT id FROM $TBL_COMMENTAIRES WHERE id_news='$id'");
$resCOMMENT = mysql_num_rows($reqCOMMENT);

if($resCOMMENT>='2'){ $COMMENT = "<a href=\"index.php3?page=read_comment&id_news=$id\" class=\"plein\">$resCOMMENT $CommentsTitle</a>"; }
elseif($resCOMMENT=='1'){ $COMMENT = "<a href=\"index.php3?page=read_comment&id_news=$id\" class=\"plein\">$UnCommentTitle</a>"; }
else{ $COMMENT = "<a href=\"index.php3?page=read_comment&id_news=$id\" class=\"plein\">$NoCommentTitle</a>"; }
?>
                                    <?
if($source!='non'){ $SOURCE = " | <b>$SourceTitle</b> : <a href=\"$url_source\">$nom_source</a> "; }
else{ $SOURCE = ""; }
?>
                                    <font style="<? echo $Comment2; ?>"> [ <b> 
                                    <? echo $InfoTitle; ?> </b> : <a href="mailto:<? echo $email_sign; ?>" title="Ecrire à <? echo $signature; ?>"> 
                                    <? echo $signature; ?> </a> | <a href="Javascript:AddComment('ajouter.php3?id_news=<? echo $id; ?>')"> 
                                    <? echo $COMMENT; ?> </a> ] </font> </td>
                                </tr> </table> 
                            </td>
                          </tr> </table></td></tr> </table></td></tr> </table><font style="<? echo $DateNews ?>">&nbsp; 
</font></td></tr><tr><td align="CENTER" width="32" valign="MIDDLE" height="25"><font style="<? echo $DateNews ?>">&nbsp; 
</font></td></tr><tr><td align="RIGHT" width="32" valign="TOP"><font style="<? echo $DateNews ?>">&nbsp; 
</font></td></tr> </table> 
      <img src="images/sep_25px_haut.gif" border="0"> 
      <?
 $i++;
}
?>
    </td>
  </tr> </table> 
<?
$reqARC = MYSQL_QUERY("SELECT DISTINCT date,id FROM $TBL_NEWS ORDER BY id");
$resARC = MYSQL_NUM_ROWS($reqARC);

$MoisDepart = substr(mysql_result($reqARC,0,"date"),3,7);
$ARCHIVES = "[<a href=\"index.php3?page=archives&date=$MoisDepart\">$MoisDepart</a>]\n";

$i=0;
WHILE($i!=$resARC)
{
 $MoisEnCours = substr(mysql_result($reqARC,$i,"date"),3,7);

 if($MoisDepart==$MoisEnCours)
   {
    $MoisDepart = $MoisEnCours;
    $i++;
   }
 else
   {
    $MoisDepart = $MoisEnCours;
    $ARCHIVES = "[<a href=\"index.php3?page=archives&date=$MoisDepart\">$MoisEnCours</a>]\n - $ARCHIVES";
    $i++;
   }
}

?>
<div align="center"></div>
<table border="0" cellspacing="0" width="500" align="center">
  <tr>
    <td width="100%" bgcolor="#000000">
      <table border="0" cellspacing="0" width="500" cellpadding="3" bgcolor="<? echo $bgcolor_corp ?>">
        <tr>
          <td align="center" valign="top"><font color="#000000" size="2"><b><font face="Verdana, Arial, Helvetica, sans-serif">Archives 
            : <font style="<? echo $CorpsNews; ?>"> 
            <? echo $ARCHIVES; ?>
            </font></font></b></font><font style="<? echo $CorpsNews; ?>" color="#000000">&nbsp; 
            </font> </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
