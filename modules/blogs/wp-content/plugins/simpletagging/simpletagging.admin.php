<?php
class SimpleTaggingAdmin {
	var $sTagObj;	// Contains SimpleTagging Object
	var $import;	// Import Variables
	
	// Constructor
	function SimpleTaggingAdmin($sTagObj) {
		global $wpdb;

		// Set object
		$this->sTagObj = $sTagObj;

		// Set Import Variables
		$this->import = array(
			// UTW
			'UTW_tTags'			=> $wpdb->prefix . 'tags',
			'UTW_tTagsID'		=> 'tag_ID',
			'UTW_tTagsName'		=> 'tag',
			'UTW_tP2T'			=> $wpdb->prefix . 'post2tag',
			'UTW_tP2TtagID'		=> 'tag_id',
			'UTW_tP2TpostID'	=> 'post_id',
			// Jeromes Keyword 2.0
			'JK2_jkeywords' 	=> $wpdb->prefix . 'jkeywords'
		);
		
		// Admin WP only
		if ( is_admin() ) {
			// add WP filters
			add_filter('simple_edit_form',	array(&$this, 'showTagEntry'));
			add_filter('edit_form_advanced',array(&$this, 'showTagEntry'));
			add_filter('edit_page_form',	array(&$this, 'showTagEntry'));

			// add menu
			add_action('admin_menu', array(&$this, 'wpaction_admin_menu'));

			// Add CSS for STP
			add_action('admin_head', array(&$this, 'stp_admin_head'));
			
			// Remove prototype		
			if ( $_GET['page'] == 'simple-tagging_cloud' ) {		
				add_action('admin_head', array(&$this, 'remove_prototype'));
			}
			
			// check if we need to upgrade
			if ( $this->sTagObj->option['version_for_install'] < $this->sTagObj->stp_version  ) {
				$this->install(); // Execute installation
				// Update version number in the options
				$this->sTagObj->setOption('version_for_install', $this->sTagObj->stp_version);
				$this->sTagObj->saveOptions();
			}			
		}

		// add WP actions - not limited to is_admin() to be applied also in case of xmlrpc posts by external blogging application
		add_action('publish_post',		array(&$this, 'savePostTags'));
		add_action('edit_post',			array(&$this, 'savePostTags'));
		add_action('save_post',			array(&$this, 'savePostTags'));
		add_action('wp_insert_post',	array(&$this, 'savePostTags'));
		add_action('delete_post', 		array(&$this, 'wpaction_delete_post'));
	}
	
