<?
/////////////////////////////////////////////////////////////////////
//
//	DLCount Admin 1.00
//	by Sascha Blansjaar
//
//	Feel free to modify this script in any way you want.
//
//	No link back is required but much appreciated.
//	Link back to http://phtml.com/
//
/////////////////////////////////////////////////////////////////////

// insert the full path to your counter text file here
$dlcountfile = "dlcount.text";
// enter your password here
$password = "test";
// nothing needs to be changed beyond this line
$fontf = "Verdana, Arial, Helvetica, Lucida, sans-serif";
$fonts = "-1";
$trcolorh = "#FFFF66";

function usecolor()
{
	$trcolor1 = "#FFFFCC";
	$trcolor2 = "#FFFF99";
	static $colorvalue;

	if($colorvalue == $trcolor1)
		$colorvalue = $trcolor2;
	else
		$colorvalue = $trcolor1;

	return($colorvalue);
}
	echo "<html><head><title>DLCount Admin</title></head>";
	echo "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#0000FF\" vlink=\"#0000FF\" alink=\"#FF0000\">";
	echo "<table width=\"100%\" border=\"0\">";
	echo "<tr bgcolor=\"$trcolorh\"><td><font face=\"$fontf\" size=\"$fonts\"><b>Page</b></font></td>";
	echo "<td><font face=\"$fontf\" size=\"$fonts\"><b>Hits</b></font></td>";
	echo "<td><font face=\"$fontf\" size=\"$fonts\"><b>Since</b></font></td></tr>";

	if (file_exists($dlcountfile))
	{
		$temparray = file($dlcountfile);
		for($index = 0; $index < count($temparray); $index++)
		{
			$entry = explode("|",$temparray[$index]);
			$trcolor = usecolor();
			echo "<tr bgcolor=\"$trcolor\"><td><font face=\"$fontf\" size=\"$fonts\"><a href=\"$entry[1]\">$entry[1]</a></font></td>";
			echo "<td><font face=\"$fontf\" size=\"$fonts\">$entry[0]</font></td>";
			echo "<td><font face=\"$fontf\" size=\"$fonts\">$entry[2]</font></td></tr>";
		}
	}
	else
	{
		echo "<tr><td>DLCount Admin error: $dlcountfile missing!</td></tr>";
	}

	echo "</table>";
	echo "</body></html>";
?>