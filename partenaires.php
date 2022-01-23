<?
if ($act == 'go')
{
$requete  = "INSERT INTO liens VALUES('','$nom2','$lien','$image','$mail','0')";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error()); 
 
?>
<script language="Javascript">
alert('Lien ajouté avec succès !\nLe lien apparaitera dès qu\'il aura été confirmé par un administrateur.\n\nUn E-mail contenant le code à insérer dans votre page vous sera envoyé quand le lien aura été confirmé.');
</script>
<?
}
?>
<script language="JavaScript">
<!--

function part(){

  with(document.partenaires){

    if( nom.value == '' ){
      alert("Vous n'avez pas rentré de nom de site");
      return false;
    }
    if( lien.value == '' ){
      alert("Vous n'avez pas rentré de lien");
      return false;
    }
	if( lien.value == 'http://' ){
      alert("Vous n'avez pas rentré de lien");
      return false;
    }
    if( mail.value == '' ){
      alert("Vous n'avez pas rentré d'e-mail");
      return false;
    }
  
  }

  return true;
}

//-->
</script>
<table width="590" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
  </tr>
  <tr> 
    <td width="442" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-= 
      Partenaires =-</font></b></font></td>
  </tr>
</table>
<br>
  <?
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$requete  = "SELECT * FROM liens WHERE conf = '1' ORDER BY id";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
$nbre =mysql_num_rows($req);
$i = 0;
?>

<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>
	<?
	while($disp = mysql_fetch_array($req))
	{
	if ($disp[image] != "")
	{
	if(ereg(".swf",$disp[image]))
	{
	?>
	<div align="center"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="88" height="31">
	<param name="movie" value="<? echo $disp[image]; ?>">
	<param name="quality" value="high">
	<embed src="<? echo $disp[image]; ?>" width="88" height="31" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed></object></div><br><br>
	<?
	}
	else
	{
	?>
	<div align="center"><a href="<? echo $disp[lien]; ?>" target="_blank"><img src="<? echo $disp[image]; ?>" border="0" alt="<? echo $disp[nom]; ?>"></a></div><br><br>
	<?
	}
	}
	else
	{
	?>
	<div align="center"><a class=type2 href="<? echo $disp[lien]; ?>" target="_blank"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? echo $disp[nom]; ?></font></a></div><br><br>
	<?
	}
	if ($i == '3')
	{
	?>
	</td></tr><tr><td>
	<?
	$i = 0;
	}
	else
	{
	?>
	</td><td>
	<?
	$i++;
	}
	}
	?>
	</td>
  </tr>
</table>
<form name="partenaires" method="post" action="index.php?page=partenaires&act=go" onSubmit="return part()">
  <a name="part"></a> 
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td colspan="2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pour 
        faire parti de nos partenaires:</font></strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom du site 
        : </font></td>
      <td width="350"><font color="#FFFFFF"> 
        <input name="nom2" type="text" id="nom2" maxlength="200" >
        </font></td>
    </tr>
    <tr> 
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lien du 
        site : </font></td>
      <td width="350"><font color="#FFFFFF"> 
        <input name="lien" type="text" value="http://" size="50" >
        </font></td>
    </tr>
    <tr> 
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Image 
        du Lien : </font></td>
      <td width="350"><font color="#FFFFFF"> 
        <input name="image" type="text" size="50" >
        </font></td>
    </tr>
    <tr> 
      <td colspan="2"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">Taille 
        par d&eacute;faut: 88x31 Taille max: 100x50</font></td>
    </tr>
    <tr> 
      <td colspan="2"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">Laissez 
        vide si vous n'avez pas d'image</font></td>
    </tr>
    <tr> 
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-Mail : 
        </font></td>
      <td width="350"><font color="#FFFFFF"> 
        <input type="text" name="mail" >
        </font></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><div align="justify"><font color="#FFFFFF" size="1" face="Verdana, Arial, Helvetica, sans-serif"><font color="#cc0000"><b>ATTENTION</b></font></font><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
          : Pour que votre site apparaisse dans nos partenaires vous devrez faire 
          apparaitre un lien vers www.k1der.net sur votre site.</font><font color="#FFFFFF" size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
          </font><font size="1" face="Verdana, Arial, Helvetica, sans-serif">Le 
          code &agrave; ins&eacute;rer vous sera envoy&eacute; par e-mail, vérifiez 
          donc bien que vous avez tapé une adresse e-mail valide.</font></div></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><div align="center"> 
          <input type="submit" name="Submit" value="Valider" >
        </div></td>
    </tr>
  </table>
</form>
