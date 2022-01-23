<? include "secu.php"; ?>
<?
$req = MYSQL_QUERY("SELECT * FROM dossiers_p WHERE id_dossier='$id' && page='$idp'");
$disp = mysql_fetch_array($req);
?>

<html>
 
<head>
<SCRIPT LANGUAGE="JavaScript">
<!--
	var agent = navigator.userAgent.toLowerCase();
	var ver = parseInt(navigator.appVersion);

	if ((agent.indexOf("ppc") >= 0) || (agent.indexOf("mac_powerpc") >= 0)) {
		document.write('');
		}
	else {
			if ((agent.indexOf("mozilla") != -1) && (navigator.appName.indexOf("Netscape") != -1)) {
				if (ver >= 5) { 
					document.write(''); }
				else { 
					document.write(''); }
		 	}
			else { document.write(''); }
		 }

function doit (newName,newValue,doPub)
{
	document.webmail.SUB_DUMMY.value = newValue;
	document.webmail.SUB_DUMMY.name = newName;
	document.webmail.target = "_self";
	document.webmail.submit ();
}

function Help() 
{
	var index = "" + document.forms["webmail"].AIDE.value + ".html"
	window.open (index,"Aide","toolbar=no,resizable=yes,scrollbars=yes,width=620,height=362");	  	
}

//-->
</SCRIPT>
<script language="javascript">
<!--
var timerID;

var last_action = '';
var timer_id;

var messageDate;
var navvers = navigator.appVersion.substring(0,1);
if (navvers > 3) navok = true;
else             navok = false;

today = new Date;
jour = today.getDay();
numero = today.getDate();
mois = today.getMonth();
if (navok) annee = today.getFullYear();
else       annee = today.getYear();
TabJour = new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
TabMois = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
messageDate = TabJour[jour] + " " + TabMois[mois] + " " + numero + " " + annee;

function Compo_doit(newName,newValue,doPub) {
	var quiz = parseInt(Math.random()*10000000);	
	var cible = "compo_" + quiz;    
    	document.forms["webmail"].SUB_DUMMY.value = newValue;
    	document.forms["webmail"].SUB_DUMMY.name = newName;
    	document.forms["webmail"].target = "_self";
    	document.forms["webmail"].submit ();
}

function SMS_doit(newName,newValue,doPub) {    
    	window.open('','SMS_IBxiMbQbAKpthSZnjvxFU8qKajNVOiJTASVat7Rawyb','scrollbars=yes,resizable=yes,width=700,height=380');
    	document.forms["webmail"].SUB_DUMMY.value = newValue;
   	document.forms["webmail"].SUB_DUMMY.name = newName;
    	document.forms["webmail"].target="SMS_IBxiMbQbAKpthSZnjvxFU8qKajNVOiJTASVat7Rawyb";
    	document.forms["webmail"].submit ();
}

function Build_folder() {
	var act_msgs;
	var folder;
	var result;
	act_msgs = "'Act_Msgs'";   
	var ret;
    	ret =  '<select class="combo_toolbar" name="Fld_Change_List" onChange="folderM_doit (' + act_msgs + ',1,1)">';
    	ret += '<option value="">Folders</option>';

    folder = "Boîte de réception";    
    ret += '<option value="aW5ib3g=">';
      ret += '|_';
      ret += folder.substr(0,10) +  '</option>';
    folder = "Brouillon";    
    ret += '<option value="ZHJhZnQ=">';
      ret += '|_';
      ret += folder.substr(0,10) +  '</option>';
    folder = "Messages envoyés";    
    ret += '<option value="b3V0Ym94">';
      ret += '|_';
      ret += folder.substr(0,10) +  '</option>';
    folder = "Corbeille";    
    ret += '<option value="dHJhc2g=">';
      ret += '|_';
      ret += folder.substr(0,10) +  '</option>';
    ret += '</select>';
    return ret;
}

function folderM_doit(newName,newValue,doPub) {
	if(document.forms["webmail"].Fld_Change_List.selectedIndex == 0) alert("Please choose a folder");
	else {
		document.forms["webmail"].C_Folder.value = document.forms["webmail"].Fld_Change_List.options[document.forms["webmail"].Fld_Change_List.selectedIndex].value;
		doit (newName,newValue,doPub);
	}
}

function inbox_doit(newName,newValue,newFolder,doPub) {
	document.forms["webmail"].C_Folder.value = newFolder;
	doit (newName,newValue,doPub);
}

