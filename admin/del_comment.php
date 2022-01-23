<?
include "secu.php";
?><html>
<head>
<meta http-equiv="Content-Language" content="fr">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<style>
<!--
.m10,.m9,.m10b,.m9b {font-family:verdana;}
.m8,.m8b {font-family:verdana;}
.m10,.m10b {font-size:10pt;}
.m9,.m9b {font-size:9pt;}
.m8,.m8b {font-size:8pt;}
.m10b,.m9b,.m8b {font-weight:bold;}

A:link {text-decoration: none; color: #DE0200;}
A:visited {text-decoration: none; color: #DE0200;}
A:active {text-decoration: none; color: #DE0200;}
A:hover {text-decoration: underline; color: red;}
-->
</style>
<?
echo "<title>$titrepage</title>";
?>

<script language="Javascript">
function SuppComment()
{
 if(document.Formulaire.NumComment.value=="")
 {
  alert('Le champ est vide !');
  document.Formulaire.NumComment.focus();
 }
 else if(isNaN(document.Formulaire.NumComment.value))
 {
  alert('Ce numéro de commentaire n\'est pas valide !');
  document.Formulaire.NumComment.select();
 }
 else
 {
  var COM = document.Formulaire.NumComment.value;
  result = confirm('Voulez-vous vraiment supprimer le commentaire n°'+COM+' ?');
  if(result==1)
    {
     document.Formulaire.method = "POST";
     document.Formulaire.action="admin/del_comment2.php";
     document.Formulaire.submit();
    }
    else
    {
     alert('Suppression du commentaire n°'+COM+' annulée !');
    }
 }
}

function SearchComment()
{
 window.open("admin/search_comment.php3","","toolbar=0,location=0,directories=0,menuBar=0,scrollbars=1,resizable=1,width=500,height=400,left=0,top=0");
}
</script>
</head>

<body bgcolor="#EEEEFC">
<div align="center"> <center> 
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td colspan="2" height="27"> <table border="0" cellpadding="0" width="465" class="m10b" cellspacing="0" height="8">
          <tr> 
            <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
            <td width="38" height="42" rowspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF"><img src="images/oeuf2.gif" width="31" height="42"></font></b></font></td>
          </tr>
          <tr> 
            <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Administation 
              des Commentaires=-</font></b></font></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td width="25">&nbsp;</td>
      <td width="475">&nbsp;</td>
    </tr>
    <tr> 
      <td width="25">&nbsp;</td>
      <td width="475"> <form name="Formulaire">
          <div align="center"><font color="#000000"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Supprimer 
            le commentaire n° : </font></b></font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
            <input type="text" name="NumComment" style="width: 80px; font-family: verdana; font-size: 9pt" maxlenght="6">
            <input type="button" value="OK" style="width: 30px; font-family: verdana; font-size: 9pt" onClick="SuppComment();" name="button">
            </b></font></div>
        </form></td>
    </tr>
  </table>
</div>
</body>
</html>