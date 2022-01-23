<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

		
### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms') && !current_user_can('track_cforms')) {
	die(__('Access Denied','cforms'));
}


// if all data has been erased quit
if ( get_option('cforms_formcount') == '' ){
	?>
	<div class="wrap">
	<h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	</div>
	<?php
	die;
}


//
// sorting of entries
//
if ( isset($_POST['order']) ) {

	$orderdir = $_POST['orderdir'];
	$order = $_POST['order'];

} else { 

	$orderdir = 'DESC';
	$order = 'sub_date';	

}



//
// delete checked entries
//
if ( (isset($_POST['delete'])) ) {

	$i=0;
	foreach ($_POST['entries'] as $entry) :
		$entry = (int) $entry;

		$fileval = $wpdb->get_row("SELECT DISTINCT field_val,form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id = '$entry' AND id=sub_id AND field_name LIKE '%[*]%'");
		$file = get_option('cforms'.$fileval->form_id.'_upload_dir').'/'.$entry.'-'.$fileval->field_val;
		
		$del='';
		if ( $fileval->field_val <> '' ){
			if ( file_exists( $file ) )
				unlink ( $file );
		}

		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformssubmissions} WHERE id = '$entry'");
		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformsdata} WHERE sub_id = '$entry'");

		$i++;
	endforeach;
	
	?>
	<div id="message" class="updated fade"><p><strong><?php echo $i; ?> <?php _e('entries succesfully removed from the tables!', 'cforms') ?></strong><br />
		<em><?php _e('Note: If you erroneously deleted an entry, no worries, you should still have an email copy.', 'cforms') ?></em></p></div>
	<?php

}



// delete an entry?
if ( isset($_POST['sqlwhere']) ){

	foreach( array_keys($_POST) as $arg){
		if ( ! (strpos($arg, 'xbutton') === false) )
			$entry = substr( $arg,7 );
	}

	$fileval = $wpdb->get_row("SELECT DISTINCT field_val,form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id = '$entry' AND id=sub_id AND field_name LIKE '%[*]%'");

	$file = get_option('cforms'.$fileval->form_id.'_upload_dir').'/'.$entry.'-'.$fileval->field_val;
	
	$del='';
	if ( $fileval->field_val <> '' ){
		if ( file_exists( $file ) ){
			unlink ( $file );
			$del = __('(including attachment)','cforms');
		}
		else
			$del = __('(but associated attachment was not found!)','cforms');
	}
	
	$nuked = $wpdb->query("DELETE FROM {$wpdb->cformssubmissions} WHERE id = '$entry'");
	$nuked = $wpdb->query("DELETE FROM {$wpdb->cformsdata} WHERE sub_id = '$entry'");

	?>
	<div id="message" class="updated fade"><p><strong><?php echo $i; ?> <?php _e('entry succesfully removed', 'cforms'); echo ' '.$del; ?>.</strong></p></div>
	<?php
}



