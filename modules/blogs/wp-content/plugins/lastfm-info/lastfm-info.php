<?php

/*

Plugin Name: Last.fm Info
Version: 1.4
Plugin URI: http://mcnicks.org/wordpress/lastfm-info/
Description: Displays last.fm information.
Author: David McNicol
Author URI: http://mcnicks.org/

Copyright (c) 2005
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

This file is part of WordPress.
WordPress is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/


// This is the URL of the AudioScrobbler web services that we use.

define( AS_WS_URL, 'http://ws.audioscrobbler.com/1.0/user/' );

// Include the configuration functions.

include_once( 'li_config.php' );



/*
 * lastfm_playlist
 *
 * Prints the last.fm playlist as HTML.
 */

function lastfm_playlist ( $before = "<li>", $after = "</li>", $dateformat = "j F Y \a\\t G:i" ) {

  // Fetch the username from WordPress options.

  $username = get_option( 'li_lastfm_username' );
  $number = get_option( 'li_tracks' );
  $timeout = get_option( 'li_timeout' );
  
  if ( ! $username ) return;

  // Get the URL of the playlist.

  $url = AS_WS_URL . "$username/recenttracks.xml";

  // Get the old contents just in case there are no entries in
  // the current playlist.

  $oldcontents = uc_get_cached_contents( $url );

  // Get the playlist from AudioScrobbler.

  $xml = xml_cache( $url, "", "", $timeout );

  // If the XML does not contain any tracks, put the old cached
  // version back into the cache and try again.

  if ( ! count( array_filter( $xml, "li_is_track" ) ) ) {

    uc_cache_contents( $url, $oldcontents );

    // Use a large timeout value to make sure that the cached
    // copy is returned. This should not be needed, since we
    // have just cached the old content above but, for some
    // reason, it is.

    $xml = xml_cache( $url, "", "", 3600 );
  }

  // Gather the data we want from the XML and print it.

  $track = "";
  $options = get_option('lastfm-infos');
  echo '<h2 class="lastfm">'.$options['title'].'</h2>';
  echo '<ul class="lastfm">';

  foreach ( $xml as $element ) {

    switch ( $element['tag'] ) {

    case 'track':

      if ( $element['type'] == 'open' ) {

        if ( $number-- < 1 ) return;
        $track = array();

      } else {
        li_print_track( $track, $before, $after, $dateformat );
      }

      break;

    case 'artist':

      $track['artist'] = $element['value'];

      break;

    case 'name':

      $track['name'] = $element['value'];

      break;

    case 'url':

      $track['url'] = $element['value'];

      break;

    case 'date':

      $track['uts'] = $element['attributes']['uts'];

      break;
    }
  }
  
  echo '</ul>';
  
}



/*
 * li_is_track
 *
 * Returns true if the element is a track. This is used to filter
 * the XML data structure to determine if there are any track specified
 * in it - see above.
 */

function li_is_track ( $element ) {
  return $element['tag'] == 'track';
}



/*
 * li_print_track
 *
 * Prints the given track.
 */

function li_print_track( $track, $before, $after, $dateformat ) {

  echo $before;
  echo " <a href=\"" . $track['url'] . "\">";
  echo $track['artist'] . " - " . $track['name'];
  echo "</a> ";
  /*echo "<span class=\"more\">played ";
  
  if ( function_exists( "time_since" ) )
    echo time_since( $track['uts'] ) . " ago";
  else
    echo "on " . date( $dateformat, $track['uts'] );

  echo "</span>";*/
  echo $after;
  echo "\n";
}

# this function gets called when widgets are supported
function widget_lastfminfos_init() {

  # does this wordpress environment support widgets?
  if (!function_exists('register_sidebar_widget'))
    return;

  # output for sidebar
  function widget_lastfminfos($args) {
    /*extract($args);

    $options = get_option('lastfm-infos');

    echo "\n\n" . $before_widget . $before_title . $options['title'] . $after_title . "\n";
    lastfmrecords_display();
		echo $after_widget . "\n\n";*/

	lastfm_playlist();		
  }

  function widget_lastfminfos_control() {
    $options = get_option('lastfm-infos');

    if (($_POST['lastfminfos-submit']) && ("" != $_POST['lastfminfos-title'])) {
      $options['title'] = strip_tags(stripslashes($_POST['lastfminfos-title']));
      update_option('lastfm-infos', $options);
    }

    $title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
    <p style="text-align:right;">
      <label for="lastfminfos-title">title: 
        <input style="width: 200px;" id="lastfminfos-title" name="lastfminfos-title" type="text" value="<?= $title ?>" />
      </label>
    </p>
    <input type="hidden" id="lastfminfos-submit" name="lastfminfos-submit" value="1" />    
<?
  }

  # if you want to use it as a widget, it's available
  register_sidebar_widget('Last.Fm Infos', 'widget_lastfminfos');

  // and we need a small form to add a title in the sidebar
  register_widget_control('Last.Fm Infos', 'widget_lastfminfos_control', 375, 95);
}

# widget variant
add_action('plugins_loaded', 'widget_lastfminfos_init');

?>
