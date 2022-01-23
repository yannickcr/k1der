<?php
/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.4
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Time: Wed, 04 May 2005 15:17:58 GMT
|   Release: 303bba6732d809e44cd52a144ab90a4b
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > IPB UPGRADE TEMPLATE MODULES
|   > Script written by Matt Mecham
|   > Date started: 23rd April 2004
+--------------------------------------------------------------------------
*/

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class template
{
	var $content  = "";

	function output($title='')
	{
		print $this->print_top($title);
		print $this->content;
		print "<br><br><br><br><center><span id='copy'><a href='http://www.invisionboard.com'>Invision Power Board</a> &copy; 2004 <a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a></span></center>
			   </div></body>
			   </html>";
		exit();
	}

	//--------------------------------------

	function print_top($title="")
	{

		return "<html>
		          <head><title>Invision Power Board Upgrade System :: $title </title>
		          <style type='text/css'>

		          	BODY
		          	{
		          		font-size: 11px;
		          		font-family: Verdana, Arial;
		          		color: #000;
		          		margin: 0px;
		          		padding: 0px;
		          		background-image: url(../install/img/fadebg.jpg);
		          		background-repeat: no-repeat;
		          		background-position: right bottom;
		          	}

		          	TABLE, TR, TD     { font-family:Verdana, Arial;font-size: 11px; color:#000 }

					a:link, a:visited, a:active  { color:#000055 }
					a:hover                      { color:#333377;text-decoration:underline }

					.centerbox { margin-right:10%;margin-left:10%;text-align:left }

					.warnbox {
							   border:1px solid #F00;
							   background: #FFE0E0;
							   padding:6px;
							   margin-right:10%;margin-left:10%;text-align:left;
							 }

					.tablepad    { background-color:#F5F9FD;padding:6px }
				    .description { color:gray;font-size:10px }
					.pformstrip { background-color: #D1DCEB; color:#3A4F6C;font-weight:bold;padding:7px;margin-top:1px;text-align:left }
					.pformleftw { background-color: #F5F9FD; padding:6px; margin-top:1px;width:50%; border-top:1px solid #C2CFDF; border-right:1px solid #C2CFDF; }
					.pformright { background-color: #F5F9FD; padding:6px; margin-top:1px;border-top:1px solid #C2CFDF; }

					.tableborder { border:1px solid #345487;background-color:#FFF; padding:0px; margin:0px; width:100% }

					.maintitle { text-align:left;vertical-align:middle;font-weight:bold; color:#FFF; letter-spacing:1px; padding:8px 0px 8px 5px; background-image: url(../install/img/tile_back.gif) }
					.maintitle a:link, .maintitle  a:visited, .maintitle  a:active { text-decoration: none; color: #FFF }
					.maintitle a:hover { text-decoration: underline }

					#copy { font-size:10px }

					#button   { background-color: #4C77B6; color: #FFFFFF; font-family:Verdana, Arial; font-size:11px }

					#textinput { background-color: #EEEEEE; color:Ê#000000; font-family:Verdana, Arial; font-size:11px; width:100% }

					#dropdown { background-color: #EEEEEE; color:Ê#000000; font-family:Verdana, Arial; font-size:10px }

					#multitext { background-color: #EEEEEE; color:Ê#000000; font-family:Courier, Verdana, Arial; font-size:10px }

					#logostrip {
								 padding: 0px;
								 margin: 0px;
								 background: #7AA3D0;
							   }

					.fade
					{
						background-image: url(../install/img/fade.jpg);
						background-repeat: repeat-x;
					}

				  </style>
				  </head>
				 <body marginheight='0' marginwidth='0' leftmargin='0' topmargin='0' bgcolor='#FFFFFF'>

				 <div id='logostrip'><img src='../install/img/title.gif' border='0' alt='Invision Power Board Installer' /></div>
				 <div class='fade'>&nbsp;</div>
				 <br />
				 <div style='padding:10px'>
				 ";

	}
}
?>