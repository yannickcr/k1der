<?php

/** ANSIname => ANSIcode LUT */
$ANSINAME2CODE  = array ('reset'     => '\e[0m',    'bold'       => '\e[1m',
                         'underline' => '\e[4m',    'blink'      => '\e[5m',
                         'reverse'   => '\e[7m',    'invisible'  => '\e[8m',
                         'black'     => '\e[0;30m', 'darkgrey'   => '\e[1;30m',
                         'red'       => '\e[0;31m', 'lightred'   => '\e[1;31m',
                         'green'     => '\e[0;32m', 'lightgreen' => '\e[1;32m',
                         'brown'     => '\e[0;33m', 'yellow'     => '\e[1;33m',
                         'blue'      => '\e[0;34m', 'lightblue'  => '\e[1;34m',
                         'purple'    => '\e[0;35m', 'magenta'    => '\e[1;35m',
                         'cyan'      => '\e[0;36m', 'lightcyan'  => '\e[1;36m',
                         'grey'      => '\e[0;37m', 'white'      => '\e[1;37m',
                         'bgblack'   => '\e[40m',   'bgred'      => '\e[41m',
                         'bggreen'   => '\e[42m',   'bgyellow'   => '\e[43m',
                         'bgblue'    => '\e[44m',   'bgmagenta'  => '\e[45m',
                         'bgcyan'    => '\e[46m',   'bgwhite'    => '\e[47m'
                        );

/** BBColor => ANSIname LUT */
$BBCOLOR2ANSI    = array('skyblue'   => 'blue',     'royalblue' => 'blue',
                         'blue'      => 'blue',     'darkblue'  => 'blue',
                         'orange'    => 'red',      'orangered' => 'red',
                         'crimson'   => 'red',      'red'       => 'lightred',
                         'firebrick' => 'red',      'darkred'   => 'red',
                         'green'     => 'green',    'limegreen' => 'green',
                         'seagreen'  => 'green',    'darkgreen' => 'green',
                         'deeppink'  => 'magenta',  'tomato'    => 'red',
                         'coral'     => 'cyan',     'purple'    => 'purple',
                         'indigo'    => 'blue',     'burlywood' => 'red',
                         'sandybrown'=> 'red',      'sierra'    => 'sierra',
                         'chocolate' => 'brown',    'teal'      => 'teal',
                         'silver'    => 'white',
                         'black'     => 'black',    'yellow'    => 'yellow',
                         'magenta'   => 'magenta',  'cyan'      => 'cyan',
                         'white'     => 'white'
                        );

/** ANSInames => BBCode LUT */
$ANSINAME2BBCODE = array('bold' => 'B', 'underline' => 'U', 'reverse' => 'I',

                         'red'    => 'COLOR=red',      'blue'   => 'COLOR=blue',
                         'green'  => 'COLOR=green',    'cyan'   => 'COLOR=cyan',
                         'magenta'=> 'COLOR=deeppink', 'purple' => 'COLOR=purple',
                         'black'  => 'COLOR=black',    'white'  => 'COLOR=white',                        
                         'yellow' => 'COLOR=yellow',   'brown'  => 'COLOR=chocolate'
                        );                      

/** Fixed width for alignments */
//$width           = 80;


/*============================================================================*/


/**
 * Module supporting conversion of markups between ANSI, BBCode and (X)HTML.
 *
 * This is the initial release of a PHP port of my BBCode module for Ruby.
 * Both modules are part of my homepage at http://cochi.bei.t-online.de/.
 * I tried to adjust the style to the possibilities within PHP and tried
 * to follow the official PEAR standards for code. Furthermore I cleaned
 * up some matching expressions in both versions and adjusted HTML output
 * to return valid XHTML constructs. Also converted the RDoc comments as
 * used for Ruby into PHPDoc comments.
 * 
 * HISTORY:
 *  20021111    Initial version of this port
 *
 * @author      Thomas-Ivo Heinen <cochi@upb.de>
 * @version     20021111-2200
 */
class BBCode {

	// private $def=array(); // PHP5
	var $def=array(); // PHP4

