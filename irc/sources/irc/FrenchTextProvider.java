/***************************************************/
/*         This java file is a part of the         */
/*                                                 */
/*          -  Plouf's Java IRC Client  -          */
/*                                                 */
/*      Copyright (C) 2002 Philippe Detournay      */
/*                                                 */
/*   This file is licensed under the GPL license   */
/*                                                 */
/*        All contacts : theplouf@yahoo.com        */
/***************************************************/

package irc;

public class FrenchTextProvider extends BasicTextProvider
{
  protected String getStringP(int code)
  {
    switch(code)
    {
    case INTERPRETOR_NOT_ON_CHANNEL:return "Pas sur un canal";
    case INTERPRETOR_UNKNOWN_DCC:return "sous-commande DCC inconnue";
    case INTERPRETOR_INSUFFICIENT_PARAMETERS:return "paramètres insuffisants";
    case INTERPRETOR_BAD_CONTEXT:return "impossible à faire dans le contexte actuel";
    case INTERPRETOR_CANNOT_CTCP_IN_DCCCHAT:return "Impossible d'envoyer des codes CTCP dans un DCC Chat";
    case INTERPRETOR_UNKNOWN_CONFIG:return "sous-commande config inconnue";
    case INTERPRETOR_TIMESTAMP_ON:return "Horodateur activé";
    case INTERPRETOR_TIMESTAMP_OFF:return "Horodateur désactivé";
    case INTERPRETOR_SMILEYS_ON:return "Emoticons activés";
    case INTERPRETOR_SMILEYS_OFF:return "Emoticons désactivés";
		case INTERPRETOR_IGNORE_ON:return "Ignore à présent";
		case INTERPRETOR_IGNORE_OFF:return "N'ignore plus";
      
    case DCC_WAITING_INCOMING:return "Attente de connexion...";
    case DCC_UNABLE_TO_OPEN_CONNECTION:return "Impossible d'ouvrir la connexion";
    case DCC_CONNECTION_ESTABLISHED:return "Connexion DCC établie";
    case DCC_CONNECTION_CLOSED:return "Connexion fermée";
    case DCC_STREAM_CLOSED:return "Connexion fermée";
    case DCC_ERROR:return "Erreur";
    case DCC_UNABLE_TO_SEND_TO:return "impossible d'envoyer vers ";
    case DCC_BAD_CONTEXT:return "Impossible d'exécuter la commande dans le contexte actuel";
    case DCC_NOT_CONNECTED:return "Non connecté";
    case DCC_UNABLE_PASSIVE_MODE:return "Impossible d'initialiser le mode passif";
    case CTCP_SECONDS:return "secondes";
      
    case IDENT_FAILED_LAUNCH:return "Echec du lancement du serveur IDENT";
    case IDENT_REQUEST:return "Requête IDENT en provenance de";
    case IDENT_ERROR:return "Une erreur est survenue";
    case IDENT_REPLIED:return "Répondu";
    case IDENT_DEFAULT_USER:return "utilisateur par défaut";
    case IDENT_NO_USER:return "Pas d'utilisateur pour cette requête";
    case IDENT_RUNNING_ON_PORT:return "Le serveur IDENT s'exécute sur le port";
    case IDENT_LEAVING:return "Le serveur IDENT se termine";
    case IDENT_NONE:return "aucun";
    case IDENT_UNKNOWN:return "inconnu";
    case IDENT_UNDEFINED:return "Résultat indéterminé";
      
    case FILE_SAVEAS:return "Sauver le fichier vers";
      
    case ABOUT_ABOUT:return "A propos";
    case ABOUT_PROGRAMMING:return "Programmation";
    case ABOUT_DESIGN:return "Design";
    case ABOUT_THANKS:return "Remerciements";
    case ABOUT_SUPPORT:return "pour leur support, leurs idées et les tests";
    case ABOUT_HELP:return "Aidez-moi!";
    case ABOUT_GPL:return "Ce programme est sous license GPL";
    
    case SERVER_UNABLE_TO_CONNECT_TO:return "Impossible de se connecter à";
    case SERVER_TRYING_TO_CONNECT:return "en train d'essayer de se connecter à";
    case SERVER_DISCONNECTING:return "Déconnexion de";
    case SERVER_CONNECTING:return "Connexion...";
    case SERVER_NOT_CONNECTED:return "Non connecté";
    case SERVER_UNABLE_TO_CONNECT:return "Impossible de se connecter";
    case SERVER_LOGIN:return "Enregistrement...";
    case SERVER_DISCONNECTED:return "Déconnecté";
    case SERVER_ERROR:return "Erreur";
      
    case SOURCE_YOU_KICKED:return "Vous avez été mis à la porte de";
    case SOURCE_BY:return "par";
    case SOURCE_STATUS:return "Statut";
    case SOURCE_CHANLIST:return "Canaux sur le serveur";
    case SOURCE_CHANLIST_RETREIVING:return "Recherche des canaux...";
    case SOURCE_HAS_JOINED:return "est entré sur";
    case SOURCE_HAS_LEFT:return "est sorti de";
    case SOURCE_HAS_BEEN_KICKED_BY:return "a été mis à la porte par";
    case SOURCE_HAS_QUIT:return "a quitté";
    case SOURCE_TOPIC_IS:return "Le sujet est";
    case SOURCE_CHANGED_TOPIC:return "a changé le sujet vers";
    case SOURCE_CHANNEL_MODE:return "met les options de canal";
    case SOURCE_CHANNEL_MODE_IS:return "Les options du canal sont";
    case SOURCE_USER_MODE:return "met l'option";
    case SOURCE_ON:return "sur";
    case SOURCE_KNOWN_AS:return "s'appelle dorénavant";
    case SOURCE_YOUR_MODE:return "Vos options sont mises à";
    case SOURCE_YOUR_NICK:return "Vous vous appelez à présent";
    case SOURCE_INFO:return "Infos";
      
    case GUI_WHOIS:return "Qui est-ce";
    case GUI_QUERY:return "Aller en privé";
    case GUI_KICK:return "Mettre à la porte";
    case GUI_BAN:return "Bannir";
    case GUI_KICKBAN:return "Ejecter et bannir";
    case GUI_OP:return "Mettre opérateur";
    case GUI_DEOP:return "Retirer l'opérateur";
    case GUI_VOICE:return "Donner la parole";
    case GUI_DEVOICE:return "Retirer la parole";
    case GUI_PING:return "Ping";
    case GUI_VERSION:return "Version";
    case GUI_TIME:return "Heure";
    case GUI_FINGER:return "Infos";
    case GUI_RETREIVING_FILE:return "Réception du fichier";
    case GUI_SENDING_FILE:return "Envoi du fichier";
    case GUI_BYTES:return "octets";
    case GUI_TERMINATED:return "terminé";
    case GUI_FAILED:return "échec";
    case GUI_CLOSE:return "Fermer";
    case GUI_CONNECT:return "Connecter";
    case GUI_DISCONNECT:return "Déconnecter";
    case GUI_CHANNELS:return "Canaux";
    case GUI_HELP:return "Aide";
    case GUI_PRIVATE:return "privé";
    case GUI_PUBLIC:return "public ";
    case GUI_ABOUT:return "A propos";
    case GUI_CHANGE_NICK:return "Modifier le nick";

    case ASL_YEARS:return "ans";
		case ASL_MALE:return "Garçon";
		case ASL_FEMALE:return "Fille";

    case ERROR_NOT_DEFINED:return "Texte non défini";
    default:return null;

    }	
  }

}

