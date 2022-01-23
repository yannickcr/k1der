<?php
/*
Plugin Name: WP-Download
Version: 1.2
Plugin URI: http://www.arno-box.net/wordpress/12/wordpress-plugin-wp-download/
Author: Arno's Toolbox
Author URI: http://www.arno-box.net
Description: Download manager for wordpress user featuring download count, force download, quicktag, members only, widgets, etc. Integrated with search engine to find your downloads easily and pagination on download list.
*/

/* On charge au besoin le fichier WP-Config */
if (!function_exists('add_action')) {
	require_once('../../../wp-config.php');
}

$tmp_info_local_file = pathinfo(__FILE__);
define("wp_download_url_condition_of_use_default", "http://".$_SERVER["SERVER_ADDR"]."/conditions_of_use/", true);
define("wp_download_add_zip_default", "yes", true);
define("wp_download_insert_file_default", str_replace("\\", "/", $tmp_info_local_file['dirname'])."/conditions_of_use.txt", true);
define("wp_download_sidebar_default", "page", true);
define("wp_download_private_file_default", "no", true);
define("wp_download_display_date_default", "no", true);

add_option("wp_download_url_condition_of_use", wp_download_url_condition_of_use_default, 'URL of Conditions of use', 'yes');
add_option("wp_download_add_zip", wp_download_add_zip_default, 'Add conditions of use in ZIP File', 'yes');
add_option("wp_download_insert_file", wp_download_insert_file_default, 'File to include in zip file', 'yes');
add_option("wp_download_sidebar", wp_download_sidebar_default, 'Type of link in sidebar', 'yes');
add_option("wp_download_private_file", wp_download_private_file_default, 'Insert link of private post in sidebar', 'yes');
add_option("wp_download_display_date", wp_download_display_date_default, 'Display date insert download', 'yes');

/* On supprime les anciens filtre de formatage */  
remove_filter('the_content', 'wptexturize');  
remove_filter('the_content', 'convert_chars');  

/* On défini les noms de nos tables */
$wpdb->downloads = $table_prefix . 'downloads';
$wpdb->downloads_groups = $table_prefix . 'downloads_groups';
$wpdb->downloads_groups_link = $table_prefix . 'downloads_groups_link';

/* On vérifie si l'on a un chemin vers un download */
if (isset($_GET['dl_id'])) {
	if (ereg("^[0-9]+$", $_GET['dl_id'])) {
		/* On récupère l'id du fichier */
		$dl_id = $_GET['dl_id'];

		/* On augmente le compteur de un */
		$wpdb->query("UPDATE `$wpdb->downloads` SET dl_count=dl_count+1 WHERE dl_id='$dl_id'");

		/* on récupère les informations sur le fichier */
		$url = "SELECT dl_url FROM `$wpdb->downloads` WHERE dl_id = '$dl_id'";
		$file = $wpdb->get_var($url);
		$file = str_replace(' ','%20',$file);
		$filename = basename($file);
	
		/* On envoi le fichier au navigateur */
		$mimetype = 'application/octet-stream';  // Set mime-type
		header("Pragma: "); // Leave blank for issues with IE
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: $mimetype");
		if (ini_get('allow_url_fopen') == 0 && !function_exists('curl_init')) {
			header('Location: '.$file.''); // Switch to normal download mode if allow_url_fopen is disabled and cURL is not available
		} else {
			header('Content-Disposition: attachment; filename='.basename($filename)); // Force download activated
		}

		if (ini_get('allow_url_fopen') == 1) {
			$file = fopen($file, "rb");
			fpassthru($file);
			exit();
		} elseif (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $file);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec ($ch);
			curl_close ($ch);
			exit();
		}
		exit();
	}
}

