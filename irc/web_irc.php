<html>
<head>
<title>-=K1der=- Web IRC</title>
</head>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?
if ($nick == '')
{
?>
<script language="JavaScript">
<!--

function Noprob(){

  with(document.logi){

    if( nick.value == '' ){
      alert("tu n'as pas rentré de pseudo");
      return false;
    }
  
  }

  return true;
}

//-->
</script>
<div class=contenu style="MARGIN-BOTTOM: 0px">
  <div align="center"><strong><font color="#CC0000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
    </font></strong> 
    <form name="form1" method="post" action="" onSubmit="return Noprob()" name="logi">
      <table width="465" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="434" height="22" valign="baseline"> <div align="right"></div></td>
          <td width="31" height="42" rowspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"><b><img src="../images/oeuf2.gif" width="31" height="42" align="absmiddle"></b></font></td>
        </tr>
        <tr> 
          <td width="434" height="20" background="../images/fond.gif"><img src="../images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=<b>Web 
            IRC </b>=-</font></b></font></td>
        </tr>
        <tr> 
          <td colspan="2"><p style="margin-bottom: 0px">&nbsp;</p>
            <div class=contenu style="MARGIN-BOTTOM: 0px"> 
              <div align="center"><strong><font color="#CC0000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Ton 
                Pseudo: </font></strong><font size="2"> 
                <input name=nick>
                <br>
                </font> </div>
            </div>
            <h2 class=news style="MARGIN-TOP: 0.2em; TEXT-ALIGN: center; margin-bottom: 0px;"> 
              <input name="submit" type=submit value=Rejoindre>
            </h2>
            <div class=contenu style="MARGIN-BOTTOM: 0px"> 
              <div align="center"></div>
            </div></td>
        </tr>
      </table>
    </form>
    <strong><font color="#CC0000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
    <br>
    <br>
    </font></strong></div>
</div>
      <?
}
else
{
?>
<h1 align="center"> 
  <applet code=IRCApplet.class archive="irc.jar" width="600" height="500">
    <param name="CABINETS" value="irc.cab,securedirc.cab">
    <param name="host" value="irc.quakenet.org">
    <param name="name" value="Gros Boulay">
    <param name="nick" value="<? echo $nick; ?>">
    <param name="port" value="6667">
    <param name="command1" value="join #k1der">
    <param name="language" value="french">
    <param name="highlight" value="true">
    <param name="helppage" value="http://www.k1der.net">
    <param name="smileys" value="true">
    <param name="highlightnick" value="true">
    <param name="quitmessage" value="Je suis un boulay et je men vais">
    <param name="asv" value="true">
    <param name="aslmale" value="h">
    <param name="aslfemale" value="f">
    <param name="bitmapsmileys" value="true">
    <param name="smiley1" value=":) images/sourire.gif">
    <param name="smiley2" value=":-) images/sourire.gif">
    <param name="smiley3" value=":-D images/content.gif">
    <param name="smiley4" value=":d images/content.gif">
    <param name="smiley5" value=":-O images/OH-2.gif">
    <param name="smiley6" value=":o images/OH-1.gif">
    <param name="smiley7" value=":-P images/langue.gif">
    <param name="smiley8" value=":p images/langue.gif">
    <param name="smiley9" value=";-) images/clin-oeuil.gif">
    <param name="smiley10" value=";) images/clin-oeuil.gif">
    <param name="smiley11" value=":-( images/triste.gif">
    <param name="smiley12" value=":( images/triste.gif">
    <param name="smiley13" value=":-| images/OH-3.gif">
    <param name="smiley14" value=":| images/OH-3.gif">
    <param name="smiley15" value=":'( images/pleure.gif">
    <param name="smiley16" value=":$ images/rouge.gif">
    <param name="smiley17" value=":-$ images/rouge.gif">
    <param name="smiley18" value="(H) images/cool.gif">
    <param name="smiley19" value="(h) images/cool.gif">
    <param name="smiley20" value=":-@ images/enerve1.gif">
    <param name="smiley21" value=":@ images/enerve2.gif">
    <param name="smiley22" value=":-S images/roll-eyes.gif">
    <param name="smiley23" value=":s images/roll-eyes.gif">
  </applet>
</h1>
<?
}
?>
</body>
</html>
<XML style=display:none>