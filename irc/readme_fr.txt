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

  Détails :
    irc.cab : Fichier d'archive pour Internet Explorer - obligatoire
    securedirc.cab : Fichier d'archive signé pour Internet Explorer - facultatif
    irc.jar : Fichier d'archive pour Netscape ou autre navigateur - obligatoire
    IRCApplet.class : Lanceur, programme principal - obligatoire

Fragment HTML de l'applet
-------------------------

  <applet code=IRCApplet.class archive="irc.jar" width=640 height=400>
  <param name="CABINETS" value="irc.cab,securedirc.cab">

  ...
  paramètres facultatifs
  ...

  </applet>

  width et height peut être adaptés au besoin
  securedirc.cab peut être enlevé

Paramètres
----------
  Les paramètres sont envoyés vers l'applet en suivant la syntaxe suivante :
    <param name="name" value="value">

Paramètres obligatoires
-----------------------

  nick : Nick à utiliser par défaut. Les '?' seront remplacés par des chiffres aléatoires.
    Exemple :
      <param name="nick" value="Guest??">   dira à l'applet d'utiliser des nicks du genre
                                            Guest47

  name : "Vrai" nom de l'utilisateur, envoyé au serveur irc.
    Exemple :
      <param name="name" value="UserName">


  host : Nom de domaine du serveur irc.
    Exemple :
      <param name="host" value="irc.server.net">

  port : Port du serveur irc.
    Exemple :
      <param name="port" value="6667">