function doit(newName,newValue,doPub) {
	clearTimeout (timerID);

	if (newName != last_action)
	{
		clearTimeout (timer_id);
		last_action = newName;	
		document.forms["webmail"].SUB_DUMMY.value = newValue;
		document.forms["webmail"].SUB_DUMMY.name = newName;
		document.forms["webmail"].target = "_self";
		document.forms["webmail"].submit ();
		timer_id = setTimeout('clearAction()',5000);		
	}	
	
	
}


function folder_doit (newName,newValue,newFolder,doPub)
{
	document.forms["webmail"].C_Folder.value = newFolder;
	doit (newName,newValue,doPub);
}

function clearAction()
{
	last_action = '';
}

function do_logout() {	
	document.forms["webmail"].SUB_DUMMY.name = "Act_Logout";
	document.forms["webmail"].SUB_DUMMY.value = "1";
	document.forms["webmail"].target = "_self";
	document.forms["webmail"].submit ();
}



function configure_zone(zone_index) {
	document.forms["webmail"].ZONEID.value = zone_index;
    	doit ('Act_Content_Open', 1, 1);
}

function Checkprofile() {
   	var currentprofile = "";    
   	if(currentprofile.length == 0) return "&nbsp;";
   	else return ("<a href=javascript:doit('Act_Pro',1,1)>Current profile :&nbsp;" + currentprofile + "</a>");  		
}
// -->
</script>
<style type="text/css">
.menulines
{
border:1px solid #Ffffff;
}

.menulines a
{
text-decoration:none;
color:black;
}
</style>
<title>Ajouter un Test de Jeu</title>
<SCRIPT LANGUAGE="JavaScript">
<!--
	var agent = navigator.userAgent.toLowerCase();
	var ver = parseInt(navigator.appVersion);

	if ((agent.indexOf("ppc") >= 0) || (agent.indexOf("mac_powerpc") >= 0)) {
		document.write('');
		}
	else {
			if ((agent.indexOf("mozilla") != -1) && (navigator.appName.indexOf("Netscape") != -1)) {
				if (ver >= 5) { 
					document.write(''); }
				else { 
					document.write(''); }
		 	}
			else { document.write(''); }
		 }

function doit (newName,newValue,doPub)
{
	document.webmail.SUB_DUMMY.value = newValue;
	document.webmail.SUB_DUMMY.name = newName;
	document.webmail.target = "_self";
	document.webmail.submit ();
}

function Help() 
{
	var index = "" + document.forms["webmail"].AIDE.value + ".html"
	window.open (index,"Aide","toolbar=no,resizable=yes,scrollbars=yes,width=620,height=362");	  	
}

//-->
</SCRIPT>
<script language="javascript">
<!--
var timerID;

var last_action = '';
var timer_id;

var messageDate;
var navvers = navigator.appVersion.substring(0,1);
if (navvers > 3) navok = true;
else             navok = false;

today = new Date;
jour = today.getDay();
numero = today.getDate();
mois = today.getMonth();
if (navok) annee = today.getFullYear();
else       annee = today.getYear();
TabJour = new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
TabMois = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
messageDate = TabJour[jour] + " " + TabMois[mois] + " " + numero + " " + annee;

function Compo_doit(newName,newValue,doPub) {
	var quiz = parseInt(Math.random()*10000000);	
	var cible = "compo_" + quiz;    
    	document.forms["webmail"].SUB_DUMMY.value = newValue;
    	document.forms["webmail"].SUB_DUMMY.name = newName;
    	document.forms["webmail"].target = "_self";
    	document.forms["webmail"].submit ();
}

function SMS_doit(newName,newValue,doPub) {    
    	window.open('','SMS_IBxiMbQbAKpthSZnjvxFU8qKajNVOiJTASVat7Rawyb','scrollbars=yes,resizable=yes,width=700,height=380');
    	document.forms["webmail"].SUB_DUMMY.value = newValue;
   	document.forms["webmail"].SUB_DUMMY.name = newName;
    	document.forms["webmail"].target="SMS_IBxiMbQbAKpthSZnjvxFU8qKajNVOiJTASVat7Rawyb";
    	document.forms["webmail"].submit ();
}

