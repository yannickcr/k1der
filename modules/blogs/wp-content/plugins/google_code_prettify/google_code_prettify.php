<?php
/*
Plugin Name: Google Code Prettify
Plugin URI: http://www.deanlee.cn/wordpress/google-code-prettify-for-wordpress/
Description: this plugin using <a href="http://code.google.com/p/google-code-prettify/">google-code-prettify</a> to highlight source code in your posts. 
Author: Dean Lee
Version: 1.0
Author URI: http://www.deanlee.cn
*/

function dean_insert_code_prettify_head() {
	$current_path = get_option('siteurl') .'/wp-content/plugins/' . basename(dirname(__FILE__)) .'/';
	?>
	<link href="<?php echo $current_path; ?>prettify.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo $current_path; ?>prettify.js"></script>
	<?php
}

function dean_insert_code_prettify_foot(){
	?>
	<script type="text/javascript">
		window.onload = function(){prettyPrint();};
	</script>
<?php
}
add_action('wp_head','dean_insert_code_prettify_head');
add_action('get_footer','dean_insert_code_prettify_foot');
?>