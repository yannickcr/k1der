<?
require("config.inc.php3");
$date2now = date("d/m/Y");

$db = @mysql_connect("$dbhost", "$dblogi", "$dbpass") OR DIE("<br/><br/><center><font color=red face=arial size=2><b>Désolé, la Base est Down ...</b></font></center>");
@mysql_select_db("$dbbase",$db) OR DIE("<br/><br/><center><font color=red face=arial size=2><b>Désolé, la Base n'est pas accessible ...</b></font></center>");

?>
<?
if ($HTTP_COOKIE_VARS[gen] != "") $req = MYSQL_QUERY("SELECT * FROM mynewsinfos WHERE conf != '0' ORDER by id DESC limit 0,5");
else $req = MYSQL_QUERY("SELECT * FROM mynewsinfos WHERE conf != '0' && url_source != 'admin' ORDER by id DESC limit 0,5");
$res = MYSQL_NUM_ROWS($req);

#==-=- Gestion de la ville de référence pour le fuseau horraire -=-==-=#
if($ville!=""){ $ville = "- $ville"; }else{ $ville = ""; }

?>

<div align="center">
 <table width="532" border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td><table width="498" border="0" align="right" cellpadding="0" cellspacing="0">
     <tr align="center">
      <td colspan="3" style="padding:10px 0px;">
	  <?php
	  if(!ereg('MSIE',$_SERVER['HTTP_USER_AGENT'])) {
	  ?>
	  <a href="tusors.php?url=http://www.arobase29.net" target="_blank"><img style="border:0px;" src="images/arobase29.gif" title="Arobase 29, Votre espace conseil en informatique" alt="Bannière de notre sponsor Arobase 29" /></a>
	  <?php
	  } else {
	  ?>
      <script type="text/javascript">
       <!--
        google_ad_client = "pub-3073868596887719";
        google_ad_width = 468;
        google_ad_height = 60;
        google_ad_format = "468x60_as_rimg";
        google_cpa_choice = "CAAQxYv7zwEaCDQ24eD-KfakKJW593M";
       //-->
      </script>
      <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
	  <?php
	  }
	  ?>
	  </td>
     </tr>
     <tr>
      <td><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=prop_news">Proposer 
        une news</a></font></strong></div></td>
      <td><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=ajouter_lan">Proposer 
        une LAN</a></font></strong></div></td>
      <td><div align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="index.php?page=partenaires#part">Devenir 
        partenaire</a></font></strong></div></td>
     </tr>
    </table>
    <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> </font></strong></td>
  </tr>
 </table>
 <br/>
</div>
<table border="0" cellpadding="0" cellspacing="0" width="532" align="center">
 <tr>
  <td><?
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



if($i==0){ $titre = "$titre"; }
?>
   <script language="JavaScript" type="text/javascript">
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
$print = "<a href=\"Javascript:PrintNews('$id')\"><img src=\"images/imprimante.gif\" border=\"0\" alt=\"Imprimer l'info\" /></a>&nbsp;&nbsp;";
}else{ $print = ""; }

if($ICONE_SEND=='oui'){
$friend = "<a href=\"Javascript:SendNews('$id')\"><img src=\"images/email.gif\" border=\"0\" alt=\"Envoyer cette info à un ami\" /></a>&nbsp;";
}else{ $friend = ""; }


#=-=-=-=-=-=-=-=-=- Gestion de l'image de droite -=-=-=-=-=-=-=-=-=-=-=#
if($image!='non')
{
$IMAGE = "<a href=\"$url_image\" target=\"_blank\">
<img src=\"$path_image\" border=\"0\" width=\"120\" height=\"90\" align=\"right\" alt=\"\" /></a>";
}
else{ $IMAGE = ""; }

?>
   <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
    <tr>
     <td align="center" width="32" valign="top" height="7"><font style="<? echo $DateNews ?>">
      <?
if ($date == $date2now)
	{
	echo "<img src=\"images/new.gif\" align=\"middle\" alt=\"\" />";
	}
?>
      </font></td>
     <td align="right" width="500" rowspan="3"><table border="0" cellpadding="1" cellspacing="0" width="500" bgcolor="#000000">
       <tr>
        <td width="100%"><table border="0" cellpadding="0" cellspacing="0" width="500" bgcolor="white">
          <tr>
           <td width="100%"><table border="0" cellpadding="3" cellspacing="0" width="500">
             <tr style="background-image:url(images/fond.gif);">
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
elseif ($url_source =="admin")
	{
		$ico = "images/icadmi.gif";
	}
	else
		{
		$ico = "images/icoeuf.gif";
	}

