<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v2.0.4
|   =============================================
|   by Matthew Mecham
|   (c) 2001 - 2004 Invision Power Services, Inc.
|   http://www.invisionpower.com
|   =============================================
|   Web: http://www.invisionboard.com
|   Time: Wed, 04 May 2005 15:17:58 GMT
|   Release: 303bba6732d809e44cd52a144ab90a4b
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Sending email module
|   > Module written by Matt Mecham
|   > Date started: 26th February 2002
|
|	> Module Version Number: 1.0.0
|   > DBA Checked: Wed 19 May 2004
+--------------------------------------------------------------------------
|
|   QUOTE OF THE MODULE: (Taken from "Shrek" (c) Dreamworks Pictures)
|   --------------------
|	DONKEY: We can stay up late, swap manly stories and in the morning,
|           I'm making waffles!
|
+--------------------------------------------------------------------------
*/

// This module is fairly basic, more functionality is expected in future
// versions (such as MIME attachments, SMTP stuff, etc)


class emailer {

	var $from         = "";
	var $to           = "";
	var $subject      = "";
	var $message      = "";
	var $header       = "";
	var $footer       = "";
	var $template     = "";
	var $error        = "";
	var $parts        = array();
	var $bcc          = array();
	var $mail_headers = "";
	var $multipart    = "";
	var $boundry      = "";

	var $html_email   = 0;
	var $char_set     = 'iso-8859-1';

	var $smtp_fp      = FALSE;
	var $smtp_msg     = "";
	var $smtp_port    = "";
	var $smtp_host    = "localhost";
	var $smtp_user    = "";
	var $smtp_pass    = "";
	var $smtp_code    = "";

	var $wrap_brackets = 0;

	var $mail_method  = 'mail';

	var $temp_dump    = 0;
	var $root_path    = './';

	/*-------------------------------------------------------------------------*/
	// CONSTRUCTOR
	/*-------------------------------------------------------------------------*/

	function emailer($ROOT_PATH="")
	{
		global $ibforums, $DB, $std;

		$this->email_init();

		//-----------------------------------------
		// Set up SMTP if we're using it
		//-----------------------------------------

		if ( $ibforums->vars['mail_method'] == 'smtp' )
		{
			$this->mail_method = 'smtp';
			$this->smtp_port   = ( intval($ibforums->vars['smtp_port']) != "" ) ? intval($ibforums->vars['smtp_port']) : 25;
			$this->smtp_host   = (     $ibforums->vars['smtp_host'] != ""     ) ?     $ibforums->vars['smtp_host']     : 'localhost';
			$this->smtp_user   = $ibforums->vars['smtp_user'];
			$this->smtp_pass   = $ibforums->vars['smtp_pass'];
		}

		if ( $ROOT_PATH )
		{
			$this->root_path = $ROOT_PATH;
		}

		if ( ! defined( 'ROOT_PATH') )
		{
			define( 'ROOT_PATH', $this->root_path );
		}

		//-----------------------------------------
		// Temporarily assign $header and $footer, this can be over-riden
		// also
		//-----------------------------------------

		$this->header  = $ibforums->vars['email_header'];
		$this->footer  = $ibforums->vars['email_footer'];
		$this->boundry = "----=_NextPart_000_0022_01C1BD6C.D0C0F9F0";  //"b".md5(uniqid(time()));

		$ibforums->vars['board_name'] = $this->clean_message($ibforums->vars['board_name']);
	}

	/*-------------------------------------------------------------------------*/
	// Email init
	/*-------------------------------------------------------------------------*/

	function email_init()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Assign $from as the admin out email address, this can be
		// over-riden at any time.
		//-----------------------------------------

