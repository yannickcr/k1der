<?
include "secu.php";
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM defi WHERE id=$id";
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
      du d&eacute;fi lanc&eacute; par les<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
      <? echo $disp[mechants]; ?></b></font>=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;Equipe 
      &agrave; affronter : <? echo "<b>".$disp[clan]."</b> (Leader: <b>".$disp[leader]."</b>)"; ?></font></td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;D&eacute;fi 
      lanc&eacute; par : <b><? echo $disp[pseudo]; ?></b></font></td>
  </tr>
  <?
  if ($disp[irc] != "")
  {
  ?>
  <?
  }
  if ($disp[msn] != "")
  {
  ?>
  <?
  }
  ?>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;pour 
      le : <b> 
      <?
	  $ladate = date2timestamp("$disp[annee]$disp[mois]$disp[jour]$disp[heure]$disp[minute]","YmdHi");
  
 $jour=date("l",$ladate); 
  $mois=date("m",$ladate); 
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
  "01 "=>"Janvier" , 
  "02" =>"Février" , 
  "03" =>"Mars" , 
  "04" =>"Avril" , 
  "05" =>"Mai" , 
  "06" =>"Juin" , 
  "07" =>"Juillet" , 
  "08" =>"Août" , 
  "09" =>"Septembre" , 
  "10" =>"Octobre" , 
  "11" =>"Novembre" , 
  "12" =>"Décembre" 
  ); 
  
  foreach($liste_jour_fr as $j_en => $j_fr ) 
  { 
  if($jour==$j_en) 
  { 
  $datefran .= $j_fr; 
  } 
  } 
  
  $datefran .= date(" d",$ladate); 
  
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
      </b> &agrave;<b> <? echo $disp[heure].":".$disp[minute]; ?></b></font></td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
    </td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;Occasion 
      :<strong>Matche</strong></font></td>
  </tr>
  <tr> 
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;Server 
      : <b><? echo $disp[server]; ?></b></font></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Contact</strong></font></td>
  </tr>
  <tr>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;E-mail : <b><a href="mailto:<? echo $disp[mail]; ?>"><? echo $disp[mail]; ?></a></b></font></td>
  </tr>
  <?
  if ($disp[irc] != "")
  {
  ?>
  <tr>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;IRC : <b><a href="irc://quakenet.org/<? echo $disp[irc]; ?>">#<? echo $disp[irc]; ?></a></b></font></td>
  </tr>
  <?
  }
  if ($disp[msn] != "")
  {
  ?>
  <tr>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;MSN : <b><? echo $disp[msn]; ?></b></font></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td colspan="2" > <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font> <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td colspan="3">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="3"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Leur 
              Carte <br>
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
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font></td>
  </tr>
  <?
  if ($disp[comm] != '')
  {
  ?>
  <tr> 
    <td colspan="2" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;Commentaires 
      :</font></td>
  </tr>
  <tr> 
    <td colspan="2" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;<? echo $disp[comm]; ?></font></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td >&nbsp;</td>
    <td >&nbsp;</td>
  </tr>
  <tr> 
    <td width="50%" > <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="admin/defi_ok.php?id=<? echo $disp[id]; ?>">Accepter 
        le match</a></font></div></td>
    <td width="50%" > <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="admin/defi_no.php?id=<? echo $disp[id]; ?>">Refuser 
        le match</a></font></div></td>
  </tr>
</table>
