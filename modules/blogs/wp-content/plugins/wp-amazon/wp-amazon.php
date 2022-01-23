<?php
/*
Plugin Name: WP-Amazon
Version: 2.1
Plugin URI: http://manalang.com/wp-amazon/
Description: WP-Amazon adds the ability to search and include items from Amazon to your entries.
Author: Rich Manalang
Author URI: http://groups.google.com/group/wp-amazon

WP-Amazon Plugin for Wordpress 2.3+
Copyright (C) 2005-2007 Rich Manalang
Version 2.1  $Rev: 7794 $ $Date: 2007-02-03 20:58:29 -0800 (Sat, 03 Feb 2007) $

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
USA
 */

class WP_Amazon {

  var $version;
  var $country;
  var $associate_id;
  var $subscription_id;
  var $plugin_home_url;

  function wp_amazon () {
    // load i18n translations
    load_plugin_textdomain('wpamazon');

    // initialize all the variables
    $this->version = '2.1';
    $this->plugin_home_url = 'http://manalang.com/wp-amazon';
    $this->country = get_option('wpamazon_country_tld');
    $this->associate_id = get_option('wpamazon_associate_id');

    // Set defaults if properties aren't set
    if ( !$this->country ) update_option('wpamazon_country_tld', 'US');
  }

  function check_for_updates() {
    $request  = "GET http://svn.wp-plugins.org/wp-amazon/trunks/latest-version.txt HTTP/1.1\n";
    $request .= "Host: svn.wp-plugins.org\n";
    $request .= "Referer: " . $_SERVER["SCRIPT_URI"] . "\n";
    $request .= "Connection: close\n";
    $request .= "\n";

    $fp = fsockopen("svn.wp-plugins.org", 80);
    fputs($fp, $request);
    while(!feof($fp)) {
      $result .= fgets($fp, 128);
    }
    fclose($fp);

    $result = split("\r\n", $result);

    foreach($result as $k) {
      if(!strncmp($k, "Version: ", 9)) {
        $result = $k;
        break;
      }
    }

    $version = split(": ", $k);
    $version = $version[1];

    return $version;
  }

