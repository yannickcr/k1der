<SCRIPT LANGUAGE="JavaScript">
//vérifie si compatible
compat=false; if(parseInt(navigator.appVersion)>=3.0){compat=true}

//préload les images
if(compat)
{
	b_supp_on      = new Image;		b_supp_on.src      = "./images/icone_supp_on.gif";
	b_supp_off     = new Image;		b_supp_off.src     = "./images/icone_supp.gif";
	b_ren_on       = new Image;		b_ren_on.src       = "./images/icone_ren_on.gif";
	b_ren_off      = new Image;		b_ren_off.src      = "./images/icone_ren.gif";
	b_move_on      = new Image;		b_move_on.src      = "./images/icone_move_on.gif";
	b_move_off     = new Image;		b_move_off.src     = "./images/icone_move.gif";
	b_copy_on      = new Image;		b_copy_on.src      = "./images/icone_copy_on.gif";
	b_copy_off     = new Image;		b_copy_off.src     = "./images/icone_copy.gif";
	b_download_on  = new Image;		b_download_on.src  = "./images/icone_download_on.gif";
	b_download_off = new Image;		b_download_off.src = "./images/icone_download.gif";
}

//fonction pour faire changer d'image
function change(x,y) { if(compat) {document.images[x].src=eval(y+'.src');} }
</SCRIPT>

<?	
require("config_dir.php");
require("fonctions_dir.php");
AfficherEntete($strExplorateurTitre,"./config.css");

// ------------------------------ Initialisation des variables ----------------------------------------------- //

if(!empty($newfichier)) $newfichier = stripslashes($newfichier);
if(!empty($chemin))     $chemin     = stripslashes($chemin); else $chemin = ".";
if(!empty($fichier))    $fichier    = stripslashes($fichier);
if(!empty($place))      $place      = stripslashes($place);
if(!empty($rep))        $rep        = stripslashes($rep);
if(empty($tri))         $tri        = "NomASC";

// ----------------------------------- Sécurité navigation -------------------------------------------------- //

if( DeuxPointDansChemin($chemin) != false)
{
	$chemin = ".";
	unset($action);
	unset($rep);
}


$strTitre = $strExplorateurTitre;
$chemintotal = $cheminrelatif."/".$chemin;

// ----------------------------------- Gestion des actions -------------------------------------------------- //

