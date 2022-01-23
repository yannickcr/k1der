<?

function Message($chaine) { ?><B CLASS="Important"><? echo $chaine; ?></B><BR><BR><? }

// ----------------------------------------- Affichage Entete --------------------------------------------- //

function AfficherEntete($titre,$chemin)
{	
?>
<HTML>
<HEAD>
<TITLE><? echo $titre; ?></TITLE>
<LINK REL="stylesheet" TYPE="text/css" HREF="<? echo $chemin; ?>">
<SCRIPT LANGUAGE="JavaScript" SRC="fonctions.js"></SCRIPT>
</HEAD>
<?
}

// ----------------------------- Fonction de gestion du chemin --------------------------- //

function DeuxPointDansChemin($chemin) {	return strpos("$chemin",".."); }

function ModifChemin($chemin)
{
	$taille = strlen($chemin);
	$i = $taille;
	$fin = 0;

	while((!$fin) || (i > 0))
	{
		$i--;
		if($chemin[$i] == "/") $fin = $i;
	}

	$newchemin = substr($chemin,0,$fin);
	return $newchemin;
}

function DecomposerChemin($chemin,$action,$tri)
{
	$taille = strlen($chemin);
	$partie = "";

	$chemindecompose = "<A HREF=./index.php3?chemin=.&tri=$tri>.</A>";

	if($taille > 1)
	{
		$cumul = ".";

		for($i=2;$i<$taille;$i++)
		{
			if($chemin[$i] == "/")
			{
				$cumul = "$cumul/$partie";
				$cumulencode = rawurlencode($cumul);
				$chemindecompose = $chemindecompose."/<A HREF=./index.php3?chemin=$cumulencode&tri=$tri>$partie</A>";
				$partie = "";
			}
			else $partie = $partie.$chemin[$i];
		}
		$cumul = "$cumul/$partie";
		$cumulencode = rawurlencode($cumul);
		$chemindecompose = $chemindecompose."/<A HREF=./index.php3?chemin=$cumulencode&tri=$tri>$partie</A>";
	}

	return $chemindecompose;
}

function RecupereEmplacement($cheminrelatif,$emplacement)
{
	$Taillecheminrelatif  = strlen($cheminrelatif);
	$TailleEmplacement = strlen($emplacement);
	$longueur = $TailleEmplacement-$Taillecheminrelatif;
	$NouvelEmplacement = substr($emplacement,$Taillecheminrelatif,$longueur);
	$NouvelEmplacement = ".".$NouvelEmplacement;
	return $NouvelEmplacement;
}

// ----------------------- Fonction de gestions des extension et icônes ------------------------ //

function GetIcone($ext)
{
	switch($ext)
	{
		case "jpg"  : $icone = "image3.gif";		break;
		case "gif"  : $icone = "image2.gif";		break;
		case "png"  : $icone = "image2.gif";		break;
		case "bmp"  : $icone = "image1.gif";		break;
		case "tif"  : $icone = "image1.gif";		break;
		case "c"    : $icone = "c.gif";				break;
		case "cpp"  : $icone = "c.gif";				break;
		case "mpg"  : $icone = "movie.gif";			break;
		case "avi"  : $icone = "movie.gif";			break;
		case "mov"  : $icone = "movie.gif";			break;
		case "pdf"  : $icone = "pdf.gif";			break;
		case "ps"   : $icone = "ps.gif";			break;
		case "zip"  : $icone = "zip.gif";			break;
		case "ace"  : $icone = "compressed.gif";	break;
		case "tar"  : $icone = "tar.gif";			break;
		case "gz"   : $icone = "tar.gif";			break;
		case "uu"   : $icone = "uu.gif";			break;
		case "tex"  : $icone = "tex.gif";			break;
		case "txt"  : $icone = "text.gif";			break;
		case "mp3"  : $icone = "sound.gif";			break;
		case "wav"  : $icone = "sound.gif";			break;
		case "au"   : $icone = "sound.gif";			break;
		case "mid"  : $icone = "sound.gif";			break;
		case "rtf"  : $icone = "quill.gif";			break;
		case "doc"  : $icone = "doc.gif";			break;
		case "xls"  : $icone = "layout.gif";		break;
		case "ppt"  : $icone = "layout.gif";		break;
		case "pps"  : $icone = "php.gif";			break;
		case "inc"  : $icone = "php.gif";			break;
		case "php"  : $icone = "php.gif";			break;
		case "php3" : $icone = "php.gif";			break;
		case "php4" : $icone = "php.gif";			break;
		case "js"   : $icone = "script.gif";		break;
		case "css"  : $icone = "script.gif";		break;
		case "inc"  : $icone = "script.gif";		break;
		case "cgi"  : $icone = "script.gif";		break;
		case "html" : $icone = "html.gif";			break;
		case "htm"  : $icone = "html.gif";			break;
		case "exe"  : $icone = "exe.gif";			break;
		default     : $icone = "unknown.gif";		break;
	}
    return $icone;
}

