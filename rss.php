<?php
header("content-type: application/xml");
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\" ?>";
/*echo "<?xml-stylesheet title=\"XSL_formatting\" type=\"text/xsl\" href=\"rss.xsl\"?>";*/
echo "<rss version=\"2.0\">\n";
?>
	<channel>
		<title>-=K1der=- The Chocolat Effect</title>
		<link>http://www.k1der.net</link>
		<image>
			<url>http://www.k1der.net/images/groslien.gif</url>
			<title>-=K1der=- The Chocolat Effect</title>
			<link>http://www.k1der.net</link>
		</image>
		<language>fr-fr</language>
		<copyright>Copyright 2001-2006 K1der Team</copyright>
		<description>Les dernieres news de K1der.net</description>
		<item>
		<title>Retour du flux RSS !</title>
		<link>http://www.k1der.net</link>
		<description>Depuis la nouvelle version du site le flux RSS avait disparut. Il est maintenant de nouveau disponible à l'adresse http://www.k1der.net/news/actualites.rss . Mettez à jour vos agrégateurs ;)</description>
		<author>no@spam.fr (Country)</author>
		<pubDate>Tue, 08 Mar 2006 16:10:00 +0200</pubDate>
		</item>
		<item>
		<title>Nouvelle version de K1der.net !</title>
		<link>http://www.k1der.net</link>
		<description>Nouvelle version de K1der.net ! Flux RSS de retour bientôt ;)</description>
		<author>no@spam.fr (Country)</author>
		<pubDate>Tue, 14 Feb 2006 00:37:22 +0200</pubDate>
		</item>
	</channel>
</rss>