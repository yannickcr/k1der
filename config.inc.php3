<?
/* connexion � la base de donn�es ------------------------------------------------- */
$dbhost = "localhost";      // host ("localhost" ou "sql.free.fr" pour Free)
$dblogi = "root";            // login de la base de donn�es
$dbpass = "";            // password de la base de donn�es
$dbbase = "k1der1";            // nom de la base de donn�es
$ftp_server="127.0.0.1";
$ftp_user_name="";
$ftp_user_pass="";


/* Noms des Tables mySQL ---------------------------------------------------------- */
$TBL_NEWS         = "mynewsinfos";
$TBL_COMMENTAIRES = "mynewscomments";
$TBL_BUENO = "bueno";
$TBL_SURPRISE = "surprise";
$TBL_COUNTRY = "country";
$TBL_PINGUI = "pingui";
$TBL_MAXI = "maxi";


/* Vos infos persos pour l'envoi des news aux amis des visiteurs ------------------ */
$URL        = "http://www.k1der.net";                  // Url de votre site
$NAME       = "-=K1der=- The Chocolat Effect";                                // Le nom de votre site
$EMAIL      = "country@k1der.net";                   // Votre Email
$PATH_INDEX = "index.php";   // Chemin complet de la page qui appelle "menu.php3" & "news.php3"

/* ceci concerne le menu et les news ---------------------------------------------- */
$limit      = "5";                    // nombre de titres de news � afficher (menu)
$limit_news = "10";                   // nombre de news � afficher
/* Affichage des 2 ic�nes en haut � droite ---------------------------------------- */
$ICONE_PRINT = "oui";  // "oui" ou "non"
$ICONE_SEND  = "oui";  // "oui" ou "non"  -- Mettez "non" si vous ne pouvez pas utiliser la fonction MAIL(), (ex. free.fr)

/* Police des news (CSS) ---------------------------------------------------------- */
$Headline  = "font-family: verdana; font-size: 8pt; color: #000000";                      // Headline (Menu)
$TitreNews = "font-family: verdana; font-size: 9pt; color: #FFFFFF; font-weight: bold";   // Titre
$DateNews  = "font-family: verdana; font-size: 8pt; color: #F0F0F0";                      // Date - Heure
$CorpsNews = "font-family: arial; font-size: 9pt; color: #000000";                        // Corps
$Comment   = "font-family: verdana; font-size: 8pt; color: #FFFFFF";                      // Infos/Source/Commentaires/Top
$Comment2   = "font-family: verdana; font-size: 8pt; color: #000000";                      // Infos/Source/Commentaires/Top


/* Couleurs de fond ---------------------------------------------------------------- */
$bgcolor_haut = "#DE0200";           // couleur de fond du titre de la news
$bgcolor_corp = "#FFFFFF";           // couleur de fond du corps de la news


/* Informations diverses ----------------------------------------------------------- */
$TOP = "#TOP";                       // Retour en haut de page

/* Language ------------------------------------------------------------------------ */
$MenuTitle      = "Derni�res News";  // titre du menu
$InfoTitle      = "Post� par";           // titre infos
$SourceTitle    = "";              // titre source

$NoCommentTitle = "aucun commentaire";   // lorsqu'il n'y a aucun commentaire sur une news
$UnCommentTitle = "1 commentaire";       // lorsqu'il n'y a qu'un seul commentaire
$CommentsTitle  = "commentaires";        // lorsqu'il y a plusieurs commentaires (on ajoute un "s" � la fin)
?>
