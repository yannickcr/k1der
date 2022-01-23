<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: phpmyvisites.php,v 1.69 2006/01/13 19:13:58 matthieu_ Exp $

define('SAVE_STAT', true ); // default true
define('LOOK_FOR_COOKIE', true); // default true
define('DEBUG', false); // default false
define('PROFILING', false); // default false
define('TRACE_IN_FILES', false); // default false
define('INCLUDE_PATH', 'include/scripts/phpmyvisites'); 

if(!SAVE_STAT)
{
	if(substr($_GET['pagename'], 0, 5) == 'FILE:')
	{
		header('Location:' . $_GET['url']);
		exit;
	}
	else
	{
		$img = INCLUDE_PATH . "/images/logos/pixel.gif";
		header("Content-type: image/gif");
		readfile($img);
		exit;
	}
}


if(PROFILING)
{
	xdebug_start_profiling();
}
// TODO dans le test du referer appartient au site, testez également en plus 
// des sitesUrls le host de l'url de la page current sur le host de l'url ref

@ignore_user_abort(true);
@set_time_limit(0);
@error_reporting(E_ALL);

require_once INCLUDE_PATH."/core/include/global.php";
require_once INCLUDE_PATH."/core/include/Logs.functions.php";
require_once INCLUDE_PATH."/core/include/common.functions.php";
require_once INCLUDE_PATH."/core/include/Cookie.class.php";
require_once INCLUDE_PATH."/core/include/Site.class.php";
require_once INCLUDE_PATH."/core/include/PmvConfig.class.php";

$GLOBALS['currentModuleIsLogModule'] = true; // hack for not do show tables (because getCurrentCompleteUrl bugs with url containing another url in one parameter)
$c =& PmvConfig::getInstance();

$db =& Db::getInstance();
$db->connect();

// when no get specified, display a marketing page :)
/*if(sizeof($_GET) === 0)
{
	require_once INCLUDE_PATH."/core/include/Lang.class.php";
	$l = new Lang();
	displayPageWhenEmptyGet();
	exit;
}*/

if(DEBUG)
{	
	require_once INCLUDE_PATH."/core/include/functions.php";
}

if(DEBUG)
	ob_start();
	
// - imprimer doc mysql "optimisation"

/*
 * Get page & visitor information
 */
$idSite       = getRequestVar('id', null, 'numeric');

$GLOBALS['cookie']   = new Cookie( COOKIE_PMVLOG_NAME . $idSite);

if(LOOK_FOR_COOKIE && $GLOBALS['cookie']->isDefined())
{
	printDebug("<b>Cookie at the beginning (size : ".$GLOBALS['cookie']->getSize()." bytes)</b> :<br>");
	printDebug($GLOBALS['cookie']->get()); 
	$returningVisitor = 1;
}
else
{
	$returningVisitor = 0;
	printDebug("<b>Cookie not found !</b><br><br>");
}

$flash        = getRequestVar('flash', 0, 'numeric');
$director     = getRequestVar('director', 0, 'numeric');
$quicktime    = getRequestVar('quicktime', 0, 'numeric');
$realPlayer   = getRequestVar('realplayer', 0, 'numeric');
$windowsMedia = getRequestVar('windowsmedia', 0, 'numeric');
$pdf          = getRequestVar('pdf', 0, 'numeric');
$java         = getRequestVar('java', 0, 'numeric');
$divx         = getRequestVar('divx', 0, 'numeric');

$refererUrl   = getRequestVar('ref', '');


$site2 = new Site2($idSite);

/*
 * site urls
 */
if(!$site2Urls = $GLOBALS['cookie']->getVar('site_urls'))
{
	$site2Urls     = $site2->getUrls();
	
	// save array of site urls in the cookie
	$GLOBALS['cookie']->setVar('site_urls', $site2Urls);
}

/*
 * site info
 */
if(!$site2Info = $GLOBALS['cookie']->getVar('site_info'))
{
	$site2Info = $site2->getInfo();
	
	// save array of site urls in the cookie
	$GLOBALS['cookie']->setVar('site_info', $site2Info);
}