switch($action)
{
	//  ------------------------------------ Renomer un fichier ou répertoire --------------------------------   //

	case "Renomer"          : if(file_exists("$chemintotal/$newfichier")) Message("$strExplorateurFichier$newfichier$strExplorateurAlertDeja");
							  else if(rename("$chemintotal/$fichier","$chemintotal/$newfichier")) Message("$strExplorateurFichier$fichier$strExplorateurMsgRenomer$newfichier");
						 	 	   else Message("$strExplorateurErreur");
					 		  break;

	//  ------------------------------------- Créer un répertoire --------------------------------------------   //

	case "CreerRep"		    : if(file_exists("$chemintotal/$rep")) Message("$strExplorateurRepertoire$rep$strExplorateurAlertDeja");
							  else if(mkdir("$chemintotal/$rep", 0777)) Message("$strExplorateurRepertoire$rep$strExplorateurMsgCreerRep");
								   else Message("$strExplorateurErreur");
							  break;

	//  ------------------------------------ Supprimer un fichier --------------------------------------------   //

	case "SupprimerFichier" : if(unlink("$chemintotal/$fichier")) Message("$strExplorateurFichier$fichier$strExplorateurMsgSupprimer");
							  else Message("$strExplorateurErreur");
							  break;


	//  ------------------------------------- Supprimer un répertoire ----------------------------------------   //

	case "SupprimerRep"     : if(rmdir("$chemintotal/$rep")) Message("$strExplorateurRepertoire$rep$strExplorateurMsgSupprimer");
							  else Message("$strExplorateurErreur");
							  break;

	//  ------------------------------------- Supprimer un répertoire non-vide -------------------------------  //

	case "SupprimerRepNV"   : if(EffacerRepertopireRecursif("$chemintotal/$fichier")) Message("$strExplorateurRepertoire$fichier$strExplorateurMsgSupprimer");
							  else Message("$strExplorateurErreur");
							  break;

	//  ------------------------------------- Télécharger un fichier -----------------------------------------   //

	case "Telecharger"      : if(copy("$fichier","$chemintotal/$fichier_name")) Message("$strExplorateurFichier$fichier_name$strExplorateurTelechargerSize$fichier_size$strExplorateurMsgTelecharger");
							  else Message("$strExplorateurErreur");
							  break;

	// -------------------------------------- Déplacer un fichier --------------------------------------------  //

	case "DeplacerFichier"  : if("$chemin/$fichier" != RecupereEmplacement($cheminrelatif,"$place/$fichier"))
							  {
								 if(copy("$chemintotal/$fichier","$place/$fichier"))
								 {
								 	if(unlink("$chemintotal/$fichier")) 
									{
										$NouvelEmplacement = RecupereEmplacement($cheminrelatif,"$place/$fichier");
										Message("$strExplorateurFichier$chemin/$fichier$strExplorateurMsgDeplacer$NouvelEmplacement");
									}
									else Message("$strExplorateurErreur");
								 }
								 else Message("$strExplorateurErreur");
							  }
							  else Message("$strExplorateurAlertSD");
							  break;

	//  ------------------------------------- Copier un fichier ----------------------------------------------  //

	case "CopierFichier"    : for($i=0;$i<$NbRepTotal;$i++)
							  {
								if($choix[$i] == "on")
								{
									if("$chemin/$fichier" != RecupereEmplacement($cheminrelatif,"$emplacement[$i]/$fichier"))
									{
										if(copy("$chemintotal/$fichier","$emplacement[$i]/$fichier")) 
										{ 
											$NouvelEmplacement = RecupereEmplacement($cheminrelatif,"$emplacement[$i]/$fichier");
											?><B CLASS="Important"><? echo $strExplorateurFichier; ?><? echo $chemin; ?>/<? echo $fichier; ?><? echo $strExplorateurMsgCopier; ?><? echo $NouvelEmplacement; ?></B><BR><? 
											$retouralaligne = true;
										}
										else Message("$strExplorateurErreur");
									}
									else Message("$strExplorateurAlertSD");
								}				
							  }

							  if($retouralaligne) { ?><BR>
<? }
							  break;

	// -------------------------------------- Déplacer un répertoire --------------------------------------------  //

	case "DeplacerRep"     : $Message[0] = $strExplorateurRepertoire;
							 $Message[1] = $strExplorateurMsgDeplacer;
							 $Message[2] = $NouvelEmplacement;
							 $Message[3] = $strExplorateurErreur;
							 $Message[4] = $strExplorateurAlertSD;

							 DeplacerRep($cheminrelatif,$chemin,$fichier,$place,$Message);
							 break;

	// -------------------------------------- Copier un répertoire --------------------------------------------  //

	case "CopierRep"       : $Message[0] = $strExplorateurRepertoire;
							 $Message[1] = $strExplorateurMsgDeplacer;
							 $Message[2] = $NouvelEmplacement;
							 $Message[3] = $strExplorateurErreur;
							 $Message[4] = $strExplorateurAlertSD;

							 CopierRep($cheminrelatif,$chemin,$fichier,$emplacement,$NbRepTotal,$choix,$Message);
							 break;
}

// ------------------------------- Récupération des fichiers et répertoires ------------------------------ //


$handle  = @opendir($chemintotal);
$file    = @readdir($handle);      // repertoire .
$file    = @readdir($handle);      // repertoire ..
$repind  = 0;
$fileind = 0;

