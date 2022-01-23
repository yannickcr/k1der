<?php

# Plugin Name: Right now
# Version: 0.1
# Plugin URI: http://blog.k1der.net/country/tag/rightnow
# Description: Display what you are doing right now.
# Author: Yannick Croissant
# Author URI: http://blog.k1der.net/country

if (!function_exists('get_option')) {
	require_once(dirname(__FILE__) . '/../../../wp-blog-header.php');
}

require_once(dirname(__FILE__) . '/steamCommunityProfile.class.php');

function rightnow_display($profil = 'country') {
	// Steam
	$steam = new steamCommunityProfile($profil);
	
	if($steam->getUserStatus()=='in-game') {
		$game = $steam->getUserCurrentGame();
		return '
		<h4>je joue à</h4>
		<img class="steam" src="'.$game['icon'].'" alt="'.$game['name'].'" />
		<p class="steam"><a href="'.$game['link'].'" title="Plus d\'informations sur '.$game['name'].'">'.$game['name'].'</a></p>
		'.(($game['join']=='')?'':'<a class="steamjoin" href="'.$game['join'].'">Rejoindre la partie</a>').'
		<a class="profil" href="http://steamcommunity.com'.$steam->profil.'" title="Mon profil sur Steam Community"><img src="http://www.k1der.net/blog/country/wp-content/themes/country-new/images/steam.gif" alt="Steam Community" /></a>
		';
	// Last.fm
	}
	$html = file_get_contents('http://ws.audioscrobbler.com/1.0/user/K1derCountry/recenttracks.xml');
	$data = new DOMDocument();
	@$data->loadXML($html); // Avoid parse errors
	$lastfm = new Domxpath($data);
	
	if($lastfm->query("//date")->length==0) $date=0;
	else $date = $lastfm->query("//date")->item(0)->getAttribute('uts');
	$diff = time()-$date;
	if($diff<3600) {
		$profil = $lastfm->query("//recenttracks")->item(0)->getAttribute('user');
		$track  = $lastfm->query("//name")->item(0)->nodeValue;
		$album  = $lastfm->query("//album")->item(0)->nodeValue;
		$artist = $lastfm->query("//artist")->item(0)->nodeValue;
		$link   = $lastfm->query("//url")->item(0)->nodeValue;
		
		$html = file_get_contents($link);
		$data = new DOMDocument();
		@$data->loadHTML($html); // Avoid parse errors
		$lastfm = new Domxpath($data);
		
		if($lastfm->query("//div[@class='albumFull']//img")->length!=0)	$icon = $lastfm->query("//div[@class='albumFull']//img")->item(0)->getAttribute('src');
		else $icon='http://cdn.last.fm/depth/catalogue/noimage/cover_85px.gif';
		return '
		<h4>j\'ai écouté</h4>
		<img class="lastfm" src="'.$icon.'" alt="'.$album.'" />
		<p class="lastfm">'.$track.'</p>
		<p class="lastfm"><a href="'.$link.'" title="Plus d\'informations sur '.$artist.'">'.$artist.'</a></p>
		<a class="profil" href="http://www.last.fm/user/'.$profil.'" title="Mon profil sur Last.fm"><img src="http://www.k1der.net/blog/country/wp-content/themes/country-new/images/lastfm.gif" alt="Last.fm" /></a>
		';
	}
	// Wakoopa
	$html = file_get_contents('http://wakoopa.com/Country/feed/recently_used');
	$data = new DOMDocument();
	@$data->loadXML($html); // Avoid parse errors
	$wakoopa = new Domxpath($data);
	$i=0;
	
	$profil = $wakoopa->query("//channel/link")->item(0)->nodeValue;
	$name = $wakoopa->query("//item/title")->item(0)->nodeValue;
	if($name=='Windows Explorer') $i++;
	$name = str_replace(array('Microsoft','Windows'),'',$wakoopa->query("//item/title")->item($i)->nodeValue);
	if(strlen($name)>16) $size=' style="font-size:.7em;"';
	else $size='';
	$link = $wakoopa->query("//item/link")->item($i)->nodeValue;
			
	$html = @file_get_contents($link);
	if(!$html) return false;
	$data = new DOMDocument();
	@$data->loadHTML($html); // Avoid parse errors
	$wakoopa = new Domxpath($data);
	
	$icon = $wakoopa->query("//div[@id='titlenav']//img")->item(0)->getAttribute('src');
	if(!ereg('http://static.wakoopa.com/',$icon)) $icon='http://static.wakoopa.com'.$icon;
	return '
	<h4>j\'ai utilisé</h4>
	<img class="wakoopa" src="'.$icon.'" alt="'.$name.'" />
	<p class="wakoopa"><a href="'.$link.'"'.$size.' title="Plus d\'informations sur '.$name.'">'.$name.'</a></p>
	<a class="profil" href="'.$profil.'" title="Mon profil sur Wakoopa"><img src="http://www.k1der.net/blog/country/wp-content/themes/country-new/images/wakoopa.png" alt="Wakoopa" /></a>
	';
}

