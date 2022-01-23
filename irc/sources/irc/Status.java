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

public class Status extends IRCSource implements ReplyServerListener
{
  private Hashtable _listeners;

  public Status(IRCConfiguration config,IRCServer s)
  {
    super(config,s);
    _listeners=new Hashtable();
  }
  
  public String getName()
  {
	  if(_ircConfiguration.getInfo())
      return getText(TextProvider.SOURCE_INFO);
		else
      return getText(TextProvider.SOURCE_STATUS);
  }

  public boolean talkable()
  {
    return false;
  }

  public void leave()
  {	
    sendString("/quit");	
  }
  
  public String getNick()
  {
    return _server.getNick();
  }
  
  public String getMode()
  {
    return getIRCServer().getMode();
  }
  
  public void addStatusListener(StatusListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeStatusListener(StatusListener lis)
  {
    _listeners.remove(lis);
  }	
  
  private void triggerNickChangedListeners(String nick)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      StatusListener lis=(StatusListener)e.nextElement();
      lis.nickChanged(nick);
    }
  }
  
  private void triggerModeChangedListeners(String mode)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      StatusListener lis=(StatusListener)e.nextElement();
      lis.modeChanged(mode);
    }
  }
  
  public void nickChanged(String nick)
  {
    triggerNickChangedListeners(nick);
  }
  
  public void modeChanged(String mode)
  {
    triggerModeChangedListeners(mode);
  }
	
  public void replyReceived(String prefix,String id,String params[])
	{
	  if(_ircConfiguration.getInfo())
		{
		  int i=new Integer(id).intValue();
			if((i>=300) && (i!=372)) return;
		}
		if(id.equals("322")) return;
    String toSend="";
    for(int i=1;i<params.length;i++) toSend+=" "+params[i];
    toSend=toSend.substring(1);
    report(toSend);
	}	
}

