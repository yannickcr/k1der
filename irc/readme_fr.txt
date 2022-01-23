Plouf's Java IRC Client Applet
------------------------------

Manuel du webmaster
-------------------

Fichiers
--------

  Liste :
    irc.cab
    securedirc.cab
    irc.jar
    IRCApplet.class

  D�tails :
    irc.cab : Fichier d'archive pour Internet Explorer - obligatoire
    securedirc.cab : Fichier d'archive sign� pour Internet Explorer - facultatif
    irc.jar : Fichier d'archive pour Netscape ou autre navigateur - obligatoire
    IRCApplet.class : Lanceur, programme principal - obligatoire

Fragment HTML de l'applet
-------------------------

  <applet code=IRCApplet.class archive="irc.jar" width=640 height=400>
  <param name="CABINETS" value="irc.cab,securedirc.cab">

  ...
  param�tres facultatifs
  ...

  </applet>

  width et height peut �tre adapt�s au besoin
  securedirc.cab peut �tre enlev�

Param�tres
----------
  Les param�tres sont envoy�s vers l'applet en suivant la syntaxe suivante :
    <param name="name" value="value">

Param�tres obligatoires
-----------------------

  nick : Nick � utiliser par d�faut. Les '?' seront remplac�s par des chiffres al�atoires.
    Exemple :
      <param name="nick" value="Guest??">   dira � l'applet d'utiliser des nicks du genre
                                            Guest47

  name : "Vrai" nom de l'utilisateur, envoy� au serveur irc.
    Exemple :
      <param name="name" value="UserName">


  host : Nom de domaine du serveur irc.
    Exemple :
      <param name="host" value="irc.server.net">

  port : Port du serveur irc.
    Exemple :
      <param name="port" value="6667">

