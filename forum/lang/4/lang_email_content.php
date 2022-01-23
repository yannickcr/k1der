<?php

//----- SET UP CUSTOM HEADERS AND FOOTERS HERE --//

$lang['header'] = "";

$lang['footer'] = <<<EOF

Cordialement,

L'�quipe <#BOARD_NAME#>.
<#BOARD_ADDRESS#>

EOF;



//-------------------------------
// NEW MOD __TOPIC__
//-------------------------------


$lang['subject__new_topic_queue_notify'] = 'Nouveau sujet attendant validation';
$lang['new_topic_queue_notify'] = <<<EOF
Bonjour !

Cet email a �t� envoy� � partir de : <#BOARD_NAME#>.

Un nouveau sujet est entr� dans la file d'attente de mod�ration et attend votre
validation.

----------------------------------
Sujet : <#TOPIC#>
Forum : <#FORUM#>
Auteur : <#POSTER#>
Date : <#DATE#>
G�rer la file d'attente : <#LINK#>
----------------------------------

Si vous ne souhaitez plus recevoir les notifications, il suffit simplement de supprimer votre adresse
email dans les options de param�trage du forum.

<#BOARD_ADDRESS#>


EOF;

//-------------------------------
// NEW MOD __POST__
//-------------------------------


$lang['subject__new_post_queue_notify'] = 'New Post Awaiting Approval';
$lang['new_post_queue_notify'] = <<<EOF
Bonjour !

Cet email a �t� envoy� � partir de : <#BOARD_NAME#>.

Un nouveau message est entr� dans la file d'attente de mod�ration et attend votre
validation.

----------------------------------
Sujet : <#TOPIC#>
Forum : <#FORUM#>
Auteur : <#POSTER#>
Date : <#DATE#>
G�rer la file d'attente : <#LINK#>
----------------------------------

Si vous ne souhaitez plus recevoir les notifications, il suffit simplement de supprimer votre adresse
email dans les options de param�trage du forum.

<#BOARD_ADDRESS#>


EOF;

//-------------------------------
// FORUM: WEEKLY
//-------------------------------


$lang['subject__digest_forum_weekly'] = 'Votre sommaire hebdomadaire des nouveaux sujets';
$lang['digest_forum_weekly'] = <<<EOF
<#NAME#>,

Voici le sommaire des messages de la semaine du forum <#NAME#>.

-----------------------------------------------------------------------------



<#CONTENT#>




-----------------------------------------------------------------------------

Le sujet se trouve ici :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>&view=getnewpost

Se d�sabonner :
---------------

Vous pouvez vous d�sabonner � tout moment en vous connectant sur votre panneau de contr�le et en cliquant sur le lien "Voir abonnements".

EOF;

//-------------------------------
// FORUM: DAILY
//-------------------------------


$lang['subject__digest_forum_daily'] = 'Votre sommaire quotidien des nouveaux sujets';
$lang['digest_forum_daily'] = <<<EOF
<#NAME#>,

Voici le sommaire quotidien des nouveaux sujets :

-----------------------------------------------------------------------------



<#CONTENT#>




-----------------------------------------------------------------------------

Le forum se trouve ici :
<#BOARD_ADDRESS#>?showforum=<#FORUM_ID#>

Se d�sabonner :
---------------

Vous pouvez vous d�sabonner � tout moment en vous connectant sur votre panneau de contr�le et en cliquant sur le lien "Voir abonnements".

EOF;


//-------------------------------
// TOPIC: WEEKLY
//-------------------------------


$lang['subject__digest_topic_weeky'] = 'Votre sommaire hebdomadaire des nouveaux sujets';
$lang['digest_topic_weeky'] = <<<EOF
<#NAME#>,

Voici le sommaire hebdomadaire des nouveaux sujets :

-----------------------------------------------------------------------------



<#CONTENT#>




-----------------------------------------------------------------------------

Le forum se trouve ici :
<#BOARD_ADDRESS#>?showforum=<#FORUM_ID#>

Se d�sabonner :
---------------

