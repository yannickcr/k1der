<script language="JavaScript">
function ChangeIfUtf8(Utf8InCookies)
{
var URL = document.location.href;
var strUtf8 = "utf8=";
var index = URL.indexOf(strUtf8);
var inCookie = Utf8InCookies;
if(index>0)
{	
var indexValue = index + strUtf8.length;
if (indexValue+1 < URL.length)
{
if (URL.charAt(index-1) == "?")
URL = URL.substring(0,index) + URL.substring(indexValue+2);
else
URL = URL.substring(0,index-1) + URL.substring(indexValue+1);
}
else
{				
URL = URL.substring(0,index-1);
}
}
var IsFirst = URL.indexOf("?");
if (IsFirst>0)
strUtf8 = "&" + strUtf8;
else
strUtf8 = "?" + strUtf8;
if (inCookie=="0" && document.charset=="utf-8")
{
URL = URL + strUtf8 + "1";
if (URL != document.location.href)
{
window.location.replace(URL);
var wHnd = window.open("", "", "height=1,width=1,menubar=no,resizable=no,titlebar=no,scrollbars=no,status=no,toolbar=no,menubar=no,location=no");
wHnd.close();
}
}
else if (inCookie=="1" && document.charset!="utf-8")
{
URL = URL + strUtf8 + "0";
if (URL != document.location.href)
{
window.location.replace(URL);
var wHnd = window.open("", "", "height=1,width=1,menubar=no,resizable=no,titlebar=no,scrollbars=no,status=no,toolbar=no,menubar=no,location=no");
wHnd.close();
}		
}
}
ChangeIfUtf8("0");
</script>
<script language="javascript">
function GAW(w)
{
if ("undefined" != typeof(GAWO))
{
GAWO(w);
}
}
function EAW(richTextFlag)
{
if ("undefined" != typeof(EAWO))
{
EAWO(richTextFlag);
}
}
</script>
<style type="text/css">
<!--
.Style1 {
	color: #CC0000;
	font-weight: bold;
}
-->
</style>

  <iframe name="XformFrame" style="position:absolute;visibility:hidden" WIDTH=0 HEIGHT=0></iframe>
        <form name="hiddentext">
          <input type="hidden" name="sigtext" value="">
          <input type="hidden" name="replytext" value="">
          <input type="hidden" name="drafttext" value="">
        </form>
        <form name="composeform" method="POST" action="admin/ajout_news2.php">
          <input type="hidden" name="curmbox" value="F000000001">
          <input type="hidden" name="HrsTest" value="">
          <input type="hidden" name="_HMaction">
          <input type="hidden" name="FinalDest" value="">
          <input type="hidden" name="subaction">
          <input type="hidden" name="plaintext">
          <input type="hidden" name="login" value="k1dercountry">
          <input type="hidden" name="wcid" value="">
          <input type="hidden" name="soid" value="">
          <input type="hidden" name="msg" value="">
          <input type="hidden" name="start" value="">
          <input type="hidden" name="len" value="">
          <input type="hidden" name="attfile" value="">
          <input type="hidden" name="type" value="">
          <input type="hidden" name="src" value="">
          <input type="hidden" name="ref" value="">
          <input type="hidden" name="ru" value="">
          <input type="hidden" name="wysiwyg" value="">
          <input type="hidden" name="msghdrid" value="4a503d3106ca720681f529fe032e064e_1064944097">
          <input type="hidden" name="RTEbgcolor" value="">
          <input type="hidden" name="sigflag" value="">
          <input type="hidden" name="newmail" value="new">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <TR class="forumline"> 
      <TD class=row1><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><SPAN class=gen><B>Auteur 
        :</B></SPAN></font></TD>
      <TD class=row2><SPAN class=genmed> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?php
		$date = date("d/m/Y");
		$heure = date("H:i");

			$auteur = ucfirst($HTTP_COOKIE_VARS[gen]);
			echo $auteur;
			?>
        <input name="auteur" type="hidden" id="auteur" value="<?php echo $auteur; ?>">
        <input name="date" type="hidden" id="date" value="<?php echo $date; ?>">
        <input name="heure" type="hidden" id="heure" value="<?php echo $heure; ?>">
        </font></SPAN></TD>
      <TD class=row2><span class=genmed><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Date 
        :</strong> <?php echo $date; ?> <strong>&amp;</strong> <strong>Heure :</strong> 
        <?php echo $heure; ?> </font></span></TD>
    </TR>
    <TR class="forumline"> 
      <TD class=row1><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><SPAN class=gen><B>Titre 
        :</B></SPAN></font></TD>
      <TD colspan="2" class=row2><SPAN class=gen>
        <input type="text" name="to" onFocus="setIt('to')" size=30 maxlength=1000 style="width:377px;" tabindex="1" title="À">
        </SPAN></TD>
    </TR>
    <TR class="forumline"> 
      <TD class=row1>&nbsp;</TD>
      <TD colspan="2" class=row2>&nbsp;</TD>
    </TR>
    <TR class="forumline"> 
      <TD class=row1><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Genre 
        :</strong></font></TD>
      <TD colspan="2" class=row2><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="type" type="radio" value="k1d" checked>
        K1der 
        <input type="radio" name="type" value="hl">
        Half-Life 
        <input type="radio" name="type" value="cs">
        Counter-Strike 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input type="radio" name="type" value="lan">