function Build_folder() {
	var act_msgs;
	var folder;
	var result;
	act_msgs = "'Act_Msgs'";   
	var ret;
    	ret =  '<select class="combo_toolbar" name="Fld_Change_List" onChange="folderM_doit (' + act_msgs + ',1,1)">';
    	ret += '<option value="">Folders</option>';

    folder = "Boîte de réception";    
    ret += '<option value="aW5ib3g=">';
      ret += '|_';
      ret += folder.substr(0,10) +  '</option>';
    folder = "Brouillon";    
    ret += '<option value="ZHJhZnQ=">';
      ret += '|_';
      ret += folder.substr(0,10) +  '</option>';
    folder = "Messages envoyés";    
    ret += '<option value="b3V0Ym94">';
      ret += '|_';
      ret += folder.substr(0,10) +  '</option>';
    folder = "Corbeille";    
    ret += '<option value="dHJhc2g=">';
      ret += '|_';
      ret += folder.substr(0,10) +  '</option>';
    ret += '</select>';
    return ret;
}

function folderM_doit(newName,newValue,doPub) {
	if(document.forms["webmail"].Fld_Change_List.selectedIndex == 0) alert("Please choose a folder");
	else {
		document.forms["webmail"].C_Folder.value = document.forms["webmail"].Fld_Change_List.options[document.forms["webmail"].Fld_Change_List.selectedIndex].value;
		doit (newName,newValue,doPub);
	}
}

function inbox_doit(newName,newValue,newFolder,doPub) {
	document.forms["webmail"].C_Folder.value = newFolder;
	doit (newName,newValue,doPub);
}

function doit(newName,newValue,doPub) {
	clearTimeout (timerID);

	if (newName != last_action)
	{
		clearTimeout (timer_id);
		last_action = newName;	
		document.forms["webmail"].SUB_DUMMY.value = newValue;
		document.forms["webmail"].SUB_DUMMY.name = newName;
		document.forms["webmail"].target = "_self";
		document.forms["webmail"].submit ();
		timer_id = setTimeout('clearAction()',5000);		
	}	
	
	
}


function folder_doit (newName,newValue,newFolder,doPub)
{
	document.forms["webmail"].C_Folder.value = newFolder;
	doit (newName,newValue,doPub);
}

function clearAction()
{
	last_action = '';
}

function do_logout() {	
	document.forms["webmail"].SUB_DUMMY.name = "Act_Logout";
	document.forms["webmail"].SUB_DUMMY.value = "1";
	document.forms["webmail"].target = "_self";
	document.forms["webmail"].submit ();
}



function configure_zone(zone_index) {
	document.forms["webmail"].ZONEID.value = zone_index;
    	doit ('Act_Content_Open', 1, 1);
}

function Checkprofile() {
   	var currentprofile = "";    
   	if(currentprofile.length == 0) return "&nbsp;";
   	else return ("<a href=javascript:doit('Act_Pro',1,1)>Current profile :&nbsp;" + currentprofile + "</a>");  		
}
// -->
</script>
<script language="javascript">

window.name = 'compo'

function doit_Send (newName,newValue,doPub)
{
  if (newName == 'Act_Sp')
  {
	if (document.webmail.Sp_Lang.options[document.webmail.Sp_Lang.selectedIndex].value == "bad")
	{
		alert("Veuillez sélectionner une langue");
	}
	else
	{
		PrepareEmail();
	  	document.webmail.SUB_DUMMY.value = newValue;
  		document.webmail.SUB_DUMMY.name = newName;
  		document.webmail.target = "_self";
  		document.webmail.submit();
  	}

  }
	
  if (newName == "Act_C_Charset")
  {
    answ = confirm ('Warning, if you confirmed, you will loose informations !');
    if (answ)
    { 		    
      document.forms["webmail"].AliasTo.value = "";
      document.forms["webmail"].AliasCc.value = "";
      document.forms["webmail"].AliasBcc.value = "";
      document.forms["webmail"].Subject.value = "";
      document.forms["webmail"].Text.value = "";
      document.forms["webmail"].Text2.value = "";
      document.forms["webmail"].SUB_DUMMY.value = newValue;
      document.forms["webmail"].SUB_DUMMY.name = newName;
      document.forms["webmail"].target = "_self";
      PrepareEmail()
      document.forms["webmail"].submit ();
    }
  }
  else
  {
    subject_length = document.forms["webmail"].Subject.value.length;
    if (subject_length > 0)
    { 
    	verif_subject = document.forms["webmail"].Subject.value;	
    	for (i=0; i<subject_length+30; i++)
    	{
       		verif_subject = verif_subject.replace (/^ /,"");       
       		verif_subject = verif_subject.replace (/ $/,"");
    	}
    	document.forms["webmail"].Subject.value = verif_subject;
    } 
    document.forms["webmail"].SUB_DUMMY.value = newValue;
    document.forms["webmail"].SUB_DUMMY.name = newName;
    PrepareEmail()
    
   	document.forms["webmail"].target = "_self";      
   	document.forms["webmail"].submit ();    	   
  }
}


