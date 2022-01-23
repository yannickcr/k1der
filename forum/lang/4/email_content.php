<?php

//----- SET UP CUSTOM HEADERS AND FOOTERS HERE --//

$EMAIL['header'] = "";

$EMAIL['footer'] = <<<EOF

Cordialement,

L'equipe <#BOARD_NAME#>.
<#BOARD_ADDRESS#>

EOF;

$SUBJECT['new_topic_queue_notify'] = 'Nouveau sujet attendant validation';
$EMAIL['new_topic_queue_notify'] = <<<EOF
Hello !

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



$SUBJECT['pm_notify'] = 'Vous avez un nouveau message personnel';
$EMAIL['pm_notify'] = <<<EOF
<#NAME#>,

<#POSTER#> vous a envoyé un nouveau message personnel titré "<#TITLE#>".

Vous pouvez lire ce message personnel en suivant le lien ci-dessous :

<#BOARD_ADDRESS#><#LINK#>


EOF;



$EMAIL['send_text']	= <<<EOF
J'ai pensé que tu pourrais être intéressé par ce site web : <#THE LINK#>

De,

<#USER NAME#>

EOF;


$EMAIL['report_post'] = <<<EOF

<#MOD_NAME#>,

Vous avez reçu cet email de <#USERNAME#> via le lien "Alerter un modérateur à propos de ce message".

------------------------------------------------
Sujet : <#TOPIC#>
------------------------------------------------
Lien vers le message : <#LINK_TO_POST#>
------------------------------------------------
Alerte :

<#REPORT#>

------------------------------------------------

EOF;



$EMAIL['pm_archive'] = <<<EOF

<#NAME#>,
Cet email a été envoyé à partir de <#BOARD_ADDRESS#>.

Vos messages archivés ont été compilés en un fichier unique
et ont été attachés à ce message.

EOF;

$EMAIL['reg_validate'] = <<<EOF

<#NAME#>,
Cet email a été envoyé à partir de <#BOARD_ADDRESS#>.

Vous avez reçu cet email car cette adresse email a été
utilisée lors de l'inscription à nos forums.
Si vous ne vous êtes pas inscrit à nos forums, merci de ne pas tenir compte de
cet email. Vous n'avez pas besoin de vous désinscrire ou d'effectuer d'action supplémentaire.

------------------------------------------------
        Instructions d'Activation
------------------------------------------------

Merci de votre inscritption.
Nous demandons que vous "validiez" votre inscription pour s'assurer que
l'adresse email que vous avez entrée était correcte. Ceci pour prévenir
du spam et des abus.

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

Il vous sera demandé un identifiant, et votre clé de validation. Ces éléments sont
présents ci-dessous :

Identifiant : <#ID#>

Clé de validation : <#CODE#>

Merci de copier coller, ou taper ces nombres dans les champs correspondants du formulaire.

Si vous ne parvenez toujours à valider votre compte, il se peut que le compte ait été supprimé.
Dans ce cas, veuillez contacter un administrateur pour rectifier le problème.

Merci de votre inscription, passez un bon moment sur nos forums !

EOF;

$EMAIL['admin_newuser'] = <<<EOF

Bonjour Monsieur L'Administrateur !

Vous recevez cet email car un nouvel utilisateur s'est inscrit !

<#MEMBER_NAME#> a validé son inscription le <#DATE#>

Vous pouvez annuler cet avertissement via le Panneau de Contrôle d'Administration ACP

Passez une bonne journée !

EOF;

$EMAIL['lost_pass'] = <<<EOF

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
     Instructions d'Activation Ci-Dessous
------------------------------------------------

Nous demandons que vous "validiez" la réinitialisation de votre mot de passe
pour s'assurer que vous avez demandé cette action. Cela protège contre les
abus et le spam.

Cliquez simplement sur le lien ci-dessous et complétez le reste du formulaire.

<#THE_LINK#>

(Utilisateurs AOL Email devront peut être copier et coller ce lien dans leur
navigateur).

------------------------------------------------
Cela ne fonctionne pas ?
------------------------------------------------

Si vous n'avez pu valider votre inscription en cliquant sur le lien, veuillez
vous rendre sur cette page :

<#MAN_LINK#>

Il vous sera demandé un id utilisateur, et votre clé de validation. Ils vous sont donnés
ci-dessous :

ID Utilisateur : <#ID#>

Clé de Validation : <#CODE#>

Merci de copier et coller, ou taper ces nombres dans les champs correspondants du formulaire.

------------------------------------------------
      Est ce que cela ne fonctionne pas ?
------------------------------------------------