function GetExtension($fichier)
{
	$taille = strlen($fichier);
	$i      = $taille;
	$fin    = 0;
	$boucle = 0;

	while(!$boucle)
	{
		$i--;
		if($i == 0) $boucle = 1;
		else if($fichier[$i] == ".")
			 {
				$fin = $i;
				$boucle = 1;
			 }
	}

	$fin++;
	$long = $taille - $fin;
	$ext = substr($fichier,$fin,$long);
	return $ext;
}

function GetTypeAffichageFichier($extension)
{
	switch($extension)
	{
		case "php3" : $type = "Source";			break;
		case "php4" : $type = "Source";			break;
		case "php"  : $type = "Source";			break;
		case "inc"  : $type = "Source";			break;
		case "pps"  : $type = "Source";		    break;
		case "html" : $type = "Source";			break;
		case "htm"  : $type = "Source";			break;
		case "txt"  : $type = "Source";			break;
		case "c"    : $type = "Source";			break;
		case "cpp"  : $type = "Source";			break;
		case "cgi"  : $type = "Source";			break;
		case "js"   : $type = "Source";			break;

		case "jpg"  : $type = "Image";		    break;
		case "gif"  : $type = "Image";		    break;
		case "bmp"  : $type = "Image";		    break;

		default     : $type = "Source";        break;
	}

	switch($type)
	{
		case "Source"	   : $affichage["Type"]    = "Source";	
							 $affichage["Lien"]    = "voirfichier.php3";
							 break;						 	
		case "Image"       : $affichage["Type"]    = "Image";
							 $affichage["Lien"]    = "voirfichier.php3";		
							 break;
	}	

	return $affichage;
}

// ---------------------------- Parte des Fonctions de gestion des dates -------------------------- //


function FormatDate($time)
{
	$d = getdate($time);
	$nojoursemaine = $d[wday];

	$date["nojour"]     = $d[mday];
	$date["nomois"]     = $d[mon];
	$date["noannee"]    = $d[year];
//	$date["nomjour"]    = GetNomJour($nojoursemaine);
//	$date["nomjour3L"]  = GetNomJour3L($nojoursemaine);
//	$date["nommois"]    = GetNomMois($date["nomois"]);
//	$date["nommois4L"]  = GetNomMois4L($date["nomois"]);

	return $date;
}

function GetDateStr($time)
{
	 $date = FormatDate($time);
	 return $date["nomjour3L"]." ".$date["nojour"]." ".$date["nommois4L"]." ".$date["noannee"];
}

function DateDuJour()
{
	 $date = FormatDate(time());
	 return $date["nojour"]." ".$date["nommois"]." ".$date["noannee"];
}

// --------------------------- fonction de gestion des permissions ------------------------- //

