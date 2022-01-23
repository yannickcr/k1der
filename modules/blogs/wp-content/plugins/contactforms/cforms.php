<?php
/*
Plugin Name: cforms II
Plugin URI: http://www.deliciousdays.com/cforms-plugin
Description: cforms II offers unparalleled flexibility in deploying contact forms across your blog. Features include: comprehensive SPAM protection, Ajax support, Backup & Restore, Multi-Recipients, Role Manager support, Database tracking and many more.
Author: Oliver Seidel
Version: 5.3
Author URI: http://www.deliciousdays.com
*/

/*
Copyright 2006  Oliver Seidel   (email : oliver.seidel@deliciousdays.com)
/*

cforms II - v5.3
*) bugfix: admin HTML with non auto conf. TXT email would cause flawed HTML CC email
*) bugfix: fixed mailer error messsages for ajax (they would not show)
*) other:  improved/simplified UI
*) other:  lots of clean up and making UI around email messaging more obvious, hopefully
*/

load_plugin_textdomain('cforms');

### http://trac.wordpress.org/ticket/3002
$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;


### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

require		(dirname(__FILE__) . '/buttonsnap.php');
require_once(dirname(__FILE__) . '/editor.php');
include_once(dirname(__FILE__) . '/wp.php');


### SMPT sever configured?
$smtpsettings=explode('$#$',get_option('cforms_smtp'));

if ( ABSPATH=='' || WPINC=='' ) {
	define('ABSPATH', substr(dirname(__FILE__),0,strpos(dirname(__FILE__),'/wp-includes')) );
	define('WPINC', '/wp-includes/');
} 

if ( $smtpsettings[0]=='1' ) {
	if ( file_exists(ABSPATH . WPINC . '/class-phpmailer.php') ) {
		require_once ABSPATH . WPINC . '/class-phpmailer.php';
		require_once(dirname(__FILE__) . '/phpmailer/cforms_phpmailer.php');
	}
	else
		$smtpsettings[0]=='0';
}


### need this for captchas
session_start();

### download form settings or tracked form data ??
add_action('init', 'download_cforms');
function download_cforms() {

	if( strpos($_SERVER['HTTP_REFERER'], $plugindir.'/cforms') !== false ) {
		global $wpdb;

		$br="\n";
		$buffer='';
		if( isset($_REQUEST['savecformsdata']) ) {
		
			// current form
			$noDISP = '1'; $no='';
			if( $_REQUEST['noSub']<>'1' )
				$noDISP = $no = $_REQUEST['noSub'];
				
			$buffer .= 'cf:'.get_option('cforms'.$no.'_count_fields') . $br;
			$buffer .= 'ff:';
			for ( $i=1; $i<=get_option('cforms'.$no.'_count_fields'); $i++)  //now delete all fields from last form
			$buffer .= get_option('cforms'.$no.'_count_field_'.$i)."+++";
			
			$buffer .= $br;
			$buffer .= 'rq:'.get_option('cforms'.$no.'_required') . $br;
			$buffer .= 'er:'.get_option('cforms'.$no.'_emailrequired') . $br;
			
			$buffer .= 'ac:'.get_option('cforms'.$no.'_confirm') . $br;
			$buffer .= 'jx:'.get_option('cforms'.$no.'_ajax') . $br;
			$buffer .= 'fn:'.get_option('cforms'.$no.'_fname') . $br;
			$buffer .= 'cs:'.get_option('cforms'.$no.'_csubject') . $br;
			$buffer .= 'cm:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_cmsg')) . $br;
			$buffer .= 'em:'.get_option('cforms'.$no.'_email') . $br;
			
			$buffer .= 'sj:'.get_option('cforms'.$no.'_subject') . $br;
			$buffer .= 'su:'.get_option('cforms'.$no.'_submit_text') . $br;
			$buffer .= 'sc:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_success')) . $br;
			$buffer .= 'fl:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_failure')) . $br;
			$buffer .= 'wo:'.get_option('cforms'.$no.'_working') . $br;
			$buffer .= 'pp:'.get_option('cforms'.$no.'_popup') . $br;
			$buffer .= 'sp:'.get_option('cforms'.$no.'_showpos') . $br;
			$buffer .= 'rd:'.get_option('cforms'.$no.'_redirect') . $br;
			$buffer .= 'rp:'.get_option('cforms'.$no.'_redirect_page') . $br;
			$buffer .= 'hd:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_header')) . $br;
			$buffer .= 'pc:'.get_option('cforms'.$no.'_space') . $br;
			$buffer .= 'at:'.get_option('cforms'.$no.'_noattachments') . $br;
			$buffer .= 'ud:'.get_option('cforms'.$no.'_upload_dir') . $br;
			$buffer .= 'ue:'.get_option('cforms'.$no.'_upload_ext') . $br;
			$buffer .= 'us:'.get_option('cforms'.$no.'_upload_size') . $br;
			$buffer .= 'ar:'.get_option('cforms'.$no.'_action') . $br;
			$buffer .= 'ap:'.get_option('cforms'.$no.'_action_page') . $br;
			$buffer .= 'bc:'.get_option('cforms'.$no.'_bcc') . $br;
			$buffer .= 'ch:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_cmsg_html')) . $br;
			$buffer .= 'hh:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_header_html')) . $br;
			$buffer .= 'fd:'.get_option('cforms'.$no.'_formdata') . $br;
			$buffer .= 'tr:'.get_option('cforms'.$no.'_tracking') . $br;
			$buffer .= 'fm:'.get_option('cforms'.$no.'_fromemail') . $br;
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment; filename=\"formconfig.txt\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " .(string)(strlen($buffer)) );
			print $buffer;
			exit();

		} // form settings
		else if( isset($_REQUEST['downloadselectedcforms']) && isset($_POST['entries']) ) {

			$sub_id = implode(',', $_POST['entries']);
			$sql = "SELECT *, form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id in ($sub_id) AND sub_id=id ORDER BY sub_id DESC";
			$entries = $wpdb->get_results($sql);
		
			$sub_id='';
			foreach ($entries as $entry){
		
				if( $sub_id<>$entry->sub_id ){

					if ( $sub_id<>'' ) 
						$buffer = substr($buffer,0,-1) . $br;
						
					$sub_id = $entry->sub_id;

					$format = ($_REQUEST['downloadformat']=="csv")?",":"\t";
					
					$buffer .= '"Form: ' . get_option('cforms'.$entry->form_id.'_fname'). '"'. $format .'"'. $entry->sub_date .'"' . $format;
				}

				$buffer .= '"' . str_replace('"','""', utf8_decode(stripslashes($entry->field_val))) . '"' . $format;
		
			}
	
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: text/download");
			header("Content-Type: text/csv");
			header("Content-Disposition: attachment; filename=\"formdata." . $_REQUEST['downloadformat'] . "\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " .(string)(strlen($buffer)) );
			print $buffer;	
			exit();
		} // tracked form submissions

	} // on a cforms page?
}



// do a couple of things necessary as part of plugin activation
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {

	require_once(dirname(__FILE__) . '/lib_activate.php');

}



// Can't use WP's function here, so lets use our own
if ( !function_exists('cf_getip') ) :
function cf_getip()
{
	if (isset($_SERVER))
	{
 		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
 		elseif (isset($_SERVER["HTTP_CLIENT_IP"]))	$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
 		else										$ip_addr = $_SERVER["REMOTE_ADDR"];
	}
	else
	{
 		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) 	$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
 		elseif ( getenv( 'HTTP_CLIENT_IP' ) )	  	$ip_addr = getenv( 'HTTP_CLIENT_IP' );
 		else										$ip_addr = getenv( 'REMOTE_ADDR' );
	}
	return $ip_addr;
}
endif;



//
// replace standard & custom variables in message/subject text
//

function check_default_vars($m,$no) {

		global $subID;
		
		// special fields: {Form Name}, {Date}, {Page}, {IP}
		$page = substr( $_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'?')-1);
		$page = (trim($page)=='')?'/':trim($page);
		$date = mysql2date(get_option('date_format'), current_time('mysql')) . ' @ ' . gmdate(get_option('time_format'), current_time('timestamp'));

		$m 	= str_replace( __('{Form Name}', 'cforms'), get_option('cforms'.$no.'_fname'), $m );
		$m 	= str_replace( __('{Page}', 'cforms'), $page, $m );
		$m 	= str_replace( __('{Date}', 'cforms'), $date, $m );
		$m 	= str_replace( __('{IP}', 'cforms'), cf_getip(), $m );
		$m 	= preg_replace( "/\.(\r\n|$)/", "\r\n", $m );			
		
		
		if  ( get_option('cforms_database') && $subID<>'' )
			$m 	= str_replace( __('{ID}', 'cforms'), $subID, $m );
							 
		return $m;
}

function check_cust_vars($m,$t) {

	preg_match_all('/\\{([^\\{]+)\\}/',$m,$findall);
	
	if ( count($findall[1]) > 0 ) {
		foreach ( $findall[1] as $fvar ) {
			$m = str_replace('{'.$fvar.'}', $t[$fvar], $m);
		}
	}
	return $m;
	
}


// Common HTML message information 
$styles  = "<HEAD><style><!--\n";
$styles .= ".fs-td { font-weight:bold; font-size:1.2em; border-bottom:1px solid #888; padding-top:15px; }\n";
$styles .= ".data-td { font-weight:bold; padding-right:20px; }\n";
$styles .= "--></style></HEAD>\n";	