Paramètres facultatifs
----------------------

  commandX, avec x un nombre : ordonne à l'applet d'exécuter cette commande une fois qu'elle est
  --------                     connectée au serveur.

                               La première commande DOIT être command1, et il ne peut pas y
                               avoir de "trous" dans la numérotation. La command14 DOIT se
                               trouver après la command13, et PAS apres command12.

                               Ces commandes ne sont pas envoyées via l'interpreteur, ce qui
                               entraine comme conséquence que seules les commandes directement
                               comprises par le serveur peuvent être utilisées. Ces commandes
                               ne doivent PAS commencer par '/'.

    Exemple : 
      <param name="command1" value="nickserv identify password">
      <param name="command2" value="join #channel">

  basecolor : Ordonne à l'applet de calculer automatiquement toutes les couleurs, en se basant sur
  ---------   les valeurs de rouge, vert et bleu données. Ces valeurs vont de 0 à 1000.

    Exemple :
      <param name="basecolor" value="384,256,128">

  colorI, avec I un nombre : Ordonne à l'applet de modifier la couleur dont on donne l'indice. Ces
  ------                     commandes sont prises en compte APRES la commande basecolor.

          Les indices suivants sont définis :
             0 : Couleur noire
             1 : Couleur blanche
             2 : Couleur gris foncée
             3 : Couleur grise
             4 : Couleur gris claire
             5 : Couleur d'avant-plan
             6 : Couleur d'arrière-plan
             7 : Couleur de sélection
             8 : Couleur d'évènement
             9 : Couleur de la fermeture
            10 : Couleur du voice
            11 : Couleur de l'op
            12 : Couleur du semi-op
            13 : Couleur ASV homme
            14 : Couleur ASV femme
            15 : Couleur ASV inconnu

    Exemple :
      <param name="color1" value="C0C000">

  helppage : configure la page d'aide à ouvrir quand l'utilisateur clique sur le bouton d'aide
  --------   sur l'interface graphique.

    Exemple :
      <param name="helppage" value="http://www.yahoo.com">

  timestamp : branche ou coupe l'horodateur. Par défaut, l'orodateur est coupé.
  ---------   

    Exemple :
      <param name="timestamp" value="true">

  language : Modifie la langue utilisée dans PJIRC. Les langues english, french et italian sont
  --------   supportées. Par défaut, english est utilisé.

    Exemple :
      <param name="language" value="french">

  smileys : Branche ou coupe la gestion graphique des émoticons. Par défaut, les émoticons graphiques
  -------   sont désactivés.

    Exemple :
      <param name="smileys" value="true">

  highlight : Branche ou coupe l'illumination de mots. Si cette option n'est pas branchée, aucune
  ---------   illumination ne sera effectuée, quelle que soit la valeur des autre options. Par défaut,
              l'illumination est coupée.

    Exemple :
      <param name="highlight" value="true">

  highlightnick : si l'illumination est branchée, cette option ordonne à l'applet d'illuminer
  -------------   toute phrase contenant le nick de l'utilisateur. Par défaut, cette option
                  est coupée.

    Exemple :
      <param name="highlightnick" value="true">

  highlightcolor : si l'illumination est branchée, spécifie quelle couleur doit être utilisée
  --------------   pour l'illumination. Par défaut, cette couleur est la couleur 5.

    Exemple :
      <param name="highlightcolor" value="9">

  highlightwords : si l'illumination est branchée, donne une liste de mots qui entrainent
  --------------   l'illumination de la phrase dans laquelle ils sont présents. Les mots
                   sont séparés par des espaces.

    Exemple :
      <param name="highlightwords" value="word1 word2 word3">

  quitmessage : règle le message de quit par défaut. Par défaut, ce message est vide.
  -----------

    Exemple :
      <param name="quitmessage" value="PJIRC forever!">

  asl : branche ou coupe la gestion asv (age, sexe, ville). L'asv est récupéré à partir du nom
  ---   complet de l'utilisateur. En fonction de ces informations, un affichage spécifique sera
        activé pour le nick. Le format du nom complet doit être "age sexe ville", par exemple
        "22 h Namur". Par défaut, la gestion asv est coupée.

    Exemple :
      <param name="asv" value="true">

  aslmale : Modifie la chaîne de caractères correspondant au genre masculin dans l'asv. Par défaut,
  -------   cette chaîne vaut "m".

    Exemple :
      <param name="aslmale" value="h">

  aslfemale : Modifie la chaîne de caractères correspondant au genre féminin dans l'asv. Par défaut,
  ---------   cette chaîne vaut "f".

    Exemple :
      <param name="aslfemale" value="f">

  showconnect : Branche ou coupe la visibilité du bouton de connexion dans l'interface. Par défaut,
  -----------   ce bouton est visible.

    Exemple :
      <param name="showconnect" value="true">

  showchanlist : Branche ou coupe la visibilité du bouton de liste des canaux dans l'interface. Par
  ------------   défaut, ce bouton est visible.

    Exemple :
      <param name="showchanlist" value="true">

  showabout : Branche ou coupe la visibilité du bouton à propos dans l'interface. Par défaut, ce
  ---------   bouton est visible.

    Exemple :
      <param name="showabout" value="true">

  showhelp : Branche ou coupe la visibilité du bouton d'aide dans l'interface. Par défaut, ce bouton
  --------   est visible.

    Exemple :
      <param name="showhelp" value="true">


  bitmapsmileys : Branche ou coupe la gestion des émoticons graphiques depuis image. Par défaut
  -------------   la gestion des images est désactivée. Ce paramètres n'a aucun effet si les
                  émoticons sont désactivés.

    Exemple :
      <param name="bitmapsmileys" value="true">

  smileyX : modifie l'émoticon numéro X. Un émoticon est une paire de texte -> image. Chaque fois
            que "texte" est trouvé dans une phrase, il est remplacé par l'image correspondante.
            De la même manière que le paramètre command, il ne peut pas y avoir de "trou" dans
            la numérotation. Le format du paramètres est "texte image", où image est une URL
            d'où l'applet peut charger l'image.

    Exemple :
      <param name="smiley1" value=":) img/smile.gif">
      <param name="smiley2" value=":( img/sad.gif">

  nicklistwidth : Modifie la valeur, en pixel, de la largeur de la liste des nicks. La valeur par
  -------------   défaut vaut 130.

    Exemple :
      <param name="nicklistwidth" value="130">


  channelfont : Modifie la police de caractère utilisée pour l'affichage des messages des canaux.
  -----------   Le format du paramètres est "taille nom". Par défaut, la police est configurée sur
                "12 Monospaced".

    Exemple :
      <param name="channelfont" value="12 Monospaced">

  chanlistfont : Modifie la police de caractère utilisée pour l'affichage de la liste des canaux.
  ------------   Le format du paramètres est "taille nom". Par défaut, la police est configurée sur
                 "12 Monospaced".
    Exemple :
      <param name="chanlistfont" value="12 Monospaced">

  useinfo : remplace la fenêtre de statut par une fenêtre d'information. La fenêtre d'information
            fonctionne exactement de la même façon que la fenêtre de statut, à part le fait qu'elle
            n'affiche que le message du jour et le message de bienvenue. Vu que les réponses
            aux commandes whois etc... ne sont plus affichées, les commandes correspondantes
            sur la liste des nicks sont coupées. Par défaut, la fenêtre d'information est coupée.

    Exemple :
      <param name="useinfo" value="false">

  nickfield : Affiche un champ de modification du nickname en bas à droite de l'applet. Par défaut,
  ---------   cette option est coupée.

    Exemple :
      <param name="nickfield" value="false">

  chanlisttextcolorX : modifie la x-ième (allant de 0 à 15) couleur du texte de la liste des canaux.
  ------------------

    Exemple :
      <param name="chanlisttextcolor4" value="FF00FF">

  defaultsourcetextcolorX : modifie la x-ième (allant de 0 à 15) couleur par défaut du texte des
  -----------------------   sources (canaux, privés, statut, ...)

    Exemple :
      <param name="defaultsourcetextcolor0" value="00FF00">

  sourcecolorconfigN : Commande de configuration avancée de couleurs numéro N. Comme pour les
  ------------------   paramètres command, la numérotation de N doit être continue et sans
                       trous. La syntaxe de ce paramètre est la suivante :
                       "SourceName ColorIndex ColorValue". SourceName est tout nom de source,
                       comme #moncanal ou nick. Statut est un nom de source valide.

    EXemple :
      <param name="sourcecolorconfig1" value="status 0 000000">
      <param name="sourcecolorconfig2" value="#channel 4 FFFF00">