function rightnow_path() {
	$url = explode('?',$_SERVER["REQUEST_URI"]);
	return 'http://' . $_SERVER['HTTP_HOST'] . $url[0];
}

function rightnow_siteurl() {
	$_file  = pathinfo(rightnow_path(), PATHINFO_BASENAME);
	$_siteurl = substr(rightnow_path(), 0, -1 * strlen($_file));
	// for when we're in the backend
	$_siteurl = str_replace('wp-admin/', '', rightnow_path());
	$_siteurl = str_replace('rightnow.php', '', $_siteurl);
	
	return $_siteurl;
}

function rightnow_add_pages() {
	if (function_exists('add_options_page')) {
		// Lorelle told me to
		add_submenu_page('plugins.php', 'Right Now', 'Right Now', 8, basename(__FILE__), 'rightnow_options_page');
	}
}

function rightnow_options_page() {

######################################################################
# direct calls to this script with a $_POST['ut'] and a $_POST['ua'] #
# are an image upload for a missing cover                            #
######################################################################

$_fadingstatus = false;
if ((array_key_exists('ut', $_POST)) && (array_key_exists('ua', $_POST))) {
if (!isset($_FILES['uf']['tmp_name'])) {
$_fadingstatus = 1;
} else {
# let's restrict the images to jpg, gif and png
$_ext = pathinfo($_FILES['uf']['name']);
$_ext = $_ext['extension'];
if (!in_array($_ext, array('jpg', 'gif', 'png'))) {
$_fadingstatus = 2;
} else {
$_newname = base64_encode($_POST['ua'] . '#_#' . $_POST['ut']) . '.' . $_ext;
if (!move_uploaded_file($_FILES['uf']['tmp_name'], $this->cachedir() . $_newname)) {
$_fadingstatus = 1;
} else {
$_array = array('image'      => $this->siteurl() . 'wp-content/plugins/last.fm/cache/' . $_newname,
'cdtitle'    => $_POST['ut'],
'artist'     => $_POST['ua']
);
$this->writecachefile($_array, $this->cachefile_albumdata($_POST['ut'], $_POST['ua']));
$_fadingstatus = 4;
}
}
}
}

# Get our options and see if we're handling a form submission.
$options = get_option('rightnow');
if (!is_array($options) ) {
$options = array('title'      => 'last.fm records',
'username'   => '',
'count'      => '6',
'imgwidth'   => '85',
'noimages'   => 'No images to display',
'period'     => 'weekly',
'htmlbefore' => '',
'htmlafter'  => '');
}

if (array_key_exists('lastfm-submit', $_POST)) {
# $options['title']       = strip_tags(stripslashes($_POST['lastfm-title']));
$options['username']    = strip_tags(stripslashes($_POST['lastfm-username']));
$options['imgwidth']    = intval($_POST['lastfm-imgwidth']);
if ($options['imgwidth'] < 10) {
$options['imgwidth'] = 0;
}

$options['count']       = intval($_POST['lastfm-count']);
if ($options['count'] < 1) {
$options['count'] = 6;
}

$options['display']     = strip_tags(stripslashes($_POST['lastfm-display']));
$options['noimages']    = strip_tags(stripslashes($_POST['lastfm-noimages']));
$options['period']      = strip_tags(stripslashes($_POST['lastfm-period']));
$options['localthumbs'] = strip_tags(stripslashes($_POST['lastfm-localthumbs']));

update_option('lastfm-records', $options);

$_fadingstatus = 3;
}

# $_fadingstatus set?
if ($_fadingstatus) {
switch($_fadingstatus) {
case 1:
echo "<div id='message' class='updated fade'><p>File upload failed.</p></div>";
break;
case 2:
echo "<div id='message' class='updated fade'><p>This file type is not supported.</p></div>";
break;
case 3:
echo "<div id='message' class='updated fade'><p>The options for Last.Fm Records have been updated.</p></div>";
break;
case 4:
echo "<div id='message' class='updated fade'><p>The cd cover has been uploaded!</p></div>";
break;
}
}

# html for options page
$url = explode('?',$_SERVER["REQUEST_URI"]);
?>
<div class="wrap">
<h2>Right Now Options</h2>
<form method=post action="<?php echo $url[0]; ?>?page=rightnow.php">
<input type="hidden" name="update" value="true">
<fieldset class="options">
<table class="optiontable"> 
<tr valign="top"> 
<th scope="row">Steam template</th> 
<td>
<textarea id="rightnow-steamtemplate" name="rightnow-steamtemplate" cols="60" rows="5"><?php echo $options['steam-template']; ?></textarea><br />
Available variables : [icon], [name], [link], [profile], [join].
</td>
</tr>
<tr valign="top"> 
<th scope="row">Wakoopa template</th>
<td>
<textarea id="rightnow-wakoopatemplate" name="rightnow-wakoopatemplate" cols="60" rows="5"><?php echo $options['wakoopa-template']; ?></textarea><br />
Available variables : [icon], [name], [link], [profile].
</td>
</tr>
<tr valign="top"> 
<th scope="row">Last.fm template</th>
<td>
<textarea id="rightnow-lastfmtemplate" name="rightnow-lastfmtemplate" cols="60" rows="5"><?php echo $options['lastfm-template']; ?></textarea><br />
Available variables : [track], [album], [artist], [icon], [link], [profile].
</td>
</tr>
</table>
<p class="submit">
<input type="submit" name="lastfm-submit" value="Update Options &raquo;" />
</p>
</fieldset>
</form>
<?
# missing cd covers?
$_missing = false;
if ($_missing) {
?>

<script type="text/javascript">
//<![CDATA[
function showupload(i) {
var el = document.getElementById('upload' + i);
if (el) {
if ('none' == el.style.display) {
el.style.display = '';
} else {
el.style.display = 'none';
}
}
return false;
}
//]]>
</script>

<h2>Upload Missing Covers</h2>
<table class="optiontable"> 
<? echo $_missing; ?>
</table>
<?
}
?>
</div>
<?
}