//
// ajax submission of form
//
function cforms_submitcomment($content) {

	global $wpdb, $subID, $styles, $smtpsettings;

	$segments = explode('$#$', $content);
	$params = array();


	for($i = 1; $i <= sizeof($segments); $i++)
		$params['field_' . $i] = $segments[$i];

	// fix reference to first form
	if ( $segments[0]=='1' ) $no=''; else $no = $segments[0];

	// init variables
	$formdata = '';
	$htmlformdata = '<br />';

	$track = array(); 
 	$to_one = "-1";
  	$ccme = false;
	$field_email = '';
	$off = 0;

	//space for pre formatted text layout
	$customspace = (int)(get_option('cforms'.$no.'_space')>0)?get_option('cforms'.$no.'_space'):30;


	for($i = 1; $i <= sizeof($params)-1; $i++) {

			$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));

			// filter non input fields
			while ( $field_stat[1] == 'fieldsetstart' || $field_stat[1] == 'fieldsetend' || $field_stat[1] == 'textonly' ) {
																				
					if ( $field_stat[1] <> 'textonly' ){ // include and make only fieldsets pretty!

							//just for email looks
							$space='-'; 
							$n = ((($customspace*2)+2) - strlen($field_stat[0])) / 2;
							$n = ($n<0)?0:$n;
							if ( strlen($field_stat[0]) < (($customspace*2)-2) )
								$space = str_repeat("-", $n );
								
							$formdata .= substr("\n$space$field_stat[0]$space",0,($customspace*2)) . "\n\n";
							$htmlformdata .= '<tr bgcolor=3Dwhite><td align=3Dcenter colspan=3D2 class=fs-td>' . $field_stat[0] . '</td></tr>';

					}
					
		   			//get next in line...
					$off++;
					$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
					
					if( $field_stat[1] == '')
						break 2; // all fields searched, break both while & for

			}

			$field_name = $field_stat[0];
			$field_type = $field_stat[1];

			// strip out default value
			if ( ($pos=strpos($field_name,'|')) )
			    $field_name = substr($field_name,0,$pos);

			// lets find an email field ("Is Email") and that's not empty!
			if ( $field_email == '' && $field_stat[3]=='1') {
					$field_email = $params ['field_' . $i];
			}

			// special case: select & radio
			if ( $field_type == "multiselectbox" || $field_type == "selectbox" || $field_type == "radiobuttons" || $field_type == "checkboxgroup") {
			  $field_name = explode('#',$field_name);
			  $field_name = $field_name[0];
			}

			// special case: check box
			if ( $field_type == "checkbox" || $field_type == "ccbox" ) {
			  $field_name = explode('#',$field_name);
			  $field_name = ($field_name[1]=='')?$field_name[0]:$field_name[1];
				// if ccbox & checked
			  if ($field_type == "ccbox" && $params ['field_' . $i]=="X" )
			      $ccme = true;
			}

			if ( $field_type == "emailtobox" ){  			//special case where the value needs to bet get from the DB!

				$field_name = explode('#',$field_stat[0]);  //can't use field_name, since '|' check earlier
				$to_one = $params ['field_' . $i];

				$offset = (strpos($field_name[1],'|')===false) ? 1 : 2; // names come usually right after the label


				$value = $field_name[(int)$to_one+$offset];  // values start from 0 or after!
				$field_name = $field_name[0];

	 		}
			else {
			    if ( strtoupper(get_option('blog_charset')) <> 'UTF-8' && function_exists('mb_convert_encoding'))
        		    $value = mb_convert_encoding(utf8_decode($params ['field_' . $i]), get_option('blog_charset'));   // convert back and forth to support also other than UTF8 charsets
                else
                    $value = $params ['field_' . $i];
            }

			// Q&A verification
			if ( $field_type == "verification" ) 
					$field_name = __('Q&A','cforms');

			
			//for db tracking
			$track[trim($field_name)] = $value;

			//for all equal except textareas!
			$htmlvalue = $value;
			$htmlfield_name = $field_name;

			// just for looks: break for textarea
 			if ( $field_type == "textarea" ) {
					$field_name = "\n" . $field_name;
					$value = "\n" . $value . "\n";
					$htmlvalue = str_replace("\n","<br/>\n",$value);
			}

			// just for looks:rest
		  	$space='';
			if ( strlen(stripslashes($field_name)) < $customspace )   // don't count ->\"  sometimes adds more spaces?!?
				  $space = str_repeat(" ",$customspace-strlen(stripslashes($field_name)));

			// create formdata block for email
			if ( $field_stat[1] <> 'verification' && $field_stat[1] <> 'captcha' ) {
				$formdata .= $field_name . ': '. $space . $value . "\n";
				$htmlformdata .= '<tr><td valign=3Dtop class=3Ddata-td>' . $htmlfield_name . '</td><td>' . $htmlvalue . '</td></tr>';
			}
					
	} // for

	// assemble html formdata
	$htmlformdata = '<table width=3D"100%" cellpadding=3D2 bgcolor=3D#fafafa>' . $htmlformdata . '</table>';


	
	//
	//reply to all email recipients
	//
	$replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes(get_option('cforms'.$no.'_email')) );

	// multiple recipients? and to whom is the email sent? to_one = picked recip.
	if ( $to_one <> "-1" ) {
			$all_to_email = explode(',', $replyto);
			$replyto = $to = $all_to_email[ $to_one ];
	} else
			$to = $replyto;


	//
	// allow the user to use form data for other apps
	//
	$trackf['id'] = $no;
	$trackf['data'] = $track;
	do_action('cforms_data',$trackf);


	//
	// FIRST write into the cforms tables!
	//
	if ( get_option('cforms_database') == '1'  ) {

		$wpdb->query("INSERT INTO $wpdb->cformssubmissions (form_id,email,ip) VALUES ".
					 "('" . $no . "','" . $field_email . "', '" . cf_getip() . "');");

		$subID = $wpdb->get_row("select LAST_INSERT_ID() as number from $wpdb->cformsdata;");
   		$subID = ($subID->number=='')?'1':$subID->number;

		$sql = "INSERT INTO $wpdb->cformsdata (sub_id,field_name,field_val) VALUES " .
					 "('$subID','page','$page'),";

		foreach ( array_keys($track) as $key )
			$sql .= "('$subID','".addslashes($key)."','".addslashes($track[$key])."'),";

		$wpdb->query(substr($sql,0,-1));		
					 
	}


	//
	// ready to send email
	// email header 
	//

	$html_show = ( substr(get_option('cforms'.$no.'_formdata'),2,1)=='1' )?true:false;
	
	$fmessage='';
	
	$eol = "\n";
	if ( ($frommail=stripslashes(get_option('cforms'.$no.'_fromemail')))=='' )
		$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';	
	
	$headers = "From: ". $frommail . $eol;
	$headers.= "Reply-To: " . $field_email . $eol;

	if ( ($tempBcc = stripslashes(get_option('cforms'.$no.'_bcc'))) != "")
	    $headers.= "Bcc: " . $tempBcc . $eol;

	$headers.= "MIME-Version: 1.0"  .$eol;
	if ($html_show) {
		$headers.= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_main_message\"";
		$fmessage = "This is a multi-part message in MIME format."  . $eol;
		$fmessage .= "------MIME_BOUNDRY_main_message"  . $eol;
		$fmessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;
		$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;
	}
	else
		$headers.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"";
	
	// prep message text, replace variables
	$message = get_option('cforms'.$no.'_header');
	$message = check_default_vars($message,$no);
	$message = check_cust_vars($message,$track);

	// text text
	$fmessage .= stripslashes($message) . $eol;
	
	// need to add form data summary or is all in the header anyway?
	if(substr(get_option('cforms'.$no.'_formdata'),0,1)=='1')
		$fmessage .= $eol . $formdata . $eol;


	// HTML text
	if ($html_show) {
	
		// actual user message
		$htmlmessage = get_option('cforms'.$no.'_header_html');					
		$htmlmessage = check_default_vars($htmlmessage,$no);
		$htmlmessage = check_cust_vars($htmlmessage,$track);

		$fmessage .= "------MIME_BOUNDRY_main_message"  . $eol;
		$fmessage .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
		$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;;

		$fmessage .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">"  . $eol;
		$fmessage .= "<HTML>" . $eol;
		$fmessage .= $styles;
		$fmessage .= "<BODY>" . $eol;

		$fmessage .= stripslashes($htmlmessage);

		// need to add form data summary or is all in the header anyway?
		if(substr(get_option('cforms'.$no.'_formdata'),1,1)=='1')
			$fmessage .= $eol . $htmlformdata;

		$fmessage .= "</BODY></HTML>"  . $eol . $eol;

	}


	//either use configured subject or user determined
	$vsubject = get_option('cforms'.$no.'_subject');
	$vsubject = check_default_vars($vsubject,$no);
	$vsubject = check_cust_vars($vsubject,$track);


	// SMTP server or native PHP mail() ?
	if ( $smtpsettings[0]=='1' )
		$sentadmin = cforms_phpmailer( $no, $frommail, $field_email, $to, $vsubject, $message, $formdata, $htmlmessage, $htmlformdata );
	else
		$sentadmin = @mail($to, stripslashes($vsubject), stripslashes($fmessage), $headers);	

	if( $sentadmin==1 )
	{
		  // send copy or notification?
	    if ( (get_option('cforms'.$no.'_confirm')=='1' && $field_email<>'') || $ccme )  // not if no email & already CC'ed
	    {

					if ( ($frommail=stripslashes(get_option('cforms'.$no.'_fromemail')))=='' )
						$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';	
					
					// HTML message part?
					$html_show_ac = ( substr(get_option('cforms'.$no.'_formdata'),3,1)=='1' )?true:false;

					$automessage = '';

					$headers2 = "From: ". $frommail . $eol;
					$headers2.= "Reply-To: " . $replyto . $eol;
					$headers2.= "MIME-Version: 1.0"  .$eol;
					if( $html_show_ac || ($html_show && $ccme) ){
						$headers2.= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_main_message\"";
						$automessage = "This is a multi-part message in MIME format."  . $eol;
						$automessage .= "------MIME_BOUNDRY_main_message"  . $eol;
						$automessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;
						$automessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;
					}
					else
						$headers2.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"";
					

					// actual user message
					$cmsg = get_option('cforms'.$no.'_cmsg');					
					$cmsg = check_default_vars($cmsg,$no);
					$cmsg = check_cust_vars($cmsg,$track);
					
					// text text
					$automessage .= $cmsg  . $eol;

					// HTML text
					if ( $html_show_ac ) {
					
						// actual user message
						$cmsghtml = get_option('cforms'.$no.'_cmsg_html');					
						$cmsghtml = check_default_vars($cmsghtml,$no);
						$cmsghtml = check_cust_vars($cmsghtml,$track);

						$automessage .= "------MIME_BOUNDRY_main_message"  . $eol;
						$automessage .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
						$automessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;;
	
						$automessage .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">"  . $eol;
						$automessage .= "<HTML><BODY>"  . $eol;
						$automessage .= $cmsghtml;
						$automessage .= "</BODY></HTML>"  . $eol . $eol;
					}

					// replace variables
				    $subject2 = get_option('cforms'.$no.'_csubject');
					$subject2 = check_default_vars($subject2,$no);
					$subject2 = check_cust_vars($subject2,$track);
					
					// different cc & ac subjects?
					$t=explode('$#$',$subject2);
					$t[1] = ($t[1]<>'') ? $t[1] : $t[0];


					// email tracking via 3rd party?
					$field_email = (get_option('cforms'.$no.'_tracking')<>'')?$field_email.get_option('cforms'.$no.'_tracking'):$field_email;

					if ( $ccme ) {
						if ( $smtpsettings[0]=='1' )
							$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[1]), $message, $formdata, $htmlmessage, $htmlformdata );
						else
							$sent = @mail($field_email, stripslashes($t[1]), stripslashes($fmessage), $headers2); //takes $message!!
					}
					else {
						if ( $smtpsettings[0]=='1' )
							$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[0]) , $cmsg , '', $cmsghtml, '' );
						else
							$sent = @mail($field_email, stripslashes($t[0]), stripslashes($automessage), $headers2);
					}
					
		  		if( $sent<>'1' ) {
					$err = __('Error occured while sending the auto confirmation message: ','cforms') . ($smtpsettings[0]?" ($sent)":'');
				    $pre = $segments[0].'*$#'.substr(get_option('cforms'.$no.'_popup'),0,1);
				    return $pre . $err .'|'.'<root><text>'.$err.'</text></root>';
		  			
		  		}
	    } // cc

		// redirect to a different page on suceess?
		if ( get_option('cforms'.$no.'_redirect') ) {
			return get_option('cforms'.$no.'_redirect_page');
		}

		// return success msg
	    $pre = $segments[0].'*$#'.substr(get_option('cforms'.$no.'_popup'),0,1);
	    return $pre . preg_replace ( '|\r\n|', '<br />', stripslashes(get_option('cforms'.$no.'_success'))).'|'.
						'<root>'.preg_replace ( '/(.*)(\r\n|$)/', '<text>\1</text>', stripslashes(get_option('cforms'.$no.'_success'))).'</root>';

	} // no admin mail sent!

	else {

		// return error msg
		$err = __('Error occured while sending the message: ','cforms') . ($smtpsettings[0]?'<br/>'.$sentadmin:'');
	    $pre = $segments[0].'*$#'.substr(get_option('cforms'.$no.'_popup'),0,1);
	    return $pre . $err .'|'.'<root>'.preg_replace ( '/(.*)(<br\/>|$)/', '<text>\1</text>', $err).'</root>!!!';
	}

} //function


