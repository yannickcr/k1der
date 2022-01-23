<?php  
/** 
* A small piece of code to find out the number of persons in a channel and/or the topic in the channel. 
* It is also able to cache, to reduce annoyance and problems with IRCops ;) 
* Written by Stefan Walk - eMail: mail@etarion.de 
* Free for non-commercial use ;) 
*/  


/** 
* IN ANY CASE: 
* Edit the basic settings, found under "Basic configuration". 
* Make sure the script can properly access its cachefile, which means setting a proper chmod like 666 for 
* the directory and the file. The default name for the cachefile (if you did not 
* change it) is 'countbot_cache.txt', located in the same directory as the script that calls this one. 
* If this script is used standalone, it will be in the same directory as this script. 
* The script will notice improper setup and will not use the cachefile, thus leading to annoyance 
* (the bot joining/quitzing every time someone views the page) and, if too many users 
* view the page in a certain amount of time, will lead the bot to fail because of irc connection limitations. 
* If you want, you can look at the advanced options too... there are pretty nice options such as HTML 
* formatting of the topic, the ident of the bot or the cache lifetime. 
* 
* IF YOU WANT TO INCLUDE THIS SCRIPT INTO YOURS: 
* I don't want to pollute your namespace, so the whole script is written as one function. To access 
* the information do something like this: 
* include_once('./channelinfo.php'); 
* get_channel_info($nicks,$topic); 
* $nicks will now contain the number of users in the channel, and $topic will contain the channel's topic. 
* Easy, isn't it? ;) 
* If you want to specify another channel, use get_channel_info($nicks,$topic,'#channel'); 
* Now edit the basic settings and don't forget to delete the lines mentioned there, and have fun :) 
* 
* IF YOU WANT TO USE THIS SCRIPT AS A STANDALONE SCRIPT: 
* For displaying the number of nicks or the channeltopic directly, for example if you want 
* <?php include('./channelinfo.php') ?> to output the number of nicks in the channel, go to the bottom of 
* the file and read the instructions there. 
* Don't forget to edit the basic settings, either. 
*/  