	// function __construct() { // PHP5
	function BBCode() { // PHP4
		global $sql;
		$req=$sql->query('SELECT mot,def FROM mod_definitions');
		while($info=$sql->fetchArray($req)) {
			$this->def['mot'][]=$info['mot'];
			$this->def['def'][]=$info['def'];
		}
	}

    /**
     * Returns the ANSI sequence for given color, if existant.
     * 
     * @param   String      A valid name of a ANSI-convertible color.
     * @returns String      The ANSi sequence for the color.
     * @public
     * @static
     */
    function ansi($colorname) {
        $colorname = trim($colorname);
        return ($GLOBALS[ANSINAME2BBCODE][strtolower($colorname)]);
    }

  
    /**
     * Will strip any BBCode tags from the given string.
     *
     * @param   String      A string with BBCode markup
     * @returns String      The string without BBCode markup
     * @public
     * @static
     */ 
    function strip_bbcode($string) {
        $string = trim($string);
        return (preg_replace('/\[[A-Za-z0-9\/=]+\]/', '', $string));
    }


    /**
     * Returns the string with all ansi escape sequences converted to BBCodes.
     * 
     * @param   String      A ANSI-sequence marked string.
     * @returns String      The string with BBCode markup.
     * @public
     * @static
     */
    function ansi_to_bbcode($string) {
        $string = trim($string);
        if (is_null($string) || empty($string)) return ('');
        $result = '';
        $tagstack = array();

        // Iterate over input lines
        foreach( explode('\n', $string) as $string) {
            preg_match('/\e\[[0-9;]+m/', $string, $ansi);
            if (is_null($ansi) || empty($ansi)) continue;

            $temp = array_flip($GLOBALS[ANSINAME2CODE]);

            // Iterate over found ansi sequences
            foreach($ansi as $seq) {
                $ansiname = $temp[ str_replace(chr(27), '\e', $seq) ];
                $bbname = '';

                // Pop last tag and form closing tag
                if ($ansiname == 'reset') {
                    $lasttag = array_pop($tagstack);

                    $temp2 = explode('=', $lasttag);
                    $bbname = '/' . $temp2[0];
                }

                // Get corresponding BBCode tag + Push to stack
                else {
                    $bbname = $GLOBALS[ANSINAME2BBCODE][$ansiname];
                    array_push($tagstack, $bbname);
                }

                // Replace ansi sequence by BBCode tag
                $replace = sprintf('[%s]', $bbname);
                $string = preg_replace( '/' . preg_quote($seq) . '/', $replace, $string );
            }

            // Append converted line
            $result .= sprintf('%s\n', $string); 
        }
      
        // Some tags are unclosed 
        while (! empty($tagstack)) {
            $temp2 = explode('=', array_pop($tagstack));
            $result .= sprintf('[/%s]', $temp2[0]); 
        }

        return ($result); 
    }


    /**
     * Returns the string with all formatting instructions in BBCodes 
     * converted to ANSI code sequences / aligned with spaces to specified 
     * width.
     * 
     * @param   String      A BBCode marked string.
     * @returns String      The string with ANSI sequences.
     * @public
     * @static
     */
    function bbcode_to_ansi($string, $usecolors = true) {
        $string = trim($string);
        if (is_null($string) || empty($string)) return '';
        $result = '';

        if (! $usecolors) return BBCode::strip_bbcode($string);

        // Iterate over lines
        foreach( explode('\n', $string) as $string) {

            // TODO: stacking? other styles!
            foreach( $GLOBALS[ANSINAME2BBCODE] as $key=>$val) {
                $string = preg_replace('/\[' . $val . '\]/', $GLOBALS[ANSINAME2CODE][$key], $string);
                $string = preg_replace('/\[\/' . $val . '\]/', $GLOBALS[ANSINAME2CODE]['reset'], $string);
            }

            // Fonttypes and sizes not available
            $string = preg_replace('/\[SIZE=\d\]/', '', $string);
            $string = preg_replace('/\[\/SIZE\]/', '', $string);
            $string = preg_replace('/\[FONT=[^\]]*\]/', '', $string);
            $string = preg_replace('/\[\/FONT\]/', '', $string);
    
            // Color-mapping
            preg_match('/\[COLOR=(.*?)\]/i', $string, $colors);
            foreach($colors as $col) {
                $name = $GLOBALS[BBCOLOR2ANSI][strtolower($col)];
                if (empty($name)) $name = $GLOBALS[BBCOLOR2ANSI]['white'];
                $code = $GLOBALS[ANSINAME2CODE][$name];
   
                $string = preg_replace('/\[COLOR=' . $col . '\]/i', $code); 
            }
            $string = str_replace('[/COLOR]', $GLOBALS[ANSINAME2CODE]['reset'], $string);

            // TODO: Alignment
            // TODO: IMGs
            // TODO: EMAILs
            // TODO: URLs
            // TODO: QUOTEs
            // TODO: LISTs

            $result .= sprintf('%s\n', $string);
        }

        return ($result);
    }


