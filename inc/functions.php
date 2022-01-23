<?
function vg_get($var)
{
if (isset($_GET[$var])) $var = $_GET[$var];
else $var = "";
return $var;
}
function vg_post($var)
{
if (isset($_POST[$var])) $var = $_POST[$var];
else $var = "";
return $var;
}
function vg_cookie($var)
{
if (isset($_COOKIE[$var])) $var = $_COOKIE[$var];
else $var = "";
return $var;
}
?>