function FormatePermissions($mode)
{
	// Determine le type

		 if($mode & 0x1000) $type='p'; // FIFO pipe
	else if($mode & 0x2000) $type='c'; // Character special
	else if($mode & 0x4000) $type='d'; // Directory
	else if($mode & 0x6000) $type='b'; // Block special
	else if($mode & 0x8000) $type='-'; // Regular
	else if($mode & 0xA000) $type='l'; // Symbolic Link
	else if($mode & 0xC000) $type='s'; // Socket
	else $type='u'; // UNKNOWN

	// Determine les permissions par groupe

	$owner["read"]    = ($mode & 00400) ? 'r' : '-';
	$owner["write"]   = ($mode & 00200) ? 'w' : '-';
	$owner["execute"] = ($mode & 00100) ? 'x' : '-';
	$group["read"]    = ($mode & 00040) ? 'r' : '-';
	$group["write"]   = ($mode & 00020) ? 'w' : '-';
	$group["execute"] = ($mode & 00010) ? 'x' : '-';
	$world["read"]    = ($mode & 00004) ? 'r' : '-';
	$world["write"]   = ($mode & 00002) ? 'w' : '-';
	$world["execute"] = ($mode & 00001) ? 'x' : '-';

	// Adjuste pour SUID, SGID et sticky bit

	if( $mode & 0x800 ) $owner["execute"] = ($owner[execute]=='x') ? 's' : 'S';
	if( $mode & 0x400 ) $group["execute"] = ($group[execute]=='x') ? 's' : 'S';
	if( $mode & 0x200 ) $world["execute"] = ($world[execute]=='x') ? 't' : 'T';

	return "$type$owner[read]$owner[write]$owner[execute]$group[read]$group[write]$group[execute]$world[read]$world[write]$world[execute]";
}

// ------------------------------- fonction de gestion des tris ------------------------- //

function TriNomASC($elt1,$elt2)
{
	$nom1 = $elt1["nom"];
	$nom2 = $elt2["nom"];
	$val = strcmp($nom1,$nom2);
	return $val;
}

function TriNomDESC($elt1,$elt2)
{
	$nom1 = $elt1["nom"];
	$nom2 = $elt2["nom"];
	$val = strcmp($nom2,$nom1);
	return $val;
}

function TriTailleASC($elt1,$elt2)
{
	$taille1 = $elt1["taille"];
	$taille2 = $elt2["taille"];

	if ($taille1 == $taille2 )
	{
		$nom1 = $elt1["nom"];
		$nom2 = $elt2["nom"];
		$val = strcmp($nom1,$nom2);
	}
	else if ($taille1 > $taille2) $val = 1;
		 else $val = -1;

	return $val;
}

function TriTailleDESC($elt1,$elt2)
{
	$taille1 = $elt1["taille"];
	$taille2 = $elt2["taille"];

	if ($taille1 == $taille2 )
	{
		$nom1 = $elt1["nom"];
		$nom2 = $elt2["nom"];
		$val = strcmp($nom2,$nom1);
	}
	else if ($taille1 < $taille2) $val = 1;
		 else $val = -1;

	return $val;
}

function TriDateASC($elt1,$elt2)
{
	$date1 = $elt1["datetri"];
	$date2 = $elt2["datetri"];

	if ($date1["noannee"] == $date2["noannee"])
	{
		if($date1["nomois"] == $date2["nomois"])
		{
			if($date1["nojour"] == $date2["nojour"]) $val = 0;
			else if ($date1["nojour"] > $date2["nojour"]) $val = 1;
						   else $val = -1;
		}
		else if ($date1["nomois"] > $date2["nomois"]) $val = 1;
				  else $val = -1;
	}
	else if ($date1["noannee"] > $date2["noannee"]) $val = 1;
		 else $val = -1;

	return $val;
}

