<?php
# import Dotclear 1.2.x or Dotclear 2.0-beta into WordPress 2.0.x, 2.1, 2.2
if ( @ini_get('max_execution_time') == 50000 ) {
	@ini_set('max_execution_time', 38); # try to modify the max_execution_time for 1and1 50000 seconds
} elseif ( @ini_get('max_execution_time') < 1200 ) {
	@ini_set('max_execution_time', 1200); # try to modify the max_execution_time ; Free.fr 20 or 30 seconds
}
/** The Main Importer Class **/
class Flat_Import {

	function import_flat_file()
	{
		global $wpdb, $utw, $STagging, $STaggingAdmin, $fi_import_format, $fi_dc_tables, $fi_dc_cats, $fi_dcposts2wpposts, $fi_linkcat_pos, $fi_linkcat_link_id, $fi_link_pos, $fi_link_link_id, $fi_dcid2wpid, $post_old_strings, $post_new_strings, $wp_version, $wp_minor_version, $fi_update_authorized;
		
		$flatimport_version = '1.1.1'; # 11:51 04/07/2007
		$time_start = time();
		
		$table_recover_count = 20; # a point of control is taken after importing the following number of values for a given table
		$time_to_reload = 1; # number of seconds to substract from max_execution_time to have the timeout to reload the current page
		/*___________________________________________________________________________________________________*/
		$max_execution_time = @ini_get('max_execution_time'); # usually 30 seconds, Free.fr 20 or 30 ; 1and1 50000
		# uncomment the following line to force the timeout ; set the value to 38 for 1and1 server ; or other appropriate value
		# $max_execution_time = 38;
		/*___________________________________________________________________________________________________*/
		$automatic_recovery = false;
		if ( is_numeric($max_execution_time) ) { # validate if the server return a positive number
			if ($max_execution_time > 3) { # to control lower values: do not handle by automatic recovery a max_execution_time lower than this value
				$automatic_recovery = true;
			}
			if ($max_execution_time < 0) {
				$max_execution_time = NULL;
			}
		} else {
			$max_execution_time = NULL;
		}
		$wp_minor_version = (real)substr($wp_version, 0, 3);
		# comment/uncomment the following line to enable/disable the automatic recovery feature. The user must click on the displayed Continue button to proceed if disabled
		#$automatic_recovery = false;
		
		$post_old_strings = array();
		$post_new_strings = array();
		if ( file_exists(dirname(__FILE__).'/../../wp-content/uploads/blog-backup-change.php') )
			include_once(dirname(__FILE__).'/../../wp-content/uploads/blog-backup-change.php');
		$fi_filename = dirname(__FILE__).'/../../wp-content/uploads/blog-backup.txt';
		$post_id_filename = dirname(__FILE__).'/../../wp-content/uploads/post_id_dc2wp.php';
		$fi_import_format = '';
		$fi_recover_filename = dirname(__FILE__).'/../../wp-content/uploads/blog-backup-recovery.txt';
		$fi_recover_mode = false;
		$recover_names = array(
			'fi_filename_size',
			'fi_file_pointer',
			'fi_dc_tables',
			'fi_dc_cats',
			'fi_dcposts2wpposts',
			'fi_table_cols',
			'fi_linkcat_pos',
			'fi_linkcat_link_id',
			'fi_link_pos',
			'fi_link_link_id',
			'fi_dcid2wpid',
			'fi_table_name',
			'fi_table_name_found',
			'fi_table_import',
			'fi_update_authorized'
			);
		$dc_version = '';
		$replacement = array(
		'\n' => "\n",
		'\r' => "\r",
		'\"' => '"'
		);

		if ( file_exists($fi_recover_filename) )
		{
			if ( filesize($fi_recover_filename) > 0) {
				# test for a non empty point of control file (uses dynamic variables $$name and serialyse unserialyse), if yes proceed to recovery
				$fi_recover = @fopen($fi_recover_filename, "r");
				if ($fi_recover) {
					while (!feof($fi_recover)) {
						$serialized_line = fgets($fi_recover);
						$name_recover = trim(substr($serialized_line, 0, strpos($serialized_line, ' ')));
						$$name_recover = unserialize(trim(substr($serialized_line, strpos($serialized_line, ' '))));
					}
					fclose($fi_recover);
					/* dump recover vars
					foreach ($recover_names as $value) {
						echo '<pre>';
						print_r($$value);
						echo '</pre>';
						echo '<hr />';
					}
					# */
					$fi_recover_mode = true;
					# vérif des variables en recovery
					foreach ($recover_names as $value) {
						if ( !isset($$value) ) $fi_recover_mode = false;
					}
					if ( $fi_recover_mode ) {
						# check that the recovery data applies to the backup file
						if ( $fi_filename_size != filesize($fi_filename) ) $fi_recover_mode = false;
					}
					if ( $fi_recover_mode ) {
						$count = $fi_dc_tables[$fi_table_name] + $table_recover_count; # reinitialize count
					} else {
						$import_type_str = ($fi_update_authorized) ? ('<strong>'.__('Update').'</strong>') : ('<strong>'.__('Import').'</strong>');
						echo '<p><strong>'.__('Warning:').'</strong> '.__('Non valid blog-backup-recovery.txt').' ; '.__('switching to').' '.$import_type_str.'</p>';
					}
				}
			}
		}
		if ( !$fi_recover_mode ) {
			$fi_dc_tables = array(
				'setting' => 0,
				'user' => 0,
				'categorie' => 0,
				'category' => 0,
				'link' => 0,
				'post' => 0,
				'comment' => 0,
				'meta' => 0,
				'post_meta' => 0,
				'blog' => 0,
				'media' => 0,
				'version' => 0
				);
			# categories dc ; nécessaires pour import des posts ; pour dc : id nom 
			$fi_dc_cats = array(); # id du post dc => nom interne de la catégorie
			# association des commentaires aux billets (tableau de correspondance des IDs, table de translation des post_id dc en ID WP)
			$fi_dcposts2wpposts = array(); # table de translation des post_id dc en ID WP
			# association des liens à leur catégorie par position
			$fi_linkcat_pos = array(); # on associe au cat_id ds wp_linkcategories la position dc
			$fi_linkcat_link_id = array(); # on associe au cat_id ds wp_linkcategories le link_id dc
			$fi_link_pos = array(); # on associe au link_id ds wp_links la position dc
			$fi_link_link_id = array(); # on associe au cat_id ds wp_linkcategories le link_id dc
			$fi_dcid2wpid = array(); # key: dc user_id (admin) => value: wp ID (1)
			$fi_table_name = '';
			$fi_table_name_found = array();
			$fi_table_import = false;
		}
		
		$backup = @fopen($fi_filename, "r");
		if ($backup)
		{
			$first_line = fgets($backup);
			
			if (strpos($first_line,'///DOTCLEAR|') === false) { # accepte fichier avec ou sans BOM
				echo __('File is not a DotClear backup.');
				return;
			}
			
			$l = explode('|',$first_line);
			
			if (isset($l[1])) {
				$dc_version = $l[1];
			}
			/*
			$mode = isset($l[2]) ? strtolower(trim($l[2])) : 'single';
			if ($mode != 'full' && $mode != 'single') {
				$mode = 'single';
			}
			# */
			$fi_import_format = substr($dc_version, 0 ,3);
			
			if ( ($fi_import_format == '1.2') OR ($fi_import_format == '2.0') ) {
				$import_type_str = ($fi_update_authorized) ? ('<strong>'.__('Update').'</strong>') : ('<strong>'.__('Import').'</strong> ('.__('only new data').')');
				$auto_str = $automatic_recovery ? (__('Automatic recovery')) : ('<strong>'.__('No automatic recovery').'</strong>');
				$timeout_str = is_null($max_execution_time) ? '' : (__('Timeout').': '.$max_execution_time.' '.__('seconds'));
				Flat_Import::say('Version: '.$flatimport_version.' - '.$import_type_str.' '.__('from Dotclear version').' <strong>'.$dc_version.
				'</strong> to WordPress <strong>'.get_bloginfo('version').
				'</strong> - '.__('data base formats').': dc '.$fi_import_format.' wp '.$wp_minor_version.' - '.
				$auto_str.' - '.$timeout_str.'<br />');
			} else {
				echo __('Unknown version of Dotclear for import file.').' - '.$first_line.' - '.$dc_version.' - '.$fi_import_format.'<br />';
				return;
			}
			if ($fi_recover_mode) { # the script was interrupted by the server ; retry from the last point of control taken
				fseek($backup, $fi_file_pointer);
				Flat_Import::say('<strong>'.__('Entering recovery mode').'</strong> '.__('from the pointer position:').
				' <strong>'.number_format($fi_file_pointer, 0, ',', ' ').'</strong> - '.
				Flat_Import::filesize_format($fi_file_pointer).'<br />');
			}
			while (!feof($backup))
			{
				$line = trim(fgets($backup));
				
				if (substr($line,0,1) == '[') # importation d'une table : le nom de la table est suivi des noms de champs
				{
					if ( $fi_table_import AND ($fi_table_name != '') AND ($fi_dc_tables[$fi_table_name] > 0) )
					# affichage de fin d'import d'une table et du nombre d'éléments traités
					{
						Flat_Import::say('<strong>'.__('Import of').' '.$fi_table_name.' : '.$fi_dc_tables[$fi_table_name].'</strong>');
					}
					
					$count = $table_recover_count; # new table : init count to do a point of control and and display an user message every 20 values for a table
					
					$fi_table_name = substr($line,1,strpos($line,' ')-1);
					$line = substr($line,strpos($line,' ')+1,-1);
					$fi_table_cols = explode(',',$line);
					$fi_table_import = true;
					if (array_key_exists($fi_table_name, $fi_dc_tables)) {
						$fi_table_name_found[] = $fi_table_name;
						# conditions d'import, précédence des tables
						if ( ($fi_table_name == 'post_meta') OR ($fi_table_name == 'meta') ) {
							if ( in_array('post', $fi_table_name_found) ) {
								if ( class_exists('UltimateTagWarriorCore') ) {
									$utw = new UltimateTagWarriorCore();
									echo 'UltimateTagWarriorCore class found. Importing tags.<br />';
								} elseif ( class_exists('SimpleTagging') ) {
									$STaggingAdmin = new SimpleTaggingAdmin($STagging);
									echo 'SimpleTagging class found. Importing tags.<br />';
								} else {
									$fi_table_import = false;
									echo '<strong>No import of tags</strong>. Ultimate Tag Warrior or SimpleTagging plugin not found ; or plugin not activated.<br />';
								}
							} else {
								$fi_table_import = false;
								Flat_Import::say('<strong>'.__('Error:').'</strong> '.__('No import of tags. No posts found.'));
							}
						}
						if ($fi_table_name == 'comment') {
							if ( !in_array('post', $fi_table_name_found) ) {
								$fi_table_import = false;
								Flat_Import::say('<strong>'.__('Error:').'</strong> '.__('No import of comments. No posts found.'));
							}
						}
						if ($fi_table_name == 'post') {
							$post_import_msg = true;
							if (!( ( in_array('categorie', $fi_table_name_found) ) OR ( in_array('category', $fi_table_name_found) ) )) {
								$post_import_msg = false;
								Flat_Import::say('<strong>'.__('Warning:').'</strong> '.__('No categories found. All posts associated to the ID 1 category'));
							}
							if ( !( in_array('user', $fi_table_name_found) ) ) {
								Flat_Import::say('<strong>'.__('Warning:').'</strong> '.__('No users found. All posts associated to admin'));
							}
						}
					} else {
						echo __('No import for table').' <strong>'.$fi_table_name.'</strong><br />';
						$fi_table_import = false;
					}
				}
				
				elseif (substr($line,0,1) == '"') # importation des valeurs d'une table
				{
					if ($fi_table_import) {
						if (array_key_exists($fi_table_name, $fi_dc_tables)) {
							$line1 = preg_replace('/^"|"$/','',$line);
							$line_split = preg_split('/(^"|","|"$)/m',$line1);
							
							if (count($fi_table_cols) != count($line_split)) {
								echo __('Import of').' '.$fi_table_name.': invalid row count'.' - '.htmlspecialchars($line).'<br />';
							} else {
								#Flat_Import::say(substr($line,0,100).'&hellip;'.'|'.$fi_dc_tables[$fi_table_name].'|'.$count.'|'.'<br />');
							}
							$res = array();
							for ($i=0; $i<count($line_split); $i++) {
								$res[$fi_table_cols[$i]] =
								str_replace(array_keys($replacement),array_values($replacement),$line_split[$i]);
							}
							call_user_func(array('Flat_Import', $fi_table_name), $res); # appel des fonctions d'import, import des valeurs d'une entrée de table
							
							# message and point of control every 20 table values ( value of $table_recover_count) or timeout reached
							if ( is_null($max_execution_time) ) {
								$exec_time_left = 1; # set it for a positive value so we will never reload the current page
								$exec_time_left_str = '';
							} else {
								$exec_time_now = time()- $time_start;
								$exec_time_left = ($max_execution_time - $time_to_reload) - $exec_time_now;
								$exec_time_left_str = '+'.$exec_time_now.' - '.$exec_time_left.' '.__('seconds left');
							}
							if ( ($fi_dc_tables[$fi_table_name] == $count) OR ($exec_time_left <= 0) ) {
								$count = $count + $table_recover_count;
								
								Flat_Import::say(__('Import of').' '.$fi_table_name.' : '.$fi_dc_tables[$fi_table_name].'&hellip;'.$exec_time_left_str);
								
								# take a point of control ; for blog-backup.txt : size to check the right file, pointeur position, then needed variables
								# serialize() arrays, vars and save them to be able to recover an interrupted import ; uses dynamic variables
								
								# delete the backup of the recovery file ; then backup it ; to allow manual recovery if the following code is interrupted
								if ( file_exists($fi_recover_filename.'.bak') ) unlink($fi_recover_filename.'.bak');
								if ( file_exists($fi_recover_filename) ) rename($fi_recover_filename, $fi_recover_filename.'.bak');
								
								$fi_filename_size = filesize($fi_filename); # to check file at recovery time
								$fi_file_pointer = ftell($backup); # take the current pointer position
								$fi_recover = fopen($fi_recover_filename, 'wb'); # open and create or empty the recovery file
								foreach ($recover_names as $value) {
									fwrite($fi_recover, $value.' '.serialize($$value)."\r\n"); # output serialysed vars
								}
								fclose($fi_recover);
								
								# reload the current page if timeout reached either by meta refresh or by clic on the Continue button
								# cannot use register_shutdown_function(): one cannot send any HTTP header in the shutdown callback
								# When zlib compression is enabled, register_shutdown_function doesn't work properly.
								# Anything output from your shutdown function will not be readable by the browser. Firefox just ignores it. IE6 will show you a blank page.
								if ($exec_time_left <= 0) {
									$recover_step = ($fi_update_authorized) ? 2 : 1; # Update : Import
									echo '<form action="admin.php?import=flatimport&amp;step='.$recover_step.'" method="post">';
									echo '<input type="submit" name="submit" value="'.__('Continue').'" />';
									echo ' '.__('Click on the <strong>Continue</strong> button to continue the import <em>(by reloading the page to reset the timeout)</em>').'.';
									echo '</form>';
									
									if ($automatic_recovery) {
										/*
										We want to avoid any  message like: Warning: the page you are refreshing contains POST data... with a mandatory click needed.
										This is the case here for PHP header(Location: ... and JavaScript location.reload() ; some remaining blanks characters or others not found.
										
										META refresh is deprecated by W3C ; should not be used for redirecting to a new address (this is not the case here).
										Reloading the current page using meta refresh works for Firefox 2 and IE7.
										
										Do the meta refresh, it won't resend the data, 0 is the time in seconds, reload the current page as the url is omitted.
										The user will use the Continue button if the meta refresh is not granted by the navigator or the server.
										*/
										echo '<meta http-equiv="refresh" content="0">';
									}
									# terminate
									fclose($backup);
									ob_flush();flush(); # send all
									exit; # end the execution of this script
								}
							}
						}
					}
				}
				else
				{
					if ($line != '') {
						if (strlen($line) > 100) $line = substr($line,0,100).'&hellip;';
						echo __('Unknown line').' <i>'.htmlspecialchars($line).'</i><br />';
					}
				}
			}
			fclose($backup);
			if ( ($fi_table_name != '') AND ($fi_dc_tables[$fi_table_name] > 0) )
				echo '<strong>'.__('Import of').' '.$fi_table_name.' : '.$fi_dc_tables[$fi_table_name].'</strong><br />';
			if ( (count($fi_linkcat_pos)>0) AND (count($fi_link_pos)>0) ) {
				# associate links to cat based on dc position or link_id if all dc positions at zero
				# $fi_linkcat_pos associe au cat_id ds wp_categories la position dc
				# $fi_linkcat_link_id associe au cat_id ds wp_categories le link_id dc
				# $fi_link_pos associe au link_id ds wp_links la position dc
				# $fi_link_link_id associe au link_id ds wp_links le link_id dc
				$link_pos_cumul = eval('return ' . implode('+', $fi_link_pos) . ';');
				if ($link_pos_cumul == 0) { # on prend le link_id si les positions sont à zéro
					$fi_linkcat_pos = $fi_linkcat_link_id;
					$fi_link_pos = $fi_link_link_id;
				}
				asort($fi_linkcat_pos); # tri les cat de liens par ordre croissant pour comparaison
				echo 'Associate links to categories.'.'<br />';
				foreach ($fi_link_pos as $_id => $_pos) { # pour chaque lien
					$link_cat = false;
					foreach ($fi_linkcat_pos as $_catid => $_catpos) {
						if ($_pos > $_catpos) {
							$link_cat = $_catid; # recherche d'une cat de lien en position avant celle du lien
						}
					}
					if ($link_cat === false) {
						$link_cat = '1';
						echo __('Orphan link').' '.$_id.' - '.__('associated to link category ID 1').'<br />';
					}
					if ($wp_minor_version >= 2.1) {
						$ret_id = wp_set_link_cats($_id, array($link_cat)); # maj table cats WP2.1 WP2.2
					} else {
						$ret_id = wp_update_link( array(
										'link_id'		=> $_id,
										'link_category'	=> $link_cat
										)
										);
					}
				}
			}
			# Import post-processing
			/*
			# on archive la table de translation des post_id dc en ID WP ;  $fi_dcposts2wpposts
			$post_id_dc2wp = fopen($post_id_filename, 'wb'); # open and create or empty the file
			fwrite($post_id_dc2wp, '<?php'."\r\n".'$post_old_strings = array('."\r\n");
			foreach ($fi_dcposts2wpposts as $key => $value) {
				fwrite($post_id_dc2wp, "'url_dc".$key."',"."\r\n");
			}
			fwrite($post_id_dc2wp, ');'."\r\n".'$post_new_strings = array('."\r\n");
			foreach ($fi_dcposts2wpposts as $key => $value) {
				fwrite($post_id_dc2wp, "'url_wp".$value."',"."\r\n");
			}
			fwrite($post_id_dc2wp, ');'."\r\n".'?>'."\r\n");
			fclose($post_id_dc2wp);
			#*/
			if ( file_exists($fi_recover_filename) ) unlink($fi_recover_filename);
			
			echo ' <strong>'.__('End of import').'.</strong> '.' - '.
			__('Read carefully the following page').'. '.__('Hit the finish button').'<br />';
			echo '<form action="admin.php?import=flatimport&amp;step=4" method="post">';
			printf('<input type="submit" name="submit" value="%s" />', __('Finish'));
			echo '</form>';
			# End of import post-processing
		} else {
			echo '<p><strong>'.__('Warning:').'</strong> '.__('No import file found').
			' <em>wp-content/uploads/blog-backup.txt</em></p>'.'<p>'.__('Unable to open:').' '.$fi_filename.'</p>';
		}
	}
	