//
// sajax stuff
//

if (!isset($SAJAX_INCLUDED)) {

	$GLOBALS['sajax_version'] = '0.12';	
	$GLOBALS['sajax_debug_mode'] = 0;
	$GLOBALS['sajax_export_list'] = array();
	$GLOBALS['sajax_request_type'] = 'POST';
	$GLOBALS['sajax_remote_uri'] = '';
	$GLOBALS['sajax_failure_redirect'] = '';
	 
	function sajax_init() {
	}
	
	function sajax_get_my_uri() {
		return $_SERVER["REQUEST_URI"];
	}
	$sajax_remote_uri = sajax_get_my_uri();
	
	function sajax_get_js_repr($value) {
		$type = gettype($value);
		
		if ($type == "boolean") {
			return ($value) ? "Boolean(true)" : "Boolean(false)";
		} 
		elseif ($type == "integer") {
			return "parseInt($value)";
		} 
		elseif ($type == "double") {
			return "parseFloat($value)";
		} 
		elseif ($type == "array" || $type == "object" ) {
			$s = "{ ";
			if ($type == "object") {
				$value = get_object_vars($value);
			} 
			foreach ($value as $k=>$v) {
				$esc_key = sajax_esc($k);
				if (is_numeric($k)) 
					$s .= "$k: " . sajax_get_js_repr($v) . ", ";
				else
					$s .= "\"$esc_key\": " . sajax_get_js_repr($v) . ", ";
			}
			if (count($value))
				$s = substr($s, 0, -2);
			return $s . " }";
		} 
		else {
			$esc_val = sajax_esc($value);
			$s = "'$esc_val'";
			return $s;
		}
	}

	function sajax_handle_client_request() {
		global $sajax_export_list;
		
		$mode = "";
		
		if (! empty($_GET["rs"])) 
			$mode = "get";
		
		if (!empty($_POST["rs"]))
			$mode = "post";
			
		if (empty($mode)) 
			return;

		$target = "";
		
		if ($mode == "get") {
			// Bust cache in the head
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			// always modified
			header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
			header ("Pragma: no-cache");                          // HTTP/1.0
			$func_name = $_GET["rs"];
			if (! empty($_GET["rsargs"])) 
				$args = $_GET["rsargs"];
			else
				$args = array();
		}
		else {
			$func_name = $_POST["rs"];
			if (! empty($_POST["rsargs"])) 
				$args = $_POST["rsargs"];
			else
				$args = array();
		}
		
		if (! in_array($func_name, $sajax_export_list))
			echo "-:$func_name not callable";
		else {
			echo "+:";
			$result = call_user_func_array($func_name, $args);
			echo "var res = " . trim(sajax_get_js_repr($result)) . "; res;";
		}
		exit;
	}
	
	// javascript escape a value
	function sajax_esc($val)
	{
		$val = str_replace("\\", "\\\\", $val);
		$val = str_replace("\r", "\\r", $val);
		$val = str_replace("\n", "\\n", $val);
		$val = str_replace("'", "\\'", $val);
		return str_replace('"', '\\"', $val);
	}

	function sajax_get_one_stub($func_name) {
		ob_start();	
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function sajax_show_one_stub($func_name) {
		echo sajax_get_one_stub($func_name);
	}
	
	function sajax_export() {
		global $sajax_export_list;
		
		$n = func_num_args();
		for ($i = 0; $i < $n; $i++) {
			$sajax_export_list[] = func_get_arg($i);
		}
	}
	
	$sajax_js_has_been_shown = 0;
	function sajax_get_javascript()
	{
		global $sajax_js_has_been_shown;
		global $sajax_export_list;
		
		$html = "";
		if (! $sajax_js_has_been_shown) {
			$html .= sajax_get_common_js();
			$sajax_js_has_been_shown = 1;
		}
		foreach ($sajax_export_list as $func) {
			$html .= sajax_get_one_stub($func);
		}
		return $html;
	}
	
	$SAJAX_INCLUDED = 1;
}

// $sajax_debug_mode = 1;
sajax_init();
sajax_export("cforms_submitcomment");
sajax_handle_client_request();	



//
// main function
//

function cforms($args = '',$no = '') {

	global $smtpsettings, $styles, $subID, $cforms_root, $wpdb;

	parse_str($args, $r);	// parse all args, and if not specified, initialize to defaults

	//custom fields support
	if ( !(strpos($no,'+') === false) ) {
	    $no = substr($no,0,-1);
		$customfields = build_fstat($args);
		$field_count = count($customfields);
		$custom=true; 
	} else {
		$custom=false;
		$field_count = get_option('cforms'.$no.'_count_fields');
	}
	
	$content = '';
	$indent .= "\t";


	$err=0;
	$filefield=0;   // for multiple file upload fields
	
	$validations = array();
	$all_valid = 1;
	$off=0;


	if( isset($_POST['sendbutton'.$no]) ) {  /* alternative sending: both events r ok!  */

		//
		// VALIDATE all fields 
		//
		for($i = 1; $i <= $field_count; $i++) {
		
					if ( !$custom ) 
						$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
					else
						$field_stat = explode('$#$', $customfields[((int)$i+(int)$off) - 1]);

					// filter non input fields
					while ( $field_stat[1] == 'fieldsetstart' || $field_stat[1] == 'fieldsetend' || $field_stat[1] == 'textonly' ) {
							$off++;

							if ( !$custom ) 
                                $field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
                            else
                                $field_stat = explode('$#$', $customfields[((int)$i+(int)$off) - 1]);
                                
							if( $field_stat[1] == '')
									break 2; // all fields searched, break both while & for
					}

				$field_name = $field_stat[0];
				$field_type = $field_stat[1];
				$field_required = $field_stat[2];
				$field_emailcheck = $field_stat[3];
				
				
				if( $field_emailcheck ) {  // email field
				
						$validations[$i+$off] = cforms_is_email( $_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)]);
						if ( !$validations[$i+$off] && $err==0 ) $err=1;
					
				}
				else if( $field_required ) { // just required

						if( $field_type=="textfield" || $field_type=="textarea" || $field_type=="vsubject" ){

									// textfields empty ?
									$validations[$i+$off] = !empty($_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)]);

									// regexp set for textfields?
									$obj = explode('|', $field_name, 3);
				  				if ( $obj[2] <> '') {
				  				
											$reg_exp = str_replace('/','\/',stripslashes($obj[2]) );
											if( !preg_match('/'.$reg_exp.'/',$_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)]) )
											    $validations[$i+$off] = false;
									}

						}else if( $field_type=="checkbox" ) {

									// textfields empty ?
									$validations[$i+$off] = !empty($_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)]);

						}else if( $field_type=="selectbox" || $field_type=="emailtobox" ) {

									// textfields empty ?
									$validations[$i+$off] = !($_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)] == '-' );

						}else if( $field_type=="multiselectbox" ) {
						
									// how many multiple selects ?
                                    $all_options = $_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)];
									if ( count($all_options) == 0 )                               
        									$validations[$i+$off] = false;
                                    else						    
        									$validations[$i+$off] = true;

						}else if( $field_type=="upload" ) {  // prelim upload check

									$validations[$i+$off] = !($_FILES['cf_uploadfile'.$no][name][$filefield++]=='');
									if ( !$validations[$i+$off] && $err==0 )
											{ $err=3; $fileerr = get_option('cforms_upload_err2'); }
						}

						if ( !$validations[$i+$off] && $err==0 ) $err=1;
					
				}
				else if( $field_type == 'verification' ){  // visitor verification code
				
	            		$validations[$i+$off] = 1;
						if ( $_POST['cforms_a'.$no] <> md5(rawurlencode(strtolower($_POST['cforms_q'.$no]))) ) {
								$validations[$i+$off] = 0;
								$err = !($err)?2:$err;
								}
								
				}
				else if( $field_type == 'captcha' ){  // captcha verification
				
	            		$validations[$i+$off] = 1;
						if ( $_POST['cforms_cap'.$no] <> md5(strtolower($_POST['cforms_captcha'.$no])) ) {
								$validations[$i+$off] = 0;
								$err = !($err)?2:$err;
								}
						
				}
				else
					$validations[$i+$off] = 1;

				$all_valid = $all_valid && $validations[$i+$off];

			}


		//
		// have to upload a file?
		//
		$uploadedfile='';
		$filefield=0;   // for multiple file upload fields

		if( isset($_FILES['cf_uploadfile'.$no]) && $all_valid){

			foreach( $_FILES['cf_uploadfile'.$no][name] as $value ) {
				
				if(!empty($value)){   // this will check if any blank field is entered

					  	$file = $_FILES['cf_uploadfile'.$no];
						
						$fileerr = '';
						// A successful upload will pass this test. It makes no sense to override this one.
						if ( $file['error'][$filefield] > 0 )
								$fileerr = get_option('cforms_upload_err1');
								
						// A successful upload will pass this test. It makes no sense to override this one.
						$fileext[$filefield] = substr($value,strrpos($value, '.')+1,strlen($value));
						$allextensions = explode(',' ,  preg_replace('/\s/', '', get_option('cforms'.$no.'_upload_ext')) );
						
						if ( get_option('cforms'.$no.'_upload_ext')<>'' && !in_array($fileext[$filefield], $allextensions) )
								$fileerr = get_option('cforms_upload_err5');
		
						// A non-empty file will pass this test.
						if ( !( $file['size'][$filefield] > 0 ) )
								$fileerr = get_option('cforms_upload_err2');
		
						// A non-empty file will pass this test.
						if ( $file['size'][$filefield] >= (int)get_option('cforms'.$no.'_upload_size') * 1024 )
								$fileerr = get_option('cforms_upload_err3');
		
		
						// A properly uploaded file will pass this test. There should be no reason to override this one.
						if (! @ is_uploaded_file( $file['tmp_name'][$filefield] ) )
								$fileerr = get_option('cforms_upload_err4');
				
						if ( $fileerr <> '' ){
		
								$err = 3;
								$all_valid = false;
		
						} else {
		
								// cool, got the file!
		
						  		$uploadedfile = file($file['tmp_name'][$filefield]);
					
					            $fp = fopen($file['tmp_name'][$filefield], "rb"); //Open it
					            $fdata = fread($fp, filesize($file['tmp_name'][$filefield])); //Read it
					            $filedata[$filefield] = chunk_split(base64_encode($fdata)); //Chunk it up and encode it as base64 so it can emailed
					            fclose($fp);
			
						} // file uploaded

		        } // if !empty
				$filefield++;
		        
		    } // while all file

		} // no file upload triggered


	} // if isset sendbutton


	//
	// what kind of error message?
	//
	switch($err){
		case 0:
				break;
		case 1:
				$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), get_option('cforms'.$no.'_failure') );
				break;
		case 2:
				$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), get_option('cforms_codeerr') );
				break;
		case 3:
				$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), $fileerr);
				break;
	}

	$usermessage_class = $all_valid?'success':'failure';
	
	
	//
	// all valid? get ready to send
	//
	if( (isset($_POST['sendbutton'.$no]) ) && $all_valid ) {

			$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes(get_option('cforms'.$no.'_success')) );


			$formdata = '';
			$htmlformdata = '<br />';

			$track = array(); 
	  		$to_one = "-1";
			$ccme = false;
			$field_email = '';

			$filefield=0;

			$customspace = (int)(get_option('cforms'.$no.'_space')>0)?get_option('cforms'.$no.'_space'):30;

			for($i = 1; $i <= $field_count; $i++) {

				if ( !$custom ) 
					$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . $i ));
				else
					$field_stat = explode('$#$', $customfields[$i-1]);


				// filter non input fields
				while ( $field_stat[1] == 'fieldsetstart' || $field_stat[1] == 'fieldsetend' || $field_stat[1] == 'textonly' ) {

						if ( $field_stat[1] <> 'textonly' ){ // include and make only fieldsets pretty!
	
							//just for email looks
							$space='-'; 
							$n = ((($customspace*2)+2) - strlen($field_stat[0])) / 2;
							$n = ($n<0)?0:$n;							
							if ( strlen($field_stat[0]) < (($customspace*2)-2) )
								$space = str_repeat("-", $n );
								
							$formdata .= substr("\n$space$field_stat[0]$space",0,($customspace*2)) . "\n\n";
							$htmlformdata .= '<tr bgcolor=3Dwhite><td align=3Dcenter valign=3Dtop colspan=3D2 class=3Dfs-td>' . $field_stat[0] . '</td></tr>';
							
						}
						
						//get next in line...
						$i++;

						if ( !$custom ) 
      						$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . $i ));
						else
           					$field_stat = explode('$#$', $customfields[$i-1]);

						if( $field_stat[1] == '')
								break 2; // all fields searched, break both while & for
				}

				$field_name = $field_stat[0];
      			$field_type = $field_stat[1];

				// strip out default value
				if ( ($pos=strpos($field_name,'|')) )
				    $field_name = substr($field_name,0,$pos);


				// find email address
				if ( $field_email == '' && $field_stat[3]=='1')
						$field_email = $_POST['cf'.$no.'_field_' . $i];


				// special case: select box & radio box
				if ( $field_type == "checkboxgroup" || $field_type == "multiselectbox" || $field_type == "selectbox" || $field_type == "radiobuttons" ) { //only needed for field name
				  $field_name = explode('#',$field_name);
				  $field_name = $field_name[0];
				}


				// special case: check box
				if ( $field_type == "checkbox" || $field_type == "ccbox" ) {
				  $field_name = explode('#',$field_name);
				  $field_name = ($field_name[1]=='')?$field_name[0]:$field_name[1];
					// if ccbox
				  if ($field_type == "ccbox" && isset($_POST['cf'.$no.'_field_' . $i]) )
				      $ccme = true;
				}


			if ( $field_type == "emailtobox" ){  				//special case where the value needs to bet get from the DB!

                $field_name = explode('#',$field_stat[0]);  //can't use field_name, since '|' check earlier
                $to_one = $_POST['cf'.$no.'_field_' . $i];
                
                $offset = (strpos($field_name[1],'|')===false) ? 1 : 2; // names come usually right after the label
                
                $value 	= $field_name[(int)$to_one+$offset];  // values start from 0 or after!
                $field_name = $field_name[0];
	 		}
	 		else if ( $field_type == "upload" ){
	 		
	 			//$fsize = $file['size'][$filefield]/1000;
	 			$value = $file['name'][$filefield++];

	 		}	 		
	 		else if ( $field_type == "multiselectbox" || $field_type == "checkboxgroup"){
	 		    
                $all_options = $_POST['cf'.$no.'_field_' . $i];
	 		    if ( count($all_options) > 0)
                    $value = implode(',', $all_options);
                else
                    $value = '';
                    
            }	            			
			else if ( $field_stat[1] == 'captcha' ) // captcha response

				$value = $_POST['cforms_captcha'.$no];
				
			else if ( $field_stat[1] == 'verification' ) { // verification Q&A response

				$value = $_POST['cforms_q'.$no]; // add Q&A label!
				$field_name = __('Q&A','cforms');
			}				
			else
				$value = $_POST['cf'.$no.'_field_' . $i];       // covers all other fields' values


			//check boxes
			if ( $field_type == "checkbox" || $field_type == "ccbox" ) {
			
					if ( isset($_POST['cf'.$no.'_field_' . $i]) )
						$value = 'X';
					else
						$value = '-';

			} else if ( $field_type == "radiobuttons" ) {

					if ( ! isset($_POST['cf'.$no.'_field_' . $i]) )
						$value = '-';
			} 


			//for db tracking
			$trackname = trim( ($field_type == "upload")?$field_name.'[*]':$field_name ); 
			$track[$trackname] = $value;

			//for all equal except textareas!
			$htmlvalue = $value;
			$htmlfield_name = $field_name;
			
			// just for looks: break for textarea
 			if ( $field_type == "textarea" ) {
					$field_name = "\n" . $field_name;
					$value = "\n" . $value . "\n";
					$htmlvalue = str_replace("\n","<br/>\n",$value);
			}


			// for looks
			$space='';
			if ( strlen(stripslashes($field_name)) < $customspace )
				  $space = str_repeat(" ", $customspace - strlen(stripslashes($field_name)));

			$field_name .= ': ' . $space;


			if ( $field_stat[1] <> 'verification' && $field_stat[1] <> 'captcha' ){
					$formdata .= $field_name . $value . "\n";
					$htmlformdata .= '<tr><td valign=3Dtop class=3Ddata-td>' . $htmlfield_name . '</td><td>' . $htmlvalue . '</td></tr>';
			}

		} //for all fields

		// assemble html formdata
		$htmlformdata = '<table width=3D\'100%\' cellpadding=3D2px bgcolor=3D#fafafa>' . $htmlformdata . '</table>';

		//
		// allow the user to use form data for other apps
		//
		$trackf['id'] = $no;
		$trackf['data'] = $track;
		do_action('cforms_data',$trackf);


		//
		// now replace the left over {xyz} variables with the input data
		//
		$message	= get_option('cforms'.$no.'_header');
		$message	= check_default_vars($message,$no);
		$message	= check_cust_vars($message,$track);


		// FIRST into the database is required!
		if ( get_option('cforms_database') == '1'  ) {
	
			$wpdb->query("INSERT INTO $wpdb->cformssubmissions (form_id,email,ip) VALUES ".
						 "('" . $no . "', '" . $field_email . "', '" . cf_getip() . "');");
	
    		$subID = $wpdb->get_row("select LAST_INSERT_ID() as number from $wpdb->cformsdata;");
    		$subID = ($subID->number=='')?'1':$subID->number;

			$sql = "INSERT INTO $wpdb->cformsdata (sub_id,field_name,field_val) VALUES " .
						 "('$subID','page','$page'),";
						 
			foreach ( array_keys($track) as $key )
				$sql .= "('$subID','".addslashes($key)."','".addslashes($track[$key])."'),";
			
			$wpdb->query(substr($sql,0,-1));

			// Files uploaded??
			$filefield=0;
			if ( isset($_FILES['cf_uploadfile'.$no]) ) {
				foreach( $_FILES['cf_uploadfile'.$no][tmp_name] as $tmpfile ) {
		            //copy attachment to local server dir
		            if ( $tmpfile <> '')
		            	copy($tmpfile,get_option('cforms'.$no.'_upload_dir').'/'.$subID.'-'.$file['name'][$filefield++]);
				}	 
			}
			
		}
		

		//set header
		$replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes(get_option('cforms'.$no.'_email')) );

		// multiple recipients? and to whom is the email sent?
		if ( $to_one <> "-1" ) {
				$all_to_email = explode(',', $replyto);
				$replyto = $to = $all_to_email[ $to_one ];
		} else
				$to = $replyto;


		//
		// ready to send email
		// email header 
		//
		$eol = "\n";

		if ( ($frommail=stripslashes(get_option('cforms'.$no.'_fromemail')))=='' )
			$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';	
		
		$headers = "From: ". $frommail . $eol;
		$headers.= "Reply-To: " . $field_email . $eol;

		if ( ($tempBcc=stripslashes(get_option('cforms'.$no.'_bcc'))) != "")
		    $headers.= "Bcc: " . $tempBcc . $eol;

		$headers.= "MIME-Version: 1.0"  .$eol;
		$headers.= "Content-Type: multipart/mixed; boundary=\"----MIME_BOUNDRY_main_message\"";
	
		// prep message text, replace variables
		$message	= get_option('cforms'.$no.'_header');
		$message	= check_default_vars($message,$no);
		$message	= check_cust_vars($message,$track);
		
		// text & html message
		$fmessage = "This is a multi-part message in MIME format."  . $eol;
		$fmessage .= "------MIME_BOUNDRY_main_message"  . $eol;


		// HTML message part?
		$html_show = ( substr(get_option('cforms'.$no.'_formdata'),2,1)=='1' )?true:false;

		if ($html_show) {
			$fmessage .= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_sub_message\"" . $eol . $eol;
			$fmessage .= "------MIME_BOUNDRY_sub_message"  . $eol;
			$fmessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;
			$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;
		}
		else
			$fmessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol . $eol;

		$fmessage .= stripslashes($message) . $eol;
		
		// need to add form data summary or is all in the header anyway?
		if(substr(get_option('cforms'.$no.'_formdata'),0,1)=='1')
			$fmessage .= $eol . $formdata . $eol;


		// HTML text
		if ( $html_show ) {
		
			// actual user message
			$htmlmessage = get_option('cforms'.$no.'_header_html');					
			$htmlmessage = check_default_vars($htmlmessage,$no);
			$htmlmessage = check_cust_vars($htmlmessage,$track);

	
			$fmessage .= "------MIME_BOUNDRY_sub_message"  . $eol;	
			$fmessage .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
			$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;;
	
			$fmessage .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">"  . $eol;

			$fmessage .= "<HTML>" . $eol;
			$fmessage .= $styles;
			$fmessage .= "<BODY>" . $eol;

			$fmessage .= stripslashes($htmlmessage);
	
			// need to add form data summary or is all in the header anyway?
			if(substr(get_option('cforms'.$no.'_formdata'),1,1)=='1')
				$fmessage .= $eol . $htmlformdata;
	
			$fmessage .= "</BODY></HTML>"  . $eol . $eol;
		}

		// end of sub message
		
		$attached='';
		// possibly add attachment
		if ( isset($_FILES['cf_uploadfile'.$no]) && $filedata[0]<>'' && !get_option('cforms'.$no.'_noattachments') ) {

				// different header for attached files
		 		//
		 		$all_mime = array("txt"=>"text/plain", "htm"=>"text/html", "html"=>"text/html", "gif"=>"image/gif", "png"=>"image/x-png",
		 						 "jpeg"=>"image/jpeg", "jpg"=>"image/jpeg", "tif"=>"image/tiff", "bmp"=>"image/x-ms-bmp", "wav"=>"audio/x-wav",
		 						 "mpeg"=>"video/mpeg", "mpg"=>"video/mpeg", "mov"=>"video/quicktime", "avi"=>"video/x-msvideo",
		 						 "rtf"=>"application/rtf", "pdf"=>"application/pdf", "zip"=>"application/zip", "hqx"=>"application/mac-binhex40",
		 						 "sit"=>"application/x-stuffit", "exe"=>"application/octet-stream", "ppz"=>"application/mspowerpoint",
								 "ppt"=>"application/vnd.ms-powerpoint", "ppj"=>"application/vnd.ms-project", "xls"=>"application/vnd.ms-excel",
								 "doc"=>"application/msword");

				if ( $html_show )
					$fmessage .= "------MIME_BOUNDRY_sub_message--"  . $eol;

				for ( $filefield=0; $filefield < count($_FILES['cf_uploadfile'.$no][name]); $filefield++) {
	
					if ( $filedata[$filefield] <> '' ){
						$mime = (!$all_mime[$fileext[$filefield]])?'application/octet-stream':$all_mime[$fileext[$filefield]];
		
						$attached .= "------MIME_BOUNDRY_main_message" . $eol;		
						$attached .= "Content-Type: $mime;\n\tname=\"" . $file['name'][$filefield] . "\"" . $eol;
						$attached .= "Content-Transfer-Encoding: base64" . $eol;
						$attached .= "Content-Disposition: inline;\n\tfilename=\"" . $file['name'][$filefield] . "\"\n" . $eol;
						$attached .= $eol . $filedata[$filefield]; 	//The base64 encoded message
					}				
					
				}
			
 		}


		//
		// finally send mails
		//
		
		//either use configured subject or user determined
		//now replace the left over {xyz} variables with the input data
		$vsubject = get_option('cforms'.$no.'_subject');
		$vsubject = check_default_vars($vsubject,$no);
		$vsubject = check_cust_vars($vsubject,$track);

		// SMTP server or native PHP mail() ?
		if ( $smtpsettings[0]=='1' )
			$sentadmin = cforms_phpmailer( $no, $frommail, $field_email, $to, $vsubject, $message, $formdata, $htmlmessage, $htmlformdata, $fileext );
		else
			$sentadmin = @mail($to, stripslashes($vsubject), stripslashes($fmessage).$attached, $headers);	

		if( $sentadmin==1 ) {
				  // send copy or notification?
			    if ( (get_option('cforms'.$no.'_confirm')=='1' && $field_email<>'') || $ccme )  // not if no email & already CC'ed
			    {
							if ( ($frommail=stripslashes(get_option('cforms'.$no.'_fromemail')))=='' )
								$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';	
							
							// HTML message part?
							$html_show_ac = ( substr(get_option('cforms'.$no.'_formdata'),3,1)=='1' )?true:false;
							$automsg = '';

							$headers2 = "From: ". $frommail . $eol;
							$headers2.= "Reply-To: " . $replyto . $eol;
							$headers2.= "MIME-Version: 1.0"  .$eol;

							if( $html_show_ac || ($html_show && $ccme ) ){
								$headers2.= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_main_message\"";
								$automsg .= "This is a multi-part message in MIME format."  . $eol;
								$automsg .= "------MIME_BOUNDRY_main_message"  . $eol;
								$automsg .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"; format=flowed" . $eol;
								$automsg .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;								
							} 
							else
								$headers2.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"; format=flowed";
							
	
							// actual user message
							$cmsg = get_option('cforms'.$no.'_cmsg');					
							$cmsg = check_default_vars($cmsg,$no);
							$cmsg = check_cust_vars($cmsg,$track);
		
					
							// text text
							$automsg .= $cmsg . $eol;
		
							// HTML text
							if ( $html_show_ac ) {
							
								// actual user message
								$cmsghtml = get_option('cforms'.$no.'_cmsg_html');					
								$cmsghtml = check_default_vars($cmsghtml,$no);
								$cmsghtml = check_cust_vars($cmsghtml,$track);
		
								$automsg .= "------MIME_BOUNDRY_main_message"  . $eol;
								$automsg .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
								$automsg .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;;
			
								$automsg .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">"  . $eol;
								$automsg .= "<HTML><BODY>"  . $eol;
								$automsg .= $cmsghtml;
								$automsg .= "</BODY></HTML>"  . $eol . $eol;
							}							
							
						 	$subject2 = get_option('cforms'.$no.'_csubject');
							$subject2 = check_default_vars($subject2,$no);
							$subject2 = check_cust_vars($subject2,$track);

							// different cc & ac subjects?
							$t=explode('$#$',$subject2);
							$t[1] = ($t[1]<>'') ? $t[1] : $t[0];

							// email tracking via 3rd party?
							$field_email = (get_option('cforms'.$no.'_tracking')<>'')?$field_email.get_option('cforms'.$no.'_tracking'):$field_email;
	    

							if ( $ccme ) {
								if ( $smtpsettings[0]=='1' )
									$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[1]), $message, $formdata, $htmlmessage, $htmlformdata, '' );
								else
									$sent = @mail($field_email, stripslashes($t[1]), stripslashes($fmessage), $headers2); //the admin one
							}
							else {
								if ( $smtpsettings[0]=='1' )
									$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[0]) , $cmsg , '', $cmsghtml, '' );
								else
									$sent = @mail($field_email, stripslashes($t[0]), stripslashes($automsg), $headers2); //takes the above
							}
					
			  		if( $sent<>'1' )
				  			$usermessage_text = __('Error occured while sending the auto confirmation message.','cforms')." ($sent)";
			    }

			// redirect to a different page on suceess?
			if ( get_option('cforms'.$no.'_redirect') ) {
				?>
				<script type="text/javascript">
					location.href = '<?php echo get_option('cforms'.$no.'_redirect_page') ?>';
				</script>
				<?php 
			}		
			
	  	} // if first email already failed
		else
			$usermessage_text = __('Error occured while sending the message: ','cforms') . '<br/>'. $smtpsettings[0]?'<br/>'.$sentadmin:'';

	} //if isset & valid sendbutton


	//
	// paint form
	//
	$break='<br />';
	$nl="\n";
	$tab="\t";

	//either show message above or below
	if( substr(get_option('cforms'.$no.'_showpos'),0,1)=='y' ) {
		$content .= $nl . $indent . $tab . '<p id="usermessage'.$no.'a" class="info ' . $usermessage_class . '" >' . $usermessage_text . '&nbsp;</p>' . $nl;
		$actiontarget = 'a';
 	} else if ( substr(get_option('cforms'.$no.'_showpos'),1,1)=='y' )
		$actiontarget = 'b';
 	
 	
 	//alternative form action
	$alt_action=false;
	if( get_option('cforms'.$no.'_action')=='1' ) {
		$action = get_option('cforms'.$no.'_action_page');
		$alt_action=true;
 	} else
		$action = $_SERVER['REQUEST_URI'] . '#usermessage'. $no . $actiontarget;

 	
	$content .= $indent . $tab . '<form enctype="multipart/form-data" action="' . $action . '" method="post" class="cform" id="cforms'.$no.'form">' . $nl;

	// start with no fieldset
	$fieldsetopen = false;
	$verification = false;
	$captcha = false;
	$upload = false;
	$fscount = 1;
	$ol = false;
	
	for($i = 1; $i <= $field_count; $i++) {

		if ( !$custom ) 
      		$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . $i));
		else
    		$field_stat = explode('$#$', $customfields[$i-1]);
		
		$field_name       = $field_stat[0];
		$field_type       = $field_stat[1];
		$field_required   = $field_stat[2];
		$field_emailcheck = $field_stat[3];
		$field_clear      = $field_stat[4];
		$field_disabled   = $field_stat[5];


		//special treatment for selectboxes  
		if (  in_array($field_type,array('multiselectbox','selectbox','radiobuttons','checkbox','checkboxgroup','ccbox','emailtobox'))  ){
			$options = explode('#', stripslashes(htmlspecialchars($field_name)) );
            $field_name = $options[0];
		}
		
		
		$labelclass='';
		//visitor verification
		if ( !$verification && $field_type == 'verification' ) {
			srand(microtime()*1000003);
        	$qall = explode( "\r\n", get_option('cforms_sec_qa') );
			$n = rand(0,(count(array_keys($qall))-1));
			$q = $qall[ $n ];
			$q = explode( '=', $q );  //  q[0]=qestion  q[1]=answer
			$field_name = stripslashes(htmlspecialchars($q[0]));
			$labelclass = ' class="secq"';
		}
		else if ( $field_type == 'captcha' )
			$labelclass = ' class="seccap"';


		//Label ID's
		$labelIDx = '';
		$labelID  = (get_option('cforms_labelID')=='1')?' id="label-'.$no.'-'.$i.'"':'';
		

		$defaultvalue = '';
		// no labels and other goodies for fieldsets, radio- & checkboxes !
		if ( ! in_array($field_type,array('fieldsetstart','fieldsetend','radiobuttons','checkbox','checkboxgroup','ccbox')) ) {

		    // check if default val & regexp are set
		    $obj = explode('|', $field_name,3);

			if ( $obj[2] <> '')	$reg_exp = stripslashes($obj[2]); else $reg_exp='';
		    if ( $obj[1] <> '')	$defaultvalue = stripslashes(htmlspecialchars($obj[1]));

				$field_name = $obj[0];

				//set label for
				switch ($field_type){
				  case 'verification':
					  $label = 'cforms_q'.$no;
				  break;
				  case 'captcha':
					  $label = 'cforms_captcha'.$no;
				  break;
				  case 'upload':
					  $label = 'cf_uploadfile'.$no;
				  break;
				  default:
					  $label = 'cf'.$no.'_field_' . $i;
				  break;
				}


				//check if fieldset is open
				if ( !$fieldsetopen && !$ol ) {
					$content .= $indent . $tab . '<ol class="cf-ol">' . $nl;
					$ol = true;
				}

				//print label only for non "textonly" fields! Skip some others too, and handle them below indiv.
				if( $field_type <> 'textonly' )
					$content .= $nl . $indent . $tab . $tab . '<li><label ' . $labelID . ' for="'.$label.'"'. $labelclass . '><span>' . stripslashes(($field_name)) . '</span></label>';

		}

		
		
		// field classes
		$field_class = 'default';
		
		if      ( $field_disabled )		$field_class .= ' disabled';
		else if ( $field_emailcheck )	$field_class .= ' fldemail';
		else if ( $field_required ) 	$field_class .= ' fldrequired';

		
		$field_value = ''; 
		if(!isset($_POST['sendbutton'.$no]) && isset($_GET['cf'.$no.'_field_'.$i]))
			$field_value = $_GET['cf'.$no.'_field_'.$i];

		// an error ocurred:
		if(! $all_valid){
			$field_class .= ($validations[$i]==1)?'':' error';  //error ?
			if ( $field_type == 'multiselectbox' || $field_type == 'checkboxgroup' ){
				$field_value = $_POST['cf'.$no.'_field_' . $i];  // in this case it's an array! will do the stripping later
			}
			else
				$field_value = stripslashes(htmlspecialchars($_POST['cf'.$no.'_field_' . $i]));
		}
		
		if ( $field_value=='' && $defaultvalue<>'' ) // if not reloaded (due to err) then use default values
			$field_value=$defaultvalue;    

		// field disabled, greyed out?
		$disabled = $field_disabled?'disabled="disabled"':'';

		$field = '';
		switch($field_type) {

			case "upload":
	  			$upload=true;  // set upload flag for ajax suppression!
				$field = '<input ' . $disabled . ' type="file" name="cf_uploadfile'.$no.'[]" id="cf_uploadfile'.$no.'" class="cf_upload ' . $field_class . '"/>';
				break;

			case "textonly":
				$field .= $indent . $tab . $tab . '<li class="textonly' . (($defaultvalue<>'')?' '.$defaultvalue:'') . '" ' . (($reg_exp<>'')?' style="'.$reg_exp.'" ':'') . '>' . stripslashes(htmlspecialchars($field_name)) . '</li>';
				break;

			case "fieldsetstart":
				if ($fieldsetopen) {
						$field = $nl . $indent . $tab . '</ol>' . $nl .
								 $indent . $tab . '</fieldset>' . $nl;
						$fieldsetopen = false;
						$ol = false;
				}
				if (!$fieldsetopen) {
						if ($ol)
							$field = $nl . $indent . $tab . '</ol>' . $nl;

						$field .= $indent . $tab .'<fieldset class="cf-fs'.$fscount++.'">' . $nl .
								  $indent . $tab . '<legend>' . stripslashes($field_name) . '</legend>' . $nl .
								  $indent . $tab . '<ol class="cf-ol">';
						$fieldsetopen = true;
						$ol = true;
		 		}
				break;

			case "fieldsetend":
				if ($fieldsetopen) {
						$field = $indent . $tab . '</ol>' . $nl .
								 $indent . $tab . '</fieldset>' . $nl;
						$fieldsetopen = false;
						$ol = false;
				} else $field='';
				break;

			case "verification":
				$field = '<input type="text" name="cforms_q'.$no.'" id="cforms_q'.$no.'" class="secinput ' . $field_class . '" value="" />';
		    	$verification=true;
				break;

			case "captcha":
				$_SESSION['turing_string_'.$no] = rc(4,5);
				
				$field = '<input type="text" name="cforms_captcha'.$no.'" id="cforms_captcha'.$no.'" class="secinput ' . $field_class . '" value="" />'.
						 '<img class="captcha" src="'.$cforms_root.'/cforms-captcha.php?ts='.$no.'" alt=""/>';
		    	$captcha=true;
				break;

			case "textfield":
			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';
					
				$field = '<input ' . $disabled . ' type="text" name="cf'.$no.'_field_' . $i . '" id="cf'.$no.'_field_' . $i . '" class="' . $field_class . '" value="' . $field_value  . '"'.$onfocus.'/>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="cf'.$no.'_field_' . $i . '_regexp" id="cf'.$no.'_field_' . $i . '_regexp" value="'.$reg_exp.'"/>';
	
				break;

			case "textarea":

			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<textarea ' . $disabled . ' cols="30" rows="8" name="cf'.$no.'_field_' . $i . '" id="cf'.$no.'_field_' . $i . '" class="' . $field_class . '"'. $onfocus.'>' . $field_value  . '</textarea>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="cf'.$no.'_field_' . $i . '_regexp" id="cf'.$no.'_field_' . $i . '_regexp" value="'.$reg_exp.'"/>';
				break;

	   		case "ccbox":
			case "checkbox":
				$err='';
				if(! $all_valid)
						$err = ($validations[$i]==1)?'':' errortxt';  //error ?

			  if ( $options[1]<>'' ) {
				 		$before = $nl . $indent . $tab . $tab . '<li>';
						$after  = '<label ' . $disabled . $labelID . ' for="cf'.$no.'_field_' . $i . '" class="cf-after'.$err.'"><span>' . ($options[1]) . '</span></label></li>';
				 		$ba = 'a ';
				}
				else {
						$before = $nl . $indent . $tab . $tab . '<li><label ' . $disabled . $labelID . ' for="cf'.$no.'_field_' . $i . '" class="cf-before'. $err .'"><span>' . ($field_name) . '</span></label>';
				 		$after  = '</li>';
				 		$ba = 'b ';
				}
				$field = $before . '<input ' . $disabled . ' type="checkbox" name="cf'.$no.'_field_' . $i . '" id="cf'.$no.'_field_' . $i . '" class="cf-box-' . $ba . $field_class . '" '.($field_value?'checked="checked"':'').' />' . $after;

				break;


			case "checkboxgroup":
				array_shift($options);
				$field .= $nl . $indent . $tab . $tab . '<li class="cf-box-title">' . (($field_name)) . '</li>' . 
						  $nl . $indent . $tab . $tab . '<li class="cf-box-group">' . $nl .
						  $indent . $tab . $tab . $tab;
				$id=1; $j=0;
				foreach( $options as $option  ) {

						//supporting names & values
					    $opt = explode('|', $option,2);

						if ( $opt[1]=='' ) $opt[1] = $opt[0];

	                    $checked = '';
	                    if ( $opt[1]==stripslashes(htmlspecialchars($field_value[$j])) )  {
	                        $checked = 'checked="checked"';
	                        $j++;
	                    }
						
						if ( $labelID<>'' ) $labelIDx = substr($labelID,0,-1) . $id . '"';

						if ( $opt[0]=='' )
							$field .= '<br />';
						else
							$field .= '<input ' . $disabled . ' type="checkbox" id="cf'.$no.'_field_'.$i. $id . '" name="cf'.$no.'_field_' . $i . '[]" value="'.$opt[1].'" '.$checked.' class="cf-box-b"/>'.
											'<label ' . $disabled . $labelIDx . ' for="cf'.$no.'_field_'. $i . ($id++) . '" class="cf-group-after"><span>'.$opt[0] . "</span></label>";
											
					}
				$field .= $nl . $indent . $tab . $tab . '</li>';
				break;
				
				
			case "multiselectbox":
				$field = '<select ' . $disabled . ' multiple="multiple" name="cf'.$no.'_field_' . $i . '[]" id="cf'.$no.'_field_' . $i . '" class="cfselectmulti ' . $field_class . '">';
				array_shift($options);
				$second = false;
				$j=0;
				foreach( $options as $option  ) {
                    //supporting names & values
                    $opt = explode('|', $option,2);
                    if ( $opt[1]=='' ) $opt[1] = $opt[0];
                    
                    $checked = '';
                    if ( $opt[1]==stripslashes(htmlspecialchars($field_value[$j])) )  {
                        $checked = 'selected="selected"';
                        $j++;
                    }
                        
                    $field.= '<option value="'. $opt[1] .'" '.$checked.'>'.$opt[0].'</option>';
                    $second = true;
                    
				}
				$field.= '</select>';
				break;

			case "emailtobox":
			case "selectbox":
				$field = '<select ' . $disabled . ' name="cf'.$no.'_field_' . $i . '" id="cf'.$no.'_field_' . $i . '" class="cformselect ' . $field_class . '">';
				array_shift($options); $jj=$j=0;
				$second = false;
				foreach( $options as $option  ) {

						//supporting names & values
				    $opt = explode('|', $option,2);
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

						//email-to-box valid entry?
				    if ( $field_type == 'emailtobox' && $opt[1]<>'-' )
								$jj = $j++; else $jj = '-';

				    $checked = '';
						if( $field_value == '' || $field_value == '-') {
								if ( !$second )
								    $checked = 'selected="selected"';
						}	else
								if ( $opt[1]==$field_value || $jj==$field_value ) $checked = 'selected="selected"';
						    
						$field.= '<option value="'.(($field_type=='emailtobox')?$jj:$opt[1]).'" '.$checked.'>'.$opt[0].'</option>';
						$second = true;
				}
				$field.= '</select>';
				break;

			case "radiobuttons":
				array_shift($options);
				$field .= $nl . $indent . $tab . $tab . '<li class="cf-box-title">' . (($field_name)) . '</li>' . 
						  $nl . $indent . $tab . $tab . '<li>';
				$second = false; $id=1;
				foreach( $options as $option  ) {
				    $checked = '';

						//supporting names & values
				    $opt = explode('|', $option,2);
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

						if( $field_value == '' ) {
								if ( !$second )
								    $checked = 'checked="checked"';
						}	else
								if ( $opt[1]==$field_value ) $checked = 'checked="checked"';
						
						if ( $labelID<>'' ) $labelIDx = substr($labelID,0,-1) . $id . '"';
						
						$field .= '<input ' . $disabled . ' type="radio" id="cf'.$no.'_field_'.$i. $id . '" name="cf'.$no.'_field_' . $i . '" value="'.$opt[1].'" '.$checked.' class="cf-box-a'.(($second)?' cformradioplus':'').'"/>'.
											'<label ' . $disabled . $labelIDx . ' for="cf'.$no.'_field_'. $i . ($id++) . '" class="cf-after"><span>'.$opt[0] . "</span></label>$break";
											
						$second = true;
					}
				$field .= '</li>';
				break;
				
		}
		
		// add new field
		$content .= $field;

		// adding "required" text if needed
		if($field_emailcheck == 1)
			$content .= '<span class="emailreqtxt">&nbsp;'.stripslashes(get_option('cforms'.$no.'_emailrequired')).'</span>';
		else if($field_required == 1 && $field_type <> 'checkbox' && $field_type <> 'multiselectbox' && $field_type <> 'selectbox' && $field_type <> 'upload' )
			$content .= '<span class="reqtxt">&nbsp;'.stripslashes(get_option('cforms'.$no.'_required')).'</span>';

		//close out li item
		if ( ! in_array($field_type,array('fieldsetstart','fieldsetend','radiobuttons','checkbox','checkboxgroup','ccbox','textonly')) )
			$content .= '</li>';
		
	} //all fields


	if ( $ol )
		$content .= $nl . $indent . $tab . '</ol>';
	if ( $fieldsetopen )
		$content .= $nl . $indent . $tab . '</fieldset>' . $nl;


	// rest of the form

	if ( get_option('cforms'.$no.'_ajax')=='1' && !$upload && !$custom && !$alt_action )   // ajax enabled & no upload file field!
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', false)"';
	else if ( ($upload || $custom || $alt_action) && get_option('cforms'.$no.'_ajax')=='1' )
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', true)"';
	else
		$ajaxenabled = '';

	// just to appease "strict"
	$content .= $indent . $tab . '<fieldset class="cf_hidden">' . $nl;

	// if visitor verification turned on:
	if ( $verification )
		$content .= $nl . $indent . $tab . '<input type="hidden" name="cforms_a'.$no.'" id="cforms_a'.$no.'" value="' . md5(rawurlencode(strtolower($q[1]))) . '"/>';

	if ( $captcha )
		$content .= $nl . $indent . $tab . '<input type="hidden" name="cforms_cap'.$no.'" id="cforms_cap'.$no.'" value="' . md5(strtolower($_SESSION['turing_string_'.$no])) . '"/>';

	$content .= $indent . $tab . $tab . '<input type="hidden" name="cf_working'.$no.'" id="cf_working'.$no.'" value="'.rawurlencode(get_option('cforms'.$no.'_working')).'"/>'. $nl .
				$indent . $tab . $tab . '<input type="hidden" name="cf_failure'.$no.'" id="cf_failure'.$no.'" value="'.rawurlencode(get_option('cforms'.$no.'_failure')).'"/>'. $nl .
				$indent . $tab . $tab . '<input type="hidden" name="cf_codeerr'.$no.'" id="cf_codeerr'.$no.'" value="'.rawurlencode(get_option('cforms_codeerr')).'"/>'. $nl .
				$indent . $tab . $tab . '<input type="hidden" name="cf_popup'.$no.'"   id="cf_popup'.$no.'"   value="'.get_option('cforms'.$no.'_popup').'"/>' . $nl;

	$content .= $indent . $tab . '</fieldset>' . $nl;

	$content .= $indent . $tab . '<p class="cf-sb"><input type="submit" name="sendbutton'.$no.'" id="sendbutton'.$no.'" class="sendbutton" value="' . get_option('cforms'.$no.'_submit_text') . '"'.
										$ajaxenabled.'/></p>' . $nl;

	$content .= $indent . $tab . '</form>' . $nl;

	//link love? you bet ;)
	//	if( get_option('cforms_linklove')=="1" ) 
		$content .= $indent . $tab . '<p class="linklove" id="ll'. $no 	.'"><a href="http://www.deliciousdays.com/cforms-plugin"><em>cforms</em> contact form by delicious:days</a></p>' . $nl;
	

	//either show message above or below
	if( substr(get_option('cforms'.$no.'_showpos'),1,1)=='y' )
		$content .= $indent . $tab . '<p id="usermessage'.$no.'b" class="info ' . $usermessage_class . '" >' . $usermessage_text . '&nbsp;</p>' . $nl;

	return $content;
}

