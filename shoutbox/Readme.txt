----------------------------------------
            K1der Shoutbox 1.7 Beta7
              Par Country
             www.k1der.net
----------------------------------------

Voici la nouvelle version du K1der Shoutbox. Au menu : pas mal de nouveaut�s et surtout une installation facilit�e.

Index
-----
	
	1. Pr�sentation
	2. Logiciels requis
	3. Contenu du fichier zip
	4. Installation
	5. Administration
	6. FAQ
	7. Change log
	8. Remerciements
	
	Vous avez pr�par� la bi�re et les chips ? Oui ? Alors on y va.
	
1. Pr�sentation
---------------
	
	Alors ce que vous avez t�l�charg� est une Shoutbox, c'est � dire un petit endroit o� les visiteurs de votre site pourront laisser un court message (r�actions � un article, avis sur le site, insultes, menaces de mort, etc.).
	
	Le succ�s de ce type de script est sans aucun doute la facilit�/rapidit� d'utilisation pour le visiteur.
	Il tape son pseudo, son message, il valide et c'est fini.
	
	Maintenant que vous savez ce que vous avez t�l�charg� et si vous �tes toujours int�ress� alors on peut passer � la suite.
	
2. Logiciels requis
-------------------
	
	Sur la machine qui h�bergera le script vous devrez avoir:
	- Php 4.x ou sup�rieur
	- MySql 3.x ou sup�rieur
	
	La majorit� des h�bergeurs poss�dent ces services (dont les h�bergeurs gratuits comme Free ou Multimania).
	Un minimum de connaissances en HTML et Php/MySql est conseill�.
	
	Et si vous avez beaucoup de connaissances en HTML et Php/MySql je pense que c'est pas la peine que vous vous fassiez chier a lire ce qui suit (ou alors pour le fun :p )
	
	Les autres vous suivez ?
	
3. Contenu du fichier zip
-------------------------
	
	k1dershoutbox17.zip
	 |
	 |-include              // les fichiers utilis�s dans la shoutbox
	 |    |-admin.js		// Scripts javascript pour l'administration
	 |    |-fonctions.php	// Principales fonctions du shoutbox
	 |    |-scripts.js		// Scripts javascript du shoutbox
	 |    |-styles.css		// Styles CSS du shoutbox (couleur des liens, du texte, etc.)
	 |
	 |-smileys              // Les images des smileys par d�fauts
	 |    |-biggrin.gif
	 |    |-bigrazz.gif
	 |    |-confused.gif
	 |    |-cool.gif
	 |    |-mad.gif
	 |    |-sad.gif
	 |    |-smile.gif
	 |    |-wink.gif
	 |
	 |-simleys_w      		// Les m�mes smileys mais en blanc (pour mettre sur un fond fonc�)
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
	 |-Readme.txt           // Vous �tes ici ;)
	 
	Vous avez tout �a ?
	Alors on continue...
	