	function blog($blog) 
	{
	/*
	blog
	blog_id,blog_uid,blog_creadt,blog_upddt,blog_url,blog_name,blog_desc,blog_status
	*/
		global $wpdb, $fi_import_format, $fi_dc_tables;
		# nop
	}
	
	function setting($setting) 
	{
	/*
	setting
	setting_id,setting_type,setting_value
	
	setting
	setting_id,blog_id,setting_ns,setting_value,setting_type,setting_label
	*/
		global $wpdb, $fi_import_format, $fi_dc_tables;
		# nop
	}
	
	function media($media) 
	{
	/*
	media
	media_id,user_id,media_path,media_title,media_file,media_meta,media_dt,media_creadt,media_upddt,media_private
	*/
		global $wpdb, $fi_import_format, $fi_dc_tables;
		# nop
	}
	
	function version($version) 
	{
		global $wpdb, $fi_import_format, $fi_dc_tables;
		# nop
	}

	function category($category)
	{
		Flat_Import::categorie($category, 'category');
	}
	function categorie($category, $fi_table_name='categorie')
	{
	/*
	categorie
	cat_id,cat_libelle,cat_desc,cat_libelle_url,cat_ord
	
	category
	cat_id,blog_id,cat_title,cat_url,cat_desc,cat_position
	
	 wp_categories
	"cat_ID";"cat_name";"category_nicename";"category_description";"category_parent";"category_count"
	"1";"Non classé";"non-classe";;"0";"622"
	
	 wp_linkcategories
	 "cat_id";"cat_name";"auto_toggle";"show_images";"show_description";"show_rating";"show_updated";"sort_order";"sort_desc";"text_before_link";"text_after_link";"text_after_all";"list_limit"
	"1";"Blogroll";"N";"Y";"N";"Y";"Y";"rand";"N";"<li>";"<br />";"</li>";"-1"
	*/
		global $wpdb, $fi_dc_cats, $fi_import_format, $fi_dc_tables, $fi_update_authorized;
		
		extract($category);
		// Make Nice Variables
		if ($fi_import_format == '2.0') {
			$cat_libelle = $cat_title;
			$cat_libelle_url = $cat_url;
		}
		$fi_dc_cats[$cat_id] = $cat_libelle_url; # nécessaire pour import de post
		$name = $wpdb->escape($cat_libelle_url);
		$title = $wpdb->escape($cat_libelle);
		$desc = $wpdb->escape($cat_desc);

		if($cinfo = category_exists($name))
		{
			if ($fi_update_authorized) {
				$ret_id = wp_update_category(array(
					'cat_ID' => $cinfo,
					'category_nicename' => $name,
					'cat_name' => addslashes($title),
					'category_description' => $desc
					));
				$fi_dc_tables[$fi_table_name]++;
			} else {
				$ret_id = $cinfo;
			}
		}
		else
		{
			$ret_id = wp_insert_category(array(
				'category_nicename' => $name,
				'cat_name' => addslashes($title),
				'category_description' => $desc
				));
			$fi_dc_tables[$fi_table_name]++;
		}
		
	}

