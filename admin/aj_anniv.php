<?
include "secu.php";?><html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
<!--

function isOK(){

  with(document.the_form){

    if( lien_lien.value == "" ){
      alert("Vous n'avez pas rentré de lien");
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
<form name="the_form" method="post" action="admin/aj_anniv2.php" onSubmit="return isOK()">
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="2" colspan="2"> <div align="left"> 
          <table width="465" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
              <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
            </tr>
            <tr> 
              <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Ajouter 
                un anniversaire=-</font></b></font></td>
            </tr>
            <tr> 
              <td colspan="2">&nbsp;</td>
            </tr>
          </table>
        </div></td>
    </tr>
    <tr> 
      <td height="2" colspan="2"> <div align="CENTER">&nbsp; </div></td>
    </tr>
    <tr> 
      <td height="2" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
        : </font></td>
      <td height="2" width="250"> <input name="nom" type="text" id="nom" size="30"></td>
    </tr>
    <tr> 
      <td width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
        de naissance : 
        <?
	  $jour = date("d");
	  $mois = date("F");
	  $annee = date("Y");
	  
	  $auteur = ucfirst($HTTP_COOKIE_VARS[gen]);
	  ?>
        </font></td>
      <td width="250" valign="top"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"> 
        <input type="text" name="jour" maxlength="2" size="1" value=<? echo $jour; ?>>
        <select name="mois">
          <option value="01"
	<?
if ($mois =="January")
	{
		echo " SELECTED";
	}
?>>Janvier</option>
          <option value="02"
	<?
if ($mois =="February")
	{
		echo " SELECTED";
	}
?>>F&eacute;vrier</option>
          <option value="03"
	<?
if ($mois =="March")
	{
		echo " SELECTED";
	}
?>>Mars</option>
          <option value="04"
	<?
if ($mois =="April")
	{
		echo " SELECTED";
	}
?>>Avril</option>
          <option value="05"
	<?
if ($mois =="May")
	{
		echo " SELECTED";
	}
?>>Mai</option>
          <option value="06"
	<?
if ($mois =="June")
	{
		echo " SELECTED";
	}
?>>Juin</option>
          <option value="07"
	<?
if ($mois =="July")
	{
		echo " SELECTED";
	}
?>>Juillet</option>
          <option value="08"
	<?
if ($mois =="August")
	{
		echo " SELECTED";
	}
?>>Ao&ucirc;t</option>
          <option value="09"
	<?
if ($mois =="September")
	{
		echo " SELECTED";
	}
?>>Septembre</option>
          <option value="10"
	<?
if ($mois =="October")
	{
		echo " SELECTED";
	}
?>>Octobre</option>
          <option value="11"
	<?
if ($mois =="November")
	{
		echo " SELECTED";
	}
?>>Novembre</option>
          <option value="12"
	<?
if ($mois =="December")
	{
		echo " SELECTED";
	}
?>>D&eacute;cembre</option>
        </select>
        <input type="text" name="annee" maxlength="4" size="2" value=<? echo $annee; ?>>
        </font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><b> 
        </b></b></font></strong></font></td>
    </tr>
    <!-- Boutons -->
    <!-- Boutons -->
  </table>
  <div align="center"> <br>
    <input type="submit" name="Submit" value="Ajouter">
  </div>
</form>
</body>
</html>