Si vous ne pouvez pas re-activer votre compte, il est possible que le compte ait été supprimé ou que vous
soyez en cours d'une autre activation, telle que l'inscription ou le changement de votre adresse email enregistrée.
Dans ce cas, merci de terminer l'activation précédente.
Si l'erreur persiste, merci de contacter un administrateur pour rectifier le problème.

Adresse ip de l'expéditeur : <#IP_ADDRESS#>


EOF;

$EMAIL['newemail'] = <<<EOF

<#NAME#>,
Cet email a été envoyé de <#BOARD_ADDRESS#>.

Vous recevez cet email parce que vous avez demandé un
changement d'adresse email.

------------------------------------------------
        Instructions d'activation ci-dessous
------------------------------------------------

Nous demandons que vous "validiez" votre changement d'adresse email pour s'assurer que vous avez
demandé cette action. Cela protège des abus et du spam.


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

Il vous sera demandé un id utilisateur, et votre clé de validation. Ils vous sont donnés
ci-dessous :

ID Utilisateur : <#ID#>

Clé de Validation : <#CODE#>

Merci de copier et coller, ou taper ces nombres dans les champs correspondants du formulaire.

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

$EMAIL['forward_page'] = <<<EOF

<#TO_NAME#>


<#THE_MESSAGE#>

---------------------------------------------------
Merci de noter que <#BOARD_NAME#> n'a aucun contrôle sur
le contenu de ce message.
---------------------------------------------------

EOF;

$SUBJECT['subs_with_post'] = 'Notification de réponse dans un sujet';

$EMAIL['subs_with_post'] = <<<EOF
<#NAME#>,

<#POSTER#> vient d'écrire une réponse dans le sujet auquel vous êtes inscrit. Titre du sujet : "<#TITLE#>".

----------------------------------------------------------------------
<#POST#>
----------------------------------------------------------------------

Le sujet se trouve à l'adresse suivante :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>&view=getnewpost



Il se peut qu'il y ait plus d'une réponse à ce sujet, mais seulement 1 email est envoyé par visite sur les forums pour chaque sujet auquel vous êtes abonné. C'est
pour limiter le nombre d'email qui est envoyé vers votre boite de réception.

Se Désabonner :
--------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir Abonnements".

EOF;

$SUBJECT['subs_new_topic'] = 'Notification de nouveau sujet dans un forum';
$EMAIL['subs_new_topic'] = <<<EOF
<#NAME#>,

<#POSTER#> vient d'écrire un nouveau sujet titré "<#TITLE#>" dans le forum "<#FORUM#>".

Le sujet se trouve ici :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>

Veuillez noter que si vous souhaitez obtenir des notifications par email lors de réponses à ce sujet, vous devrez cliquer sur le
lien "Suivre ce Sujet" affiché sur la page des sujets, ou en cliquant sur le lien ci-dessous :
<#BOARD_ADDRESS#>?act=Track&f=<#FORUM_ID#>&t=<#TOPIC_ID#>


Désabonnement :
--------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir Abonnements".

EOF;

$SUBJECT['subs_no_post'] = 'Notification de réponse dans un sujet';
$EMAIL['subs_no_post'] = <<<EOF
<#NAME#>,

<#POSTER#> vient d'écrire une réponse dans le sujet auquel vous êtes inscrit. Titre du sujet : "<#TITLE#>".

Le sujet se trouve à l'adresse suivante :
<#BOARD_ADDRESS#>?showtopic=<#TOPIC_ID#>&view=getnewpost

Il se peut qu'il y ait plus d'une réponse à ce sujet, mais seulement 1 email est envoyé par visite sur les forums pour chaque sujet auquel vous êtes abonné. C'est
pour limiter le nombre d'email qui est envoyé vers votre boite de réception.

Se Désabonner :
--------------

Vous pouvez vous désabonner à tout moment en vous connectant sur votre panneau de contrôle et en cliquant sur le lien "Voir Abonnements".

EOF;



$EMAIL['email_member'] = <<<EOF
<#MEMBER_NAME#>,

<#FROM_NAME#> vous a envoyé cet email de <#BOARD_ADDRESS#>.


<#MESSAGE#>

---------------------------------------------------
Merci de noter que <#BOARD_NAME#> n'a aucun contrôle sur
le contenu de ce message.
---------------------------------------------------


EOF;

$EMAIL['complete_reg'] = <<<EOF

Bravo !

Un administrateur a accepté votre demande d'inscription ou changement d'adresse email à <#BOARD_NAME#>. Vous pouvez maintenant vous connecter avec
vos informations et accéder à votre compte utilisateur dans son intégralité à <#BOARD_ADDRESS#>

EOF;


?>