    /**
     * Returns the (X)HTML markup string as BBCode.
     * 
     * @param   String      A (X)HTML marked string.
     * @returns String      The string with BBCode
     * @public
     * @static
     */
    function html_to_bbcode($string) {
        $string = trim($string);
        if (is_null($string) || empty($string)) return ('');

        // Iterate over lines
            $styles = array('strong'	=> 'b', 'b' => 'b',
                            'em'		=> 'i', 'i' => 'i',
                            'u'			=> 'u', 's' => 's',
							'del' => 's', 'strike' => 's');
            // preserve B, I, U
            foreach( $styles as $html => $code) {
                $string = preg_replace('/<' . $html . '>/i', '[' . strtoupper($code) .']', $string);
                $string = preg_replace('/<\/' . $html . '>/i', '[/' . strtoupper($code) .']', $string);
            }

            // TODO: COLORs
            // TODO: SIZEs
            $string = preg_replace('#<span style="font-size:(.*?)em;">(.*?)<\/span>#is','[SIZE=$1]$2[/SIZE]',$string);
            $string = preg_replace('#<span style="color:(.*?);">(.*?)<\/span>#is','[COLOR=$1]$2[/COLOR]',$string);
           // $string = preg_replace('//i','[/SIZE]',$string);
            // TODO: FONTs

            // EMAIL
            $string = preg_replace('/<a +href *= *"mailto:(.*?)".*?>.*?<\/a>/i', '[EMAIL]\\1[/EMAIL]', $string);

            // URL
            $string = preg_replace('/<a +href *= *"((?:https?|ftp):\/\/.*?)".*?>(.*?)<\/a>/i', '[URL=\\1]\\2[/URL]', $string);

            // Other refs + closing tags => throw away
            $string = preg_replace('/<a +href *= *".*?".*?>/i', '', $string);
            $string = preg_replace('/<\/a>/i',            '', $string);

            // IMG
            $string = preg_replace('/<img +src *= *"(.*?)".*?\/?>/i', '[IMG=\\1]', $string);

            // CENTER (right/left??)
            $string = preg_replace('/<center>/i','[ALIGN=center]',$string);
            $string = preg_replace('/<\/center>/i','[/ALIGN]',$string);

            // QUOTE
           //if(preg_match('/\[B\]Citation de (.*) :\[\/B\]\n<div class="quote">/i',$string)) echo 'ok';
		 // echo $string; exit();
			$string = preg_replace('#\[B\]Citation de (.*) :\[\/B\]<br \/>\n<div class="quote">#is', '[QUOTE=\\1]', $string);
            $string = preg_replace('#<div class="quote">#is','[QUOTE]',$string);
			$string = preg_replace('/<\/div>/i','[/QUOTE]',$string);

			// Texte citation de...
            $string = preg_replace('/\n\[B\]Citation :\[\/B\]/i', '', $string);
			
			// Texte code
            $string = preg_replace('/\n\[B\]Code :\[\/B\]/i', '', $string);

            // LIST
            $string = preg_replace('/<ul>/i',   '[LIST]',  $string);
            $string = preg_replace('/<\/ul>/i', '[/LIST]', $string);
            $string = preg_replace('/<li *\/?> */i', '[*] ', $string);

	
            // Unknown tags => throw away
            $string = preg_replace('/<.*? *\/?>/', '', $string);
       
            //$result .= sprintf('%s<br />\n', $string);

        return (preg_replace('/<br *\/?>/i', '\n', $string));
    }