function sumit (newName,newValue,doPub)
{
  if (newName== 'Act_C_Sum' & htmlmode)
  {
    Imprime ();
  }        
  else 
  {
// Pb with Netscape 5 : print function return an error page : no more session file
// This is an alternative function to display the page without an error

    if ((navigator.appName == "Netscape") && (navigator.appVersion >= "5"))
    {
      print_win = open('','','');
      document.webmail.SUB_DUMMY.value = newValue;
      document.webmail.SUB_DUMMY.name = newName;
      document.webmail.target = "";
      PrepareEmail()
      document.webmail.submit ();		
    }	
    else
    {
      document.webmail.SUB_DUMMY.value = newValue;
      document.webmail.SUB_DUMMY.name = newName;
      document.webmail.target = "_blank";
      PrepareEmail()
      document.webmail.submit ();
    }
  }
}

function Imprime()
{
  document.webmail.impr.value = window.editeurHtml.document.body.innerHTML;
  lance_impr = window.open ('','preview','toolbar=no');   
}

function TraitEvent()
{
  var TypeVal;
  if(window.document.webmail.Val.value != "")
  {   
    TypeVal = parseInt(window.document.webmail.TypeVal.value);
    switch (TypeVal)
    {      
      case 1 : MiseEnForme("ForeColor",window.document.webmail.Val.value);
               break;
      case 2 : MiseEnForme("BackColor",window.document.webmail.Val.value);
               break;
      case 6 : MiseEnForme("CreateLink",window.document.webmail.TypeURL.value + window.document.webmail.Val.value);
               break;
      case 7 : MiseEnForme("InsertImage",window.document.webmail.Val.value);
               break;
    }         
  }   
  window.document.webmail.Val.value = ""
}

function MiseEnForme(TypeM,ValeurM)
{
  var TypeSel = window.editeurHtml.document.selection.type;
  var Cible;   
  window.editeurHtml.focus()

  if(TypeSel == "None")
  { 
    Cible = window.editeurHtml.document;
  }
  else  
    Cible = Sel;

  if (Sel.item)
    if(Sel.item(0).tagName == "IMG") 
    {      
      Sel.item(0).width  = Sel.item(0).offsetWidth
      Sel.item(0).height = Sel.item(0).offsetHeight
      Sel.item(0).border = 1
    }
  Sel.select();
  Cible.execCommand(TypeM,false,ValeurM)

  if(Sel)
    if((Sel.item) && (Sel.item(0).tagName != "IMG"))
      window.editeurHtml.focus()            
  FcVal = Sel;       
}

function doab (newName,newValue,doPub,role)
{
  window.open ('','abook','scrollbars=yes,resizable=0,width=620,height=362,top=2')
  document.webmail.target = 'abook'
  document.webmail.SUB_DUMMY.value = newValue;
  document.webmail.SUB_DUMMY.name = newName;
  document.webmail.Act_Role.value = role;
  PrepareEmail();
  document.webmail.submit ();
}

function PrepareEmail()
{
	if ((agent.indexOf("ppc") >= 0) || (agent.indexOf("mac_powerpc") >= 0))
	{
	}
	else
	{
			if(navigator.appName == "Microsoft Internet Explorer")
			{  
					window.document.webmail.HtmlText.value = window.editeurHtml.document.body.innerHTML;
					window.document.webmail.Text.value = window.editeurHtml.document.body.innerHTML;
			} 
	}

  return true;   
}

function doit_Spell(newName,newValue,doPub,role)
{
	if (document.webmail.Sp_Lang.options[document.webmail.Sp_Lang.selectedIndex].value == "bad")
	{
		alert("Veuillez sélectionner une langue");
	}
	else
	{
		PrepareEmail();
	  	document.webmail.SUB_DUMMY.value = newValue;
  		document.webmail.SUB_DUMMY.name = newName;
  		document.webmail.target = "_self";
  		document.webmail.submit();
  	}
}


var documentCharge = false;
var htmlmode = false;
var Sel;
var first = 1;
var FcVal = "";