Vous pouvez vous d�sabonner � tout moment en vous connectant sur votre panneau de contr�le et en cliquant sur le lien "Voir abonnements".

EOF;

//-------------------------------
// TOPIC: DAILY
//-------------------------------


$lang['subject__digest_topic_daily'] = 'Votre sommaire quotidien des nouveaux messages';
$lang['digest_topic_daily'] = <<<EOF
<#NAME#>,

Voici le sommaire des messages dans le sujet "<#TITLE#>" pour aujourd'hui :

-----------------------------------------------------------------------------



<#CONTENT#>




-----------------------------------------------------------------------------

Le sujet se trouve ici :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>&view=getnewpost

Se d�sabonner :
---------------

Vous pouvez vous d�sabonner � tout moment en vous connectant sur votre panneau de contr�le et en cliquant sur le lien "Voir abonnements".

EOF;



//----------


$lang['subject__pm_notify'] = 'Vous avez un nouveau message personnel';
$lang['pm_notify'] = <<<EOF
<#NAME#>,

<#POSTER#> vous a envoy� un nouveau message personnel titr� "<#TITLE#>".

Vous pouvez lire ce message personnel en suivant le lien ci-dessous :

<#BOARD_ADDRESS#><#LINK#>


EOF;



$lang['send_text']	= <<<EOF
J'ai pens� que tu pourrais �tre int�ress� par ce site web : <#THE LINK#>

De,

<#USER NAME#>

EOF;


$lang['report_post'] = <<<EOF

<#MOD_NAME#>,

Vous avez re�u cette alerte de <#USERNAME#> via le lien "Alerter un mod�rateur � propos de ce message".

------------------------------------------------
Sujet : <#TOPIC#>
------------------------------------------------
Lien vers le message : <#LINK_TO_POST#>
------------------------------------------------
Alerte :

<#REPORT#>

------------------------------------------------

EOF;



$lang['pm_archive'] = <<<EOF

<#NAME#>,
Cet email a �t� envoy� � partir de <#BOARD_ADDRESS#>.

Vos messages archiv�s ont �t� compil�s en un fichier unique
et ont �t� attach�s � ce message.

EOF;

$lang['reg_validate'] = <<<EOF

<#NAME#>,
Cet email a �t� envoy� � partir de <#BOARD_ADDRESS#>.

Vous avez re�u cet email car cette adresse email a �t�
utilis�e lors de l'inscription � nos forums.
Si vous ne vous �tes pas inscrit � nos forums, merci de ne pas tenir compte de
cet email. Vous n'avez pas besoin de vous d�sinscrire ou d'effectuer d'action suppl�mentaire.

------------------------------------------------
Instructions d'activation
------------------------------------------------

Merci de votre inscription.
Nous demandons que vous "validiez" votre inscription pour nous assurer que
l'adresse email que vous avez entr�e est correcte. Ceci afin de pr�venir
et �viter les spams et les abus.

Pour activer votre compte, cliquez sur le lien suivant :

<#THE_LINK#>

(Les utilisateurs email d'AOL devront peut �tre copier et coller ce lien dans leur
navigateur).

------------------------------------------------
Cela ne fonctionne pas ?
------------------------------------------------

Si vous n'avez pas pu valider votre inscription en cliquant sur le lien, merci
de vous rendre sur cette page :

<#MAN_LINK#>

Il vous sera demand� un identifiant et votre cl� de validation. Ces �l�ments sont
pr�sents ci-dessous :

Identifiant : <#ID#>

Cl� de validation : <#CODE#>

Merci de copier coller, ou taper ces nombres dans les champs correspondants du formulaire.

Si vous ne parvenez toujours � valider votre compte, il se peut que le compte ait �t� supprim�.
Dans ce cas, veuillez contacter un administrateur pour r�soudre le probl�me.

Merci de votre inscription, passez un bon moment sur nos forums !

EOF;

$lang['admin_newuser'] = <<<EOF

Bonjour Monsieur l'Administrateur !

Vous recevez cet email car un nouvel utilisateur s'est inscrit !

<#MEMBER_NAME#> a valid� son inscription le <#DATE#>

