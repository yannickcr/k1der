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

class Nick
{
  public Nick(String n,String m)
  {
    Name=n;
    Mode=new ModeHandler(m);
		Whois="";
  }

  public String Name;
	public String Whois;
  public ModeHandler Mode;
}

public class Channel extends IRCSource implements ReplyServerListener
{
  private String _name;
  private String _topic;
  private ModeHandler _mode;
  private Hashtable _listeners;
  private Hashtable _nicks;

  public Channel(IRCConfiguration config,String name,IRCServer s)
  {
    super(config,s);
    _name=name;
    _topic="";
    _mode=new ModeHandler();
    _listeners=new Hashtable();
    _nicks=new Hashtable();
		s.addReplyServerListener(this);
		if(_ircConfiguration.getASL()) getIRCServer().execute("WHO "+_name);
  }
  
  public void addChannelListener(ChannelListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeChannelListener(ChannelListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerJoinListeners(String nick,String mode)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.nickJoin(nick,mode);
    }
  }
  
  private void triggerSetListeners(String[] nicks,String modes[])
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.nickSet(nicks,modes);
    }	
  }

  private void triggerQuitListeners(String nick,String reason)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.nickQuit(nick,reason);
    }	
  }

  private void triggerPartListeners(String nick,String reason)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.nickPart(nick,reason);
    }	
  }
  
  private void triggerKickListeners(String nick,String by,String reason)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.nickKick(nick,by,reason);
    }	
  }
  
  private void triggerTopicListeners(String topic,String by)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.topicChanged(topic,by);
    }	
  }
  
  private void triggerModeListeners(String mode,String from)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.modeApply(mode,from);
    }	
  }
  
  private void triggerNickModeListeners(String nick,String mode,String from)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.nickModeApply(nick,mode,from);
    }	
  }
 
  private void triggerNickChange(String oldNick,String newNick)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.nickChanged(oldNick,newNick);
    }	
  }
  
  private void triggerNickWhoisUpdate(String nick,String whois)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChannelListener lis=(ChannelListener)e.nextElement();
      lis.nickWhoisUpdated(nick,whois);
    }	
  }
	
  public String getName()
  {
    return _name;
  }
  
  public boolean talkable()
  {
    return true;
  }
  
  public void leave()
  {
    getIRCServer().leaveChannel(getName());
  }

  public boolean hasNick(String nick)
  {
    return _nicks.get(nick)!=null;
  }
  
  public void joinNick(String nick,String mode)
  {
    _nicks.put(nick,new Nick(nick,mode));
		if(_ircConfiguration.getASL()) getIRCServer().execute("WHO "+nick);
    triggerJoinListeners(nick,mode);
  }
  
  public void setNicks(String[] nicks,String[] modes)
  {
    for(int i=0;i<nicks.length;i++) _nicks.put(nicks[i],new Nick(nicks[i],modes[i]));
    triggerSetListeners(nicks,modes);
  }
  
  public void partNick(String nick,String reason)
  {
    _nicks.remove(nick);
    triggerPartListeners(nick,reason);
  }
  
  public void kickNick(String nick,String by,String reason)
  {
    _nicks.remove(nick);
    triggerKickListeners(nick,by,reason);
  }
  
  public void quitNick(String nick,String reason)
  {
    _nicks.remove(nick);
    triggerQuitListeners(nick,reason);
  }
  
  public String[] getNicks()
  {
    String[] ans=new String[_nicks.size()];
    Enumeration e=_nicks.elements();
    int i=0;
    while(e.hasMoreElements())
      ans[i++]=((Nick)e.nextElement()).Name;
    return ans;
  }
  
  public String getNickMode(String nick)
  {
    Nick n=(Nick)_nicks.get(nick);
    if(n==null) return null;
    return n.Mode.getMode();
  }
  
  public void setTopic(String topic,String by)
  {
    _topic=topic;
    triggerTopicListeners(topic,by);
  }
  
  public void applyUserMode(String nick,String mode,String from)
  {
    Nick n=(Nick)_nicks.get(nick);
    if(n!=null) n.Mode.apply(mode);
    triggerNickModeListeners(nick,mode,from);
  }
  
  public void applyMode(String mode,String from)
  {
    _mode.apply(mode);
    triggerModeListeners(mode,from);
  }
  
  public String getMode()
  {
    return _mode.getMode();
  }
  
  public String getTopic()
  {
    return _topic;
  }
  
  public void changeNick(String oldNick,String newNick)
  {
    Nick n=(Nick)_nicks.get(oldNick);
    _nicks.remove(oldNick);
    n.Name=newNick;
    _nicks.put(newNick,n);
    
    triggerNickChange(oldNick,newNick);
  }
  
	private void learn(String nick,String whois)
	{
	  Nick n=(Nick)_nicks.get(nick);
		if(n==null) return;
		n.Whois=whois;
		triggerNickWhoisUpdate(nick,whois);
		//System.out.println("learned "+nick+" is "+whois);
	}
	
	public String whois(String nick)
	{
	  Nick n=(Nick)_nicks.get(nick);
	  if(n==null) return "";
		return n.Whois;
	}
	
  public void replyReceived(String prefix,String id,String params[])
	{
	 /* System.out.print(id+" - ");
		for(int i=0;i<params.length;i++) System.out.print(params[i]+" ");
		System.out.println("");*/
	  if(id.equals("352"))
  	{
		  String name=params[params.length-1];
			int pos=name.indexOf(" ");
			if(pos!=-1) name=name.substring(pos+1);
			String nick=params[5];
			learn(nick,name);
		}
		/*else if(id.equals("311"))
		{
		  String name=params[params.length-1];
			String nick=params[2];
			learn(nick,name);
		}*/
	}	
}