function LancerEditeur()
{
  var Sel;
  var init;

  if(window.document.webmail.HtmlBufferFormat.value=="1" && first == 1)
  {
    window.editeurHtml.document.designMode = "on"
    init = "<BODY style='{border-bottom:0px ;border-left:0px;border-right:0px;border-top:0px;font-family:verdana, arial;font-size: 12px}' ONCONTEXTMENU=\"return false\">" + "<DIV></DIV>" +   "</BODY>";
    window.editeurHtml.document.write(init);
    htmlmode = true;
    window.document.webmail.FormatHTML == 1;
    window.document.webmail.Text2.style.visibility = "hidden";
    document.all.editeurHtml.style.visibility = "visible";
    document.all.BoiteOutils.style.visibility = "visible";


    if (window.document.webmail.Text2.value == "")
    {
    }
    else
    {
      window.editeurHtml.document.body.innerHTML = window.document.webmail.Text2.value;
    }
  
    first = 0;
  }  
  else
  {  
      window.editeurHtml.document.designMode = "on"
      init = "<BODY style='{border-bottom:0px ;border-left:0px;border-right:0px;border-top:0px;font-family:verdana, arial;font-size: 12px}' ONCONTEXTMENU=\"return false\">" + "<DIV></DIV>" +   "</BODY>";
      window.editeurHtml.document.write(init)
      htmlmode = true
      window.document.webmail.Text2.style.visibility = "hidden";
      document.all.editeurHtml.style.visibility = "visible";
      document.all.BoiteOutils.style.visibility = "visible";
      document.webmail.HtmlBufferFormat.value = "1"      
      
      if (window.document.webmail.HtmlBufferFormat.value=="1")
      { 
        window.editeurHtml.document.body.innerHTML = window.document.webmail.Text2.value;
      }
      else
      { 
        texte = window.document.webmail.Text2.value;
        window.editeurHtml.document.body.innerHTML = "<DIV>" + texte.replace(/\n/g, "</DIV><DIV>") //+"</DIV>";
      }
     
      first = 0
      Sel = window.editeurHtml.document.body.createTextRange()
      Sel.collapse(true)
      Sel.select()
  }  
  documentCharge = true 
  window.editeurHtml.focus() 
  FcVal = window.editeurHtml.document.selection.createRange();   
}


var FcIsSet = false;

function IsFc ()
{
  FcIsSet = true;
}

function RmFc ()
{
  setTimeout ('FcIsSet = false',600); 
}

function Imprime()
{
  document.webmail.impr.value = window.editeurHtml.document.body.innerHTML;
  lance_impr = window.open ('','preview','toolbar=no');   
}

  <!-- /HTML Actions //-->

  <!-- toolbar item //-->

function cutcoppas (text1)
{
  Sel = window.editeurHtml.document.selection.createRange();
  MiseEnForme(text1)
}

function StyleCar(text2)
{
  Sel = window.editeurHtml.document.selection.createRange();
  MiseEnForme(text2)
}

function justification (jus)
{
  Sel = window.editeurHtml.document.selection.createRange();
  MiseEnForme(jus)
}

function CodeCouleur(colors)
{
  Sel = window.editeurHtml.document.selection.createRange();
	if(colors == 1)	
		Couleur(colors);
	if(colors == 2)
		Couleur2(colors);
}

function Couleur(param)
{
  window.document.webmail.TypeVal.value = param;
  window.open('couleur_police.htm','couleur','toolbar=no,width=350,height=250,resizable=no')
}

function Couleur2(param)
{
  window.document.webmail.TypeVal.value = param;
  window.open('couleur_fond.htm','couleur','toolbar=no,width=350,height=250,resizable=no')
}

function LienURL()
{
  window.document.webmail.TypeVal.value = "6";
  window.open('ajout_lien.htm','Liens','toolbar=no,width=420,height=123,resizable=no');
}

function Ajout_Image()
{
  window.document.webmail.TypeVal.value = "7";
  window.open('ajout_image.htm','Image','toolbar=no,width=425,height=124,resizable=no');
}

function NomPolice ()
{
  Sel = window.editeurHtml.document.selection.createRange();
  MiseEnForme("FontName",window.document.webmail.police.value);
  window.document.webmail.police.selectedIndex = 0
}

function GererTaille()
{
  Sel = window.editeurHtml.document.selection.createRange();
  var t = window.document.webmail.TailleCar[window.document.webmail.TailleCar.selectedIndex].value;
  if(t != 0) 
  {   
    MiseEnForme("FontSize",t);
  }
  window.document.webmail.TailleCar.selectedIndex = 0 
}

