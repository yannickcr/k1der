<?php
/**
 * Classe pour la conversion BBCode<->(X)HTML.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class bbcode {

	private $purify = true;

	/**
	 * Constructeur de la classe BBCode.
	 * Selectionne les dfinitions dans la base SQL et les place dans le tableau $def
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	public function __construct() {
		global $sql;
		$req=$sql->query('SELECT mot,def,type FROM mod_definitions');
		$def=array();
		while($info=$sql->fetchAssoc($req)) {
			$this->def['mot'][]=strtolower($info['mot']);
			$this->def['def'][]=$info['def'];
			$this->def['type'][]=$info['type'];
		}
		if(!$this->purify) return true;
		
		set_include_path('include/scripts/htmlpurifier' . PATH_SEPARATOR . get_include_path());
		require_once 'include/scripts/htmlpurifier/HTMLPurifier.php';
		$this->purifierConfig = HTMLPurifier_Config::createDefault();
		$this->purifierConfig->set('Core', 'TidyFormat', false);
		$this->purifier = new HTMLPurifier($this->purifierConfig);
	}

    /**
     * Supprime le BBCode de la chaine.
     *
	 * @author	Yannick Croissant
	 * @access	public
     * @param	string	Texte avec BBCode
     * @return	string	Texte sans le BBCode
     */ 
    public function stripBBCode($string) {
        $string = trim($string);
		$string = preg_replace('/\[URL=(http\:\/\/)([^\]]*)\]([^\]]*)\[\/URL\]/', '', $string);
		$string = preg_replace('/\[IMG=(http\:\/\/)([^\]]*)\]/', '', $string);
        return (preg_replace('/\[[A-Za-z0-9\/=]+\]/', '', $string));
    }

    /**
	 * Convertit l'(X)HTML de la chaine en BBCode.
     *
	 * @todo a moiti fini, donc... finir
	 * @todo nettoyer et commenter
     * @param	string	Texte avec (X)HTML
     * @return	string	Texte avec le BBCode
	 * @author	Yannick Croissant
	 * @access	public
     */ 
    public function htmlToBBCode($string) {
        $string = trim($string);
        if (is_null($string) || empty($string)) return ('');

        // Trucs simples
            $styles = array('strong'	=> 'b', 'b' => 'b',
                            'em'		=> 'i', 'i' => 'i',
                            'u'			=> 'u', 's' => 's',
							'del' => 's', 'strike' => 's'
							);
            foreach( $styles as $html => $code) {
                $string = preg_replace('/<' . $html . '>/i', '[' . strtoupper($code) .']', $string);
                $string = preg_replace('/<\/' . $html . '>/i', '[/' . strtoupper($code) .']', $string);
            }

            $string = preg_replace('#\<span style=\"font\-size\:(.*?)em;\">(.*?)<\/span>#is','[SIZE=$1]$2[/SIZE]',$string);
            $string = preg_replace('#\<SPAN style=\"font\-size\:(.*?)em;\">(.*?)<\/SPAN>#is','[SIZE=$1]$2[/SIZE]',$string);
            $string = preg_replace('#\<span style=\"color\:(.*?);\">(.*?)<\/span>#is','[COLOR=$1]$2[/COLOR]',$string);
            $string = preg_replace('#\<SPAN style=\"color\:(.*?);\">(.*?)<\/SPAN>#is','[COLOR=$1]$2[/COLOR]',$string);
            $string = preg_replace('#\<span style=\'color\:(.*?);\'>(.*?)<\/span>#is','[COLOR=$1]$2[/COLOR]',$string);
            $string = preg_replace('#\<SPAN style=\'color\:(.*?);\'>(.*?)<\/SPAN>#is','[COLOR=$1]$2[/COLOR]',$string);
            $string = preg_replace('#\<span style=\'color\:(.*?)\'>(.*?)<\/span>#is','[COLOR=$1]$2[/COLOR]',$string);
            $string = preg_replace('#\<SPAN style=\'color\:(.*?)\'>(.*?)<\/SPAN>#is','[COLOR=$1]$2[/COLOR]',$string);

            // EMAIL
            $string = preg_replace('/<a +href *= *"mailto:(.*?)".*?>.*?<\/a>/i', '[EMAIL]\\1[/EMAIL]', $string);
            $string = preg_replace('/<A +href *= *"mailto:(.*?)".*?>.*?<\/A>/i', '[EMAIL]\\1[/EMAIL]', $string);

            // URL
            $string = preg_replace('/<a +href *= *"((?:https?|ftp):\/\/.*?)".*?>(.*?)<\/a>/i', '[URL=\\1]\\2[/URL]', $string);
            $string = preg_replace('/<A +href *= *"((?:https?|ftp):\/\/.*?)".*?>(.*?)<\/A>/i', '[URL=\\1]\\2[/URL]', $string);

            $string = preg_replace('/<a +href *= *".*?".*?>/i', '', $string);
            $string = preg_replace('/<A +href *= *".*?".*?>/i', '', $string);
            $string = preg_replace('/<\/a>/i',            '', $string);
            $string = preg_replace('/<\/A>/i',            '', $string);

            // IMG
            $string = preg_replace('/<img +src *= *"(.*?)".*?\/?>/i', '[IMG=\\1]', $string);
            $string = preg_replace('/<IMG ([^>]*?) +src *= *"(.*?)".*?\/?>/i', '[IMG=\\2]', $string);

            // CENTER
			$string = preg_replace('/<DIV>(.*?)<\/DIV>/i',      '\\1', $string);
			$string = preg_replace('/<div>(.*?)<\/div>/i',      '\\1', $string);
			
            $string = preg_replace('/\<div style=\"text-align\:justify;\">/i', '[ALIGN=left]',  $string);
            $string = preg_replace('/\<div style=\"text-align\:center;\">/i', '[ALIGN=center]',  $string);
            $string = preg_replace('/\<div style=\"text-align\:left;\">/i',   '[ALIGN=left]',  $string);
            $string = preg_replace('/\<div style=\"text-align\:right;\">/i',  '[ALIGN=right]',  $string);
			
            $string = preg_replace('/\<div align=justify>/i', '[ALIGN=left]',  $string);
            $string = preg_replace('/\<div align=center>/i', '[ALIGN=center]',  $string);
            $string = preg_replace('/\<div align=left>/i',   '[ALIGN=left]',  $string);
            $string = preg_replace('/\<div align=right>/i',  '[ALIGN=right]',  $string);
            $string = preg_replace('#<CENTER>([^<]*?)</CENTER>#is',  '[ALIGN=center]\\1[/ALIGN]',  $string);

            $string = preg_replace('/\<DIV align=justify>/i', '[ALIGN=left]',  $string);
            $string = preg_replace('/\<DIV align=center>/i', '[ALIGN=center]',  $string);
            $string = preg_replace('/\<DIV align=left>/i',   '[ALIGN=left]',  $string);
            $string = preg_replace('/\<DIV align=right>/i',  '[ALIGN=right]',  $string);
			
            $string = preg_replace('/<\/div\>/i',      '[/ALIGN]', $string);
            $string = preg_replace('/<\/DIV\>/i',      '[/ALIGN]', $string);
			
           /* $string = preg_replace('/\<P align=center>/i', '[ALIGN=center]',  $string);
            $string = preg_replace('/\<P align=left>/i',   '[ALIGN=left]',  $string);
            $string = preg_replace('/\<P align=right>/i',  '[ALIGN=right]',  $string);
            $string = preg_replace('/<\/P\>/i',      '[/ALIGN]', $string);*/
            // QUOTE
            $string = preg_replace('#<span style="font-size:0.85em"><strong>Citation<\/strong> de (.*?)<\/span><br /><blockquote><p>#i','[QUOTE=\\1]',$string);
            $string = preg_replace('/\<blockquote>\<p>/is','[QUOTE]',$string);
			$string = preg_replace('/\<\/p>\<\/blockquote>/i','[/QUOTE]',$string);


			// Texte citation de...
            $string = preg_replace('#\<!--QuoteBegin-->([^!]*?)<!--QuoteEBegin-->([^<]*?)<!--QuoteEnd-->([^!]*?)<!--QuoteEEnd-->#is','[QUOTE]$2[/QUOTE]',$string);
			
            $string = preg_replace('/\n\[B\]Citation :\[\/B\]/i', '', $string);
            $string = preg_replace('/\n\[B\]Quote :\[\/B\]/i', '[B]Citation :\[/B]', $string);
			
			// Texte code
            $string = preg_replace('/\n\[B\]Code :\[\/B\]/i', '', $string);

            // LIST
            $string = preg_replace('/<ul>/i',   '[LIST]',  $string);
            $string = preg_replace('/<\/ul>/i', '[/LIST]', $string);
            $string = preg_replace('/<li *\/?> */i', '[*] ', $string);
			
            $string = preg_replace('/<UL>/i',   '[LIST]',  $string);
            $string = preg_replace('/<\/UL>/i', '[/LIST]', $string);
            $string = preg_replace('/<LI *\/?> */i', '[*] ', $string);

	
            // Vire les tags qu'on connais pas
			$string = preg_replace('/<br *\/?>/i', '\n', $string);
			$string = preg_replace('/<BR *\/?>/i', '\n', $string);
			
            $string = preg_replace('/<.*? *\/?>/', '', $string);
       
        return $string;
    }

    /**
	 * Convertit le BBCode de la chaine en (X)HTML.
     *
	 * @todo nettoyer et commenter
     * @param	string	Texte avec le BBCode
     * @return	string	Texte avec l'(X)HTML
	 * @author	Yannick Croissant
	 * @access	public
	 * @todo	c'est moche, ammliorer tout a
     */ 
    public function MiniBBCodeToHtml($text) {
		global $site,$string;	
        $text = ' '.str_replace("\r",'',$text);
		
        if (is_null($text) || empty($text)) return ('');
        $result = '';
		$ducode=0;

		$tab1=array("http://www.","www.");
		$tab2=array("www.","http://www.");
		$text=str_replace($tab1,$tab2,strip_tags($text));
		$text=substr($text,0,$site->config('shoutbox_max_caract'));
		
		// Remplacement des Liens
		$in=array('`((?:https?|ftp)://\S+)(\s|\z)`','`((?<!//)(www\.)\S+)(\s|\z)`');
		$out=array('<a href="$1" title="$1">[lien]</a>','<a href="http://$1" title="http://$1">[lien]</a>');
		$text=preg_replace($in,$out,$text); 
		// Remplacement des Mais
		$in=array('([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)');
		$out=array('<a href="mailto:$0">[mail]</a>');
		$text=preg_replace($in,$out,$text); 
		
		// DEF
        $text = preg_replace('/\[DEF=(.*?)\](.+?)\[\/DEF\]/i', '<acronym title="\\1">\\2</acronym>', $text);
        $text = preg_replace('/\[ABBR=(.*?)\](.+?)\[\/ABBR\]/i', '<abbr title="\\1">\\2</abbr>', $text);

		// SMILEYS
		$smileys=unserialize($site->config('smileys'));
		foreach($smileys as $code => $image) {
			$code=preg_quote($code,'/');
			$text=preg_replace( "!(?<=[^\w&;/])".$code."!",'<img style="vertical-align:middle;border:0px;" alt="'.stripslashes($code).'" title="'.stripslashes($code).'" src="templates/'.THEME.'/images/smileys/'.$image.'" />',$text);
		}

		// Troncage des mots sans couper les liens
		$text=$string->cutLongWords($text,$site->config('shoutbox_max_length'));
		
		return nl2br(trim($text));
	}

    /**
	 * Convertit le BBCode de la chaine en (X)HTML.
     *
	 * @todo nettoyer et commenter
     * @param	string	Texte avec le BBCode
     * @return	string	Texte avec l'(X)HTML
	 * @author	Yannick Croissant
	 * @access	public
     */ 
    public function BBCodeToHtml($string) {
		global $site;
        $string = ' '.str_replace("\r",'',$string);
		//$string=$this->addDefs($string);
        if (is_null($string) || empty($string)) return ('');
        $result = '';
		$ducode=0;
			
            $styles = array( 'b' => 'strong', 'i' => 'em','s' => 'del','del' => 'del', 'strike' => 'del');

            // Trucs simples
            foreach( $styles as $code => $html) {
                $string = preg_replace('#\['.$code.'\](.+?)\[/'.$code.'\]#is','<'.$html.'>$1</'.$html.'>',$string);
            }
			
			// SMILEYS
			$smileys=unserialize($site->config('smileys'));
			foreach($smileys as $code => $image) {
				$code=preg_quote($code,'/');
				$string=preg_replace( "!(?<=[^\w&;/])".$code."!",'<img class="messsmiley" alt="'.stripslashes($code).'" title="'.stripslashes($code).'" src="'.dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/'.'templates/'.THEME.'/images/smileys/'.$image.'" />',$string);
			}

            // U
            $string = preg_replace('#\[u\](.+?)\[/u\]#is','<span style="text-decoration:underline;">$1</span>',$string);

            // COLOR
            $string = preg_replace('/\[COLOR=(.*?)\]/i', '<span style="color:\\1;">', $string);
            $string = preg_replace('/\[\/COLOR\]/i',     '</span>',              $string);

            // SIZE
            $string = preg_replace('/\[SIZE=(.*?)\]/i', '<span style="font-size:\\1em;">', $string);
            $string = preg_replace('/\[\/SIZE\]/i',     '</span>',             $string);

            // URL
           // $string = preg_replace('/\[URL TITLE=(.*?)\]([^\[]+?)\[\/URL\]/i',   'cbk_UrlTitle''<a href="$2" title="$1">$2</a>', $string);
           // $string = preg_replace('/\[URL=(.*?) TITLE=(.*?)\](.+?)\[\/URL\]/i', 'cbk_UrlTitle''<a href="$1" title="$2">$3</a>', $string);
            $string = preg_replace_callback('/\[URL\]([^\[]+?)\[\/URL\]/i',   'cbk_Url', $string);
            $string = preg_replace_callback('/\[URL=(.*?)\](.+?)\[\/URL\]/i', 'cbk_Url', $string);
			
           // $string = preg_replace('/\[URL=(.*?)\](.+?)\[\/URL\]/i', '<a href="$1">$2</a>', $string);

            // IMG
            $string = preg_replace_callback('/\[IMG=(.*?)\]/i','cbk_Img', $string);
            $string = preg_replace_callback('/\[IMG\](.*?)\[\/IMG\]/i','cbk_Img', $string);
			
            // SWF
            $string = preg_replace_callback('/\[SWF=(.*?)\]/i','cbk_Swf', $string);
            $string = preg_replace_callback('/\[SWF\](.*?)\[\/SWF\]/i','cbk_Swf', $string);
			
            // VIDEO
            $string = preg_replace_callback('/\[VIDEO=(.*?) TITLE=(.*?) SIZE=(.*?)\]/i','cbk_Video', $string);
            $string = preg_replace_callback('/\[VIDEO TITLE=(.*?) SIZE=(.*?)\](.*?)\[\/VIDEO\]/i','cbk_Video', $string);
			
			// DEF
            $string = preg_replace('/\[DEF=(.*?)\](.+?)\[\/DEF\]/i', '<acronym title="\\1">\\2</acronym>', $string);
			// ABBR
            $string = preg_replace('/\[ABBR=(.*?)\](.+?)\[\/ABBR\]/i', '<abbr title="\\1">\\2</abbr>', $string);
            
            // ALIGN
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

            $string = preg_replace('/\[QUOTE\]/i',"<strong style=\"font-size:0.85em\">Citation</strong>\n<blockquote><p>",$string);
            $string = preg_replace('/\[QUOTE=(.*?)]/i',"<span style=\"font-size:0.85em\"><strong>Citation</strong> de $1</span>\n<blockquote><p>",$string);
            $string = preg_replace('/\[\/QUOTE\]/i','</p></blockquote>',$string);
            if ($quote > 0) {
                $string = preg_replace('/^/', str_repeat('&#62;', $quote), $string);
            }

			// CODE
			$string = preg_replace_callback('`\[CODE\](.+?)\[/CODE\]`si','cbk_Code', $string); 
			
            // EMAIL
            $string = preg_replace('/\[EMAIL\](.*?)\[\/EMAIL\]/i', '<a href="mailto:\\1">\\1</a>', $string);

            // LIST
            $string = preg_replace('/\[LIST(?:=(.*?))?\]\n/i', '<ul>',  $string);
            $string = preg_replace('/\[LIST(?:=(.*?))?\]/i', '<ul>',  $string);
            $string = preg_replace('/\[\/LIST\]\n/i',          '</ul>', $string);
            $string = preg_replace('/\[\/LIST\]/i',          '</ul>', $string);
            $string = preg_replace('/\[\*\](.*)\n/i',              '<li>$1</li>',  $string);
            $string = preg_replace('/\[\*\](.*)/i',              '<li>$1</li>',  $string);
			
			$this->quoteList($string);

			// CLASSEMENT
            $string = preg_replace('/\[CLASSEMENT\]\n/i', '<table class="classement"><col class="col1"></col><tr><th colspan="2">Classement</th></tr>',  $string);
            $string = preg_replace('/\[CLASSEMENT(?:=(.*?))?\]\n/i', '<table class="classement"><col class="col1"></col><tr><th colspan="2">Classement: $1</th></tr>',  $string);
            $string = preg_replace('/\[\/CLASSEMENT\]/i',          '</table>', $string);
            $string = preg_replace('/\[(.*)(&egrave;me|er)\](.*)\n/i',              '<tr><td>$1$2</td><td>$3</td></tr>',  $string);

			$from=array("\n","\r",'');
			$to=array('','','&euro;');
			$string = str_replace($from,$to,nl2br(trim($string)));
			
			if($this->purify) $string = $this->purifier->purify($string);		

        return $string;
    }


     /**
	 * Convertit le BBCode de la chaine en iHTML.
     *
	 * @todo nettoyer et commenter
     * @param	string	Texte avec le BBCode
     * @return	string	Texte avec l'(X)HTML
	 * @author	Yannick Croissant
	 * @access	public
     */ 
    public function BBCodeToiHtml($string) {
		global $site;
        $string = ' '.str_replace("\r",'',$string);
        if (is_null($string) || empty($string)) return ('');
        $result = '';
		$ducode=0;
			
            $string = preg_replace('#\[b\](.+?)\[/b\]#is','\\1',$string);
            $string = preg_replace('#\[del\](.+?)\[/del\]#is','',$string);
            $string = preg_replace('#\[strike\](.+?)\[/strike\]#is','',$string);
			
			// SMILEYS
			$smileys=unserialize($site->config('smileys'));
			foreach($smileys as $code => $image) {
				$code=preg_quote($code,'/');
				$string=preg_replace( "!(?<=[^\w&;/])".$code."!",stripslashes($code),$string);
			}

            // COLOR
            $string = preg_replace('/\[COLOR=(.*?)\]/i', '<font color="\\1">', $string);
            $string = preg_replace('/\[\/COLOR\]/i',     '</font>',              $string);

            // SIZE
            $string = preg_replace('/\[SIZE=(.*?)\]/i', '<font size="\\1em">', $string);
            $string = preg_replace('/\[\/SIZE\]/i',     '</font>',             $string);

            // URL
            $string = preg_replace_callback('/\[URL\]([^\[]+?)\[\/URL\]/i',   'cbk_Url', $string);
            $string = preg_replace_callback('/\[URL=(.*?)\](.+?)\[\/URL\]/i', 'cbk_Url', $string);
			
            // IMG
            $string = preg_replace_callback('/\[IMG=(.*?)\]/i','cbk_iImg', $string);
            $string = preg_replace_callback('/\[IMG\](.*?)\[\/IMG\]/i','cbk_iImg', $string);
			
            // SWF
            $string = preg_replace('/\[SWF=(.*?)\]/i','', $string);
            $string = preg_replace('/\[SWF\](.*?)\[\/SWF\]/i','', $string);
			
            // VIDEO
            $string = preg_replace('/\[VIDEO=(.*?) TITLE=(.*?) SIZE=(.*?)\]/i','', $string);
            $string = preg_replace('/\[VIDEO TITLE=(.*?) SIZE=(.*?)\](.*?)\[\/VIDEO\]/i','', $string);
			
			// DEF
            $string = preg_replace('/\[DEF=(.*?)\](.+?)\[\/DEF\]/i', '', $string);
			// ABBR
            $string = preg_replace('/\[ABBR=(.*?)\](.+?)\[\/ABBR\]/i', '', $string);
            
            // ALIGN
            $string = preg_replace('/\[ALIGN=center\]/i', '<div align="center">',  $string);
            $string = preg_replace('/\[ALIGN=left\]/i',   '<div align="left">',  $string);
            $string = preg_replace('/\[ALIGN=right\]/i',  '<div align="right">',  $string);
			
            $string = preg_replace('/\[ALIGN=centrer\]/i', '<div align="center">',  $string);
            $string = preg_replace('/\[ALIGN=gauche\]/i',   '<div align="left">',  $string);
            $string = preg_replace('/\[ALIGN=droite\]/i',  '<div align="right">',  $string);
            $string = preg_replace('/\[\/ALIGN\]/i',      '</div>', $string);

            // QUOTE
			$quote=0;
            if (preg_match('/\[QUOTE\]/i',$string) || preg_match('/\[QUOTE=(.*?)]/i',$string)) $quote+=1;
            if ((preg_match('/\[QUOTE\]/i',$string) || preg_match('/\[QUOTE=(.*?)]/i',$string)) && ($quote > -1)) $quote-=1;

            $string = preg_replace('/\[QUOTE\]/i',"Citation\n<blockquote><p>",$string);
            $string = preg_replace('/\[QUOTE=(.*?)]/i',"Citation de $1\n<blockquote><p>",$string);
            $string = preg_replace('/\[\/QUOTE\]/i','</p></blockquote>',$string);
            if ($quote > 0) {
                $string = preg_replace('/^/', str_repeat('&#62;', $quote), $string);
            }

			// CODE
			$string = preg_replace('`\[CODE\](.+?)\[/CODE\]`si','', $string); 
			
            // EMAIL
            $string = preg_replace('/\[EMAIL\](.*?)\[\/EMAIL\]/i', '<a href="mailto:\\1">\\1</a>', $string);

            // LIST
            $string = preg_replace('/\[LIST(?:=(.*?))?\]\n/i', '<ul>',  $string);
            $string = preg_replace('/\[LIST(?:=(.*?))?\]/i', '<ul>',  $string);
            $string = preg_replace('/\[\/LIST\]\n/i',          '</ul>', $string);
            $string = preg_replace('/\[\/LIST\]/i',          '</ul>', $string);
            $string = preg_replace('/\[\*\](.*)\n/i',              '<li>$1</li>',  $string);
            $string = preg_replace('/\[\*\](.*)/i',              '<li>$1</li>',  $string);
			
			$this->quoteList($string);

			// CLASSEMENT
            $string = preg_replace('/\[CLASSEMENT\]\n/i', '<table><tr><th colspan="2">Classement</th></tr>',  $string);
            $string = preg_replace('/\[CLASSEMENT(?:=(.*?))?\]\n/i', '<table><tr><th colspan="2">Classement: $1</th></tr>',  $string);
            $string = preg_replace('/\[\/CLASSEMENT\]/i',          '</table>', $string);
            $string = preg_replace('/\[(.*)(&egrave;me|er)\](.*)\n/i',              '<tr><td>$1$2</td><td>$3</td></tr>',  $string);

			$from=array("\n","\r",'');
			$to=array('','','&euro;');
			$string = str_replace($from,$to,nl2br(trim($string)));

        return $string;
    }
 
	public function quoteList(&$string) {
		$tab=explode(">",$string);
		$prec='';
		$quote=false;
		foreach($tab as $var) {
			if(strpos($var,'<blockquote')!==false) $quote=true;
			else if(strpos($var,'</blockquote')!==false) $quote=false;
			
			if(strpos($var,'<ul')!==false && $quote==true) $tab=str_replace('<ul','</p><ul',$tab);
			else if(strpos($var,'</ul')!==false && $quote==true) $tab=str_replace('</ul','</ul><p',$tab);
		}
		$string=implode('>',$tab);
		$string=str_replace('<p></p>','',$string);
	}

    /**
	 * Ajout des dfinitions dans le message.
     *
	 * @todo chai plus si a marche et si c'est fini, faudrai que je jette un oeuil un jour ;)
     * @param	string	Texte sans les dfinitions
     * @return	string	Texte avec les dfinitions
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	public function addDefs($string) {
		$texte=$string;
		$traite=array();
		$tab1=array('[',']','(',')');
		$tab2=array('<','>','');
		$texte=str_replace($tab1,$tab2,$texte);
		
		$texteCasse=strip_tags($texte);
		while(ereg('  ',$texteCasse)) $texteCasse=str_replace('  ',' ',$texteCasse);
		$tabCasse=preg_split("/[\s,]+/",$texteCasse);
		
		$texte=strtolower(strip_tags($texte));
		while(ereg('  ',$texte)) $texte=str_replace('  ',' ',$texte);
		$tab=preg_split("/[\s,]+/",$texte);
		foreach($tab as $i=>$mot) {
			$index=array_search(preg_replace('/([^afr-z0-9])s?x?$/i','\\1',$mot),$this->def['mot']);
			if($index!==false && !in_array($index,$traite)) {
				if($this->def['type'][$index]=='def') $string=preg_replace('/([^a-z0-9])'.$mot.'/i',"\\1[DEF=".$this->def['def'][$index]."]".$tabCasse[$i].'[/DEF]',$string);
				else if($this->def['type'][$index]=='abbr') $string=preg_replace('/([^a-z0-9])'.$mot.'/i',"\\1[ABBR=".$this->def['def'][$index]."]".$tabCasse[$i].'[/ABBR]',$string);
				$traite[]=$index;
			}
		}
		return $string;
	}
}

    /**
	 * Fonction callback de mise en forme de code prsent dans le message.
     *
	 * @todo style CSS  mettre a part
     * @param	string	Code brut
     * @return	string	Code mis en forme
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function cbk_Code($match) {
		//$code        = explode('<br />',nl2br($match[1]));
		$num_lignes      = 1;
		$out = '<strong style="font-size:0.85em">Code :</strong><pre><code>'.nl2br(trim($match[1]));
		/*foreach ($code as $code_line) {
			$out .= ''.nl2br($code_line).'';
			$num_lignes++;
		}*/
		$out .= '</code></pre>';
	
		return str_replace("\n",'',$out);
	}
	
    /**
	 * Fonction callback de mise en forme des images présentes dans le message.
     *
	 * @todo style CSS  mettre a part
     * @param	string	URL de l'image
     * @return	string	Code HTML de l'image
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function cbk_Img($match) {
		$alt=pathinfo($match[1]);
		if(!eregi('^([a-z0-9]{3,5})://',$match[1])) $match[1]=dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/'.$match[1];
		return '<img class="messimg" src="'.htmlentities($match[1]).'" alt="'.str_replace('.'.$alt['extension'],'',$alt['basename']).'" />';
	}
	
    /**
	 * Fonction callback de mise en forme des images présentes dans le message (version iHTML.
     *
	 * @todo style CSS  mettre a part
     * @param	string	URL de l'image
     * @return	string	Code HTML de l'image
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function cbk_iImg($match) {
		$utils = new Utils();
	
		$alt=pathinfo($match[1]);
		$fullUrl=dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/';
		if(!eregi('^([a-z0-9]{3,5})://',$match[1])) {
			if(file_exists($match[1])) $match[1]=$utils->miniature($match[1],80,60,'medias/temp/imode',50);
			$match[1]=$fullUrl.$match[1];
		} else if(eregi('^'.$fullUrl,$match[1])) {
			$match[1]=str_replace($fullUrl,'',$match[1]);
			if(file_exists($match[1])) $match[1]=$utils->miniature($match[1],80,60,'medias/temp/imode',50);
			$match[1]=$fullUrl.$match[1];
		}
		return '<img src="'.htmlentities($match[1]).'" alt="'.str_replace('.'.$alt['extension'],'',$alt['basename']).'" />';
	}
	
    /**
	 * Fonction callback de mise en forme des animations flash présentes dans le message.
     *
	 * @todo virer l'astuce pour re-so
     * @param	string	URL de l'animation
     * @return	string	Code HTML de l'animation
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function cbk_Swf($match) {
		$size=getimagesize($match[1]);
		if(!isset($size[0])) $size[0]=468;
		if(!isset($size[1])) $size[1]=60;
		return '<object type="application/x-shockwave-flash" data="'.$match[1].'" width="'.$size[0].'" height="'.$size[1].'">
		<param name="movie" value="'.$match[1].'" />
		<param name="wmode" value="transparent" />
		<p>Pour voir cette animation vous devez avoir <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">le plugin Flash 8</a></p>
		</object>';
	}


    /**
	 * Fonction callback de mise en forme des vidéo DivX présentes dans le message.
     *
	 * @todo virer l'astuce pour re-so
     * @param	string	URL de la vidéo
     * @return	string	Code HTML de l'animation
	 * @author	Yannick Croissant
	 * @access	public
     */ 
	function cbk_Divx($match) {
		global $site;
		static $divx=0;
		$divx++;
		if(!ereg('http://',$match[1])) $match[1]=$site->getRoot().$match[1];
		
		//return '<div><a id="divx'.$divx.'" href="'.$match[1].'"><img style="border:0px;margin:5px;vertical-align:middle;" src="templates/'.THEME.'/images/divx.png" alt="Vidéo DivX" /></a></div>';
		return '<object style="border:0px;margin:5px;vertical-align:middle;" classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="160" height="128" codebase="http://download.divx.com/labs/DivXBrowserPlugin.cab"><param name="src" value="'.$match[1].'" /><param name="mode" value="zero" /><embed search="for divx w3c valid embeding code, thx" type="video/divx" width="160" height="128" src="'.$match[1].'" mode="zero" pluginspage="http://go.divx.com/plugin/download/"></embed></object>';
	}
	
	function cbk_Video($match) {
		$size=explode('_',$match[3]);
		return '<object type="application/x-shockwave-flash" data="include/scripts/player_flv/player_flv.swf?flv='.$match[1].'&amp;title='.$match[2].'&amp;width='.(int)$size[0].'&amp;height='.(int)$size[1].'&amp;loadingcolor=0&amp;bgcolor1=ffffff&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=0&amp;slidercolor1=cccccc&amp;slidercolor2=aaaaaa&amp;sliderovercolor=666666&amp;playercolor=eeeeee&amp;showvolume=1" width="'.(int)$size[0].'" height="'.(int)$size[1].'">
		 <param name="movie" value="include/scripts/player_flv/player_flv.swf?flv='.$match[1].'&amp;title='.$match[2].'&amp;width='.(int)$size[0].'&amp;height='.(int)$size[1].'&amp;loadingcolor=0&amp;bgcolor1=ffffff&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=0&amp;slidercolor1=cccccc&amp;slidercolor2=aaaaaa&amp;sliderovercolor=666666&amp;playercolor=eeeeee&amp;showvolume=1" />
		 <param name="wmode" value="transparent" />
		 <p>Pour voir cette vidéo vous devez avoir <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">le plugin Flash 8</a></p>
		</object>';
	}
	
	function cbk_Url($match) {
		if(!eregi('^([a-z0-9]{3,5})://',$match[1])) $match[1]=dirname('http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]).'/'.$match[1];
		if(!empty($match[2])) return '<a href="'.$match[1].'">'.$match[2].'</a>';
		else return '<a href="'.$match[1].'">'.$match[1].'</a>';
	}
?>