function get_channel_info(&$number_of_nicks,&$channeltopic,$reg_channel=NULL)   
{  


    /** 
     * Basic configuration: 
     * Edit this to fit your needs. 
     */  
    $irc_server = "de.quakenet.org"; // The server the script connects to. 
    $irc_port = 6667; // The port the script uses. 
    $nick = 'K1der_Site'; // The nick the bot uses. If the nick is in use already, the bot will append a _ until the server 
                     // let's him in :) 
    $channel = ($reg_channel!=NULL)?$reg_channel:'#k1der'; // You might want to change this :p 
      
    // *** Delete the following three lines to make the bot work: *** 
  //  die('<br /><span style="font-size:14px;font-weight:bold;color:red;background-color:white"> 
  //      Edit the channelinfo-file (probably channelinfo.php) first to enter your settings! 
  //      </span><br />');  
    // Don't delete any further :p 


    /** 
     * Advanced Configuration 
     * You don't have to change this, but you might want to. 
     */  
    $ident = 'K1der_Site'; // Appears in the hostmask if the server has no identd running. 
    $realname = 'K1der_Site'; // This will show up if someone does a /whois on the bot. I doubt there will be 
                                  // enough time to to that, though ;) 
    $cache_filename = './countbot_cache.txt'; // The path/file in which the cached information will be stored. 
    $cache_lifetime = 300;  // Time in seconds till the bot joins the IRC again. A smaller number leads to more accurate 
                            // Values, though it will annoy the people in the channel when the bot joins/quits all the 
                            // time. If this value is too low, the bot may not be able to join IRC, so i would STRONGLY 
                            // recommend leaving this value above 120 seconds. 
    $htmlentities = TRUE; // When set to true, special chars will be replaced by HTML-entities (for example, < into &lt; 
                          // to prevent injection of malicious code into your page. (strongly recommended) 
    $colorize_topic     = TRUE; // If set to true, the returned topic will be formatted in HTML so they will be displayed 
                                // like they would in IRC. (mIRC, that is.) 
                                // If set to false, control codes will be stripped from the topic. 
    $default_color      = 1; // Starting color (default mIRC colors are used). 
    $default_background = 0; // Starting background-color. 
    $use_background     = TRUE;      // Whether to use the background or not. 
      
    /** 
     * Execution: 
     * The code part... LEAVE IT ALONE if you don't know what you are doing :p 
     */  
    $LB = "\r\n";  
    $status=FALSE; // Let's assume it does not work. Hey, it's the law! 
    $getnewvalues=FALSE; // WARNING: Do not change this or caching will be disabled. 
    if ((!$getnewvalues) && (!file_exists($cache_filename))) { // I check if the cache-file exists... 
        $getnewvalues=TRUE;  
    }  
    if ((!$getnewvalues) && (!is_readable($cache_filename))) { // I check if the cachefile is readable (should be, if you 
        $getnewvalues=TRUE;                                 // set a proper chmod).    
    }  
    if (!$getnewvalues) {  
        $file = file($cache_filename);        // 
        $file = implode('',$file);  // Get the data from the file 
        if (!($data = unserialize($file))) {  
            $getnewvalues = TRUE; // Bah, you gave me some junk i can't read. 
        } else {  
            $topic = $data['topic']; // The variables are set, even if the data might be outdated - this way, if the 
            $nicks = $data['nicks']; // connection to IRC fails, there is at least some data. 
            if ( $data['timestamp'] < time() - $cache_lifetime ) {   
            $getnewvalues=TRUE; // Data is outdated 
            }  
        }  
    }  
      
    if ($getnewvalues) {  
        if ( !( $irc = fsockopen($irc_server,$irc_port,$errno,$errstr,10) ) ) {  
            $error=$errno.' '.$errstr; // Connection failed. 
                                       // Now let's hope there is some info in the cachefile ;) 
        } else {  
            $disconnected=FALSE;  
            fputs($irc,"USER {$ident} 2 3 :{$realname}".$LB); // Register at the IRC Server 
            fputs($irc,"NICK {$nick}".$LB); // Let's choose a nickname. 
            while ($line=fgets($irc,600)) {  
                if ( preg_match('%^\:\S+ 43[123]%',$line) ) {  
                    $nick.='_'; // Damn, the server did not like my nickname. Let's try a new one. 
                    fputs($irc,"NICK {$nick}".$LB);  
                } elseif ( preg_match('%^PING \:\d+%',$line) ) {  
                   $line{1}='O'; // Playing PING-PONG with the server... 
                   fputs($irc,$line);  
                } elseif ( preg_match('%^\:\S+ 376%',$line) ) {  
                    break; // Hey! The server is done sending me the MOTD. Let's start fetching some data. 
                } elseif ( (feof($irc)) || ($line === FALSE) ) {  
                    $disconnected = TRUE; // Damn! The Server disconnected me. Guess i'll have to live with the cache. 
                    break;  
                } else {  
                    continue; // The server sent me some info we don't care about. 
                }  
            }  
            if (!$disconnected) {  
                fputs($irc,"JOIN {$channel}".$LB); // Joining the channel 
                $topic_matched = FALSE; // 
                $nicks_matched = FALSE; // Initializing some variables... 
                $topic='';              // 
                $nicks=0;               // 
                do {  
                    $line=fgets($irc,1024);  
                    if (preg_match('%^\:\S+ 33[12] '.$nick.' (\S+) :(.*)$%',$line,$matches)) {  
                        $topic = trim($matches[2]); // I GOT MAIL! err, i got the topic. 
                        $topic_matched=TRUE;  
                    } elseif (preg_match('%^\:\S+ 353 '.$nick.' [=\*@] (\S+) :(.*)$%',$line,$matches)) {  
                        $nicks+=count(explode(' ',$matches[2])); // The server sent me a list of nicks, 
                                                                 // so let's count them. 
                    } elseif (preg_match('%^\:\S+ 366 '.$nick.' (\S+) :(.*)$%',$line,$matches)) {  
                        $nicks_matched = TRUE; // The Server just told me he is done sending me nicks. 
                        $nicks--; // I guess you don't care about me *sniff* so im removing me from the number of nicks. 
                    }  
                } while ((!$topic_matched) || (!$nicks_matched));  
                fputs($irc,'QUIT :Nan, de toute fasson vous m\'avez pas vu'.$LB);  
                fclose($irc); //Connection is not needed anymore, so i am removing myself from IRC. 
                $status = TRUE; // Looks like it worked, if we came until here... :D 
            }  
        }  
    }  
    if ( ($status) && ( (!file_exists($cache_filename)) || (is_writable($cache_filename)) ) ) {  
        if ($fp=@fopen($cache_filename,'w')) {  
            $data['topic']     = $topic;  
            $data['nicks']     = $nicks;  
            $data['timestamp'] = time();  
            fputs($fp,serialize($data));  
            fclose($fp);  
        }  
    }  
    switch($htmlentities) {  
    case TRUE:  
        $topic=htmlentities($topic); // Convert special chars to html entities 
        break;  
    case FALSE:  
        // mmmh. nothing. 
        break;  
    }  
    switch($colorize_topic) {  
    case FALSE:  
        $topic=preg_replace('%\x02|\x03\d{0,2}(,\d{1,2})?|\x0f|\x16|\x1f%','',$topic); // Removing the control codes. 
        break;  
    case TRUE:  
        // initializing some variables, creating arrays for more comfort... 
        $temptopic = '';  
        $style_status_default = array(  
            'bold'       => 0,  
            'underline'  => 0,                   // 
            'reverse'    => 0,                   // This is how we start. 
            'color'      => $default_color,      // 
            'background' => $default_background,  
            );  
        $style_status = $style_status_default; // Used to keep track of the status 
        $style = array(  
            'bold' => array (  
                0 => 'normal',  
                1 => 'bold'            // 
                ),                     // Used to transform the binary values in $style_status into css styles. 
            'underline' => array (     // 
                0 => 'none',  
                1 => 'underline'  
                )  
            );  
        $mirc_colors = array(  
             0 => "#FFFFFF",  // 
             1 => "#000000",  // The mIRC Colortable... 
             2 => "#000080",  // 
             3 => "#009300",    
             4 => "#FF0000",  
             5 => "#7F0000",  
             6 => "#9C009C",  
             7 => "#FF8000",  
             8 => "#FFFF00",  
             9 => "#00FF00",  
            10 => "#009393",  
            11 => "#00FFFF",  
            12 => "#0000FF",  
            13 => "#FF00FF",  
            14 => "#7F7F7F",  
            15 => "#D2D2D2"  
            );  
        $topic_parts=preg_split('%(\x02|\x03\d{0,2}(?:,\d{1,2})?|\x0f|\x16|\x1f)%', $topic, -1,(PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) );  
        // this splits the topic into its part. A part can be 
        // a) a control code, maybe followed by comma seperated digits (in case of color codes) 
        // or 
        // b) Text. 
        foreach ($topic_parts as $part) {  
            switch($part{0}) {  
            case "\x02": // "bold" control code 
                $style_status['bold']*=-1; // Swap the bold status 
                $style_status['bold']+=1;  // 
                break;  
            case "\x03": // "color" control code 
                preg_match('%^\x03(\d+)?,?(\d+)?$%',$part,$matches);  
                if (!isset($matches[1])) {                           // No trailing digits, 
                    $style_status['color']=$default_color;           // so.we.reset 
                    $style_status['background']=$default_background; // the colors. 
                } elseif (!isset($matches[2])) {          // One group of digits, 
                    $style_status['color'] = $matches[1]; // so oreground-color gets changed. 
                } else {                                       // Two groups of digits seperated by a comma, 
                    $style_status['color'] = $matches[1];      // so foreground- 
                    $style_status['background'] = $matches[2]; // and background-color get changed. 
                }  
                break;  
            case "\x0f": // "reset" control code 
                $style_status = $style_status_default; // Style gets resetted. Easy. 
                break;  
            case "\x16": // "reverse" control code 
                $style_status['reverse']*=-1;  // Reverse the reverse status. 
                $style_status['reverse']+=1;   // :D 
                break;  
            case "\x1f": // "underline" control code 
                $style_status['underline']*=-1; // Swap the underline status 
                $style_status['underline']+=1;  // 
                break;  
            default: // Text. 
                $color      = ($style_status['reverse']==1)?$default_background:$mirc_colors[$style_status['color']];  
                $background = ($style_status['reverse']==1)?$default_color:$mirc_colors[$style_status['background']];  
                // Reverse overrides color settings (at least it does it in mIRC) 
                $underline  = $style['underline'][$style_status['underline']];  
                $bold       = $style['bold'][$style_status['bold']];  
                if ($use_background) { // self-explanatory. 
                    $temptopic.="<span style=\"font-weight:{$bold};text-decoration:{$underline};color:{$color};background-color:{$background};\">";  
                } else {               // same. 
                    $temptopic.="<span style=\"font-weight:{$bold};text-decoration:{$underline};color:{$color};\">";  
                }  
                $temptopic.=$part.'</span>'; // end of text part, ready for more control codes or output :) 
                break;  
            }  
        }  
        $topic = $temptopic;   
        break;  
    }  
    $number_of_nicks = $nicks; // finally - setting the return values 
    $channeltopic    = $topic; // Now we are done, basically. 
}  



/** 
* NOTES FOR STANDALONE USE: 
* If you want to use this script as standalone, do this: 
* Uncomment (remove the // in front of the line - not the one trailing the command) 
* the get_channel_info() line - the first. Then, if you want to output channel 
* name AND number of nicks, uncomment the second line and change the value of $LB 
* to suit your needs, if you don't like the default value(a linebreak). 
* Last, uncomment the lines doing the output - the "echo $nicks.(isset($LB)?$LB:'');" 
* one if you want to output the number of nicks, and similar for the topic. 
* You can reverse their order too, if you want ;) 
*/  
get_channel_info($nicks,$topic); 
//$LB = "\r\n"; // A new line will be started behind the output of the channel topic / number of nicknames. 
echo $nicks.(isset($LB)?$LB:''); 
//echo $topic.(isset($LB)?$LB:''); 


/** 
* END OF FILE 
* Send a eMail to mail@etarion.de if you like this code or if you have any suggestions. 
*/  
?>