// replace placeholder by generated code
function cforms_insert( $content ) {

  $forms = get_option('cforms_formcount');
  for ($i=1;$i<=$forms;$i++)
  {
    if($i==1)
    {
      if(preg_match('#<!--cforms-->#', $content))
  	   	$content = preg_replace('/(<p>)?<!--cforms-->(<\/p>)?/', cforms(''), $content);
    } else {
      if(preg_match('#<!--cforms'.$i.'-->#', $content))
    		$content = preg_replace('/(<p>)?<!--cforms'.$i.'-->(<\/p>)?/', cforms('',$i), $content);
    }
	}

	return $content;
}

// captcha random code
function rc($min,$max) 
{
	$src = 'abcdefghijkmnpqrstuvwxyz';   
	$src .= '23456789';         
	$srclen = strlen($src)-1;
	
	$length = mt_rand($min,$max);
	$Code = '';
	
	for($i=0; $i<$length; $i++) 
		$Code .= substr($src, mt_rand(0, $srclen), 1);
	
	return $Code;

}

function cforms_is_email($string)
{ return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $string); }

// some css for positioning the form elements
function cforms_style() {
	global $cforms_root;
	echo "\n\t<!-- Start Of Script Generated By cforms v".cforms_info('localversion')." [Oliver Seidel | www.deliciousdays.com] -->\n";
	echo "\t".'<link rel="stylesheet" type="text/css" href="' . $cforms_root . '/styling/' . get_option('cforms_css') . '" />'."\n";
	echo "\t".'<script type="text/javascript" src="' . $cforms_root. '/js/cforms.js"></script>'."\n";
	echo "\t".'<!-- End Of Script Generated By cforms -->'."\n\n";
}