  function options_page() {

    if(isset($_POST['submitted'])) {

      update_option('wpamazon_country_tld', $_POST['wpamazon_country_tld']);
      update_option('wpamazon_associate_id', $_POST['wpamazon_associate_id']);

      //get any new variables
      $this->wp_amazon();

      echo '<div class="updated"><p><strong>' . __('Options saved.', 'wpamazon') . '</strong></p></div>';
    }

    $this->country = get_option('wpamazon_country_tld');
    $this->associate_id = get_option('wpamazon_associate_id');
    $var[$this->country] = "selected";

    $formaction = $_SERVER['PHP_SELF'] . "?page=wp-amazon/wp-amazon.php";

    // Check if there is a new version of WP-Amazon
    $version_synch_val = get_option('wpamazon_check_version');

    if ( empty($version_synch_val) )
      add_option('wpamazon_check_version', '0');

    if (get_option('wpamazon_check_version') < ( time() - 1200 ) ) {
      $latest_version = $this->check_for_updates();
      update_option('wpamazon_check_version', time());
      update_option('wpamazon_latest_version', $latest_version);
    } else {
      $latest_version = get_option('wpamazon_latest_version');
    }

    if ($this->version != $latest_version )
      $update = "<a href=\"$this->plugin_home_url\" style=\"color:red\">Click here to get the latest update.</a>";

    // Start outputting XHMTL
?>
        <div class="wrap">
            <h2><?php _e('General Options', 'wpamazon'); ?></h2>

            <form name="wpamazon_options" method="post" action="<?php echo $formaction; ?>">
            <input type="hidden" name="submitted" value="1" />

            <fieldset class="options">
                <legend>
                    <label><?php _e('Default Country', 'wpamazon'); ?></label>
                </legend>

                <p>
                <?php _e('Which Amazon country site would you like as your default?  Currently, Amazon\'s E-Commerce Service works with the following countries: Canada, France, Germany, Great Britain, Japan, and United States', 'wpamazon'); ?>

                </p>

                <table width="100%" cellspacing="2" cellpadding="5" class="editform">
                <tr>
                    <th width="33%" valign="top" scope="row"><?php _e('Default Country:', 'wpamazon'); ?> </th>
                    <td>
                        <select name="wpamazon_country_tld">
                            <option value="ca" <?php echo $var['ca']; ?>><?php _e('Canada', 'wpamazon'); ?></option>
                            <option value="fr" <?php echo $var['fr']; ?>><?php _e('France', 'wpamazon'); ?></option>
                            <option value="co.uk" <?php echo $var['co.uk']; ?>><?php _e('Great Britain', 'wpamazon'); ?></option>
                            <option value="de" <?php echo $var['de']; ?>><?php _e('Germany', 'wpamazon'); ?></option>
                            <option value="co.jp" <?php echo $var['co.jp']; ?>><?php _e('Japan', 'wpamazon'); ?></option>
                            <option value="com" <?php echo $var['com']; ?>><?php _e('United States', 'wpamazon'); ?></option>
                        </select>

                    </td>
                </tr>
                </table>
            </fieldset>

            <fieldset class="options">
                <legend>
                    <label><?php _e('Associates ID', 'wpamazon'); ?></label>
                </legend>

                <p>
                <?php _e('Amazon has an affiliate program called Amazon Associates.  This program allows you to earn money for refering customers to Amazon. To apply for the Associates Program, visit the <a href="http://www.amazon.com/associates">Amazon Associates website</a> for details.', 'wpamazon'); ?>
                </p>
                <p>
                <?php _e('You can chose to have WP-Amazon apply your Associate ID to any Amazon products you post via WP-Amazon &mdash; just specify your Associate ID here.', 'wpamazon'); ?>
                </p>

                <table width="100%" cellspacing="2" cellpadding="5" class="editform">
                <tr>
                    <th width="33%" valign="top" scope="row"><?php _e('Associate ID:', 'wpamazon'); ?> </th>
                    <td>
                        <input name="wpamazon_associate_id" type="text" id="wpamazon_associate_id" value="<?php echo $this->associate_id; ?>" size="50" /><br />
                    </td>
                </tr>
                </table>
            </fieldset>

            <p><?php printf(__('This version of WP-Amazon is %1$s and the latest version is %2$s. %3$s', 'wpamazon'), $this->version, $latest_version, $update); ?></p>

            <p class="submit">
                <input type="submit" name="Submit" value="<?php _e('Update Options &raquo;', 'wpamazon'); ?>" />
            </p>
        </form>


        </div>

<?php
  }

  // Adds javascript function to launch a new window for the search page
  function add_head() {
    if (!(strstr($_SERVER['PHP_SELF'], 'post-new.php') || strstr($_SERVER['PHP_SELF'], 'page-new.php')
      || strstr($_SERVER['PHP_SELF'], 'post.php') || strstr($_SERVER['PHP_SELF'], 'page.php')))
        return 0;
?>
            <link rel="stylesheet" href="../wp-content/plugins/wp-amazon/css/wp-amazon.css" type="text/css" />
    <script type="text/javascript">
<?php
    echo("var wpa2AssociatesId = '" . $this->associate_id . "';");
    echo("var wpa2CountryTLD = '" . $this->country . "';");
?>
    </script>
            <script type="text/javascript" src="../wp-content/plugins/wp-amazon/js/wp-amazon.js"></script>
            <script type="text/javascript" src="../wp-content/plugins/wp-amazon/js/dimensions.js"></script>
<?php
  }

  function show_options_page() {
    global $wp_amazon;
    add_options_page(__('WP-Amazon Options', 'wpamazon'), __('Amazon', 'wpamazon'), 8, __FILE__, array(&$wp_amazon, 'options_page'));
  }

} // Class WP_Amazon



// Add actions to call the function
add_action('plugins_loaded', create_function('$a', 'global $wp_amazon; $wp_amazon = new WP_Amazon;'));
add_action('admin_head', array(&$wp_amazon, 'add_head'));
add_action('admin_menu', array(&$wp_amazon, 'show_options_page'));

?>