4. Installation
---------------
	
	D�compressez tous les fichiers quelque part sur votre disque dur (en gardant l'arborescence) puis uploadez les fichiers
	sur votre serveur web dans le dossier d�sir� (par exemple dans un dossier nomm� shoutbox) avec un chmod de 0777.
	
	Tapez ensuite l'adresse de la page d'installation dans votre navigateur, exemple : http://www.votresite/shoutbox/install.php
	
	Suivez ensuite les �tapes indiqu�es � l'�cran.
	
	NOTE : Si vous effectuez une mise � jour consultez la FAQ.
	
5. Administration
-----------------
	
	Pour administrer la Shoutbox allez sur la page admin.php : http://www.votresite.com/shoutbox/admin.php
	(Vous pouvez aussi faire appara�tre le lien sur la page de la shoutbox)
	Identifiez vous avec le login et mot de passe rentr� lors de l'installation
	
	Vous pouvez alors le configurer, modifier son apparence, ajouter des mod�rateurs, etc...
	
	Pour vous d�connecter cliquez sur le bouton D�connecter (oui je sais j'ai beaucoup d'imagination) en haut � gauche de la fen�tre.
	
6. FAQ
------------

	1. Je poss�de une ancienne version du K1der Shoutbox, puis-je la mettre � jour sans perdre ma configuration et mes messages ?
	2. Je poss�de une ancienne version du K1der Shoutbox que j'ai modifi�e, vais-je perdre mes modifications ?
	3. Puis-je installer 2 versions du K1der Shoutbox sur le m�me serveur ?
	4. Comment ajouter/modifier les smileys ?
	5. J'ai une/des/plein d'id�es pour le shoutbox, je fais quoi ?
	6. J'ai trouv� un bug, je fais quoi ?
	
	1. Je poss�de une ancienne version du K1der Shoutbox, puis-je la mettre � jour sans perdre ma configuration et mes messages ?
	-----------------------------------------------------------------------------------------------------------------------------
		Oui vous pouvez la mettre facilement � jour sans perdre votre configuration ou vos messages.
		
		Si vous avez la version 1.2 : Uploadez la shoutbox dans le m�me dossier que votre ancienne version et au d�but de l'installation
		s�lectionnez "Mise � jour � partir de la version 1.2", tous vos messages seront convertit pour la nouvelle version et vos param�tres 
		seront r�cup�r�s. Seuls vos styles CSS seront perdus, mais vous pourrez facilement et rapidement les refaire dans le menu "Apparence" 
		de l'administration.
		
		Si vous avez une version supp�rieure � la 1.2 : Uploadez la shoutbox dans le m�me dossier que votre ancienne version (hormis le fichier 
		styles.css si vous voulez garder vos styles personnalis�s).

	2. Je poss�de une ancienne version du K1der Shoutbox que j'ai modifi�e, vais-je perdre mes modifications ?
	---------------------------------------------------------------------------------------------------------
		Oui, mais de nombreuses fonctionnalit�s ont �t�s ajout�s dans cette version, peut-�tre les v�tres en font parties. Si ce n'est
		pas le cas soumettez les moi et je pourrai les inclure dans une prochaine version.

	3. Puis-je installer 2 versions du K1der Shoutbox sur le m�me serveur ?
	---------------------------------------------------------------------------------------------------------
		Oui, installez les dans des dossiers diff�rents et utilisez des base SQL diff�rentes pour les 2 shoutbox
		(Par ex: k1der_shoutbox2 et k1der_shoutbox_config2).

	4. Comment ajouter/modifier les smileys ?
	---------------------------------------------------------------------------------------------------------
		Uploadez tout d�abord les images des smileys dans le dossier appropri�. Acc�dez ensuite � l'administration et rendez-vous sur la page 
		"Configuration", dans la liste des smileys associ�s l'image voulue au smiley � remplacer (si vos images n'apparaissent pas dans la liste 
		modifier le dossier des smileys, validez puis retournez sur la page de configuration). Si vous voulez en ajouter un remplissez les cases 
		en face de "Nouveau". Pour en supprimer un s�lectionnez "Supprimer" � la place de l'image.

	5. J'ai une/des/plein d'id�es pour le shoutbox, je fais quoi ?
	---------------------------------------------------------------------------------------------------------
		Vous pouvez me les soumettre en laissant un petit message sur le forum de k1der.net, en passant sur le Channel IRC #k1der sur 
		Quakenet, en me contactant par e-mail (country@k1der.net) ou par MSN (m�me adresse).

	6. J'ai trouv� un bug, je fais quoi ?
	---------------------------------------------------------------------------------------------------------
		Laissez-moi un message sur le forum de k1der.net (d�crivez bien le bug et la fa�on de le d�clencher), en passant sur le Channel 
		IRC #k1der sur Quakenet, en me contactant par e-mail (country@k1der.net) ou par MSN (m�me adresse).

7. Change log
------------
	
	1.7 Beta7
	---
	Fix: 	Bug si le fichier d'o� est appell� le shoutbox ne s'appel pas index.php 
	
	1.7 Beta6
	---
	New: 	Possibilit� d'afficher un ascenceur dans la shoutbox (d�sactiv� par d�faut)
	Fix: 	Possibilit� pour un admin/modo d'utiliser le pseudo d'un autre admin/modo
	
	1.7 Beta5
	---
	New: 	Possibilit� de limiter la taille des pseudos (par d�faut 20 caract�res)
	Fix: 	Filtrage du pseudo
	
	1.7 Beta4
	---
	Fix: 	Protection des pseudos
	Fix: 	Ajout/Suppression de mod�rateurs
	Fix: 	Bug de couleurs dans la page apparence
	
	1.7 Beta3
	---
	Fix: 	Erreurs sur Free
	Fix: 	Bugs divers
	
	1.7 Beta2
	---
	Fix: 	Erreur de r�cup�ration des param�tres lors de la mise � jour
	Fix: 	Erreur de conversion des anciens messages
	
	1.7 Beta1
	---
	New:	Fichier d'installation
	New:	Possibilit� d'ajouter des mod�rateurs
	New:	Protection des pseudos des administrateurs et des mod�rateurs
	New:	Edition simplifi�e de l'apparence de la shoutbox avec pr�visualisation
	New: 	Configuration simplifi�e
	New: 	Ajouts de quelques options
	New: 	Possibilit� pour les administrateurs et mod�rateurs d'�diter les messages
	Fix: 	Bug avec la bulle sous Mozilla/Firefox/Op�ra
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
	Premi�re version
	
8. Remerciement
---------------

Merci a Surprise pour ces id�es � la con :)
Merci � vous d'avoir t�l�charg� ce script
Et merci aux gens qui l'utilisent ;)

7. Contact et Copyright
----------------------- 
Visitez k1der.net pour des informations et des mises � jours. 

Si vous avez besoin d'aide pour l'installation ou l'utilisation du script vous pouvez me contacter � country@k1der.net (e-mail et MSN)

Copyright:

Ne modifiez et ne redistribuez pas ce script.
Ne supprimez pas le copyright des pages.
L'auteur ne sera tenu responsable de la mauvaise utilisation de ce script. Utilisez-le � vos propres risques.