Param�tres facultatifs
----------------------

  commandX, avec x un nombre : ordonne � l'applet d'ex�cuter cette commande une fois qu'elle est
  --------                     connect�e au serveur.

                               La premi�re commande DOIT �tre command1, et il ne peut pas y
                               avoir de "trous" dans la num�rotation. La command14 DOIT se
                               trouver apr�s la command13, et PAS apres command12.

                               Ces commandes ne sont pas envoy�es via l'interpreteur, ce qui
                               entraine comme cons�quence que seules les commandes directement
                               comprises par le serveur peuvent �tre utilis�es. Ces commandes
                               ne doivent PAS commencer par '/'.

    Exemple : 
      <param name="command1" value="nickserv identify password">
      <param name="command2" value="join #channel">

  basecolor : Ordonne � l'applet de calculer automatiquement toutes les couleurs, en se basant sur
  ---------   les valeurs de rouge, vert et bleu donn�es. Ces valeurs vont de 0 � 1000.

    Exemple :
      <param name="basecolor" value="384,256,128">

  colorI, avec I un nombre : Ordonne � l'applet de modifier la couleur dont on donne l'indice. Ces
  ------                     commandes sont prises en compte APRES la commande basecolor.

          Les indices suivants sont d�finis :
             0 : Couleur noire
             1 : Couleur blanche
             2 : Couleur gris fonc�e
             3 : Couleur grise
             4 : Couleur gris claire
             5 : Couleur d'avant-plan
             6 : Couleur d'arri�re-plan
             7 : Couleur de s�lection
             8 : Couleur d'�v�nement
             9 : Couleur de la fermeture
            10 : Couleur du voice
            11 : Couleur de l'op
            12 : Couleur du semi-op
            13 : Couleur ASV homme
            14 : Couleur ASV femme
            15 : Couleur ASV inconnu

    Exemple :
      <param name="color1" value="C0C000">

  helppage : configure la page d'aide � ouvrir quand l'utilisateur clique sur le bouton d'aide
  --------   sur l'interface graphique.

    Exemple :
      <param name="helppage" value="http://www.yahoo.com">

  timestamp : branche ou coupe l'horodateur. Par d�faut, l'orodateur est coup�.
  ---------   

    Exemple :
      <param name="timestamp" value="true">

  language : Modifie la langue utilis�e dans PJIRC. Les langues english, french et italian sont
  --------   support�es. Par d�faut, english est utilis�.

    Exemple :
      <param name="language" value="french">

  smileys : Branche ou coupe la gestion graphique des �moticons. Par d�faut, les �moticons graphiques
  -------   sont d�sactiv�s.

    Exemple :
      <param name="smileys" value="true">

  highlight : Branche ou coupe l'illumination de mots. Si cette option n'est pas branch�e, aucune
  ---------   illumination ne sera effectu�e, quelle que soit la valeur des autre options. Par d�faut,
              l'illumination est coup�e.

    Exemple :
      <param name="highlight" value="true">

  highlightnick : si l'illumination est branch�e, cette option ordonne � l'applet d'illuminer
  -------------   toute phrase contenant le nick de l'utilisateur. Par d�faut, cette option
                  est coup�e.

    Exemple :
      <param name="highlightnick" value="true">

  highlightcolor : si l'illumination est branch�e, sp�cifie quelle couleur doit �tre utilis�e
  --------------   pour l'illumination. Par d�faut, cette couleur est la couleur 5.

    Exemple :
      <param name="highlightcolor" value="9">

  highlightwords : si l'illumination est branch�e, donne une liste de mots qui entrainent
  --------------   l'illumination de la phrase dans laquelle ils sont pr�sents. Les mots
                   sont s�par�s par des espaces.

    Exemple :
      <param name="highlightwords" value="word1 word2 word3">

  quitmessage : r�gle le message de quit par d�faut. Par d�faut, ce message est vide.
  -----------

    Exemple :
      <param name="quitmessage" value="PJIRC forever!">

  asl : branche ou coupe la gestion asv (age, sexe, ville). L'asv est r�cup�r� � partir du nom
  ---   complet de l'utilisateur. En fonction de ces informations, un affichage sp�cifique sera
        activ� pour le nick. Le format du nom complet doit �tre "age sexe ville", par exemple
        "22 h Namur". Par d�faut, la gestion asv est coup�e.

    Exemple :
      <param name="asv" value="true">

  aslmale : Modifie la cha�ne de caract�res correspondant au genre masculin dans l'asv. Par d�faut,
  -------   cette cha�ne vaut "m".

    Exemple :
      <param name="aslmale" value="h">

  aslfemale : Modifie la cha�ne de caract�res correspondant au genre f�minin dans l'asv. Par d�faut,
  ---------   cette cha�ne vaut "f".

    Exemple :
      <param name="aslfemale" value="f">

  showconnect : Branche ou coupe la visibilit� du bouton de connexion dans l'interface. Par d�faut,
  -----------   ce bouton est visible.

    Exemple :
      <param name="showconnect" value="true">

  showchanlist : Branche ou coupe la visibilit� du bouton de liste des canaux dans l'interface. Par
  ------------   d�faut, ce bouton est visible.

    Exemple :
      <param name="showchanlist" value="true">

  showabout : Branche ou coupe la visibilit� du bouton � propos dans l'interface. Par d�faut, ce
  ---------   bouton est visible.

    Exemple :
      <param name="showabout" value="true">

  showhelp : Branche ou coupe la visibilit� du bouton d'aide dans l'interface. Par d�faut, ce bouton
  --------   est visible.

    Exemple :
      <param name="showhelp" value="true">


  bitmapsmileys : Branche ou coupe la gestion des �moticons graphiques depuis image. Par d�faut
  -------------   la gestion des images est d�sactiv�e. Ce param�tres n'a aucun effet si les
                  �moticons sont d�sactiv�s.

    Exemple :
      <param name="bitmapsmileys" value="true">

  smileyX : modifie l'�moticon num�ro X. Un �moticon est une paire de texte -> image. Chaque fois
            que "texte" est trouv� dans une phrase, il est remplac� par l'image correspondante.
            De la m�me mani�re que le param�tre command, il ne peut pas y avoir de "trou" dans
            la num�rotation. Le format du param�tres est "texte image", o� image est une URL
            d'o� l'applet peut charger l'image.

    Exemple :
      <param name="smiley1" value=":) img/smile.gif">
      <param name="smiley2" value=":( img/sad.gif">

  nicklistwidth : Modifie la valeur, en pixel, de la largeur de la liste des nicks. La valeur par
  -------------   d�faut vaut 130.

    Exemple :
      <param name="nicklistwidth" value="130">


  channelfont : Modifie la police de caract�re utilis�e pour l'affichage des messages des canaux.
  -----------   Le format du param�tres est "taille nom". Par d�faut, la police est configur�e sur
                "12 Monospaced".

    Exemple :
      <param name="channelfont" value="12 Monospaced">

  chanlistfont : Modifie la police de caract�re utilis�e pour l'affichage de la liste des canaux.
  ------------   Le format du param�tres est "taille nom". Par d�faut, la police est configur�e sur
                 "12 Monospaced".
    Exemple :
      <param name="chanlistfont" value="12 Monospaced">

  useinfo : remplace la fen�tre de statut par une fen�tre d'information. La fen�tre d'information
            fonctionne exactement de la m�me fa�on que la fen�tre de statut, � part le fait qu'elle
            n'affiche que le message du jour et le message de bienvenue. Vu que les r�ponses
            aux commandes whois etc... ne sont plus affich�es, les commandes correspondantes
            sur la liste des nicks sont coup�es. Par d�faut, la fen�tre d'information est coup�e.

    Exemple :
      <param name="useinfo" value="false">

  nickfield : Affiche un champ de modification du nickname en bas � droite de l'applet. Par d�faut,
  ---------   cette option est coup�e.

    Exemple :
      <param name="nickfield" value="false">

  chanlisttextcolorX : modifie la x-i�me (allant de 0 � 15) couleur du texte de la liste des canaux.
  ------------------

    Exemple :
      <param name="chanlisttextcolor4" value="FF00FF">

  defaultsourcetextcolorX : modifie la x-i�me (allant de 0 � 15) couleur par d�faut du texte des
  -----------------------   sources (canaux, priv�s, statut, ...)

    Exemple :
      <param name="defaultsourcetextcolor0" value="00FF00">

  sourcecolorconfigN : Commande de configuration avanc�e de couleurs num�ro N. Comme pour les
  ------------------   param�tres command, la num�rotation de N doit �tre continue et sans
                       trous. La syntaxe de ce param�tre est la suivante :
                       "SourceName ColorIndex ColorValue". SourceName est tout nom de source,
                       comme #moncanal ou nick. Statut est un nom de source valide.

    EXemple :
      <param name="sourcecolorconfig1" value="status 0 000000">
      <param name="sourcecolorconfig2" value="#channel 4 FFFF00">


