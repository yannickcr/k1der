<?php

$dir = dirname(__FILE__).'/../modules/blogs/inc/clearbricks';

$__autoload = array(
	# Classes
	'feedParser'		=> $dir.'/class.feed.parser.php',
	'feedReader'		=> $dir.'/class.feed.reader.php',
	'filemanager'		=> $dir.'/class.filemanager.php',
	'fileItem'		=> $dir.'/class.filemanager.php',
	'htmlFilter'		=> $dir.'/class.html.filter.php',
	'imageMeta'		=> $dir.'/class.image.meta.php',
	'imageTools'		=> $dir.'/class.image.tools.php',
	'mail'			=> $dir.'/class.mail.php',
	'socketMail'		=> $dir.'/class.mail.php',
	'pager'			=> $dir.'/class.pager.php',
	'restServer'		=> $dir.'/class.rest.php',
	'sessionDB'		=> $dir.'/class.session.db.php',
	'xmlTag'			=> $dir.'/class.rest.php',
	'template'		=> $dir.'/class.template.php',
	'urlHandler'		=> $dir.'/class.url.handler.php',
	'wiki2xhtml'		=> $dir.'/class.wiki2xhtml.php',
	'xmlsql'			=> $dir.'/class.xmlsql.php',
	
	# Libraries
	'crypt'			=> $dir.'/lib.crypt.php',
	'dt'				=> $dir.'/lib.date.php',
	'files'			=> $dir.'/lib.files.php',
	'path'			=> $dir.'/lib.files.php',
	'form'			=> $dir.'/lib.form.php',
	'formSelectOption'	=> $dir.'/lib.form.php',
	'html'			=> $dir.'/lib.html.php',
	'http'			=> $dir.'/lib.http.php',
	'text'			=> $dir.'/lib.text.php',
	
	# Database layer
	'dbLayer'			=> $dir.'/dblayer/dblayer.php',
	
	# Third party libs
	'HttpClient'				=> $dir.'/ext/incutio.http_client.php',
	'IXR_Value'				=> $dir.'/ext/incutio.ixr_library.php',
	'IXR_Message'				=> $dir.'/ext/incutio.ixr_library.php',
	'IXR_Server'				=> $dir.'/ext/incutio.ixr_library.php',
	'IXR_Request'				=> $dir.'/ext/incutio.ixr_library.php',
	'IXR_Client'				=> $dir.'/ext/incutio.ixr_library.php',
	'IXR_Error'				=> $dir.'/ext/incutio.ixr_library.php',
	'IXR_Date'				=> $dir.'/ext/incutio.ixr_library.php',
	'IXR_Base64'				=> $dir.'/ext/incutio.ixr_library.php',
	'IXR_IntrospectionServer'	=> $dir.'/ext/incutio.ixr_library.php',
	'IXR_ClientMulticall'		=> $dir.'/ext/incutio.ixr_library.php'
);


function __autoload($class) {
	global $__autoload;

	if(file_exists('modules/'.$class.'/'.$class.'.class.php')) require_once('modules/'.$class.'/'.$class.'.class.php');
    else if(file_exists('include/librairies/'.$class.'.class.php')) require_once('include/librairies/'.$class.'.class.php');
	else if (isset($__autoload[$class])) {
		require_once $__autoload[$class];
	}
}
?>