=== Plugin Name ===
Contributors: a-bishop
Donate link: http://a-bishop.spb.ru/wordpress/
Tags: comments, userpic, gravatar, livejournal
Requires at least: 2.0.5
Tested up to: 2.1.3
Stable tag: 1.2

This plugin allows you to generate a gravatar URL. It also shows LiveJournal userpics in comments.

== Description ==

This plugin allows you to generate a gravatar URL complete with rating, size, default, and border options. See the documentation for syntax and usage: http://www.gravatar.com/implement.php#section_2_2. 
It also shows LiveJournal userpics in comments.

== Installation ==

Place gravatar.php to your plugins folder and activate it in control panel.
Place /gravatars/ directory to your plugins folder and CHMOD it 777. 

In comments.php add the following string where it is needed:
<?php if (function_exists('gravatar')) { ?><img vspace=5 src="<?php gravatar("X", 80, ""); ?>" class="gravatar" alt="Gravatar Icon" /><?php } ?>
