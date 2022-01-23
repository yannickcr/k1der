<?php
/*
Plugin Name: AJAX Comment Preview
Plugin URI: http://blogwaffe.com/ajax-comment-preview/
Description:  Click Button Coment Preview which filters content through WordPress filters.  Inspired by Bitflux GmbH via Jeff Minard.
Version: 1.2.1
Author: Michael D Adams
Author URI: http://blogwaffe.com/
*/

class Ajax_Comment_Preview {

	function version() { return '1.2'; }

	function init() {
		add_option( 'ajax_comment_preview', array(
			'template' => "<ul class='commentlist'>\n\t<li class='alt'>\n\t<cite>%author%</cite> Says:<br />\n\t<small class='commentmetadata'><a href='#'>%date%</a></small>\n\t%content%\n\t</li>\n</ul>",
			'date_format' => 'F jS, Y \a\t g:i a',
			'empty_string' => 'Click the "Preview" button to preview your comment here.',
			'button_value' => 'Preview',
			'ver' => time()
		));
	}

	function wp_print_scripts() {
		if ( !is_single() && !is_page() || !comments_open() )
			return;
		extract(get_option( 'ajax_comment_preview' ));
		wp_enqueue_script( 'ajax_comment_preview', Ajax_Comment_Preview::htmldir() . '/ajax-comment-preview-js.php', array('sack'), Ajax_Comment_Preview::version() . $ver );
	}

	function comment_form() {
		$preview_vars = get_option( 'ajax_comment_preview' );
		//echo '<input name="acp-preview" type="button" id="acp-preview" tabindex="6" value="' . attribute_escape( $preview_vars['button_value'] ) . '" />';
		echo '<div id="ajax-comment-preview"></div>';
	}

	function send() {
		global $user_ID, $user_url, $user_identity, $user_email;
		$author	= trim($_POST['author']);
		if (!$author) $author = 'Anonymous';
		$url	= trim($_POST['url']);
		$text	= trim($_POST['text']);
		$email = trim($_POST['email']);

		get_currentuserinfo();
		if ( $user_ID ) :
			$author	= addslashes($user_identity);
			$url	= addslashes($user_url);
			$email  = addslashes($user_email);
		endif;

		$text = apply_filters('pre_comment_content', $text);
		$text = apply_filters('post_comment_text', $text); // Deprecated
		$text = apply_filters('comment_content_presave', $text); // Deprecated
		$text = stripslashes($text);
		$text = apply_filters('get_comment_text', $text);
		$text = apply_filters('comment_text', $text);

		$author = apply_filters('pre_comment_author_name', $author);
		$author = stripslashes($author);
		$author = apply_filters('get_comment_author', $author);

		$email = apply_filters('pre_comment_author_email', $email);
		$email = stripslashes($email);
		$email = apply_filters('get_comment_author_email', $email);

		if ( $url && 'http://' !== $url ) :
			$url = apply_filters('pre_comment_author_url', $url);
			$url = stripslashes($url);
			$url = apply_filters('get_comment_url', $url);
			$author = '<a href="' . $url . '" rel="external">' . $author . '</a>';
			$author = apply_filters('get_comment_author_link', $author);
			$author = apply_filters('comment_author_link', $author);
		endif;
		$preview_vars = get_option( 'ajax_comment_preview' );
		$preview_vars['template'] = str_replace(
			array('%author%', '%date%', '%content%', '%email%'),
			array($author, date_i18n($preview_vars['date_format'], time() + get_settings('gmt_offset') * 3600 - date('Z')), $text, $email),
			$preview_vars['template']
		);

		if ( false !== strpos($preview_vars['template'], '%email_hash%') )
			$preview_vars['template'] = str_replace('%email_hash%', md5($email), $preview_vars['template']);

		return $preview_vars['template'];
	}

