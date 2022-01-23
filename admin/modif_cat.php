<?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$req = MYSQL_QUERY("SELECT * FROM admin_cat WHERE id='$id'");

$disp = mysql_fetch_array($req);
?>
<title></title>
<body>
<div align="center">
              
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td width="475"> <table border="0" cellpadding="4" cellspacing="0" width="490">
          <tr> 
            <td nowrap> <form method="POST" action="admin/modif_cat2.php">
                <p> 
                  <input type="hidden" value="<? echo $disp[id]; ?>" name="id">
                </p>
                <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr> 
                    <td height="2" width="250"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nom 
                      : </font></td>
                    <td height="2" width="250"> <input name="nom" type="text" id="nom" value="<? echo $disp[nom]; ?>" size="30"> 
                    </td>
                  </tr>
                  <!-- Boutons -->
                  <!-- Boutons -->
                </table>
                <p align="center"> 
                  <input type="submit" name="envoi" value="Valider les modifications ...">
                </p>
                </form></td>
          </tr>
        </table></td>
    </tr>
  </table>
              
</div>

