var quoteActivity;

function quote() {
	makeQuoteActivity();
	var quote=document.getElementById('phrase');
	quote.style.cursor='pointer';
	quote.onclick=function() {
		switchQuoteActivity('on');
		sendData('action=random','quote/random.html','POST','phrase','innerHTML',false,stopQuoteActivity);
	}
}

function makeQuoteActivity() {
	var quote=document.getElementById('phrase');
	quoteActivity=document.createElement('img');
	quoteActivity.style.visibility='hidden';
	quoteActivity.id='quote_activity';
	quoteActivity.src='templates/'+THEME+'/images/activity-black-on-white.gif';
	quoteActivity.alt='loading';
	quote.parentNode.insertBefore(quoteActivity,quote);
}

function stopQuoteActivity() {
	switchQuoteActivity('off');
}

function switchQuoteActivity(onoff) {
	if(onoff=='on') quoteActivity.style.visibility='visible';
	else quoteActivity.style.visibility='hidden';
}

addToStart(quote);