// this function gets called when widgets are supported
function widget_rightnow_init() {
	
	// does this wordpress environment support widgets?
	if (!function_exists('register_sidebar_widget')) return;
	
	function widget_rightnow() {
		echo '<div id="now">';
		echo '<h3>Juste à l\'instant</h3>';
		echo rightnow_display();
		echo '</div>';
		echo '<div id="sidebar"><ul>';
	}
	
	function widget_rightnow_control() {
		$options = get_option('rightnow');
		
		if (($_POST['rightnow-submit'])) {
			$options['steam']   = strip_tags(stripslashes($_POST['rightnow-steam']));
			$options['wakoopa'] = strip_tags(stripslashes($_POST['rightnow-wakoopa']));
			$options['lastfm']  = strip_tags(stripslashes($_POST['rightnow-lastfm']));
			update_option('rightnow', $options);
		}
		
		$steam  = htmlspecialchars($options['steam'], ENT_QUOTES);
		$akoopa = htmlspecialchars($options['wakoopa'], ENT_QUOTES);
		$lastfm = htmlspecialchars($options['lastfm'], ENT_QUOTES);
		?>
		<p style="text-align:right;">
		<label for="rightnow-steam">Steam ID: 
		<input style="width: 200px;" id="rightnow-steam" name="rightnow-steam" type="text" value="<?php echo $steam; ?>" />
		</label>
		</p>
		<p style="text-align:right;">
		<label for="rightnow-wakoopa">Wakoopa ID: 
		<input style="width: 200px;" id="rightnow-wakoopa" name="rightnow-wakoopa" type="text" value="<?php echo $wakoopa; ?>" />
		</label>
		</p>
		<p style="text-align:right;">
		<label for="rightnow-lastfm">Last.fm ID: 
		<input style="width: 200px;" id="rightnow-lastfm" name="rightnow-lastfm" type="text" value="<?php echo $lastfm; ?>" />
		</label>
		</p>
		<p>Other options are on the <a href="<?= rightnow_siteurl(); ?>wp-admin/plugins.php?page=rightnow.php">options page</a> for this plugin.</p>
		<input type="hidden" id="rightnow-submit" name="rightnow-submit" value="1" />    
		<?php
	}
	
	
	// if you want to use it as a widget, it's available
	register_sidebar_widget('Right Now', 'widget_rightnow');
	
	register_widget_control('Right Now', 'widget_rightnow_control', 375, 135);
	
}

// widget variant
add_action('plugins_loaded', 'widget_rightnow_init');

// one place were we can change the settings
add_action('admin_menu', 'rightnow_add_pages');
?>