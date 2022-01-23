<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of DotClear.
# Copyright (c) 2005 Olivier Meunier and contributors. All rights
# reserved.
#
# DotClear is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# DotClear is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with DotClear; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

if (isset($_SERVER['DC_RC_PATH'])) {
	$rc_path = $_SERVER['DC_RC_PATH'];
} elseif (isset($_SERVER['REDIRECT_DC_RC_PATH'])) {
	$rc_path = $_SERVER['REDIRECT_DC_RC_PATH'];
} else {
	$rc_path = dirname(__FILE__).'/../blogs/inc/config.php';
}

if (!is_file($rc_path)) {
	printf('Configuration file does not exist. Please create one
first. You may use the <a href="%s">wizard</a>.','wizard.php');
	exit;
}

//calcul du nom de domaine à utiliser en création
$url_expl=explode('.',$_SERVER['HTTP_HOST']);
$url_expl=array_reverse($url_expl);
$domain_name='.'.$url_expl[1].'.'.$url_expl[0];

require dirname(__FILE__).'/../blogs/inc/clearbricks/lib.date.php';
require dirname(__FILE__).'/../blogs/inc/clearbricks/lib.files.php';
require dirname(__FILE__).'/../blogs/inc/clearbricks/lib.http.php';
require dirname(__FILE__).'/../blogs/inc/clearbricks/lib.html.php';
require dirname(__FILE__).'/../blogs/inc/clearbricks/class.url.handler.php';
require dirname(__FILE__).'/../blogs/inc/core/class.dc.core.php';
require dirname(__FILE__).'/../blogs/inc/core/class.dc.error.php';
require dirname(__FILE__).'/../blogs/inc/core/class.dc.auth.php';
require dirname(__FILE__).'/../blogs/inc/core/class.dc.session.php';
require dirname(__FILE__).'/../blogs/inc/core/class.dc.modules.php';
require dirname(__FILE__).'/../blogs/inc/clearbricks/class.rest.php';
require dirname(__FILE__).'/../blogs/inc/core/class.dc.rest.php';
require dirname(__FILE__).'/../blogs/inc/clearbricks/dblayer/dblayer.php';
require dirname(__FILE__).'/../blogs/inc/prepend.php';
require dirname(__FILE__).'/../blogs/inc/clearbricks/lib.form.php';

# Loading locales for detected language
$dlang = http::getAcceptLanguage();
if ($dlang) {
	l10n::init();
	l10n::set(dirname(__FILE__).'/../blogs/locales/'.$dlang.'/main');
}

if (!defined('DC_MASTER_KEY') || DC_MASTER_KEY == '') {
	echo __('Please set a master key (DC_MASTER_KEY) in configuration file.');
	exit;
}

# Check if dotclear is already installed
/*if (in_array($core->prefix.'version',$core->con->getTables())) {
	echo __('DotClear is already installed.');
	exit;
}*/

# Get information and perform install
$u_email = $u_firstname = $u_name= $u_blog ='';
$mail_sent = false;
if (!empty($_POST))
{
	$u_email = !empty($_POST['u_email']) ? $_POST['u_email'] : null;
	$u_firstname = !empty($_POST['u_firstname']) ? $_POST['u_firstname'] : null;
	$u_name = !empty($_POST['u_name']) ? $_POST['u_name'] : null;
	$u_blog = !empty($_POST['u_blog']) ? strtolower($_POST['u_blog']) : null;
	
	try
	{
		if (empty($u_email)) {
			throw new Exception(__('No email'));
		}
		if (!text::isEmail($u_email)) {
			throw new Exception(__('Invalid email address'));
		}
		if (empty($u_blog)) {
			throw new Exception(__('Blog name missing'));
		}
		if ($core->userExists($u_blog)) {
			throw new Exception(__('User ID already exist : choose an other blog name'));
		}
		if ($core->blogExists($u_blog)) {
			throw new Exception(__('Blog name already exist'));
		}
		if (!preg_match('|^[a-zA-Z0-9]+$|',$u_blog) ) {
			throw new Exception(__('Invalid blog name, only chars and nums'));
		}
		
		
		$user_pwd = crypt::createPassword();
		
		$cur = $core->con->openCursor($core->prefix.'user');
		$cur->user_id = (string) $u_blog;
		$cur->user_super = 0;
		$cur->user_pwd = crypt::hmac(DC_MASTER_KEY,$user_pwd);
		$cur->user_name = (string) $u_name;
		$cur->user_firstname = (string) $u_firstname;
		$cur->user_email = (string) $u_email;
		$cur->user_lang = $dlang;
		$cur->user_tz = 'Europe/Paris';
		$cur->user_creadt = array('NOW()');
		$cur->user_upddt = array('NOW()');
		$cur->user_post_status = 1;
		$cur->user_options = serialize($core->userDefaults());
		$cur->user_options['post_format'] = 'xhtml';
		$cur->insert();
		
		$core->auth->checkUser('admin');
		
		$admin_url = 'http://'.$u_blog.$domain_name.'/admin';
		$root_url = 'http://'.$u_blog.$domain_name.'/';
		
		$cur = $core->con->openCursor($core->prefix.'blog');
		$cur->blog_id = $u_blog.$domain_name;
		$cur->blog_url = $root_url.'/index.php/';
		$cur->blog_name = __('My first blog');
		$core->addBlog($cur);

		$core->setUserBlogPermissions($u_blog, $u_blog.$domain_name,  array('admin'=>1), true);

		$core->blogDefaults($cur->blog_id);
		
		$blog_settings = new dcSettings($core,$u_blog.$domain_name);
		$blog_settings->setNameSpace('system');
		$blog_settings->put('lang',$dlang);
		$blog_settings->put('public_url','/public/'.$u_blog.$domain_name);
		$blog_settings->put('public_path','public/'.$u_blog.$domain_name);//.$u_blog.$domain_name);
		$blog_settings->put('themes_url',$root_url.'themes');
		mkdir ('/home/www/web1/web/public/'.$u_blog.$domain_name);
		require dirname(__FILE__).'/../blogs/inc/clearbricks/class.mail.php';
		
		$subject = mb_encode_mimeheader('DotClear '.__('successfully installed'),'UTF-8','B');
		
		$message =
		__('Your new DotClear blog has been successfully set up at:')."\n\n".
				preg_replace('%/admin/install/index.php$%','',$_SERVER['REQUEST_URI'])."\n\n".
		
		__('You can log in to the administrator account with the following information:')."\n\n".
		
		__('Username:')." ".$u_blog."\n".
		__('Password:').' '.$user_pwd."\n\n".
		__('Connectez vous à l\'adresse suivante : http://'.$u_blog.$domain_name.'/admin')."\n\n".
		
		__('We hope you enjoy your new weblog. Thanks!')."\n\n".
		
		"--\n".
		"Olivier\n".
		"http://www.blogamoi.org\n".
		"Propulsé par\n".
		"http://www.dotclear.net/";
		
		$headers[] = 'From: webmaster@'.$_SERVER['HTTP_HOST'];
		$headers[] = 'Content-Type: text/plain; charset=UTF-8;';
		
		try {
			mail::sendMail($u_email,$subject,$message,$headers);
			$mail_sent = true;
		} catch (Exception $e) {	}
		
		$step = 1;
	}
	catch (Exception $e)
	{
		$err = $e->getMessage();
	}
}

if (!isset($step)) {
	$step = 0;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta http-equiv="Content-Language" content="en" />
  <meta name="MSSmartTagsPreventParsing" content="TRUE" />
  <meta name="ROBOTS" content="NOARCHIVE,NOINDEX,NOFOLLOW" />
  <meta name="GOOGLEBOT" content="NOSNIPPET" />
  <title>Création de votre blog de test avec DotClear 2</title>
  
  <style type="text/css">
  @import url(../style/default.css); 
  </style>
</head>

<body id="dotclear-admin" class="install">
<div id="content">
<?php
echo
'<h1>Création de votre blog de test avec DotClear 2</h1>';

if (!empty($err)) {
	echo '<div class="error"><p><strong>'.__('Errors:').'</strong> '.$err.'</p></div>';
}

if ($step == 0)
{

	echo
	'<h2>'.__('User information').'</h2>'.
	
	'<p>'.__('Please provide the following information needed to create the first user.').'</p>'.
	'<h2>ATTENTION vous allez créer un blog de TEST</h2>'.
	
	'<p>Ce blog sera détruit fin juillet. Le but est uniquement de vous permettre de découvrir DotClear 2.0 beta.</p>'.
	'<form action="index.php" method="post">'.
	'<p><label class="required" title="'.__('Required field').'">'.__('Email:').' '.
	form::field('u_email',20,255,html::escapeHTML($u_email)).'</label></p>'.
	'<p><label class="required" title="'.__('Required field').'">'.__('Blog name:').' *'.$domain_name.
	form::field('u_blog',20,255,html::escapeHTML($u_blog)).'</label></p>'.
	'<p><label>'.__('Firstname:').' '.
	form::field('u_firstname',20,255,html::escapeHTML($u_firstname)).'</label></p>'.
	'<p><label>'.__('Name:').' '.
	form::field('u_name',20,255,html::escapeHTML($u_name)).'</label></p>'.
	'<p><input type="submit" value="'.__('save').'" /></p>'.
	'</form>';
}
elseif ($step == 1)
{
	echo
	'<h2>'.__('All done!').'</h2>'.
	
	'<p>'.sprintf(__('Now you can <a href="%s">log in</a> with the following information:'),
	'http://'.$u_blog.$domain_name.'/admin').'</p>';
	
	if ($mail_sent) {
		echo
		'<p>'.sprintf(__('A password reminder was sent to %s. You\'ll be able to change it once you\'re logged in.'),
		$u_email).'</p>';
	}
}
?>
</div>
</body>
</html>
<? exit(); ?>