while ($file = @readdir($handle))
{
	if(is_dir("$chemintotal/$file"))
	{
		$reptab[$repind]["nom"]           = $file;
		$reptab[$repind]["taille"]        = filesize("$chemintotal/$file");
		$reptab[$repind]["date"]          = GetDateStr(filemtime("$chemintotal/$file"));
		$reptab[$repind]["datetri"]       = FormatDate(filemtime("$chemintotal/$file"));
		$reptab[$repind]["permissions"]   = FormatePermissions(fileperms("$chemintotal/$file"));
		$repind++;
	}
	else
	{
		$filetab[$fileind]["nom"]         = $file;
		$filetab[$fileind]["taille"]      = filesize("$chemintotal/$file");
		$filetab[$fileind]["date"]        = GetDateStr(filemtime("$chemintotal/$file"));
		$filetab[$fileind]["datetri"]     = FormatDate(filemtime("$chemintotal/$file"));
		$filetab[$fileind]["permissions"] = FormatePermissions(fileperms("$chemintotal/$file"));
		$fileind++;
	}
}

@closedir($handle);

// --------------------------------------- Gestion des tris -------------------------------------------- //

switch($tri)
{
	// Tri par nom
	case "NomASC"      : if(count($reptab))  usort($reptab,TriNomASC);
					     if(count($filetab)) usort($filetab,TriNomASC);
					     break;
	case "NomDESC"     : if(count($reptab))  usort($reptab,TriNomDESC);
					     if(count($filetab)) usort($filetab,TriNomDESC);
					     break;			

	// Tri par taille
	case "TailleASC"   : if(count($reptab))  usort($reptab,TriTailleASC);
					     if(count($filetab)) usort($filetab,TriTailleASC);
					     break;
	case "TailleDESC"  : if(count($reptab))  usort($reptab,TriTailleDESC);
					     if(count($filetab)) usort($filetab,TriTailleDESC);
					     break;

	// Tri par date
	case "TriDateASC"  : if(count($reptab))  usort($reptab,TriDateASC);
						 if(count($filetab)) usort($filetab,TriDateASC);
			             break;
	case "TriDateDESC" : if(count($reptab))  usort($reptab,TriDateDESC);
						 if(count($filetab)) usort($filetab,TriDateDESC);
			             break;
}

$cheminencode = rawurlencode($chemin);
$CheminDecompose = DecomposerChemin($chemin,$action,$tri);

// --------------------------------------- Affichage -------------------------------------- //
?>
<P> <TABLE WIDTH="500" BORDER="0" CELLPADDING="1" CELLSPACING="1"> 
<? 

if($chemin != ".")
{
	$cheminretour = ModifChemin($chemin);
	$cheminretour = rawurlencode($cheminretour);

	?>
<?
}

$cheminencode = rawurlencode($chemin);

// -------------------------------------- Affichage des répertoires --------------------------------------- //

for($i=0;$i<$repind;$i++)
{
	$nomrep      = $reptab[$i]["nom"];
	$cheminrep   = rawurlencode($chemin."/".$nomrep);
	$repencode   = rawurlencode($nomrep);
	$IndiceImage = $i;

	?>
<?
}

// --------------------------------------- Affichage des fichiers ----------------------------------------- //

$IndiceImage++;


for($i=0;$i<$fileind;$i++)
{
	$nomfic      = $filetab[$i]["nom"];
	$ficencode   = rawurlencode($nomfic);
	$ext         = GetExtension($nomfic);
	$ext         = strtolower($ext);
	$icone       = GetIcone($ext);
	$affichage   = GetTypeAffichageFichier($ext);
	$type        = $affichage["Type"];
	$lien        = $affichage["Lien"];
	$IndiceImage += $i;


	?>
<?
}

if(($repind == "0") && ($fileind == "0")) { ?>
<TR><TD ALIGN="center"><B CLASS="Important"> 
<? echo $strExplorateurPasDeFichier; ?>
</B></TD></TR> 
<? }

// ------ fin du tableau ---- //

?>
</TABLE><BR>
<? $AfficherNbFileAndNbRep = 1; ?>
</BODY>
</HTML>}

if(($repind == "0") && ($fileind == "0")) { ?>
<TR><TD ALIGN="center"><B CLASS="Important"> 
<? echo $strExplorateurPasDeFichier; ?>
</B></TD></TR> 
<? }

// ------ fin du tableau ---- //

?>
</TABLE><BR>
<? $AfficherNbFileAndNbRep = 1; ?>
</BODY>
</HTML>