    /**
     * Returns the string with all formatting instructions in BBCodes 
     * converted to XHTML markups
     * 
     * @param   String      A BBCode marked string.
     * @returns String      The string with XHTML.
     * @public
     * @static
     */
    function bbcode_to_html($string) {
		global $site;
        $string = ' '.str_replace("\r",'',$string);
        if (is_null($string) || empty($string)) return ('');
        $result = '';
		$ducode=0;

            $styles = array( 'b' => 'strong', 'i' => 'em','s' => 'del','del' => 'del', 'strike' => 'del');

            // preserve B, I
            foreach( $styles as $code => $html) {
                $string = preg_replace('#\['.$code.'\](.+?)\[/'.$code.'\]#is','<'.$html.'>$1</'.$html.'>',$string);
            }

			// SMILEYS
			$smileys=unserialize($site->config('forum_smileys'));
			foreach($smileys as $code => $image) {
				$code=preg_quote($code,'/');
				$string=preg_replace( "!(?<=[^\w&;/])".$code."!",'<img style="vertical-align:middle;border:0px;" alt="'.stripslashes($code).'" src="templates/'.THEME.'/images/forum/smileys/'.$image.'"/>',$string);
			}
            // U
            $string = preg_replace('#\[u\](.+?)\[/u\]#is','<span style="text-decoration:underline;">$1</span>',$string);

            // COLOR => font color=... (TODO: should be numeric!)
            $string = preg_replace('/\[COLOR=(.*?)\]/i', '<span style="color:\\1;">', $string);
            $string = preg_replace('/\[\/COLOR\]/i',     '</span>',              $string);

            // SIZE => font size=...
            $string = preg_replace('/\[SIZE=(.*?)\]/i', '<span style="font-size:\\1em;">', $string);
            $string = preg_replace('/\[\/SIZE\]/i',     '</span>',             $string);

            // URL
            $string = preg_replace('/\[URL TITLE="(.*?)"\]([^\[]+?)\[\/URL\]/i',   '<a href="\\2" title="\\1">\\2</a>', $string);
            $string = preg_replace('/\[URL="(.*?)" TITLE="(.*?)"\](.+?)\[\/URL\]/i', '<a href="\\1" title="\\2">\\3</a>', $string);
            $string = preg_replace('/\[URL\]([^\[]+?)\[\/URL\]/i',   '<a href="\\1">\\1</a>', $string);
            $string = preg_replace('/\[URL="(.*?)"\](.+?)\[\/URL\]/i', '<a href="\\1">\\2</a>', $string);
            $string = preg_replace('/\[URL=(.*?)\](.+?)\[\/URL\]/i', '<a href="\\1">\\2</a>', $string);

            // IMG
            $string = preg_replace_callback('/\[IMG=(.*?)\]/i','callback_img', $string);
			
			// DEF
            $string = preg_replace('/\[DEF=(.*?)\](.+?)\[\/DEF\]/i', '<span class="smarttag" title="\\1">\\2</span>', $string);
            
            // ALIGN=center (TODO: right, left)
            $string = preg_replace('/\[ALIGN=center\]/i', '<div style="text-align:center;">',  $string);
            $string = preg_replace('/\[ALIGN=left\]/i',   '<div style="text-align:left;">',  $string);
            $string = preg_replace('/\[ALIGN=right\]/i',  '<div style="text-align:right;">',  $string);
			
            $string = preg_replace('/\[ALIGN=centrer\]/i', '<div style="text-align:center;">',  $string);
            $string = preg_replace('/\[ALIGN=gauche\]/i',   '<div style="text-align:left;">',  $string);
            $string = preg_replace('/\[ALIGN=droite\]/i',  '<div style="text-align:right;">',  $string);
            $string = preg_replace('/\[\/ALIGN\]/i',      '</div>', $string);

            // QUOTE
			$quote=0;
            if (preg_match('/\[QUOTE\]/i',$string) || preg_match('/\[QUOTE=(.*?)]/i',$string)) $quote+=1;
            if ((preg_match('/\[QUOTE\]/i',$string) || preg_match('/\[QUOTE=(.*?)]/i',$string)) && ($quote > -1)) $quote-=1;
            $string = preg_replace('/\[QUOTE\]/i',"<strong style=\"font-size:0.85em\">Citation</strong>\n<div class=\"quote\">",$string);
            $string = preg_replace('/\[QUOTE=(.*?)]/i',"<span style=\"font-size:0.85em\"><strong>Citation</strong> de $1</span>\n<div class=\"quote\">",$string);
            $string = preg_replace('/\[\/QUOTE\]/i','</div>',$string);
            if ($quote > 0) {
                $string = preg_replace('/^/', str_repeat('&#62;', $quote), $string);
            }

			// CODE
			if(!function_exists('callback_code')) {
			
			}
			$string = preg_replace_callback('`\[code\](.+?)\[/code\]`si','callback_code', $string); 
			
            // EMAIL
            $string = preg_replace('/\[EMAIL\](.*?)\[\/EMAIL\]/i', '<a href="mailto:\\1">\\1</a>', $string);

            // LIST (TODO: LIST=1, LIST=A)
            $string = preg_replace('/\[LIST(?:=(.*?))?\]\n/i', '<ul>',  $string);
            $string = preg_replace('/\[\/LIST\]\n/i',          '</ul>', $string);
            $string = preg_replace('/\[\*\](.*)\n/i',              '<li>$1</li>',  $string);
			
            // FONT => font ??????
            // ?BLUR?, FADE?
        return nl2br(trim($string));
    }
  

/*----------------------------------------------------------------------------*/
// Transitive methods


