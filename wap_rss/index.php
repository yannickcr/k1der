<?php
function encode($url) {
	$tab1=array('_',':','/','-','=','?','&');
	$tab2=array('@5F','@3A','@2F','@2D','@3D','@3F','&amp;');
	return str_replace($tab1,$tab2,$url);
}


header("Cache-control: private, no-cache");
header("Content-Type: text/vnd.wap.wml");
echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>';
?>
<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">
<wml>
<?php

/**
 * Tableau des fils RSS
 */
$fils=array(
	'NoFrag'			=>	array('http://www.nofrag.com/nofrag.rss',2),
	'PcInpact'			=>	array('http://www.pcinpact.com/include/news.xml',1),
	'Clubic'			=>	array('http://www.clubic.com/xml/news.xml',1),
	'Alsacréation'		=>	array('http://blog.alsacreations.com/rss.php',7),
	'BlogZinet'			=>	array('http://blogzinet.free.fr/ecrire/tools/dcBlogmark/feeds/rss_2_0.xml',0),
	'CS-Fusion'			=>	array('http://www.faqdb.net/rss/?id=6',0),
	'Half-Life Fusion'	=>	array('http://www.halflifefusion.com/hlfnews.rss',0),
	'Vossey'			=>	array('http://www.vossey.com/xml/news.xml',12),
	'New Dimension'		=>	array('http://www.newdimension-fr.net/rss/news.xml',0),
	'Presence PC'		=>	array('http://www.presence-pc.com/ppcrss.xml',0),
	'K1der'				=>	array('http://www.k1der.net/rss.php',2),
	'Pompage.net'		=>	array('http://www.pompage.net/rss091.php3',0)
);

if(isset($_GET['rss'])) {

?>
<card id="fil">
	<do type="options" label="Menu">
		<go href="#menu"/>
	</do>
	<do type="prev">
		<prev/>
	</do>
<?php
	
	/**
	 * Affichages du fil
	 */
	require_once "magpierss/rss_fetch.inc";
	$fichier_xml = $fils[$_GET['rss']][0];
	$nombre_element = 100;
	
	$rss = fetch_rss($fichier_xml);
	
	if (is_array($rss->items)) {
		$liste = array_slice($rss->items, 0, $nombre_element);
		echo '	<p align="center" mode="nowrap">
		<b><small>'.$rss->channel['title'].'</small></b>
	</p>
	<p mode="nowrap">'."\n";

		
		foreach ($liste as $item ) {
			
			//print_r($item);
			
			$title = $item['title']; $url = $item['link'];
			if(isset($item['dc']['date'])) $pubdate = $item['dc']['date'];
			else if(isset($item['date'])) $pubdate = $item['date'];
			else if(isset($item['pubdate'])) $pubdate = $item['pubdate'];
			
			$pubdate = date("d/m/Y", strtotime($pubdate));
			echo $pubdate.' : <a href="http://wmlproxy.google.com/wmltrans/h=fr/g=/q=truc/s=0/u='.encode($url).'/c='.$fils[$_GET['rss']][1].'">'.htmlspecialchars($title,ENT_QUOTES).'</a><br />'."\n";
		} 
	} 

?>	</p>
</card>
<?php } ?>
<card id="menu">
	<do type="prev">
		<prev/>
	</do>
	<p align="center" mode="nowrap">
		<b><small>Fils RSS</small></b>
	</p>
	<p mode="nowrap">
<?php
	/**
	 * Parcours du tableau des fils RSS
	 */
	foreach($fils as $i=>$var) {
		echo '<a href="index.php?rss='.$i.'">'.$i.'</a>'."<br />\n";
	}
?>
</p>
</card>
</wml>