function TriDateDESC($elt1,$elt2)
{
	$date1 = $elt1["datetri"];
	$date2 = $elt2["datetri"];

	if ($date1["noannee"] == $date2["noannee"])
	{
		if($date1["nomois"] == $date2["nomois"])
		{
			if($date1["nojour"] == $date2["nojour"]) $val = 0;
			else if ($date1["nojour"] < $date2["nojour"]) $val = 1;
						   else $val = -1;
		}
		else if ($date1["nomois"] < $date2["nomois"]) $val = 1;
				  else $val = -1;
	}
	else if ($date1["noannee"] < $date2["noannee"]) $val = 1;
		 else $val = -1;

	return $val;
}


// -------------------------------- Fonctions de gestion de l'arborescence ------------------------- //

function GetNbRepertoire($chemin)
{
	$Nb = 0;

	$handle  = @opendir($chemin);
	$file    = @readdir($handle);      // repertoire .
	$file    = @readdir($handle);      // repertoire ..

	while ($file = @readdir($handle)) if(is_dir("$chemin/$file")) $Nb++;

	@closedir($handle);
	return $Nb;
}

function GetNiveauMax($chemin,$niveau,$max)
{
	$niveau++;
	if($max < $niveau) $max = $niveau;

	$handle  = @opendir($chemin);
	$file    = @readdir($handle);      // repertoire .
	$file    = @readdir($handle);      // repertoire ..

	while ($file = @readdir($handle)) if(is_dir("$chemin/$file"))	$max = GetNiveauMax("$chemin/$file",$niveau,$max);

	@closedir($handle);
	return $max;
}

function TriRep($rep1,$rep2) {	$val = strcmp($rep1,$rep2);	return $val; }


// -------------------------------------- Fonctions de gestion des répertoires ---------------------------------------  //

// fonction qui efface un répertoire non vide
function EffacerRepertopireRecursif($chemin)
{
	$correct = 1;
	$handle  = @opendir($chemin);
	$file    = @readdir($handle);      // repertoire .
	$file    = @readdir($handle);      // repertoire ..

	while($file = @readdir($handle))
	{
		if(is_dir("$chemin/$file"))
		{
			if(EstVide("$chemin/$file")) { if(!rmdir("$chemin/$file")) $correct = 0; }
			else $correct = EffacerRepertopireRecursif("$chemin/$file");
		}
		else unlink("$chemin/$file");
	}
	
	if(!rmdir($chemin)) $correct = 0;

	@closedir($handle);
	return $correct;
}

// fonction qui test si un répertoire est vide
function EstVide($chemin)
{
	$handle  = @opendir($chemin);
	$file    = @readdir($handle);      // repertoire .
	$file    = @readdir($handle);      // repertoire ..

	if($file = @readdir($handle)) $val = 0;
	else $val = 1;

	@closedir($handle);
	return $val;
}

// fonction qui copie un répertoire et son contenu
function CopierRepRecursif($source,$destination)
{
	$correct = 1;
	$handle  = @opendir($source);
	$file    = @readdir($handle);      // repertoire .
	$file    = @readdir($handle);      // repertoire ..

	if(mkdir($destination,0777))
	{
		while($file = @readdir($handle))
		{
			if(is_dir("$source/$file"))	$correct = CopierRepRecursif("$source/$file","$destination/$file");
			else if(!copy("$source/$file","$destination/$file")) $correct = 0;
		}
	}
	else $correct = 0;

	@closedir($handle);

	return $correct;
}