function over_effect(e,state)
{
  if (document.all)
     source4=event.srcElement
  else 
  if (document.getElementById)
     source4=e.target
  if (source4.className=="menulines")
     source4.style.borderStyle=state
  else
  {
    while(source4.tagName!="TABLE")
    {
      source4=document.getElementById? source4.parentNode : source4.parentElement
      if (source4.className=="menulines")
      source4.style.borderStyle=state
    }
  }
}

function InsertElement(ad)
{
  Sel = window.editeurHtml.document.selection.createRange();
  if (ad=="image")
  {
    if (FcIsSet)
    {          
      Ajout_Image()      
    }
    else 
    {
      Sel = FcVal;
      Ajout_Image()
    }
  }
  else if (ad=="link")
  {
    LienURL ()
  }
}

function texttoobig (m,n,k)

{

		window.document.webmail.HtmlText.value = window.editeurHtml.document.body.innerHTML;
		if (window.document.webmail.HtmlText.value.length > 32767)
		{
    		check = confirm ("Votre texte est supérieur à 32 Ko,\nil sera tronqué à 32 Ko lors de l'envoi.\nVoulez vous continuer ?");
   			if (check) doit (m,n,k)
			else return;
		}
		else doit_Send (m,n,k);
  
}

</script>
</head>
<div align=center>
<script language="JavaScript">
var time_id = 0;
 function Delog(){
        var img = new Image()
		img.src = ''
		img.onload = new Function('Online();')
	    time_id = window.setTimeout('Offline()', 2000);
  }
  
  function Offline()
  {
	window.location = "";

		
	document.webmail.target = "_parent";
        document.webmail.SUB_DUMMY.name = "Act_Logout";
        document.webmail.SUB_DUMMY.value = "1";
        document.webmail.submit ();
		
  }
  
  function Online(){
    clearTimeout(time_id);
	window.location = "";
  }
</script>
</div>
<BODY onfocus="TraitEvent()" BGCOLOR=White TEXT=Black LINK=Blue VLINK=Blue ALINK=Blue LEFTMARGIN=0 TOPMARGIN=4 MARGINWIDTH=0 MARGINHEIGHT=4>
   <div align="center">
   <FORM METHOD="POST" action="admin/modif_dossier2.php" name="webmail" onsubmit="return true;">
    <TABLE width="570" 
        border=0 cellPadding=3 cellSpacing=0 class=forumline>
      <TBODY>
        <TR> 
          <TD colspan="2" class=row1><div align="center"> 
              <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr> 
                  <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
                  <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
                </tr>
                <tr> 
                  <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Modifier 
                    une page=-</font></b></font></td>
                </tr>
                <tr> 
                  <td colspan="2">&nbsp;</td>
                </tr>
              </table>
            </div></TD>
        </TR>
        <TR> 
          <TD colspan="2" class=row1>&nbsp;</TD>
        </TR>
        <TR> 
          <TD width="125" class=row1><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><SPAN class=gen>Auteur 
            :</SPAN></font></TD>
          <TD width="612" class=row2><SPAN class=genmed> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?
			$auteur = ucfirst($HTTP_COOKIE_VARS[gen]);
			echo $auteur;
			?>
            <input name="id_dossier" type="hidden" id="id_dossier" value="<? echo $id; ?>">
            </font></SPAN></TD>
        </TR>
      <input name="numpage" type="hidden" id="numpage" value="<? echo $idp; ?>">
      <TR> 
        <TD valign="top" nowrap class=row1><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Titre 
          de la Page :</font></TD>
        <TD class=row2><INPUT name=titrepage class=post 
            style="WIDTH: 350px" tabIndex=2size=45 value="<? echo $disp[titrepage]; ?>"> 
          <br> <font size="1" face="Verdana, Arial, Helvetica, sans-serif">(inutile 
          si page unique)</font></TD>
      </TR></TBODY>
    </TABLE>
    <table width="570" border="0" cellspacing="0" cellpadding="0">
      
      
      <script>
