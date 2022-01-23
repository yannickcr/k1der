<?
require("config.inc.php3");

$db = @mysql_connect("$dbhost", "$dblogi", "$dbpass") OR DIE("<br/><br/><center><font color=red face=arial size=2><b>Désolé, la Base est Down ...</b></font></center>");
@mysql_select_db("$dbbase",$db) OR DIE("<br/><br/><center><font color=red face=arial size=2><b>Désolé, la Base n'est pas accessible ...</b></font></center>");

$req = MYSQL_QUERY("SELECT * FROM mynewsinfos WHERE date LIKE '%$archives' ORDER BY id DESC");
$res = MYSQL_NUM_ROWS($req);

#==-=- Gestion de la ville de référence pour le fuseau horraire -=-==-=#
if($ville!=""){ $ville = "- $ville"; }else{ $ville = ""; }

# nous changeons les chiffres du mois #
$MONTH = $archives;
$MONTH = str_replace("01/","Janvier ",$MONTH);
$MONTH = str_replace("02/","Février ",$MONTH);
$MONTH = str_replace("03/","Mars ",$MONTH);
$MONTH = str_replace("04/","Avril ",$MONTH);
$MONTH = str_replace("05/","Mai ",$MONTH);
$MONTH = str_replace("06/","Juin ",$MONTH);
$MONTH = str_replace("07/","Juillet ",$MONTH);
$MONTH = str_replace("08/","Août ",$MONTH);
$MONTH = str_replace("09/","Septembre ",$MONTH);
$MONTH = str_replace("10/","Octobre ",$MONTH);
$MONTH = str_replace("11/","Novembre ",$MONTH);
$MONTH = str_replace("12/","Décembre ",$MONTH);

?>
<div align="center"> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><a name=\"TOP\">Archives 
  : 
  <? echo $MONTH; ?>
  </a></font> </div>
<br/>

<table border="0" cellpadding="0" cellspacing="0" width="500" align="center">
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
?>

