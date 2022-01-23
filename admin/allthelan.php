<?
function date2timestamp($date,$format){
// Paramètres : 
   //    $date : date formattée comme renvoie date()
   //    $format : format de la date similire au paramètre de date()
/* exemple : date2timestamp("2001-07-11 16:00:00","Y-m-d h:i:s");
retourne 994860000
*/


   //jour
   $d = "([0-3][0-9])";
   $j = "([1-3]?[0-9])";
   // mois
   $m = "(0[0-9]|1[0-2])";
   $n = "([0-9]|1[0-2])";
   $F = "(January|February|March|April|May|June|July|August|September|October|November|December)";
   $M = "(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)";
   //annee
   $Y = "([0-9]{4,4})";
   $y = "([0-9]{2,2})";
   //heures
   $g = "([1]?[0-9])";
   $G = "([0-2]?[0-9])";
   $h = "([01][0-9])";
   $H = "([0-2][0-9])";
   //minutes
   $i = "([0-5][0-9])";
   //secondes
   $s = "([0-5][0-9])";
   
   $z = "([0-3]?[0-9]?[0-9])";
   $I = "[01]" ;
   
   $lesmois = array("January"=>1,"February"=>2,"March"=>3,"April"=>4,"May"=>5,"June"=>6,
   "July"=>7,"August"=>8,"September"=>9,"October"=>10,"November"=>11,"December"=>12,
   "Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,
   "Oct"=>10,"Nov"=>11,"Dec"=>12);
   
   $ok = array("M","F","I","d","j","m","n","y","Y","g","G","h","H","i","s","z");
   $nok = array("a","A","L","B","D","S","t","T","w","Z");

   $form_m = preg_replace("/([\(\)\[\]\{\}\?\.\*\?\$\^\/\\\\])/","\\\\$1",$format);
   $len = strlen($form_m);
   $form="";
   for($count=0;$count<$len;$count++)
      {
      $chr = substr($form_m,$count,1);
      if ($chr == "\\" || substr($form,-1,1) == "\\")
         {
         $form .= substr($form_m,$count,2);
         $count++;
         continue;
         }
      if (in_array($chr,$ok))
         $form .= $$chr; 
      else      
         if (in_array($chr,$nok))
            $form .= ".+"; 
         else
            if ($chr == "r")
               $form .= ", $d $M $Y $H:$i:$s [-+][0-9]{4,4}";
            else
               $form .= $chr;
      }
   
   $format = preg_replace("/(^|[^\\\\])(r)/","$1, d M Y H:i:s",$format);
   $form = preg_replace("/\\\\\\\\([a-zA-Z])/","$1",$form);
   preg_match("/$form/",$date,$reg);
  
   $len = strlen($format);
   $pos = 1;
   $annee = $mois = $jour = 0;

   for($count=0;$count<$len;$count++)
      {
      $chr = substr($format,$count,1);
      if ($chr == "\\")
         {
         $count++;
         continue;
         }
      if ($chr == "d" || $chr == "j")
         $jour = $reg[$pos++];
      if ($chr == "m" || $chr == "n")
         $mois = $reg[$pos++];
      if ($chr == "M" || $chr == "F")
         $mois = $lesmois[$reg[$pos++]];
      if ($chr == "y"|| $chr == "Y")
         $annee = $reg[$pos++];
      if ($chr == "g" || $chr == "h"||$chr == "G" || $chr == "H")
         $heure = $reg[$pos++];
      if ($chr == "i")
         $min = $reg[$pos++];
      if ($chr == "s" || $chr == "z")
         $sec = $reg[$pos++];
      if ($chr == "I")
         $dst = $reg[$pos++];
      }

   if ($jour == 0)
      return "Pas de jour specifie";
   if ($mois == 0)
      return "Pas de mois specifie";
   if ($annee == 0)
      return "Pas d\'annee specifiee";
   if (!isset($heure))
      $heure=0;
   if (!isset($min))
      $min=0;
   if (!isset($sec))
      $sec=0;
   if (!isset($dst))
      $dst=-1;
   $timestamp = mktime($heure, $min, $sec, $mois, $jour, $annee, $dst);
   return $timestamp;
}

					setlocale(LC_ALL, "fr");
					$debutan = date2timestamp($orderdate, "Ymd");
					$debutan = strftime("%d %B %Y",$debutan);
					
					$debutan = str_replace("January","Janvier",$debutan);
					$debutan = str_replace("February","Février",$debutan);
					$debutan = str_replace("March","Mars",$debutan);
					$debutan = str_replace("April","Avril",$debutan);
					$debutan = str_replace("May","Mai",$debutan);
					$debutan = str_replace("June","Juin",$debutan);
					$debutan = str_replace("July","Juillet",$debutan);
					$debutan = str_replace("August","Août",$debutan);
					$debutan = str_replace("September","Septembre",$debutan);
					$debutan = str_replace("October","Octobre",$debutan);
					$debutan = str_replace("November","Novembre",$debutan);
					$debutan = str_replace("December","Decembre",$debutan);
					
					$finan = date2timestamp($caca, "Ymd");
					$finan = strftime("%d %B %Y",$finan);
					
					$finan = str_replace("January","Janvier",$finan);
					$finan = str_replace("February","Février",$finan);
					$finan = str_replace("March","Mars",$finan);
					$finan = str_replace("April","Avril",$finan);
					$finan = str_replace("May","Mai",$finan);
					$finan = str_replace("June","Juin",$finan);
					$finan = str_replace("July","Juillet",$finan);
					$finan = str_replace("August","Août",$finan);
					$finan = str_replace("September","Septembre",$finan);
					$finan = str_replace("October","Octobre",$finan);
					$finan = str_replace("November","Novembre",$finan);
					$finan = str_replace("December","Decembre",$finan);

					$thedate ="du <b>$debutan</b> au <b>$finan</b> ( $dur jours )";
					