// some css for arranging the table fields in wp-admin
function cforms_options_page_style() {

	if (   strpos($_SERVER['REQUEST_URI'], 'plugins.php') !== false ) {
		if( cforms_remote_version_check()==1 ) {
			global $cforms_root;
			$nl="\n";
			echo '<script type="text/javascript" src="' . $cforms_root . '/js/cformsadmin.js"></script>' . $nl .
				 '<script type=\'text/javascript\'>' . $nl .
				 '//<![CDATA[' . $nl .
				 'addLoadEvent(newcformsversion);' . $nl .
				 '//]]>'. $nl .
				 '</script>' . $nl;
	  } // no newer release avail.
	} // not plugin page
  
 	// other admin pages
	global $cforms_root;
	if (   strpos($_SERVER['REQUEST_URI'], $plugindir.'/cforms') !== false )
		echo	'<link rel="stylesheet" type="text/css" href="' . $cforms_root . '/cforms-admin.css" />' . "\n" .
				'<script type="text/javascript" src="' . $cforms_root . '/js/dbx.js"></script>' . "\n" .
				'<script type="text/javascript" src="' . $cforms_root . '/js/cformsadmin.js"></script>' . "\n";
}

//footer unbder all options pages
function cforms_footer() {
?>	<p style="padding-top:50px; font-size:11px; text-align:center;">
		<em>
			<?php echo str_replace('[url]','http://www.deliciousdays.com/cforms-forum/',__('For more information and support, visit the <strong>cforms</strong> <a href="[url]" title="cforms support forum">support forum</a>. ', 'cforms')) ?>
			<?php _e('Translation provided by Oliver Seidel, for updates <a href="http://deliciousdays.com/cforms-plugin">check here.</a>', 'cforms') ?>
		</em>
	</p>

	<p align="center">Version <?php echo cforms_info('localversion'); ?></p>
<?php 
}