	function user($user)
	{
	/*
	user 1.2
	user_id,user_level,user_pwd,user_nom,user_prenom,user_pseudo,user_email,user_post_format,user_edit_size,user_pref_cat,user_lang,user_delta,user_post_pub
	"rssmaster","9","000","RSS Master","John","albaran","master@free.fr","wiki","10","1","fr","0","1"
	
	user 2.0
	user_id,user_super,user_status,user_pwd,user_recover_key,user_name,user_firstname,user_displayname,user_email,user_url,user_desc,user_default_blog,user_options,
	user_lang,user_tz,user_post_status,user_creadt,user_upddt

	 wp_users
	"ID";"user_login";"user_pass";"user_nicename";"user_email";"user_url";"user_registered";"user_activation_key";"user_status";"display_name"
	"1";"admin";"000";"admin";"xxxx@free.fr";"http://albert.premier.fr";"2006-11-12 14:27:39";;"0";"Albert Premier"
	
	 wp_usermeta
	"umeta_id";"user_id";"meta_key";"meta_value"
	"1";"1";"wp_user_level";"10"
	"2";"1";"wp_capabilities";"a:1:{s:13:\"administrator\";b:1;}"
	"3";"1";"first_name";"Albert"
	"4";"1";"last_name";"Premier"
	"5";"1";"nickname";"albprem"
	"6";"1";"description";"a propos"
	"11";"1";"jabber";
	"12";"1";"aim";
	"13";"1";"yim";
	"10";"1";"rich_editing";"true"
	
	*/
		global $wpdb, $fi_import_format, $fi_dc_tables, $fi_dcid2wpid, $fi_update_authorized;
		extract($user);
		if ($user_id == 'admin') {
			$fi_dcid2wpid[$user_id] = 1; # set dc user_id admin to wp user ID 1
			return; # don't modify WordPress admin
		}
		if ($fi_import_format == '2.0') {
			$user_nom = $user_name;
			$user_prenom = $user_firstname;
			$user_pseudo = $user_displayname;
		}

		$user_login = $wpdb->escape($user_id); # was $name
		if($user_pseudo != '') { # Dotclear 1.2 : on affiche le pseudo s'il existe, sinon prénom nom
		  $display_name = $wpdb->escape($user_pseudo);
		} else {
			if($user_prenom != '') {
				$display_name = $wpdb->escape(trim($user_prenom.' '.$user_nom));
			} else {
				$display_name = $wpdb->escape(trim($user_nom));
			}
		}
		if($uinfo = get_userdatabylogin($user_login))
		{
			if ($fi_update_authorized) {
				$ret_id = wp_update_user(array(
					'ID'		=> $uinfo->ID,
					'user_login'	=> $user_login,
					'user_nicename'	=> $display_name,
					'user_email'	=> $user_email,
					'user_url'	=> 'http://',
					'display_name'	=> $display_name
					));
				$fi_dc_tables['user']++;
			} else {
				$ret_id = $uinfo->ID;
				$update_meta = false;
			}
		}
		else 
		{
			$ret_id = wp_insert_user(array(
				'user_login'	=> $user_login,
				'user_nicename'	=> $user_pseudo,
				'user_email'	=> $user_email,
				'user_url'	=> 'http://',
				'display_name'	=> $display_name
				));
			$fi_dc_tables['user']++;
			$update_meta = true;
		}
		$fi_dcid2wpid[$user_id] = $ret_id;
		
		if ($update_meta) {
			// Set Dotclear-to-WordPress permissions translation
			// Update Usermeta Data
			$user = new WP_User($ret_id);
			$wp_perms = $user_level + 1;
			if(10 == $wp_perms) { $user->set_role('administrator'); }
			else if(9  == $wp_perms) { $user->set_role('editor'); }
			else if(5  <= $wp_perms) { $user->set_role('editor'); }
			else if(4  <= $wp_perms) { $user->set_role('author'); }
			else if(3  <= $wp_perms) { $user->set_role('contributor'); }
			else if(2  <= $wp_perms) { $user->set_role('contributor'); }
			else                     { $user->set_role('subscriber'); }
			
			update_usermeta( $ret_id, 'wp_user_level', $wp_perms);
			update_usermeta( $ret_id, 'rich_editing', 'false');
			update_usermeta( $ret_id, 'first_name', $user_prenom);
			update_usermeta( $ret_id, 'last_name', $user_nom);
			update_usermeta( $ret_id, 'nickname', $user_pseudo);
		}
	}// End function user

