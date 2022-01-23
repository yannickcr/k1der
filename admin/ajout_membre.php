<? $level = "10"; include "secu.php";

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

function joueur($nom)
{
$requete  = "SELECT * FROM equipe WHERE kinder='$nom'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
return $nbre; 
}
?>
<form name="form1" method="post" action="admin/ajout_membre2.php">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
      <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
    </tr>
    <tr> 
      <td width="442" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>Ajouter 
        un Membre</b>=-</font></b></font></td>
    </tr>
  </table>
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="250"> <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
          :</font></div></td>
      <td width="400"> <select name="kinder" id="kinder">
          <?
	  $gaga = joueur("Surprise");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Surprise">Surprise</option>
          <?
	  }
	  $gaga = joueur("Country");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Country">Country</option>
          <?
	  }
	  $gaga = joueur("Maxi Surprise");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Maxi Surprise">Maxi Surprise</option>
          <?
	  }
	  $gaga = joueur("Bueno");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Bueno">Bueno</option>
          <?
	  }
	  $gaga = joueur("Maxi");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Maxi">Maxi</option>
          <?
	  }
	  $gaga = joueur("Circus");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Circus">Circus</option>
          <?
	  }
	  $gaga = joueur("Maxi King");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Maxi King">Maxi King</option>
          <?
	  }
	  $gaga = joueur("Chocolat");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Chocolat">Chocolat</option>
          <?
	  }
	  $gaga = joueur("Noel");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Noel">Noel</option>
          <?
	  }
	 /* $gaga = joueur("Tranche au Lait");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Tranche au Lait">Tranche au Lait</option>
          <?
	  } */
	  $gaga = joueur("Chocobon");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Chocobon">Chocobon</option>
          <?
	  }
	  $gaga = joueur("Pingui");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Pingui">Pingui</option>
          <?
	  }
	  $gaga = joueur("Délice");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="D&eacute;lice">D&eacute;lice</option>
		  <?
	  }
	  $gaga = joueur("Venice");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Venice">Venice</option>
		            <?
	  }
	  $gaga = joueur("Maxi Mix");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Maxi Mix">Maxi Mix</option>
		            <?
	  }
	  $gaga = joueur("Lapin");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Lapin">Lapin</option>
		            <?
	  }
	  $gaga = joueur("Chocofresh");
	  if ($gaga == '0')
	  {
	  ?>
          <option value="Chocofresh">Chocofresh</option>
		            <?
	  }	  ?>
        </select></td>
    </tr>
    <tr>
      <td><div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-Mail 
          :</font></div></td>
      <td><input name="mail" type="text" id="mail"></td>
    </tr>
  </table>
  <div align="center"><br>
    <input type="submit" name="Submit" value="Valider">
  </div>
</form>
