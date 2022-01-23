<?php

//----- SET UP CUSTOM HEADERS AND FOOTERS HERE --//

$lang['header'] = "";

$lang['footer'] = <<<EOF

Cordialement,

L'équipe <#BOARD_NAME#>.
<#BOARD_ADDRESS#>

EOF;



//-------------------------------
// NEW MOD __TOPIC__
//-------------------------------


$lang['subject__new_topic_queue_notify'] = 'Nouveau sujet attendant validation';
$lang['new_topic_queue_notify'] = <<<EOF
Bonjour !

Cet email a été envoyé à partir de : <#BOARD_NAME#>.

Un nouveau sujet est entré dans la file d'attente de modération et attend votre
validation.

----------------------------------
Sujet : <#TOPIC#>
Forum : <#FORUM#>
Auteur : <#POSTER#>
Date : <#DATE#>
Gérer la file d'attente : <#LINK#>
----------------------------------

Si vous ne souhaitez plus recevoir les notifications, il suffit simplement de supprimer votre adresse
email dans les options de paramétrage du forum.

<#BOARD_ADDRESS#>


EOF;

//-------------------------------
// NEW MOD __POST__
//-------------------------------


$lang['subject__new_post_queue_notify'] = 'New Post Awaiting Approval';
$lang['new_post_queue_notify'] = <<<EOF
Bonjour !

Cet email a été envoyé à partir de : <#BOARD_NAME#>.

Un nouveau message est entré dans la file d'attente de modération et attend votre
validation.

----------------------------------
Sujet : <#TOPIC#>
Forum : <#FORUM#>
Auteur : <#POSTER#>
Date : <#DATE#>
Gérer la file d'attente : <#LINK#>
----------------------------------

Si vous ne souhaitez plus recevoir les notifications, il suffit simplement de supprimer votre adresse
email dans les options de paramétrage du forum.

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

Se désabonner :
---------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir abonnements".

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

Se désabonner :
---------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir abonnements".

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

Se désabonner :
---------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir abonnements".

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

Se désabonner :
---------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir abonnements".

EOF;



//----------


$lang['subject__pm_notify'] = 'Vous avez un nouveau message personnel';
$lang['pm_notify'] = <<<EOF
<#NAME#>,

<#POSTER#> vous a envoyé un nouveau message personnel titré "<#TITLE#>".

Vous pouvez lire ce message personnel en suivant le lien ci-dessous :

<#BOARD_ADDRESS#><#LINK#>


EOF;



$lang['send_text']	= <<<EOF
J'ai pensé que tu pourrais être intéressé par ce site web : <#THE LINK#>

De,

<#USER NAME#>

EOF;


$lang['report_post'] = <<<EOF

<#MOD_NAME#>,

Vous avez reçu cette alerte de <#USERNAME#> via le lien "Alerter un modérateur à propos de ce message".

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
Cet email a été envoyé à partir de <#BOARD_ADDRESS#>.

Vos messages archivés ont été compilés en un fichier unique
et ont été attachés à ce message.

EOF;

$lang['reg_validate'] = <<<EOF

<#NAME#>,
Cet email a été envoyé à partir de <#BOARD_ADDRESS#>.

Vous avez reçu cet email car cette adresse email a été
utilisée lors de l'inscription à nos forums.
Si vous ne vous êtes pas inscrit à nos forums, merci de ne pas tenir compte de
cet email. Vous n'avez pas besoin de vous désinscrire ou d'effectuer d'action supplémentaire.

------------------------------------------------
Instructions d'activation
------------------------------------------------

Merci de votre inscription.
Nous demandons que vous "validiez" votre inscription pour nous assurer que
l'adresse email que vous avez entrée est correcte. Ceci afin de prévenir
et éviter les spams et les abus.

Pour activer votre compte, cliquez sur le lien suivant :

<#THE_LINK#>

(Les utilisateurs email d'AOL devront peut être copier et coller ce lien dans leur
navigateur).

------------------------------------------------
Cela ne fonctionne pas ?
------------------------------------------------

Si vous n'avez pas pu valider votre inscription en cliquant sur le lien, merci
de vous rendre sur cette page :

