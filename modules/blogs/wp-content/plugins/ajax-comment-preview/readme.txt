=== Ajax Comment Preview ===
Tags: ajax, preview, comment, comments
Contributors: mdawaffe
Requires at least: 2.1
Tested up to: 2.2.1
Stable Tag: 1.2.1

Visitors to your site can preview their comments with a click of a button.

== Description ==

Other preview plugins don't know what sort of changes WordPress will make to a
visitor's comment, but this plugin uses AJAX to send each previewed comment
through WordPress' inner voodoo in order to figure out those changes.

The result?  With the click of a button, your site's visitors can preview their
comment *exactly* as it will appear when they submit it.

== Installation ==

1. Upload the plugin to your plugins folder: `wp-content/plugins/`
2. Activate the 'Ajax Comment Preview' plugin from the Plugins admin panel.
3. Go to the Options -> Ajax Comment Preview admin panel to configure the look
   of the preview.

== Frequently Asked Questions ==

= How do I change the look of the preview? =

Go to the Options -> Ajax Comment Preview admin panel.  From there you'll be
able to specify the markup used to display the comment being previewed.  The
markup you enter will depend on what theme your site is using.  If you're using
Kubrick (the default theme for WordPress), the settings that come installed
with the plugin will work fine.  For other themes, I suggest the following.

1. Go to the permalink page for a post on your site that has a few comments.
2. In your web browser, view the Page Source of that page.  You can usually do
   this by finding that option in your browsers Edit or View menu or in the menu
   that pops up when you right click on the page.
3. Find the section of code that corresponds to one of the comments.  Copy it
   into your clipboard.
4. Paste that code into the big text box in the Options -> Ajax Comment Preview
   admin panel.
5. Replace the text specific to that comment (author name, time, comment text,
   ...) with the plugin's special tags (`%author%`, `%date%`, `%content%`, ...).
6. Most themes' code has all the comments inside one big `<ol>`, `<ul>`, or `<div>`
   tag.  You'll probably need to put your preview markup inside that
   "parent" tag too.  Make sure it has the same class(es) as the tag in your
   theme's code.
