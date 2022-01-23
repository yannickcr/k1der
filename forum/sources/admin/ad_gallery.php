<?php

/*
+--------------------------------------------------------------------------
|   Invision Gallery Module
|   ========================================
|   by Joshua Williams
|   (c) 2001 - 2003 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisiongallery.com
|   Email: josh@invisiongallery.com
+---------------------------------------------------------------------------
|
|   > Main Admin Module
|   > Script written by Joshua Williams
|
+--------------------------------------------------------------------------
*/

$idx = new ad_gallery();

class ad_gallery {

	var $base_url;

	function auto_run()
	{
		global $ibforums, $DB,  $std;
		
		//-----------------------------------------
		// Kill globals - globals bad, Homer good.
		//-----------------------------------------
		
		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );
		
		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}
		
		$ibforums->admin->page_title = "Invision Gallery Manager";
		
		$ibforums->admin->page_detail = "You can set up and manage your gallery in this section.";
		
		$ibforums->admin->nav[] = array( 'act=gallery'              , 'Invision Gallery Manager Home' );
		
		//-----------------------------------------
		// Do some set up
		//-----------------------------------------
		
		if ( ! @is_dir( ROOT_PATH.'/modules/gallery' ) )
		{
			//$std->boink_it("http://www.invisiongallery.com/?why");
		}
		else
		{
			define( 'IPB_CALLED', 1 );
    		$DB->load_cache_file( ROOT_PATH . 'sources/sql/'.SQL_DRIVER.'_gallery_queries.php', 'gallery_sql_queries' );
    		$DB->load_cache_file( ROOT_PATH . 'sources/sql/'.SQL_DRIVER.'_gallery_admin_queries.php', 'gallery_admin_sql_queries' );
            
            $section = ( $ibforums->input['code'] ) ? "ad_{$ibforums->input['code']}" : "ad_cats";

			require ROOT_PATH.'modules/gallery/lib/gallery_library.php';
			require ROOT_PATH.'modules/gallery/admin/'.$section.'.php';
			
			$PLUGIN = new ad_plugin_gallery_sub();
            $PLUGIN->auto_run();
		}		
	}		
}

?>