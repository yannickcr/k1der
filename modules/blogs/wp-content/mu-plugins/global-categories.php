<?php

function global_categories( $cat_ID ) {
	global $wpdb;

	$cat_ID = intval( $cat_ID );
	$c = $wpdb->get_row( "SELECT * FROM $wpdb->categories WHERE cat_ID = '$cat_ID'" );

	$global_category = $wpdb->get_row( "SELECT * FROM $wpdb->sitecategories WHERE category_nicename = '" . $wpdb->escape( $c->category_nicename ) . "'" );

	if ( $global_category ) {
		$global_id = $global_category->cat_ID;
	} else {
		$wpdb->query( "INSERT INTO $wpdb->sitecategories ( cat_name, category_nicename ) VALUES ( '" . $wpdb->escape( $c->cat_name ) . "', '" . $wpdb->escape( $c->category_nicename ) . "' )" );
		$global_id = $wpdb->insert_id;
	}
	$wpdb->query( "UPDATE $wpdb->categories SET cat_ID = '$global_id' WHERE cat_id = '$cat_ID'" );
	$wpdb->query( "UPDATE $wpdb->categories SET category_parent = '$global_id' WHERE category_parent = '$cat_ID'" );
	$wpdb->query( "UPDATE $wpdb->post2cat SET category_id = '$global_id' WHERE category_id = '$cat_ID'" );
	$wpdb->query( "UPDATE $wpdb->link2cat SET category_id = '$global_id' WHERE category_id = '$cat_ID'" );
	wp_cache_delete($cat_ID, 'category');
	wp_cache_delete($global_id, 'category');
	wp_cache_delete('all_category_ids', 'category');

	do_action('update_cat_id', $global_id, $cat_ID);

	return $global_id;
}

add_filter( 'cat_id_filter', 'global_categories' );
?>