	//Only works for files in wp-content
	function htmldir() {
		$realdir = dirname(realpath(__FILE__));
		return get_option( 'siteurl' ) . strstr($realdir, '/wp-content');
	}

	function admin_menu() {
		add_options_page( 'AJAX Comment Preview', 'AJAX Comment Preview', 'manage_options', 'acp-admin', array('Ajax_Comment_Preview', 'admin_page') );
	}

	function admin_page() {
		if ( isset($_POST['ajax_comment_preview_options_submit']) ) {
			check_admin_referer( 'ajax_comment_preview' );
			$ajax_comment_preview_options = stripslashes_deep($_POST['acp']);
			if ( !$ajax_comment_preview_options['button_value'] )
				$ajax_comment_preview_options['button_value'] = 'Preview';
			$ajax_comment_preview_options['ver'] = time();
			update_option( 'ajax_comment_preview', $ajax_comment_preview_options );
			echo '<div class="updated fade"><p>Ajax Comment Preview options updated.</p></div>';
		}
		extract(get_option( 'ajax_comment_preview' )); ?>
<style type="text/css">
.acp-focusable:focus {background-color: #ffc}
dl { margin-left: 3em }
</style>
<div class="wrap">
<h2>Ajax Comment Preview Options</h2>
<form method="post">
<fieldset>
	<div>
		<p>Enter the markup from your theme's comment template here.  The following special tags are available.</p>
		<dl>
			<dt>%author%</dt>
			<dd>The name of the comment author linked to the comment author's url.</dd>
			<dt>%date%</dt>
			<dd>The date formatted as <a><label for="date-format">below</label></a>.</dd>
			<dt>%content%</dt>
			<dd>The text of the comment.</dd>
			<dt>%email%</dt>
			<dd>The email of the comment author.</dd>
			<dt>%email_hash%</dt>
			<dd>The MD5 hash of the comment author's email address.  Useful for gravatars.</dd>
		</dl>
		<textarea name="acp[template]" class="acp-focusable widefat" rows="10"><?php echo attribute_escape( $template ); ?></textarea>

		<p>
			<label for="date-format"><a href="http://codex.wordpress.org/Formatting_Date_and_Time">Date format</a> of the date to be displayed in the preview.<br />
			<input name="acp[date_format]" id="date-format" class="acp-focusable" type="text" value="<?php echo attribute_escape( $date_format ); ?>" /></label>
		</p>

		<p>
			<label for="button-value">Text to appear on the Preview Button.<br />
			<input name="acp[button_value]" id="button-value" class="acp-focusable" type="text" value="<?php echo attribute_escape( $button_value ); ?>" /></label>
		</p>

		<p>
			<label for="empty-string">This text will appear in the preview area before the user previews the comment.  Leave blank to make the preview area initially invisible.<br />
			<input name="acp[empty_string]" id="empty-string" type="text" class="acp-focusable widefat" value="<?php echo attribute_escape( $empty_string ); ?>" /></label>
		</p>
	</div>
</fieldset>
<?php if ( function_exists( 'wp_nonce_field' ) ) wp_nonce_field( 'ajax_comment_preview' ); ?>
<p class="submit"><input type="submit" name="ajax_comment_preview_options_submit" value="Update Options &#187;" /></p>
</form>
</div>
<?php	}
}

if ( function_exists('add_action') ) :
	add_action('init', array('Ajax_Comment_Preview', 'init') );
	add_action('admin_menu', array('Ajax_Comment_Preview', 'admin_menu') );
	add_action('wp_print_scripts', array('Ajax_Comment_Preview', 'wp_print_scripts') );
	add_action('comment_form', array('Ajax_Comment_Preview', 'comment_form') );
endif;

if (  isset($_POST['text']) && $_POST['text'] ) :
	$wp_config = preg_replace('|wp-content.*$|','', __FILE__) . 'wp-config.php';
	require_once($wp_config);
	echo Ajax_Comment_Preview::send();
endif;
?>