include "../cal/alltheregion.php";

					if (($adresse != "") && ($adresse != "?"))
					{
					$theadresse = "$adresse, $ville (<i>$region</i>, $dep)";
					}
					else
					{
					$theadresse = "$ville (<i>$region</i>, $dep)";
					}
					$tournois = '';
					
					if (($tournois1 == '') && ($tournois2 == '') && ($tournois3 == '') && ($tournois4 == '') && ($tournois5 == '') && ($tournois6 == '') && ($tournois7 == '') && ($tournois8 == ''))
						  {
						  $thetournois = "Non";
						  }
						  else
						  {
					if ($tournois1 != '')
					{
					$thetournois .= "<li>$tournois1<br>";
					}
					if ($tournois2 != '')
					{
					$thetournois .= "<li>$tournois2<br>";
					}
					if ($tournois3 != '')
					{
					$thetournois .= "<li>$tournois3<br>";
					}
					if ($tournois4 != '')
					{
					$thetournois .= "<li>$tournois4<br>";
					}
					if ($tournois5 != '')
					{
					$thetournois .= "<li>$tournois5<br>";
					}
					if ($tournois6 != '')
					{
					$thetournois .= "<li>$tournois6<br>";
					}
					if ($tournois7 != '')
					{
					$thetournois .= "<li>$tournois7<br>";
					}
					if ($tournois8 != '')
					{
					$thetournois .= "<li>$tournois8<br>";
					}
					}
					
				  if (($tournois1 == '') && ($tournois2 == '') && ($tournois3 == '') && ($tournois4 == '') && ($tournois5 == '') && ($tournois6 == '') && ($tournois7 == '') && ($tournois8 == ''))
				  {
				  $lotations = '';
				  }
				  else
				  {
				  $lotations = "<font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><strong>Lots :</strong></font> <font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'>$lots</font>";
				  }
				  
				  if ($infos == '')
				  {
				  }
				  else
				  {
				  $informations = "Infos compl&eacute;mentaires :</strong></font> <font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><br>$infos";
				  }
				  
