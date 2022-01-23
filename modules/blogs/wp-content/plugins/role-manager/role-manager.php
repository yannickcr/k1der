<?php
/*
Plugin Name: Role Manager
Plugin URI: http://www.im-web-gefunden.de/wordpress-plugins/role-manager/
Description: Role Management for WordPress 2.0, 2.1 and 2.2.
Version: 2.0.8
Author: Thomas Schneider
Author URI: http://www.im-web-gefunden.de/
Update Server:  http://www.im-web-gefunden.de/
Min WP Version: 2.0
Max WP Version: 2.1
License: MIT License - http://www.opensource.org/licenses/mit-license.php

Original coding by David House and Owen Winkler
Icons were provided by http://www.famfamfam.com/lab/icons/silk/ under
a Creative Commons Attribution 2.5 license.
 
*/

load_plugin_textdomain('role-manager',$path = 'wp-content/plugins/role-manager/languages');

class RoleManager
{
  var $path_file;
  var $image_dir;
  var $style_dir;

	function RoleManager()
	{
    global $wp_db_version;

    $wp_db_version < 4772 ? $this->path_file='profile.php' : $this->path_file='users.php';
    $this->image_dir = $this->dirname() . '/images/';
    $this->style_dir = $this->dirname() . '/styles/';

    add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('edit_user_profile', array(&$this, 'manage_user_caps_page'));
		if (strstr($_SERVER['REQUEST_URI'], 'user-edit.php') !== false) {
			add_action('init', array(&$this, 'handle_user_caps_edit'));
			add_action('admin_head', array(&$this, 'admin_head'));
		}
		if (strstr($_SERVER['REQUEST_URI'], $this->path_file) !== false) {
			add_action('init', array(&$this, 'handle_role_caps_edit'));
			add_action('init', array(&$this, 'process_role_changes'));
			add_action('admin_head', array(&$this, 'admin_head'));
		}
	}

	function admin_menu() {
    if ( function_exists('wp_enqueue_script') ) {
      wp_enqueue_script('sack');
    } else {
      global $sack_js;
      $sack_js = true;
    }
    add_submenu_page($this->path_file, __('Role Management'), __('Roles'), 'edit_users', $this->basename(), array(&$this, 'manage_roles_page'));
  }

	function admin_head()
	{
   echo '
  <link rel="stylesheet" href="'.$this->style_dir.'style.css" type="text/css" />
		<script type="text/javascript">
		function badidea() {
			return confirm("' . addslashes(__("Removing this permission from a role assigned to you could be a really bad idea.  Are you sure?", 'role-manager')) . '");
		}
		function setdefaultrole(rolename) {
			var ajax = new sack();
			ajax.requestFile = "' . $this->manage_roles_uri() . '";
			ajax.setVar("action", "makedefault");
			ajax.setVar("role", rolename);			
			ajax.setVar("ajax", "1");
			ajax.execute = true;
			//ajax.element = "toast";  // Debug ajax returned script
			ajax.runAJAX();
			return true;		
		}
		function setlevel(level,rolename) {
			var ajax = new sack();
			ajax.requestFile = "' . $this->manage_roles_uri() . '";
			ajax.setVar("action", "setuserlevel");
			ajax.setVar("role", rolename);
			ajax.setVar("level", level);
			ajax.setVar("ajax", "1");
			ajax.execute = true;
			//ajax.element = "toast";  // Debug ajax returned script
			ajax.runAJAX();
			return true;			
		}
		function fadeuserlevel(rolename) {
			Fat.fade_element(rolename + "___user_level");
		}
		function showdefaultrole(rolename) {
			var imgs = document.getElementsByTagName("IMG");
			for(z=0;z<imgs.length;z++) {
				if(imgs[z].id == "defrole_" + rolename) {
					imgs[z].src = "' . $this->image_dir . 'star.png";
					imgs[z].className = "defrole";
				}
				else if(imgs[z].className == "defrole") {
					imgs[z].src = "' . $this->image_dir . 'star_disabled.png";
					imgs[z].className = "nondefrole";
				}
			}
		}
		function submitme(frm) {
			var ajax = new sack();
			ajax.requestFile = "' . $this->manage_roles_uri() . '";
			inputs = frm.getElementsByTagName("INPUT");
			for(z=0;z<inputs.length;z++) {
				ajax.setVar(inputs[z].name, inputs[z].value);
			}
			ajax.setVar("ajax", "1");
			ajax.execute = true;
			//ajax.element = "toast";  // Debug ajax returned script
			ajax.runAJAX();
			return false;
		}
		function toggleCap(capbtnname) {
			var btn = document.getElementById(capbtnname);
			btn.value = (btn.value == "0") ? "1" : "0";
			btn.src = "' . $this->image_dir . '" + ((btn.value == "0") ? "accept.png" : "cancel.png");
			Fat.fade_element(btn.parentNode.id);
		}
		function setMessage(message) {
			var msg = document.getElementById("message");
			try {
				msg.innerHTML = "<p>" + message + "</p>";
			}
			catch(e) {
				msg = document.createElement("DIV");
				msg.className = "updated fade";
				msg.setAttribute("id", "message");
				main = document.getElementById("main_page");
				main.parentNode.insertBefore(msg, main);
				msg.innerHTML = "<p>" + message + "</p>";
			}
			Fat.fade_all();
		}
		</script>';
	}
	
