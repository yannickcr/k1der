----------------------------------------
            K1der Shoutbox 1.7 Beta7
              Par Country
             www.k1der.net
----------------------------------------

Voici la nouvelle version du K1der Shoutbox. Au menu : pas mal de nouveautés et surtout une installation facilitée.

Index
-----
	
	1. Présentation
	2. Logiciels requis
	3. Contenu du fichier zip
	4. Installation
	5. Administration
	6. FAQ
	7. Change log
	8. Remerciements
	
	Vous avez préparé la bière et les chips ? Oui ? Alors on y va.
	
1. Présentation
---------------
	
	Alors ce que vous avez téléchargé est une Shoutbox, c'est à dire un petit endroit où les visiteurs de votre site pourront laisser un court message (réactions à un article, avis sur le site, insultes, menaces de mort, etc.).
	
	Le succès de ce type de script est sans aucun doute la facilité/rapidité d'utilisation pour le visiteur.
	Il tape son pseudo, son message, il valide et c'est fini.
	
	Maintenant que vous savez ce que vous avez téléchargé et si vous êtes toujours intéressé alors on peut passer à la suite.
	
2. Logiciels requis
-------------------
	
	Sur la machine qui hébergera le script vous devrez avoir:
	- Php 4.x ou supérieur
	- MySql 3.x ou supérieur
	
	La majorité des hébergeurs possèdent ces services (dont les hébergeurs gratuits comme Free ou Multimania).
	Un minimum de connaissances en HTML et Php/MySql est conseillé.
	
	Et si vous avez beaucoup de connaissances en HTML et Php/MySql je pense que c'est pas la peine que vous vous fassiez chier a lire ce qui suit (ou alors pour le fun :p )
	
	Les autres vous suivez ?
	
3. Contenu du fichier zip
-------------------------
	
	k1dershoutbox17.zip
	 |
	 |-include              // les fichiers utilisés dans la shoutbox
	 |    |-admin.js		// Scripts javascript pour l'administration
	 |    |-fonctions.php	// Principales fonctions du shoutbox
	 |    |-scripts.js		// Scripts javascript du shoutbox
	 |    |-styles.css		// Styles CSS du shoutbox (couleur des liens, du texte, etc.)
	 |
	 |-smileys              // Les images des smileys par défauts
	 |    |-biggrin.gif
	 |    |-bigrazz.gif
	 |    |-confused.gif
	 |    |-cool.gif
	 |    |-mad.gif
	 |    |-sad.gif
	 |    |-smile.gif
	 |    |-wink.gif
	 |
	 |-simleys_w      		// Les mêmes smileys mais en blanc (pour mettre sur un fond foncé)
	 |    |-biggrin.gif
	 |    |-bigrazz.gif
	 |    |-confused.gif
	 |    |-cool.gif
	 |    |-mad.gif
	 |    |-sad.gif
	 |    |-smile.gif
	 |    |-wink.gif
	 |
	 |-admin.php            // Fichier d'administration
	 |-board.php            // Shoutbox
	 |-exemple.php          // Exemple d'utilisation
	 |-index.php          	// Index du shoutbox
	 |-install.php          // Fichier d'installation
	 |-Readme.txt           // Vous êtes ici ;)
	 
	Vous avez tout ça ?
	Alors on continue...
	