//
// load a specific entry
//
if ( ($_POST['showid']<>'' || isset($_POST['showselected']) || isset($_POST['sqlwhere'])) && !isset($_POST['delete']) && !isset($_POST['downloadselectedcforms']) && !$reorder ) {

	if ( isset($_POST['showselected']) && isset($_POST['entries']) )
		$sub_id = implode(',', $_POST['entries']);
	else if ( $_POST['showid']<>'' )
		$sub_id = $_POST['showid'];
	else if ( isset($_POST['sqlwhere']) )
		$sub_id = $_POST['sqlwhere'];	
	else
		$sub_id = '-1';
	
	$sql="SELECT *, form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id in ($sub_id) AND sub_id=id ORDER BY sub_id, f_id";
	$entries = $wpdb->get_results($sql);
	
	?>

	<div class="wrap"><a id="top"></a>
	<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p3-title.jpg" alt=""/>

	<?php if ($entries) :

		echo '<form name="datactrl" method="post" action="#"><input type="hidden" name="sqlwhere" value="'.$sub_id.'">';
		
		$sub_id='';
		foreach ($entries as $entry){

			if( $sub_id<>$entry->sub_id ){
				$sub_id = $entry->sub_id;
				echo '<div class="showform">Form: <span>'. get_option('cforms'.$entry->form_id.'_fname') . '</span> &nbsp; <em>(ID:' . $entry->sub_id . ')</em>' .
						'&nbsp; <input class="allbuttons xbutton" type="submit" name="xbutton'.$entry->sub_id.'" title="'.__('delete this entry', 'cforms').'" value=""/></div>' . "\n";
			}

			$name = $entry->field_name==''?'&nbsp;':stripslashes($entry->field_name);
			$val  = $entry->field_val ==''?'&nbsp;':stripslashes($entry->field_val);

			if (strpos($name,'[*]')!==false) {  // attachments?

					$no   = $entry->form_id; 
                    $file = get_option('cforms'.$no.'_upload_dir').'/'.$entry->sub_id.'-'.strip_tags($val);
                    $file = get_settings('siteurl') . substr( $file, strpos($file, '/wp-content/') );
                    
					echo '<div class="showformfield" style="margin:4px 0;color:#3C575B;"><div class="L">';
					_e('Attached file:', 'cforms');
					if ( $entry->field_val == '' )
						echo 	'</div><div class="R">' . __('-','cforms') . '</div></div>' . "\n";					
					else
						echo 	'</div><div class="R">' . '<a href="' . $file . '">' . str_replace("\n","<br />", strip_tags($val) ) . '</a>' . '</div></div>' . "\n";

			}
			elseif ($name=='page') {  // special field: page 
			
					echo '<div class="showformfield" style="margin-bottom:10px;color:#3C575B;"><div class="L">';
					_e('Submitted via page', 'cforms');
					echo 	'</div><div class="R">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

			} else {

					echo '<div class="showformfield"><div class="L">' . $name . '</div>' .
							'<div class="R">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

			}

		}

		echo '</form>';


	else : ?>
	
		<p><?php _e('Sorry, no form data found.', 'cforms') ?></p>

	<?php endif;


} else {


	//
	// load entries
	//
	$sql="SELECT * FROM {$wpdb->cformssubmissions} ORDER BY $order $orderdir";
	$entries = $wpdb->get_results($sql);

	?>

	<div class="wrap"><a id="top"></a>
	<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p3-title.jpg" alt=""/>

		<?php if ($entries) :?>

		<p><?php _e('Keep track of all form submissions & data entered, view individual entries or a whole bunch and download as TAB or CSV formatted file. Attachments can be accessed in the details section. When deleting entries, associated attachments will be removed, too! ', 'cforms') ?></p>

		<form id="cformsdata" name="form" method="post" action="">
				<input type="hidden" name="showid" value=""/>
				<input type="hidden" name="order" value="<?php echo $order; ?>"/>
				<input type="hidden" name="orderdir" value="<?php echo $orderdir; ?>"/>
				<input type="hidden" name="checkflag" value="0"/>


				<div class="dataheader">
					<input type="submit" class="allbuttons delete" name="delete" value="<?php _e('delete selected entries', 'cforms') ?>" onclick="return confirm('Do you really want to erase the selected records?');"/>
					<input type="submit" class="allbuttons showselected" name="showselected" value="<?php _e('show selected entries', 'cforms') ?>" />&nbsp;&nbsp;
					<input type="submit" class="allbuttons downloadselectedcforms" name="downloadselectedcforms" value="<?php _e('download selected entries', 'cforms') ?>" />
					<select name="downloadformat" class="downloadformat">
						<option value="csv"><?php _e('CSV', 'cforms') ?></option>
						<option value="txt"><?php _e('TXT (tab delimited)', 'cforms') ?></option>
					</select>
				</div>

				<ul class="sortheader">
					<li class="col0">#</li>
					<li class="col1"><?php _e('?') ?></li>
					<li class="col2"><span class="abbr" title="<?php _e('click to sort', 'cforms'); ?>"><a href="javascript:void(0);" onclick="sort_entries('form_id');"><?php _e('Form','cforms') ?></a></span></li>
					<li class="col3"><span class="abbr" title="<?php _e('click to sort', 'cforms'); ?>"><a href="javascript:void(0);" onclick="sort_entries('email');"><?php _e('Who','cforms') ?></a></span></li>
					<li class="col4"><span class="abbr" title="<?php _e('click to sort', 'cforms'); ?>"><a href="javascript:void(0);" onclick="sort_entries('sub_date');"><?php _e('When','cforms') ?></a></span></li>
					<li class="col5"><span class="abbr" title="<?php _e('click to sort', 'cforms'); ?>"><a href="javascript:void(0);" onclick="sort_entries('ip');"><?php _e('IP','cforms') ?></a></span></li>
				</ul>


				<ul class="selectrow" style="margin-top:5px;">
					<li>
						<label for="allchktop"><input type="checkbox" id="allchktop" name="allchktop" onClick="javascript:checkonoff('form','entries[]');"/><strong><?php _e('select/deselect all', 'cforms') ?></strong></label>
					</li>
				</ul>

				<?php
				$class=''; $i=0;
				foreach ($entries as $entry)
				{
					$class = ('alternate' == $class) ? '' : 'alternate'; ?>

					<ul class="datarow <?php echo $class; ?>" onclick="checkentry('e<?php echo $entry->id; ?>')">
						<li class="col0"><?php echo $entry->id; ?></li>
						<li class="col1"><input type="checkbox" name="entries[]" id="e<?php echo $entry->id; ?>" value="<?php echo $entry->id; ?>" /></li>
						<li class="col2"><?php echo get_option('cforms'.$entry->form_id.'_fname'); ?></li>
						<li class="col3"><?php echo $entry->email; ?></li>
						<li class="col4"><?php echo $entry->sub_date; ?></li>
						<li class="col5"><?php echo $entry->ip; ?></li>
						<li class="col6"><?php echo '<a href="#" onclick="document.form.showid.value=\''.$entry->id.'\';document.form.submit();">'; ?><?php _e('view', 'cforms') ?></a></li>
					</ul>

				<?php
				}
				?>

				<ul class="selectrow">
					<li>
						<label for="allchkbottom"><input type="checkbox" id="allchkbottom" name="allchkbottom" onClick="javascript:checkonoff('form','entries[]');"/><strong><?php _e('select/deselect all', 'cforms') ?></strong></label>
					</li>
				</ul>

				<div class="dataheader">
					<input type="submit" class="allbuttons delete" name="delete" value="<?php _e('delete selected entries', 'cforms') ?>" onclick="return confirm('Do you really want to erase the selected records?');" />
					<input type="submit" class="allbuttons showselected" name="showselected" value="<?php _e('show selected entries', 'cforms') ?>" />&nbsp;&nbsp;
					<input type="submit" class="allbuttons downloadselectedcforms" name="downloadselectedcforms" value="<?php _e('download selected entries', 'cforms') ?>" />
				</div>

			</form>

		<?php else :?>

		<p><?php _e('No data available at this time.','cforms'); ?></p>

		<?php endif; 

} // all data or just one

cforms_footer();
echo '</div>';
?>