function trunc (Folder,TruncLimit)
{
  var Long = Folder.length
  if  (Long > TruncLimit) 
  {
    var FolderToCut = Folder.substr (0,TruncLimit) + " ..."
    return FolderToCut
  }
  else 
  {
    return Folder
  }
  
  
}
</script>
    <td height="13"> 
    &nbsp; 
    <input type='hidden' name='AIDE' value='compo'>
    <input type='hidden' name="HtmlBufferFormat" value="0">
    <input type='hidden' name='HtmlText'>
    <input type='hidden' name='Role'>
    <input type="hidden" name="R_Act" value="Act_Compo_Reload">
    <input type='hidden' name='Text'>
    <input type="hidden" name="impr" value="">
    <input type="hidden" name="C_Folder" value="">
    <input type="hidden" name="ID" value="IBxiMbQbAKpthSZnjvxFU8qKajNVOiJTASVat7Rawyb">
    <input type="hidden" name="SUB_DUMMY" value="0">
    <input type="hidden" name="HELP_ID" value="compose">
    <input type="hidden" name="RecipNb" value="0">
    <input type="hidden" name="FileNb" value="0">
    <input type="hidden" name="CompoState" value="1">
    <input type="hidden" name="Act_Role" value="-1">
    <input type="hidden" name="Sign_Added" value="1">
    <input type="hidden" name="Facility" value="0">
    <input type="hidden" name="TEMP_FOLDER" value="">
    <script language='javascript'>
