<? include "secu.php"; ?><HTML>
<HEAD>
  <TITLE>Uploader v1.1 - Powered by: (http://www.phpscriptcenter.com/uploader.php)</TITLE>
</HEAD>
<BODY BGCOLOR="#ffffff">
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Ajouter 
      un dessin=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
<!--

Powered by: Uploader Version 1.1 (http://www.phpscriptcenter.com/uploader.php)

-->
<?php

///////////////////////////////////////////////
//                                           //
// Uploader v.1.1                            //
// ----------------------------------------- //
// by Graeme (webmaster@phpscriptcenter.com) //
// http://www.phpscriptcenter.com            //
//                                           //////////////////////////////
// PHP Script CENTER offers no warranties on this script.                //
// The owner/licensee of the script is solely responsible for any        //
// problems caused by installation of the script or use of the script    //
//                                                                       //
// All copyright notices regarding Uploader, must remain                 //
// intact on the scripts and in the HTML for the scripts.                //
//                                                                       //
// (c) Copyright 2001 PHP Script CENTER                                  //
//                                                                       //
// For more info on Uploader,                                            //
// see http://www.phpscriptcenter.com/uploader.php                       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require("setup.php");

if($doupload) {

if($ADMIN[RequirePass] == "Yes") {
if($password != "$ADMIN[Password]") {
?>
<P><CENTER><B><FONT FACE="Verdana">Error</FONT></B></CENTER></P>
<P><CENTER><TABLE WIDTH="450" BORDER="0" CELLSPACING="0"
CELLPADDING="0">
  <TR>
    <TD WIDTH="100%" BGCOLOR="#000000">
    <TABLE WIDTH="450" BORDER="0" CELLSPACING="1" CELLPADDING="2">
      <TR>
        <TD COLSPAN="2" BGCOLOR="#ffffff">
        <FONT COLOR="#000000" SIZE="-1" FACE="Verdana">Invalid Password</FONT></TD>
      </TR>
    </TABLE></TD>
  </TR>
</TABLE>
</CENTER>
<P>&nbsp;</P>
<P>&nbsp;</P>
<P>&nbsp;
</BODY>
</HTML>
<?php
exit();
}
}

$num = 0;
while($num < $ADMIN[UploadNum]) {
$num++;


$picture = "fileup$num"."_name";
$picture1 = $$picture;
$picture2 = "fileup$num";
$picture3 = $$picture2;

if($picture3 != "none") {
$filesizebtyes = filesize($picture3);

$ok = 1;
if($filesizebtyes < 10) {
$error .= "Error uploading (file size lower than 10 bytes) for file $num<BR>";
$ok = 2;
}



if(file_exists("$ADMIN[directory]/$picture1") OR $ok == 2) {
$error .="File name already exists for file $num<BR>";
} else {
copy ($picture3, "$ADMIN[directory]/$picture1");
$error .="<center>L'image à été uploadée avec succès</center><BR>";

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$dir = str_replace("images/dessins/","",$ADMIN[directory]);
$rqt = Mysql_Query("UPDATE config SET valeur='$dir/piti/$picture1' WHERE nom like 'image'");

if(!$rqt)
{
$error .="<BR><center>Ce dessin n'a pas été mis en Dessin de la Semaine (ERREUR)</center><BR>";
}
else
{
$error .="<BR><center>Ce dessin a été mis en Dessin de la Semaine</center><BR>";
}
}
}
}

if(!$error) {
$error .= "No files have been selected for upload";
}


?>

<P><CENTER><TABLE WIDTH="450" BORDER="0" CELLSPACING="0"
CELLPADDING="0">
  <TR>
    <TD WIDTH="100%" BGCOLOR="#000000">
    <TABLE WIDTH="450" BORDER="0" CELLSPACING="0" CELLPADDING="0">
      <TR>
        <TD COLSPAN="2" BGCOLOR="#ffffff">
        <FONT COLOR="#000000" SIZE="-1" FACE="Verdana"><?php echo $error; ?></FONT></TD>
      </TR>
    </TABLE></TD>
  </TR>
</TABLE></CENTER></P>
</BODY>
</HTML>
<?php

} else {

$num = 0;
while($num < $ADMIN[UploadNum]) {
$num++;
$html .= "<TR>
        <TD WIDTH=\"25%\" BGCOLOR=\"#ffffff\">
        <FONT SIZE=\"-1\" FACE=\"Verdana\"><right>Image :</right></FONT></TD> 
        <TD WIDTH=\"75%\" BGCOLOR=\"#ffffff\">
        <INPUT NAME=\"fileup$num\" TYPE=\"file\" SIZE=\"25\">
</TD> ";
}

?>
<FORM ENCTYPE="multipart/form-data" ACTION="index.php?page=dessins_upload" METHOD="POST">

<P><CENTER><TABLE WIDTH="450" BORDER="0" CELLSPACING="0" CELLPADDING="0">
  <TR>
    <TD>
    <TABLE WIDTH="450" BORDER="0" CELLSPACING="0" CELLPADDING="0">
<?php echo $html; ?>
<tr><td><right><FONT SIZE="-1" FACE="Verdana">Catégorie :</font></right></td><td><select name="cat">
<?
$dir = opendir("images/dessins");
while($fichier = readdir($dir))
{
if (($fichier != '.') && ($fichier != '..'))
{
echo "<option value='$fichier'>$fichier</option>";
}
}
closedir($dir);
?>
</select></td></tr>
    </TABLE></TD>
  </TR>
</TABLE>
<br><br><FONT SIZE="1" FACE="Verdana"><FONT color="#CC0000"><b>Attention</b></FONT> : La limite d'upload est de 200ko (Multimerde Powaaa !)<br>donc si ton image fait plus de 200ko tu sera obligé de passer par le FTP.<br> Upload les dans le dossier 'images/dessins' puis la catégorie de ton choix.</FONT></CENTER></P>
<?php
if($ADMIN[RequirePass] == "Yes") {
?>
<P><CENTER><TABLE BORDER="0" CELLSPACING="0"  CELLPADDING="0">
  <TR>
    <TD WIDTH="100%" BGCOLOR="#000000">
    <TABLE WIDTH="300" BORDER="0" CELLSPACING="1" CELLPADDING="2">
      <TR>
        <TD WIDTH="33%" BGCOLOR="#295e85">
        <B><FONT COLOR="#ffffff" SIZE="-1" FACE="Verdana">Password:</FONT></B></TD> 
        <TD WIDTH="67%" BGCOLOR="#ffffff">
        <INPUT NAME="password" TYPE="password" SIZE="25">
</TD> 
      </TR>
    </TABLE></TD>
  </TR>
</TABLE></CENTER></P>
<?php
}
?>
<P><CENTER><INPUT NAME="doupload" TYPE="submit" VALUE="Uploader"></CENTER></FORM>
</BODY>
</HTML>
<?php
}
?>
<center><input type="button" value="Retour à l'admin" onClick="Javascript:window.location='index.php?page=admin'" style="width: 200px"></center>