$logo              = $site2->getLogo();
$site2Params        = $site2->getParams();
$paramsExclude     = $site2Params['params_names'];
$paramsIncludeOnly = $site2Params['params_names'];

$pageUrl      = getRequestVar('url');

/**
 * exit if visitor is cookie excluded from the stats
 */

if(isset($_COOKIE[COOKIE_NAME_NO_STAT.$site2->getId()]))
{
	printDebug("Excluded from stats with the cookie!");
	
	/*if(substr($_GET['pagename'], 0, 5) == PREFIX_FILES)
	{
		header('Location:' . $pageUrl);
		exit;
	}
	else
	{
		loadImage($logo, $idSite);
	}*/
}

/*
 * page variables
 */
$a_vars = getRequestVar('a_vars', array(), 'array');

/*
 * visitor config, as saved in the database
 */
$userAgent    = secureVar(@$_SERVER['HTTP_USER_AGENT']);
$os           = getOs($userAgent);
$a_browser    = getBrowserInfo($userAgent);
$resolution   = getRequestVar('res', 'unknown', 'string');
$colorDepth   = getRequestVar('col', 32, 'numeric');

$browserLang  = secureVar(@$_SERVER['HTTP_ACCEPT_LANGUAGE']);

$localTime    = getRequestVar('h',date("H"),'numeric').':'.
						getRequestVar('m',date("i"),'numeric').':'.getRequestVar('s',date("s"),'numeric');



// assign pageCategory default value of the parse_url::path?query
$pageUrlParamsProcessed = processParams($pageUrl, $site2Params);

// fix add site.com because else parse_url bugs with ':' in query string
if(!ereg('http://',$pageUrlParamsProcessed))
{
	$urlParse     = parse_url('http://site.com'.$pageUrlParamsProcessed);
}
else
{
	$urlParse     = parse_url($pageUrlParamsProcessed);
}

if(isset($urlParse['path']))
{
	$pageNameDefault = substr($urlParse['path'], 1);
}

if(isset($urlParse['query']))
{
	$pageNameDefault .= '?'.$urlParse['query'];
}

if(  (!isset($pageNameDefault) 
		|| strcmp($pageNameDefault, '')===0)
		&& isset($urlParse['host'])
	)
{
	$pageNameDefault = DEFAULT_PAGE_NAME;
}
else if(!isset($pageNameDefault))
{
	$pageNameDefault = null;
}
printDebug("PageNameDefault : " . $pageNameDefault);

// stripslashed because otherwise pageName if value is pageNameDefault is slashed twice 
$pageCompleteName = utf8_encode(getRequestVar('pagename' , 
											stripslashes(html_entity_decode($pageNameDefault))
									)
								);

// works only on 'path' of this url because the query may contain delimiter !
$file = '';
if(substr($pageCompleteName, 0, 5) === 'FILE:')
{
	$file = 'FILE:';
}

printDebug("<br>Page complete name:".$pageCompleteName);
$urlParse = parse_url('http://site.com/'.$pageCompleteName);


$pageCompleteNamePath = substr($urlParse['path'],1);
$lastDelimiter = strrpos($pageCompleteNamePath, CATEGORY_DELIMITER);
printDebug("<br>Page name complete path:".$pageCompleteNamePath);

if($lastDelimiter !== false)
{
	// in the $pageCompleteName "g1>g2>page" select only "g1>g2"
	$pageCategory = substr($pageCompleteNamePath, 0, $lastDelimiter);
	$pageCategory = str_replace( 'FILE:', '', $pageCategory);
	if($pageCategory == '/'
	|| $pageCategory == ' '
	|| $pageCategory == '+'
	|| $pageCategory == '-'
	|| $pageCategory == '"'
	|| $pageCategory == '\''
	)
	{
		$pageCategory = '';
	}
	
	// in the $pageCompleteName "g1>g2>page" select only "page"
	// if pageCompleteNamePath bug when all variables recorded, no pagename, only save index.php
	$pageName = $file . substr($pageCompleteName, $lastDelimiter + 1);
}
else
{
	$pageCategory = '';
	$pageName = $pageCompleteName;
}
//var_dump($pageCategory);exit;

