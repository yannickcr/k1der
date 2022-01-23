<? 
function get_meteo ($sortie)
{
if (eregi("<html>(.*)</html>", $sortie, $meteo))
return "<pre>".strip_tags($meteo[0])."</pre>";
}
ob_start("get_meteo");
readfile("http://www.google.fr");
ob_end_flush();
?>