<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
 <head profile="http://gmpg.org/xfn/11">
  <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
  <title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
  <meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
  <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
  <!--[if IE]><link rel="stylesheet" href="http://www.k1der.net/blog/country/wp-content/themes/country-new/style-ie.css" type="text/css" media="screen" /><![endif]-->
  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>

 </head>
 <body>
  <h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?><span></span></a></h1>
  <ul id="prelude">
   <li><a href="#menu">Aller au menu</a></li>
   <li><a href="#content">Aller au contenu</a></li>
   <li><a href="#search">Aller à la recherche</a></li>
  </ul>
  <?php include (TEMPLATEPATH . "/searchform.php"); ?>
  <h2 class="menu">Menu</h2>
  <ul id="menu">
   <li><a href="<?php echo get_option('home'); ?>/">Le Blog</a></li>
   <li><a href="<?php echo get_option('home'); ?>/a-propos/">A propos</a></li>
   <li><a href="<?php echo get_option('home'); ?>/contact/">Contact</a></li>
  </ul>
  <div id="main">
