<?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM next_matches WHERE id=$id";
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
      du prochain match : <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>K1der</b> 
      vs <b><? echo $disp[mechants]; ?></b></font>=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;Equipe 
      &agrave; affronter : <? echo "<b>".$disp[mechants]."</b> (Leader: <b>".$disp[leader]."</b>)"; ?><b></b></font></td>
  </tr>
  <tr>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;D&eacute;fi lanc&eacute; par : <b><? echo $disp[pseudo]; ?></b></font></td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;le 
      : <b> 
      <?
						  
 $jour=date("l",$disp[date]); 
  $mois=date("m",$disp[date]); 
  $liste_jour_fr=array( 
  "Monday"=>"Lundi " , 
  "Tuesday"=>"Mardi" , 
  "Wednesday"=>"Mercredi" , 
  "Thursday"=>"Jeudi" , 
  "Friday"=>"Vendredi" , 
  "Saturday"=>"Samedi" , 
  "Sunday"=>"Dimanche" 
  ); 
  
  $liste_mois_fr=array( 
  "01 "=>"janvier" , 
  "02" =>"février" , 
  "03" =>"mars" , 
  "04" =>"avril" , 
  "05" =>"mai" , 
  "06" =>"juin" , 
  "07" =>"juillet" , 
  "08" =>"août" , 
  "09" =>"septembre" , 
  "10" =>"octobre" , 
  "11" =>"novembre" , 
  "12" =>"décembre" 
  ); 
  
  foreach($liste_jour_fr as $j_en => $j_fr ) 
  { 
  if($jour==$j_en) 
  { 
  $datefran .= $j_fr; 
  } 
  } 
  
  $datefran .= date(" d",$disp[date]); 
  
  foreach($liste_mois_fr as $m_en => $m_fr ) 
  { 
  if($mois==$m_en) 
  { 
  $datefran .= " $m_fr"; 
  } 
  } 
  //$datefren .= date(" Y");
  //return $datefren; 
						  
						  //$dute = date_en_francais();
						  echo $datefran;
						  	  ?>
      </b> &agrave;<b> <? echo $disp[heure]; ?></b></font></td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
    </td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;Occasion 
      :<b> <? echo $disp[occ]; ?></b></font></td>
  </tr>
  <tr>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;Server : <b><? echo $disp[server]; ?></b></font></td>
  </tr>
  <tr>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
  </tr>
  <?
  if ($HTTP_COOKIE_VARS[gen] != '')
  {
  ?>	
  <tr>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Contact</strong></font></td>
  </tr>
  <tr>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;E-mail : <b><a href="mailto:<? echo $disp[mail]; ?>"><? echo $disp[mail]; ?></a></b></font></td>
  </tr>
  <?
  if ($disp[irc] != "")
  {
  ?>
  <tr>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;IRC : <b><a href="irc://quakenet.org/<? echo $disp[irc]; ?>">#<? echo $disp[irc]; ?></a></b></font></td>
  </tr>
  <?
  }
  if ($disp[msn] != "")
  {
  ?>
  <tr>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;MSN : <b><? echo $disp[msn]; ?></b></font></td>
  </tr>
  <?
  }
  }
  ?>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2" > <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <? if ($disp[map2] != 'Aucune') { ?>
      </font> <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td colspan="4">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="4"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Cartes 
              jou&eacute;es <br>
              <br>
              </strong></font></div></td>
        </tr>
        <tr> 
          <td width="25%"><div align="center"></div></td>
          <td width="25%"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/cartes/<? echo $disp[map]; ?>.jpg" width="109" height="81" border="1"></font></div></td>
          <td width="25%"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/cartes/<? echo $disp[map2]; ?>.jpg" width="109" height="81" border="1"></font></div></td>
          <td width="25%">&nbsp;</td>
        </tr>
        <tr> 
          <td><div align="center"></div></td>
          <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><? echo $disp[map]; ?></strong></font></div></td>
          <td width="25%"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><? echo $disp[map2]; ?></strong></font></div></td>
          <td width="25%">&nbsp;</td>
        </tr>
      </table>
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <? } else { ?>
      </font> <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td colspan="3">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="3"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Carte 
              jou&eacute;e<br>
              <br>
              </strong></font></div></td>
        </tr>
        <tr> 
          <td width="25%"><div align="center"></div></td>
          <td> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/cartes/<? echo $disp[map]; ?>.jpg" width="109" height="81" border="1"></font></div>
            <div align="center"></div></td>
          <td width="25%">&nbsp;</td>
        </tr>
        <tr> 
          <td><div align="center"></div></td>
          <td><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><? echo $disp[map]; ?></strong></font></div>
            <div align="center"></div></td>
          <td width="25%">&nbsp;</td>
        </tr>
      </table>
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <? } ?>
      </font></td>
  </tr>
  <tr> 
    <td colspan="2" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
  </tr>
  <tr> 
    <td colspan="2" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
  </tr>
  <tr> 
    <td colspan="2" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;Line 
      up : 
	  <?
	  if ($disp[joueur1] != '')
	  {
	  echo "<b>$disp[joueur1]</b>, <b>$disp[joueur2]</b>, <b>$disp[joueur3]</b>, <b>$disp[joueur4]</b>, <b>$disp[joueur5]";
	  }
	  else
	  {
	  echo "<font color=#cc0000><b>Inconnue</b></font>";
	  }
	  ?></b></font></td>
  </tr>
  <tr> 
    <td colspan="2" ><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"></font></div></td>
  </tr>
</table>
