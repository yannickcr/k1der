<?php

$LOFISKIN['wrapper'] = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="robots" content="index,follow" />
	<link rel="stylesheet" rev="stylesheet" href="<% FULL_URL %>lofiscreen.css" media="screen" />
	<link rel="stylesheet" rev="stylesheet" href="<% FULL_URL %>lofihandheld.css" media="handheld" />
	<link rel="stylesheet" rev="stylesheet" href="<% FULL_URL %>lofiprint.css" media="print" />
	<title><% TITLE %></title>
</head>
<body>
<div id='ipbwrapper'>
  <div class='ipbnavsmall'>
   <a href='{$ibforums->base_url}act=Help'>Help</a> -
   <a href='{$ibforums->base_url}act=Search'>Search</a> -
   <a href='{$ibforums->base_url}act=Members'>Member List</a> -
   <a href='{$ibforums->base_url}act=calendar'>Calendar</a>
  </div>
  <div id='largetext'>Full Version: <a href='<% LINK %>'><% LARGE_TITLE %></a></div>
  <div class='ipbnav'><% NAV %></div>
  <% PAGES %>
  <div id='ipbcontent'>
  <% CONTENT %>
  </div>
  <div class='smalltext'>This is a "lo-fi" version of our main content. To view the full version with more information, formatting and images, please <a href='<% LINK %>'>click here</a>.</div>
</div>
<div id='ipbcopyright'><% COPYRIGHT %></div>
</body>
</html>
EOF;


function LOFISKIN_forums($forums="") {
return <<<EOF
<div class='forumwrap'>
<ul>
$forums
</ul>
</div>
EOF;
}

function LOFISKIN_forums_entry($depth_guide, $forum_data, $win_path="" ) {
return <<<EOF
\n{$depth_guide}<li><a href='{$win_path}f{$forum_data['id']}.html'>{$forum_data['name']}</a> <span class='desc'>({$forum_data['total_posts']} posts)</span></li>
EOF;
}


function LOFISKIN_forums_entry_end($depth_guide) {
return <<<EOF
\n{$depth_guide}</ul></li>
EOF;
}

function LOFISKIN_forums_entry_start($depth_guide) {
return <<<EOF
\n{$depth_guide}<li><ul>
EOF;
}

function LOFISKIN_forums_entry_first($forum_data, $win_path="") {
return <<<EOF
\n<li><strong><a href='{$win_path}f{$forum_data['id']}.html'>{$forum_data['name']}</a></strong></li>\n<ul>
EOF;
}


function LOFISKIN_topics($topics="") {
return <<<EOF
<div class='topicwrap'>
<ol>
$topics
</ol>
</div>
EOF;
}

function LOFISKIN_topics_entry($r, $win_path="") {
return <<<EOF
\n<li>{$r['_prefix']}<a href='{$win_path}t{$r['tid']}.html'>{$r['title']}</a> <span class='desc'>({$r['posts']} replies)</span></li>
EOF;
}

function LOFISKIN_posts_entry($r) {
return <<<EOF
<div class='postwrapper'>
 <div class='posttopbar'>
  <div class='postname'>{$r['author_name']}</div>
  <div class='postdate'>{$r['post_date']}</div>
 </div>
 <div class='postcontent'>
  {$r['post']}
 </div>
</div>
EOF;
}

function LOFISKIN_pages($pages="") {
return <<<EOF
<div class='ipbpagespan'>
Pages: $pages
</div>
EOF;
}

?>