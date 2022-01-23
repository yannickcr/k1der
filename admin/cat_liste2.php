<?
include "secu.php";
?><head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<style>
<!--
.m10,.m9,.m10b,.m9b {font-family:verdana;}
.m8,.m8b            {font-family:verdana;}
.m10,.m10b          {font-size:10pt;}
.m9,.m9b            {font-size:9pt;}
.m8,.m8b            {font-size:8pt;}
.m10b,.m9b,.m8b     {font-weight:bold;}

A:link    {text-decoration: none; color: #DE0200;}
A:visited {text-decoration: none; color: #DE0200;}
A:active  {text-decoration: none; color: #DE0200;}
A:hover   {text-decoration: underline; color: red;}
-->
</style>

<title>ADMIN - MyNEWS v1.2</title>

<script language="Javascript">
function Modifier(data)
	{
	window.open("admin/modif_cat_dir.php?nom="+data,"Modifier","toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=0,height=200,width=500")
//	window.open'admin/modifier.php3?id='+data;
}


function Supprimer(data)
{
 resultat = confirm('Voulez-vous vraiment supprimer la catégorie '+data+'  ?');
 if(resultat==1)
 {
  window.location='admin/suppr_cat2.php?cat='+data;
 }
 else
 {
  alert('Suppression annulée !');
 }
}
</script>


</head>

<body bgcolor="#EEEEFC">

<div align="center">
  <center>
    <div align="left"></div>
    <table width="500" border="0" cellspacing="0" cellpadding="0">
      <tr> 
        <td colspan="2"> <div align="center"><font color="#FFFF00" size="5" face="Minnie"> 
            <?
		if ($action == 'modif')
		{
		echo 'Modifier';
		}
		else
		{
		echo 'Supprimer';
		}
		?>
            une categorie</font></div></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr> 
        <td width="25">&nbsp;</td>
        <td width="25"> <table border="0" cellpadding="4" width="475" cellspacing="0" height="40">
            <tr valign="bottom"> 
              <td colspan="2" align="center"></td>
            </tr>
            <tr> 
              <td width="460" align="center" class="m9"><font color="#FFFFFF" size="4" face="Minnie">Categories</font></td>
              <td align="center" class="m9"><font color="#FFFFFF" size="4" face="Minnie">Action</font></td>
            </tr>
            <tr> 
              <td class="m9" width="460"> <font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">
			  <?
			  $dir = opendir("images/gallerie");
			  // parcourir le répertoire en lisant le nom d'un fichier
			  // à chaque itération
			  while($fichier = readdir($dir)) {
			  if ($fichier != '.')
			  {
			  if ($fichier != '..')
			  {
			  echo "<font color=\"#FFFFFF\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$fichier</font>";
			  if ($action == 'modif')
			  {
			  echo "</td><td><b><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><a class=type1 href=\"Javascript:Modifier('$fichier');\">Modifier</a></font></b>";
			  }
			  else
			  {
			  echo "</td><td><b><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><a class=type1 href=\"Javascript:Supprimer('$fichier');\">Supprimer</a></font></b>";
			  }
			  }
			  }
			  }
			  // ferme le répertoire
			  closedir($dir); 
			  ?>
			  </td>
            </tr>
          </table></td>
      </tr>
    </table>
  </center>
<br>
<form>
<input type="button" value="Retour à la page d'administration" onClick="Javascript:window.location='index.php?page=admin';">
</form>
</div>
</body>
</html>