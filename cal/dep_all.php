<?
//Bretagne
if ($region == "Bretagne")
{
$requete  = "SELECT * FROM calendrier WHERE dep='29' or dep='22' or dep='56' or dep='35'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 6;
}

//Basse-Normandie
if ($region == "Basse-Normandie")
{
$requete  = "SELECT * FROM calendrier WHERE dep='50' or dep='14' or dep='61'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 4;
}

//Pays de la Loire
if ($region == "Pays de la Loire")
{
$requete  = "SELECT * FROM calendrier WHERE dep='44' or dep='53' or dep='49' or dep='72'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 19;
}

//Poitou-Charentes
if ($region == "Poitou-Charentes")
{
$requete  = "SELECT * FROM calendrier WHERE dep='17' or dep='79' or dep='86' or dep='16'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 21;
}

//Centre
if ($region == "Centre")
{
$requete  = "SELECT * FROM calendrier WHERE dep='28' or dep='45' or dep='41' or dep='37' or dep='36' or dep='18'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 7;
}

//Ile-de-France
if ($region == "Ile-de-France")
{
$requete  = "SELECT * FROM calendrier WHERE dep='95' or dep='75' or dep='78' or dep='91' or dep='77' or dep='92' or dep='93' or dep='94'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 13;
}

//Haute-Normandie
if ($region == "Haute-Normandie")
{
$requete  = "SELECT * FROM calendrier WHERE dep='76' or dep='27'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 12;
}

//Picardie
if ($region == "Picardie")
{
$requete  = "SELECT * FROM calendrier WHERE dep='80' or dep='02' or dep='60'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 20;
}

//Nord-Pas-De-Calais
if ($region == "Nord-Pas-De-Calais")
{
$requete  = "SELECT * FROM calendrier WHERE dep='62' or dep='59'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 18;
}

//Corse
if ($region == "Corse")
{
$requete  = "SELECT * FROM calendrier WHERE dep='2B' or dep='2A' or dep='20'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 9;
}

//Champagne-Ardenne
if ($region == "Champagne-Ardenne")
{
$requete  = "SELECT * FROM calendrier WHERE dep='08' or dep='51' or dep='10' or dep='52'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 8;
}

//Lorraine
if ($region == "Lorraine")
{
$requete  = "SELECT * FROM calendrier WHERE dep='55' or dep='57' or dep='54' or dep='88'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 16;
}

//Alsace
if ($region == "Alsace")
{
$requete  = "SELECT * FROM calendrier WHERE dep='67' or dep='68'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 1;
}

//Bourgogne
if ($region == "Bourgogne")
{
$requete  = "SELECT * FROM calendrier WHERE dep='89' or dep='58' or dep='21' or dep='71'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 5;
}

//Franche-Comté
if ($region == "Franche-Comté")
{
$requete  = "SELECT * FROM calendrier WHERE dep='70' or dep='90' or dep='25' or dep='39'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 11;
}

//Rhone-Alpes
if ($region == "Rhone-Alpes")
{
$requete  = "SELECT * FROM calendrier WHERE dep='42' or dep='69' or dep='01' or dep='38' or dep='74' or dep='73' or dep='07' or dep='26'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
if (($dep == "42") or ($dep == "69") or ($dep == "01") or ($dep == "74") or ($dep == "38") or ($dep == "73") or ($dep == "07") or ($dep == "26"))
$img = 23;
}

//Auvergne
if ($region == "Auvergne")
{
$requete  = "SELECT * FROM calendrier WHERE dep='63' or dep='15' or dep='43'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 3;
}

//Limousin
if ($region == "Limousin")
{
$requete  = "SELECT * FROM calendrier WHERE dep='87' or dep='23' or dep='19'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 15;
}

//Aquitaine
if ($region == "Aquitaine")
{
$requete  = "SELECT * FROM calendrier WHERE dep='33' or dep='24' or dep='47' or dep='40' or dep='64'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 2;
}

//Midi-Pyrénées
if ($region == "Midi-Pyrénées")
{
$requete  = "SELECT * FROM calendrier WHERE dep='46' or dep='12' or dep='82' or dep='81' or dep='32' or dep='31' or dep='65' or dep='09'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 17;
}

//Languedoc-Roussillon
if ($region == "Languedoc-Roussillon")
{
$requete  = "SELECT * FROM calendrier WHERE dep='48' or dep='30' or dep='34' or dep='11' or dep='66'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 14;
}

//Provence-Alpes-Côte d'Azur
if ($region == "Provence-Alpes-Côte dAzur")
{
$requete  = "SELECT * FROM calendrier WHERE dep='02' or dep='04' or dep='84' or dep='13' or dep='83' or dep='06'";
$req = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
$img = 22;
}
?>