	function handle_role_changes() {
		global $wp_roles;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
			
		// Handle changes
		if ($_POST['grant'] == 1) {
			$wp_roles->add_cap($_POST['role'], $_POST['cap'], true);
		} 
		else {
			$wp_roles->remove_cap($_POST['role'], $_POST['cap']);
		}
		$wp_roles->WP_Roles();
		$role = $wp_roles->get_role($_POST['role']);
		
		update_option('caplist', $this->get_cap_list());
				
		//Redirect them away
		if (isset($_POST['ajax'])) {
			$changed = addslashes($_POST['grant'] ? __('Capability granted', 'role-manager') : __('Capability denied', 'role-manager'));
			die('
				toggleCap("btn_' . $_POST['role'] . '__' . $_POST['cap'] . '");
				setMessage("' . $changed . '");
			');
		}
		else {
			$changed = ($_POST['grant'] ? 'granted' : 'denied') . '=' . $_POST['role'];
			header('Location: ' . $this->manage_roles_uri() . '&' . $changed);
		}
	}
	
	function handle_new_role_creation() {
		global $wp_roles;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
		
		if (empty($_POST['role-name'])) {
			$this->error(__('You must enter a role name.'));
		}
		$caps = $_POST['caps'];
		//turn all the 'on's to 1s
		if($caps) {
			foreach ($caps as $k => $cap) {
				$caps[$k] = 1;
			}
		}
		else {
			$caps = array();
		}
		
		$role = $wp_roles->add_role(sanitize_title($_POST['role-name']), stripslashes($_POST['role-name']), $caps);
		header('Location: ' . $this->manage_roles_uri() . '&new-role=true');
	}
	
	function handle_new_cap_creation() {
		if (empty($_POST['cap-name'])) {
			$this->error('You must enter a capability name.');
		}
		$cap = strtolower($_POST['cap-name']);
		$cap = preg_replace('#[^a-z]#', '_', $cap);
		
		$caps = $this->get_cap_list();
		if(!in_array($cap, $caps)) $caps[] = $cap;
		update_option('caplist', $caps);
		header('Location: ' . $this->manage_roles_uri() . '&new-cap=true');
	}
	
	function handle_cap_purge() {
		$caps = array();
		update_option('caplist', $caps);
		$caps = $this->get_cap_list();
		update_option('caplist', $caps);		
		header('Location: ' . $this->manage_roles_uri() . '&purge-caps=true');
	}
	
	function rename_role($role) {
		global $wp_roles, $wpdb;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
    
		if ($_POST['role-name']) {
			$roletitle = sanitize_title($_POST['role-name']);
      
      if ( $roletitle != $role ) {
        if ( $wp_roles->is_role($roletitle) ) {
          $this->error(__('Can\'t rename. A Role with the name ' . $roletitle . ' already exist.', 'role-manager'));
        }
        $oldrole = $wp_roles->get_role($role);
			  $wp_roles->add_role($roletitle, stripslashes($_POST['role-name']), $oldrole->capabilities);
        //$this->debug('before', $wp_roles);
			
			  if($userids = $wpdb->get_col("SELECT ID FROM {$wpdb->users}")) {

				  foreach($userids as $userid) {
					  $user = new WP_User($userid);
            $user->roles = $this->fill_array_keys_with_true( $user->roles );
            if ( in_array($role, array_keys($user->roles))) {
              $user->add_role($roletitle);
              // Bug in capabilities.php, WP_User::get_role_caps(), Start at Line 176
              // this->roles has [0]=admin and should be [admin]=true
              // wrong form comes from array_keys($this->caps) at line 176
              $user->roles = $this->fill_array_keys_with_true( $user->roles );
              $user->remove_role($role);
              //$this->debug('after', $user);
					  }
				  }
			  }
			  $wp_roles->remove_role($role);
      }   
			header('Location: ' . $this->manage_roles_uri() . '&role-renamed=true');
		}
	}
	
	function rename_role_form($role) { 
		global $wp_roles;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
		
		?>
		<div class="wrap">
			<h2><?php _e('Rename Role', 'role-manager'); ?> '<?php echo $wp_roles->role_names[$role]; ?>'</h2>
			<form method="post" class="role-manager" action="<?php echo $this->manage_roles_uri(); ?>">
				<input type="hidden" name="action" value="rename" />
				<input type="hidden" name="role" value="<?php echo $role; ?>" />
				<label for="role-name"><?php _e('New Name:', 'role-manager'); ?>
					<input type="text" name="role-name" id="role-name" 
					value="<?php echo $wp_roles->role_names[$role]; ?>" />
				</label>
				<input type="submit" value="<?php _e('Rename Role', 'role-manager'); ?>" />
			</form>
		</div>
	<?php 
	}
	
	function get_all_user_ids() {
		if ($ids = wp_cache_get('all_user_ids', 'users')) {
			return $ids;
		} else {
			global $wpdb;
			$ids = $wpdb->get_col('SELECT ID from ' . $wpdb->users);
			wp_cache_set('all_user_ids', $ids, 'users');
			return $ids;
		}
	}
	
	function delete_role($role) 
	{
		global $wp_roles;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
		
		if ($_POST['confirm']) {
			$defaultrole = get_settings('default_role');
			if ($role == $defaultrole) {
				//LAZY CODE ALERT! we should give the option of changing the default role
				$this->error(__('You cannot delete the default role.', 'role-manager'));
			}
			
			//remove the role from all the users
			foreach ($this->get_all_user_ids() as $id) { //we need a global get_all_user_ids() func
				$user = new WP_User($id);
        $user->roles = $this->fill_array_keys_with_true( $user->roles );
        if ( in_array($role, array_keys($user->roles))) {
				  //if this role removal would end them up with no roles, assign the default role instead of removing
				  if (count($user->roles) <= 1) {
					  $user->add_role($defaultrole);
            $user->roles = $this->fill_array_keys_with_true( $user->roles );
          }
					$user->remove_role($role);
        }    
			}
      $wp_roles->remove_role($role);
			header('Location: ' . $this->manage_roles_uri() . '&role-deleted=true');
		}
	}

	function make_default($role) {
		global $wp_roles;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
		
		if ($wp_roles->is_role($role)) {
			update_option('default_role', $role);
		} else {
			$this->error(__('Can\'t make ' . $role . ' the default. Not a role.', 'role-manager'));
		}
		if (isset($_POST['ajax'])) {
			$changed = addslashes(__('Default role changed.', 'role-manager'));
			die('
				showdefaultrole("' . $role .'");
				setMessage("' . $changed . '");
			');		
		}
		header('Location: ' . $this->manage_roles_uri() . '&made-default=true');
	}
	
	function set_user_level($role)
	{
		global $wp_roles;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
		
		$level = isset($_POST['level']) ? $_POST['level'] : $_GET['level'];
		
		for($z=0;$z<=10;$z++) {
			if ($z > $level) {
				$wp_roles->remove_cap($role, "level_{$z}");
			}
			else {
				$wp_roles->add_cap($role, "level_{$z}");
			}
		}
		if (isset($_POST['ajax'])) {
			$changed = addslashes(__('Set user level of role.', 'role-manager'));
			die('
				fadeuserlevel("' . $role .'");
				setMessage("' . $changed . '");
			');		
		}
		header('Location: ' . $this->manage_roles_uri() . '&set-userlevel=true');
	}
	
	function delete_role_form($role) 
	{ 
		global $wp_roles; 
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
		
		?>
		<div class="wrap">
			<h2><?php _e('Delete Role', 'role-manager'); ?> '<?php echo $wp_roles->role_names[$role]; ?>'</h2>
			<p><?php echo __('All users with this role will have it removed, and they will lose all capabilities from this role (unless other roles provide it). If a user has only this role, they will be assigned the default role, ', 'role-manager') . get_settings('default_role'); ?></p>
			<form method="post" class="role-manager" action="<?php echo $this->manage_roles_uri(); ?>">
				<input type="hidden" name="action" value="delete" />
				<input type="hidden" name="role" value="<?php echo $role; ?>" />
				<input type="submit" name="confirm" value="<?php _e('Confirm Delete Role', 'role-manager'); ?>" />
			</form>
		</div>
	<?php 
	}
		
	function manage_roles_page()
	{
		global $wp_roles, $current_user;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
		
		if(!current_user_can('edit_users')) {
			echo "<p>Sneaky.</p>";  //If accessed properly, this message doesn't appear.
			return;
		}
		
		$action = $_POST['action'] ? $_POST['action'] : $_GET['action'];
		$role = $_POST['role'] ? $_POST['role'] : $_GET['role'];
	
		switch ($action) {
			case 'rename': $this->rename_role_form($role); break;
			case 'delete': $this->delete_role_form($role); break;
			default: 
		
		// Display a message if we've made changes
		if ($_GET['granted']) {
			echo '<div class="updated fade" id="message"><p>' . __('Capability granted', 'role-manager') . '</p></div>';
		} elseif ($_GET['denied']) {
			echo '<div class="updated fade" id="message"><p>' . __('Capability denied', 'role-manager') . '</p></div>';
		} elseif ($_GET['new-role']) {
			echo '<div class="updated fade" id="message"><p>' . __('New role created', 'role-manager') . '</p></div>';
		} elseif ($_GET['new-cap']) {
			echo '<div class="updated fade" id="message"><p>' . __('New capability created', 'role-manager') . '</p></div>';
		} elseif ($_GET['purge-caps']) {
			echo '<div class="updated fade" id="message"><p>' . __('Unused capabilites purged', 'role-manager') . '</p></div>';
		} elseif ($_GET['role-renamed']) {
			echo '<div class="updated fade" id="message"><p>' . __('Role renamed', 'role-manager') . '</p></div>';
		} elseif ($_GET['role-deleted']) {
			echo '<div class="updated fade" id="message"><p>' . __('Role deleted', 'role-manager') . '</p></div>';
		} elseif ($_GET['made-default']) {
			echo '<div class="updated fade" id="message"><p>' . __('Default role changed', 'role-manager') . '</p></div>';
		} elseif ($_GET['set-userlevel']) {
			echo '<div class="updated fade" id="message"><p>' . __('Set user level of role', 'role-manager') . '</p></div>';
		}
		
		// Output an admin page
		echo '<div class="wrap" id="main_page"><h2>' . __('Manage Roles', 'role-manager') . '</h2>';
		echo '<p>' . __('This page is for editing what capabilities are associated with each role. To change the capabilities of a specific user, click on Authors &amp; Users, then click Edit next to the user you want to change. You can <a href="#new-role">add new roles</a> as well.', 'role-manager') . '</p>';

		$capnames = $this->get_cap_list();
		
		$defaultrole = get_settings('default_role');
		foreach($wp_roles->role_names as $roledex => $rolename) {
			if ($roledex == $defaultrole) {
				$lovelylittlestar = '<img src="' . $this->image_dir . 'star.png" alt="Default Role" class="defrole" id="defrole_' . $roledex . '" onclick="return !setdefaultrole(\'' . $roledex . '\');" />';
			} else {
				$lovelylittlestar = '<img src="' . $this->image_dir . 'star_disabled.png" class="nondefrole" id="defrole_' . $roledex . '" alt="Click to make this the default role" onclick="return !setdefaultrole(\'' . $roledex . '\');" />';
			}
			
			$lovelylittlestar = '<a href="' . $this->manage_roles_uri() . '&amp;action=makedefault&amp;role=' . $roledex . '" class="roledefaulter">'
				. $lovelylittlestar . '</a>';
			
			echo "<h3 class=\"roles_name\">$lovelylittlestar {$rolename} 
				(<a href='" . $this->manage_roles_uri() . "&amp;action=rename&amp;role={$roledex}'>" . __('rename', 'role-manager') . "</a>";
			$role = $wp_roles->get_role($roledex);
						
			if(!($role->has_cap('edit_users') && in_array($roledex, $current_user->roles))) {
				echo ",<a href='" . $this->manage_roles_uri() . "&amp;action=delete&amp;role={$roledex}'>" . __('delete', 'role-manager') . "</a>";
			}
			echo ")</h3>";

			foreach($capnames as $cap) {
				$capname = $this->get_cap_name($cap);
				echo '<form onsubmit="submitme(this);return false;" method="post" class="cap_form" id="' . $roledex . '__' . $cap . '" action="' . $this->manage_roles_uri() . '">';
				echo '<input type="hidden" name="role" value="' . $roledex . '" />';
				echo '<input type="hidden" name="cap" value="' . $cap . '" />';
				echo '<input type="hidden" name="grant" value="' . ($role->has_cap($cap)?'0':'1') . '" />';
				if ($role->has_cap($cap)) {
					if($cap == 'edit_users' && in_array($roledex, $current_user->roles)) {
						echo '<input type="image" id="btn_' . $roledex . '__' . $cap . '" src="' . $this->image_dir . 'accept.png"  name="grant" value="0" alt="' . __('Granted', 'role-manager') . '" onclick="return badidea();"/>';
					}
					else {
						echo '<input type="image" id="btn_' . $roledex . '__' . $cap . '" src="' . $this->image_dir . 'accept.png"  name="grant" value="0" alt="' . __('Granted', 'role-manager') . '" />';
					}
				}
				else {
					echo '<input type="image" id="btn_' . $roledex . '__' . $cap . '" src="' . $this->image_dir . 'cancel.png"  name="grant" value="1" alt="' . __('Denied', 'role-manager') . '" />';
				}
				echo ' ' . $capname . '</form>';
			}
			$role_user_level = array_reduce(array_keys($role->capabilities), array('WP_User', 'level_reduction'), 0);
			echo '<form method="post" class="userlevel_form" id="' . $roledex . '___user_level" action="' . $this->manage_roles_uri() . '" onsubmit="return !setlevel(document.getElementById(\'' . $roledex . '___ulvalue\').value,\'' . $roledex . '\');">';
			echo '<input type="hidden" name="role" value="' . $roledex . '" />';
			echo '<input type="hidden" name="action" value="setuserlevel" /><input type="image" src="' . $this->image_dir . 'refresh.png" alt="' . __('Update', 'role-manager') . '"/><label style="font-style:italic;">';
			$sel = '<select name="level" id="' . $roledex . '___ulvalue" onchange="setlevel(this.value,\'' . $roledex . '\');">';
			for($level=0;$level<=10;$level++) {
				$sel .= '<option value="' . $level . '"';
				if($role_user_level == $level) {
					$sel .= ' selected="selected"';
				}
				$sel .= '>' . $level . '</option>';
			}
			$sel .= '</select>';
			echo sprintf(__(' User Level: %s', 'role-manager'), $sel);
			echo '</label></form>';
		}

		//Echo the new role form
		?>
		<h2 id="new-role"><?php _e('Create a new Role', 'role-manager'); ?></h2>
		<form method="post" class="role-manager">
			<label for="role-name"><?php _e('Role Name:', 'role-manager'); ?> <input type="text" name="role-name" id="role-name" /></label>
			<label><?php _e('Capabilities to be included:', 'role-manager'); ?></label>
			<fieldset>
				<?php foreach ($this->get_cap_list(true) as $cap) { ?>
					<label for="cap-<?php echo $cap; ?>" class="cap-label">
					<input type="checkbox" name="caps[<?php echo $cap; ?>]" id="cap-<?php echo $cap; ?>" />
					<?php echo $this->get_cap_name($cap); ?></label>
				<?php } ?>
			</fieldest>
			<input type="submit" name="new-role" value="<?php _e('Create Role', 'role-manager'); ?>" />
		</form>
		
		<h2 id="new-cap"><?php _e('Custom Capabilities', 'role-manager'); ?></h2>
		<form method="post" class="role-manager">
			<label for="cap-name"><?php _e('New Capability Name:', 'role-manager'); ?> <input type="text" name="cap-name" id="cap-name" /></label>
			<input type="submit" name="new-cap" value="<?php _e('Create Capability', 'role-manager'); ?>" />
		</form>
		<form method="post" class="role-manager">
			<input type="submit" name="purge-caps" value="<?php _e('Purge Unused Capabilities', 'role-manager'); ?>" onclick="return confirm('<?php _e('If core capabilities are not currently assigned to any Role, then you must manually re-add them after this action if you want to use them.  Are you sure you want to do this?', 'role-manager'); ?>');"/>
		</form>		<?php
		
		} //switch ($action)
	}

	//the manage caps widget on the edit user page
	function manage_user_caps_page() {
		global $profileuser, $wp_roles;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
		
    //echo '<h3>' . __('Assign extra capabilites', 'role-manager') . '</h3>';
    echo '<fieldset style="width:90%;"><legend>' . __('Assign extra capabilites', 'role-manager') . '</legend>';
		foreach($this->get_cap_list() as $cap) {
			$capname = $this->get_cap_name($cap);
			$checked = $profileuser->has_cap($cap) ? 'checked="checked"' : '';
			if(isset($profileuser->allcaps[$cap]) && !isset($profileuser->caps[$cap]))
				$inherited = ' style="font-weight:bold;"';
			elseif(isset($profileuser->allcaps[$cap]) && ($profileuser->allcaps[$cap] == false)) {
				$inherited = '';
				foreach($profileuser->roles as $role) {
					if($wp_roles->role_objects[$role]->has_cap($cap)) {
						$inherited = ' style="font-weight:bold;font-style:italic;color:red;"';
						break;
					}
				}
			}
			else
				$inherited = '';
			echo '<label for="cap-' . $cap . '" class="cap-label" ' . $inherited . '>' . 
				'<input type="checkbox" style="width: auto;margin-right: 5px;" name="caps[' . $cap . ']" id="cap-' . $cap . '" ' . $checked . '/>'
				. $capname . '</label>';
		}
		echo '</fieldset><br style="clear:both;" />';
	}
	
	function handle_role_caps_edit() {
		if (isset ($_POST['grant'])) {
			$this->handle_role_changes();
		}
		if (isset($_POST['new-role'])) {
			$this->handle_new_role_creation();
		}
		if (isset($_POST['new-cap'])) {
			$this->handle_new_cap_creation();
		}
		if (isset($_POST['purge-caps'])) {
			$this->handle_cap_purge();
		}
	}
		
  function process_role_changes() {
		$action = $_POST['action'] ? $_POST['action'] : $_GET['action'];
		$role = $_POST['role'] ? $_POST['role'] : $_GET['role'];
	
		switch ($action) {
			case 'rename': $this->rename_role($role); break;
			case 'delete': $this->delete_role($role); break;
			case 'makedefault': $this->make_default($role); break;
			case 'setuserlevel': $this->set_user_level($role); break;
		}
	}

	
	//handle changes for specific user caps
	function handle_user_caps_edit() {
		global $user_ID;
		get_currentuserinfo();

		$user = new WP_User($_POST['user_id']);
		//don't let users remove edit_users from themselves
		if ($user->id == $user_ID && (empty($_POST['caps']['edit_users']) ||
			 ($_POST['caps']['edit_users'] != 'on')) ) {
			 $this->error(__('You cannot remove the Edit Users capability from yourself.'));
		}
		foreach ($this->get_cap_list(false) as $cap) {
			//this looks stupid but it's really quite simple :)
			//if the user has just been denied an 'extra' cap (i.e., one that isn't a role), use remove_cap
			//(note that $user->caps is the array where role-caps aren't listed)
			if ($user->has_cap($cap) && array_key_exists($cap, $user->caps) && (!isset($_POST['caps'][$cap]) || $_POST['caps'][$cap] == 'off')) {
				//die( 'removing ' . $cap);
				$user->remove_cap($cap);
			//if the user has been denied a cap that was inherited from a role, explicity deny it
			} elseif ($user->has_cap($cap) && (!isset($_POST['caps'][$cap]) || $_POST['caps'][$cap] == 'off')) {
				$user->add_cap($cap, false);
				//die( 'denying ' . $cap);
			//if the user has been given a cap, that was previously denied (i.e., its a role cap), undeny it
			} elseif (!$user->has_cap($cap) && array_key_exists($cap, $user->allcaps) && !$user->allcaps[$cap] && $_POST['caps'][$cap] == 'on') {
				//die( 'undenying ' . $cap);
				unset($user->caps[$cap]); //remove the ban
				update_usermeta($user->id, $user->cap_key, $user->caps);
			//if we're just adding a new, extra cap, just good ol' fashioned add_cap :)
			} elseif (!$user->has_cap($cap) && $_POST['caps'][$cap] == 'on') {
				//die( 'adding ' . $cap);
				$user->add_cap($cap);
			}
		}
	}
		
	/* Utility Functions */
	function get_cap_list($roles = true, $kill_levels = true) {
		global $wp_roles;
		
		if ( ! isset($wp_roles) )
			$wp_roles = new WP_Roles();
		
		// Get Role List
		foreach($wp_roles->role_objects as $key => $role) {
			foreach($role->capabilities as $cap => $grant) {
				$capnames[$cap] = $cap;
        //$this->debug('grant', ($role->capabilities));
			}
		}	
		
		if ($caplist = get_settings('caplist')) {
			$capnames = array_unique(array_merge($caplist, $capnames));
		}
		
		$capnames = apply_filters('capabilities_list', $capnames);
		if(!is_array($capnames)) $capnames = array();
		$capnames = array_unique($capnames);
		sort($capnames);

		//Filter out the level_x caps, they're obsolete
		if($kill_levels) {
			$capnames = array_diff($capnames, array('level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5',
				'level_6', 'level_7', 'level_8', 'level_9', 'level_10'));
		}
		
		//Filter out roles if required
		if (!$roles) {
			foreach ($wp_roles->get_names() as $role) {
				$key = array_search($role, $capnames);
				if ($key !== false && $key !== null) { //array_search() returns null if not found in 4.1
					unset($capnames[$key]);
				}
			}
		}
		
		return $capnames;
	}
	
	//this is crap
	function get_cap_name($cap) {
		return ucwords(str_replace('_', ' ', $cap));
	}
	
	function debug($foo)
	{
		$args = func_get_args();
		echo "<pre style=\"background-color:#ffeeee;border:1px solid red;\">";
		foreach($args as $arg1)
		{
			echo htmlentities(print_r($arg1, 1)) . "<br/>";
		}
		echo "</pre>";
	}
	
	function error($error) {
		//TODO: better errors
		die($error);
	}
	
	function basename() 
	{
		$name = preg_replace('/^.*wp-content[\\\\\/]plugins[\\\\\/]/', '', __FILE__);
		return str_replace('\\', '/', $name);
	}
	
	function dirname()
	{
		return dirname($this->plugin_uri()); 
	}
	
	function plugin_uri()
	{
		return get_settings('siteurl') . '/wp-content/plugins/' . $this->basename(); 
	}
	
	function manage_roles_uri() {
		//return get_settings('siteurl') . '/wp-admin/profile.php?page=' . $this->basename();
		return get_settings('siteurl') . '/wp-admin/'.$this->path_file.'?page=' . $this->basename();
	}
		
	function reset_vars($wpvarstoreset) {
		for ($i=0; $i<count($wpvarstoreset); $i += 1) {
			$wpvar = $wpvarstoreset[$i];
			if (!isset($this->$wpvar)) {
				if (empty($_POST["$wpvar"])) {
					if (empty($_GET["$wpvar"])) {
						$this->$wpvar = '';
					} else {
						$this->$wpvar = $_GET["$wpvar"];
					}
				} else {
					$this->$wpvar = $_POST["$wpvar"];
				}
			}
		}
	}

  function fill_array_keys_with_true( $arr ) {
    $arr = array_flip($arr);
    foreach ($arr as $key=>$val) { $arr[$key] = true ; };
    return ($arr);
  }
  
}

$rolemanager = new RoleManager();

?>