    /**
     * Returns the string with ANSI code sequences converted to XHTML markup.
     * 
     * @param   String      A ANSI-sequence marked string.
     * @returns String      The string with XHTML.
     * @public
     * @static
     */
    function ansi_to_html($string) {
        $bbcoded = BBCode::ansi_to_bbcode($string );
        $htmled  = BBCode::bbcode_to_html($bbcoded);
    
        return ($htmled);
    } 


    /**
     * Returns the (X)HTML markup code as ANSI sequences.
     * 
     * @param   String      A (X)HTML marked string.
     * @returns String      The string with ANSI sequences.
     * @public
     * @static
     */
    function html_to_ansi($string) {
        $bbcoded = BBCode::ansi_to_bbcode($string );
        $ansied  = BBCode::bbcode_to_ansi($bbcoded);

        return ($ansied);
    }
	
	function addTitles($string) {
        $string = preg_replace_callback('/\[URL\]([^\[]+?)\[\/URL\]/i',   'callback_url', $string);
        $string = preg_replace_callback('/\[URL=(.*?)\](.+?)\[\/URL\]/i', 'callback_url2', $string);
		return $string;
	}
	function stripTitles($string) {
        $string = preg_replace('/\[URL TITLE="(.*?)"\]([^\[]+?)\[\/URL\]/i',   '[URL]\\1[/URL]', $string);
        $string = preg_replace('/\[URL="(.*?)" TITLE="(.*?)"\](.+?)\[\/URL\]/i', '[URL=\\1]\\3[/URL]', $string);
		return $string;
	}
	
	function addDefs($string) {
		$texte=$string;
		$tab1=array('[',']','(',')');
		$tab2=array('<','>','');
		$texte=str_replace($tab1,$tab2,$texte);
		$texte=strip_tags($texte);
		while(ereg('  ',$texte)) $texte=str_replace('  ',' ',$texte);
		$tab=preg_split("/[\s,]+/",$texte);
		foreach($tab as $mot) {
			$index=array_search($mot,$this->def['mot']);
			if($index!==false) $string=str_replace($mot,'[DEF='.$this->def['def'][$index].']'.$mot.'[/DEF]',$string);
		}
		return $string;
	}
}