<script language="JavaScript">
function SendNews(data){
window.open('send_news.php3?news='+data,'Envoyer','toolbar=0,location=0,directories=0,menuBar=0,scrollbars=1,resizable=0,width=420,height=350,left=0,top=0');
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
<tr><td>

<table border="0" cellpadding="1" cellspacing="0" width="100%" bgcolor="#000000">
<tr><td width="100%">

<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
<tr><td width="100%"><div align="center">

                        <table border="0" cellpadding="3" cellspacing="0" width="100%">
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
                            <td background="images/fond.gif" class=m9><img src="<? echo $ico ?>" align="absmiddle"><font style="<? echo $TitreNews ?>"><a name="id_news_<? echo $id ?>"> 
                              <? echo $titre ?></font>
                              <font style="<? echo $DateNews ?>"> - 
                              <? echo "$date @ $heure $ville"; ?>
                              </font></td>
                            <td background="images/fond.gif" class=m8>
<p align="right"><? echo "$print $friend"; ?></td>
</tr>
<tr><td colspan="2" bgcolor="<? echo $bgcolor_corp ?>">

<!-- Tableau intérieur du corps et des comments -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td width="100%"><font style="<? echo $CorpsNews; ?>"><? echo "$IMAGE <p style=\"text-align: justify\">$news</p>"; ?></font></td>
  </tr>
  <tr>
    <td width="100%" height="15">&nbsp;</td>
  </tr>
  <tr>
                                  <td width="100%" align="right"> 
                                    <?
#=-=-=-=- Comptage des commentaires pour la news -=-=-=-=-=-=-=-=-=-=-=-=-=-#
$caca = "_".$id."_";
//echo "$caca<br/>";
$dodo = mysql_query("SELECT * FROM ib_posts WHERE attach_hits='$caca'");
$grosse = mysql_fetch_array($dodo);
$popo = mysql_num_rows($dodo);
$grosseconne = $grosse[topic_id];
$dada = mysql_query("SELECT * FROM ib_posts WHERE topic_id='$grosseconne'");
$nombre = mysql_num_rows($dada);
$nombre = $nombre-1;
//echo "$nombre Commentaires";

/*if ($nombreux == '1')
{
if($nombre >='2')
{
$COMMENT = "<a href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">$nombre Commentaires</a>";
}
if($nombre =='1')
{
$COMMENT = "<a href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">1 Commentaire</a>";
}
else
{
$nombre = "<a href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">Aucun Commentaire</a>";
}
}
else
{
$reqCOMMENT = mysql_query("SELECT id FROM $TBL_COMMENTAIRES WHERE id_news='$id'");
$resCOMMENT = mysql_num_rows($reqCOMMENT);

if($resCOMMENT>='2'){ $COMMENT = "<a href=\"index.php?page=read_comment&id_news=$id\" class=\"plein\">$resCOMMENT $CommentsTitle</a>"; }
elseif($resCOMMENT=='1'){ $COMMENT = "<a href=\"index.php?page=read_comment&id_news=$id\" class=\"plein\">$UnCommentTitle</a>"; }
else{ $COMMENT = "<a href=\"index.php?page=read_comment&id_news=$id\" class=\"plein\">$NoCommentTitle</a>"; }
}*/
?>
                                    <?
if($source!='non'){ $SOURCE = " | <b>$SourceTitle</b> : <a href=\"$url_source\">$nom_source</a> "; }
else{ $SOURCE = ""; }

if ($nombre >= '1')
{
$disdonc = "<a target=_blank href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">$nombre Commentaires</a>";
}
if ($nombre == '1')
{
$disdonc = "<a target=_blank href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">$nombre Commentaire</a>";
}
if ($nombre == '0')
{
$disdonc = "<a target=_blank href=\"forum/index.php?act=ST&f=1&t=$grosseconne\" class=\"plein\">Aucun Commentaire</a>";
}
if ($grosse[topic_id] <= "3")
{
$reqCOMMENT = mysql_query("SELECT id FROM $TBL_COMMENTAIRES WHERE id_news='$id'");
$resCOMMENT = mysql_num_rows($reqCOMMENT);

if($resCOMMENT>='2'){ $disdonc = "<a target=_blank href=\"index.php?page=read_comment&id_news=$id\" class=\"plein\">$resCOMMENT $CommentsTitle</a>"; }
elseif($resCOMMENT=='1'){ $disdonc = "<a target=_blank href=\"index.php?page=read_comment&id_news=$id\" class=\"plein\">$UnCommentTitle</a>"; }
else{ $disdonc = "<a target=_blank href=\"index.php?page=read_comment&id_news=$id\" class=\"plein\">$NoCommentTitle</a>"; }
}
//echo "[$nombreux]";
?>
                                    <!-- COMMENTAIRES -->

                                    <font style="<? echo $Comment2; ?>"> [ <b> 
                                    <? echo $InfoTitle; ?>
									<?
									if($email_sign != '')
									{
									$mail_0 = strstr ($email_sign , "@");
									$mail_1 = str_replace ($mail_0,"",$email_sign);
									$mail_2 = str_replace ("@","",$mail_0);
									?>
									
									  <script language="JavaScript" type="text/javascript">
									  var un="<? echo $mail_1; ?>";
									  var deux = "<? echo $mail_2; ?>";
									  var texteCrypte="05204A0E420E22556659262B1B1A1A53";
									  var texteCrypte2="1B7F";
									  var texteCrypte3="056E0B58";
									  document.write(decrypte(texteCrypte)+un+"[AT]"+deux+decrypte(texteCrypte2)+"<? echo $signature; ?>"+decrypte(texteCrypte3));
									  </script>
                                    <?
									}
									else
									{
									echo $signature;
									}
									?>
                                    | <a href="Javascript:AddComment('ajouter.php3?id_news=<? echo $id; ?>')"> 
                                    <? echo $disdonc; ?>
                                    </a> ] </font> 
                                    <!-- COMMENTAIRES -->
                                  </td>
  </tr>
</table>
<!-- Tableau intérieur du corps et des comments -->

</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>

<!------- Séparation entre chaque news ------->
<img src="images/sep_25px_haut.gif" border="0">
<!------- Séparation entre chaque news ------->

<?
 $i++;
}
?>
</td></tr>
</table>

<!-- ARCHIVES -->
<?
$reqARC = MYSQL_QUERY("SELECT DISTINCT date,id FROM $TBL_NEWS ORDER BY id");
$resARC = MYSQL_NUM_ROWS($reqARC);

$MoisDepart = substr(mysql_result($reqARC,0,"date"),3,7);
$ARCHIVES = "[<a href=\"news/archives.php3?date=$MoisDepart\">$MoisDepart</a>]\n";

$i=0;
WHILE($i!=$resARC)
{
 $MoisEnCours = substr(mysql_result($reqARC,$i,"date"),3,7);

 if($MoisDepart==$MoisEnCours)
   {
    $i++;
   }
 else
   {
    $ARCHIVES = "[<a href=\"news/archives.php3?date=$MoisDepart\">$MoisEnCours</a>]\n - $ARCHIVES";
    $MoisDepart = $MoisEnCours;
    $i++;
   }
}

?>
<table border="0" cellspacing="0" width="500" align="center">
  <tr>
    <td width="100%" bgcolor="#000000">
      <table border="0" cellspacing="0" width="100%" cellpadding="3" bgcolor="<? echo $bgcolor_corp ?>">
        <tr>
          <td align="center" valign="top">
          <font style="<? echo $TitreNews; ?>"><a href="Javascript:history.back();">- Retour -</a></font>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!-- ARCHIVES -->

<br/>
