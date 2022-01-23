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
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Uploader 
      sa config=-</font></b></font></td>
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

require("setup2.php");

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



copy ($picture3, "$ADMIN[directory]/$picture1");

$user = ucwords($HTTP_COOKIE_VARS[gen]);
$user = str_replace(" ","_",$user);
$user = str_replace("é","e",$user);
$user = strtolower($user);
$tablo = pathinfo($picture1) ;
$ext = $tablo['extension'] ;

if(file_exists("$ADMIN[directory]/".$user."_conf.zip") OR $ok == 2)
{
unlink("$ADMIN[directory]/".$user."_conf.zip");
}
if(file_exists("$ADMIN[directory]/".$user."_conf.rar") OR $ok == 2)
{
unlink("$ADMIN[directory]/".$user."_conf.rar");
}
if(file_exists("$ADMIN[directory]/".$user."_conf.cab") OR $ok == 2)
{
unlink("$ADMIN[directory]/".$user."_conf.cab");
}
if(file_exists("$ADMIN[directory]/".$user."_conf.cfg") OR $ok == 2)
{
unlink("$ADMIN[directory]/".$user."_conf.cfg");
}
if(file_exists("$ADMIN[directory]/".$user."_conf.txt") OR $ok == 2)
{
unlink("$ADMIN[directory]/".$user."_conf.txt");
}
if(file_exists("$ADMIN[directory]/".$user."_conf.exe") OR $ok == 2)
{
unlink("$ADMIN[directory]/".$user."_conf.exe");
}

rename("$ADMIN[directory]/$picture1","$ADMIN[directory]/".$user."_conf.".$ext);


$error .="<center>Le fichier à été uploadée avec succès</center><BR>";

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");

$dir = str_replace("images/dessins/","",$ADMIN[directory]);

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
        <FONT SIZE=\"-1\" FACE=\"Verdana\"><right>Fichier :</right></FONT></TD> 
        <TD WIDTH=\"75%\" BGCOLOR=\"#ffffff\">
        <INPUT NAME=\"fileup$num\" TYPE=\"file\" SIZE=\"25\">
</TD> ";
}

?>
<FORM ENCTYPE="multipart/form-data" ACTION="index.php?page=config_upload" METHOD="POST">

<P><CENTER><TABLE WIDTH="450" BORDER="0" CELLSPACING="0" CELLPADDING="0">
  <TR>
    <TD>
    <TABLE WIDTH="450" BORDER="0" CELLSPACING="0" CELLPADDING="0">
<?php echo $html; ?>
    </TABLE></TD>
  </TR>
</TABLE>
<br><br><FONT SIZE="1" FACE="Verdana"><FONT color="#CC0000"><b>Attention</b></FONT> : La limite d'upload est de 800ko<br>Mais bon si ta config fais plus de 800ko...bin enlève l'ogc.</FONT></CENTER></P>
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