4. Installation
---------------
	
	Décompressez tous les fichiers quelque part sur votre disque dur (en gardant l'arborescence) puis uploadez les fichiers
	sur votre serveur web dans le dossier désiré (par exemple dans un dossier nommé shoutbox) avec un chmod de 0777.
	
	Tapez ensuite l'adresse de la page d'installation dans votre navigateur, exemple : http://www.votresite/shoutbox/install.php
	
	Suivez ensuite les étapes indiquées à l'écran.
	
	NOTE : Si vous effectuez une mise à jour consultez la FAQ.
	
5. Administration
-----------------
	
	Pour administrer la Shoutbox allez sur la page admin.php : http://www.votresite.com/shoutbox/admin.php
	(Vous pouvez aussi faire apparaître le lien sur la page de la shoutbox)
	Identifiez vous avec le login et mot de passe rentré lors de l'installation
	
	Vous pouvez alors le configurer, modifier son apparence, ajouter des modérateurs, etc...
	
	Pour vous déconnecter cliquez sur le bouton Déconnecter (oui je sais j'ai beaucoup d'imagination) en haut à gauche de la fenêtre.
	
6. FAQ
------------

	1. Je possède une ancienne version du K1der Shoutbox, puis-je la mettre à jour sans perdre ma configuration et mes messages ?
	2. Je possède une ancienne version du K1der Shoutbox que j'ai modifiée, vais-je perdre mes modifications ?
	3. Puis-je installer 2 versions du K1der Shoutbox sur le même serveur ?
	4. Comment ajouter/modifier les smileys ?
	5. J'ai une/des/plein d'idées pour le shoutbox, je fais quoi ?
	6. J'ai trouvé un bug, je fais quoi ?
	
	1. Je possède une ancienne version du K1der Shoutbox, puis-je la mettre à jour sans perdre ma configuration et mes messages ?
	-----------------------------------------------------------------------------------------------------------------------------
		Oui vous pouvez la mettre facilement à jour sans perdre votre configuration ou vos messages.
		
		Si vous avez la version 1.2 : Uploadez la shoutbox dans le même dossier que votre ancienne version et au début de l'installation
		sélectionnez "Mise à jour à partir de la version 1.2", tous vos messages seront convertit pour la nouvelle version et vos paramètres 
		seront récupérés. Seuls vos styles CSS seront perdus, mais vous pourrez facilement et rapidement les refaire dans le menu "Apparence" 
		de l'administration.
		
		Si vous avez une version suppérieure à la 1.2 : Uploadez la shoutbox dans le même dossier que votre ancienne version (hormis le fichier 
		styles.css si vous voulez garder vos styles personnalisés).

	2. Je possède une ancienne version du K1der Shoutbox que j'ai modifiée, vais-je perdre mes modifications ?
	---------------------------------------------------------------------------------------------------------
		Oui, mais de nombreuses fonctionnalités ont étés ajoutés dans cette version, peut-être les vôtres en font parties. Si ce n'est
		pas le cas soumettez les moi et je pourrai les inclure dans une prochaine version.

	3. Puis-je installer 2 versions du K1der Shoutbox sur le même serveur ?
	---------------------------------------------------------------------------------------------------------
		Oui, installez les dans des dossiers différents et utilisez des base SQL différentes pour les 2 shoutbox
		(Par ex: k1der_shoutbox2 et k1der_shoutbox_config2).

	4. Comment ajouter/modifier les smileys ?
	---------------------------------------------------------------------------------------------------------
		Uploadez tout d’abord les images des smileys dans le dossier approprié. Accédez ensuite à l'administration et rendez-vous sur la page 
		"Configuration", dans la liste des smileys associés l'image voulue au smiley à remplacer (si vos images n'apparaissent pas dans la liste 
		modifier le dossier des smileys, validez puis retournez sur la page de configuration). Si vous voulez en ajouter un remplissez les cases 
		en face de "Nouveau". Pour en supprimer un sélectionnez "Supprimer" à la place de l'image.

	5. J'ai une/des/plein d'idées pour le shoutbox, je fais quoi ?
	---------------------------------------------------------------------------------------------------------
		Vous pouvez me les soumettre en laissant un petit message sur le forum de k1der.net, en passant sur le Channel IRC #k1der sur 
		Quakenet, en me contactant par e-mail (country@k1der.net) ou par MSN (même adresse).

	6. J'ai trouvé un bug, je fais quoi ?
	---------------------------------------------------------------------------------------------------------
		Laissez-moi un message sur le forum de k1der.net (décrivez bien le bug et la façon de le déclencher), en passant sur le Channel 
		IRC #k1der sur Quakenet, en me contactant par e-mail (country@k1der.net) ou par MSN (même adresse).

7. Change log
------------
	
	1.7 Beta7
	---
	Fix: 	Bug si le fichier d'où est appellé le shoutbox ne s'appel pas index.php 
	
	1.7 Beta6
	---
	New: 	Possibilité d'afficher un ascenceur dans la shoutbox (désactivé par défaut)
	Fix: 	Possibilité pour un admin/modo d'utiliser le pseudo d'un autre admin/modo
	
	1.7 Beta5
	---
	New: 	Possibilité de limiter la taille des pseudos (par défaut 20 caractères)
	Fix: 	Filtrage du pseudo
	
	1.7 Beta4
	---
	Fix: 	Protection des pseudos
	Fix: 	Ajout/Suppression de modérateurs
	Fix: 	Bug de couleurs dans la page apparence
	
	1.7 Beta3
	---
	Fix: 	Erreurs sur Free
	Fix: 	Bugs divers
	
	1.7 Beta2
	---
	Fix: 	Erreur de récupération des paramètres lors de la mise à jour
	Fix: 	Erreur de conversion des anciens messages
	
	1.7 Beta1
	---
	New:	Fichier d'installation
	New:	Possibilité d'ajouter des modérateurs
	New:	Protection des pseudos des administrateurs et des modérateurs
	New:	Edition simplifiée de l'apparence de la shoutbox avec prévisualisation
	New: 	Configuration simplifiée
	New: 	Ajouts de quelques options
	New: 	Possibilité pour les administrateurs et modérateurs d'éditer les messages
	Fix: 	Bug avec la bulle sous Mozilla/Firefox/Opéra
	Change: Affichage de l'historique par page
	
	1.2
	---
	New:    Sand-alone
	New:    Interface d'administration
	New:    Options de configuration
	Fix:    Bug quand une adresse mail et un lien se trouvait dans 1 seul message
	Change: Nettoyage du code
	
	1.0
	---
	Première version
	
8. Remerciement
---------------

Merci a Surprise pour ces idées à la con :)
Merci à vous d'avoir téléchargé ce script
Et merci aux gens qui l'utilisent ;)

7. Contact et Copyright
----------------------- 
Visitez k1der.net pour des informations et des mises à jours. 

Si vous avez besoin d'aide pour l'installation ou l'utilisation du script vous pouvez me contacter à country@k1der.net (e-mail et MSN)

Copyright:

Ne modifiez et ne redistribuez pas ce script.
Ne supprimez pas le copyright des pages.
L'auteur ne sera tenu responsable de la mauvaise utilisation de ce script. Utilisez-le à vos propres risques.