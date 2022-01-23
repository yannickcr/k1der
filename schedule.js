/*******************************
 P o w e r e d   b y   a K k Y
*******************************/


/*******************************
	  Variables de Date
*******************************/
var date = new Date();
var currentDate = new Date();
var theDay;
var body;
var dayChoosen="";

/*******************************
	  Variables de style
/******************************/
var colorClick = "#cc0000";
var colorOut = "";
var colorOver = "#cccccc";
var colorOldDate = "#666666";
var fontClick = "#FFFFFF";
var fontOut = "#000000";
var fontOver = "#000000";
var cursorStyle = 'hand';

function initDate () {
	nbArg=initDate.arguments;
	theDay=""; dayChoosen="";
	if (!nbArg.length) { 
		date.setYear(currentDate.getFullYear());
		date.setMonth(currentDate.getMonth());
	}
	else if (nbArg[0]=='m') date.setMonth(nbArg[1]);
	else if (nbArg[0]=='y') {
		if (nbArg[1]>=(currentDate.getFullYear()+1)) {
			alert("Dis donc, t'es pas préssé lol.\nL'année "+date.getFullYear()+" devrait faire l'affaire ;-)");
			document.challenge.year.selectedIndex=0;
			return false;
		}
		else date.setYear(nbArg[1]);
	}
	
	date.setDate(1);
	constructTable();
}

function checkOldDate (value) {
	if (date.getMonth()<currentDate.getMonth()) return false;
	else if (date.getMonth()==currentDate.getMonth()) {
		if (value<currentDate.getDate()) return false;
		else return true;	
	}
	else return true;
}

function constructTable () {
	var nbDays;
	var tab1 = new Array(1,3,5,7,8,10,12);
	var currentMonth = date.getMonth()+1;
	var currentDay = date.getDay()-1;
	var maxCellDate= 35;
	var startDay = 1;
	
	for (i=0;i<=tab1.length;i++) {
		if (tab1[i]==currentMonth) { nbDays=31; break; }
	}
	if (currentMonth==2) nbDays=28;
	else if (!nbDays) nbDays=30;
	
	emptyCell = "<td>&nbsp;</td>";
	body = '<table width="200" border="1" cellspacing="2" cellpadding="2" bordercolorlight="#ffffff" bordercolordark="#ffffff">';
	body+= '<tr><td align=center class="cellDaySchedule"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">L</font></td><td align=center class="cellDaySchedule"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">M</font></td><td align=center class="cellDaySchedule"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">M</font></td><td align=center class="cellDaySchedule"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">J</font></td><td align=center class="cellDaySchedule"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">V</font></td><td align=center class="cellDaySchedule"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">S</font></td><td align=center class="cellDaySchedule"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">D</font></td></tr><tr>';
	for (i=0;i<maxCellDate;i++) {
		
		// Pour dimanche du premier du mois
		if (i==0 && currentDay==-1) {
			if (checkOldDate(startDay) && (i==0 && currentDay==-1)) {
				body += "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
				body += "<td id=\"cell"+startDay+"\" class=\"cellSchedule\" onmouseover=\"changeCellStyle(this,'over',"+startDay+");\" onmouseout=\"changeCellStyle(this,'out',"+startDay+");\" onclick=\"chooseDate(this,"+startDay+");\">"+startDay+"</td>";
				currentDay=0; startDay++
			}
			else body += "<td id=\"cell"+startDay+"\" class=\"cellSchedule\" style=\"background-color:"+colorOldDate+";\">"+startDay+"</td>";
		}		
		
		if ((i%7)==0) body+='</tr></tr>';
		
		if (checkOldDate(startDay)) bodyCell = "<td align=center bordercolor=\"#000000\" id=\"cell"+startDay+"\" class=\"cellSchedule\" onmouseover=\"changeCellStyle(this,'over',"+startDay+");\" onmouseout=\"changeCellStyle(this,'out',"+startDay+");\" onclick=\"chooseDate(this,"+startDay+");\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">"+startDay+"</font></td>";
		else bodyCell = "<td align=center bordercolor=\"#000000\" id=\"cell"+startDay+"\" class=\"cellSchedule\" style=\"background-color:"+colorOldDate+";\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">"+startDay+"</font></td>";
		
		if (startDay<=nbDays) {
			if (startDay==1&&i!=currentDay) body += emptyCell;
			else if (startDay>=1&&i==currentDay) { body+=bodyCell; startDay+=1; }
			else if (startDay>1) { body+=bodyCell; startDay+=1; }
		}
		else body += emptyCell;	
	}
	body+="<tr></table>";
	document.getElementById('schedule').innerHTML = body;
	
}

function resetCellStyle () {
	for (i=1;document.getElementById('cell'+i);i++) {
		if (checkOldDate(i)) {
			document.getElementById('cell'+i).style.background=colorOut;
			document.getElementById('cell'+i).style.color=fontOut;
		}
	}
}

function chooseDate(obj,day) {
	resetCellStyle();
	obj.style.background=colorClick;
	obj.style.color=fontClick;
	theDay = day;
	return true;
}

function changeCellStyle(obj,mode,aDay) {
	if (mode=='out') {
		if (theDay!=aDay) {
			obj.style.background=colorOut;
			obj.style.color=fontOut;
		}	
	}
	else if (mode=='over') {
		if (theDay!=aDay) {
			obj.style.background=colorOver;
			obj.style.color=fontOver;
			obj.style.cursor=cursorStyle;
		}
	}
}

function verifDatas () {
	var error="";
	var form = document.challenge;
	if (document.getElementById('server').value == '') error="Ta pas dis sur quel server vous voulez jouer: le votre, le notre,...";
	if (document.getElementById('mail').value == '') error="Les mails c'est bien, surtout si on veu avoir une réponse...";
	if (document.getElementById('map').value == '') error="Ta pas choisi la map";
	//if (document.getElementById('num').value == '') error="Alors ? C'est quoi comme match que vous voulez faire ? 5vs5 , 2vs2 , ???";
	if (document.getElementById('leader').value == '') error="Aller ! Dis nous qui c'est le leader de ton clan";
	if (document.getElementById('clan').value == '') error="On aimerai bien savoir quel clan nous défie";
	if (document.getElementById('pseudo').value == '') error="Met ton pseudo, c bien les pseudos";
	if (document.getElementById('minute').value == '') error="Ta pas choisi les minutes de l'heure du matche\n(on est chiant ? oui, je sais :)";
	if (document.getElementById('hour').value == '') error="Ta pas choisi l'heure du matche";
	if (!theDay) error="Tu dois choisir une date\nPour cela clique sur un jour du calendrier";
		
	if (error) {
		alert(error);
		return false;
	}
	else {
		document.getElementById('dayFight').value=theDay;
		return true;
	}	
}

function updateImageMap (value) {
	if (document.images) {
		if (value=="") document.images['imageMap'].src='images/cartes/Sais%20pas.jpg';
		else document.images['imageMap'].src='images/cartes/'+value+'.jpg';
		document.getElementById('divMap').style.border="#000000 1px solid";
	}
}