		$this->from          = $ibforums->vars['email_out'];
		$this->temp_dump     = $ibforums->vars['fake_mail'];
		$this->wrap_brackets = $ibforums->vars['mail_wrap_brackets'];
	}

	/*-------------------------------------------------------------------------*/
	// ADD ATTACHMENT
	/*-------------------------------------------------------------------------*/

	function add_attachment($data = "", $name = "", $ctype='application/octet-stream')
	{

		$this->parts[] = array( 'ctype'  => $ctype,
								'data'   => $data,
								'encode' => 'base64',
								'name'   => $name
							  );
	}

	/*-------------------------------------------------------------------------*/
	// BUILD HEADERS
	/*-------------------------------------------------------------------------*/

	function build_headers()
	{
		global $ibforums;

		//-----------------------------------------
		// HTML (hitmuhl)
		//-----------------------------------------

		if ( $this->html_email )
		{
			$this->mail_headers .= "MIME-Version: 1.0\n";
			$this->mail_headers .= "Content-type: text/html; charset=\"".$this->char_set."\"\n";
		}

		//-----------------------------------------
		// Start mail headers
		//-----------------------------------------

		$this->mail_headers  .= "From: \"".$ibforums->vars['board_name']."\" <".$this->from.">\n";

		if ( $this->mail_method != 'smtp' )
		{
			if ( count( $this->bcc ) > 1 )
			{
				$this->mail_headers .= "Bcc: ".implode( "," , $this->bcc ) . "\n";
			}
		}
		else
		{
			if ( $this->to )
			{
				$this->mail_headers .= "To: ".$this->to."\n";
			}
			$this->mail_headers .= "Subject: ".$this->subject."\n";
		}

		//-----------------------------------------
		// we're not spam, really!
		//-----------------------------------------

		$this->mail_headers .= "Return-Path: ".$this->from."\n";
		$this->mail_headers .= "X-Priority: 3\n";
		$this->mail_headers .= "X-Mailer: IPB PHP Mailer\n";

		//-----------------------------------------
		// Count.. oh you get the idea
		//-----------------------------------------

		if ( count ($this->parts) > 0 )
		{
			if ( ! $this->html )
			{
				$this->mail_headers .= "MIME-Version: 1.0\n";
				$this->mail_headers .= "Content-Type: multipart/mixed;\n\tboundary=\"".$this->boundry."\"\n\nThis is a MIME encoded message.\n\n--".$this->boundry;
				$this->mail_headers .= "\nContent-Type: text/plain;\n\tcharset=\"".$this->char_set."\"\nContent-Transfer-Encoding: quoted-printable\n\n".$this->message."\n\n--".$this->boundry;
			}
			else
			{
				$this->mail_headers .= "MIME-Version: 1.0\n";
				$this->mail_headers .= "Content-Type: multipart/mixed;\n\tboundary=\"".$this->boundry."\"\n\nThis is a MIME encoded message.\n\n--".$this->boundry;
				$this->mail_headers .= "\nContent-Type: text/html;\n\tcharset=\"".$this->char_set."\"\nContent-Transfer-Encoding: quoted-printable\n\n".$this->message."\n\n--".$this->boundry;
			}

			$this->mail_headers .= $this->build_multipart();

			$this->message = "";
		}

	}

	/*-------------------------------------------------------------------------*/
	// ENCODE ATTACHMENT
	/*-------------------------------------------------------------------------*/

	function encode_attachment($part)
	{

		$msg = chunk_split(base64_encode($part['data']));

		return "Content-Type: ".$part['ctype']. ($part['name'] ? ";\n\tname =\"".$part['name']."\"" : "").
			  "\nContent-Transfer-Encoding: ".$part['encode']."\nContent-Disposition: attachment;\n\tfilename=\"".$part['name']."\"\n\n".$msg."\n";

	}

	/*-------------------------------------------------------------------------*/
	// BUILD MULTIPART
	/*-------------------------------------------------------------------------*/

	function build_multipart()
	{

		$multipart = "";

		for ($i = sizeof($this->parts) - 1 ; $i >= 0 ; $i--)
		{
			$multipart .= "\n".$this->encode_attachment($this->parts[$i]) . "--".$this->boundry;
		}

		return $multipart . "--\n";

	}


	/*-------------------------------------------------------------------------*/
	// send_mail:
	// Physically sends the email
	/*-------------------------------------------------------------------------*/

	function send_mail()
	{
		global $ibforums, $DB, $std;

		//-----------------------------------------
		// Wipe ya face
		//-----------------------------------------

		$this->to   = preg_replace( "/[ \t]+/" , ""  , $this->to   );
		$this->from = preg_replace( "/[ \t]+/" , ""  , $this->from );

		$this->to   = preg_replace( "/,,/"     , ","  , $this->to );
		$this->from = preg_replace( "/,,/"     , ","  , $this->from );

		$this->to     = preg_replace( "#\#\[\]'\"\(\):;/\$!£%\^&\*\{\}#" , "", $this->to  );
		$this->from   = preg_replace( "#\#\[\]'\"\(\):;/\$!£%\^&\*\{\}#" , "", $this->from);

		$this->subject = ( trim($this->lang_subject) != "" ) ? $this->lang_subject : $this->subject;

		$this->subject = $this->clean_message($this->subject);

		//-----------------------------------------
		// Build headers
		//-----------------------------------------

		$this->build_headers();

		//-----------------------------------------
		// Lets go..
		//-----------------------------------------

		if ( ($this->from) and ($this->subject) )
		{
			$this->subject .= " ( From ".$ibforums->vars['board_name']." )";

			//-----------------------------------------
			// Tmp dump? (Testing)
			//-----------------------------------------

			if ($this->temp_dump == 1)
			{
				$blah = $this->subject."\n------------\n".$this->mail_headers."\n\n".$this->message;

				$pathy = $this->root_path.'_mail/'.date("M-j-Y,hi-A").str_replace( '@', '+', $this->to ).".txt";
				$fh = fopen ($pathy, 'w');
				fputs ($fh, $blah, strlen($blah) );
				fclose($fh);
			}
			else
			{
				//-----------------------------------------
				// PHP MAIL()
				//-----------------------------------------
				if ($this->mail_method != 'smtp')
				{
					if ( ! @mail( $this->to, $this->subject, $this->message, $this->mail_headers ) )
					{
						$this->fatal_error("Could not send the email", "Failed at 'mail' command");
					}
				}
				//-----------------------------------------
				// SMTP
				//-----------------------------------------
				else
				{
					$this->smtp_send_mail();
				}
			}
		}
		else
		{
			$this->fatal_error("From or subject empty");
			return FALSE;
		}

		$this->to           = "";
		$this->from         = "";
		$this->message      = "";
		$this->subject      = "";
		$this->mail_headers = "";

		$this->email_init();
	}


	/*-------------------------------------------------------------------------*/
	// get_template:
	// Queries the database, and stores the template we wish to use in memory
	/*-------------------------------------------------------------------------*/

	function get_template($name="", $language="")
	{
		global $ibforums, $IB, $DB, $lang;

		//-----------------------------------------
		// Check..
		//-----------------------------------------

		if ($name == "")
		{
			$this->error++;
			$this->fatal_error("A valid email template ID was not passed to the email library during template parsing", "");
		}

		//-----------------------------------------
		// Default?
		//-----------------------------------------

		if ( $ibforums->vars['default_language'] == "")
		{
			$ibforums->vars['default_language'] = 'en';
		}

		if ($language == "")
		{
			$language = $ibforums->vars['default_language'];
		}

		//-----------------------------------------
		// Check and get
		//-----------------------------------------

		if ( ! file_exists($this->root_path."lang/$language/lang_email_content.php") )
		{
			if ( ! is_array( $lang ) )
			{
				$lang = array();
			}

			require_once( $this->root_path."lang/".$ibforums->vars['default_language']."/lang_email_content.php" );
		}
		else
		{
			if ( ! is_array( $lang ) )
			{
				$lang = array();
			}

			require_once( $this->root_path."lang/$language/lang_email_content.php" );
		}

		//-----------------------------------------
		// Stored KEY?
		//-----------------------------------------

		if ( ! isset($lang[ $name ]) )
		{
			if ( $language == $ibforums->vars['default_language'] )
			{
				$this->fatal_error("Could not find an email template with an ID of '$name'", "");
			}
			else
			{
				require_once( $this->root_path."lang/".$ibforums->vars['default_language']."/lang_email_content.php" );

				if ( ! isset($lang[ $name ]) )
				{
					$this->fatal_error("Could not find an email template with an ID of '$name'", "");
				}
			}
		}

		//-----------------------------------------
		// Subject?
		//-----------------------------------------

		if ( isset( $lang[ 'subject__'. $name ] ) )
		{
			$this->lang_subject = stripslashes($lang[ 'subject__'. $name ]);
		}

		//-----------------------------------------
		// Return
		//-----------------------------------------

		$this->template = stripslashes($lang['header']) . stripslashes($lang[ $name ]) . stripslashes($lang['footer']);
	}

	/*-------------------------------------------------------------------------*/
	// build_message:
	// Swops template tags into the corresponding string held in $words array.
	// Also joins header and footer to message and cleans the message for sending
	/*-------------------------------------------------------------------------*/

	function build_message($words)
	{
		global $ibforums;

		if ($this->template == "")
		{
			$this->error++;
			$this->fatal_error("Could not build the email message, no template assigned", "Make sure a template is assigned first.");
		}

		$this->message = $this->template;

		// Add some default words

		$words['BOARD_ADDRESS'] = $ibforums->vars['board_url'] . '/index.' . $ibforums->vars['php_ext'];
		$words['WEB_ADDRESS']   = $ibforums->vars['home_url'];
		$words['BOARD_NAME']    = $ibforums->vars['board_name'];
		$words['SIGNATURE']     = $ibforums->vars['signature'];

		// Swop the words

		$this->message = preg_replace( "/<#(.+?)#>/e", "\$words[\\1]", $this->message );

		$this->message = $this->clean_message( $this->message );

	}


	/*-------------------------------------------------------------------------*/
	// clean_message: (Mainly used internally)
	// Ensures that \n and <br> are converted into CRLF (\r\n)
	// Also unconverts some BBCode
	/*-------------------------------------------------------------------------*/

	function clean_message($message = "" ) {

		$message = preg_replace( "/^(\r|\n)+?(.*)$/", "\\2", $message );

		$message = preg_replace( "#<b>(.+?)</b>#" , "\\1", $message );
		$message = preg_replace( "#<i>(.+?)</i>#" , "\\1", $message );
		$message = preg_replace( "#<s>(.+?)</s>#" , "--\\1--", $message );
		$message = preg_replace( "#<u>(.+?)</u>#" , "-\\1-"  , $message );

		$message = preg_replace( "#<!--emo&(.+?)-->.+?<!--endemo-->#", "\\1" , $message );

		$message = preg_replace( "#<!--c1-->(.+?)<!--ec1-->#", "\n\n------------ CODE SAMPLE ----------\n"  , $message );
		$message = preg_replace( "#<!--c2-->(.+?)<!--ec2-->#", "\n-----------------------------------\n\n"  , $message );

		$message = preg_replace( "#<!--QuoteBegin-->(.+?)<!--QuoteEBegin-->#"                       , "\n\n------------ QUOTE ----------\n" , $message );
		$message = preg_replace( "#<!--QuoteBegin--(.+?)\+(.+?)-->(.+?)<!--QuoteEBegin-->#"         , "\n\n------------ QUOTE ----------\n" , $message );
		$message = preg_replace( "#<!--QuoteEnd-->(.+?)<!--QuoteEEnd-->#"                           , "\n-----------------------------\n\n" , $message );

		$message = preg_replace( "#<!--Flash (.+?)-->.+?<!--End Flash-->#"                         , "(FLASH MOVIE)" , $message );
		$message = preg_replace( "#<img src=[\"'](\S+?)['\"].+?".">#"                                  , "(IMAGE: \\1)"   , $message );
		$message = preg_replace( "#<a href=[\"'](http|https|ftp|news)://(\S+?)['\"].+?".">(.+?)</a>#"  , "\\1://\\2"     , $message );
		$message = preg_replace( "#<a href=[\"']mailto:(.+?)['\"]>(.+?)</a>#"                       , "(EMAIL: \\2)"   , $message );

		$message = preg_replace( "#<!--sql-->(.+?)<!--sql1-->(.+?)<!--sql2-->(.+?)<!--sql3-->#i"    , "\n\n--------------- SQL -----------\n\\2\n----------------\n\n", $message);
		$message = preg_replace( "#<!--html-->(.+?)<!--html1-->(.+?)<!--html2-->(.+?)<!--html3-->#i", "\n\n-------------- HTML -----------\n\\2\n----------------\n\n", $message);

		$message = preg_replace( "#<!--EDIT\|.+?\|.+?-->#" , "" , $message );

		//-----------------------------------------
		// Bear with me...
		//-----------------------------------------

		$message = str_replace( "\n"          , "<br />", $message );
		$message = str_replace( "\r"          , ""      , $message );

		$message = str_replace( "<br>"        , "\r\n", $message );
		$message = str_replace( "<br />"      , "\r\n", $message );
		$message = preg_replace( "#<.+?".">#" , ""    , $message );

		$message = str_replace( "&quot;", "\"", $message );
		$message = str_replace( "&#092;", "\\", $message );
		$message = str_replace( "&#036;", "\$", $message );
		$message = str_replace( "&#33;" , "!" , $message );
		$message = str_replace( "&#39;" , "'" , $message );
		$message = str_replace( "&lt;"  , "<" , $message );
		$message = str_replace( "&gt;"  , ">" , $message );
		$message = str_replace( "&#124;", '|' , $message );
		$message = str_replace( "&amp;" , "&" , $message );
		$message = str_replace( "&#58;" , ":" , $message );
		$message = str_replace( "&#91;" , "[" , $message );
		$message = str_replace( "&#93;" , "]" , $message );
		$message = str_replace( "&#064;", '@' , $message );
		$message = str_replace( "&#60;" , '<' , $message );
		$message = str_replace( "&#62;" , '>' , $message );
		$message = str_replace( "&nbsp;" , ' ' , $message );

		return $message;
	}

	/*-------------------------------------------------------------------------*/
	// FATAL ERROR : LOG AND RETURN
	/*-------------------------------------------------------------------------*/

	function fatal_error($msg, $help="")
	{
		global $DB, $std, $ibforums;

		$DB->do_insert( 'mail_error_logs',
						array(
								'mlog_date'     => time(),
								'mlog_to'       => $this->to,
								'mlog_from'     => $this->from,
								'mlog_subject'  => $this->subject,
								'mlog_content'  => substr( $this->message, 0, 200 ),
								'mlog_msg'      => $msg,
								'mlog_code'     => $this->smtp_code,
								'mlog_smtp_msg' => $this->smtp_msg
							 )
					  );

		return;
	}


	/*-------------------------------------------------------------------------*/
	//
	// SMTP methods
	//
	/*-------------------------------------------------------------------------*/

	//-----------------------------------------
	//| get_line()
	//|
	//| Reads a line from the socket and returns
	//| CODE and message from SMTP server
	//|
	//-----------------------------------------

	function smtp_get_line()
	{
		$this->smtp_msg = "";

		while ( $line = fgets( $this->smtp_fp, 515 ) )
		{
			$this->smtp_msg .= $line;

			if ( substr($line, 3, 1) == " " )
			{
				break;
			}
		}
	}

	//-----------------------------------------
	//| send_cmd()
	//|
	//| Sends a command to the SMTP server
	//| Returns TRUE if response, FALSE if not
	//|
	//-----------------------------------------

	function smtp_send_cmd($cmd)
	{
		$this->smtp_msg  = "";
		$this->smtp_code = "";

		fputs( $this->smtp_fp, $cmd."\r\n" );

		$this->smtp_get_line();

		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );

		return $this->smtp_code == "" ? FALSE : TRUE;
	}

	//-----------------------------------------
	//| error()
	//|
	//| Returns SMTP error to our global
	//| handler
	//|
	//-----------------------------------------

	function smtp_error($err = "")
	{
		$this->smtp_msg = $err;
		$this->fatal_error( $err );
		return;
	}

	//-----------------------------------------
	//| crlf_encode()
	//|
	//| RFC 788 specifies line endings in
	//| \r\n format with no periods on a
	//| new line
	//-----------------------------------------

	function smtp_crlf_encode($data)
	{
		$data .= "\n";
		$data  = str_replace( "\n", "\r\n", str_replace( "\r", "", $data ) );
		$data  = str_replace( "\n.\r\n" , "\n. \r\n", $data );

		return $data;
	}

	//-----------------------------------------
	//| send_mail
	//|
	//| Does the bulk of the email sending
	//-----------------------------------------

	//$this->to, $this->subject, $this->message, $this->mail_headers

	function smtp_send_mail()
	{
		$this->smtp_fp = @fsockopen( $this->smtp_host, intval($this->smtp_port), $errno, $errstr, 30 );

		if ( ! $this->smtp_fp )
		{
			$this->smtp_error("Could not open a socket to the SMTP server");
			return;
		}

		$this->smtp_get_line();

		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );

		if ( $this->smtp_code == 220 )
		{
			$data = $this->smtp_crlf_encode( $this->mail_headers."\n" . $this->message);

			//-----------------------------------------
			// HELO!, er... HELLO!
			//-----------------------------------------

			$this->smtp_send_cmd("HELO ".$this->smtp_host);

			if ( $this->smtp_code != 250 )
			{
				$this->smtp_error("HELO");
				return;
			}

			//-----------------------------------------
			// Do you like my user!
			//-----------------------------------------

			if ($this->smtp_user and $this->smtp_pass)
			{
				$this->smtp_send_cmd("AUTH LOGIN");

				if ( $this->smtp_code == 334 )
				{
					$this->smtp_send_cmd( base64_encode($this->smtp_user) );

					if ( $this->smtp_code != 334  )
					{
						$this->smtp_error("Username not accepted from the server");
						return;
					}

					$this->smtp_send_cmd( base64_encode($this->smtp_pass) );

					if ( $this->smtp_code != 235 )
					{
						$this->smtp_error("Password not accepted from the server");
						return;
					}
				}
				else
				{
					$this->smtp_error("This server does not support authorisation");
					return;
				}
			}

			//-----------------------------------------
			// We're from MARS!
			//-----------------------------------------

			if ( $this->wrap_brackets )
			{
				if ( ! preg_match( "/^</", $this->from ) )
				{
					$this->from = "<".$this->from.">";
				}
			}

			$this->smtp_send_cmd("MAIL FROM:".$this->from);

			if ( $this->smtp_code != 250 )
			{
				$this->smtp_error();
				return;
			}

			$to_arry = array( $this->to );

			if ( count( $this->bcc ) > 0 )
			{
				foreach ($this->bcc as $bcc)
				{
					if ( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", str_replace( " ", "", $bcc ) ) )
					{
						$to_arry[] = $bcc;
					}
				}
			}

			//-----------------------------------------
			// You are from VENUS!
			//-----------------------------------------

			foreach( $to_arry as $to_email )
			{
				if ( $this->wrap_brackets )
				{
						$this->smtp_send_cmd("RCPT TO:<".$to_email.">");
				}
				else
				{
					$this->smtp_send_cmd("RCPT TO:".$to_email);
				}

				if ( $this->smtp_code != 250 )
				{
					$this->smtp_error("Incorrect email address: $to_email");
					return;
					break;
				}
			}

			//-----------------------------------------
			// SEND MAIL!
			//-----------------------------------------

			$this->smtp_send_cmd("DATA");

			if ( $this->smtp_code == 354 )
			{
				//$this->smtp_send_cmd( $data );
				fputs( $this->smtp_fp, $data."\r\n" );
			}
			else
			{
				$this->smtp_error("Error on write to SMTP server");
				return;
			}

			//-----------------------------------------
			// GO ON, NAFF OFF!
			//-----------------------------------------

			$this->smtp_send_cmd(".");

			if ( $this->smtp_code != 250 )
			{
				$this->smtp_error();
				return;
			}

			$this->smtp_send_cmd("quit");

			if ( $this->smtp_code != 221 )
			{
				$this->smtp_error();
				return;
			}

			//-----------------------------------------
			// Tubby-bye-bye!
			//-----------------------------------------

			@fclose( $this->smtp_fp );
		}
		else
		{
			$this->smtp_error();
			return;
		}
	}

}

?>