// fonction qui déplace un répertoire
function DeplacerRep($cheminrelatif,$chemin,$fichier,$place,$Message)
{
	$strExplorateurRepertoire  = $Message[0];
	$strExplorateurMsgDeplacer = $Message[1];
	$NouvelEmplacement         = $Message[2];
	$strExplorateurErreur      = $Message[3];
	$strExplorateurAlertSD	   = $Message[4];


	$chemintotal = $cheminrelatif."/".$chemin;

	if("$chemin/$fichier" != RecupereEmplacement($cheminrelatif,"$place/$fichier"))
	{									
		if(EstVide("$chemintotal/$fichier"))
		{
			if(mkdir("$place/$fichier",0777))
			{
				if(rmdir("$chemintotal/$fichier")) 
				{
					$NouvelEmplacement = RecupereEmplacement($cheminrelatif,"$place/$fichier");
					Message("$strExplorateurRepertoire$chemin/$fichier$strExplorateurMsgDeplacer$NouvelEmplacement");
				}
				else Message("$strExplorateurErreur");
			}
			else Message("$strExplorateurErreur");
		}
		else
		{
			if(CopierRepRecursif("$chemintotal/$fichier","$place/$fichier"))
			{
				if(EffacerRepertopireRecursif("$chemintotal/$fichier"))
				{
					$NouvelEmplacement = RecupereEmplacement($cheminrelatif,"$place/$fichier");
					Message("$strExplorateurRepertoire$chemin/$fichier$strExplorateurMsgDeplacer$NouvelEmplacement");
				}
				else Message("$strExplorateurErreur");
			}
			else Message("$strExplorateurErreur");
		}
	}
	else Message("$strExplorateurAlertSD");	
}

// fonction qui copie un répertoire
function CopierRep($cheminrelatif,$chemin,$fichier,$emplacement,$NbRepTotal,$choix,$Message)
{
	$strExplorateurRepertoire  = $Message[0];
	$strExplorateurMsgDeplacer = $Message[1];
	$NouvelEmplacement         = $Message[2];
	$strExplorateurErreur      = $Message[3];
	$strExplorateurAlertSD	   = $Message[4];

	$chemintotal = $cheminrelatif."/".$chemin;

    if(EstVide("$chemintotal/$fichier"))
	{
		for($i=0;$i<$NbRepTotal;$i++)
		{
			if($choix[$i] == "on")
			{		
				if("$chemin/$fichier" != RecupereEmplacement($cheminrelatif,"$emplacement[$i]/$fichier"))
				{	
					if(mkdir("$emplacement[$i]/$fichier",0777))
					{
						$NouvelEmplacement = RecupereEmplacement($cheminrelatif,"$emplacement[$i]/$fichier");
						?><B CLASS="Important"><? echo "$strExplorateurRepertoire$chemin/$fichier$strExplorateurMsgDeplacer$NouvelEmplacement"; ?><BR></B><?
						$retouralaligne = true;
					}
					else Message("$strExplorateurErreur");
				}
				else Message("$strExplorateurAlertSD");
			}
		}
	}
	else
	{
		for($i=0;$i<$NbRepTotal;$i++)
		{
			if($choix[$i] == "on")
			{		
				if("$chemin/$fichier" != RecupereEmplacement($cheminrelatif,"$emplacement[$i]/$fichier"))
				{	
					if(CopierRepRecursif("$chemintotal/$fichier","$emplacement[$i]/$fichier"))
					{
						$NouvelEmplacement = RecupereEmplacement($cheminrelatif,"$emplacement[$i]/$fichier");
						?><B CLASS="Important"><? echo "$strExplorateurRepertoire$chemin/$fichier$strExplorateurMsgDeplacer$NouvelEmplacement"; ?><BR></B><?
						$retouralaligne = true;
					}
					else Message("$strExplorateurErreur");
				}
				else Message("$strExplorateurAlertSD");
			}
		}
	}

    if($retouralaligne) { ?><BR><? }
}

?>)
				{	
					if(CopierRepRecursif("$chemintotal/$fichier","$emplacement[$i]/$fichier"))
					{
						$NouvelEmplacement = RecupereEmplacement($cheminrelatif,"$emplacement[$i]/$fichier");
						?><B CLASS="Important"><? echo "$strExplorateurRepertoire$chemin/$fichier$strExplorateurMsgDeplacer$NouvelEmplacement"; ?><BR></B><?
						$retouralaligne = true;
					}
					else Message("$strExplorateurErreur");
				}
				else Message("$strExplorateurAlertSD");
			}
		}
	}

    if($retouralaligne) { ?><BR><? }
}

?>