	function stp_admin_head() {
		echo '<style type="text/css">
			input.stpwarn:hover { background: #ce0000; color: #fff; }
			.stpexplan { font-size: 95%; line-height:120%; }
			.stpexplan ul, .stpexplan ul li {margin: 0; padding:0; line-height:120%;  }
			.stpexplan ul { margin-left: 20px; }
			.stp_admin { text-align: center; font-size: .85em; }
			fieldset#taglist ul { list-style: none; margin: 0; padding: 0; }
			fieldset#taglist ul li { margin: 0; padding: 0; font-size: 85%; }
		</style>';
	}
	
	function remove_prototype() {	
		wp_deregister_script('prototype');
	}
	
	function javascript_colorpicker() {
		?>
		<script src="<?php echo $this->sTagObj->info['install_url'] ?>/colorpicker/mootools.js" type="text/javascript"></script>
		<script src="<?php echo $this->sTagObj->info['install_url'] ?>/colorpicker/mooRainbow.js" type="text/javascript"></script>
		<link rel="stylesheet" href="<?php echo $this->sTagObj->info['install_url'] ?>/colorpicker/mooRainbow.css" type="text/css" />	
		<script type="text/javascript">
			window.addEvent('load', function() {
				if (window.ie) var max_color;
				max_color = new MooRainbow('img_cloud_max_color', {	
					id: 'max_color',
					wheel: true,
					select_lang: '<?php _e('Select', 'simpletagging'); ?>',
					imgPath: '<?php echo $this->sTagObj->info['install_url'] ?>/colorpicker/images/',
					onChange: function(color) {
						$('cloud_max_color').setStyle('background-color', color.hex);
						var myValue = $E('#max_color .moor-hexInput').value;
					},
					onComplete: function(color) {
						$('cloud_max_color').setStyle('background-color', '#ffffff');
						$('cloud_max_color').value = color.hex;
					}
				});				
				if (window.ie) var min_color;
				min_color = new MooRainbow('img_cloud_min_color', {	
					id: 'min_color',
					wheel: true,
					select_lang: '<?php _e('Select', 'simpletagging'); ?>',
					imgPath: '<?php echo $this->sTagObj->info['install_url'] ?>/colorpicker/images/',
					onChange: function(color) {
						$('cloud_min_color').setStyle('background-color', color.hex);
						var myValue = $E('#min_color .moor-hexInput').value;
					},
					onComplete: function(color) {
						$('cloud_min_color').setStyle('background-color', '#ffffff');
						$('cloud_min_color').value = color.hex;
					}
				});
			});	
		</script>
		<?php
	}
	
	function wpaction_delete_post($postid) {
		$this->deleteTagsByPostID($postid);
	}
	
	function wpaction_admin_menu() {
		add_menu_page(__('Simple Tagging', 'simpletagging'), __('Tags', 'simpletagging'), 8, __FILE__, array(&$this, 'stp_page_manage'));
		add_submenu_page(__FILE__, __('Simple Tagging: Manage Tags', 'simpletagging'), __('Manage Tags', 'simpletagging'), 8, __FILE__, array(&$this, 'stp_page_manage'));
		add_submenu_page(__FILE__, __('Simple Tagging: Not Tagged Articles', 'simpletagging'), __('Not Tagged Articles', 'simpletagging'), 8, 'simple-tagging_not-tagged', array(&$this, 'stp_page_not_tagged'));
		add_submenu_page(__FILE__, __('Simple Tagging: Import Tags', 'simpletagging'), __('Import Tags', 'simpletagging'), 8, 'simple-tagging_import', array(&$this, 'stp_page_import'));
		add_submenu_page(__FILE__, __('Simple Tagging: General Options', 'simpletagging'), __('General Options', 'simpletagging'), 8, 'simple-tagging_options', array(&$this, 'stp_page_options'));	
		add_submenu_page(__FILE__, __('Simple Tagging: Cloud Options', 'simpletagging'), __('Cloud Options', 'simpletagging'), 8, 'simple-tagging_cloud', array(&$this, 'stp_page_cloud_options'));	
		add_submenu_page(__FILE__, __('Simple Tagging: Related Options', 'simpletagging'), __('Related Options', 'simpletagging'), 8, 'simple-tagging_related', array(&$this, 'stp_page_related_options'));	
	}

	function getAllTags($sort='desc') {
		/* get all tags from the db */
		global $wpdb;
		$all_tags = array();
		
		switch($sort) {
			case 'asc':
				$orderby = 'tag_count';
				break;
			case 'desc':
				$orderby = 'tag_count DESC';
				break;
			default:
				$orderby = 'tag_name';
				break;
		}
		$tablename = $this->sTagObj->info['stptable'];
		$tags = $wpdb->get_results("SELECT tag_name, COUNT(post_id) AS tag_count
									FROM {$tablename}
									GROUP BY tag_name ORDER BY $orderby ");
		if (is_array($tags)) {
			foreach($tags as $t)
			$all_tags[$t->tag_name] = $t->tag_count;
			
			switch($sort) {
			case 'natural':
				uksort($all_tags, 'strnatcasecmp');
				break;
			default:
				//do nothing
				break;
			}
		}		
		return $all_tags;
	}

	function getPostTags($id) {
		/* get all tags for the specified post from the db */
		global $wpdb;
		$post_tags = array();
		
		$tablename = $this->sTagObj->info['stptable'];
		$tags = $wpdb->get_results("SELECT tag_name FROM {$tablename} WHERE post_id='$id'");
		
		if (is_array($tags)) {
			foreach($tags as $t)
			$post_tags[] = $t->tag_name;
		}
		return $post_tags;
	}
	
	// Display tag entry & suggested tag fields
	function showTagEntry() {		
		global $post, $wpdb;

		################################ 
		# prepare options
		################################
		// max number of tags to be displayed
		$opt['max_suggest'] = intval( $this->sTagObj->option['admin_max_suggest'] );
		if($opt['max_suggest'] == 0)
			$opt['max_suggest'] = 99999;	// if user wants no limit
		// display suggested tags or all tags?
		$opt['dsp_suggested'] = $this->sTagObj->option['admin_tag_suggested'];
		// sorting
		$opt['tag_sort'] = $this->sTagObj->option['admin_tag_sort'];
		
		################################ 
		# get tags of this post and all tags
		################################
		$tags = $this->getPostTags($post->ID);
		$post_tags = implode(', ', $tags);
		$all_tags = $this->getAllTags('desc');

		################################ 
		# create array of tags to be displayed
		################################
		$result_tags = array();
		foreach($all_tags as $tag => $count) {
			if (!in_array($tag, $tags)) {
				if ( $opt['dsp_suggested'] ) {
					// only add tag if it is somewhere used in post content
					// added check for $tag not empty since some users got error message "Warning: stristr() [function.stristr]: Empty delimiter"				
					if ( is_string($tag) && $tag != '' && stristr($post->post_content, $tag) )
						$result_tags[] = $tag;

				} else {
					$result_tags[] = $tag;
				}
				if ( count($result_tags) >= $opt['max_suggest'] )
					break;		
			}
		}
		
		// Add more tags if 'suggested tags' should be displayed but not enough tags were found
		if ( (count($result_tags) < $opt['max_suggest']) && $opt['dsp_suggested'] ) {
			foreach($all_tags as $tag => $count) {
				if (!in_array($tag, $tags) && !in_array($tag, $result_tags)) {
					$suggested[] = $tag;
					if (count($suggested) >= $opt['max_suggest'])
						break;
				}
			}
		}
		
		if (count($result_tags) > 0) {

			if (strtolower($opt['tag_sort']) == 'alpha')
			natcasesort($result_tags);			

			$result_tags_str = '<span onclick="javascript:addTag(this.innerHTML);">' . implode('</span> <span onclick="javascript:addTag(this.innerHTML);">', $result_tags) . '</span>';

		} else {
			$result_tags_str = __('No (suggested) tags founds.', 'simpletagging');
		}

		// TODO: add word boundaries to the regexp, or use a global array to store
		//       already added tags, too tired now and too many years since I last
		//       used JS --ludo
		echo '
		<style type="text/css">
			/* Style for tag section */
			#stp_tag_entry { width: 98%; }
			#stp_taglist { margin: 3px 0 0 1%; }
			#stp_taglist h4 { margin: 0; padding: 0 0 2px 0; font-weight: normal; font-size: 10pt}
			#stp_taglist p { padding: 0; margin: 0}
			#stp_taglist span { font-size: 90%; display: block; float: left; background-color: #f0f0ee; padding: 0px 1px 0px 1px;
				margin: 1px; border: solid 1px; border-color: #ccc #999 #999 #ccc; color: #333; cursor: pointer; }
			#stp_taglist span:hover { color: black; background-color: #b6bdd2; border-color: #0a246a; }
			#stp_taglist div#clearer { clear:both; line-height: 1px; font-size: 1px; height: 5px; }
	
			/* Style for Type Ahead (Wick) */ 
			table.floater { position:absolute; z-index:1000; display:none; padding:0; margin:0; }
			table.floater td { font-family: Gill, Helvetica, sans-serif; background-color:white; border:1px inset #979797; color:black; } 
			.matchedSmartInputItem { font-size:0.8em; padding: 5px 10px 1px 5px; margin:0; cursor:pointer; }
			.selectedSmartInputItem { color:white; background-color:#3875D7; }
			#smartInputResults { padding:0; margin:0; }
			.siwCredit { margin:0; padding:0; margin-top:10px; font-size:0.7em; color:black; }  
		</style>

		<!-- Hack for IE incl. IE7, since the dropdown is not placed properly -->		
			<!--[if IE]>
				<style type="text/css"> table.floater { position:static; }	</style>
			<![endif]-->

		<script type="text/javascript">
		if(document.all && !document.getElementById) {
			document.getElementById = function(id) { return document.all[id]; }
		}
		function addTag(tag) {
			var stp_tag_entry = document.getElementById("stp_tag_entry");
			if (stp_tag_entry.value.length > 0 && !stp_tag_entry.value.match(/,\s*$/))
				stp_tag_entry.value += ", ";
			var re = new RegExp(tag + ",");
			if (!stp_tag_entry.value.match(re))
				stp_tag_entry.value += tag + ", ";
		}
		</script>
		';
		
		/* Prepare Type-Ahead */
		$tablename = $this->sTagObj->info['stptable'];
		$ta_alltags = $wpdb->get_col("SELECT DISTINCT tag_name FROM {$tablename}");
		$ta_result = '';
		if (is_array($ta_alltags)) {
			foreach($ta_alltags as $t) {
				$ta_alltags2[] = '\'' . $t . '\'';
			}
			$ta_result = implode(',', $ta_alltags2);
			$ta_result = '//<![CDATA[
				collection = [' . $ta_result . '];
			//]]>';
		}

		// display tag entry fields
		// the hidden field is there due to http://markjaquith.wordpress.com/2007/01/28/authorization-and-intentionorigination-verification-when-using-the-edit_post-hook/
		echo '
		<div id="lptagstuff" class="dbx-group">
			<fieldset class="dbx-box" id="posttags">
				<h3 class="dbx-handle">' . __('Tags (comma separated list)', 'simpletagging') . '</h3>
				<div class="dbx-content">
					<input class="wickEnabled" name="tag_list" id="stp_tag_entry" value="' . $post_tags . '" />
					<input type="hidden" name="simpletaggingverifykey" id="simpletaggingverifykey" value="' . wp_create_nonce('simpletagging') . '" />' . '
					<script type="text/javascript" language="JavaScript">' . $ta_result . '</script>
					<script type="text/javascript" language="JavaScript" src="' . $this->sTagObj->info['install_url'] . '/simpletagging.type-ahead.js"></script>
					<div id="stp_taglist">
						<h4>' . (($opt['dsp_suggested']) ? __('Suggested Tags', 'simpletagging') : __('Tags', 'simpletagging')) . ':</h4>
						<p>' . $result_tags_str . '</p>
						<div id="clearer">&nbsp;</div>
					</div>
				</div>
			</fieldset>
		</div>
		';
	}

	function saveTag($id, $tag) {
		global $wpdb;
		$wpdb->query("INSERT IGNORE INTO {$this->sTagObj->info['stptable']} VALUES ('$id', '$tag')");
	}
	
	/**
	* Retrieves embedded tags ([tags]tag1, tag2[/tags]) from the post
	*/
	function getEmbeddedTags($postID) {
		
		$post = &get_post($postID);
		$postContent = $post->post_content;

		$regex = '/(' . $this->regexEscape($this->sTagObj->option['tag_embed_start']) . '(.*?)' . $this->regexEscape($this->sTagObj->option['tag_embed_end']) . ')/i';

		// Return Tags
		preg_match_all($regex, $postContent, $matches);
		$tags = array();
		foreach ($matches[2] as $match) {
			foreach(explode(',', $match) as $tag) {
				$tags[] = $tag;
			}
		}
		return $tags;
	}


	/* save new list of post tags to database */
	function savePostTags($id) {		
		global $wpdb;

		#### Added due to <http://markjaquith.wordpress.com/2007/01/28/authorization-and-intentionorigination-verification-when-using-the-edit_post-hook/>
		#### However, also added "XMLRPC_REQUEST" to be able to use embedded tags [tags]...[/tags] 
		// authorization
		if ( !current_user_can('edit_post', $id) )
			return $id;
		// origination and intention
		if ( ! ( wp_verify_nonce($_POST['simpletaggingverifykey'], 'simpletagging') || ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST )  ) )
			return $id;
		##################

		// clear old values first
		$this->deleteTagsByPostID($id);

		// Retrieve post content (tags between [tags] and [/tags]) as list
		$tags_embedded = ( $this->sTagObj->option['tag_embed_use'] ) ? implode(',', $this->getEmbeddedTags($id)) : '';

		// Retrieve tags entered in field
		$tags_entered = attribute_escape($_REQUEST['tag_list']);
		
		// Merge 'em
		$tag_list = $tags_embedded . ',' . $tags_entered;
		
		// Replace some stuff since we will not allow _ and + etc.
		$tag_list = $this->sTagObj->tag_convertUserInput($tag_list);
		
		// Consider slashes
		$tag_list = ( get_magic_quotes_gpc() ) ? $tag_list : addslashes($tag_list);

		// convert to array & remove duplicate values
		$tag_list = array_unique(explode(',', $tag_list));

		foreach($tag_list as $tag) {
			$tag = trim($tag);
			if (!empty($tag))
				$this->saveTag($id, $tag);
		}
		
		return 1;
	}
	
	// Deletes all tags of a post according passed post id.
	function deleteTagsByPostID($postid) {		
		global $wpdb;
		if ( is_numeric($postid) || $postid > 0 ) { 
			$wpdb->query("DELETE FROM {$this->sTagObj->info['stptable']} WHERE post_id='$postid'");
			return true;
		} else
			return false;
	}

	// Deletes list of tags from the database
	function deleteTagsByTagList($todelete) {
		global $wpdb;		
		/* split list of tags */
		$old_tags = array_unique(explode(',', $todelete));
		$old_list = '';
		foreach($old_tags as $key=>$tag) {
			if (!empty($old_list))
				$old_list .= ',';
			$old_list .= "'" . addslashes(trim($tag)) . "'";
		}
		
		/* delete old tags */
		$tablename = $this->sTagObj->info['stptable'];
		if ($wpdb->query("DELETE FROM {$tablename} WHERE tag_name IN ($old_list)") > 0)
			return sprintf(__('Deleted the following tag(s): %s', 'simpletagging'), $todelete);
		else
			return sprintf(__('Could not find tag(s) in database: %s', 'simpletagging'), $todelete);
	}

	// Resaves list of old tags to new value(s) or adds new tag to all posts ($rename=false)
	function updateTags($old, $new, $rename=true) {
		global $wpdb;
		$tablename = $this->sTagObj->info['stptable'];
		
		// split lists of old & new tags
		$old_tags = array_unique(explode(',', $old));
		$old_list = '';
		foreach($old_tags as $tag) {
			if (!empty($old_list))
				$old_list .= ',';
			$old_list .= "'" . addslashes(trim($tag)) . "'";
		}
		if (trim(str_replace(',', '', stripslashes($new))) == '')
			return (__('No new tag specified!', 'simpletagging')); 
		
		$new_tags = array_unique(explode(',', $new));
		
		// Get list of posts matching old tags
		if ($old == '' && $rename === false) {
			// User wants the tag(s) to be added to all published posts
			$posts_arg = ($this->sTagObj->is_wp_21) ? "AND posts.post_type = 'post' AND posts.post_status = 'publish'"
			: "AND posts.post_status = 'publish'";
			$qry = "SELECT DISTINCT posts.ID as the_id FROM {$wpdb->posts} posts
					WHERE posts.post_date < '" . current_time('mysql') . "'
					$posts_arg ";
		} else {
			// Only add tags to the posts which are tagged with the old tag(s)
			$qry = "SELECT DISTINCT post_id as the_id FROM {$tablename}
					WHERE tag_name IN ($old_list)
					GROUP BY post_id";
		}
		
		$posts = $wpdb->get_results($qry);

		if (is_array($posts) && (count($posts) > 0)) {
			if ($rename) {
				// delete old tags
				$wpdb->query("DELETE FROM {$tablename} WHERE tag_name IN ($old_list)");
			}
			
			// save new tags
			foreach ($posts as $p) {
				foreach($new_tags as $tag) {
					$tag = addslashes(trim($tag));
					// check if tag already exists for post before saving
					if ($wpdb->query("SELECT post_id, tag_name FROM {$tablename} WHERE tag_name='$tag' AND post_id='{$p->the_id}'") <= 0)
					$this->saveTag($p->the_id, $tag);
				}
			}
			if ($rename)		
				return sprintf(__('Renamed tag(s) &laquo;%1$s&raquo; to &laquo;%2$s&raquo;', 'simpletagging'), $old, $new);
			elseif ($old == '' && $rename === false)
				return sprintf(__('Added tag(s) &laquo;%s&raquo; to all posts.', 'simpletagging'), $new);
			else
				return sprintf(__('Added tag(s) &laquo;%1$s&raquo; to posts tagged with &laquo;%2$s&raquo;', 'simpletagging'), $new, $old);
		} else
			return __('No posts found matching tag(s):', 'simpletagging')." ".$old;
	}
	
	function install() {
		global $wpdb;

		/* create tags table if it doesn't exist */
		$tablename = $this->sTagObj->info['stptable'];
		$found = false;
		foreach ($wpdb->get_results("SHOW TABLES;", ARRAY_N) as $row) {
			if ($row[0] == $tablename) {
				$found = true;
				break;
			}
		}
		if (!$found)
			$res = $wpdb->get_results("CREATE TABLE $tablename " . $this->sTagObj->tablestruct);
			
		return 1;
	}

	function STP_ConvertCatsToTags() {		
		global $wpdb;
		
		// Get an array of all categories, e,g. ([cat_name] => Wordpress, [post_id] => 81) ([cat_name] => Wordpress, [post_id] => 82), ([cat_name] => Gadget, [post_id] => 82), ([cat_name] => Apps, [post_id] => 82)
		$qry = "
			SELECT cats.cat_name, p2c.post_id FROM $wpdb->posts posts
			LEFT JOIN $wpdb->post2cat p2c ON (posts.ID = p2c.post_id)
			INNER JOIN $wpdb->categories cats ON (p2c.category_id = cats.cat_id) 
			WHERE posts.post_status = 'publish'
			AND posts.post_type <> 'page'
				";	
		$sqlres = $wpdb->get_results($qry);
		if (count($sqlres) > 0) {
			$count = 0;
			$qry_count = "
					SELECT count(distinct(cats.cat_name)) FROM $wpdb->posts posts
					LEFT JOIN $wpdb->post2cat p2c ON (posts.ID = p2c.post_id)
					INNER JOIN $wpdb->categories cats ON (p2c.category_id = cats.cat_id) 
					WHERE posts.post_status = 'publish'
					AND posts.post_type <> 'page'
				";
			$nb_count = $wpdb->get_var($qry_count);
			$queryInsert = '';
			foreach($sqlres as $loopval) {				
				// Prepare values
				$postid = $loopval->post_id;
				$catname = $loopval->cat_name;
				$catname = str_replace(',', '.', $catname); // remove comma, for some reasons does Wordpress allow commas in categories
				$catname = $this->sTagObj->tag_convertUserInput($catname); // Convert several other chars we don't allow

				// Create query
				if ( $postid != '' && $catname != '' ) {
					$count++;
					$addquote = ($count == 1) ? '' : ',';
					$queryInsert .= "$addquote($postid,'$catname')";
				}
			}
			if ( $count > 0 ) {
				// Write values into STP table
				$table = $this->sTagObj->info['stptable'];
				$query = "INSERT IGNORE INTO $table ( post_id, tag_name ) VALUES $queryInsert";
				$wpdb->query($query);
			}
			return sprintf(__('Converting categories to tags was successful, %s categories converted :)', 'simpletagging'), $nb_count);
		}		
		return __('No categories found so nothing was converted.', 'simpletagging');	
	} // function STP_ConvertCatsToTags() {

	function STP_ImportFromJK19() {		
		global $wpdb;
		
		$qry = "SELECT post_id, meta_id, meta_key, meta_value FROM  {$wpdb->postmeta} meta WHERE meta.meta_key = 'keywords'";
		$metakeys = $wpdb->get_results($qry);
		if (count($metakeys) > 0) {
			$count = 0;
			$queryInsert = '';
			foreach($metakeys as $post_meta) {
				if ($post_meta->meta_value != '') {
					$post_keys = explode(',', $post_meta->meta_value);
					foreach($post_keys as $keyword) {
						$keyword = addslashes(trim($keyword));
						if ($keyword != '') {
							
							// Prepare values
							$keyword = trim($keyword);
							$keyword = $this->sTagObj->tag_convertUserInput($keyword); // Convert several other chars we don't allow
							$post_id = $post_meta->post_id;

							// Create query
							if ( $keyword != '' && $post_id != '' ) {
								$count++;
								$addquote = ($count == 1) ? '' : ',';
								$queryInsert .= "$addquote($post_id,'$keyword')";
							}
						}
					} // foreacg
				} // if
			} // foreach
			
			if ( $count > 0 ) {
				// Write values into STP table
				$table = $this->sTagObj->info['stptable'];
				$query = "	INSERT IGNORE INTO $table ( post_id, tag_name )
							VALUES $queryInsert";
				$wpdb->query($query);
			}
			return sprintf(__('Importing Tags of Jerome\'s Keywords 1.9 was successful, %s tags imported :)', 'simpletagging'), $count);
		} else		
			return __('No tags found so nothing was imported.', 'simpletagging');		
	} // function STP_ImportFromJK19

	function STP_ImportFromJK20Beta() {		
		global $wpdb;

		$jk2table = $this->import['JK2_jkeywords'];
		$qry = "SELECT post_id, tag_name FROM  {$jk2table}";
		$sql_res = $wpdb->get_results($qry);

		if (count($sql_res) > 0) {
			$count = 0;
			$queryInsert = '';
			foreach($sql_res as $entry) {
				// Prepare values
				$post_id = $entry->post_id;
				$tag = $entry->tag_name;
				$tag = $this->sTagObj->tag_convertUserInput($tag); // Convert several other chars we don't allow
				
				// Create query
				if ( $post_id != '' && $tag != '' ) {
					$count++;
					$addquote = ($count == 1) ? '' : ',';
					$queryInsert .= "$addquote($post_id,'$tag')";
				}
			}

			if ( $count > 0 ) {
				// Write values into STP table
				$table = $this->sTagObj->info['stptable'];
				$query = "INSERT IGNORE INTO $table ( post_id, tag_name ) VALUES $queryInsert";
				$wpdb->query($query);
			}
			return sprintf(__('Importing Tags from Jerome\'s Keywords 2.0 Beta was successful, %s tags imported :)', 'simpletagging'), $count);
		} 	
		return __('No tags found so nothing was imported.', 'simpletagging');				
	} // function STP_ImportFromJK20Beta

	function STP_ImportFromUTW() {		
		global $wpdb;
		
		$UTW_tTags			= $this->import['UTW_tTags'];
		$UTW_tTagsID		= $this->import['UTW_tTagsID'];
		$UTW_tTagsName		= $this->import['UTW_tTagsName'];
		$UTW_tP2T			= $this->import['UTW_tP2T'];
		$UTW_tP2TtagID		= $this->import['UTW_tP2TtagID'];
		$UTW_tP2TpostID		= $this->import['UTW_tP2TpostID'];
		
		$query = "SELECT DISTINCT p2t.$UTW_tP2TpostID, t.$UTW_tTagsName
					FROM $UTW_tTags t
					INNER JOIN $UTW_tP2T p2t ON p2t.$UTW_tP2TtagID = t.$UTW_tTagsID
					ORDER BY p2t.$UTW_tP2TpostID ASC 
					";
		$sql_res = $wpdb->get_results($query);
		
		if (count($sql_res) > 0) {	
			$count = 0;
			$queryInsert = '';
			foreach ($sql_res as $loopval) {
				// Prepare values
				$lpID = $loopval->$UTW_tP2TpostID;
				$lpName = $loopval->$UTW_tTagsName;
				$lpName = str_replace('-', ' ', $lpName); // UTW uses minus ("-") instead of space
				$lpName = str_replace('_', '-', $lpName); // We do not allow underscore "_"
				$lpName = trim($lpName);
				$lpName = $this->sTagObj->tag_convertUserInput($lpName); // Convert several other chars we don't allow
				
				// Create query
				if ( $lpID != '' && $lpName != '' ) {
					$count++;
					$addquote = ($count == 1) ? '' : ',';
					$queryInsert .= "$addquote($lpID,'$lpName')";
				}				
			}

			if ( $count > 0 ) {
				// Write values into STP table
				$table = $this->sTagObj->info['stptable'];
				$query = "	INSERT IGNORE INTO $table ( post_id, tag_name )
							VALUES $queryInsert";
				$wpdb->query($query);
			}		
			return sprintf(__('Importing Tags from Ultimate Tag Warrior was successful, %s tags imported :)', 'simpletagging'), $count);
		} else			
			return __('No tags found so nothing was imported.', 'simpletagging');		
	} // function STP_ImportFromUTW
		
	######################################################################################################################
	# ADMIN MENU: OPTIONS 
	######################################################################################################################
	function stp_page_options() {
		// presentation data for each configurable option
		$option_data = array(
			__('General Options', 'simpletagging') => array(
				array('query_varname', __('Tag search base:', 'simpletagging'), 'text', 40, __('Please don\'t enter a leading or trailing slash. <br />Output: ', 'simpletagging') . htmlspecialchars($this->sTagObj->base_url)),
				array('trailing_slash', __('Include trailing slash on tag urls:', 'simpletagging'), 'checkbox', '1',
					__('This will add a trailing slash on tag urls, so "<code>/tag/sometag</code>" becomes "<code>/tag/sometag/</code>". It will work only if you are using <a href="http://codex.wordpress.org/Using_Permalinks">pretty permalinks</a>.', 'simpletagging')),
				array('usehyphen', __('Use hyphens as space separator:', 'simpletagging'), 'checkbox', '1', __('This will convert spaces to hyphens "-" instead of underscores "_". A tag name like \'Hello World\' is per default converted to the URL \'Hello_World\', this makes it possible that you can also use hyphens "-" in tag names like \'plug-in\', \'re-import\' etc. However, if you enable this option, then spaces will be replaced with hyphens "-", but this will also disable the use of hyphens in tag names and will convert tags like \'re-import\' into \'re import\'.', 'simpletagging')),
				array('template', __('Search results template file:', 'simpletagging'), 'text', 40, 
					__('Create a template file with this name in your theme\'s directory to display custom results. Otherwise, search tag results will use \'tags.php\' if it exists, or your category template.', 'simpletagging')),
			),
			__('Feed Options', 'simpletagging') => array(
				array('use_feed_cats', __('Include tags as categories in feeds:', 'simpletagging'), 'checkbox', '1',
					__('This will index your tags with <a href="http://technorati.com/tag">Technorati</a>.', 'simpletagging'))
			),
			__('Meta Keyword Options', 'simpletagging') => array(
				array('meta_autoheader', __('Automatically include in header:', 'simpletagging'), 'checkbox', '1',
					__('Includes the meta keywords tag automatically in your header (most, but not all, themes support this). These keywords are sometimes used by search engines.', 'simpletagging')),
				array('meta_always_include', __('Always add these keywords:', 'simpletagging'), 'text', 80),
				array('meta_includecats', __('Include categories as keywords:', 'simpletagging'), 'dropdown', 'default/all/none', 
					__("'Default' = includes all categories on the homepage, and only categories for posts in the current view for all other pages.<br/>'All' = includes all categories in every view.<br/> 'None' = never includes categories in the meta keywords.", 'simpletagging'))
			),
			__('Display Tags of the Current Post', 'simpletagging') => array(
				array('post_linkformat', __('Post tag link format:', 'simpletagging'), 'text', 80, 
					'<ul>
						<li>'.__('<code>%tagname%</code> &ndash; Replaced by the name of the tag, e.g. <em>Coffee and Tea</em>.', 'simpletagging').'</li>
						<li>'.__('<code>%fulltaglink%</code> &ndash; Replaced by the full link, e.g. <em>http://site.com/tag/Coffee+and+Tea</em>.', 'simpletagging').'</li>
						<li>'.__('<code>%taglink%</code> &ndash; Replaced by the link, e.g. <em>Coffee_and_Tea</em>. Can be used to create links e.g. to Technorati.', 'simpletagging').'</li>
						<li>'.__('<code>%flickr%</code> &ndash; Replaced by the link for Flickr compatibility, e.g. <em>coffeeandtea</em>.', 'simpletagging').'</li>
						<li>'.__('<code>%delicious%</code> &ndash; Replaced by the link for de.icio.us compatibility, e.g. <em>CoffeeandTea</em>.', 'simpletagging').'</li>
					</ul>'),		
				array('post_tagseparator', __('Post tag separator string:', 'simpletagging'), 'text', 40),
				array('post_includecats', __('Include categories in tag list:', 'simpletagging'), 'checkbox', '1'),
				array('post_include_content', __('Automatically display tag list at end of post:', 'simpletagging'), 'checkbox', '1'),
				array('post_include_content_before', __('Text to display before automatically tags list:', 'simpletagging'), 'text', 40),
				array('post_include_content_after', __('Text to display after automatically tags list:', 'simpletagging'), 'text', 40),
				array('post_notagstext', __('Text to display if no tags found:', 'simpletagging'), 'text', 80)
			),
			__('Administration', 'simpletagging') => array(
				array('admin_max_suggest', __('Maximum number of tags:', 'simpletagging'), 'text', 40, __('The maximum number of tags displayed under <em>Write > Post</em>. Zero (0) means no limit and shows all tags.', 'simpletagging')),
				array('admin_tag_suggested', __('Display suggested tags only:', 'simpletagging'), 'checkbox', '1',
					__('Displays suggested tags only instead of all tags. Tags will be suggested if they are part of the post. In adddition, tags that are used more often will be considered first.', 'simpletagging')),
				array('admin_tag_sort', __('Tag cloud sort order:', 'simpletagging'), 'dropdown', 'alpha/relevance', 
					'<ul>
						<li>'.__('<code>Alpha</code> &ndash; alphabetic order.', 'simpletagging').'</li>
						<li>'.__('<code>Relevance</code> &ndash; most relevant/used tags first.', 'simpletagging').'</li>
					</ul>'),
			),
			__('Embedded Tags', 'simpletagging') => array(
				array('tag_embed_use', __('Use embedded tags:', 'simpletagging'), 'checkbox', '1', __('Enabling this will cause Wordpress to look for embedded tags when saving and displaying posts. Such set of tags is marked <code>[tags]like this, and this[/tags]</code>, and is added to the post when the post is saved, but does not display on the post.', 'simpletagging')),
				array('tag_embed_start', __('Prefix for embedded tags:', 'simpletagging'), 'text', 40),
				array('tag_embed_end', __('Suffix for embedded tags:', 'simpletagging'), 'text', 40),
			),
		); // $option_data = array	
		
		// handle form actions
		if (!empty($_POST['updateoptions'])) { //Pressed button: Update Options
			foreach($this->sTagObj->option as $key => $value) {
				if ( $key != 'version_for_install' && $key != 'tags_table') {
					$newval = (isset($_POST[$key])) ? stripslashes($_POST[$key]) : $value;
					if ($newval != $value)
						$this->sTagObj->setOption($key, $newval);					
				}
			}
			$this->sTagObj->saveOptions();
			$pagemsgArr = array('type' => 'updated', 'msg' => __('Simple Tagging options saved', 'simpletagging'));
		} elseif (!empty($_POST['resetoptions'])) { //Pressed button: Reset Options
			$this->sTagObj->resetToDefaultOptions();
			$pagemsgArr = array('type' => 'updated', 'msg' => __('Simple Tagging options resetted to default options!', 'simpletagging'));
		}

		// Display message
		$res['pagemsg'] = '';
		if (!empty($pagemsgArr)) {
			$res['pagemsg'] = '<div id="message" class="' . $pagemsgArr['type'] . ' fade"><p><strong>' . $pagemsgArr['msg'] . '</strong></p></div>';
		}
		
		// URL for form actions
		$actionurl = stripslashes($_SERVER['REQUEST_URI']); // additional slashes are added when saving, so we remove 'em... 
		
		// Put submit & reset buttons into string
		$res['buttons'] = '<p class="submit">
				<input type="submit" name="updateoptions" value="' . __('Update Options', 'simpletagging') . ' &raquo;" />
				<input type="submit" name="resetoptions" class="stpwarn" onclick=\'return confirm("'.__('Do you really want to restore the default options?', 'simpletagging').'");\' value="' . __('Reset Options', 'simpletagging') . '" />
			</p>';

		// Put options into string		
		$res['options'] = $this->print_options( $option_data );
		
		########################### OUTPUT #####################################		
		?>
		<?php echo $res['pagemsg']; ?>

		<div class="wrap">  
			<h2><?php _e('Simple Tagging: Options', 'simpletagging'); ?></h2>
			<p><?php _e('Visit the <a href="http://trac.herewithme.fr/project/simpletagging/">plugin\'s homepage</a> for further details.', 'simpletagging'); ?></p>
			<form action="<?php echo $actionurl; ?>" method="post">
				<?php echo $res['buttons']; ?>
				<?php echo $res['options']; ?>
				<?php echo $res['buttons']; ?>
			</form>	
			<?php $this->print_admin_footer(); ?>
		</div> <!-- [wrap] -->
		<?php
	} // function stp_page_options()	
	
	function stp_page_related_options() {
		// presentation data for each configurable option
		$option_data = array(
			__('Related Posts', 'simpletagging') => array(
				array('related_format', __('Related Posts format:', 'simpletagging'), 'text', 80,
					'<ul>
						<li>'.__('<code>%date%</code> &ndash; Replaced by the post date.', 'simpletagging').'</li>
						<li>'.__('<code>%permalink%</code> &ndash; Replaced by the post\'s permalink.', 'simpletagging').'</li>
						<li>'.__('<code>%title%</code> &ndash; Replaced by the post\'s title.', 'simpletagging').'</li>
						<li>'.__('<code>%commentcount%</code> &ndash; Replaced by the number of comments of the post.', 'simpletagging').'</li>
					</ul>'),
				array('related_postsseparator', __('Related posts separator(s):', 'simpletagging'), 'text', 80),
				array('related_sortorder', __('Related Posts sort order:', 'simpletagging'), 'dropdown', 'date-desc/date-asc/alpha/random', 
					'<ul>
						<li>'.__('<code>Date-desc</code> &ndash; sorting by post date, descending.', 'simpletagging').'</li>
						<li>'.__('<code>Date-asc</code> &ndash; sorting by post date, ascending.', 'simpletagging').'</li>
						<li>'.__('<code>Alpha</code> &ndash; alphabetic order.', 'simpletagging').'</li>
						<li>'.__('<code>Random</code> &ndash; randomized every time the page is loaded.', 'simpletagging').'</li>
					</ul>'),
				array('related_limit_qty', __('Maximum number of related posts to display:', 'simpletagging'), 'text', 40),
				array('related_limit_days', __('Maximum number of days to be considered:', 'simpletagging'), 'text', 40, 
					__('\'365\' means that related posts of the past 365 days are being considered.', 'simpletagging')),
				array('related_dateformat', __('Format of the post’s date \'%date%\':', 'simpletagging'), 'text', 40, 
					__('Check out <a href="http://www.php.net/date">Formatting Date and Time</a> and <a href="http://www.php.net/date">PHP manual for date()</a> to see which format character you can use here.', 'simpletagging')),
				array('related_nothingfound', __('Text to display if no related posts found:', 'simpletagging'), 'text', 80),
				array('related_excludecat', __('Categories to excluded', 'simpletagging'), 'text', 80, __('Category ID separated with a comma.', 'simpletagging')),
				array('related_excludetag', __('Tags to excluded','simpletagging'), 'text', 80, __('Tag separated with a comma.', 'simpletagging')),
				array('related_includepages', __('Include pages in related posts list:', 'simpletagging'), 'checkbox', '1'),
			),
			__('Related Tags', 'simpletagging') => array(
				array('relatedtags_format', __('Related Tags format:', 'simpletagging'), 'text', 80,
					'<ul>
						<li>'.__('<code>%count%</code> &ndash; Replaced by the number of posts that do have this tag.', 'simpletagging').'</li>
						<li>'.__('<code>%tagname%</code> &ndash; Replaced by the tag name.', 'simpletagging').'</li>
						<li>'.__('<code>%taglink%</code> &ndash; Replaced by the tag link.', 'simpletagging').'</li>
						<li>'.__('<code>%taglink_plus%</code> &ndash; Replaced by the tag link that adds the current tag to the currently browsing tag(s).', 'simpletagging').'</li>
					</ul>'),
				array('relatedtags_tagseparator', __('Related tag separator(s):', 'simpletagging'), 'text', 80),
				array('relatedtags_sortorder', __('Related Tags sort order:', 'simpletagging'), 'dropdown', 'alpha-asc/count-desc/count-asc/alpha-desc', 
					'<ul>
						<li>'.__('<code>Alpha-asc</code> &ndash; alphabetic order.', 'simpletagging').'</li>
						<li>'.__('<code>Count-desc</code> &ndash; descending order by number of tags.', 'simpletagging').'</li>
						<li>'.__('<code>Count-asc</code> &ndash; ascending order by number of tags.', 'simpletagging').'</li>
						<li>'.__('<code>Alpha-desc</code> &ndash; descending alphabetic order.', 'simpletagging').'</li>
					</ul>'),
				array('relatedtags_limit_qty', __('Maximum number of related tags to display:', 'simpletagging'), 'text', 40),
				array('relatedtags_nonfoundtext', __('Text to display if no related tags found:', 'simpletagging'), 'text', 80)
			),
			__('Related Tags: Links for Removing Tags', 'simpletagging') => array(
				array('relatedtags_remove_format', __('Format for \'removing tag\' links:', 'simpletagging'), 'text', 80,
					'<ul>
						<li>'.__('<code>%url%</code> &ndash; Replaced by the permalink of the URL without the appropriate tag.', 'simpletagging').'</li>
						<li>'.__('<code>%tagname%</code> &ndash; Replaced by the tag name.', 'simpletagging').'</li>
					</ul>'),
				array('relatedtags_remove_separator', __('Separator(s) for removing tags:', 'simpletagging'), 'text', 80),
				array('relatedtags_remove_nonfoundtext', __('Text to display if there are no tags to remove:', 'simpletagging'), 'text', 80),
			)
		); // $option_data = array	
		
		// handle form actions
		if (!empty($_POST['updateoptions'])) { //Pressed button: Update Options
			foreach($this->sTagObj->option as $key => $value) {
				if ( $key != 'version_for_install' && $key != 'tags_table') {
					$newval = (isset($_POST[$key])) ? stripslashes($_POST[$key]) : $value;
					if ($newval != $value)
						$this->sTagObj->setOption($key, $newval);					
				}
			}
			$this->sTagObj->saveOptions();
			$pagemsgArr = array('type' => 'updated', 'msg' => __('Simple Tagging options saved', 'simpletagging'));
		} elseif (!empty($_POST['resetoptions'])) { //Pressed button: Reset Options
			$this->sTagObj->resetToDefaultOptions();
			$pagemsgArr = array('type' => 'updated', 'msg' => __('Simple Tagging options resetted to default options!', 'simpletagging'));
		}

		// Display message
		$res['pagemsg'] = '';
		if (!empty($pagemsgArr)) {
			$res['pagemsg'] = '<div id="message" class="' . $pagemsgArr['type'] . ' fade"><p><strong>' . $pagemsgArr['msg'] . '</strong></p></div>';
		}
		
		// URL for form actions
		$actionurl = stripslashes($_SERVER['REQUEST_URI']);	// additional slashes are added when saving, so we remove 'em... 
		
		// Put submit & reset buttons into string
		$res['buttons'] = '<p class="submit">
				<input type="submit" name="updateoptions" value="' . __('Update Options', 'simpletagging') . ' &raquo;" />
				<input type="submit" name="resetoptions" class="stpwarn" onclick=\'return confirm("'.__('Do you really want to restore the default options?', 'simpletagging').'");\' value="' . __('Reset Options', 'simpletagging') . '" />
			</p>';

		// Put options into string		
		$res['options'] = $this->print_options( $option_data );
		
		########################### OUTPUT #####################################		
		?>
		<?php echo $res['pagemsg']; ?>

		<div class="wrap">  
			<h2><?php _e('Simple Tagging: Related Options', 'simpletagging'); ?></h2>
			<p><?php _e('Visit the <a href="http://trac.herewithme.fr/project/simpletagging/">plugin\'s homepage</a> for further details.', 'simpletagging'); ?></p>
			<form action="<?php echo $actionurl; ?>" method="post">
				<?php echo $res['buttons']; ?>
				<?php echo $res['options']; ?>
				<?php echo $res['buttons']; ?>
			</form>	
			<?php $this->print_admin_footer(); ?>
		</div> <!-- [wrap] -->
		<?php
	} // function stp_page_related_options()	
	
	function stp_page_cloud_options() {
		// presentation data for each configurable option
		$option_data = array(
			__('Tag Cloud', 'simpletagging') => array(
				array('cloud_linkformat', __('Cloud tag link format:', 'simpletagging'), 'text', 80, 
					'<ul>
						<li>'.__('<code>%tagname%</code> &ndash; Replaced by the name of the tag.', 'simpletagging').'</li>
						<li>'.__('<code>%fulltaglink%</code> &ndash; Replaced by the full link, e.g. <em>http://site.com/tag/Coffee+and+Tea</em>.', 'simpletagging').'</li>
						<li>'.__('<code>%feedtaglink%</code> &ndash; Replaced by the feed link, e.g. <em>http://site.com/tag/Coffee+and+Tea/feed</em>.', 'simpletagging').'</li>
						<li>'.__('<code>%taglink%</code> &ndash; Replaced by the link, e.g. <em>Coffee_and_Tea</em>. Can be used to create links e.g. to Technorati.', 'simpletagging').'</li>
						<li>'.__('<code>%flickr%</code> &ndash; Replaced by the encoded version of the tag which conforms to Flickr link standards., e.g. <em>coffeeandtea</em>.', 'simpletagging').'</li>
						<li>'.__('<code>%delicious%</code> &ndash; Replaced by the encoded version of the tag which conforms to del.icio.us link standards., e.g. <em>coffeeandtea</em>.', 'simpletagging').'</li>
						<li>'.__('<code>%count%</code> &ndash; Replaced by the actual number of times the tag has been used.', 'simpletagging').'</li>
						<li>'.__('<code>%scale%</code> &ndash; Replaced by the value the tag is scaled to.', 'simpletagging').'</li>
						<li>'.__('<code>%color%</code> &ndash; Replaced by the color of the tag, e.g. <em>color: #CCCCCC;</em>.', 'simpletagging').'</li>
						<li>'.__('<code>%size%</code> &ndash; Replaced by the size of the tag, e.g. <em>font-size: 14px;</em>.', 'simpletagging').'</li>
						<li>'.__('<code>%colorsize%</code> &ndash; Replaced by the color <strong>and</strong> size of the tag, e.g. <em>color: #CCCCCC; font-size: 14px;</em>.', 'simpletagging').'</li>
					</ul>'),
				array('cloud_tagseparator', __('Cloud tag separator(s):', 'simpletagging'), 'text', 80),
				array('cloud_includecats', __('Include categories in tag cloud:', 'simpletagging'), 'checkbox', '1'),
				array('cloud_exclude_cat', __('Excludes specified categories in tag cloud:', 'simpletagging'), 'text', 40, __('ID separated with a comma.', 'simpletagging')),
				array('cloud_limit_days', __('Maximum number of days to be considered:', 'simpletagging'), 'text', 40, 	__('\'365\' means that tags of the past 365 days are being considered.', 'simpletagging')),
				array('cloud_sortorder', __('Tag cloud sort order:', 'simpletagging'), 'dropdown', 'natural/countup/countdown/asc/desc/random', 
					'<ul>
						<li>'.__('<code>Natural</code> &ndash; natural case sorting (i.e. treats capital & non-capital the same).', 'simpletagging').'</li>
						<li>'.__('<code>Countup/Asc</code> &ndash; ascending order by tag usage.', 'simpletagging').'</li>
						<li>'.__('<code>Countdown/Desc</code> &ndash; descending order by tag usage.', 'simpletagging').'</li>
						<li>'.__('<code>Alpha Asc</code> &ndash; strict alphabetic order (capitals first).', 'simpletagging').'</li>
						<li>'.__('<code>Alpha Desc</code> &ndash; strict alphabetic order (capitals first), descending.', 'simpletagging').'</li>
						<li>'.__('<code>Random</code> &ndash; randomized every time the page is loaded.', 'simpletagging').'</li>
					</ul>'),
				array('cloud_displaymax', __('Maximum number of tags to display:', 'simpletagging'), 'text', 40,
					__("Set to zero (0) to show all tags.", 'simpletagging')),
				array('cloud_displaymin', __('Minimum tag count required:', 'simpletagging'), 'text', 40, 
					__("Tags must be used at least this many times to show up in the cloud.", 'simpletagging')),
				array('cloud_scalemax', __('Tag count scaling maximum:', 'simpletagging'), 'text', 40, 
					__("Set to zero (0) to disable tag scaling and color cloud.", 'simpletagging')),
				array('cloud_scalemin', __('Tag count scaling minimum:', 'simpletagging'), 'text', 40, 
					__("Use with the maximum scale to limit the range between your most popular and least popular tags.", 'simpletagging')),
				array('cloud_max_color', __('Most popular color:', 'simpletagging'), 'color', 40, 
					__("The colours are hexadecimal colours,  and need to have the full six digits (#eee is the shorthand version of #eeeeee).", 'simpletagging')),
				array('cloud_min_color', __('Least popular color:', 'simpletagging'), 'color', 40), 	
				array('cloud_max_size', __('Most popular font size:', 'simpletagging'), 'text', 20, 
					__("The two font sizes are the size of the largest and smallest tags.", 'simpletagging')),
				array('cloud_min_size', __('Least popular font size:', 'simpletagging'), 'text', 20),
				array('cloud_unit', __('The units to display the font sizes with, on tag clouds:', 'simpletagging'), 'dropdown', 'px/em/%', 
					__("The font size units option determines the units that the two font sizes use.", 'simpletagging')),
				array('cloud_notagstext', __('Text to display if no tags found:', 'simpletagging'), 'text', 80)
			)
		); // $option_data = array	
		
		// handle form actions
		if (!empty($_POST['updateoptions'])) { //Pressed button: Update Options
			foreach($this->sTagObj->option as $key => $value) {
				if ( $key != 'version_for_install' && $key != 'tags_table') {
					$newval = (isset($_POST[$key])) ? stripslashes($_POST[$key]) : $value;
					if ($newval != $value)
						$this->sTagObj->setOption($key, $newval);					
				}
			}
			$this->sTagObj->saveOptions();
			$pagemsgArr = array('type' => 'updated', 'msg' => __('Simple Tagging options saved', 'simpletagging'));
		} elseif (!empty($_POST['resetoptions'])) { //Pressed button: Reset Options
			$this->sTagObj->resetToDefaultOptions();
			$pagemsgArr = array('type' => 'updated', 'msg' => __('Simple Tagging options resetted to default options!', 'simpletagging'));
		}

		// Display message
		$res['pagemsg'] = '';
		if (!empty($pagemsgArr))
			$res['pagemsg'] = '<div id="message" class="' . $pagemsgArr['type'] . ' fade"><p><strong>' . $pagemsgArr['msg'] . '</strong></p></div>';

		// URL for form actions
		$actionurl = stripslashes($_SERVER['REQUEST_URI']);	// additional slashes are added when saving, so we remove 'em... 
		
		// Put submit & reset buttons into string
		$res['buttons'] = '<p class="submit">
				<input type="submit" name="updateoptions" value="' . __('Update Options', 'simpletagging') . ' &raquo;" />
				<input type="submit" name="resetoptions" class="stpwarn" onclick=\'return confirm("'.__('Do you really want to restore the default options?', 'simpletagging').'");\' value="' . __('Reset Options', 'simpletagging') . '" />
			</p>';

		// Put options into string		
		$res['options'] = $this->print_options( $option_data );
		
		########################### OUTPUT #####################################		
		?>
		<?php echo $res['pagemsg']; ?>

		<div class="wrap">  
			<?php $this->javascript_colorpicker(); ?>
			<h2><?php _e('Simple Tagging: Tags Cloud Options', 'simpletagging'); ?></h2>
			<p><?php _e('Visit the <a href="http://trac.herewithme.fr/project/simpletagging/">plugin\'s homepage</a> for further details.', 'simpletagging'); ?></p>
			<form action="<?php echo $actionurl; ?>" method="post">
				<?php echo $res['buttons']; ?>
				<?php echo $res['options']; ?>
				<?php echo $res['buttons']; ?>
			</form>	
			<?php $this->print_admin_footer(); ?>
		</div> <!-- [wrap] -->
		<?php
	} // function stp_page_cloud_options()	
	
	function print_admin_footer() {
		?>
			<p class="stp_admin"><?php _e('&copy; Copyright 2007 <a href="http://www.herewithme.fr/" title="Here With Me">Amaury Balmer</a> (Based on the excellent work of <a href="http://sw-guide.de">Michael W&ouml;hrer</a>)', 'simpletagging'); ?></p>
		<?php
	}
	
	function print_options( $option_data ) {
		$output_option = '';
		foreach($option_data as $section => $options) {
			$output_option .= "\n" . '<fieldset class="options"><legend>' . __($section) . '</legend><table class="optiontable">';

			foreach($options as $option) {
				////////////////////////////////////////////////////////////////
				// Determine input type -- $input_type
				////////////////////////////////////////////////////////////////
				if ($option[2] == 'checkbox') :    // checkbox
					$input_type = '<input type="checkbox" id="' . $option[0] . '" name="' . $option[0] . '" value="' . htmlspecialchars($option[3]) . '" ' . ( ($this->sTagObj->option[ $option[0] ]) ? 'checked="checked"' : '') . ' />';
				elseif ($option[2] == 'dropdown') : // select/dropdown 
					$selopts = explode('/', $option[3]);
					$seldata = '';
					foreach($selopts as $sel) {
						$seldata .= '<option value="' . $sel . '" ' .(($this->sTagObj->option[ $option[0] ] == $sel) ? 'selected="selected"' : '') .' >' . ucfirst($sel) . '</option>';
					}
					$input_type = '<select id="' . $option[0] . '" name="' . $option[0] . '">' . $seldata . '</select>';
				elseif ($option[2] == 'color') : // color picker	
					$input_type = '<img id="img_' . $option[0] . '" src="'.$this->sTagObj->info['install_url']. '/colorpicker/color_wheel.png" alt="[r]" width="16" height="16" /><input class="color" type="text" ' . (($option[3]>50) ? ' style="width: 95%" ' : '') . 'id="' . $option[0] . '" name="' . $option[0] . '" value="' . htmlspecialchars($this->sTagObj->option[ $option[0] ]) . '" size="' . $option[3] .'" />';
				else : // text input				
					$input_type = '<input type="text" ' . (($option[3]>50) ? ' style="width: 95%" ' : '') . 'id="' . $option[0] . '" name="' . $option[0] . '" value="' . htmlspecialchars($this->sTagObj->option[ $option[0] ]) . '" size="' . $option[3] .'" />';
				endif;
				
				////////////////////////////////////////////////////////////////
				// Additional Information
				////////////////////////////////////////////////////////////////
				$extra = '';
				if( $option[4] != '' )
					$extra = '<div class="stpexplan">' . __($option[4]) . '</div>';

				////////////////////////////////////////////////////////////////
				// Output
				////////////////////////////////////////////////////////////////
				$output_option .= '<tr style="vertical-align: top;"><th scope="row">' . __($option[1]) . '</th><td>' . $input_type . '	' . $extra . '</td></tr>';	
			} // foreach($options as $option)
			$output_option .= '</table>' . "\n";
			$output_option .= '</fieldset>';
		}
	
		return $output_option;
	}
	
	######################################################################################################################
	# ADMIN MENU: MANAGE TAGS
	######################################################################################################################
	function stp_page_manage() {
		if ( isset($_POST['tag_action']) ) {
			if ($_POST['tag_action'] == 'renametag') {
				$oldtag = stripslashes( (isset($_POST['renametag_old'])) ? $_POST['renametag_old'] : '');
				$newtag = stripslashes( (isset($_POST['renametag_new'])) ? $_POST['renametag_new'] : '');
				$pagemsg = $this->updateTags($oldtag, $newtag, true);
			} elseif ($_POST['tag_action'] == 'deletetag') {
				$todelete = stripslashes( (isset($_POST['deletetag_name'])) ? $_POST['deletetag_name'] : '');
				$pagemsg = $this->deleteTagsByTagList($todelete);
			} elseif ($_POST['tag_action'] == 'addtag') {
				$matchtag = stripslashes( (isset($_POST['addtag_match'])) ? $_POST['addtag_match'] : '');
				$newtag   = stripslashes( (isset($_POST['addtag_new'])) ? $_POST['addtag_new'] : '');
				$pagemsg = $this->updateTags($matchtag, $newtag, false);
			}
		}
		
		if ( !empty($pagemsg) ) {
			$pagemsg = '<div id="message" class="updated fade"><p><strong>' . $pagemsg . '</strong></p></div>';
		}
			
		/* URL for form actions */
		$actionurl = stripslashes($_SERVER['REQUEST_URI']);

		/* tag sort order */
		$tag_listing = '<p style="margin:0; padding:0;">'.__('Sort Order:', 'simpletagging').'</p><p style="margin:0 0 10px 10px; padding:0;">';
		$order_array = array(
			'desc' => __('Most&nbsp;Popular', 'simpletagging'),
			'asc' => __('Least&nbsp;Used', 'simpletagging'),
			'natural' => __('Alphabetical', 'simpletagging')
		);
		$sortorder = strtolower((isset($_GET['tag_sortorder'])) ? $_GET['tag_sortorder'] : 'desc');
		$sortbaseurl = preg_replace('/&?tag_sortorder=[^&]*/', '', $actionurl, 1);

		foreach($order_array as $sort => $caption) {
			$tag_listing .= ($sort == $sortorder) ? " <span style='color: red;'>$caption</span> <br />" : " <a href=\"{$sortbaseurl}&amp;tag_sortorder=$sort\">$caption</a> <br/>";
		}
		$tag_listing .= '</p>';
		
		/* create tag listing */
		$all_tags = $this->getAllTags($sortorder);
		$tag_listing .= '<ul>';
		foreach($all_tags as $tag => $count)
			$tag_listing .= '<li><span style="cursor: pointer;" onclick="javascript:updateTagFields(this.innerHTML);">'.$tag.'</span>&nbsp;<a href="'.$this->sTagObj->getTagPermalink($tag).'" title="'.sprintf(__('View all posts tagged with %s', 'simpletagging'), $tag).'">('.$count.')</a></li>'."\n";
		$tag_listing .= '</ul>';
		
		echo $pagemsg;
		?>
		<div class="wrap">			
			<h2><?php _e('Simple Tagging: Manage Tags', 'simpletagging'); ?></h2>
			<p><?php _e('Visit the <a href="http://trac.herewithme.fr/project/simpletagging/">plugin\'s homepage</a> for further details.', 'simpletagging'); ?></p>
			<table>
				<tr>
					<td style="vertical-align: top; border-right: 1px dotted #ccc;">				
						<fieldset class="options" id="taglist"><legend><?php _e('Existing Tags', 'simpletagging'); ?></legend>
							<?php echo $tag_listing; ?>
						</fieldset>				
					</td>					
					<td style="vertical-align: top;">				
						<fieldset class="options"><legend><?php _e('Rename Tag', 'simpletagging'); ?></legend>
							<p><?php _e('Enter the tag to rename and its new value.  You can use this feature to merge tags too. Click "Rename" and all posts which use this tag will be updated.', 'simpletagging'); ?></p>
							<p><?php _e('You can specify multiple tags to rename by separating them with commas.', 'simpletagging'); ?></p>
							<form action="<?php echo $actionurl; ?>" method="post">
								<input type="hidden" name="tag_action" value="renametag" />
								<table>
									<tr><th><?php _e('Tag(s) to Rename:', 'simpletagging'); ?></th><td> <input type="text" id="renametag_old" name="renametag_old" value="" size="40" /> </td></tr>
									<tr><th><?php _e('New Tag Name(s):', 'simpletagging'); ?></th><td> <input type="text" id="renametag_new" name="renametag_new" value="" size="40" /> </td></tr>
									<tr><th></th><td> <input type="submit" name="Rename" value="<?php _e('Rename', 'simpletagging'); ?>" /> </td></tr>
								</table>
							</form>
						</fieldset>				
						<fieldset class="options"><legend><?php _e('Delete Tag', 'simpletagging'); ?></legend>
							<p><?php _e('Enter the name of the tag to delete.  This tag will be removed from all posts.', 'simpletagging'); ?></p>
							<p><?php _e('You can specify multiple tags to delete by separating them with commas', 'simpletagging'); ?>.</p>
							<form action="<?php echo $actionurl; ?>" method="post">
								<input type="hidden" name="tag_action" value="deletetag" />
								<table>
									<tr><th><?php _e('Tag(s) to Delete:', 'simpletagging'); ?></th><td> <input type="text" id="deletetag_name" name="deletetag_name" value="" size="40" /> </td></tr>
									<tr><th></th><td> <input type="submit" name="Delete" value="<?php _e('Delete', 'simpletagging'); ?>" /> </td></tr>
								</table>
							</form>
						</fieldset>					
						<fieldset class="options"><legend><?php _e('Add Tag', 'simpletagging'); ?></legend>
							<p><?php _e('This feature lets you add one or more new tags to all posts which match any of the tags given.', 'simpletagging'); ?></p>
							<p><?php _e('You can specify multiple tags to add by separating them with commas.  If you want the tag(s) to be added to all posts, then don\'t specify any tags to match.', 'simpletagging'); ?></p>
							<form action="<?php echo $actionurl; ?>" method="post">
								<input type="hidden" name="tag_action" value="addtag" />
								<table>
									<tr><th><?php _e('Tag(s) to Match:', 'simpletagging'); ?></th><td> <input type="text" id="addtag_match" name="addtag_match" value="" size="40" /> </td></tr>
									<tr><th><?php _e('Tag(s) to Add:', 'simpletagging'); ?></th><td>   <input type="text" id="addtag_new" name="addtag_new" value="" size="40" /> </td></tr>
									<tr><th></th><td> <input type="submit" name="Add" value="<?php _e('Add', 'simpletagging'); ?>" /> </td></tr>
								</table>
							</form>
						</fieldset>				
					</td>
				</tr>
			</table>
			
			<script type="text/javascript">
				if(document.all && !document.getElementById) {
					document.getElementById = function(id) { return document.all[id]; }
				}
				function addTag(tag, input_element) {
					if (input_element.value.length > 0 && !input_element.value.match(/,\s*$/))
						input_element.value += ", ";
					var re = new RegExp(tag + ",");
					if (!input_element.value.match(re))
						input_element.value += tag + ", ";
				}
				function updateTagFields(tag) {
					addTag(tag, document.getElementById("renametag_old"));
					addTag(tag, document.getElementById("deletetag_name"));
					addTag(tag, document.getElementById("addtag_match"));
				}			
			</script>			
			<?php $this->print_admin_footer(); ?>
		</div> <!-- wrap -->
		<?php
	} // function stp_page_manage()

	######################################################################################################################
	# ADMIN MENU: IMPORT
	######################################################################################################################
	function stp_page_import() {
		global $wpdb;

		$UTW_tTags			= $this->import['UTW_tTags'];
		$UTW_tTagsID		= $this->import['UTW_tTagsID'];
		$UTW_tTagsName		= $this->import['UTW_tTagsName'];
		$UTW_tP2T			= $this->import['UTW_tP2T'];
		$UTW_tP2TtagID		= $this->import['UTW_tP2TtagID'];
		$UTW_tP2TpostID		= $this->import['UTW_tP2TpostID'];
		$jk2table 			= $this->import['JK2_jkeywords'];

		// Handle form actions
		if ( isset($_POST['tag_action']) ) {
			if ($_POST['tag_action'] == 'convertcatstotags' ) {
				if (isset($_POST['convertcatstotags_confirm']) && $_POST['convertcatstotags_confirm'] == 1 ) {
					$pagemsg = $this->STP_ConvertCatsToTags();
				} else {
					$pagemsg = __('Error: You clicked the &laquo;Category&raquo; import button but you did not confirm that you have backuped your database.', 'simpletagging');		
				}
			} elseif ($_POST['tag_action'] == 'importfromutw' ) {
				if (isset($_POST['importfromutw_confirm']) && $_POST['importfromutw_confirm'] == 1 ) {
					$pagemsg = $this->STP_ImportFromUTW();
				} else {
					$pagemsg = __('Error: You clicked the &laquo;UTW&raquo; import button but you did not confirm that you have backuped your database.', 'simpletagging');
				}
			} elseif ($_POST['tag_action'] == 'importfromjk19' ) {
				if (isset($_POST['importfromjk19_confirm']) && $_POST['importfromjk19_confirm'] == 1 ) {
					$pagemsg = $this->STP_ImportFromJK19();
				} else {
					$pagemsg = __('Error: You clicked the &laquo;Jerome\'s Keywords 1.9&raquo; import button but you did not confirm that you have backuped your database.', 'simpletagging');
				}
			} elseif ($_POST['tag_action'] == 'importfromjk20beta' ) {
				if (isset($_POST['importfromjk20beta_confirm']) && $_POST['importfromjk20beta_confirm'] == 1 ) {
					$pagemsg = $this->STP_ImportFromJK20Beta();
				} else {
					$pagemsg = __('Error: You clicked the &laquo;Jerome\'s Keywords 2.0&raquo; import button but you did not confirm that you have backuped your database.', 'simpletagging');		
				}
			}
		}
		
		if (!empty($pagemsg)) {
			$pagemsg = '<div id="message" class="updated fade"><p><strong>' . $pagemsg . '</strong></p></div>';
		}

		$actionurl = stripslashes($_SERVER['REQUEST_URI']);

		?>
		<?php echo $pagemsg; ?>
		<div class="wrap">		
		<h2><?php _e('Simple Tagging: Import Tags', 'simpletagging'); ?></h2>
		<p><?php _e('Howdy! Here you can import tags from several different sources.', 'simpletagging'); ?></p>
		<p><?php _e('These importers are smart enough not to import duplicates, so you can run these multiple times without worry if — for whatever reason — they don\'t finish.', 'simpletagging'); ?></p>
		<p><?php _e('<strong>Please note</strong> that importing tags could break your entire blog. Make sure you back up your database before clicking the button.', 'simpletagging'); ?></p>
		<p><?php _e('Visit the <a href="http://trac.herewithme.fr/project/simpletagging/">plugin\'s homepage</a> for further details.', 'simpletagging'); ?></p>
		
		<hr />
		<!-- **************************************************************************************** -->		
		<h3><?php _e('Convert Categories to Tags', 'simpletagging'); ?></h3>		
		<fieldset class="options">
			<form action="<?php echo $actionurl; ?>" method="post">
				<input type="hidden" name="tag_action" value="convertcatstotags" />
				<label for="convertcatstotags_confirm">
					<input name="convertcatstotags_confirm" type="checkbox" id="convertcatstotags_confirm" value="1" /><span style="color: blue;"> <?php _e('I\'ve backuped my database.', 'simpletagging'); ?></span>
				</label>
				<br /><br />
				<input type="submit" name="Convert Categories to Tags" value="<?php _e('Convert Categories to Tags', 'simpletagging'); ?>" />
			</form>
		</fieldset>
		
		<hr />		
		<!-- **************************************************************************************** -->
		<h3><?php _e('Ultimate Tag Warrior Plugin (UTW) Version 3.141', 'simpletagging'); ?></h3>
		<?php		
		// Do UTW tables exist?
		$notexistingtable = array();
		if($wpdb->get_var("SHOW TABLES LIKE '$UTW_tTags'") != $UTW_tTags) {
			$notexistingtable[] = $UTW_tTags;
		}
		if($wpdb->get_var("SHOW TABLES LIKE '$UTW_tP2T'") != $UTW_tP2T) {
			$notexistingtable[] = $UTW_tP2T;
		}
		if (count($notexistingtable) != 0 ) {
			$notexistingtable_str = implode(' , ', $notexistingtable);
			echo sprintf(__('<p>A supported UTW installation could not be found, missing table(s): <code>%s</code></p>', 'simpletagging'), $notexistingtable_str);
		}
		else {			
			?>
			<p><?php _e('This function has been tested with UTW version 3.141 only, other versions may not work and if you use another version it is even more important to back up your database before you click the button.', 'simpletagging'); ?></p>
	
			<fieldset class="options">
				<form action="<?php echo $actionurl; ?>" method="post">
					<input type="hidden" name="tag_action" value="importfromutw" />
					<label for="importfromutw_confirm">
						<input name="importfromutw_confirm" type="checkbox" id="importfromutw_confirm" value="1" /><span style="color: blue;"> <?php _e('I\'ve backuped my database.', 'simpletagging'); ?></span>
					</label>
					<br /><br />
					<input type="submit" name="Import from UTW" value="<?php _e('Import from UTW', 'simpletagging'); ?>" />
				</form>
			</fieldset>
			<?php 
			
		} // if (count($notexistingtable) != 0 ) {
		?>		
		
		<hr />
		<!-- **************************************************************************************** -->
		<h3><?php _e('Jerome\'s Keywords Plugin Version 1.9', 'simpletagging'); ?></h3>

		<?php
		/* import Jerome's Keywords 1.9 */
		$qry = "SELECT post_id, meta_id, meta_key, meta_value FROM  {$wpdb->postmeta} meta WHERE meta.meta_key = 'keywords'";
		$metakeys = $wpdb->get_results($qry);
		if (count($metakeys) > 0) {
		?>			
			<fieldset class="options">
				<form action="<?php echo $actionurl; ?>" method="post">
					<input type="hidden" name="tag_action" value="importfromjk19" />
					<label for="importfromjk19_confirm">
						<input name="importfromjk19_confirm" type="checkbox" id="importfromjk19_confirm" value="1" /><span style="color: blue;"> <?php _e('I\'ve backuped my database.', 'simpletagging'); ?></span>
					</label>
					<br /><br />
					<input type="submit" name="Import from Jerome's Keywords 1.9" value="<?php _e('Import from Jerome\'s Keywords 1.9', 'simpletagging'); ?>" />
				</form>
			</fieldset>
			<?php
		} else
			_e('<p>Tags from Jerome\'s Keywords Plugin version 1.9 have not been found...</p>', 'simpletagging');
		?>		
		<hr />

		<h3><?php _e('Jerome\'s Keywords Plugin Version 2.0 Beta 3', 'simpletagging'); ?></h3>		
		<?php
		
		if($wpdb->get_var("SHOW TABLES LIKE '{$jk2table}'") != $jk2table)
			_e('<p>A supported Jerome\'s Keywords 2.0 installation could not be found.</p>', 'simpletagging');
		else {
			?>			
			<fieldset class="options">
				<form action="<?php echo $actionurl; ?>" method="post">
					<input type="hidden" name="tag_action" value="importfromjk20beta" />
					<label for="importfromjk20beta_confirm">
						<input name="importfromjk20beta_confirm" type="checkbox" id="importfromjk20beta_confirm" value="1" /><span style="color: blue;"> <?php _e('I\'ve backuped my database.', 'simpletagging'); ?></span>
					</label>
					<br /><br />
					<input type="submit" name="Import from Jerome's Keywords 2.0 Beta" value="<?php _e('Import from Jerome\'s Keywords 2.0 Beta', 'simpletagging'); ?>" />
				</form>
			</fieldset>
			<?php
		} //
		?>		
		<?php $this->print_admin_footer(); ?>
		</div> <!-- wrap -->
		<?php
	} // function stp_page_import()


	######################################################################################################################
	# ADMIN MENU: DISPLAY NOT TAGGED ARTICLES
	######################################################################################################################
	function stp_page_not_tagged() {
		global $wpdb;
		$actionurl = stripslashes($_SERVER['REQUEST_URI']);

		// Prepare drop-down
		$dposts = ($this->sTagObj->is_wp_21) ? "AND posts.post_type = 'post' AND posts.post_status = 'publish'" : "AND posts.post_status = 'publish'";
		$dpages = ($this->sTagObj->is_wp_21) ? "AND posts.post_type = 'page' AND posts.post_status = 'publish'" : "AND posts.post_status = 'static'";
		$dboth = ($this->sTagObj->is_wp_21) ? "AND posts.post_type IN('page','post') AND posts.post_status = 'publish'" : "AND posts.post_status IN('static','publish')";

		$opt = array (
			'sortorder' => 	array(
				'title'		=> array(__('Sort by Title', 'simpletagging'), 'posts.post_title ASC'),
				'date_desc'	=> array(__('Sort by Date Descending', 'simpletagging'), 'posts.post_date DESC'),
				'date_asc'	=> array(__('Sort by Date Ascending', 'simpletagging'), 'posts.post_date ASC')
			),	
			'postspages' => array(
				'posts'			=> array(__('Show Posts', 'simpletagging'), $dposts),
				'pages'			=> array(__('Show Pages', 'simpletagging'), $dpages),
				'postsandpages'	=> array(__('Show Posts and Pages', 'simpletagging'), $dboth)
			)
		);

		foreach ($opt as $optname => $optionvalues) {
			$list[$optname] = '';
			foreach ($optionvalues as $optval => $cont) {
				$selected = (isset($_POST[$optname]) && $optval == $_POST[$optname] ) ? ' selected="selected"' : '';
				$list[$optname] .= '<option' . $selected . ' value="' . $optval . '">' . $cont[0] . '</option>';		
			}
			if ( isset($_POST[$optname] ) ) {
				$curr[$optname] = $optionvalues[$_POST[$optname]][1];
			} else {
				// Standard value is first value of array, so let's retrieve it:
				$output = array_values($optionvalues);		
				$curr[$optname] = $output[0][1];
			}
		}

		// SQL query
		$query = "SELECT posts.post_title, posts.post_date, posts.id
				FROM {$wpdb->posts} AS posts LEFT JOIN {$this->sTagObj->info['stptable']} AS tags
				ON posts.id = tags.post_id
				WHERE tags.post_id IS NULL
				{$curr['postspages']}
				AND posts.post_date < '" . current_time('mysql') . "'
				GROUP BY posts.post_title
				ORDER BY {$curr['sortorder']}
				";
		$queryresult = $wpdb->get_results($query);
		
		$result = '';
		if ( !is_null($queryresult) && is_array($queryresult) ) {
			foreach ($queryresult as $postloop) {
				$lp_date = mysql2date(get_option('date_format'), $postloop->post_date);
				$lp_title = $postloop->post_title;
				$lp_id = $postloop->id;
				$lp_perma = get_permalink($postloop->id);
				
				$result .= '<li><a title="' . $lp_title . ' (ID: ' . $lp_id . ')" href="' . $lp_perma . '">' . $lp_title . '</a> (' . $lp_date . ')';
				if ( current_user_can('edit_post', $lp_id) )
				$result .= ' [<a title="' . __('Edit Post', 'simpletagging') . '" href="post.php?action=edit&amp;post=' . $lp_id . '">' . __('Edit', 'simpletagging') . '</a>]';
				$result .= '</li>';
			}
		} 

		echo "\n" . '<div class="wrap">' . "\n"; 
		echo '<h2>'. __('Simple Tagging: Not Tagged Articles', 'simpletagging') .'</h2>' . "\n";
		?>

		<form name="viewposts" action="<?php echo $actionurl; ?>" method="post" >
			<fieldset>
				<select name='sortorder'>
					<?php echo $list['sortorder']; ?>
				</select>
				<select name='postspages'>
					<?php echo $list['postspages']; ?>
				</select>
				<input type="submit" name="changelist" value="<?php _e('Show', 'simpletagging'); ?>"  /> 
			</fieldset>
		</form>
		<br />
		<?php
		if ($result == '') {
			_e('<p>Every article is tagged :-)</p>', 'simpletagging');
		} else {
			_e('<p>The following articles are not tagged:</p>', 'simpletagging');
			echo "\n" . '<ul>' . "\n".$result."\n" . '</ul>' . "\n";
		}
		?>
		<?php $this->print_admin_footer(); ?>
		</div>
	<?php
	} // function stp_menu_not-tagged()
	
	/**
	* regexEscape
	*
	* Escape string so that it can used in Regex. E.g. used for [tags]...[/tags]
	*/
	function regexEscape($str) {
		$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('/', '\\/', $str);
		$str = str_replace('[', '\\[', $str);
		$str = str_replace(']', '\\]', $str);
		
		return $str;
	}
} // class SimpleTaggingAdmin
?>