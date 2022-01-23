<?
$dep = $disp[dep];
$img = "map";
$region = "inconnue";
//Bretagne
if (($dep == "29") or ($dep == "22") or ($dep == "56") or ($dep == "35"))
{
$region = "Bretagne";
$img = 6;
}

//Basse-Normandie
if (($dep == "50") or ($dep == "14") or ($dep == "61"))
{
$region = "Basse-Normandie";
$img = 4;
}

//Pays de la Loire
if (($dep == "44") or ($dep == "53") or ($dep == "85") or ($dep == "49") or ($dep == "72"))
{
$region = "Pays de la Loire";
$img = 19;
}

//Poitou-Charentes
if (($dep == "17") or ($dep == "79") or ($dep == "86") or ($dep == "16"))
{
$region = "Poitou-Charentes";
$img = 21;
}

//Centre
if (($dep == "28") or ($dep == "45") or ($dep == "41") or ($dep == "37") or ($dep == "36") or ($dep == "18"))
{
$region = "Centre";
$img = 7;
}

//Ile-de-France
if (($dep == "95") or ($dep == "75") or ($dep == "78") or ($dep == "91") or ($dep == "77") or ($dep == "92") or ($dep == "93") or ($dep == "94"))
{
$region = "Ile-de-France";
$img = 13;
}

//Haute-Normandie
if (($dep == "76") or ($dep == "27"))
{
$region = "Haute-Normandie";
$img = 12;
}

//Picardie
if (($dep == "80") or ($dep == "02") or ($dep == "60"))
{
$region = "Picardie";
$img = 20;
}

//Nord-Pas-De-Calais
if (($dep == "62") or ($dep == "59"))
{
$region = "Nord-Pas-De-Calais";
$img = 18;
}

//Corse
if (($dep == "2A") or ($dep == "2B") or ($dep == "20"))
{
$region = "Corse";
$img = 9;
}

//Champagne-Ardenne
if (($dep == "08") or ($dep == "51") or ($dep == "10") or ($dep == "52"))
{
$region = "Champagne-Ardenne";
$img = 8;
}

//Lorraine
if (($dep == "55") or ($dep == "57") or ($dep == "54") or ($dep == "88"))
{
$region = "Lorraine";
$img = 16;
}

//Alsace
if (($dep == "67") or ($dep == "68"))
{
$region = "Alsace";
$img = 1;
}

//Bourgogne
if (($dep == "89") or ($dep == "58") or ($dep == "21") or ($dep == "71"))
{
$region = "Bourgogne";
$img = 5;
}

//Franche-Comté
if (($dep == "70") or ($dep == "90") or ($dep == "25") or ($dep == "39"))
{
$region = "Franche-Comté";
$img = 11;
}

//Rhone-Alpes
if (($dep == "42") or ($dep == "69") or ($dep == "01") or ($dep == "74") or ($dep == "38") or ($dep == "73") or ($dep == "07") or ($dep == "26"))
{
$region = "Rhone-Alpes";
$img = 23;
}

//Auvergne
if (($dep == "03") or ($dep == "63") or ($dep == "15") or ($dep == "43"))
{
$region = "Auvergne";
$img = 3;
}

//Limousin
if (($dep == "87") or ($dep == "23") or ($dep == "19"))
{
$region = "Limousin";
$img = 15;
}

//Aquitaine
if (($dep == "33") or ($dep == "24") or ($dep == "47") or ($dep == "40") or ($dep == "64"))
{
$region = "Aquitaine";
$img = 2;
}

//Midi-Pyrénées
if (($dep == "46") or ($dep == "12") or ($dep == "82") or ($dep == "81") or ($dep == "32") or ($dep == "31") or ($dep == "65") or ($dep == "09"))
{
$region = "Midi-Pyrénées";
$img = 17;
}

//Languedoc-Roussillon
if (($dep == "48") or ($dep == "30") or ($dep == "34") or ($dep == "11") or ($dep == "66"))
{
$region = "Languedoc-Roussillon";
$img = 14;
}

//Provence-Alpes-Côte d'Azur
if (($dep == "05") or ($dep == "04") or ($dep == "84") or ($dep == "13") or ($dep == "83") or ($dep == "06"))
{
$region = "Provence-Alpes-Côte d'Azur";
$img = 22;
}
?>