	function post($post)
	{
	/*
	SELECT dc_post.*, dc_categorie.cat_libelle_url AS post_cat_name FROM dc_post INNER JOIN dc_categorie ON dc_post.cat_id = dc_categorie.cat_id
	
	post
	post_id,user_id,cat_id,post_dt,post_creadt,post_upddt,post_titre,post_titre_url,post_chapo,post_chapo_wiki,post_content,post_content_wiki,
	post_notes,post_pub,post_selected,post_open_comment,post_open_tb,nb_comment,nb_trackback,post_lang
	
	post
	post_id,blog_id,user_id,cat_id,post_dt,post_tz,post_creadt,post_upddt,post_password,post_type,post_format,post_url,post_lang,post_title,post_excerpt,
	post_excerpt_xhtml,post_content,post_content_xhtml,post_notes,post_words,post_meta,post_status,post_selected,post_open_comment,post_open_tb,nb_comment,nb_trackback
	
	wp_posts
	"ID";"post_author";"post_date";"post_date_gmt";"post_content";"post_title";"post_category";"post_excerpt";"post_status";"comment_status";
	"ping_status";"post_password";"post_name";"to_ping";"pinged";"post_modified";"post_modified_gmt";"post_content_filtered";"post_parent";
	"guid";"menu_order";"post_type";"post_mime_type";"comment_count"

	"25";"36";"2004-05-28 09:57:50";"2004-05-28 09:57:50";"<p>Le vote Ã©lectronique est en vogue. ...</p>";"publish";"open";"open";;
	"vote-electronique-nouvelle-mode-dangereuse";;;"2004-05-28 09:57:50";"2004-05-28 09:57:50";;"0";"http://secondmonde.free.fr/blog/?p=25";"0";;;"0"

	wp_post2cat
	"rel_id";"post_id";"category_id"
	"9";"10";"1"
	*/
		global $wpdb, $fi_dc_cats, $fi_dcposts2wpposts, $fi_import_format, $fi_dc_tables, $fi_dcid2wpid, $post_old_strings, $post_new_strings, $post_import_msg, $fi_update_authorized;
		extract($post);
		if ($fi_import_format == '2.0') {
			$post_titre = $post_title;
			$post_content = $post_content_xhtml;
			$post_chapo = $post_excerpt_xhtml;
			$post_pub = $post_status;
		}
		if ( isset($post_type) ) {
			if($post_type != 'post') {
				$post_type = ($post_type == 'related') ? $post_type = 'page' : $post_type = 'post';
				}
		} else {
			$post_type = 'post';
		}
		# associate post to wp user ; if dc user not found default to user ID 1 admin
		if ( array_key_exists($user_id, $fi_dcid2wpid) ) {
			$post_author = $fi_dcid2wpid[$user_id];
		} else {
			$post_author = 1;
		}
		# replace strings, example: old URL by new URL
		$post_content = str_replace($post_old_strings, $post_new_strings, $post_content);

		// Set Dotclear-to-WordPress status translation
		$stattrans = array(0 => 'draft', 1 => 'publish');
		$comment_status_map = array (0 => 'closed', 1 => 'open');
		
		$Title = $wpdb->escape($post_titre);
		$post_content = preg_replace ('|(?<!<br />)\s*\n|', ' ', $post_content);
		$post_excerpt = ""; # http://trac.wordpress.org/ticket/2430
		if ($post_chapo != "") {
			$post_excerpt = preg_replace ('|(?<!<br />)\s*\n|', ' ', $post_chapo);
			$post_content = $post_excerpt ."\n<!--more-->\n".$post_content;
		}
		$post_excerpt = $wpdb->escape ($post_excerpt);
		$post_content = $wpdb->escape ($post_content);
		$post_status = $stattrans[$post_pub];
		
		// Import Post data into WordPress
		
		if($pinfo = post_exists($Title,$post_content))
		{
			if ($fi_update_authorized) {
				$ret_id = wp_update_post(array(
					'ID'				=> $pinfo,
					'post_author'		=> $post_author,
					'post_date'			=> $post_dt,
					'post_date_gmt'		=> $post_dt,
					'post_modified'		=> $post_upddt,
					'post_modified_gmt'	=> $post_upddt,
					'post_title'		=> $Title,
					'post_content'		=> $post_content,
					'post_excerpt'		=> $post_excerpt,
					'post_status'		=> $post_status,
					'post_name'			=> $post_titre_url,
					'comment_status'	=> $comment_status_map[$post_open_comment],
					'ping_status'		=> $comment_status_map[$post_open_tb],
					'post_type'			=> $post_type,
					'comment_count'		=> $post_nb_comment + $post_nb_trackback)
					);
				$fi_dc_tables['post']++;
			} else {
				$ret_id = $pinfo;
			}
		}
		else 
		{
			$ret_id = wp_insert_post(array(
				'post_author'		=> $post_author,
				'post_date'			=> $post_dt,
				'post_date_gmt'		=> $post_dt,
				'post_modified'		=> $post_modified_gmt,
				'post_modified_gmt'	=> $post_modified_gmt,
				'post_title'		=> $Title,
				'post_content'		=> $post_content,
				'post_excerpt'		=> $post_excerpt,
				'post_status'		=> $post_status,
				'post_name'			=> $post_titre_url,
				'comment_status'	=> $comment_status_map[$post_open_comment],
				'ping_status'		=> $comment_status_map[$post_open_tb],
				'post_type'			=> $post_type,
				'comment_count'		=> $post_nb_comment + $post_nb_trackback)
				);
			$fi_dc_tables['post']++;
		}
		$fi_dcposts2wpposts[$post_id] = $ret_id; # table de translation des post_id dc en ID WP
		
		# Make Post-to-Category associations ;  $fi_dc_cats # id du post dc ($cat_id) => nom interne de la catégorie
		# si la catégorie dc (par id dc), a un nom de catégorie wp, le post est assigné à cette cat ; sinon assigné à cat ID 1 (Non classé)
		# $post_cat_name est la valeur de $fi_dc_cats pour la même clé que cat_id ; à mettre en minuscules pour WP
		# category_nicename est en minuscules ; cat_libelle_url ou cat_url 1er char majuscule -> utiliser strtolower
		$post_cat_name = false;
		$cats = array();
		if ( array_key_exists($cat_id, $fi_dc_cats) ) { 
			$post_cat_name = $fi_dc_cats[$cat_id]; # à mettre en minuscules pour compar avec category_nicename de WP
			# $cat_id -= 0; 	// force numeric ??
			$cat1 = $wpdb->get_var('SELECT cat_ID FROM '.$wpdb->categories.' WHERE category_nicename="'.strtolower($post_cat_name).'"');
			if( $cat1  ) { $cats[1] = $cat1; }
			if( !empty($cats) ) { wp_set_post_cats('', $ret_id, $cats); }
		}
		if ( ($post_cat_name == false) OR ( count($cats) == 0 ) ) {
			if ($post_import_msg) {
				echo __('No category for post').' '.$ret_id.' - '.__('associated to first category ID 1').'<br />';
			}
			$cats = get_category_to_edit(1);
			wp_set_post_cats('', $ret_id, $cats);
		}
		
	}
	