// concerning names of pages in subgroups like /g1/g2/g3/ without page names
if(strcmp($pageName, '')===0)
{
	$pageName = DEFAULT_PAGE_NAME;
}



printDebug('<br>URL : '.$pageUrl);
printDebug('<br>pageName : '.$pageName);
printDebug('<br>pageCategory : '.$pageCategory);
printDebug('<br>a_vars : '); printDebug($a_vars);
printDebug('<br>referer : '); printDebug($refererUrl);
printDebug('<br>flash : '.$flash);
printDebug('<br>director : '.$director);
printDebug('<br>quicktime : '.$quicktime);
printDebug('<br>real player : '.$realPlayer);
printDebug('<br>windows media : '.$windowsMedia);
printDebug('<br>PDF : '.$pdf);
printDebug('<br>java : '.$java);
printDebug('<br>divx : '.$divx);
printDebug('<br>referer Url : '.$refererUrl);
printDebug('<br>id site : '.$idSite);
printDebug('<br>site Urls : '); printDebug($site2Urls);
printDebug('<br>site Info : '); printDebug($site2Info);
printDebug('<br>user Agent : '.$userAgent);
printDebug('<br>os : '.$os);
printDebug('<br>browser : '.$a_browser['longName']);
printDebug('<br>resolution : '.$resolution);
printDebug('<br>color : '.$colorDepth);

/**
 * other information
 */
$todayDate = date("Y-m-d");

/**
 * try to recognize the visitor, with or without cookie
 * who said we are very strong ?
 */

// last_visit = last visit timestamp
// idcookie = id cookie

printDebug("<br><strong>Try to recognize the visitor...</strong><br>");

function saveCountInFile( $fileName )
{
	$count = 0;
	$fileName = './count/'.$fileName;
	if(is_file($fileName))
	{
		include($fileName);
		if(isset($count) && is_integer($count) && $count != 0)
		{
			saveConfigFile( $fileName, $count+1, "count");
		}
	}
	
}
if(TRACE_IN_FILES) saveCountInFile( 'p_total' );