function callback_code($match) {
	/*require_once('include/librairies/geshi.inc.php');
	$geshi = new GeSHi($match[1],'php');
	$geshi->set_header_type(GESHI_HEADER_DIV);
	$geshi->set_overall_class('code');
	$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 0);
	return '<strong style="font-size:0.85em">Code :</strong>'.str_replace("\n",'',$geshi->parse_code());*/
    $code        = explode('<br />',nl2br($match[1]));
    $num_lignes      = 1;
	$out = '<strong style="font-size:0.85em">Code :</strong><div class="code"><ol>';
    foreach ($code as $code_line) {
        $out .= '<li>'.htmlentities($code_line).'</li>';
		$num_lignes++;
    }
	$out .= '</ol></div>';

    return str_replace("\n",'',$out);
}
function callback_img($match) {
	$alt=pathinfo($match[1]);
	return '<img style="border:0px;margin:5px;" src="'.htmlentities($match[1]).'" alt="'.str_replace('.'.$alt['extension'],'',$alt['basename']).'" />';
}

function callback_url($match) {
	return '[URL]'.$match[1].'[/URL]'; // OFFLINE MODE
	$url = parse_url($match[1]);
	
	if(!in_array($url['scheme'],array('','http')))
	   return '[URL]'.$match[1].'[/URL]';
	
	$fp = @fsockopen ($url['host'], (isset($url['port']) && $url['port'] > 0 ? $url['port'] : 80), $errno, $errstr,1);
	if (!$fp) {
	   return '[URL]'.$match[1].'[/URL]';
	} else {
	   fputs ($fp, "GET /".(isset($url['path']) ? '?'.$url['path'] : '').(isset($url['query']) ? '?'.$url['query'] : '')." HTTP/1.0\r\nHost: ".$url['host']."\r\n\r\n");
	   $d = '';
	   while (!feof($fp)) {
		   $d .= fgets ($fp,2048);
		   if(preg_match('~(</head>|<body>|(<title>\s*(.*?)\s*</title>))~i', $d, $m)) break;
	   }
	   fclose ($fp);
	   return '[URL TITLE="'.$m[3].'"]'.$match[1].'[/URL]';
	}
}
function callback_url2($match) {
	return '[URL="'.$match[1].'"]'.$match[2].'[/URL]'; // OFFLINE MODE
	$url = parse_url($match[1]);
	if(!isset($url['scheme']) || !in_array($url['scheme'],array('','http')))
	   return '[URL="'.$match[1].'"]'.$match[2].'[/URL]';
	
	$fp = @fsockopen ($url['host'], (isset($url['port']) && $url['port'] > 0 ? $url['port'] : 80), $errno, $errstr,1);
	if (!$fp) {
	   return '[URL="'.$match[1].'"]'.$match[2].'[/URL]';
	} else {
	   fputs ($fp, "GET /".(isset($url['path']) ? '?'.$url['path'] : '').(isset($url['query']) ? '?'.$url['query'] : '')." HTTP/1.0\r\nHost: ".(isset($url['host']) ? $url['host'] : '')."\r\n\r\n");
	   $d = '';
	   while (!feof($fp)) {
		   $d .= fgets ($fp,2048);
		   if(preg_match('~(</head>|<body>|(<title>\s*(.*?)\s*</title>))~i', $d, $m)) break;
	   }
	   fclose ($fp);
	   return '[URL="'.$match[1].'" TITLE="'.$m[3].'"]'.$match[2].'[/URL]';
	}
	/*$site = $match[1];
	$file = @fopen($site, "r");
	if($file==false) return '[URL="'.$match[1].'" TITLE="'.$site.'"]'.$match[2].'[/URL]';
	for($i=0;!feof($file) && $i<10;$i++) {
	   $line = @fgets($file,1024);
	   if (eregi("<title>(.*)</title>", $line, $out)) {
		 $title = $out[1];
		 break;
	   }
	}
	@fclose($file);
	if(empty($title)) $title = $site;
	return '[URL="'.$match[1].'" TITLE="'.$title.'"]'.$match[2].'[/URL]';*/
}
?>
