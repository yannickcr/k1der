<?php
if ( ! empty($tag_ID) ) {
	$heading = __('Edit Tag');
	$submit_text = __('Edit Tag');
	$form = '<form name="edittag" id="edittag" method="post" action="edit-tags.php" class="validate">';
	$action = 'editedtag';
	$nonce_action = 'update-tag_' . $tag_ID;
	do_action('edit_tag_form_pre', $tag);
} else {
	$heading = __('Add Tag');
	$submit_text = __('Add Tag');
	$form = '<form name="addtag" id="addtag" method="post" action="edit-tags.php" class="add:the-list: validate">';
	$action = 'addtag';
	$nonce_action = 'add-tag';
	do_action('add_tag_form_pre', $tag);
}
?>

<div class="wrap">
<h2><?php echo $heading ?></h2>
<div id="ajax-response"></div>
<?php echo $form ?>
<input type="hidden" name="action" value="<?php echo $action ?>" />
<input type="hidden" name="tag_ID" value="<?php echo $tag->term_id ?>" />
<?php wp_original_referer_field(true, 'previous'); wp_nonce_field($nonce_action); ?>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name"><?php _e('Tag name') ?></label></th>
			<td><input name="name" id="name" type="text" value="<?php echo attribute_escape($tag->name); ?>" size="40" aria-required="true" />
            <p><?php _e('The name is how the tag appears on your site.'); ?></p></td>
		</tr>
	</table>
<p class="submit"><input type="submit" class="button" name="submit" value="<?php echo $submit_text ?>" /></p>
<?php do_action('edit_tag_form', $tag); ?>
</form>
</div>
