<?php
$wp_config = preg_replace('|wp-content.*$|','', __FILE__) . 'wp-config.php';
require_once($wp_config);
header('Content-type: text/javascript; charset=' . get_settings('blog_charset'), true);
header('Cache-control: max-age=2600000, must-revalidate', true);
extract(get_option( 'ajax_comment_preview' ));
header('ETag: ' . Ajax_Comment_Preview::version() . $ver, true);
?>/*
// +------------------------------------------------------------------+
// | Inspired by Code that is Copyright (c) 2004 Bitflux GmbH         |
// |     http://blog.bitflux.ch/p1735.html                            |
// | And heavily modified by Jeff Minard                              |
// |     http://www.creatimation.net                                  |
// +------------------------------------------------------------------+
// | Dependent on SACK which is Copyright (c) 2005 Gregory Wild-Smith |
// |     http://twilightuniverse.com/resources/code/sack/             |
// +------------------------------------------------------------------+
// | Author: Michael D. Adams                                         |
// |     http://blogwaffe.com/                                        |
// +------------------------------------------------------------------+
// | Version:  <?php echo Ajax_Comment_Preview::version(); ?>                                                  |
// +------------------------------------------------------------------+
// | License: GPL2                                                    |
// |     http://www.gnu.org/copyleft/gpl.html                         |
// +------------------------------------------------------------------+
*/

// Simon Willison http://simon.incutio.com/archive/2004/05/26/addLoadEvent

if ( 'function' != typeof addLoadEvent )
	addLoadEvent = function(func) {
		var oldonload=window.onload;
		if(typeof window.onload!='function')window.onload=func;
		else window.onload=function(){oldonload();func();}
	}

var commentPreview;var commentPreviewLast = '';
var emptyString = "<?php echo js_escape( $empty_string ); ?>";
var acpFormElement; var inputElement;var outputElement;var doitElement;var authorElement;var urlElement;

function commentPreviewInit() {
	doitElement = document.getElementById('acp-preview');
	acpFormElement = doitElement.parentNode;
	while ( 'form' != acpFormElement.tagName.toLowerCase() )
		acpFormElement = acpFormElement.parentNode;
	inputElement = acpGetFormInput( 'comment' );
	outputElement = document.getElementById('ajax-comment-preview');
	authorElement = acpGetFormInput( 'author' );
	urlElement = acpGetFormInput( 'url' );
	if ( inputElement == null || outputElement == null || doitElement == null ) return;
	doitElement.onclick = commentPreviewAJAX;
	// set the result field to hidden, or to default string
	if ( '' == emptyString ) outputElement.style.display = 'none';
	else outputElement.innerHTML = emptyString;
}

addLoadEvent(commentPreviewInit);

function acpGetFormInput( el ) {
	for ( i=0; i < acpFormElement.elements.length; i++ )
		if ( el == acpFormElement.elements[i].name )
			return acpFormElement.elements[i];
}

function commentPreviewAJAX() {
	var req = '';
	var newSack = false;
	commentPreview = new sack('<?php echo Ajax_Comment_Preview::htmldir(); ?>/ajax-comment-preview.php');
	var sep = commentPreview.argumentSeparator ? commentPreview.argumentSeparator : '&';
	commentPreview.method = 'POST';
	commentPreview.encodeURIString = false;
	commentPreview.onLoading = function() { outputElement.innerHTML = 'Loading.'; };
	commentPreview.onLoaded = function() { outputElement.innerHTML += '.'; };
	commentPreview.onInteractive = function() { outputElement.innerHTML += '.'; };
	commentPreview.onCompletion = function() { outputElement.innerHTML = commentPreview.response; };

	req = commentPreview.encVar('text', inputElement.value);
	if ( req ) {
		req += authorElement ? sep + commentPreview.encVar('author', authorElement.value) : '';
		req += urlElement ? sep + commentPreview.encVar('url', urlElement.value) : '';
	} else {
		if ( authorElement ) commentPreview.encVar('author', authorElement.value);
		if ( urlElement ) commentPreview.encVar('url', urlElement.value);
		req = new Array();
		for (key in commentPreview.vars)
			req[req.length] = key + "=" + commentPreview.vars[key][0];
		req = req.join(sep);
		newSack = true;
	}
	if ( req != commentPreviewLast && '' != inputElement.value ) { commentPreview.runAJAX(newSack?'':req); }
	else if ( '' == inputElement.value ) {
		if( '' == emptyString ) { outputElement.innerHTML = ''; outputElement.style.display = "none"; }
		else outputElement.innerHTML = emptyString;
	}
	commentPreviewLast = req;
}