<#MAN_LINK#>

Il vous sera demandé un identifiant et votre clé de validation. Ces éléments sont
présents ci-dessous :

Identifiant : <#ID#>

Clé de validation : <#CODE#>

Merci de copier coller, ou taper ces nombres dans les champs correspondants du formulaire.

Si vous ne parvenez toujours à valider votre compte, il se peut que le compte ait été supprimé.
Dans ce cas, veuillez contacter un administrateur pour résoudre le problème.

Merci de votre inscription, passez un bon moment sur nos forums !

EOF;

$lang['admin_newuser'] = <<<EOF

Bonjour Monsieur l'Administrateur !

Vous recevez cet email car un nouvel utilisateur s'est inscrit !

<#MEMBER_NAME#> a validé son inscription le <#DATE#>

Vous pouvez annuler cet avertissement via le panneau de contrôle d'administration ACP

Passez une bonne journée !

EOF;

$lang['lost_pass'] = <<<EOF

<#NAME#>,
Cet email a été envoyé à partir de <#BOARD_ADDRESS#>.

Vous avez reçu cet email parce qu'une réinitialisation du mot de passe du compte utilisateur
a été demandée par vous sur <#BOARD_NAME#>.

------------------------------------------------
IMPORTANT !
------------------------------------------------

Si vous n'avez pas demande une reinitialisation du mot de passe IGNOREZ et EFFACEZ cet
email immédiatement ! Continuez uniquement si vous souhaitez que votre mot de passe soit réinitialisé !

------------------------------------------------
Instructions d'activation ci-dessous
------------------------------------------------

Nous demandons que vous "validiez" la réinitialisation de votre mot de passe
pour nous assurer que vous avez demandé cette action. Ceci afin de prévenir et éviter les spams et les abus.


Cliquez simplement sur le lien ci-dessous et complétez le reste du formulaire.

<#THE_LINK#>

(Les utilisateurs email d'AOL devront peut être copier et coller ce lien dans leur
navigateur).

------------------------------------------------
Cela ne fonctionne pas ?
------------------------------------------------

Si vous n'avez pu valider votre inscription en cliquant sur le lien, veuillez
vous rendre sur cette page :

<#MAN_LINK#>

Il vous sera demandé un identifiant et votre clé de validation. Ces éléments sont
présents ci-dessous :

Identifiant : <#ID#>

Clé de validation : <#CODE#>

Merci de copier coller, ou taper ces nombres dans les champs correspondants du formulaire.

------------------------------------------------
Est-ce que cela ne fonctionne pas ?
------------------------------------------------

Si vous ne pouvez pas re-activer votre compte, il est possible que le compte ait été supprimé ou que vous
soyez en cours d'une autre activation, telle que l'inscription ou le changement de votre adresse email enregistrée.
Dans ce cas, merci de terminer l'activation précédente.
Si l'erreur persiste, merci de contacter un administrateur pour rectifier le problème.

Adresse IP de l'expéditeur : <#IP_ADDRESS#>


EOF;

$lang['newemail'] = <<<EOF

<#NAME#>,
Cet email a été envoyé à partir de <#BOARD_ADDRESS#>.

Vous recevez cet email parce que vous avez demandé un
changement d'adresse email.

------------------------------------------------
Instructions d'activation ci-dessous
------------------------------------------------

Nous demandons que vous "validiez" la réinitialisation de votre mot de passe
pour nous assurer que vous avez demandé cette action. Ceci afin de prévenir et éviter les spams et les abus.


Pour activer votre compte, cliquez sur le lien suivant :

<#THE_LINK#>

(Les utilisateurs email d'AOL devront peut être copier et coller ce lien dans leur
navigateur).

------------------------------------------------
Cela ne fonctionne pas ?
------------------------------------------------

Si vous n'avez pu valider votre inscription en cliquant sur le lien, veuillez
vous rendre sur cette page :

<#MAN_LINK#>

Il vous sera demandé un identifiant et votre clé de validation. Ces éléments sont
présents ci-dessous :

Identifiant : <#ID#>

Clé de validation : <#CODE#>

Merci de copier coller, ou taper ces nombres dans les champs correspondants du formulaire.