function folderEpx_doit (newName,newValue,newFolder,doPub)
{
	document.forms["webmail"].Fld_ExpName.value = newFolder;
	doit (newName,newValue,doPub);
}
function default_doit(newName,newValue,newFolder,doPub) {
	document.forms["webmail"].C_Folder.value = newFolder;
	document.forms["webmail"].chTpl.name = 'Tpl';
	document.forms["webmail"].chTpl.value = 'default';
	doit (newName,newValue,doPub);
}
</script>
    <input type="hidden" name="Fld_ExpName" value="">
    <input type="hidden" name="chTpl" value="">
        <!-- Debut main -->
        <input name="FormatHTML" type="hidden" id="FormatHTML2" value="1"> 
        <table width="570" border="0" cellspacing="0" cellpadding="0">
            <tr> 
              <td><table width="570" border="0" cellspacing="0" cellpadding="10" bgcolor="FFFFFF">
                  <tr> 
                    <td width="100%"> <div id='BoiteOutils' style='width: 100%; position: relative; top: 0px; visibility:hidden'> 
                        <table height='28' border='0' cellpadding='0' cellspacing='0' width="100%" onMouseover="over_effect(event,'outset')" onMouseout="over_effect(event,'solid')" onMousedown="over_effect(event,'inset')" onMouseup="over_effect(event,'outset')" style="background-color:#Ffffff">
                          <tr> 
                            <td width='5'></td>
                            <td class="menulines" onClick="cutcoppas('cut')"><img src='images/cut.gif' border="0" alt="Couper"> 
                            <td> 
                            <td class="menulines" onClick="cutcoppas('copy')"><img src='images/copy.gif' border="0" alt="Copier"> 
                            <td> 
                            <td class="menulines" onClick="cutcoppas('paste')"><img src='images/paste.gif' border="0" alt="Coller"> 
                            <td> 
                            <td class="menulines" onClick="StyleCar('bold')"><img src='images/bold.gif' border="0" alt="Gras"> 
                            <td> 
                            <td class="menulines" onClick="StyleCar('italic')"><img src='images/italic.gif' border="0" alt="Italique"> 
                            <td> 
                            <td class="menulines" onClick="StyleCar('underline')"><img src='images/underline.gif'border="0" alt="Souligné"> 
                            <td> 
                            <td class="menulines" onClick="justification('justifyleft')"><img src='images/aleft.gif' border="0" alt="Aligné à gauche"> 
                            <td> 
                            <td class="menulines" onClick="justification('justifycenter')"><img src='images/acenter.gif' border="0" alt="Centrer"> 
                            <td> 
                            <td class="menulines" onClick="justification('justifyright')"><img src='images/aright.gif' border="0" alt="Aligné à droite"> 
                            <td> 
                            <td class="menulines" onClick="CodeCouleur('1')"><img src='images/police.gif' border="0" alt="Couleur de police"> 
                            <td> 
                            <td class="menulines" onClick="CodeCouleur('2')"><img src='images/back.gif'border="0" alt="Couleur de fond"> 
                            <td> 
                            <td class="menulines" onClick="InsertElement('link')"><img src='images/link.gif' border="0" alt="Insérer lien"> 
                            <td> 
                            <td class="menulines" onClick="InsertElement('image')"><img src='images/photo.gif' border="0" alt="Insérer image"> 
                            <td> 
                            <td width='10'></td>
                            <td width='40'> <select name='police' class='combo_compose' size='1' onChange='NomPolice ()'>
                                <option selected >Police</option>
                                <option value='Arial'>Arial</option>
                                <option value='Arial Black'>Arial Black</option>
                                <option value='Book Antiqua'>Book Antiqua</option>
                                <option value='Century Gothic'>Courier</option>
                                <option value='Garamond'>Garamond</option>
                                <option value='Courier'>Courier</option>
                                <option value='Impact'>Impact</option>
                                <option value='Lucida'>Lucida</option>
                                <option value='Tahoma'>Tahoma</option>
                                <option value='Times New Roman'>Times New Roman</option>
                                <option value='Verdana'>Verdana</option>
                                <option value='Webdings'>Webdings</option>
                                <option value='Wingdings'>Wingdings</option>
                              </select> </td>
                            <td width='10'></td>
                            <td width='40'> <select size='1' name='TailleCar' class='combo_compose' onChange='GererTaille()'>
                                <option value='0' selected>Taille</option>
                                <option value='1'>6</option>
                                <option value='2'>10</option>
                                <option value='3'>12</option>
                                <option value='4'>14</option>
                                <option value='5'>18</option>
                                <option value='6'>24</option>
                                <option value='7'>28</option>
                              </select> </td>
                            <td width='5'></td>
                          </tr>
                        </table>
                      </div></td>
                  </tr>
                  <tr> 
                    <td><div id='boite' name='boite' style='height: 700; position: relative; top: 0px; width: 100%; border: 1px solid #000000'> 
                        <textarea wrap="HARD" name="Text2" id="textarea6" rows=1 style='left : 0px; top : 0px ;HEIGHT: 100%; visibility: visible; POSITION: absolute ; WIDTH: 100%; Z-INDEX: 100' cols='20'><? echo $disp[text]; ?></Textarea>
                        <iframe SRC='' name="editeurHtml" id='editeurHtml' style='left : 0px; top : 0px ; HEIGHT: 100%; position: absolute; visibility : visible;  WIDTH: 100%' onFocus="IsFc ()" onBlur="RmFc ()"></IFRAME>
                      </div></td>
                  </tr>
                  <tr> 
                    <td width='110' height="58"> <input name='Val' type='hidden'> 
                      <input type='hidden' name='TypeVal'> <input type='hidden' name='TypeURL'> 
                      <input type='hidden' name='Txt1' value=''> <input type='hidden' name='Txt2' value=''> 
                      <input type='hidden' name='Txt3' value=''> <input type='hidden' name='Txt4' value=''> 
                      <input type='hidden' name='Txt5' value=''> <input type='hidden' name='Txt6' value=''> 
                      <input type='hidden' name='Txt7' value=''> <input type='hidden' name='Txt8' value=''> 
                      <input type='hidden' name='Txt9' value=''> <input type='hidden' name='Txt10' value=''> 
                      <input type='hidden' name='Txt11' value=''> <input type='hidden' name='Txt12' value=''> 
                      <input type='hidden' name='Txt13' value=''> <input type='hidden' name='Txt14' value=''> 
                      <input type='hidden' name='Txt15' value=''> <input type='hidden' name='Txt16' value=''> 
                      <input type='hidden' name='Bcg1'> <input type='hidden' name='Bcg2'> 
                      <input type='hidden' name='Bcg3'> <input type='hidden' name='Bcg4'> 
                      <input type='hidden' name='Bcg5'> <input type='hidden' name='Bcg6'> 
                      <input type='hidden' name='Bcg7'> <input type='hidden' name='Bcg8'> 
                      <input type='hidden' name='Bcg9'> <input type='hidden' name='Bcg10'> 
                      <input type='hidden' name='Bcg11'> <input type='hidden' name='Bcg12'> 
                      <input type='hidden' name='Bcg13'> <input type='hidden' name='Bcg14'> 
                      <input type='hidden' name='Bcg15'> <input type='hidden' name='change' value='0'> 
                      <span class="gen"> 
                      <input name="Subject" type="hidden" id="Subject6" value="d">
                      </span></td>
                  </tr>
                  <script language="javascript">
              setTimeout('LancerEditeur()',200);</script>
                </table></td>
            </tr></table><tr><td></table>
    
    <input type="submit" name="submit" value="Terminer" onClick="javascript:texttoobig('Act_C_Send', 1, 1)">&nbsp; <br>
  </form>
  </div>
</BODY>
</html>