LAN Party</font>
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input type="radio" name="type" value="admin">
        <span class="Style1">Admin</span></font>
        <input name="tools" type="hidden">
        <input type="hidden" name="cc" value="" title="Cc">
        <input type="hidden" name="bcc" value="" title="Cci">
        <input type="hidden" id="subject5" name="subject" value="">
        </font></TD>
    </TR>
  </table>
  <table align="center" cellpadding=0 cellspacing=0>
    <tr> 
                  
            <td colspan=2 align="center"> <div id="cdiv" style='position:relative; left:0px; top:0px; height:314px; width:603px;border:solid 1px #9C9C9C;'> 
          <textarea name="body" wrap="soft" style='visibility:visible; z-index:100; position:absolute; left:0px; top:0px; height:314px; width:603px'></textarea>
          <IFRAME SRC="admin/hot_news.php" id='richedit' style='visibility: hidden; position: absolute; left: 0px; top: 0px; height:314px; width:603px'></IFRAME>
        </div></td>
    </tr>
          </table>
              
        
  <div align="center">
    <input type="submit" name="Send.x" value="  Envoyer  " class="sbttn" onclick="return onSubmitCompose(1,this);">
  </div>
</form>
<script language="JavaScript">
var frm = document.composeform;
function CT()
{
if ( (frm.to.value=="") && (frm.cc.value=="") && (frm.bcc.value=="") )
{
HMError("A","Veuillez+saisir+une+adresse+dans+le+champ+%c0%2e","","");
frm.to.focus();
return false;
}
}
function DT(a){
if (a=="AddOrigonalText")
AOT();
else if (a=="SpellChk"){
onSubmitCompose(1,frm.tools);
frm._HMaction.value='SpellChk';
frm.submit();
}else if (a=="Dictionary"){
onSubmitCompose(1,frm.tools);
frm._HMaction.value='Dictionary';
frm.submit();
}else if (a=="Thesaurus"){
onSubmitCompose(1,frm.tools);
frm._HMaction.value='Thesaurus';
frm.submit();
}else if (a=="RTE"){
if(HMError("C","Pour+changer+la+mise+en+forme+de+ce+message+et+utiliser+un+format+de+texte+brut+%e0+la+place+d%27un+format+de+texte+riche%2c+vous+devez+supprimer+toute+la+mise+en+forme+actuelle%2e%5cnVoulez%2dvous+continuer+%3f","",""))
{
frm._HMaction.value='RTEOFF';
onSubmitCompose(0,frm.tools);
frm.submit();
}
else
{
frm.tools.selectedIndex=0;
}
}
}
function DoSaveMSG(Furl)
{
onSubmitCompose(1,frm.tools);
frm.FinalDestvalue=Furl?Furl:rv.url;
frm._HMaction.value='upsell';
frm.submit();
}
function DOL() {
frm.sigflag.value = '';
document.hiddentext.sigtext.value = '';
}
function SIG()
{
if (frm.sigflag.value && document.hiddentext.sigtext.value.length > 0)
{
if (document.hiddentext.sigtext.value.match(/<html>/)!=null)
{
var _frame = XformFrame.document;
_frame.designMode="on";
_frame.open("text/html","replace");
_frame.write(document.hiddentext.sigtext.value);
_frame.close();
frm.body.value += '\r\n\r\n';
frm.body.value += _frame.body.innerText;
_frame.body.innerHTML="";
}
else
{
frm.body.value = '\r\n\r\n'+document.hiddentext.sigtext.value+'\r\n\r\n'+frm.body.value;
}
}
}
function DRFT()
{
if (document.hiddentext.drafttext.value.match(/<html>/)!=null)
{
var _frame = document.XformFrame.document;
_frame.designMode="on";
_frame.open("text/html","replace");
_frame.write(document.hiddentext.drafttext.value);
_frame.close();
frm.body.value += _frame.body.innerText;
_frame.body.innerHTML="";
}
else
{
frm.body.value += document.hiddentext.drafttext.value;
}
}
function FTF() 
{
iCount = 0;
if (document.activeElement != frm.to) 
{
if (iCount >= 0 && iCount < 10) 
{
frm.to.focus();
iCount++;
}
setTimeout("FTF()",0)
}
}
function window.document.composeform.to.onkeydown() 
{
return SetFocus();
}
function window.document.composeform.cc.onkeydown() 
{
return SetFocus();
}
function window.document.composeform.bcc.onkeydown() 
{
return SetFocus();
}
function window.document.composeform.subject.onkeydown() 
{
return SetFocus();
}
function SetFocus() 
{
if (window.event.keyCode == "13")
return false;
}
function onSubmitCompose(c,el) 
{
if (el.name.indexOf("Send")==0)
{
if (CT()==false)
return false;
}
if (c==1||c==2) 
{	
if (window.richedit.getBGColor() != "")
document.composeform.RTEbgcolor.value = window.richedit.getBGColor();
else if (document.composeform.RTEbgcolor.value  != "")
window.richedit.setBGColor(document.composeform.RTEbgcolor.value)
if (c==1)
{
frm.body.value = "<html>"
+ "<div style='background-color:"  + window.richedit.getBGColor() + "'>"
+ window.richedit.getHTML()
+ "</div></html>"			
}
else
frm.body.value = "<html>" + window.richedit.getHTML() + "</html>"	
}
else
frm.body.value = window.richedit.getText();
frm.plaintext.value = window.richedit.getText();
}
function RTELoaded(w) {	
w.setToolbar("tbmode",true)
w.setToolbar("tbimage",true)
w.setToolbar("tbtable",true)		
w.setSkin("#idToolbar {border: 1px black solid; background:#EEEEEE}")
if (frm.RTEbgcolor.value != "") 
{
w.setBGColor(frm.RTEbgcolor.value);
}
Ued();
FTF();
if (document.composeform && document.composeform.RTEbgcolor.value  != "")
window.richedit.setBGColor(document.composeform.RTEbgcolor.value);
}
function Ued() {
plaintext =   frm.body.value;
if (plaintext.search(/<html>/) == 0) 
window.richedit.setHTML("<DIV>" + plaintext + "</DIV>");
else 
window.richedit.setHTML("<DIV>" + plaintext.replace(/\n/g, "</DIV>"));
frm.body.style.visibility='hidden';
document.all.richedit.style.visibility = 'visible';	
window.richedit.setFocus()
}
var qF = "to";
function setIt(H){qF=H;}
function MIT(qaName){
var qL = frm.elements[qaName].value;
if (frm.elements[qF].value.length == 0 || frm.elements[qF].value.indexOf(qL) == -1) 
{
if (frm.elements[qF].value.length != 0 && frm.elements[qF].value.charAt(frm.elements[qF].value.length - 1) != ",")
frm.elements[qF].value += ",";
frm.elements[qF].value += qL;
}
}
function GAWO(toccbcc){
var url = "/cgi-bin/quiklist?curmbox=F000000001&a=d8b5fb903632268c5c717df06b0590f3&fromcompose=1&where=all";
url += "&to="+escape(frm.to.value)+"&cc="+escape(frm.cc.value)+"&bcc="+escape(frm.bcc.value);
var hWnd = window.open(url,"","width=475,height=345,resizable=yes,status=yes,scrollbars=yes");
if ((document.window != null) && (!hWnd.opener))
hWnd.opener = document.window;
}
function EAWO(richTextFlag){
onSubmitCompose(1,frm.tools);
var url = "/cgi-bin/quiklist?curmbox=F000000001&a=d8b5fb903632268c5c717df06b0590f3&from=compose&richtextyes="+richTextFlag+"&wcid=&soid=";
var qWnd = window.open(url,"","width=475,height=250,resizable=yes,status=yes,scrollbars=yes");
if ((document.window != null) && (!qWnd.opener))
qWnd.opener = document.window;
qWnd.focus();
}
</script>