<STYLE type=text/css>A {
	TEXT-DECORATION: none
}
</STYLE>
<body bgcolor="#FFFFFF" text="#000000" link="#DE0200" vlink="#DE0200" alink="#DE0200">
<?
require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass");
mysql_select_db("$dbbase",$db);

$rqt = MYSQL_QUERY("SELECT id FROM $TBL_NEWS");
$tot = MYSQL_NUM_ROWS($rqt);

$req = MYSQL_QUERY("SELECT * FROM $TBL_NEWS ORDER BY id DESC LIMIT 0, $limit");
$res = MYSQL_NUM_ROWS($req);
?>

<div align="center">

<table border="0" cellpadding="1" cellspacing="0" BGcolor="#000000">
<tr><td>
        <table border="0" cellpadding="0" cellspacing="3" BGcolor="#FFFFFF" height="33">
          <tr><td>
              <table border="0" cellpadding="0" cellspacing="0" BGcolor="<? echo $bgcolor_corp; ?>" height="21">
                <?
$i=0;
WHILE($i!=$res)
{
$id    = mysql_result($req,$i,"id");
$titre = mysql_result($req,$i,"titre");
$date  = substr(mysql_result($req,$i,"date"),0,5);

if(strlen($titre)>=13){ $titre = substr($titre,0,13)."..."; }

?>
                <tr>
                  <td nowrap> <font style="<? echo $Headline; ?>"> &nbsp;<img src="images/fle_noi.gif" border="0"><a href="<? echo $PATH_INDEX; ?>#id_news_<? echo $id; ?>"> 
                    <? echo $titre; ?>
                    </a></font> </td>
                </tr>
                <?
$i++;
}
?>
              </table>
</td></tr>
</table>
</td></tr>
</table></div>