//build field_stat string from array (for custom forms)
function build_fstat($fields) {	
    $cfarray = array();
    for($i=0; $i<count($fields['label']); $i++) {
        if ( $fields['type'][$i] == '') $fields['type'][$i] = 'textfield';
        if ( $fields['isreq'][$i] == '') $fields['isreq'][$i] = '0';
        if ( $fields['isemail'][$i] == '') $fields['isemail'][$i] = '0';
        if ( $fields['isclear'][$i] == '') $fields['isclear'][$i] = '0';
        if ( $fields['isdisabled'][$i] == '') $fields['isdisabled'][$i] = '0';
        $cfarray[$i]=$fields['label'][$i].'$#$'.$fields['type'][$i].'$#$'.$fields['isreq'][$i].'$#$'.$fields['isemail'][$i].'$#$'.$fields['isclear'][$i].'$#$'.$fields['isdisabled'][$i];
    }
    return $cfarray;
}

// inserts a cform anywhere you want
function insert_cform($no='') {	echo cforms('',$no); }

// inserts a custom cform anywhere you want
function insert_custom_cform($fields='',$no='') { echo cforms($fields,$no.'+'); }


// Set 'manage_database' Capabilities To Administrator
add_action('activate_'.$plugindir.'/cforms.php', 'cforms_init');
function cforms_init() {
	global $wpdb;
	
	$role = get_role('administrator');
	if(!$role->has_cap('manage_cforms')) {
		$role->add_cap('manage_cforms');
	}
	if(!$role->has_cap('track_cforms')) {
		$role->add_cap('track_cforms');
	}
	
	//alter tracking tables if needed
	$tables = $wpdb->get_col("SHOW TABLES FROM " . DB_NAME . " LIKE '$wpdb->cformssubmissions'",0);

	if( $tables[0]==$wpdb->cformssubmissions ) {

		$columns = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->cformssubmissions}");
		if ( $columns[2]->Field == 'date' ) 
			$result = $wpdb->query("ALTER TABLE `{$wpdb->cformssubmissions}` CHANGE `date` `sub_date` TIMESTAMP");
	
	}
	
}



