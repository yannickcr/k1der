===================================================================
WP-Amazon plugin for Wordpress 2.1+
Version 2.0 Beta 1.1  $Rev: 7839 $ $Date: 2007-02-06 08:34:53 -0800 (Tue, 06 Feb 2007) $
Author: Rich Manalang
Home: http://manalang.com/wp-amazon/
Support: http://groups.google.com/group/wp-amazon

Copyright (C) 2005-2007 Rich Manalang

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
USA
===================================================================

Version History
---------------
2.0 Beta 1.1 - Fixed wp-amazon.php
2.0 Beta 1 -  Complete rewrite.
		KNOWN ISSUES: 
			1.	This release only works on Firefox 2, IE 6, and Opera 9.  
				Safari will be supported... but in future releases.
			2. 	Drag and drop of images/links only work on Firefox 2.
				To copy an image/link to the visual editor in other
				browsers, use the browser's copy/paste command.
			3.	Most of the emphasis with this release is to support
				the visual editor and so, the standard text based
				editor is not really working... when you copy an image
				into it, it just gives you the URL, not the tags.  This
				will be supported in future releases, but not this one.
1.3.2 - Reverted to using a hyperlink below the post textarea to launch
        WP-Amazon... too many people complained about it not working
        when a WYSIWYG plugin was enabled.
1.3.1 - Amazon changed their XML format which prevented WP-Amazon from
        displaying images returned from results.  Thanks go to Nick Walton
        (http://www.nickwalton.com/) for finding and reporting the problem.
1.3   - Added plugin options page so users can update the default country, 
        associate ID, and subscription ID without modifying the plugin source.
      - Added a "check for update" feature that allows users to easily see if 
        their plugin is up-to-date.
      - Dropped support for 1.2.x
1.2.8 - Changed the Amazon Link on the edit page to be a button in the
        Quicktags region.  Allows for WP-Amazon to work with the Tiger
        Style Administration plugin.
1.2.7 - Added some inline CSS to hide the admin header
1.2.6 - Added Added Michiel Maandag's enhancements 
        (http://wordpress.org/support/topic/32412)
1.2.5 - Fixed code that checks if curl is installed.  By default,
        curl is preferred, but if your host doesn't have it installed,
        it downgrades to using fopen.
1.2.4 - Added support for WordPress 1.5.  Still supports 1.2.x.
      - Relocated wp-amazon.php to the plugins directory.
1.2.3 - Added support for Amazon ECS 4.0 France and Canada
1.2.2 - Added check to see if magic quotes is turned on or off.  This was
        causing a JavaScript problem with servers that had magic quotes turned
        off.
1.2.1 - Replaced htmlentities to htmlspecialchars to support special characters
        in Japanese and German
1.2   - Upgraded to Amazon E-Commerce Service 4.0
      - Removed dependency on NuSOAP and moved to using standard PHP XML support
      - Added ability to change the Amazon country site to search form
1.1   - Cleanup
1.0   - David Schlosnagle's blended search has been included (thanks David!)
0.92  - Removed target="_blank" and properly encodes entities to preserve
        XHTML compliance.
0.91  - Reworked search features. Removed search by and replaced with product
        line search field.  All searches are now by product.
      - Added support for Amazon UK, Amazon Germany, and Amazon Japan. Needs
        to be thoroughly tested still.       
0.9   - Initial release

About
-----
WP-Amazon adds the ability to search and include items from 
Amazon.com to your WordPress post entries.

Features
--------
* Uses Amazon E-Commerce Service 4.0
* Supports the following countries:
  - United States
  - Great Britain
  - Germany
  - Japan
  - France
  - Canada
* Support for Amazon Associates IDs.  For more information about
  the Amazon Associates Program, visit this site:
  http://www.amazon.com/gp/browse.html/104-9918661-1983944?node=3435371.
  To change the default Amazon Associates ID, change the Associates ID option
  under Options > Amazon.
* Results display a thumbnail image of the product and the product
  name.  
* Supports copying Amazon images and links to the WP visual editor
* Supports search across all Amazon product search indices (aka, blended results)
* Supports searching across multiple Amazon country sites


Installation
------------
* Extract the wp-amazon2.0.zip file to your wp-content\plugins
  directory
* Go to the WordPress plugins admin panel then activate the
  WP-Amazon plugin.
* The post edit page should now have a button on the right hand side
  of the Write/Edit Post and Write/Edit Page pages


Configuration
-------------
There's really no configuration needed after installation.  If
you have an Amazon Associates Program account and would like to
use it, go to Options > Amazon to add your Associate ID.


Questions/Suggestions/Bugs
--------------------------
Please go to http://groups.google.com/group/wp-amazon
