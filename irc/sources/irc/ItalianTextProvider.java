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
/*                                                 */
/*      This translation file was provided by      */
/*                  Devis  Lucato                  */
/***************************************************/

package irc;

public class ItalianTextProvider extends BasicTextProvider
{
  protected String getStringP(int code)
  {
    switch(code)
    {
    case INTERPRETOR_NOT_ON_CHANNEL:return "Nessun canale attivo";
    case INTERPRETOR_UNKNOWN_DCC:return "comando DCC sconosciuto";
    case INTERPRETOR_INSUFFICIENT_PARAMETERS:return "parametri insufficienti";
    case INTERPRETOR_BAD_CONTEXT:return "impossibile eseguire nel contesto attuale";
    case INTERPRETOR_CANNOT_CTCP_IN_DCCCHAT:return "Impossibile inviare CTCP dentro una DCC chat";
    case INTERPRETOR_UNKNOWN_CONFIG:return "comando di configurazione sconosciuto";
    case INTERPRETOR_TIMESTAMP_ON:return "Data e Ora attivate";
    case INTERPRETOR_TIMESTAMP_OFF:return "Data e Ora disattivate";
    case INTERPRETOR_SMILEYS_ON:return "Emoticons attivati";
    case INTERPRETOR_SMILEYS_OFF:return "Emoticons disattivati";
		case INTERPRETOR_IGNORE_ON:return "Stai ignorando";
		case INTERPRETOR_IGNORE_OFF:return "Non verrà più ignorato";


    case DCC_WAITING_INCOMING:return "Tentativo di connessione...";
    case DCC_UNABLE_TO_OPEN_CONNECTION:return "Impossibile aprire la connessione";
    case DCC_CONNECTION_ESTABLISHED:return "Connessione DCC stabilita";
    case DCC_CONNECTION_CLOSED:return "Connessione chiusa";
    case DCC_STREAM_CLOSED:return "Connessione chiusa";
    case DCC_ERROR:return "Errore";
    case DCC_UNABLE_TO_SEND_TO:return "impossibile inviare a ";
    case DCC_BAD_CONTEXT:return "Impossibile eseguire il comando nel contesto attuale";
    case DCC_NOT_CONNECTED:return "Non connesso";
    case DCC_UNABLE_PASSIVE_MODE:return "Impossibile inizializzare il modo passivo";
    case CTCP_SECONDS:return "secondi";
      
    case IDENT_FAILED_LAUNCH:return "Avvio del server IDENT non riuscito";
    case IDENT_REQUEST:return "Richiesta IDENT proveniente da";
    case IDENT_ERROR:return "Si è verificato un errore";
    case IDENT_REPLIED:return "Risposto";
    case IDENT_DEFAULT_USER:return "Utente di default";
    case IDENT_NO_USER:return "Nessun utente per questa richiesta";
    case IDENT_RUNNING_ON_PORT:return "Il server IDENT è in esecuzione sulla porta";
    case IDENT_LEAVING:return "Chiusura del server IDENT";
    case IDENT_NONE:return "nessun";
    case IDENT_UNKNOWN:return "sconosciuto";
    case IDENT_UNDEFINED:return "Risultato indeterminato";
      
    case FILE_SAVEAS:return "Salva il file su";
      
    case ABOUT_ABOUT:return "Informazioni";
    case ABOUT_PROGRAMMING:return "Programmazione";
    case ABOUT_DESIGN:return "Design";
    case ABOUT_THANKS:return "Ringraziamenti";
    case ABOUT_SUPPORT:return "per supporto, idee e prove";
    case ABOUT_HELP:return "Aiutatemi!";
    case ABOUT_GPL:return "Questo programma è sotto licenza GPL";
    
    case SERVER_UNABLE_TO_CONNECT_TO:return "Impossibile connettersi a";
    case SERVER_TRYING_TO_CONNECT:return "connessione in corso a ";
    case SERVER_DISCONNECTING:return "Disconnessione da";
    case SERVER_CONNECTING:return "Connessione...";
    case SERVER_NOT_CONNECTED:return "Non connesso";
    case SERVER_UNABLE_TO_CONNECT:return "Impossibile connettersi";
    case SERVER_LOGIN:return "Registrazione...";
    case SERVER_DISCONNECTED:return "Disconnesso";
    case SERVER_ERROR:return "Errore";
      
    case SOURCE_YOU_KICKED:return "Sei stato cacciato da";
    case SOURCE_BY:return "da";
    case SOURCE_STATUS:return "Stato";
    case SOURCE_CHANLIST:return "Canali del server";
    case SOURCE_CHANLIST_RETREIVING:return "Ricerca dei canali...";
    case SOURCE_HAS_JOINED:return "è entrato in";
    case SOURCE_HAS_LEFT:return "è uscito da";
    case SOURCE_HAS_BEEN_KICKED_BY:return "è stato cacciato da";
    case SOURCE_HAS_QUIT:return "è uscito";
    case SOURCE_TOPIC_IS:return "L'argomento è";
    case SOURCE_CHANGED_TOPIC:return "ha cambiato argomento in";
    case SOURCE_CHANNEL_MODE:return "cambia le impostazioni del canale";
    case SOURCE_CHANNEL_MODE_IS:return "Le impostazioni del canale sono";
    case SOURCE_USER_MODE:return "imposta l'opzione";
    case SOURCE_ON:return "su";
    case SOURCE_KNOWN_AS:return "ha cambiato nick in";
    case SOURCE_YOUR_MODE:return "Le tue impostazioni irc:";
    case SOURCE_YOUR_NICK:return "Il tuo nick è";
    case SOURCE_INFO:return "Infos";
      
    case GUI_WHOIS:return "Informazioni Whois";
    case GUI_QUERY:return "Chiama in privato";
    case GUI_KICK:return "Kick";
    case GUI_BAN:return "Banna";
    case GUI_KICKBAN:return "Kickban";
    case GUI_OP:return "Dai OP";
    case GUI_DEOP:return "Togli OP";
    case GUI_VOICE:return "Dai Voice";
    case GUI_DEVOICE:return "Togli Voice";
    case GUI_PING:return "Ping";
    case GUI_VERSION:return "Versione";
    case GUI_TIME:return "Ora";
    case GUI_FINGER:return "Info";
    case GUI_RETREIVING_FILE:return "Ricezione del file";
    case GUI_SENDING_FILE:return "Invio del file";
    case GUI_BYTES:return "bytes";
    case GUI_TERMINATED:return "terminato";
    case GUI_FAILED:return "fallito";
    case GUI_CLOSE:return "Chiudi";
    case GUI_CONNECT:return "Connetti";
    case GUI_DISCONNECT:return "Disconnetti";
    case GUI_CHANNELS:return "Canali";
    case GUI_HELP:return "Aiuto";
    case GUI_PRIVATE:return "privato";
    case GUI_PUBLIC:return "pubblico ";
    case GUI_ABOUT:return "Informazioni";
    case GUI_CHANGE_NICK:return "Cambia il nick in";
		
    case ASL_YEARS:return "anni";
    case ASL_MALE:return "Ragazzo";
    case ASL_FEMALE:return "Ragazza";

    case ERROR_NOT_DEFINED:return "Testo non definito";
    default:return null;

    }	
  }

}