Vous pouvez annuler cet avertissement via le panneau de contr�le d'administration ACP

Passez une bonne journ�e !

EOF;

$lang['lost_pass'] = <<<EOF

<#NAME#>,
Cet email a �t� envoy� � partir de <#BOARD_ADDRESS#>.

Vous avez re�u cet email parce qu'une r�initialisation du mot de passe du compte utilisateur
a �t� demand�e par vous sur <#BOARD_NAME#>.

------------------------------------------------
IMPORTANT !
------------------------------------------------

Si vous n'avez pas demande une reinitialisation du mot de passe IGNOREZ et EFFACEZ cet
email imm�diatement ! Continuez uniquement si vous souhaitez que votre mot de passe soit r�initialis� !

------------------------------------------------
Instructions d'activation ci-dessous
------------------------------------------------

Nous demandons que vous "validiez" la r�initialisation de votre mot de passe
pour nous assurer que vous avez demand� cette action. Ceci afin de pr�venir et �viter les spams et les abus.


Cliquez simplement sur le lien ci-dessous et compl�tez le reste du formulaire.

<#THE_LINK#>

(Les utilisateurs email d'AOL devront peut �tre copier et coller ce lien dans leur
navigateur).

------------------------------------------------
Cela ne fonctionne pas ?
------------------------------------------------

Si vous n'avez pu valider votre inscription en cliquant sur le lien, veuillez
vous rendre sur cette page :

<#MAN_LINK#>

Il vous sera demand� un identifiant et votre cl� de validation. Ces �l�ments sont
pr�sents ci-dessous :

Identifiant : <#ID#>

Cl� de validation : <#CODE#>

Merci de copier coller, ou taper ces nombres dans les champs correspondants du formulaire.

------------------------------------------------
Est-ce que cela ne fonctionne pas ?
------------------------------------------------

Si vous ne pouvez pas re-activer votre compte, il est possible que le compte ait �t� supprim� ou que vous
soyez en cours d'une autre activation, telle que l'inscription ou le changement de votre adresse email enregistr�e.
Dans ce cas, merci de terminer l'activation pr�c�dente.
Si l'erreur persiste, merci de contacter un administrateur pour rectifier le probl�me.

Adresse IP de l'exp�diteur : <#IP_ADDRESS#>


EOF;

$lang['newemail'] = <<<EOF

<#NAME#>,
Cet email a �t� envoy� � partir de <#BOARD_ADDRESS#>.

Vous recevez cet email parce que vous avez demand� un
changement d'adresse email.

------------------------------------------------
Instructions d'activation ci-dessous
------------------------------------------------

Nous demandons que vous "validiez" la r�initialisation de votre mot de passe
pour nous assurer que vous avez demand� cette action. Ceci afin de pr�venir et �viter les spams et les abus.


Pour activer votre compte, cliquez sur le lien suivant :

<#THE_LINK#>

(Les utilisateurs email d'AOL devront peut �tre copier et coller ce lien dans leur
navigateur).

------------------------------------------------
Cela ne fonctionne pas ?
------------------------------------------------

Si vous n'avez pu valider votre inscription en cliquant sur le lien, veuillez
vous rendre sur cette page :

<#MAN_LINK#>

Il vous sera demand� un identifiant et votre cl� de validation. Ces �l�ments sont
pr�sents ci-dessous :

Identifiant : <#ID#>

Cl� de validation : <#CODE#>

Merci de copier coller, ou taper ces nombres dans les champs correspondants du formulaire.

Une fois l'activation termin�e, vous devrez peut �tre vous reconnecter pour mettre � jour vos
permissions.

------------------------------------------------
Aidez-moi, j'ai eu une erreur !
------------------------------------------------

Si vous ne pouvez pas re-activer votre compte, il est possible que le compte ait �t� supprim� ou que vous
soyez en cours d'une autre activation, telle que l'inscription ou le changement de votre adresse email enregistr�e.
Dans ce cas, merci de terminer l'activation pr�c�dente.
Si l'erreur persiste, merci de contacter un administrateur pour rectifier le probl�me.