// does phpmyvisites cookie exist ?
if($GLOBALS['cookie']->isDefined())
{
	// yes, known visitor
	$idVisit = $GLOBALS['cookie']->getVar('idvisit');
	$idCookie = $GLOBALS['cookie']->getVar('idcookie');
	$lastVisit = $GLOBALS['cookie']->getVar('last_visit_time');
	$serverTime = $GLOBALS['cookie']->getVar('server_time');
	$serverDate = $GLOBALS['cookie']->getVar('server_date');
	if(TRACE_IN_FILES) saveCountInFile( 'p_cookie' );

	if(DEBUG)
		printDebug("<br>We know the visitor (thanks to his cookie). 
					<br>He has idvisit = $idVisit and went last time on 
					".getTimeForDisplay($lastVisit)."<br>"); 
}
else
{
	printDebug("=>We can't find the cookie...<br>");
	if(TRACE_IN_FILES) saveCountInFile( 'p_non_cookie' );
	// compute IP
	$ip           = getIp();
	$ip2long      = ip2long($ip);
//	exitIfIpExcluded($ip2long, $logo, $site2);
	
	
	$serverDate = $todayDate;
	
	$tryPutCookie = true;
	
	// no
	// does the referer belong to the website ?	
	if($site2->isUrlIn($refererUrl))
	{
		printDebug("=> Referer Is in the site ! try to catch the visitor...<br>");

		// referer is in the current site
		$refererUrlIsInSite = true;
		
		if(TRACE_IN_FILES) saveCountInFile( 'p_ref_url_in' );
		$md5Config = md5( $os . serialize($a_browser) . $resolution . $colorDepth . $pdf
					. $flash . $java . $director . $quicktime . $realPlayer . $windowsMedia
					. $ip2long . $browserLang);
					
		// does the config (os+browser+resolution+color_depth) and the IP match any visitor ?
		$r = query("SELECT idvisit, idcookie, TIME_TO_SEC(last_visit_time), 
									TIME_TO_SEC(server_time)
				 FROM ".T_VISIT."
				 WHERE   server_date = '$todayDate'
					 AND idsite = $idSite
			 		 AND md5config = '$md5Config'
				 ORDER BY last_visit_time DESC
				 LIMIT 1");

		if(mysql_num_rows($r)>0)
		{
			// yes
			$r = mysql_fetch_row($r);
			$idVisit = $r[0];
			$idCookie = $r[1];
			$lastVisit = $r[2];
			$serverTime = $r[3];
			if(TRACE_IN_FILES) saveCountInFile( 'p_ref_url_in_found' );
			printDebug("=> We found the MD5CONFIG of visitor so he is known<br>");
		}
	}
	
	// case we didn't match any visitor
	if(!isset($idVisit))
	{
		// no, new visitor
		$newVisitor = true;
		if(TRACE_IN_FILES) saveCountInFile( 'p_ref_not_found' );
		printDebug("=> It's definitely a new visitor<br>");
	}
}

/**
 * Current visitor is a new visitor or an old one
 * but without cookie, so we put a cookie
 */
if(isset($newVisitor) || isset($tryPutCookie))
{
	// record it and set the cookie
	$idCookie = $GLOBALS['cookie']->put(isset($idCookie)?$idCookie:'');
	
	// case : visitor known but cookie not set during his first page views...
	if(isset($idVisit))
	{
		$GLOBALS['cookie']->setVar('idvisit', $idVisit);
	}
	else
	{
		$lastVisit = todayTime();
	}
}

/**
 * Visitor is known, we now look if it's a new visit or not
 */
// is the visit older than 30 minutes ?
if(!isset($newVisitor))
{
	if(DEBUG)
		printDebug("(idvisit = $idVisit, ".getTimeForDisplay($serverTime)." | now : " .                         
				todayTime()." :: first page last time : $serverTime)");
			
	if ($serverDate == date("Y-m-d")
			&& ($serverTime > (todayTime() - TIME_ONE_VISIT))
		)
	{
		// yes, new visit
		$GLOBALS['cookie']->setVar('last_visit_time', todayTime());
		$knownVisit = true;
		printDebug("<br><b>=>Visit is known on  date : $serverDate</b><br>");
	}
	else
	{
		printDebug("=>Last visit  is too old <b>==> New visit</b><br>");
		$returningVisitor = 1;
	}
}
else
{
	printDebug("<br><b>=>New Visitor also means new visit</b><br>");
}

/*
 * find $idPageRef if possible
 */
// update referer information if referer belongs to the site and 
// if visitor is known (we need idVisit !)
if(isset($idVisit) 
	&& !empty($refererUrl) 
	//&& (isset($refererUrlIsInSite) || $site2->isUrlIn($refererUrl))
	)
{
	$timeDiffRef = todayTime() - $lastVisit;
	
	// try to find the last "idpage" value
	// look in the cookie first if defined $a_idPage[$url]
	if($GLOBALS['cookie']->isDefined())
	{
		$a_idPage = $GLOBALS['cookie']->getVar('a_idPage');
		if(isset($a_idPage[$refererUrl]))
		{
			$idPageRef = $a_idPage[$refererUrl];
			printDebug("=>idPageRef found in cookie : $refererUrl => $idPageRef <br>");
		}
	}
	
	// else in database
	if(!isset($idPageRef))
	{
		// try to find it manually in the database (can be very heavy query)
		$r = query("SELECT l.idpage
				FROM  ".T_LINK_VP." as l, 
						".T_PAGE_MD5URL." as pu
				WHERE l.idvisit = '$idVisit'
					AND pu.md5url = '".md5($refererUrl)."'
					AND pu.idpage = l.idpage 
				ORDER BY pu.idpage_url DESC
				LIMIT 1");
		if(mysql_num_rows($r)>0)
		{
			$r = mysql_fetch_assoc($r);
			$idPageRef = $r['idpage'];
			printDebug("=>idPageRef found in BDD : $refererUrl => $idPageRef <br>");
		}
	}
	
	if(!isset($idPageRef))
	{
		// url not found in database... don't save
		printDebug("=> Url ref not found in database and in cookie... forget it !<br>");
	}
}

if(!isset($idPageRef))
{
	$idPageRef = 0;
	$timeDiffRef = DEFAULT_TIME_PAGE;
}

/**
 * Current visit is a known visit
 */
// save current page, etc.
if(isset($knownVisit) && $knownVisit)
{
	// we know 
	// * $lastVisit
	// * $idVisit
	// * $idCookie
	
	// update last_visit_time
	printDebug("==> This is a visit known... we update the data <br>");
	
	// do it first because we need idpage for visit info insert
	//print("$pageName <br> $pageUrl <br> $pageCategory ");exit;
	$a_idPages = recordDbPage($pageName, $pageUrl, $pageCategory);
	
	$idPageUrl = $a_idPages[1];
	$idPage = $a_idPages[0];
	
	// save current page & url & variables information
	$idLink_vp = recordDbInfoPage($idVisit, $idPage, $idPageRef, $timeDiffRef, $a_vars);
	
	$total_time = DEFAULT_TIME_PAGE + todayTime() - $serverTime;
	// update last_visit_time & total_pages & c_total_time
	$r = query("UPDATE ".T_VISIT."
			SET last_visit_time = CURRENT_TIME(),
				total_pages = total_pages + 1,
				total_time = '$total_time',
				exit_idpage = '$idPage'
			WHERE idvisit = '$idVisit'
			LIMIT 1");
			
	// if a page ref really exists
	if(isset($idPageRef) && $idPageRef != 0)
	{
		// save path
//		recordDbPath($idVisit, $idPageRef, $idPage); 
	}	
	// save idlink_pv in the cookie
}
/*
 * Current visit a new visit
 */
// now we know the visitor and its idcookie
// save new visit
else 
{
	printDebug("==> This is a new visit, we create datas in the database when necessary<br>");
	
	if(!isset($ip) || !isset($ip2long))
	{		
		$ip           = getIp();
		$ip2long      = ip2long($ip);
		//exitIfIpExcluded($ip2long, $logo, $site2);
	}
	$hostExt    = getHostnameExt(getHost($ip));
	printDebug('<br>ip : '.$ip);
	printDebug('<br>hostname : '.$hostExt);
	
	$serverDate = date("Y-m-d");
	$serverTime = date("H:i:s");
	
	$country = getCountry($hostExt, $browserLang);
	
	if($country=='cs')
	{
		$country = 'cz';
	}
	else if($hostExt == 'proxad.net')
	{
		$country = 'fr';
	}
	
	$continent = getContinent($country);
	
	if(strlen($refererUrl)===0)
	{
		$refererUrl = 'NULL';
	}
	else
	{
		$refererUrl = "'".$refererUrl."'";
	}
	
	// do it first because we need idpage for visit info insert
	$a_idPages = recordDbPage($pageName, $pageUrl, $pageCategory);
	
	$idPageUrl = $a_idPages[1];
	$idPage = $a_idPages[0];
	
	$md5Config = md5( $os . serialize($a_browser) . $resolution . $colorDepth . $pdf
					. $flash . $java . $director . $quicktime . $realPlayer . $windowsMedia . $divx
					. $ip2long . $browserLang);
	
	// save visitor information
	$r = query("INSERT INTO ".T_VISIT." 
						(idsite, idcookie, returning, last_visit_time, server_date, server_time, md5config, 
						referer, os, browser_name, browser_version, resolution, color_depth, 
						pdf, flash, java, director, quicktime, realplayer, windowsmedia, divx, 
						local_time, ip, hostname_ext, browser_lang, country, continent, 
						total_pages, total_time, entry_idpage, entry_idpageurl, exit_idpage)
					VALUES ('$idSite', '$idCookie', '$returningVisitor', CURRENT_TIME(), 
									'$serverDate', '$serverTime', '$md5Config', 
					$refererUrl, '$os', '".$a_browser['shortName']."',
					'".$a_browser['version']."', '$resolution', '$colorDepth', 
					'$pdf', '$flash','$java', '$director', '$quicktime', '$realPlayer', '$windowsMedia', '$divx', 
					'$localTime','$ip2long', '$hostExt', '$browserLang', '$country', '$continent', 
					1, '".DEFAULT_TIME_PAGE ."', '$idPage', '$idPageUrl', '$idPage')
			");
	$idVisit = mysql_insert_id();
	
	// save page view and URL and variables
	recordDbInfoPage($idVisit, $idPage, $idPageRef, $timeDiffRef, $a_vars);
	
	// save idvisit
	$GLOBALS['cookie']->setVar('idvisit', $idVisit);
	$GLOBALS['cookie']->setVar('last_visit_time', todayTime());
	$GLOBALS['cookie']->setVar('server_time', todayTime());
	$GLOBALS['cookie']->setVar('server_date', $serverDate);
	
}

// cookie post-process
// save in cookie
if($GLOBALS['cookie']->isDefined())
{
	$a_idPage = array();
	
	// save idpage of the current page in the a_idPage array in the cookie
	$a_idPage = $GLOBALS['cookie']->getVar('a_idPage');

	// if array size of "url => id" is superior to max_url_in_cookie, unset the array because the cookie can't be so big
	if(sizeof($a_idPage) > MAX_URL_IN_COOKIE) 
	{
		reset($a_idPage);
		unset($a_idPage[key($a_idPage)]);
	}
	
	// add current idpage
	$a_idPage[$pageUrl] = $idPage;
	
	$GLOBALS['cookie']->setVar('a_idPage', $a_idPage);	
}
	

$GLOBALS['cookie']->save();
printDebug("<br><b>Next cookie should be :</b>");
printDebug($GLOBALS['cookie']->getContent());

// footer
if(PRINT_QUERY_COUNT)
	printQueryCount();
if(PRINT_TIME)
	printTime();

if(SAVE_DB_LOG)
{
	recordDbQueryCount($idSite);
}

require_once INCLUDE_PATH . "/core/include/common.functions.php";
$crontabFile = INCLUDE_PATH . "/config/crontab.php";
if(is_file($crontabFile))
{
	include($crontabFile);
}
if( (	!isset($crontab) 
		|| $crontab['date_last_success'] != getDateFromTimestamp(time())
	)
&& (
		!is_file($crontabFile) 
		|| ( isset($crontab) 
			&& $crontab['time_last_try'] < time() - TIME_TO_WAIT_FOR_PARALLEL_ARCHIVE // every 5min
			)
	)
)
{
	printDebug('==========================<br>
				CRONTAB BEGIN/			  <br>
				==========================<br>
				');
	$crontab['time_last_try'] = time();
	$crontab['date_last_success'] = '2000-12-31';
	saveConfigFile( $crontabFile, $crontab, "crontab");
	require_once  INCLUDE_PATH .  '/core/include/PmvConfig.class.php';
	require_once INCLUDE_PATH . '/core/include/ApplicationController.php';
	
	$r =& Request::getInstance();
	$r->setModuleName('send_mail');
	$r->setCrontabAllowed();
	ApplicationController::init();
	printDebug('==========================<br>
				CRONTAB END/			  <br>
				==========================<br>
				');
	$crontab['date_last_success']  = getDateFromTimestamp(time());
	saveConfigFile( $crontabFile, $crontab, "crontab");
}

$db->close();


if(!PROFILING)
{
	if(substr($pageCompleteName, 0, 5) == PREFIX_FILES)
	{
		if(DEBUG)
		{
			printDebug("=====================<br>Header() to file to download<br>=====================");
		}
		else
		{
			//header('Location:' . $pageUrl);
			//exit;
		}
	}
	else
	{
		//loadImage($logo, $idSite);
	}
}

// flush content for display
if(DEBUG)
	ob_end_flush();

/*if(PROFILING)
	xdebug_dump_function_profile(4);*/
?>