Certification
-------------

  Le fichier securedirc.cab est un fichier cabinet sign� avec permissions totales. Il
  est n�cessaire si l'on d�sire utiliser certaines fonctionalit�s particuli�res telles
  le transfert DCC ou le serveur ident. Si ce fichier est manquant ou non d�clar�,
  l'utilisateur n'aura pas de message lui demandant d'accepter le certificat, mais ces
  options seront non disponibles.

  La certification ne fonctionne que sous Internet Explorer

  Note : Si la certification est coup�e, l'applet ne sera pas capable de contacter le
         serveur irc, sauf si ce dernier se trouve sur le m�me nom de domaine que le
         serveur http d'o� elle a �t� charg�e.

  Il est FORTEMENT CONSEILLE de remplacer le fichier securedirc.cab par un fichier
  qui utilise votre propre certificat. A cette fin, un fichier securedirc-unsigned.cab
  est fourni dans le package.


Fonctionalit�s
--------------

  Codes CTCP
    ACTION VERSION PING TIME FINGER USERINFO CLIENTINFO DCC

  Support DCC
    DCC file transfert (certification only)
    DCC chat (certification only)

  Auto-compl�tion du nick

  Serveur Ident

  D�tecteur d'URL

  Illumination de mots-cl�s

  Gestion de l'ASV

  ... et bien plus encore :)


Fragment HTML minimal
---------------------

<applet code=IRCApplet.class archive="irc.jar" width=640 height=400>
<param name="CABINETS" value="irc.cab,securedirc.cab">

<param name="nick" value="Anonymous???">
<param name="name" value="Java User">
<param name="host" value="irc.dal.net">
<param name="port" value="6667">

</applet>

Contacts
--------

PJIRC est d�velopp� par Plouf - theplouf@yahoo.com
Pour les derni�res nouvelles : http://groups.yahoo.com/group/pjirc/

Historique des versions
-----------------------

  1.4b : 31/05/2002
  ----
    Version initiale

  1.41b : 14/06/2002
  -----
    Compteur d'utilisateurs sur le canal
    Illumination
    Message de quit

  1.411b : 05/08/2002
  ------
    Le bug sur la commande /notice est r�gl�

  1.42b : 01/10/2002
  -----
    Gestion du mode semi-op (%)

  1.5b : 13/10/2002
  ----
    Gestion ASV
    Commandes ShowXXX
    Le probl�me avec le join automatique et les autres commandes ex�cut�es
    � la connexion est r�gl�.

  1.6b : 17/10/2002
  ----
    Gestion ASV compl�t�e
    Emoticons avec bitmaps
    Des nouvelles options du genre nicklistwidth, channelfont, chanlistfont, useinfo
    Le bug avec le scrolling horizontal du la liste des channels est r�gl�
    Les caract�res sp�ciaux sont d�plac�es sur la zone "user-defined" 0xE000 de
    l'UNICODE, afin d'�viter les probl�mes avec les utilisateurs de mac (normalement
    vous n'avez strictement rien compris � ce qui pr�c�de)
    Champ de changement de nick
    D'autres changements mineurs, des updates, des bugs corrig�s....
    Et d�j� plus de 12000 lignes de code de java...

  1.61 : 23/10/2002
  ----
    Les barres de d�filement se d�placent quand on maintient le bouton de la souris
    enfonc�
    La fen�tre de la liste des channel s'affiche � pr�sent � une vitesse civilis�e
    Le th�me des canaux peut � pr�sent �tre d�plac� horizontalement avec la souris
    Les th�mes contiennent les smileys
    La liste de canaux affiche � pr�sent les premiers canaux quand elle est ouverte
    Le d�tecteur d'url et de mot est � pr�sent un peu plus malin, il comprend que
    <nick> correspond � nick, etc...
    Des bugs corrig�s...

  1.611 : 24/10/2002
  -----
    Le deadlock qui pouvait survenir avec les barres de d�filement a �t� corrig�.

  1.62 : 28/10/2002
  ----
    Les caract�res [ et ] ne sont plus ignor�s dans le reconnaisseur de mots.
    Les couleurs du texte des sources (canaux, statut, priv�s...) et de la liste
    des canaux sont � pr�sent configurables.
