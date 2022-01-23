<?

function DirTri($rep,$tri)
{
  $Array = array(); $dir = opendir($rep);
  $i=0;
  while ($File = readdir($dir)){
    if($File != "." && $File != ".." && $File != "index.htm")
    {
      $Array[] = "$File";
    }
    $i++;
  }
  closedir($dir);

  if($tri == 'DESC'){
    rsort($Array);
  }else{
    sort($Array);
  }
  $Max = count($Array);
  $texto = "Fichiers associés:";
  for($i = 0; $i != $Max; $i++){
  echo "<td valign='top' nowrap><font size='2' face='Verdana, Arial, Helvetica, sans-serif'>$texto&nbsp;</font></td><td width='80%' colspan='-1' valign='top'><font size='2' face='Verdana, Arial, Helvetica, sans-serif'><a href=\"$rep"."$Array[$i]\">$Array[$i]</a></font></td></tr><tr>";
  $texto = "";
  }
  //echo "<br><br>".$Max." fichier(s)" ;
}

// utilisation de la fonction
// param.1 : chemin du répertoire ("." si il s'agit du rép. courant)
// param.2 : ASC ou DESC (A-Z ou Z-A)

  require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM lan_party WHERE id='$la_lan'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$disp = mysql_fetch_array($req);
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Details 
      de la LAN=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
      : <b><b><? echo $disp[nom]; ?></b></b></font></td>
  </tr>
  <tr> 
    <td width="50%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
      :<b><b> <? echo "$disp[jour] $disp[mois] $disp[annee]"; ?></b></b></font></td>
    <td width="50%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      Nombre de photos : <b>
      <?
	$rep_lan = "./lan";
$rep_lan_photos = "photos";
$repertoire = opendir($rep_lan);
while($nom_lan = readdir($repertoire)) {					//lecture du rep pour savoir les lans
  if(($nom_lan != ".") && ($nom_lan != "..")){
    if(is_dir($rep_lan."/".$nom_lan)) {						// comptage du nom de photos de la lan
       if(is_dir($rep_lan."/".$nom_lan."/".$rep_lan_photos)) {			// s'il existe lan/westarena/photos
        $rep_a_photos = @opendir($rep_lan."/".$nom_lan."/".$rep_lan_photos);	// alors on y est
        $nbr_photos = 0;							// nombre par defaut des images
        while($les_photos = @readdir($rep_a_photos)) {				// lecture du rep
          $Extensions = explode(".",$les_photos);
          $nombrE_ext = count($Extensions);
          $Extension = strtolower($Extensions[$nombrE_ext-1]);
          if ( ( ($les_photos != ".") || ($les_photos != "..") ) && (!is_dir($les_photos)) && ($Extension == "jpg") ) {
          									// on compte le nombre de photos
            $nbr_photos++;
          }
        }
		
		if ($nom_lan == $la_lan)
		{
		if ($nbr_photos != '0')
		{
		?>
		<a href="index.php?page=lan_photos&la_lan=<? echo $la_lan; ?>&nbr_photos=<? echo $nbr_photos; ?>&nbr=21&start=0"><? echo $nbr_photos; ?></a> 
		<?
		}
		else
		{
		echo "Aucune";
		}
		}
		}
		}
		}
		}
		?>
      </b> </font></td>
  </tr>
  <tr> 
    <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      Ville : <b><? echo $disp[loc]; ?></b></font></td>
  </tr>
  <tr>
    <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Site 
      Internet : <b><a href="<? echo $disp[url]; ?>" target="_blank"><? echo $disp[url]; ?></a></b></font></td>
  </tr>
</table>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <?php
// ouvrir le répertoire
$dir = opendir("lan/$disp[id]/fichiers");
// parcourir le répertoire en lisant le nom d'un fichier
// à chaque itération
?>
  <tr> 
    <td colspan="2" valign="top">&nbsp;</td>
  </tr>
  <tr> 
    <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Joueurs 
      &agrave; cette LAN :</font></td>
    <td colspan="-1" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?
	  $joueurs = str_replace(";"," ",$disp[joueurs]);
	  $joueurs = trim($joueurs);
	  $joueurs = str_replace(" ",", ",$joueurs);
	  echo $joueurs; ?>
      </font></td>
  </tr>
  <tr>
    <td valign="top" nowrap>&nbsp;</td>
    <td colspan="-1" valign="top">&nbsp;</td>
  </tr>
  <?
  $requete2  = "SELECT * FROM matches WHERE loc='$disp[nom]' ORDER BY id";
  $req2 = mysql_query($requete2) or die('Erreur SQL !<br>'.$requete2.'<br>'.mysql_error());  
  $nbre2 =mysql_num_rows($req2);
  
  if ($nbre2 != 0 )
  {
  ?>
  
  <tr> 
    <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Matches 
      de cette LAN : </font></td>
    <td colspan="-1" valign="top"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?
	  while($disp2 = mysql_fetch_array($req2))
	  {
	  echo "<a href='index.php?page=matches_details&id=$disp2[id]'><font color='#000000'>-=K<font color='#CC0000'>1der</font>=- vs $disp2[mechants]</a><br>";
	  }
	  ?>
      </font></strong></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
  </tr>
  <tr> 
    <? DirTri("lan/$disp[id]/fichiers/","ASC"); ?>
  </tr>
  <tr> 
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font></td>
    <td width="73%" colspan="-1" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font> </td>
  </tr>
</table>
<br>
<?
$caca = "_".$la_lan."_";
$dodo = mysql_query("SELECT * FROM ib_topics WHERE pinned='$caca' && forum_id='4'");
$grosse = mysql_fetch_array($dodo);
$popo = mysql_num_rows($dodo);
$grosseconne = $grosse['tid'];
$dada = mysql_query("SELECT * FROM ib_posts WHERE topic_id='$grosseconne'");
$nombre = mysql_num_rows($dada);
$nombre = $nombre-1;
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
if ($grosse['tid'] <= "3")
{
$reqCOMMENT = mysql_query("SELECT * FROM lan_partycomments WHERE id_lan='$disp[id]' ORDER BY id DESC");
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
          <div align="center"><a href="Javascript:Comments('ajouter_comm_lan.php?id_lan=<? echo $disp[id]; ?>')"> 
            <font style="<? echo $TitreNews; ?>">Ajouter un commentaire</font> 
            </a></div>
        </td>
      </tr>
    </table>
    <br>
    <br>
    
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#000000">
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
              <td width="100%" align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Aucun commentaires</b></font></td>
            </tr>
<?
}
?>
          </table>
        </div>
      </td>
    </tr>
  </table>
<?
}
else
{
?>
<table width="600" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
    <td><div align="right"><font color=#FFFFFF size=2 face='Verdana, Arial, Helvetica, sans-serif'><? echo $disdonc; ?></font></div></td>
  </tr>
</table>
<?
}
?>
