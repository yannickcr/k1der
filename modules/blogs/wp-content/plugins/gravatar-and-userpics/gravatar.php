<?php

/*
Plugin Name: Gravatar (and userpics)
Plugin URI: http://a-bishop.spb.ru/wordpress/
Description: This plugin allows you to generate a gravatar URL complete with rating, size, default, and border options. See the <a href="http://www.gravatar.com/implement.php#section_2_2">documentation</a> for syntax and usage. It also shows LiveJournal userpics in comments.
Version: 1.2
Author: Tom Werner, modified by Alexander Bishop
Author URI: http://a-bishop.spb.ru/

CHANGES
2007-06-26 readme.txt rewritten accordingly to WordPress.org plugins list.
2006-07-19 Added possibility to show LiveJournal userpics
2004-11-14 Fixed URL ampersand XHTML encoding issue by updating to use proper entity
*/

function gravatar($rating = false, $size = false, $default = false, $border = false) {
	global $comment;


/* Here we'll define if it is LJ user */

	$lj = explode("@", $comment->comment_author);
	$ljuser = $lj[0];

	if ($lj[1] == "livejournal") 
	{
		if(file_exists($_SERVER["DOCUMENT_ROOT"].'/wp-content/plugins/gravatars/'.$ljuser))
		{
			$out = 'http://'.$hostname = getenv("HTTP_HOST").'/wp-content/plugins/gravatars/'.$ljuser;
		}
		else 
		{
			$out = getuserpic($ljuser);
			/* Next string caches userpic to /gravarars/ folder.
			   Comment it if you do not have wget on server side */
			system("wget -O ".$_SERVER["DOCUMENT_ROOT"]."/wp-content/plugins/gravatars/$ljuser $out");
		}
//		else $out = 'http://'.$hostname = getenv("HTTP_HOST").'/wp-content/plugins/gravatars/blank';
	}

/* This is original code by Tom Werner */

	else 
	{
		$out = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($comment->comment_author_email);
		if($rating && $rating != '')
			$out .= "&amp;rating=".$rating;
		if($size && $size != '')
			$out .="&amp;size=".$size;
		if($default && $default != '')
			$out .= "&amp;default=".urlencode($default);
		if($border && $border != '')
			$out .= "&amp;border=".$border;
	}
	echo $out;
}

function getuserpic($ljuser)
{
	$url = "http://users.livejournal.com/$ljuser/profile/";
	$userinfo = file_get_contents($url);

	$starttoken="http://www.livejournal.com/allpics.bml?user=".str_replace("-","_",$ljuser)."'><img src='";
	$endtoken="'";
	$start=strpos($userinfo,$starttoken)+strlen($starttoken);

	if($start>strlen($starttoken))
	{
		$end=strpos($userinfo,$endtoken,$start);
		return substr($userinfo,$start,$end-$start);
	}
	else
	{
		return 'http://'.$hostname = getenv("HTTP_HOST").'/wp-content/plugins/gravatars/blank';
	}
}
?>
