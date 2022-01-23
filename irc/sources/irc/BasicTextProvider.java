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

import java.util.*;

public abstract class BasicTextProvider implements TextProvider
{
  private Hashtable _list;
	
	public BasicTextProvider()
	{
	  _list=new Hashtable();
		for(int i=0;i<0xFFFF;i++)
		{
		  String txt=getStringP(i);
			if(txt!=null) _list.put(new Integer(i),txt);
		}
	}
	
	public String getString(int code)
	{
	  String ans=(String)_list.get(new Integer(code));
		if(ans==null) return getStringP(ERROR_NOT_DEFINED);
		return ans;
	}
	
  protected String getStringP(int code)
  {
    switch(code)
    {
    case INTERPRETOR_NOT_ON_CHANNEL:return "Not on a channel";
    case INTERPRETOR_UNKNOWN_DCC:return "unknown dcc subcommand";
    case INTERPRETOR_INSUFFICIENT_PARAMETERS:return "insufficient parameters";
    case INTERPRETOR_BAD_CONTEXT:return "unable to perform in current context";
    case INTERPRETOR_CANNOT_CTCP_IN_DCCCHAT:return "Cannot send CTCP codes via DCC Chat";
    case INTERPRETOR_UNKNOWN_CONFIG:return "unknown config subcommand";
    case INTERPRETOR_TIMESTAMP_ON:return "Timestamp enabled";
    case INTERPRETOR_TIMESTAMP_OFF:return "Timestamp disabled";
    case INTERPRETOR_SMILEYS_ON:return "Graphical smileys enabled";
    case INTERPRETOR_SMILEYS_OFF:return "Graphical smileys disabled";
    
    case DCC_WAITING_INCOMING:return "Waiting for incoming connection...";
    case DCC_UNABLE_TO_OPEN_CONNECTION:return "Unable to open connection";
    case DCC_CONNECTION_ESTABLISHED:return "DCC Connection established";
    case DCC_CONNECTION_CLOSED:return "Connection closed";
    case DCC_STREAM_CLOSED:return "Stream closed";
    case DCC_ERROR:return "Error";
    case DCC_UNABLE_TO_SEND_TO:return "unable to send to ";
    case DCC_BAD_CONTEXT:return "Unable to execute command from current context";
    case DCC_NOT_CONNECTED:return "Not connected";
    case DCC_UNABLE_PASSIVE_MODE:return "Unable to initialize passive mode";
    case CTCP_SECONDS:return "seconds";
      
    case IDENT_FAILED_LAUNCH:return "Failed to launch Ident server";
    case IDENT_REQUEST:return "Ident request from";
    case IDENT_ERROR:return "Error occurred";
    case IDENT_REPLIED:return "Replied";
    case IDENT_DEFAULT_USER:return "default user";
    case IDENT_NO_USER:return "No user for request";
    case IDENT_RUNNING_ON_PORT:return "Ident server running on port";
    case IDENT_LEAVING:return "Ident server leaving";
    case IDENT_NONE:return "none";
    case IDENT_UNKNOWN:return "unknown";
    case IDENT_UNDEFINED:return "Undefined result";
      
    case FILE_SAVEAS:return "Save file as";
      
    case ABOUT_ABOUT:return "About";
    case ABOUT_PROGRAMMING:return "Programming";
    case ABOUT_DESIGN:return "Design";
    case ABOUT_THANKS:return "Thanks to";
    case ABOUT_SUPPORT:return "for support, ideas and testing";
    case ABOUT_HELP:return "Help me!";
    case ABOUT_GPL:return "This software is licensed under the GPL license";
      
    case SERVER_UNABLE_TO_CONNECT_TO:return "Unable to connect to";
    case SERVER_TRYING_TO_CONNECT:return "currently trying to connect to";
    case SERVER_DISCONNECTING:return "Disconnecting from";
    case SERVER_CONNECTING:return "Connecting...";
    case SERVER_NOT_CONNECTED:return "Not connected";
    case SERVER_UNABLE_TO_CONNECT:return "Unable to connect";
    case SERVER_LOGIN:return "Loggin in...";
    case SERVER_DISCONNECTED:return "Disconnected";
    case SERVER_ERROR:return "Error";
      
    case SOURCE_YOU_KICKED:return "You've been kicked out of";
    case SOURCE_BY:return "by";
    case SOURCE_STATUS:return "Status";
    case SOURCE_CHANLIST:return "Channels for";
    case SOURCE_CHANLIST_RETREIVING:return "Retrieving channels...";
    case SOURCE_HAS_JOINED:return "has joined";
    case SOURCE_HAS_LEFT:return "has left";
    case SOURCE_HAS_BEEN_KICKED_BY:return "has been kicked by";
    case SOURCE_HAS_QUIT:return "has quit";
    case SOURCE_TOPIC_IS:return "Topic is";
    case SOURCE_CHANGED_TOPIC:return "changed topic to";
    case SOURCE_CHANNEL_MODE:return "sets channel mode to";
    case SOURCE_CHANNEL_MODE_IS:return "Channel mode is";
    case SOURCE_USER_MODE:return "sets mode";
    case SOURCE_ON:return "on";
    case SOURCE_KNOWN_AS:return "is now known as";
    case SOURCE_YOUR_MODE:return "Mode changed to";
    case SOURCE_YOUR_NICK:return "Your nick is now";
    case SOURCE_INFO:return "Infos";
      
    case GUI_WHOIS:return "Whois";
    case GUI_QUERY:return "Query";
    case GUI_KICK:return "Kick";
    case GUI_BAN:return "Ban";
    case GUI_KICKBAN:return "Kick + Ban";
    case GUI_OP:return "Op";
    case GUI_DEOP:return "DeOp";
    case GUI_VOICE:return "Voice";
    case GUI_DEVOICE:return "DeVoice";
    case GUI_PING:return "Ping";
    case GUI_VERSION:return "Version";
    case GUI_TIME:return "Time";
    case GUI_FINGER:return "Finger";
    case GUI_RETREIVING_FILE:return "Receiving file";
    case GUI_SENDING_FILE:return "Sending file";
    case GUI_BYTES:return "bytes";
    case GUI_TERMINATED:return "terminated";
    case GUI_FAILED:return "failed";
    case GUI_CLOSE:return "Close";
    case GUI_CONNECT:return "Connect";
    case GUI_DISCONNECT:return "Disconnect";
    case GUI_CHANNELS:return "Channels";
    case GUI_HELP:return "Help";
    case GUI_PRIVATE:return "private";
    case GUI_PUBLIC:return "public";
    case GUI_ABOUT:return "About";
    case GUI_CHANGE_NICK:return "Change nick to";
		
    case ASL_YEARS:return "years old";
		case ASL_MALE:return "Boy";
		case ASL_FEMALE:return "Girl";

    case ERROR_NOT_DEFINED:return "Undefined string";
		
		default:return null;

    }	
  }
  


}

