<?
include "secu.php";
require("config.inc.php3");
	  
$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");


if ($mail == "ok")
{
$requete  = "SELECT * FROM equipe";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  

$date = date("d/m/Y"); 
$heure = date("H:i");
$from="From:k1der@fr.st\n"; 
$from.="MIME-version: 1.0\n"; 
$from.="Content-type: text/html; charset= iso-8859-1\n"; 
$sujet = 'Mailing du Site -=K1der=-'; 
$message = str_replace("
","<br>",$message);
$message = "<font size='2' face='Verdana, Arial, Helvetica, sans-serif'>$message<br><br>---------------------------------------<br>@+ sur <a target=_blank href=http://www.k1der.net>-=K1der=- The Chocolat Effect</a><br></font>"; 
$message = stripslashes($message);
while($disp = mysql_fetch_array($req))
{
if ($membre[$i] != '')
{
//echo $membre[$i];
//mail ($a,$sujet,$texte,"From:$de");
mail ($membre[$i],$sujet,$message,$from);
}
$i++;
}
echo"<script language=\"Javascript\">alert('Message envoyé avec Succès !'); window.location='index.php?page=admin';</script>";
}
?>
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
<!--

function isOK(){

  with(document.the_form){

    if( message.value == "" ){
      alert("Vous n'avez pas rentré de message");
      return false;
    }
  
  }

  return true;
}

//-->
</script></head>

<body>
<?
$date = date("d/m/Y");
?>
<form name="the_form" method="post" action="index.php?page=mailing&mail=ok" onSubmit="return isOK()">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="2"><div align="center"> 
          <table width="600" border="0" cellpadding="0" cellspacing="0">
            <tr> 
              <td width="442" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="32" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
            </tr>
            <tr> 
              <td width="442" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>Mailing</b>=-</font></b></font></td>
            </tr>
          </table>
        </div></td>
    </tr>
    <tr> 
      <td height="19">&nbsp;</td>
    </tr>
    <tr> 
      <td height="19"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Membres 
        &agrave; Mailer:&nbsp; </font></td>
    </tr>
    <tr> 
      <td height="2"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
          <?
		  $i = 1;
	  $requete  = "SELECT * FROM equipe";
	  $req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());  
	  while($disp = mysql_fetch_array($req))
	  {
	  ?>
          <tr> 
            <td colspan="2"><div align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="membre[<? echo $i; ?>]" type="checkbox" id="membre[<? echo $i; ?>]" value="<? echo $disp[e_mail]; ?>">
                </font></div></td>
            <td width="500"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<? echo $disp[kinder]; ?></font></td>
          </tr>
          <?
	  $i++;
	  }
	  ?>
        </table></td>
    </tr>
    <tr>
      <td height="2">&nbsp;</td>
    </tr>
    <tr> 
      <td height="2"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Message</strong><br>
          <textarea name="message" cols="70" rows="20" id="message"></textarea>
          </font></div></td>
    </tr>
    <!-- Boutons -->
    <!-- Boutons -->
  </table>
  <div align="center"> <br>
    <input type="submit" name="Submit" value="Envoyer">
  </div>
</form>
</body>
</html>
