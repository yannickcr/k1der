<? $level = "10"; include "secu.php";

require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$ryq = MYSQL_QUERY("SELECT * FROM equipe ORDER by kinder");
$res = MYSQL_NUM_ROWS($ryq);
$nbre =mysql_num_rows($ryq);

?>
<?
function count_files($dir)
{
    $num = 0;
    
    $dir_handle = opendir($dir);
    while($entry = readdir($dir_handle))
        if(is_file($dir.'/'.$entry))
            $num++;
    closedir($dir_handle);

    return $num;
}   
?>
<div align="center">
  <center>
    <div align="left"></div>
      <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td> <div align="left"><font color="#FFFF00" size="5" face="Minnie"> </font> 
            <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr> 
                <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
                <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
              </tr>
              <tr> 
                  
                <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
                  Voir les Levels des membre=-</font></b></font></td>
              </tr>
              <tr> 
                <td colspan="2">&nbsp;</td>
              </tr>
            </table>
          </div></td>
      </tr>
      <tr> 
        <td>&nbsp;</td>
      </tr>
      <tr> 
        <td width="25"> <table border="0" cellpadding="4" width="600" cellspacing="0">
            <tr valign="bottom"> 
              <td colspan="4" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font class="m10"><? echo "Total: $nbre"; ?></font></b></font></td>
            </tr>
            <tr valign="bottom"> 
              <td align="center">&nbsp;</td>
              <td width="200" align="center">&nbsp;</td>
              <td width="300" colspan="2" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Level</strong></font></td>
            </tr>
            <?
while($dysp = mysql_fetch_array($ryq))
{
?>
            <tr> 
              <td class="m9" width="50"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
              <td class="m9" width="200"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><? echo $dysp[kinder]; ?></b></font> 
              </td>
              <td width="150" align="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?
			  $lepeudo = $dysp[kinder];
			  echo $lepeudo;
			  $req  = mysql_query("SELECT * FROM mynewsinfos WHERE signature='$lepeudo'");
			  $nbre_news =mysql_num_rows($req);
			  $rooq  = mysql_query("SELECT * FROM ib_members WHERE name='$lepeudo'");
			  $doosp = mysql_fetch_array($rooq);
			  $nbre_forum = $doosp[posts];
			  if ($lepeudo == 'Surprise')
			  {
			  $req  = mysql_query("SELECT * FROM shoutbox WHERE pseudo='$lepeudo' or pseudo='SurPriseS' or pseudo='SurPr][seS'");
			  }
			  else
			  {
			  $req  = mysql_query("SELECT * FROM shoutbox WHERE pseudo='$lepeudo'");
			  }
			  $nbre_chat =mysql_num_rows($req);
			  $req  = mysql_query("SELECT * FROM lan_party WHERE joueurs like '%;$lepeudo;%'");
			  $nbre_lan =mysql_num_rows($req);
			  $req  = mysql_query("SELECT * FROM dossiers WHERE auteur='$lepeudo'");
			  $nbre_dossier =mysql_num_rows($req);
			  
			  $req  = mysql_query("SELECT * FROM matches WHERE jou_k1='$lepeudo'");
			  $nbre_matches1 =mysql_num_rows($req);
			  $req  = mysql_query("SELECT * FROM matches WHERE jou_k2='$lepeudo'");
			  $nbre_matches2 =mysql_num_rows($req);
			  $req  = mysql_query("SELECT * FROM matches WHERE jou_k3='$lepeudo'");
			  $nbre_matches3 =mysql_num_rows($req);
			  $req  = mysql_query("SELECT * FROM matches WHERE jou_k4='$lepeudo'");
			  $nbre_matches4 =mysql_num_rows($req);
			  $req  = mysql_query("SELECT * FROM matches WHERE jou_k5='$lepeudo'");
			  $nbre_matches5 =mysql_num_rows($req);

			  $nbre_matches = $nbre_matches1 + $nbre_matches2 + $nbre_matches3 + $nbre_matches4 + $nbre_matches5;

			  if ($lepeudo == "Maxi")
			  {
			  $nbre_des1 =  count_files("images/dessins/BD"); 
			  $nbre_des2 =  count_files("images/dessins/Dessins Divers"); 
			  $nbre_des3 =  count_files("images/dessins/Techniques");
			  $nbre_des = $nbre_des1 + $nbre_des2 + $nbre_des3;
			  
			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='11'");
			  $nbre_flash =mysql_num_rows($req);

			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='12'");
			  $nbre_wall =mysql_num_rows($req);

			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='13'");
			  $nbre_theme =mysql_num_rows($req);
			  
			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='14'");
			  $nbre_div =mysql_num_rows($req);
			  
			  $req  = mysql_query("SELECT * FROM liens_down WHERE cat='24'");
			  $nbre_skin =mysql_num_rows($req);
			  
			  }

			  $level = $nbre_news*3 + $nbre_forum*2 + $nbre_chat*1 + $nbre_dossier*10 + $nbre_lan*25 + $nbre_matches*5 + $nbre_des*20 + $nbre_wall*5 + $nbre_flash*80 + $nbre_theme*5 + $nbre_div*5 + $nbre_skin*5;
			  
			  mysql_query("UPDATE equipe SET klevel='$level' WHERE kinder='$HTTP_COOKIE_VARS[gen]'");

			  $level2 = $level;

			  
			$dur = date2timestamp($dysp[date],"Ymd");
			$jour = date("d",$dur);
			$mois = date("m",$dur);
			$an = date("Y",$dur);
			$jour2 = date("d");
			$mois2 = date("m");
			$an2 = date("Y");
			$dur = diff_date($jour2 , $mois2 , $an2 , $jour , $mois , $an);
			//echo "$jour/$mois/$an ($dur jours)";			
            if ($level != 0)
			{
			$part = $dur/$level;
			}
			else
			{
			$part = $dur;
			}
			?>
                </font></td>
              <td width="250" align="center"> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"><? echo $level2; ?><br>
                <?
			if ($part > 3)
			{
			echo "Tu est comme le H de Hawaï";
			}
			else if ($part > 2)
			{
			echo "Tu fout pas grand chose quand même";
			}
			else if ($part > 1)
			{
			echo "Sa serai bien de participer";
			}
			else if ($part > 1.5)
			{
			echo "Ya des efforts de fait :)";
			}
			else if ($part > 1)
			{
			echo "Tu participe, mais pas trop";
			}
			else if ($part > 0.5)
			{
			echo "T'es asser actif, c'est bien";
			}
			else if ($part > 0.3)
			{
			echo "T'est un bon membre, bien actif";
			}
			else if ($part > 0.2)
			{
			echo "Oua ! Toi t'est hyper actif !";
			}
			else
			{
			echo "Ta vie, ton clan";
			}
			?>
                </font></td>
            </tr>
            <?
}
?>
          </table></td>
      </tr>
    </table>
      </center>
<br>
<form>
<input type="button" value="Retour à la page d'administration" onClick="Javascript:window.location='index.php?page=admin';">
</form>
</div>