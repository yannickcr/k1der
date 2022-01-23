<?
function postforum($fichier)
{
$buffer = file($fichier);
$i=0;
while($buffer[$i])
{
if ($i > 13)
{
if (!ereg("class=\"plein\"",$buffer[$i]))
{
$buffer[$i] = str_replace("images/","../images/",$buffer[$i]);
$post .= $buffer[$i];
}
}
$i++;
}
return $post;
}
?>