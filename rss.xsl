<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:output method="html" />
 <xsl:variable name="title" select="/rss/channel/title"/>
 <xsl:template match="/">
  <html>
  <head>
  <title><xsl:value-of select="$title"/> RSS Feed</title>
  <link rel="stylesheet" href="rss.css" type="text/css"/>
  </head>
  <xsl:apply-templates select="rss/channel"/></html>
 </xsl:template>
 <xsl:template match="channel">
  <body>
  <h1><a href="{link}"><xsl:value-of select="$title" /><img src="http://www.k1der.net/images/groslien.gif" width="468" height="60" alt="K1der - The Chocolat Effect" border="0" /></a></h1>
   <br />
  <div class="topbox">
   <div class="padtopbox">
    <h2>Où je suis tombé ?</h2>
    <p>Ceci est le flux RSS ("RSS Feed" en anglais) du site <xsl:value-of select="image/title" />. Les flux RSS vous permettent de rester au courant de l'actualité du site souhaité sans avoir à user votre touche F5 sur celui-ci.</p>
	<p>Pour vous inscrire à un flux RSS vous devez posséder un agrégateur.</p>
    <h3>Exemples d'agrégateurs RSS</h3>
	<ul>
		<li><a href="http://feedreader.com/">FeedReader</a> (Windows)</li>
		<li><a href="http://liferea.sourceforge.net/">Liferea</a> (Linux)</li>
		<li><a href="http://ranchero.com/netnewswire/">NetNewsWire</a> (Mac OSX)</li>
	</ul>
	<p>Les navigateurs modernes tels que <a href="http://www.mozilla.org/products/firefox/">Mozilla Firefox</a>, <a href="http://www.opera.com/">Opera</a> ou encore <a href="http://www.apple.com/macosx/features/safari/">Safari</a> possèdent leur propre lecteur de flux RSS.</p>
	<p>Certains client mail, comme <a href="http://www.mozilla.org/products/thunderbird/">Mozilla Thunderbird</a>, vous permettent aussi de consulter les flux RSS.</p>
   </div>
  </div>
  <div class="mainbox">
   <div class="itembox">
    <div class="paditembox">
     <xsl:apply-templates select="item"/>
     
    </div>
   </div>
   <div class="rhsbox">
    <div class="padrhsbox">
     <h2>S'inscrire à ce flux RSS</h2>
     <p>Pour cela copiez/collez le lien de ce flux RSS dans votre agrégateur RSS.</p>
     <p><a href="http://www.k1der.net/rss.php">http://www.k1der.net/rss.php</a></p>
    </div>
   </div>
  </div>
  </body>
 </xsl:template>
 <xsl:template match="item">
  <div id="item">
   <ul>
    <li><a href="{link}" class="item"><xsl:value-of select="title"/></a><br/>
     <div><xsl:value-of select="description" /></div>
    </li>
   </ul>
  </div>
 </xsl:template>
</xsl:stylesheet>
