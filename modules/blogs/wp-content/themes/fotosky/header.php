<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Archives du Blog <?php } ?> <?php wp_title(); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>
</head>

<body>
<center>
<div id="page">

<div id="header">
	<div id="header_top">
		<div id="header_title">
			<?php bloginfo('name'); ?><div><?php bloginfo('description'); ?></div>
		</div>
	</div>
	<div id="header_end">
		<div id="menu">
			<div id="menu_items">
				<div><a href="<?php bloginfo('home'); ?>">ACCUEIL</a></div><?php $my_query = new WP_Query('post_type=page&orderby=name&order=DSC'); ?>

<?php while ($my_query->have_posts()) : $my_query->the_post(); 

if( $post->post_parent == 0 )
{?>
					
					<div class="no_bg">&nbsp;</div><div><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
				
<?php } endwhile; 
?>
			</div>
			<div id="menu_search_box">
				<form method="get" id="searchform" style="display:inline;" action="<?php bloginfo('home'); ?>/">
				<span>CHERCHER:&nbsp;</span>
				<input type="text" class="s" value="<?php the_search_query(); ?>" name="s" id="s" />
				</form>
			</div>
		</div>
	</div>
</div>

<div id="blog">
	<div id="blog_left">
		<?php get_sidebar(); ?>
	</div>
	<div id="blog_center">