Certification
-------------

  Le fichier securedirc.cab est un fichier cabinet signé avec permissions totales. Il
  est nécessaire si l'on désire utiliser certaines fonctionalités particulières telles
  le transfert DCC ou le serveur ident. Si ce fichier est manquant ou non déclaré,
  l'utilisateur n'aura pas de message lui demandant d'accepter le certificat, mais ces
  options seront non disponibles.

  La certification ne fonctionne que sous Internet Explorer

  Note : Si la certification est coupée, l'applet ne sera pas capable de contacter le
         serveur irc, sauf si ce dernier se trouve sur le même nom de domaine que le
         serveur http d'où elle a été chargée.

  Il est FORTEMENT CONSEILLE de remplacer le fichier securedirc.cab par un fichier
  qui utilise votre propre certificat. A cette fin, un fichier securedirc-unsigned.cab
  est fourni dans le package.


Fonctionalités
--------------

  Codes CTCP
    ACTION VERSION PING TIME FINGER USERINFO CLIENTINFO DCC

  Support DCC
    DCC file transfert (certification only)
    DCC chat (certification only)

  Auto-complétion du nick

  Serveur Ident

  Détecteur d'URL

  Illumination de mots-clés

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

PJIRC est développé par Plouf - theplouf@yahoo.com
Pour les dernières nouvelles : http://groups.yahoo.com/group/pjirc/

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
    Le bug sur la commande /notice est réglé

  1.42b : 01/10/2002
  -----
    Gestion du mode semi-op (%)

  1.5b : 13/10/2002
  ----
    Gestion ASV
    Commandes ShowXXX
    Le problème avec le join automatique et les autres commandes exécutées
    à la connexion est réglé.

  1.6b : 17/10/2002
  ----
    Gestion ASV complétée
    Emoticons avec bitmaps
    Des nouvelles options du genre nicklistwidth, channelfont, chanlistfont, useinfo
    Le bug avec le scrolling horizontal du la liste des channels est réglé
    Les caractères spéciaux sont déplacées sur la zone "user-defined" 0xE000 de
    l'UNICODE, afin d'éviter les problèmes avec les utilisateurs de mac (normalement
    vous n'avez strictement rien compris à ce qui précède)
    Champ de changement de nick
    D'autres changements mineurs, des updates, des bugs corrigés....
    Et déjà plus de 12000 lignes de code de java...

  1.61 : 23/10/2002
  ----
    Les barres de défilement se déplacent quand on maintient le bouton de la souris
    enfoncé
    La fenêtre de la liste des channel s'affiche à présent à une vitesse civilisée
    Le thème des canaux peut à présent être déplacé horizontalement avec la souris
    Les thèmes contiennent les smileys
    La liste de canaux affiche à présent les premiers canaux quand elle est ouverte
    Le détecteur d'url et de mot est à présent un peu plus malin, il comprend que
    <nick> correspond à nick, etc...
    Des bugs corrigés...

  1.611 : 24/10/2002
  -----
    Le deadlock qui pouvait survenir avec les barres de défilement a été corrigé.

  1.62 : 28/10/2002
  ----
    Les caractères [ et ] ne sont plus ignorés dans le reconnaisseur de mots.
    Les couleurs du texte des sources (canaux, statut, privés...) et de la liste
    des canaux sont à présent configurables.