?>
              <td bgcolor="<? echo $bgcolor_haut ?>" class="m9" style="background-image:url(images/fond.gif); text-align:left;"><img src="<?=$ico;?>" align="top" alt="<?=$url_source;?>" /><font style="<?=$TitreNews;?>"> <? echo $titre ?></font><font style="<? echo $DateNews ?>"> - <? echo "$date @ $heure $ville"; ?> </font></td>
              <td bgcolor="<? echo $bgcolor_haut ?>" class="m8" style="background-image:url(images/fond.gif);text-align:right;"><?=$print." ".$friend;?></td>
             </tr>
             <tr>
              <td colspan="2" bgcolor="<? echo $bgcolor_corp ?>"><table border="0" cellpadding="0" cellspacing="0" width="500">
                <tr>
                 <td width="100%"><?
								  $tab1=array(
								  "<A href=",
								  "size=1",
								  "size=2",
								  "size=3",
								  "size=4",
								  "Verdana, Arial, Helvetica, sans-serif",
								  "face=Verdana, size=\"2\" sans-serif Helvetica, Arial,",
								  "</A>",
								  "<DIV",
								  "</DIV>",
								  "<IMG",
								  "<BR>",
								  "<FONT",
								  "</FONT>",
								  " target=_blank"
								  );
								  $tab2=array(
								  "<a target=\"_blank\" href=",
								  "size=\"1\"",
								  "size=\"2\"",
								  "size=\"3\"",
								  "size=\"4\"",
								  "\"Verdana, Arial, Helvetica, sans-serif\"",
								  "face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"",
								  "</a>",
								  "<div",
								  "</div>",
								  "<img",
								  "<br/>",
								  "<font",
								  "</font>",
								  ""
								  );
									//$news = str_replace($tab1,$tab2,strip_tags($news,"<a><font><img><br>"));
									//$news = str_replace($tab1,$tab2,$news);
									/*$news = preg_replace("/<img (.*?)+src *= *\"(.*?)\"(.*?).*?\/?>/i", "[IMG=\\2]", $news);
									$news = preg_replace("/\[IMG=(.*?)\]/i", "<img style=\"vertical-align:middle\" border=\"0\" src=\"\\1\" alt=\"\" />", $news);
									*/
									echo "$IMAGE <div style=\"text-align: justify\">$news</div>"; ?>
                 </td>
                </tr>
                <tr>
                 <td width="100%" height="15">&nbsp;</td>
                </tr>
                <tr>
                 <td width="100%" align="right"><?
#=-=-=-=- Comptage des commentaires pour la news -=-=-=-=-=-=-=-=-=-=-=-=-=-#
$caca = "_".$id."_";
//echo "$caca<br/>";
$dodo = @mysql_query("SELECT * FROM ib_topics WHERE pinned='$caca' && (forum_id='1' or forum_id='26')");
$grosse = @mysql_fetch_array($dodo);
$popo = @mysql_num_rows($dodo);
$groscon = $grosse['forum_id'];
$grosseconne = $grosse['tid'];
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
$disdonc = "<a target=\"_blank\" href=\"forum/index.php?act=ST&amp;f=$groscon&amp;t=$grosseconne\" class=\"plein\">$nombre Commentaires</a>";
}
if ($nombre == '1')
{
$disdonc = "<a target=\"_blank\" href=\"forum/index.php?act=ST&amp;f=$groscon&amp;t=$grosseconne\" class=\"plein\">$nombre Commentaire</a>";
}
if ($nombre == '0')
{
$disdonc = "<a target=\"_blank\" href=\"forum/index.php?act=ST&amp;f=$groscon&amp;t=$grosseconne\" class=\"plein\">Aucun Commentaire</a>";
}
if ($grosse['tid'] <= "3")
{
$reqCOMMENT = mysql_query("SELECT id FROM $TBL_COMMENTAIRES WHERE id_news='$id'");
$resCOMMENT = mysql_num_rows($reqCOMMENT);

if($resCOMMENT>='2'){ $disdonc = "<a target=\"_blank\" href=\"index.php?page=read_comment&amp;id_news=$id\" class=\"plein\">$resCOMMENT $CommentsTitle</a>"; }
elseif($resCOMMENT=='1'){ $disdonc = "<a target=\"_blank\" href=\"index.php?page=read_comment&amp;id_news=$id\" class=\"plein\">$UnCommentTitle</a>"; }
else{ $disdonc = "<a target=\"_blank\" href=\"index.php?page=read_comment&amp;id_news=$id\" class=\"plein\">$NoCommentTitle</a>"; }
}
//echo "[$nombreux]";
?>
                  <font style="<? echo $Comment2; ?>"> [ <b> <? echo $InfoTitle; ?> </b> :
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
                  | <? echo $disdonc; ?> ] </font> </td>
                </tr>
               </table></td>
             </tr>
            </table></td>
          </tr>
         </table></td>
       </tr>
      </table>
      <font style="<? echo $DateNews ?>">&nbsp; </font></td>
    </tr>
    <tr>
     <td align="center" width="32" valign="middle" height="25"><font style="<? echo $DateNews ?>">&nbsp; </font></td>
    </tr>
    <tr>
     <td align="right" width="32" valign="top"><font style="<? echo $DateNews ?>">&nbsp; </font></td>
    </tr>
   </table>
   <img src="images/sep_25px_haut.gif" border="0" alt="" />
   <?
 $i++;
 $nombre = 0;
}
?>
  </td>
 </tr>
</table>
<?
$reqARC = MYSQL_QUERY("SELECT DISTINCT date,id FROM $TBL_NEWS ORDER BY id");
$resARC = MYSQL_NUM_ROWS($reqARC);

$MoisDepart = substr(mysql_result($reqARC,0,"date"),3,7);
$ARCHIVES = "[<a href=\"index.php?page=archives&amp;archives=$MoisDepart\">$MoisDepart</a>]\n";

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
    $ARCHIVES = "[<a href=\"index.php?page=archives&amp;archives=$MoisDepart\">$MoisEnCours</a>]\n - $ARCHIVES";
    $i++;
   }
}

?>
<div align="center"></div>
<table border="0" cellspacing="0" width="500" align="center">
 <tr>
  <td width="100%" bgcolor="#000000"><table border="0" cellspacing="0" width="500" cellpadding="3" bgcolor="<? echo $bgcolor_corp ?>">
    <tr>
     <td align="center" valign="top"><font color="#000000" size="2"><b><font face="Verdana, Arial, Helvetica, sans-serif">Archives 
      : <font style="<? echo $CorpsNews; ?>"> <? echo $ARCHIVES; ?> </font></font></b></font><font style="<? echo $CorpsNews; ?>" color="#000000">&nbsp; </font> </td>
    </tr>
   </table></td>
 </tr>
</table>
<br/>