$allthelan = "<table width=\'950\' border=\'0\' align=\'center\' cellpadding=\'0\' cellspacing=\'0\'>
  <tr> 
    <td width=\'870\' colspan=\'3\' valign=\'top\'> 
      <font size=\'5\' face=\'Verdana, Arial, Helvetica, sans-serif\'><b>$nom</b></font> </td>
  </tr>
  <tr> 
    <td colspan=\'3\' valign=\'top\'><table width=\'950\' border=\'0\' cellpadding=\'0\' cellspacing=\'0\'>
        <tr> 
          <td valign=\'top\'>&nbsp;</td>
          <td rowspan=\'7\' valign=\'top\'><div align=\'center\'><img src=\'../images/regions/$img.gif\' width=\'201\' height=\'186\'><br>
              <strong><font color=\'#CC0000\' size=\'1\' face=\'Verdana, Arial, Helvetica, sans-serif\'><br>
              <font size=\'2\'>$region</font></font></strong></div></td>
          <td valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td valign=\'top\'>&nbsp;</td>
          <td valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td width=\'75%\' valign=\'top\'><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><strong>Date 
            :</strong></font> <font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'>$thedate</font><font size=\'5\' face=\'Verdana, Arial, Helvetica, sans-serif\'>&nbsp;</font></td>
          <td width=\'25%\' valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td width=\'75%\'> <table width=\'100%\' border=\'0\' cellpadding=\'0\' cellspacing=\'0\'>
              <tr> 
                <td width=\'45\' valign=\'top\'><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><strong>Lieu 
                  :</strong></font></td>
                <td><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'>$theadresse</font></td>
              </tr>
            </table></td>
          <td width=\'25%\' valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td width=\'75%\' valign=\'top\'><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><strong>Site 
            Internet :</strong></font> <a target=_blank href=\'$site\'><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><b>$site</b></font></a><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><b> 
            </b></font></td>
          <td width=\'25%\' valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td width=\'75%\' valign=\'top\'><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><strong>E-Mail 
            :</strong></font> <strong><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'> 
            <a href=\'mailto:$mail\'>$mail</a> 
            </font></strong></td>
          <td width=\'25%\' valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td width=\'75%\' height=\'100\' valign=\'top\'>&nbsp;</td>
          <td width=\'25%\' height=\'100\' valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td colspan=\'3\' valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><strong>Nombre 
            de Places :</strong></font> <font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'>$places</font> </td>
          <td rowspan=\'5\' valign=\'top\'>&nbsp; </td>
          <td valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><strong>Prix 
            :</strong></font> <font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'>$prix &euro; </font></td>
          <td valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td valign=\'top\'> <table width=\'100%\' border=\'0\' cellpadding=\'0\' cellspacing=\'0\'>
              <tr> 
                <td width=\'60\' valign=\'top\'><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><strong>Tournois 
                  :</strong></font> </td>
                <td width=\'350\' valign=\'bottom\'> <font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'>$thetournois</font></td>
              </tr>
              <tr> 
                <td width=\'60\'>&nbsp;</td>
                <td width=\'350\'>&nbsp;</td>
              </tr>
            </table></td>
          <td valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td valign=\'top\'>$lotations</td>
          <td valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td valign=\'top\'>&nbsp;</td>
          <td valign=\'top\'>&nbsp;</td>
          <td valign=\'top\'>&nbsp;</td>
        </tr>
        <tr> 
          <td colspan=\'3\' valign=\'top\'><font size=\'2\' face=\'Verdana, Arial, Helvetica, sans-serif\'><strong>$informations</font></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td colspan=\'3\' valign=\'top\'>&nbsp;</td>
  </tr>
</table>";