	function comment($comment)
	{
	/*
	comment
	comment_id,post_id,comment_dt,comment_upddt,comment_auteur,comment_email,comment_site,comment_content,comment_ip,comment_pub,comment_trackback
	"1","9","2004-05-28 19:44:55","2004-05-28 19:44:55","kozlika","","kozlika.free.fr/blog.php","<p>Merci Nanard ...","81.67.152.148","1","0","0","1"
	
	comment
	comment_id,post_id,comment_dt,comment_tz,comment_upddt,comment_author,comment_email,comment_site,comment_content,comment_words,comment_ip,
	comment_status,comment_spam_status,comment_trackback

	wp_comments
	"comment_ID";"comment_post_ID";"comment_author";"comment_author_email";"comment_author_url";"comment_author_IP";"comment_date";
	"comment_date_gmt";"comment_content";"comment_karma";"comment_approved";"comment_agent";"comment_type";"comment_parent";"user_id"
	"8";"0";"kozlika";;"http://kozlika.free.fr/blog.php";"81.67.152.148";"2004-05-28 19:44:55";"2004-05-28 19:44:55";"<p>Merci Nanard  ...";"0";"1";;;"0";"0"
	*/
		global $wpdb, $fi_dcposts2wpposts, $fi_import_format, $fi_dc_tables, $fi_update_authorized;
		
		extract($comment);
		if ($fi_import_format == '2.0') {
			$comment_auteur = $comment_author;
			$comment_pub = $comment_status;
		}
		// WordPressify Data
		$comment_ID = ltrim($comment_id, '0');
		if ( array_key_exists($post_id, $fi_dcposts2wpposts) ) { # orphans test
			$comment_post_ID = $fi_dcposts2wpposts[$post_id];
		} else {
			$comment_post_ID = 1; # pas de billet associé, associe à billet ID 1
			echo __('Orphan comment').' '.__('associated to first post ID 1').'<br />';
		}
		$comment_approved = "$comment_pub";
		$name = $wpdb->escape($comment_auteur);
		$email = $wpdb->escape($comment_email);
		$web = "http://".$wpdb->escape($comment_site);
		$message = $wpdb->escape( preg_replace ('|(?<!<br />)\s*\n|', ' ', $comment_content) );
		
		if($cinfo = $wpdb->get_var("SELECT comment_ID FROM $wpdb->comments
					WHERE comment_author = '$name' AND comment_date = '$comment_dt'") )
		{
			// Update comments
			if ($fi_update_authorized) {
				$ret_id = wp_update_comment(array(
					'comment_ID'		=> $cinfo,
					'comment_post_ID'	=> $comment_post_ID,
					'comment_author'	=> $name,
					'comment_author_email'	=> $email,
					'comment_author_url'	=> $web,
					'comment_author_IP'	=> $comment_ip,
					'comment_date'		=> $comment_dt,
					'comment_date_gmt'	=> $comment_dt,
					'comment_content'	=> $message,
					'comment_approved'	=> $comment_approved)
					);
				$fi_dc_tables['comment']++;
			} else {
				$ret_id = $cinfo;
			}
		}
		else 
		{
			// Insert comments
			$ret_id = wp_insert_comment(array(
				'comment_post_ID'	=> $comment_post_ID,
				'comment_author'	=> $name,
				'comment_author_email'	=> $email,
				'comment_author_url'	=> $web,
				'comment_author_IP'	=> $comment_ip,
				'comment_date'		=> $comment_dt,
				'comment_date_gmt'	=> $comment_dt,
				'comment_content'	=> $message,
				'comment_approved'	=> $comment_approved)
				);
			$fi_dc_tables['comment']++;
		}
	} # comment
	
	function link($link)
	{
	/*
	link
	link_id,href,label,title,lang,rel,position
	
	link
	link_id,blog_id,link_href,link_title,link_desc,link_lang,link_xfn,link_position # ATTENTION, vérifier que link_position dans DC2 est à zéro !
	
	wp_link2cat
	"rel_id","link_id","category_id"
	
	wp_links
	"link_id","link_url","link_name","link_image","link_target","link_category","link_description","link_visible","link_owner","link_rating","link_updated","link_rel","link_notes","link_rss"
	
	"cat_ID","cat_name","category_nicename","category_description","category_parent","category_count","link_count","posts_private","links_private"

	la position détermine la catégorie de liens d'appartenance ; sauf si toutes les positions sont à zéro, c'est alors le link_id qui détermine la position
	au départ on associe le lien à la catégorie cat_id 1 Blogroll pour les liens dc sans catégorie
	 associate links to cat based on dc position or link_id if all dc positions at zero
	 $fi_linkcat_pos associe au cat_id ds wp_categories la position dc
	 $fi_linkcat_link_id associe au cat_id ds wp_categories le link_id dc
	 $fi_link_pos associe au link_id ds wp_links la position dc
	 $fi_link_link_id associe au link_id ds wp_links le link_id dc

	*/
		global $wpdb, $fi_linkcat_pos, $fi_linkcat_link_id, $fi_link_pos, $fi_link_link_id, $fi_import_format, $fi_dc_tables, $wp_minor_version, $fi_update_authorized;
		extract($link);
		if ($fi_import_format == '2.0') {
			$href = $link_href;
			$label = $link_title;
			$title = $link_desc;
			$lang = $link_lang;
			$rel = $link_xfn;
			$position = $link_position;
		}
		$title = addslashes($title);
		if ( ($href == "") AND ($title != "") ) {
			if ($wp_minor_version >= 2.1) {
				if ( !($cat_id = category_exists($title) ) ) {
					$cat_id = wp_create_category($title);
				}
			} else {
				$cinfo = $wpdb->get_var('SELECT cat_id FROM '.$wpdb->categories.' WHERE cat_name = "'.$wpdb->escape($title).'"');
				if ($cinfo) {
					$cat_id = $cinfo;
				} else {
					$wpdb->query ("INSERT INTO $wpdb->categories (cat_name) VALUES ('".$wpdb->escape ($title)."')");
					$cat_id = $wpdb->insert_id;
				}
			}
			$fi_linkcat_pos[$cat_id] = $position;
			$fi_linkcat_link_id[$cat_id] = $link_id;
		} else {
			$linkname = $wpdb->escape($label);
			$description = $wpdb->escape($title);
			$linfo = $wpdb->get_var('SELECT link_id FROM '.$wpdb->links.' WHERE link_name = "'.$linkname.'"');
			if($linfo) {
				if ($fi_update_authorized) {
					$ret_id = wp_update_link( array(
						'link_id'		=> $linfo,
						'link_url'		=> $href,
						'link_name'		=> $linkname,
						'link_description'	=> $description,
						'link_category'	=> 1
						)
						);
					$fi_dc_tables['link']++;
				} else {
					$ret_id = $linfo;
				}
			} else {
				$ret_id = wp_insert_link( array(
					'link_url'		=> $href,
					'link_name'		=> $linkname,
					'link_description'	=> $description,
					'link_category'	=> 1
					)
					);
				$fi_dc_tables['link']++;
			}
			$fi_link_pos[$ret_id] = $position;
			$fi_link_link_id[$ret_id] =  $link_id;
		}
	}

	function post_meta($tags)
	{
		Flat_Import::meta($tags, 'post_meta');
	}
	function meta($tags, $fi_table_name='meta') 
	{
	/*
	dc 1.2.5
	post_meta
	meta_id,post_id,meta_key,meta_value
	"3","46","tag","Dotclear"
	
	dc 2beta3.1
	meta
	meta_id,meta_type,post_id
	"dotclear","tag","15"
	"wordpress","tag","15"
	
	UTW3
	 wp_post2tag
	"rel_id";"tag_id";"post_id";"ip_address"
	"1";"1";"18";
	
	 wp_tags
	 "tag_ID";"tag"
	"1";"dotclear"
	
	 wp_tag_synonyms
	"tagsynonymid";"tag_id";"synonym"
	
	Simple Tagging
	wp_stp_tags
	"post_id","tag_name"
	"6","Dotclear"
	*/
		global $wpdb, $utw, $STagging, $STaggingAdmin, $fi_import_format, $fi_dc_tables, $fi_dcposts2wpposts, $wp_minor_version;
		extract($tags);
		$tag = '';
		if ($fi_import_format == '1.2') {
			if ($meta_key == 'tag') $tag = $meta_value;
		} elseif ($fi_import_format == '2.0') {
			if ($meta_type == 'tag') $tag = $meta_id;
		}
		if ($tag != '') {
			if ( array_key_exists($post_id, $fi_dcposts2wpposts) ) {
				$post_id = $fi_dcposts2wpposts[$post_id]; # get WP post_id
				if (function_exists('wp_add_post_tags')) { # tags en natif à partir de WP 2.3
					wp_add_post_tags($post_id, $tag);
				} elseif (is_object($utw)) {
					$utw->AddTag($post_id, $tag);
				} elseif (is_object($STaggingAdmin)) {
					$STaggingAdmin->saveTag($post_id, $tag);
				}
				$fi_dc_tables[$fi_table_name]++;
			}
		}
	}
	
	function filesize_format($bytes) {
		$bytes=(float)$bytes;
		if ($bytes<1024){
		$numero=number_format($bytes, 0, ',', '.')." Byte";
		return $numero;
		}
		if ($bytes<1048576){
		 $numero=number_format($bytes/1024, 2, ',', '.')." KByte";
		return $numero;
		}
		if ($bytes>=1048576){
		 $numero=number_format($bytes/1048576, 2, ',', '.')." MByte";
		return $numero;
		}
	}

	function say($str)
	{ # add html tags and force output
		echo $str.'<br />';
		ob_flush();flush();
	}
	
	function tips()
	{
		echo '<p>'.__('Welcome to WordPress.  We hope (and expect!) that you will find this platform incredibly rewarding!  As a new WordPress user coming from Dotclear, there are some things that we would like to point out.  Hopefully, they will help your transition go as smoothly as possible.').'</p>';
		echo '<h3>'.__('Users').'</h3>';
		echo '<p>'.sprintf(__('You have already setup WordPress and have been assigned an administrative login and password.  Forget it.  You didn\'t have that login in Dotclear, why should you have it here?  Instead we have taken care to import all of your users into our system.  Unfortunately there is one downside.  Because both WordPress and Dotclear uses a strong encryption hash with passwords, it is impossible to decrypt it and we are forced to assign temporary passwords to all your users.  <strong>Every user has the same username, but their passwords are reset to password123.</strong>  So <a href="%1$s">Login</a> and change it.'), '/wp-login.php').'</p>';
		echo '<h3>'.__('Preserving Authors').'</h3>';
		echo '<p>'.__('Secondly, we have attempted to preserve post authors.  If you are the only author or contributor to your blog, then you are safe.  In most cases, we are successful in this preservation endeavor.  However, if we cannot ascertain the name of the writer due to discrepancies between database tables, we assign it to you, the administrative user.').'</p>';
		echo '<h3>'.__('Textile').'</h3>';
		echo '<p>'.__('Also, since you\'re coming from Dotclear, you probably have been using Textile to format your comments and posts.  If this is the case, we recommend downloading and installing <a href="http://www.huddledmasses.org/category/development/wordpress/textile/">Textile for WordPress</a>.  Trust me... You\'ll want it.').'</p>';
		echo '<h3>'.__('WordPress Resources').'</h3>';
		echo '<p>'.__('Finally, there are numerous WordPress resources around the internet.  Some of them are:').'</p>';
		echo '<ul>';
		echo '<li>'.__('<a href="http://www.wordpress.org">The official WordPress site</a>').'</li>';
		echo '<li>'.__('<a href="http://wordpress.org/support/">The WordPress support forums</a>').'</li>';
		echo '<li>'.__('<a href="http://codex.wordpress.org">The Codex (In other words, the WordPress Bible)</a>').'</li>';
		echo '</ul>';
		echo '<p>'.sprintf(__('That\'s it! What are you waiting for? Go <a href="%1$s">login</a>!'), '../wp-login.php').'</p>';
	}

	function header() 
	{
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Dotclear').'</h2>';
	}

	function footer() 
	{
		echo '</div>';
	}
	
	function greet() 
	{
		$filename = dirname(__FILE__).'/../../wp-content/uploads/blog-backup.txt';
		$filename_exists = ( file_exists($filename) ) ? true : false;
		$recover_filename = dirname(__FILE__).'/../../wp-content/uploads/blog-backup-recovery.txt';
		$recover_exists = ( file_exists($recover_filename) ) ? true : false;
		if ($recover_exists) {
			if ( filesize($recover_filename) == 0) $recover_exists = false;
		}
		echo '<p>'.__('This importer allows you to import a Dotclear blog into a WordPress blog.').'</p>';
		echo '<p>'.__('Import of posts, tags, comments, categories ; users ; links, categories from DotClear 1.2.x or Dotclear 2.').'</p>';
		echo '<p>'.__('It requires the <em>wp-contents/uploads/</em><strong>blog-backup.txt</strong> file.').'</p>';
		echo '<p>'.__('The first post <strong>ID 1</strong> ; <em>Hello World</em> initial name (for orphans comments) and the first category <strong>cat_ID 1</strong> <em>Unclassified</em> initial name (for posts without category) are required.').'</p>';
		echo '<p>'.__('The maximum execution time in seconds is:').' <strong>'.ini_get('max_execution_time').'</strong></p>';
		echo '<p>'.__('If the importer do not display the message:').' <strong>'.__('End of import').'</strong>, '.
		__('then <strong>retry</strong> the import. Click <em>Import</em>, click <em>Dotclear flat import</em> and so on.').'</p>';
		
		echo '<p><strong>Saving</strong> your existing WordPress data is <strong>mandatory</strong> before doing an import. The version 2 of <a href="http://www.ilfilosofo.com/blog/wp-db-backup">WordPress Database Backup</a> is recommended for WordPress 2.1.</p>';
		if ($filename_exists AND $recover_exists) {
			echo '<form action="admin.php?import=flatimport&amp;step=3" method="post">';
			echo '<input type="submit" name="submit" value="'.__('Continue').'" />';
			echo ' '.__('the import from the latest point of control (if not wanted, please delete the wp-contents/uploads/<strong>blog-backup-recovery.txt</strong> file ; then retry the import').'.';
			echo '</form>';
		} elseif ($filename_exists) {
			echo '<form action="admin.php?import=flatimport&amp;step=2" method="post">';
			echo '<input type="submit" name="submit" value="'.__('Import').'" />';
			echo ' '.__('Import only new data. Do not modify WordPress existing already imported posts, comments, categories, links, users').'.';
			echo '</form>';
			echo '<form action="admin.php?import=flatimport&amp;step=1" method="post">';
			echo '<input type="submit" name="submit" value="'.__('Update').'" />';
			echo ' '.__('Update the WordPress blog with the latest blog-backup.txt data (replace existing already imported WordPress posts, comments, categories, links, users by the DotClear ones)').'.';
			echo '</form>';
		} else {
			echo '<p><strong>Warning:</strong> No wp-contents/uploads/<strong>blog-backup.txt</strong> file found.</p>';
		}
	}
	
	function dispatch() {
		global $fi_update_authorized;

		if ( empty($_GET['step']) ) {
			$step = 0;
		} else {
			$step = (int) $_GET['step'];
		}
		
		$this->header();

		switch ($step) 
		{
			default:
			case 0 :
				$this->greet();
				break;
			case 1 :
				/* specify what to do when the same post, comment ... is imported: if true, the existing WordPress post, comment ... is replaced by the Dotclear one */
				$fi_update_authorized = true;
				$this->import_flat_file();
				break;
			case 2 :
				/* specify what to do when the same post, comment ... is imported: if false:  the existing WordPress post, comment ... is NOT replaced by the Dotclear one */
				$fi_update_authorized = false;
				$this->import_flat_file();
				break;
			case 3 :
				$fi_update_authorized = false; # default for a non valid blog-backup-recovery.txt
				$this->import_flat_file();
				break;
			case 4 :
				$this->tips();
				break;
		}
		$this->footer();
	}
	
	function Flat_Import() 
	{
		// Nothing.	
	}
}

/** define import object and execute **/
$fi_dc_import = new Flat_Import();
register_importer('flatimport', __('Dotclear flat import'), __('Import from a Dotclear Blog'), array ($fi_dc_import, 'dispatch'));
?>