Une fois l'activation terminée, vous devrez peut être vous reconnecter pour mettre à jour vos
permissions.

------------------------------------------------
Aidez-moi, j'ai eu une erreur !
------------------------------------------------

Si vous ne pouvez pas re-activer votre compte, il est possible que le compte ait été supprimé ou que vous
soyez en cours d'une autre activation, telle que l'inscription ou le changement de votre adresse email enregistrée.
Dans ce cas, merci de terminer l'activation précédente.
Si l'erreur persiste, merci de contacter un administrateur pour rectifier le problème.


EOF;

$lang['forward_page'] = <<<EOF

<#TO_NAME#>


<#THE_MESSAGE#>

---------------------------------------------------
Merci de noter que <#BOARD_NAME#> n'a aucun contrôle sur
le contenu de ce message.
---------------------------------------------------

EOF;

$lang['subject__subs_with_post'] = 'Notification de réponse dans un sujet';

$lang['subs_with_post'] = <<<EOF
<#NAME#>,

<#POSTER#> vient d'écrire une réponse dans le sujet auquel vous êtes inscrit. Titre du sujet : "<#TITLE#>".

-----------------------------------------------------------------------------
<#POST#>
-----------------------------------------------------------------------------

Le sujet se trouve ici :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>&view=getnewpost



Il se peut qu'il y ait plus d'une réponse à ce sujet, mais seulement un email est envoyé par visite sur les forums pour chaque sujet auquel vous êtes abonné. C'est
pour limiter le nombre d'email qui est envoyé vers votre boite de réception.

Se Désabonner :
---------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir Abonnements".

EOF;

$lang['subject__subs_new_topic'] = 'Notification de nouveau sujet dans un forum';
$lang['subs_new_topic'] = <<<EOF
<#NAME#>,

<#POSTER#> vient d'écrire un nouveau sujet titré "<#TITLE#>" dans le forum "<#FORUM#>".


----------------------------------------------------------------------
<#POST#>
----------------------------------------------------------------------

Le sujet se trouve ici :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>

Veuillez noter que si vous souhaitez obtenir des notifications par email lors de réponses à ce sujet, vous devrez cliquer sur le lien "Suivre ce Sujet" affiché sur la page des sujets, ou en cliquant sur le lien ci-dessous :
<#BOARD_ADDRESS#>?act=Track&f=<#FORUM_ID#>&t=<#TOPIC_ID#>


Se Désabonner :
---------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir Abonnements".
Si vous n'êtes pas souscrits à aucun forum et souhaitez cesser de recevoir cette notification, décochez ce paramètre
"Envoyez-moi toutes les annonces emails effectuées par l'administrateur des forums" situé dans 'Mes contrôles' sous 'Paramètres email'.

EOF;

$lang['subject__subs_no_post'] = 'Notification de réponse dans un sujet';
$lang['subs_no_post'] = <<<EOF
<#NAME#>,

<#POSTER#> vient d'écrire une réponse dans le sujet auquel vous êtes inscrit. Titre du sujet : "<#TITLE#>".

Le sujet se trouve ici :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>&view=getnewpost

Il se peut qu'il y ait plus d'une réponse à ce sujet, mais seulement un email est envoyé par visite sur les forums pour chaque sujet auquel vous êtes abonné. C'est
pour limiter le nombre d'email qui est envoyé vers votre boite de réception.

Se Désabonner :
---------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir Abonnements".

EOF;



$lang['email_member'] = <<<EOF
<#MEMBER_NAME#>,

<#FROM_NAME#> vous a envoyé cet email de <#BOARD_ADDRESS#>.


<#MESSAGE#>

---------------------------------------------------
Merci de noter que <#BOARD_NAME#> n'a aucun contrôle sur
le contenu de ce message.
---------------------------------------------------


EOF;

$lang['complete_reg'] = <<<EOF

Bravo !

Un administrateur a accepté votre demande d'inscription ou changement d'adresse email à <#BOARD_NAME#>. Vous pouvez maintenant vous connecter avec vos informations et accéder à votre compte utilisateur dans son intégralité à <#BOARD_ADDRESS#>

EOF;


?>