/* Fonction appelé lors de l'installation du plugin */
add_action('activate_wp-download/wp-download.php', 'WP_Download_Install');
function WP_Download_Install() {
	global $wpdb;

	if ($wpdb->get_var("show tables like '$wpdb->downloads'") != $wpdb->downloads) {
		$sql = "CREATE TABLE `$wpdb->downloads` (
				`dl_id` bigint(20) NOT NULL auto_increment,
				`dl_title` varchar(255) NOT NULL,
				`dl_url` varchar(255) NOT NULL,
				`dl_page` varchar(255) NOT NULL,
				`dl_post_id` int(11) default NULL,
				`dl_create_date` datetime default NULL,
				`dl_count` int(11) NOT NULL default '0',
				`dl_top10` tinyint(1) NOT NULL default '1',
				primary key (`dl_id`),
				UNIQUE KEY `dl_url` (`dl_url`)
				);";
		mysql_query($sql) or die("An unexpected error occured." . mysql_error());
	} else {
		/* Suite à la MAJ du 17/06/2007 on vérifie l'existence de l'entrée top10 */
		$exist_top10_colum = false;
		$columns_dl = $wpdb->get_results("SHOW COLUMNS FROM `$wpdb->downloads`");
		foreach ($columns_dl as $entry) {
			if ($entry->Field == "dl_top10")
				$exist_top10_colum = true;
		}
		if ($exist_top10_colum == false) {
			$sql = "ALTER TABLE `$wpdb->downloads` ADD `dl_top10` BOOL NOT NULL DEFAULT '1'";
			mysql_query($sql);
		}
		/* Suite à la MAJ du 02/09/2007 on vérifie l'existence de l'entrée post_id */
		$exist_post_id_colum = false;
		$columns_dl = $wpdb->get_results("SHOW COLUMNS FROM `$wpdb->downloads`");
		foreach ($columns_dl as $entry) {
			if ($entry->Field == "dl_post_id")
				$exist_post_id_colum = true;
		}
		if ($exist_post_id_colum == false) {
			$sql = "ALTER TABLE `$wpdb->downloads` ADD `dl_post_id` INT NULL AFTER `dl_page`";
			mysql_query($sql);
			$sql = "ALTER TABLE `$wpdb->downloads` ADD `dl_create_date` DATETIME NULL AFTER `dl_post_id`";
			mysql_query($sql);
		}
	}
	
	/* Suite à la MAJ du 02/09/2007 on vérifie l'existence des tables wp_downloads_groups et wp_downloads_groups_link */
	if ($wpdb->get_var("show tables like '$wpdb->downloads_groups'") != $wpdb->downloads_groups) {
		$sql = "CREATE TABLE `$wpdb->downloads_groups` (
				`dl_group_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`dl_group_title` VARCHAR( 256 ) NOT NULL ,
				`dl_group_create_date` datetime default NULL
				);";
		mysql_query($sql) or die("An unexpected error occured." . mysql_error());
	}
	if ($wpdb->get_var("show tables like '$wpdb->downloads_groups_link'") != $wpdb->downloads_groups_link) {
		$sql = "CREATE TABLE `$wpdb->downloads_groups_link` (
				`dl_group_id` int(11) NOT NULL,
				`dl_id` int(11) NOT NULL,
				`dl_main_download` enum('y','n') NOT NULL default 'y',
				UNIQUE KEY `UNIQUE` (`dl_group_id`,`dl_id`)
				);";
		mysql_query($sql) or die("An unexpected error occured." . mysql_error());
	}
}

/* Fonction d'ajout de la classe CSS */
add_action('wp_head', 'WP_Download_Header');
function WP_Download_Header() {
	echo '<link rel="stylesheet" href="'.get_option('siteurl').'/wp-content/plugins/wp-download/downloads-css.css" type="text/css" media="screen" />'."\n";
}

/* Fonction de recherche de download */
add_filter('the_content', 'WP_Download_Insert');
function WP_Download_Insert($content) {
	/* On remplace nos balises */
	$pattern = '/\[download\s*([^\]]*)\](.*)\[\/download\]/Uis';
	$content = preg_replace_callback($pattern, 'WP_Download_Insert_Callback', $content);
	return $content;
}

/* Fonction de remplacement du download */
function WP_Download_Insert_Callback($matched) {
	global $wpdb, $table_prefix;

	/* On défini nos variables par défaut */
	$url = $matched[2];
	$title = "Pas de titre d&eacute;fini";
	$conditions_of_use = "false";
	$top10_use = "true";
	$id_download = -1;
	$nb_download = 0;
	
	/* On parse les paramètres */
	$pattern = '/(Title|DisplayConditionsOfUse|DisplayTop10)="([^"]*)"/Uis';
	preg_match_all($pattern, $matched[1], $params, PREG_PATTERN_ORDER);

	/* On met à jour les valeurs par défaut */
	for ($cpt = 0; $cpt < count($params[0]); $cpt++) {
		if (strtolower($params[1][$cpt]) == "title") $title = $params[2][$cpt]; 
		if (strtolower($params[1][$cpt]) == "displayconditionsofuse") $conditions_of_use = $params[2][$cpt]; 
		if (strtolower($params[1][$cpt]) == "displaytop10") $top10_use = $params[2][$cpt]; 
	}

	/* On vérifi si l'on est sur une page de preview */	
	$preview_page = false;
	$check_publish = $wpdb->get_results("SELECT post_status FROM ".$table_prefix."posts WHERE ID = '".$GLOBALS["id"]."'");
	if (($check_publish[0]->post_status != "publish") && ($check_publish[0]->post_status != "private")) {
		$preview_page = true;
	}
		
	/* On cherche à savoir si le lien est déjà présent dans la base de données */
	if ($preview_page == false) {
		$check_dl = $wpdb->get_results("SELECT * FROM `$wpdb->downloads` WHERE dl_url = '".$url."'");
		if ($check_dl) {
			$result = $check_dl[0];
			$id_download = $result->dl_id;
			$nb_download = $result->dl_count;
			if ($result->dl_create_date != "") {
				if (ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $result->dl_create_date, $regs))
					$create_date = " depuis le ".$regs[3]."/".$regs[2]."/".$regs[1];
			}
			if (get_option('wp_download_display_date') == "no")
				$create_date = "";
			/* On met à jour le titre */
			$wpdb->query("UPDATE `$wpdb->downloads`
			                 SET dl_title = '".addslashes($title)."',
			                     dl_top10 = '".(($top10_use == "true") ? 1 : 0)."'
			               WHERE dl_url = '".$url."'");
			/* On met à jour l'id de la page si celui-ci n'existe pas */
			if ($result->dl_post_id == "") {
				$wpdb->query("UPDATE `$wpdb->downloads`
				                 SET dl_post_id = '".$GLOBALS["post"]->ID."'
				               WHERE dl_url = '".$url."'");
			}
		} else {
			/* On insère le download dans la base */
			$wpdb->query("INSERT INTO `$wpdb->downloads` (dl_url, dl_page, dl_post_id, dl_title, dl_count, dl_top10)
			              VALUES ('".$url."', 'http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."', '".$GLOBALS["post"]->ID."', '".addslashes($title)."', 0, '".(($top10_use == "true") ? 1 : 0)."')");
			$id_download = mysql_insert_id();
		}
	}
	
	/* On récupère la taille de l'archive */
	$file_size = WP_Download_RemoteFileSize($url);
	if ($file_size) {
		$kb = 1024;         // Kilobyte
		$mb = 1024 * $kb;   // Megabyte
		$gb = 1024 * $mb;   // Gigabyte
		if($file_size < $kb) {
			$file_size = $file_size." bytes";
		} else if($file_size < $mb) {
			$file_size = round($file_size/$kb, 2)." Ko";
		} else if($file_size < $gb) {
			$file_size = round($file_size/$mb, 2)." Mo";
	  } else if($file_size < $tb) {
			$file_size = round($file_size/$gb, 2)." Go";
		}
	} else {
		$file_size = "Taille inconnue";
	}
	
	/* On affiche les infos */
	if ($preview_page == false)
		$content = "<div class=\"post-download\"><strong><a href=\"".get_option('siteurl')."/wp-content/plugins/wp-download/wp-download.php?dl_id=".$id_download."\">".$title." (".$file_size.")</a></strong><br/><i>T&eacute;l&eacute;charg&eacute; : ".$nb_download." fois".$create_date."</i></div>";
	else
		$content = "<div class=\"post-download\"><strong><a href=\"".$url."\">".$title." (".$file_size.")</a></strong><br/><i>T&eacute;l&eacute;charg&eacute; : ".$nb_download." fois ".$create_date."</i></div>";
	if ($conditions_of_use == "true") {
		if (get_option("wp_download_add_zip") == "yes")
			WP_Download_Include_ConditionOfUse($id_download);
		$content .= "<div class=\"post-download-conditions\">* Merci de lire les <a href=\"".get_option("wp_download_url_condition_of_use")."\">conditions d'utilisation</a> avant de t&eacute;l&eacute;charger le fichier.</div>";
	}
	return $content;
}

/* On défini la fonciton get_headers si celle-ci n'existe pas */
function php4_get_headers($url, $format = 0) {
	$fp = fopen($url, 'r');
	$headers = stream_get_meta_data($fp);
	fclose($fp);

	if ($format) {
		$v = array();
		foreach($headers["wrapper_data"] as $i) {
			if (preg_match('/^([a-zA-Z -]+): +(.*)$/',$i,$parts)) {
				$v[$parts[1]] = $parts[2];
			} else {
				array_push($v, $i);
			}
		}
		return $v;
	}
	return $header["wrapper_data"];
}

/* Récupère la taille des fichiers distant */
function WP_Download_RemoteFileSize($url) {
	$info_url = parse_url($url);
	
	$sch = $info_url["scheme"];
	/* Si l'on ne reconnait pas le protocole, on retoune une erreur */
	if (($sch != "http") && ($sch != "https") && ($sch != "ftp") && ($sch != "ftps")) {
		return false;
	}
	/* Si l'on a un fichier HTTP ou HTTPS */
	if (($sch == "http") || ($sch == "https")) {
		if(!function_exists('get_headers'))
			$headers = php4_get_headers($url, 1);
		else
			$headers = get_headers($url, 1);
		if ((!array_key_exists("Content-Length", $headers))) { return false; }
		return $headers["Content-Length"];
	}
	/* Si le fichier est sur un serveur FTP */
	if (($sch == "ftp") || ($sch == "ftps")) {
		$server = $info_url["host"];
		$port = $info_url["port"];
		$path = $info_url["path"];
		$user = $info_url["user"];
		$pass = $info_url["pass"];
		if ((!$server) || (!$path)) { return false; }
		if (!$port) { $port = 21; }
		if (!$user) { $user = "anonymous"; }
		if (!$pass) { $pass = "phpos@"; }
		switch ($sch) {
			case "ftp":
				$ftpid = ftp_connect($server, $port);
				break;
			case "ftps":
				$ftpid = ftp_ssl_connect($server, $port);
				break;
		}
		if (!$ftpid) { return false; }
		$login = ftp_login($ftpid, $user, $pass);
		if (!$login) { return false; }
		$ftpsize = ftp_size($ftpid, $path);
		ftp_close($ftpid);
		if ($ftpsize == -1) { return false; }
		return $ftpsize;
	}
}

/* Cette fonction rajoute le fichier dans le zip si il n'y est pas déjà */
function WP_Download_Include_ConditionOfUse($id_download) {
	global $wpdb;
	
	/* infos sur le download*/
	$result = $wpdb->get_results("SELECT * FROM `$wpdb->downloads` WHERE dl_id = '".$id_download."'");
	$info_dl = $result[0];

	/* On vérifie si l'on a un fichier zip local */
	$local_zip_file = true;
	$parsed = parse_url($info_dl->dl_url);
	if ($parsed["host"] != $_SERVER["SERVER_ADDR"] && $parsed["host"] != $_SERVER["SERVER_NAME"]) {
		/* Le fichier n'est pas hébergé sur le serveur */
		$local_zip_file = false;
	} else {
		$local_name = $_SERVER["DOCUMENT_ROOT"].$parsed["path"];
		if (file_exists($local_name)) {
			$path_parts = pathinfo($local_name);
			if ($path_parts['extension'] != "zip") {
				/* Le fichier n'a pas une extension ZIP */
				$local_zip_file = false;
			}
		} else {
			/* Le fichier n'a pas été trouvé sur le serveur */
			$local_zip_file = false;
		}
	}

	if ($local_zip_file) {
		/* Fichier contenant les conditions d'utilisation */
		$conditionsofuse_filename = get_option("wp_download_insert_file");
		$conditionsofuse_basename = basename($conditionsofuse_filename);
		
		/* On ajoute le fichier dans l'archive */
 		$zip = new ZipArchive;
		if ($zip->open($local_name) === TRUE) {
			$conditions_of_use = "";
			$lines = file($conditionsofuse_filename);
			foreach ($lines as $line_num => $line) {
				if ($conditions_of_use != "") $conditions_of_use .= "\r\n";
				$conditions_of_use .= trim($line);
			}

			$zip->addFromString($conditionsofuse_basename, $conditions_of_use);
    	$zip->close();
		}
	}
}

/* Fonction pour le top10 dans la sidebar */
function WP_Download_Sidebar($limit = 10, $order = 'top', $sort = 'DESC') {
	global $wpdb, $table_prefix;

	if ($limit == '') { $limit = "LIMIT 10"; } else { $limit = "LIMIT $limit"; }

	$display_private_file = false;
	if (get_option("wp_download_private_file") == "yes")
		$display_private_file = true;

	if ($order == 'top') {
		$where_clause = ($display_private_file == false) ? "WHERE post_status = 2" : "";
		$sql = "SELECT *
		          FROM (SELECT ID, MAX(post_status) AS post_status, SUM(dl_count) AS dl_count
		                  FROM (SELECT IF (`$wpdb->downloads_groups`.dl_group_id, CONCAT('G',`$wpdb->downloads_groups`.dl_group_id), CONCAT('P',`$wpdb->downloads`.dl_id)) AS ID,
		                               IF(STRCMP(".$table_prefix."posts.post_status, 'private'), 2, 1) AS post_status,
		                               `$wpdb->downloads`.dl_count
		                          FROM `$wpdb->downloads` 
		                     LEFT JOIN `$wpdb->downloads_groups_link` ON `$wpdb->downloads_groups_link`.dl_id = `$wpdb->downloads`.dl_id
		                     LEFT JOIN `$wpdb->downloads_groups` ON `$wpdb->downloads_groups`.dl_group_id = `$wpdb->downloads_groups_link`.dl_group_id
		                     LEFT JOIN ".$table_prefix."posts ON ".$table_prefix."posts.ID = `$wpdb->downloads`.dl_post_id
		                       ) AS SelectDownload
		                      GROUP BY ID
		                      ORDER BY dl_count ".$sort."
		                ) AS ListDownload
		         ".$where_clause." ".$limit;
		$results = $wpdb->get_results($sql);
		if ($results) {
			foreach ($results as $result) {
				$id = $result->ID;
				$count = $result->dl_count;
				
				// On récupère les infos suivant le groupe ou le fichier seul
				ereg("([G|P])([0-9]+)", $id, $type_entry);
				if ($type_entry[1] == "P") {
					$sql = "SELECT * FROM `$wpdb->downloads` WHERE dl_id = '".$type_entry[2]."'";
				} else {
					$sql = "SELECT `$wpdb->downloads`.dl_id, `$wpdb->downloads`.dl_page,
					               `$wpdb->downloads_groups`.dl_group_title AS \"dl_title\"
					          FROM `$wpdb->downloads`
					     LEFT JOIN `$wpdb->downloads_groups_link` ON `$wpdb->downloads_groups_link`.dl_id = `$wpdb->downloads`.dl_id
					     LEFT JOIN `$wpdb->downloads_groups` ON `$wpdb->downloads_groups`.dl_group_id = `$wpdb->downloads_groups_link`.dl_group_id
					         WHERE `$wpdb->downloads_groups_link`.dl_group_id = '".$type_entry[2]."'
					           AND `$wpdb->downloads_groups_link`.dl_main_download = 'y'";
				}
				$results = $wpdb->get_results($sql);
				$title = stripslashes($results[0]->dl_title);
				if (get_option("wp_download_sidebar") == "file") 
					$url = get_settings('siteurl') . '/wp-content/plugins/wp-download/wp-download.php?dl_id='.$results[0]->dl_id;
				else
					$url = $results[0]->dl_page;
				echo '<li><a href="'.$url.'" title="'.$title.'">'.$title.'</a> ('.$count.')</li>';
			}
		}
	}
	
	if ($order == 'new') {
		$where_clause = ($display_private_file == false) ? "WHERE post_status = 2" : "";
		$sql = "SELECT *
		          FROM (SELECT ID, MAX(post_status) AS post_status, SUM(dl_count) AS dl_count, MAX(dl_id) AS dl_max_id
		                  FROM (SELECT IF (`$wpdb->downloads_groups`.dl_group_id, CONCAT('G',`$wpdb->downloads_groups`.dl_group_id), CONCAT('P',`$wpdb->downloads`.dl_id)) AS ID,
		                               IF(STRCMP(".$table_prefix."posts.post_status, 'private'), 2, 1) AS post_status,
		                               `$wpdb->downloads`.dl_count,
		                               `$wpdb->downloads`.dl_id
		                          FROM `$wpdb->downloads` 
		                     LEFT JOIN `$wpdb->downloads_groups_link` ON `$wpdb->downloads_groups_link`.dl_id = `$wpdb->downloads`.dl_id
		                     LEFT JOIN `$wpdb->downloads_groups` ON `$wpdb->downloads_groups`.dl_group_id = `$wpdb->downloads_groups_link`.dl_group_id
		                     LEFT JOIN ".$table_prefix."posts ON ".$table_prefix."posts.ID = `$wpdb->downloads`.dl_post_id
		                       ) AS SelectDownload
		                      GROUP BY ID
		                      ORDER BY dl_max_id ".$sort."
		                ) AS ListDownload
		         ".$where_clause." ".$limit;
		$results = $wpdb->get_results($sql);
		if ($results) {
			foreach ($results as $result) {
				$id = $result->ID;
				$count = $result->dl_count;
				
				// On récupère les infos suivant le groupe ou le fichier seul
				ereg("([G|P])([0-9]+)", $id, $type_entry);
				if ($type_entry[1] == "P") {
					$sql = "SELECT * FROM `$wpdb->downloads` WHERE dl_id = '".$type_entry[2]."'";
				} else {
					$sql = "SELECT `$wpdb->downloads`.dl_id, `$wpdb->downloads`.dl_page,
					               `$wpdb->downloads_groups`.dl_group_title AS \"dl_title\"
					          FROM `$wpdb->downloads`
					     LEFT JOIN `$wpdb->downloads_groups_link` ON `$wpdb->downloads_groups_link`.dl_id = `$wpdb->downloads`.dl_id
					     LEFT JOIN `$wpdb->downloads_groups` ON `$wpdb->downloads_groups`.dl_group_id = `$wpdb->downloads_groups_link`.dl_group_id
					         WHERE `$wpdb->downloads_groups_link`.dl_group_id = '".$type_entry[2]."'
					           AND `$wpdb->downloads_groups_link`.dl_main_download = 'y'";
				}
				$results = $wpdb->get_results($sql);
				$title = stripslashes($results[0]->dl_title);
				if (get_option("wp_download_sidebar") == "file") 
					$url = get_settings('siteurl') . '/wp-content/plugins/wp-download/wp-download.php?dl_id='.$results[0]->dl_id;
				else
					$url = $results[0]->dl_page;
				echo '<li><a href="'.$url.'" title="'.$title.'">'.$title.'</a> ('.$count.')</li>';
			}
		}		
	}
}

/* Fonction d'administration */
add_action('admin_menu', 'WP_Download_AddMenu');
function WP_Download_AddMenu() {
	add_options_page('WP-Download Options', 'WP-Download', 8, basename(__FILE__), 'WP_Download_Admin');
}

function WP_Download_Admin() {
	global $wpdb;

	if (isset($_POST['wp_download_settings_submit'])) {
		check_admin_referer();
		
		// On met à jour le lien vers les conditions d'utilisations
		$wp_download_url = $_POST["wp_download_settings"]["url"];
		if ($wp_download_url == '')
			$wp_download_url = wp_download_url_condition_of_use_default;
		update_option("wp_download_url_condition_of_use", $wp_download_url);

		// On met à jour l'ajout ou non du fichier des conditions d'utilisations
		$wp_download_zip = $_POST["wp_download_settings"]["zip"];
		if ($wp_download_zip <> 'yes' && $wp_download_zip <> 'no')
			$wp_download_zip = wp_download_add_zip_default;
		update_option("wp_download_add_zip", $wp_download_zip);
		
		// On met à jour le fichier à insérer dans les fichiers ZIP
		$wp_download_insert_file = $_POST["wp_download_settings"]["insert_file"];
		if ($wp_download_insert_file == '')
			$wp_download_insert_file = wp_download_insert_file_default;
		update_option("wp_download_insert_file", $wp_download_insert_file);
		
		// On met à jour l'ajout ou non du fichier des conditions d'utilisations
		$wp_download_sidebar = $_POST["wp_download_settings"]["sidebar"];
		if ($wp_download_sidebar <> 'page' && $wp_download_sidebar <> 'file')
			$wp_download_sidebar = wp_download_sidebar_default;
		update_option("wp_download_sidebar", $wp_download_sidebar);

		// On met à jour l'ajout ou non du fichier des conditions d'utilisations
		$wp_download_private_file = $_POST["wp_download_settings"]["private_file"];
		if ($wp_download_private_file <> 'yes' && $wp_download_private_file <> 'no')
			$wp_download_private_file = wp_download_private_file_default;
		update_option("wp_download_private_file", $wp_download_private_file);

		// On met à jour l'option affichant la date de début de download
		$wp_download_display_date = $_POST["wp_download_settings"]["display_date"];
		if ($wp_download_display_date <> 'yes' && $wp_download_display_date <> 'no')
			$wp_download_display_date = wp_download_display_date_default;
		update_option("wp_download_display_date", $wp_download_display_date);

		// Message de mise à jour
		echo "<div class='updated'><p><strong>WP-Download Options mis &agrave; jour</strong></p></div>";
	}

	if (isset($_POST['wp_download_file_submit'])) {
		check_admin_referer();
		
		switch ($_GET['action']) {
			case "update":
				// On vérifie le format de la date
				$check_create_date = true;
				if ($_POST["wp_download"]["create_date"] != "") {
					if (ereg ("^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})$", trim($_POST["wp_download"]["create_date"]), $regs))
						$create_date = $regs[3]."-".$regs[2]."-".$regs[1];
					else
						$check_create_date = false;
				}
				
				if ($check_create_date) {
					$sql = "UPDATE `$wpdb->downloads`
					           SET dl_create_date = ".(($_POST["wp_download"]["create_date"] == "") ? "NULL" : "'".$create_date."'").",
					               dl_count = '".$_POST["wp_download"]["count"]."'
					         WHERE dl_id = '".$_POST["dl_id"]."'";
					$wpdb->query($sql);

					// Message de mise à jour
					echo "<div class='updated'><p><strong>WP-Download - Mise &agrave; jour des informations effectu&eacute;es.</strong></p></div>";
					unset($_GET['action']);
				} else {
					// Message d'erreur
					echo "<div class='error'><p><strong>La date n'est pas au bon format.</strong></p></div>";
				}
				break;

			case "delete":
				$sql = "DELETE FROM `$wpdb->downloads_groups_link` WHERE dl_id = '".$_POST["dl_id"]."'";
				$wpdb->query($sql);
				$sql = "DELETE FROM `$wpdb->downloads` WHERE dl_id = '".$_POST["dl_id"]."'";
				$wpdb->query($sql);

				// Message de mise à jour
				echo "<div class='updated'><p><strong>WP-Download - Suppression de l'entr&eacute;e effectu&eacute;e.</strong></p></div>";
				unset($_GET['action']);
				break;
		}
	}

	if (isset($_POST['wp_download_regroup_submit'])) {
		check_admin_referer();
		
		switch ($_GET['action']) {
			case "newgroup":
				if ($_POST["wp_download_regroup"]["main_file"] <> "" && $_POST["wp_download_regroup"]["title"] != "") {
					$sql = "INSERT INTO `$wpdb->downloads_groups` (dl_group_title, dl_group_create_date)
					        VALUES ('".addslashes($_POST["wp_download_regroup"]["title"])."', NOW())";
					$wpdb->query($sql);
					$id_group = mysql_insert_id();
	
					$sql = "INSERT INTO `$wpdb->downloads_groups_link` (dl_group_id, dl_id, dl_main_download)
					        VALUES ('".$id_group."', '".$_POST["wp_download_regroup"]["main_file"]."', 'y')";
					$wpdb->query($sql);
	
					if (count($_POST["wp_download_regroup"]["id_files"]) > 0) {
						foreach ($_POST["wp_download_regroup"]["id_files"] as $id_file) {
							if ($id_file != $_POST["wp_download_regroup"]["main_file"]) {
								$sql = "INSERT INTO `$wpdb->downloads_groups_link` (dl_group_id, dl_id, dl_main_download)
								        VALUES ('".$id_group."', '".$id_file."', 'n')";
								$wpdb->query($sql);
							}
						}
					}

					// Message de mise à jour
					echo "<div class='updated'><p><strong>WP-Download - Regroupement de fichiers cr&eacute;&eacute;.</strong></p></div>";
					unset($_GET['action']);
				} else {
					// Message d'erreur
					echo "<div class='error'><p><strong>Il faut saisir un libell&eacute; et un fichier principal pour cr&eacute;er un regroupement !!!</strong></p></div>";
				}
				break;
			
			case "updategroup":
				if ($_POST["wp_download_regroup"]["main_file"] <> "" && $_POST["wp_download_regroup"]["title"] != "") {
					$sql = "UPDATE `$wpdb->downloads_groups`
					           SET dl_group_title = '".addslashes($_POST["wp_download_regroup"]["title"])."'
					         WHERE dl_group_id = '".$_POST["dl_gid"]."'";
					$wpdb->query($sql);
					
					$sql = "DELETE FROM `$wpdb->downloads_groups_link` WHERE dl_group_id = '".$_POST["dl_gid"]."'";
					$wpdb->query($sql);
					
					$sql = "INSERT INTO `$wpdb->downloads_groups_link` (dl_group_id, dl_id, dl_main_download)
					        VALUES ('".$_POST["dl_gid"]."', '".$_POST["wp_download_regroup"]["main_file"]."', 'y')";
					$wpdb->query($sql);
	
					foreach ($_POST["wp_download_regroup"]["id_files"] as $id_file) {
						if ($id_file != $_POST["wp_download_regroup"]["main_file"]) {
							$sql = "INSERT INTO `$wpdb->downloads_groups_link` (dl_group_id, dl_id, dl_main_download)
							        VALUES ('".$_POST["dl_gid"]."', '".$id_file."', 'n')";
							$wpdb->query($sql);
						}
					}

					// Message de mise à jour
					echo "<div class='updated'><p><strong>WP-Download - Regroupement de fichiers mis &agrave; jour.</strong></p></div>";
					unset($_GET['action']);
				} else {
					// Message d'erreur
					echo "<div class='error'><p><strong>Il faut saisir un libell&eacute; et un fichier principal pour mettre &agrave; jour le regroupement !!!</strong></p></div>";
				}
				break;

			case "deletegroup":
				$sql = "DELETE FROM `$wpdb->downloads_groups` WHERE dl_group_id = '".$_POST["dl_gid"]."'";
				$wpdb->query($sql);
				$sql = "DELETE FROM `$wpdb->downloads_groups_link` WHERE dl_group_id = '".$_POST["dl_gid"]."'";
				$wpdb->query($sql);

				// Message de mise à jour
				echo "<div class='updated'><p><strong>WP-Download - Suppression du regroupement.</strong></p></div>";
				unset($_GET['action']);
				break;
		}	
	}
	
	if (!isset($_GET['action'])) {
?>
		<div class="wrap">
			<h2>WP-Download Options <span style='font-size:12px;font-weight:bold;'><span style='color:red;'>v1.2</span></span></h2>
			<form method="post" action="options-general.php?page=wp-download.php">
				<fieldset class='options'>
					<li><label for="url">URL des conditions d'utilisation : <input type="text" size="60" id="url" name="wp_download_settings[url]" value="<?php echo get_option("wp_download_url_condition_of_use"); ?>" /></label></li>
					<li><label for="zip">Ajouter les conditions d'utilisation dans les fichiers zip (si option demand&eacute;s) ? <input type="radio" name="wp_download_settings[zip]" value="yes" <?php if (get_option("wp_download_add_zip") == "yes") echo "checked" ?> /> Oui <input type="radio" name="wp_download_settings[zip]" value="no" <?php if (get_option("wp_download_add_zip") == "no") echo "checked" ?> /> Non</label></li>
					<li><label for="insert_file">Fichier contenant les conditions d'utilisation : <input type="text" size="60" id="insert_file" name="wp_download_settings[insert_file]" value="<?php echo get_option("wp_download_insert_file"); ?>" /></label></li>
					<li><label for="sidebar">Lien vers les fichiers dans la sidebar : <input type="radio" name="wp_download_settings[sidebar]" value="file" <?php if (get_option("wp_download_sidebar") == "file") echo "checked" ?> /> T&eacute;l&eacute;chargement du fichier <input type="radio" name="wp_download_settings[sidebar]" value="page" <?php if (get_option("wp_download_sidebar") == "page") echo "checked" ?> /> Afficher la page contenant le fichier</label></li>
					<li><label for="private_file">Afficher les fichiers des pages priv&eacute;es dans la sidebar ? <input type="radio" name="wp_download_settings[private_file]" value="yes" <?php if (get_option("wp_download_private_file") == "yes") echo "checked" ?> /> Oui <input type="radio" name="wp_download_settings[private_file]" value="page" <?php if (get_option("wp_download_private_file") == "no") echo "checked" ?> /> Non</label></li>
					<li><label for="display_date">Afficher la date de mise en ligne du fichier ? <input type="radio" name="wp_download_settings[display_date]" value="yes" <?php if (get_option("wp_download_display_date") == "yes") echo "checked" ?> /> Oui <input type="radio" name="wp_download_settings[display_date]" value="page" <?php if (get_option("wp_download_display_date") == "no") echo "checked" ?> /> Non</label></li>
				</fieldset>
				<p class="submit"><input type="submit" name="wp_download_settings_submit" value="Mise &agrave; jour des options" /></p>
			</form>
		
			<h2>Liste des 10 fichiers les plus t&eacute;l&eacute;charg&eacute;s</h2>
			<table class="widefat">
				<thead>
				<tr> 
					<th scope="col"><center>ID</center></th>
					<th scope="col">Titre</th>
					<th scope="col">Adresse</th>
					<th scope="col"><center>T&eacute;l&eacute;charg&eacute;</center></th>
					<th scope="col" colspan="2"><center>Actions</center></th>
				</tr>
				</thead>
			<?php
				$results = $wpdb->get_results("SELECT * FROM `$wpdb->downloads` ORDER BY dl_count DESC, dl_create_date DESC LIMIT 10");
				if ($results) {
					foreach ($results as $result) {
						$class = ('alternate' != $class) ? 'alternate' : '';
						?>
						<tr class="<?php echo $class; ?>">
							<td align="center"><?php echo $result->dl_id; ?></td>
							<td><?php echo $result->dl_title; ?></td>
							<td><?php echo $result->dl_url; ?></td>
							<td align="center"><?php echo $result->dl_count; ?></td>
							<td><center><?php echo '<a href="?page='.basename(__FILE__).'&amp;action=update&amp;dlid='.$result->dl_id.'">Modifier</a>'; ?></center></td>
							<td><center><?php echo '<a href="?page='.basename(__FILE__).'&amp;action=delete&amp;dlid='.$result->dl_id.'" >Supprimer</a>'; ?></center></td>
						</tr>
						<?php
					}
				}
			?>
			</table>
			<p style="float: right;"><a href="?page=<?php echo basename(__FILE__); ?>&amp;action=listfiles">Voir la liste compl&egrave;te &raquo;</a></p>
			&nbsp;<br/>
			&nbsp;<br/>
		
			<h2>Regrouper des downloads</h2>
			<p>Dans le cas de nouvelles version il peut &ecirc;tre interessant de regrouper les downloads pour n'afficher qu'une seul entr&eacute;e dans le TOP 10 des t&eacute;l&eacute;chargements.</p>
			<table class="widefat">
				<thead>
				<tr> 
					<th scope="col"><center>ID</center></th>
					<th scope="col">Titre</th>
					<th scope="col">Liste des download regroup&eacute;s</th>
					<th scope="col"><center>T&eacute;l&eacute;charg&eacute;</center></th>
					<th scope="col" colspan="2"><center>Actions</center></th>
				</tr>
				</thead>
				<?php
					$sql = "SELECT `$wpdb->downloads_groups`.dl_group_id, `$wpdb->downloads_groups`.dl_group_title, sum(dl_count) as dl_count
					          FROM `$wpdb->downloads_groups`
					     LEFT JOIN `$wpdb->downloads_groups_link` ON `$wpdb->downloads_groups_link`.dl_group_id = `$wpdb->downloads_groups`.dl_group_id
					     LEFT JOIN `$wpdb->downloads` ON `$wpdb->downloads`.dl_id = `$wpdb->downloads_groups_link`.dl_id
					      GROUP BY `$wpdb->downloads_groups`.dl_group_id, `$wpdb->downloads_groups`.dl_group_title
					      ORDER BY dl_count DESC";
					$results = $wpdb->get_results($sql);
					if ($results) {
						foreach ($results as $result) {
							$class = ('alternate' != $class) ? 'alternate' : '';
							$sql = "SELECT `$wpdb->downloads`.*, `$wpdb->downloads_groups_link`.dl_main_download
							          FROM `$wpdb->downloads_groups_link`
							     LEFT JOIN `$wpdb->downloads` ON `$wpdb->downloads`.dl_id = `$wpdb->downloads_groups_link`.dl_id
							         WHERE `$wpdb->downloads_groups_link`.dl_group_id = '".$result->dl_group_id."'
							      ORDER BY `$wpdb->downloads_groups_link`.dl_main_download ASC, `$wpdb->downloads`.dl_title ASC";
							$list_files = $wpdb->get_results($sql);
							$display_include_file = "";
							foreach ($list_files as $file) {
								if ($display_include_file != "") $display_include_file .= "<br />";
								if ($file->dl_main_download == "y")
									$display_include_file .= "<b>".$file->dl_title." (".$file->dl_count." t&eacute;l&eacute;chargement".(($file->dl_count > 1) ? "s" : "").")</b>";
								else
									$display_include_file .= $file->dl_title." (".$file->dl_count." t&eacute;l&eacute;chargement".(($file->dl_count > 1) ? "s" : "").")";
							}
							?>
							<tr class="<?php echo $class; ?>">
								<td align="center"><?php echo $result->dl_group_id; ?></td>
								<td><?php echo $result->dl_group_title; ?></td>
								<td><?php echo $display_include_file; ?></td>
								<td align="center"><?php echo $result->dl_count; ?></td>
								<td><center><?php echo '<a href="?page='.basename(__FILE__).'&amp;action=updategroup&amp;dlgid='.$result->dl_group_id.'">Modifier</a>'; ?></center></td>
								<td><center><?php echo '<a href="?page='.basename(__FILE__).'&amp;action=deletegroup&amp;dlgid='.$result->dl_group_id.'" >Supprimer</a>'; ?></center></td>
							</tr>
							<?php
						}
					}
				?>
			</table>
			<p align="center"><a href="?page=<?php echo basename(__FILE__); ?>&amp;action=newgroup">Cr&eacute;er un nouveau regroupement</a></p>
			
		</div>
<?php
	} else {
		switch ($_GET['action']) {
			case "update":
				$sql = "SELECT * FROM `$wpdb->downloads` WHERE dl_id = '".$_GET["dlid"]."'";
				$results = $wpdb->get_results($sql);
				$title = $results[0]->dl_title;
				$url = $results[0]->dl_url;
				$page = $results[0]->dl_page;
				$create = $results[0]->dl_create_date;
				if (ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $create, $regs))
					$create = $regs[3]."/".$regs[2]."/".$regs[1];
				$count = $results[0]->dl_count;
?>
		<div class="wrap">
			<div align="right"><a href="?page=<?php echo basename(__FILE__); ?>">Retour au panneau de configuration &raquo;</a></div>
			<h2>WP-Download - Mise &agrave; jour d'informations sur le fichier</h2>
			<form method="post" action="options-general.php?page=wp-download.php&amp;action=<?php echo $_GET['action']; ?>&amp;dlid=<?php echo $_GET["dlid"]; ?>">
				<input type="hidden" name="dl_id" value="<?php echo $_GET["dlid"]; ?>">
				<p>La plus part des informations li&eacute;es aux fichiers se mettent directement &agrave; jour depuis l'article ou la page o&ugrave; se situe le lien de t&eacute;l&eacute;chargement.</p>
				<table>
					<tr>
						<td>Libell&eacute; du fichier :</td>
						<td><b><?php echo $title; ?></b></td>
					</tr>
					<tr>
						<td>Adresse du fichier :</td>
						<td><b><?php echo $url; ?></b></td>
					</tr>
					<tr>
						<td>Adresse de la page :</td>
						<td><b><?php echo $page; ?></b></td>
					</tr>
					<tr>
						<td>Date de mise en ligne :<br /><i>(format jj/mm/aaaa)</i></td>
						<td><input type="text" size="20" id="create_date" name="wp_download[create_date]" value="<?php echo $create; ?>" /></td>
					</tr>
					<tr>
						<td>Nombre de t&eacute;l&eacute;chargements :</td>
						<td><input type="text" size="20" id="count" name="wp_download[count]" value="<?php echo $count; ?>" /></td>
					</tr>
				</table>
				<p class="submit"><input type="submit" name="wp_download_file_submit" value="Mettre &agrave; jour les informations" /></p>
			</form>
		</div>
<?php
				break;

			case "delete":
				$sql = "SELECT * FROM `$wpdb->downloads` WHERE dl_id = '".$_GET["dlid"]."'";
				$results = $wpdb->get_results($sql);
				$title = $results[0]->dl_title;
?>
		<div class="wrap">
			<div align="right"><a href="?page=<?php echo basename(__FILE__); ?>">Retour au panneau de configuration &raquo;</a></div>
			<h2>WP-Download - Suppression d'un fichier</h2>
			<form method="post" action="options-general.php?page=wp-download.php&amp;action=delete">
				<input type="hidden" name="dl_id" value="<?php echo $_GET["dlid"]; ?>">
				<p>Etes vous sur de vouloir supprimer l'entr&eacute;e : <b>"<?php echo $title; ?>"</b> ?</p>
				<p><i>Attention, si vous supprimez un fichier et qu'il reste une r&eacute;f&eacute;rence dans un article ou une page, cette entr&eacute;e sera automatiquement recr&eacute;er.</i></p>
				<p class="submit"><input type="submit" name="wp_download_file_submit" value="Supprimer l'entr&eacute;e" /></p>
			</form>
		</div>
<?php
				break;
				
			case "newgroup":
			case "updategroup":
				if ($_GET['action'] == "updategroup") {
					$sql = "SELECT * FROM `$wpdb->downloads_groups` WHERE dl_group_id = '".$_GET["dlgid"]."'";
					$results = $wpdb->get_results($sql);
					$title = $results[0]->dl_group_title;

					$main_file = "";
					$id_files = array();
					$sql = "SELECT * FROM `$wpdb->downloads_groups_link` WHERE dl_group_id = '".$_GET["dlgid"]."'";
					$results = $wpdb->get_results($sql);
					foreach ($results as $file) {
						if ($file->dl_main_download == "y")
							$main_file = $file->dl_id;	
						array_push($id_files, $file->dl_id);
					}
					$where_clause = "(`$wpdb->downloads_groups_link`.dl_id IS NULL OR `$wpdb->downloads_groups_link`.dl_group_id = '".$_GET["dlgid"]."')";
					$btn_title = "Mettre &agrave; jour le regroupement";
				} else {
					$title = "";
					$main_file = "";
					$id_files = array();
					$where_clause = "`$wpdb->downloads_groups_link`.dl_id IS NULL";
					$btn_title = "Cr&eacute;er le regroupement";
				}
?>
		<div class="wrap">
			<div align="right"><a href="?page=<?php echo basename(__FILE__); ?>">Retour au panneau de configuration &raquo;</a></div>
			<h2>WP-Download - Regroupement de fichiers</h2>
			<form method="post" action="options-general.php?page=wp-download.php&amp;action=<?php echo $_GET['action']; ?>">
				<input type="hidden" name="dl_gid" value="<?php echo $_GET["dlgid"]; ?>">
				<table>
					<tr>
						<td>Libell&eacute; du regroupement :</td>
						<td><input type="text" size="60" id="url" name="wp_download_regroup[title]" value="<?php echo $title; ?>" style="width:500px" /></td>
					</tr>
					<tr>
						<td valign="top">Fichier principal :</td>
						<td>
							<select name="wp_download_regroup[main_file]" style="width:500px;">
								<?php
									$sql = "SELECT `$wpdb->downloads`.*
									          FROM `$wpdb->downloads`
									     LEFT JOIN `$wpdb->downloads_groups_link` ON `$wpdb->downloads_groups_link`.dl_id = `$wpdb->downloads`.dl_id
									         WHERE ".$where_clause."
									      ORDER BY `$wpdb->downloads`.dl_title";
									$results = $wpdb->get_results($sql);
									if ($results) {
										foreach ($results as $result) {
											$selected = ($result->dl_id == $main_file) ? "selected" : "";
											echo "<option value=\"".$result->dl_id."\" ".$selected.">".$result->dl_title." (".$result->dl_count." t&eacute;l&eacute;chargement".(($result->dl_count > 1) ? "s" : "").")</option>";
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">S&eacute;lectionner les fichiers<br />compl&eacute;mentaire &agrave; regrouper <br/><i>(S&eacute;lection multiple).</i></td>
						<td>
							<select name="wp_download_regroup[id_files][]" size="8" multiple="true" style="width:500px;">
								<?php
									$sql = "SELECT `$wpdb->downloads`.*
									          FROM `$wpdb->downloads`
									     LEFT JOIN `$wpdb->downloads_groups_link` ON `$wpdb->downloads_groups_link`.dl_id = `$wpdb->downloads`.dl_id
									         WHERE ".$where_clause."
									      ORDER BY `$wpdb->downloads`.dl_title";
									$results = $wpdb->get_results($sql);
									if ($results) {
										foreach ($results as $result) {
											$selected = (in_array($result->dl_id, $id_files)) ? "selected" : "";
											echo "<option value=\"".$result->dl_id."\" ".$selected.">".$result->dl_title." (".$result->dl_count." t&eacute;l&eacute;chargement".(($result->dl_count > 1) ? "s" : "").")</option>";
										}
									}
								?>
							</select>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" name="wp_download_regroup_submit" value="<?php echo $btn_title; ?>" /></p>
			</form>
		</div>
<?php
				break;

			case "deletegroup":
				$sql = "SELECT * FROM `$wpdb->downloads_groups` WHERE dl_group_id = '".$_GET["dlgid"]."'";
				$results = $wpdb->get_results($sql);
				$title = $results[0]->dl_group_title;
?>
		<div class="wrap">
			<div align="right"><a href="?page=<?php echo basename(__FILE__); ?>">Retour au panneau de configuration &raquo;</a></div>
			<h2>WP-Download - Regroupement de fichiers</h2>
			<form method="post" action="options-general.php?page=wp-download.php&amp;action=deletegroup">
				<input type="hidden" name="dl_gid" value="<?php echo $_GET["dlgid"]; ?>">
				<p>Etes vous sur de vouloir supprimer le regroupement : <b>"<?php echo $title; ?>"</b> ?</p>
				<p class="submit"><input type="submit" name="wp_download_regroup_submit" value="Supprimer le regroupement" /></p>
			</form>
		</div>
<?php
				break;

			case "listfiles":
				$nb_entries = $wpdb->get_var("SELECT COUNT(*) AS Nb FROM `$wpdb->downloads`");

				if (isset($_POST["wp_download_filter_submit"])) {
					if ($_POST["wp_download_filter_submit"] == "Filtrer")
						$where_clause = "WHERE dl_title LIKE '%".$_POST["wp_download_files"]["search"]."%'";
					else
						$_POST["wp_download_files"]["search"] = "";
				}
				$results = $wpdb->get_results("SELECT * FROM `$wpdb->downloads` ".$where_clause." ORDER BY dl_count DESC, dl_create_date DESC");
?>
		<div class="wrap">
			<div align="right"><a href="?page=<?php echo basename(__FILE__); ?>">Retour au panneau de configuration &raquo;</a></div>
			<h2>WP-Download - Liste des fichiers</h2>
			<p><?php echo count($results); ?> fichier(s) sur les <?php echo $nb_entries; ?> existants en base sont affich&eacute;s.</p>
			<form method="post" action="options-general.php?page=wp-download.php&amp;action=listfiles">
				<p>
					Filtrer la liste des fichiers :<br />
					<input type="text" id="search" name="wp_download_files[search]" value="<?php echo $_POST["wp_download_files"]["search"]; ?>" /> <input type="submit" name="wp_download_filter_submit" value="Filtrer" /> <input type="submit" name="wp_download_filter_submit" value="Liste compl&egrave;te" />
				</p>
			</form>
			<table class="widefat">
				<thead>
				<tr> 
					<th scope="col"><center>ID</center></th>
					<th scope="col">Titre</th>
					<th scope="col">Adresse</th>
					<th scope="col"><center>T&eacute;l&eacute;charg&eacute;</center></th>
					<th scope="col" colspan="2"><center>Actions</center></th>
				</tr>
				</thead>
			<?php
				if ($results) {
					foreach ($results as $result) {
						$class = ('alternate' != $class) ? 'alternate' : '';
						?>
						<tr class="<?php echo $class; ?>">
							<td align="center"><?php echo $result->dl_id; ?></td>
							<td><?php echo $result->dl_title; ?></td>
							<td><?php echo $result->dl_url; ?></td>
							<td align="center"><?php echo $result->dl_count; ?></td>
							<td><center><?php echo '<a href="?page='.basename(__FILE__).'&amp;action=update&amp;dlid='.$result->dl_id.'">Modifier</a>'; ?></center></td>
							<td><center><?php echo '<a href="?page='.basename(__FILE__).'&amp;action=delete&amp;dlid='.$result->dl_id.'" >Supprimer</a>'; ?></center></td>
						</tr>
						<?php
					}
				}
			?>
			</table>
		</div>
<?php
				break;
		}
	}
}

/* Ajout du button dans l'editeur WYSIWYG */
function WP_Download_MCE_Plugins($plugins) {    
	array_push($plugins, "-wpdownload", "bold");    
	return $plugins;
}
function WP_Download_MCE_Buttons($buttons) {
	array_push($buttons, "separator", "wpdownload");
	return $buttons;
}
function WP_Download_MCE_Load_Before_Init() {	
	$plugin_path = get_settings('siteurl') . '/wp-content/plugins/wp-download';
	echo "tinyMCE.loadPlugin('wpdownload', '" . $plugin_path . "/tinymce/');\n";
	return;
}
add_filter("mce_plugins", "WP_Download_MCE_Plugins", 5);
add_filter("mce_buttons", "WP_Download_MCE_Buttons", 5);
add_action("tinymce_before_init","WP_Download_MCE_Load_Before_Init");

/* Ajout du boutton pour les personnes n'utilisant par l'editeur WYSIWYG */
function WP_Download_Add_Quicktag(){
?>
<script type="text/javascript">
	<!--
		// code from http://roel.meurders.nl/wordpress-plugins/wp-addquicktag-plugin-for-adding-quicktags/
		if (wpaqToolbar = document.getElementById("ed_toolbar")){
			var wpaqNr, wpaqBut, wpaqStart, wpaqEnd;
			wpaqStart = '';
			wpaqEnd = '';
			wpaqNr = edButtons.length;
			edButtons[wpaqNr] = new edButton('ed_WPDownload','WPDownload','', '','',-1);
			var wpaqBut = wpaqToolbar.lastChild;
			while (wpaqBut.nodeType != 1){
				wpaqBut = wpaqBut.previousSibling;
			}
			wpaqBut = wpaqBut.cloneNode(true);
			wpaqToolbar.appendChild(wpaqBut);
			wpaqBut.value = 'WPDownload';
			wpaqBut.title = wpaqNr;
			wpaqBut.onclick = function () {
				WPDownloadOpenDialog();
			}
			wpaqBut.id = "ed_WPDownload";
		}
	//-->
</script>
<?php
}
add_action('admin_footer', 'WP_Download_Add_Quicktag');

/* Javascript pour l'ouverture de la boite de config */
function WP_Download_Admin_Head () {
	$plugin_path = get_settings('siteurl') . '/wp-content/plugins/wp-download';
	echo "<script type='text/javascript' src='" . $plugin_path . "/tinymce/dialog.js'></script>\n";
}
add_action('admin_head', 'WP_Download_Admin_Head');
?>