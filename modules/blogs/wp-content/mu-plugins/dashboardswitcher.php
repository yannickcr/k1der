<?php

add_action( 'admin_print_scripts', 'switcher_scripts' );

function switcher_scripts() {
	wp_enqueue_script('jquery');
}


function switcher_css() {
?>
<style type="text/css">
#switchermenu a {
	font-size: 20px;
	padding: 0 1.5em 0 10px;
	display: block;
	color: #c3def1;
}

#switchermenu a:hover {
	background: #1a70b4;
	color: #fff;
}

#switchermenu li {
	margin: 0;
	padding: 2px;
}

#switchermenu {
	display: none;
	list-style: none;
	margin: 0;
	padding: 0;
	overflow: hidden;
	border-top: 1px solid #1a70b4;
	border-left: 1px solid #1a70b4;
	position: absolute;
	left: 0;
	top: 1em;
	background: #14568a;
	z-index: 1;
}
</style>
<script type="text/javascript">
jQuery( function($) {
var switchTime;
var w = false;
var h = $( '#blog-title' )
	.css({
		background: 'transparent url( ../wp-content/mu-plugins/bullet_arrow_down.gif ) no-repeat scroll 100% .2em;',
		padding: '0 25px 2px 5px',
		cursor: 'pointer',
		border: '1px solid #14568a',
	})
	.parent().css( { position: 'relative' }).end()
	.append( $('#switchermenu') )
	.hover( function() {
		$(this).css({ border: '1px solid #1a70b4'});
		switchTime = window.setTimeout( function() {
			$('#switchermenu').fadeIn('fast').css( 'top', h ).find('a').width( w = w ? w : $('#switchermenu').width() );
		}, 300 );
	}, function() {
		window.clearTimeout( switchTime );
		$(this).css({ border: '1px solid #14568a' }) ;
		$('#switchermenu').hide();
	})
	.height() - 3;
});
</script>
<?php
}
add_action( "admin_head", "switcher_css" );

function add_switcher() {
	global $current_user;
	$out = '<h1><span id="blog-title">' . wptexturize(get_bloginfo(("name"))) . '</span><span id="viewsite">(<a href="' . get_option("home") . "/" . '">' . __("View site &raquo;") . '</a>)</span></h1>';
	$out .= '<ul id="switchermenu">';
	$blogs = get_blogs_of_user($current_user->ID);
	if ( ! empty($blogs) ) foreach ( $blogs as $blog ) {
		$out .= '<li><a href="http://' . $blog->domain . $blog->path . 'wp-admin/">' . $blog->blogname . '</a></li>';
	}
	$out .= "</ul>";
	?>
	<script type="text/javascript">
	document.getElementById('wphead').innerHTML = '<?php echo $out ?>'
	</script>
	<?php
}
add_action( 'admin_footer', 'add_switcher' );

?>