function widget_cforms_init() {
	if (! function_exists("register_sidebar_widget")) {
		return;
	}
	
	function widget_cforms($args) {
		extract($args);
		
		echo $before_widget;
		
		preg_match('/^.*widgetcform([^"]*)".*$/',$before_widget,$form_no);

		$no = ($form_no[1]=='0')?'':(int)($form_no[1]+1);
		echo $before_widget . "<!--oli $no-->" . insert_cform($no) . $after_widget;
	}

	for ( $i=0;$i<get_option('cforms_formcount');$i++ ) {
	
		$no = ($i==0)?'':($i+1);
		$form = substr(get_option('cforms'.$no.'_fname'),0,10).'...';
		
		$nodisp = ($i==0)?'default':'#'.($i+1);
		register_sidebar_widget('cforms II ('.$nodisp.')<br />&raquo;'.$form, 'widget_cforms','widgetcform'.$i);
	}

}




function cforms_menu() {
	global $plugindir, $wpdb;

	$tablesup = ($wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions)?true:false;
	
	if (function_exists('add_menu_page')) {
		add_menu_page(__('cforms II', 'cforms'), __('cforms II', 'cforms'), 'manage_cforms', $plugindir.'/cforms-options.php');
	}
	if (function_exists('add_submenu_page')) {
		add_submenu_page($plugindir.'/cforms-options.php', __('Plugin Settings', 'cforms'), __('Plugin Settings', 'cforms'), 'manage_cforms', $plugindir.'/cforms-global-settings.php');
		if ( ($tablesup || isset($_REQUEST['cforms_database'])) && !isset($_REQUEST['deletetables']) )
			add_submenu_page($plugindir.'/cforms-options.php', __('Tracking', 'cforms'), __('Tracking', 'cforms'), 'track_cforms', $plugindir.'/cforms-database.php');
		add_submenu_page($plugindir.'/cforms-options.php', __('Styling', 'cforms'), __('Styling', 'cforms'), 'manage_cforms', $plugindir.'/cforms-css.php');
		add_submenu_page($plugindir.'/cforms-options.php', __('Help!', 'cforms'), __('Help!', 'cforms'), 'manage_cforms', $plugindir.'/cforms-help.php');
	}
}


if (function_exists('add_action')){
	add_action('admin_head', 'cforms_options_page_style');
	add_action('admin_menu', 'cforms_menu');
	add_action('plugins_loaded', 'widget_cforms_init');
}

// add content actions and filters
add_filter('wp_head', 'cforms_style'); 
add_filter('the_content', 'cforms_insert',10);

?>