EOF;

$lang['forward_page'] = <<<EOF

<#TO_NAME#>


<#THE_MESSAGE#>

---------------------------------------------------
Merci de noter que <#BOARD_NAME#> n'a aucun contr�le sur
le contenu de ce message.
---------------------------------------------------

EOF;

$lang['subject__subs_with_post'] = 'Notification de r�ponse dans un sujet';

$lang['subs_with_post'] = <<<EOF
<#NAME#>,

<#POSTER#> vient d'�crire une r�ponse dans le sujet auquel vous �tes inscrit. Titre du sujet : "<#TITLE#>".

-----------------------------------------------------------------------------
<#POST#>
-----------------------------------------------------------------------------

Le sujet se trouve ici :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>&view=getnewpost



Il se peut qu'il y ait plus d'une r�ponse � ce sujet, mais seulement un email est envoy� par visite sur les forums pour chaque sujet auquel vous �tes abonn�. C'est
pour limiter le nombre d'email qui est envoy� vers votre boite de r�ception.

Se D�sabonner :
---------------

Vous pouvez vous d�sabonner � tout moment en vous connectant sur votre panneau de contr�le et en cliquant sur le lien "Voir Abonnements".

EOF;

$lang['subject__subs_new_topic'] = 'Notification de nouveau sujet dans un forum';
$lang['subs_new_topic'] = <<<EOF
<#NAME#>,

<#POSTER#> vient d'�crire un nouveau sujet titr� "<#TITLE#>" dans le forum "<#FORUM#>".


----------------------------------------------------------------------
<#POST#>
----------------------------------------------------------------------

Le sujet se trouve ici :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>

Veuillez noter que si vous souhaitez obtenir des notifications par email lors de r�ponses � ce sujet, vous devrez cliquer sur le lien "Suivre ce Sujet" affich� sur la page des sujets, ou en cliquant sur le lien ci-dessous :
<#BOARD_ADDRESS#>?act=Track&f=<#FORUM_ID#>&t=<#TOPIC_ID#>


Se D�sabonner :
---------------

Vous pouvez vous d�sabonner � tout moment en vous connectant sur votre panneau de contr�le et en cliquant sur le lien "Voir Abonnements".
Si vous n'�tes pas souscrits � aucun forum et souhaitez cesser de recevoir cette notification, d�cochez ce param�tre
"Envoyez-moi toutes les annonces emails effectu�es par l'administrateur des forums" situ� dans 'Mes contr�les' sous 'Param�tres email'.

EOF;

$lang['subject__subs_no_post'] = 'Notification de r�ponse dans un sujet';
$lang['subs_no_post'] = <<<EOF
<#NAME#>,

<#POSTER#> vient d'�crire une r�ponse dans le sujet auquel vous �tes inscrit. Titre du sujet : "<#TITLE#>".

Le sujet se trouve ici :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>&view=getnewpost

Il se peut qu'il y ait plus d'une r�ponse � ce sujet, mais seulement un email est envoy� par visite sur les forums pour chaque sujet auquel vous �tes abonn�. C'est
pour limiter le nombre d'email qui est envoy� vers votre boite de r�ception.

Se D�sabonner :
---------------

Vous pouvez vous d�sabonner � tout moment en vous connectant sur votre panneau de contr�le et en cliquant sur le lien "Voir Abonnements".

EOF;



$lang['email_member'] = <<<EOF
<#MEMBER_NAME#>,

<#FROM_NAME#> vous a envoy� cet email de <#BOARD_ADDRESS#>.


<#MESSAGE#>

---------------------------------------------------
Merci de noter que <#BOARD_NAME#> n'a aucun contr�le sur
le contenu de ce message.
---------------------------------------------------


EOF;

$lang['complete_reg'] = <<<EOF

Bravo !

Un administrateur a accept� votre demande d'inscription ou changement d'adresse email � <#BOARD_NAME#>. Vous pouvez maintenant vous connecter avec vos informations et acc�der � votre compte utilisateur dans son int�gralit� � <#BOARD_ADDRESS#>

EOF;


?>