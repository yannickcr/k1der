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
import irc.ident.*;

public class IRCServer extends IRCObject implements Server, ServerProtocolListener
{
  private ServerProtocol _protocol;
  private Hashtable _channels;
  private Hashtable _queries;
  private Hashtable _chanlist;
  private Hashtable _ignoreList;
	
  private Status _status;
  private Hashtable _listeners;
  private Hashtable _replylisteners;
  private String _askedNick;
  private String _nick;
  private String _userName;
  private ModeHandler _mode=new ModeHandler();
  private IdentServer _ident;
  private String _system;
  private String _id;
  private String _host;
  private int _localPort;
	private boolean _connected;
  
  public IRCServer(IRCConfiguration config,String nick,String userName)
  {
    this(config,nick,userName,null,"","");
  }
  
  public IRCServer(IRCConfiguration config,String nick,String userName,IdentServer ident,String system,String id)
  {
    super(config);
		_connected=false;
    _ident=ident;
    _system=system;
    _id=id;
    _userName=userName;
		_ignoreList=new Hashtable();
    _channels=new Hashtable();
    _queries=new Hashtable();
    _chanlist=new Hashtable();
    _listeners=new Hashtable();
    _replylisteners=new Hashtable();
    _protocol=new ServerProtocol(_ircConfiguration);
    _protocol.addServerProtocolListener(this);
    _status=new Status(_ircConfiguration,this);
		addReplyServerListener(_status);
    _askedNick=nick;
    _nick=nick;
  }
  
  public void connect(String host,int port)
  {
    if(_protocol.connecting())
    {
      sendStatusMessage(getText(TextProvider.SERVER_UNABLE_TO_CONNECT_TO)+" "+host+" : "+getText(TextProvider.SERVER_TRYING_TO_CONNECT)+" "+_host);
      return;
    }
    if(_protocol.connected())
    {
      sendStatusMessage(getText(TextProvider.SERVER_DISCONNECTED)+" "+_host);
      disconnect();
    }
    _host=host;
	  _connected=false;
    sendStatusMessage(getText(TextProvider.SERVER_CONNECTING));
    _protocol.connect(host,port);
  }
  
  public void disconnect()
  {
    if(_protocol.connected())
    {
      //_protocol.disconnect();
      if(_ircConfiguration.quitMessage().length()==0)
      {
        execute("QUIT");
      }
      else
      {
        execute("QUIT :"+_ircConfiguration.quitMessage());			
      }
    }
    else
    {
      sendStatusMessage(getText(TextProvider.SERVER_NOT_CONNECTED));
    }
  }
  
  public boolean isConnected()
  {
  //  return _protocol.connected();
	  return _connected;
  }
  
  public void connectionFailed(String message)
  {
    sendStatusMessage(getText(TextProvider.SERVER_UNABLE_TO_CONNECT)+" : "+message);
    triggerDisconnect();
  }
  
	private boolean trynickagain()
	{
	  return _askedNick.indexOf("?")!=-1;
	}
	
	private void register()
	{
	  String ans="";
    for(int i=0;i<_askedNick.length();i++)
    {
      char c=_askedNick.charAt(i);
      if(c=='?') c=(char)('0'+Math.random()*10);
      ans+=c;
    }

    sendString("nick "+ans);
    sendString("user "+ans+" 0 0 :"+_userName);
	}
	
  public void connected()
  {
    _localPort=_protocol.getLocalPort();
    if(_ident!=null)
    {
      _ident.registerLocalConnection(_localPort,_system,_id);
    }
    sendStatusMessage(getText(TextProvider.SERVER_LOGIN));
		register();
  }
  
  public void disconnected()
  {
	  _connected=false;
    sendStatusMessage(getText(TextProvider.SERVER_DISCONNECTED));
    if(_ident!=null)
    {
      _ident.unregisterLocalConnection(_localPort);
    }
    
    Enumeration e;
    
    e=_channels.elements();
    while(e.hasMoreElements())
    {
      Channel c=(Channel)e.nextElement();
      triggerChannelRemoval(c);
    }
    _channels.clear();
    
    e=_queries.elements();
    while(e.hasMoreElements())
    {
      Query c=(Query)e.nextElement();
      triggerQueryRemoval(c);
    }
    _queries.clear();
    
    e=_chanlist.elements();
    while(e.hasMoreElements())
    {
      ChanList c=(ChanList)e.nextElement();
      triggerChanListRemoval(c);
    }
    _chanlist.clear();
    
    _mode.reset();
    _status.modeChanged(getMode());
  
    triggerDisconnect();
  }
  
  public void sendStatusMessage(String msg)
  {
    _status.report(msg);
  }
  
  public Enumeration getChannels()
  {
    return _channels.elements();
  }
  
  public Enumeration getQueries()
  {
    return _queries.elements();
  }
  
  public Enumeration getChanLists()
  {
    return _chanlist.elements();	
  }
  
  public Channel getChannel(String name)
  {
    Channel c=(Channel)_channels.get(name.toLowerCase());
    if(c==null)
    {
      c=new Channel(_ircConfiguration,name,this);
      _channels.put(name.toLowerCase(),c);
      triggerChannelCreation(c);
    }
    return c;
  }
  
  public Query getQuery(String nick,boolean bring)
  {
    Query c=(Query)_queries.get(nick.toLowerCase());
    if(c==null)
    {
      c=new Query(_ircConfiguration,nick,this);
      _queries.put(nick.toLowerCase(),c);
      triggerQueryCreation(c,bring);
    }
    return c;
  }
  
  public ChanList getChanList(String name)
  {
    ChanList c=(ChanList)_chanlist.get(name.toLowerCase());
    if(c==null)
    {
      c=new ChanList(_ircConfiguration,this,name);
      _chanlist.put(name.toLowerCase(),c);
      triggerChanListCreation(c);
    }
    return c;	
  }
  
  public void leaveChannel(String name)
  {
    sendString("part "+name);
  }
  
  public void leaveQuery(String name)
  {
    triggerQueryRemoval(getQuery(name,false));
    deleteQuery(name);
  }
  
  public void leaveChanList(String name)
  {
    triggerChanListRemoval(getChanList(name));
    deleteChanList(name);
  }
  
  private void deleteChannel(String name)
  {
    _channels.remove(name.toLowerCase());
  }
  
  private void deleteQuery(String name)
  {
    _queries.remove(name.toLowerCase());
  }
  
  private void deleteChanList(String name)
  {
    _chanlist.remove(name.toLowerCase());
  }
  
  public Status getStatus()
  {
    return _status;
  }
  
  public void addServerListener(ServerListener l)
  {
    _listeners.put(l,l);
  }
  
  public void removeServerListener(ServerListener l)
  {
    _listeners.remove(l);
  }
  
  public void addReplyServerListener(ReplyServerListener l)
  {
    _replylisteners.put(l,l);
  }
  
  public void removeReplyServerListener(ReplyServerListener l)
  {
    _replylisteners.remove(l);
  }
	
  private void triggerConnect()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerListener lis=(ServerListener)e.nextElement();
      lis.serverConnected();
    }
  }
  
  private void triggerDisconnect()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerListener lis=(ServerListener)e.nextElement();
      lis.serverDisconnected();
    }
  }
  
  private void triggerChannelCreation(Channel c)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerListener lis=(ServerListener)e.nextElement();
      lis.channelCreated(c);
    }
  }
  
  private void triggerChannelRemoval(Channel c)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerListener lis=(ServerListener)e.nextElement();
      lis.channelRemoved(c);
    }
  }
  
  private void triggerQueryCreation(Query c,boolean bring)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerListener lis=(ServerListener)e.nextElement();
      lis.queryCreated(c,bring);
    }
  }
  
  private void triggerQueryRemoval(Query c)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerListener lis=(ServerListener)e.nextElement();
      lis.queryRemoved(c);
    }
  }
  
  private void triggerChanListCreation(ChanList c)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerListener lis=(ServerListener)e.nextElement();
      lis.chanListCreated(c);
    }
  }
  
  private void triggerChanListRemoval(ChanList c)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerListener lis=(ServerListener)e.nextElement();
      lis.chanListRemoved(c);
    }
  }
  
  private void setNicks(Channel c,Vector nicks)
  {
    String[] n=new String[nicks.size()];
    String[] modes=new String[nicks.size()];
    
    for(int i=0;i<nicks.size();i++)
    {
      n[i]=(String)nicks.elementAt(i);
      modes[i]="";
      if(n[i].startsWith("+")) modes[i]="v";
      if(n[i].startsWith("@")) modes[i]="o";
      if(n[i].startsWith("%")) modes[i]="h";
      if(modes[i].length()!=0) n[i]=n[i].substring(1);
    }
    c.setNicks(n,modes);
  }
 
  public void replyReceived(String prefix,String id,String params[])
  {
    if(id.equals("324")) //mode
    {
      Channel c=getChannel(params[1]);
      String mode="";
      for(int i=2;i<params.length;i++) mode+=" "+params[i];
      mode=mode.substring(1);
      c.applyMode(mode,"");
    }
    if(id.equals("332")) //topic
    {
      Channel c=getChannel(params[1]);
      c.setTopic(params[2],"");		
    }
    if(id.equals("353")) //names
    {
      int first=1;
      if(params[1].length()==1) first++;
      Channel c=getChannel(params[first]);
      String nick="";
      Vector nicks=new Vector();
      for(int i=0;i<params[first+1].length();i++)
      {
        char u=params[first+1].charAt(i);
        if(u==' ')
        {
          if(nick.length()>0) nicks.insertElementAt(nick,nicks.size());
          nick="";
        }
        else
        {
          nick+=u;
        }
      }
      if(nick.length()>0) nicks.insertElementAt(nick,nicks.size());
      setNicks(c,nicks);
    }
    else if(id.equals("001"))
    {
      String nick=params[0];
      if(!(nick.equals(_nick)))
      {
        _nick=nick;
        _status.nickChanged(nick);
      }
			_connected=true;
      triggerConnect();			
    }
    else if(id.equals("321")) ///list begin
    {
      getChanList(_host).begin();
    }
    else if(id.equals("322")) ///list
    {
      String name=params[1];
      int count=new Integer(params[2]).intValue();
			if(count<32767)
			{
        String topic=params[3];
        getChanList(_host).addChannel(new ChannelInfo(name,topic,count));		
			}
    }
    else if(id.equals("323")) ///list end
    {
      getChanList(_host).end();
    }
		else if(id.equals("433")) //nick used
		{
		  if(!_connected && trynickagain()) register();
		}
    else
    {
   /*   String toSend="";
      for(int i=1;i<params.length;i++) toSend+=" "+params[i];
      toSend=toSend.substring(1);
      sendStatusMessage(toSend);*/
    }
		
    Enumeration e=_replylisteners.elements();
    while(e.hasMoreElements())
    {
      ReplyServerListener lis=(ReplyServerListener)e.nextElement();
      lis.replyReceived(prefix,id,params);
    }
	  /*System.out.print(id+" - ");
		for(int i=0;i<params.length;i++) System.out.print(params[i]+" ");
		System.out.println("");*/
		
  }
  
  private String extractNick(String full)
  {
    int pos=full.indexOf('!');
    if(pos==-1) return full;
    return full.substring(0,pos);
  }
  
  private boolean isChannel(String name)
  {
    if(name.length()==0) return false;
    char f=name.charAt(0);
    return (f=='!') || (f=='+') || (f=='#') || (f=='&');
  }
  
  private void globalNickRemove(String nick,String reason)
  {
    Enumeration e=_channels.elements();
    while(e.hasMoreElements())
    {
      Channel c=(Channel)e.nextElement();
      if(c.hasNick(nick)) c.quitNick(nick,reason);
    }
  }
  
  private void globalNickChange(String oldNick,String newNick)
  {
    Enumeration e;
    e=_channels.elements();
    while(e.hasMoreElements())
    {
      Channel c=(Channel)e.nextElement();
      if(c.hasNick(oldNick)) c.changeNick(oldNick,newNick);
    }
    
    Query q=(Query)_queries.get(oldNick.toLowerCase());
    if(q!=null)
    {
      _queries.remove(oldNick.toLowerCase());
      q.changeNick(newNick);
      _queries.put(newNick.toLowerCase(),q);
    }		
  }
	
	public synchronized boolean ignore(String nick)
	{
	  return _ignoreList.get(nick)!=null;
	}
	
	public synchronized void addIgnore(String nick)
	{
	  _ignoreList.put(nick,nick);
	}
	
	public synchronized void removeIgnore(String nick)
	{
	  _ignoreList.remove(nick);
	}
	
  public void messageReceived(String prefix,String command,String params[])
  {
    String toSend="";
    for(int i=0;i<params.length;i++) toSend+=" "+params[i];
    
    
    command=command.toLowerCase();
    
    String nick=extractNick(prefix);
    
    if(command.equals("notice"))
    {
      if(!ignore(nick)) _status.noticeReceived(nick,params[1]);
    }
    else if(command.equals("privmsg"))
    {
		  if(!ignore(nick))
			{
        if(isChannel(params[0]))
          getChannel(params[0]).messageReceived(nick,params[1]);
        else
          getQuery(nick,false).messageReceived(nick,params[1]);
			}
    }
    else if(command.equals("join"))
    {
      Channel c=getChannel(params[0]);
      if(!nick.equals(getNick()))
        c.joinNick(nick,"");
      else
        sendString("mode "+params[0]);
    }
    else if(command.equals("part"))
    {
      Channel c=getChannel(params[0]);
      if(params.length>1)
      {
        c.partNick(nick,params[1]);
      }
      else
      {
        c.partNick(nick,"");
      }
      if(nick.equals(getNick()))
      {
        deleteChannel(c.getName());
        triggerChannelRemoval(c);
      }
    }
    else if(command.equals("kick"))
    {
      Channel c=getChannel(params[0]);
      String target=params[1];
      String source=nick;
      String reason="";
      if(params.length>2) reason=params[2];
      c.kickNick(target,nick,reason);
      if(target.equals(getNick()))
      {
        sendStatusMessage(getText(TextProvider.SOURCE_YOU_KICKED)+" "+params[0]+" "+getText(TextProvider.SOURCE_BY)+" "+source+" ("+reason+")");
        deleteChannel(c.getName());
        triggerChannelRemoval(c);
      }
    }
    else if(command.equals("topic"))
    {
      Channel c=getChannel(params[0]);
      c.setTopic(params[1],nick);
    }
    else if(command.equals("mode"))
    {
      if(isChannel(params[0]))
      {
        Channel c=getChannel(params[0]);
        String mode=params[1];
				String mod=""+mode.charAt(0);
				int modeCount=mode.length()-1;
				int targetCount=params.length-2;
				int targetUse=0;
				
				for(int i=0;i<modeCount;i++)
				{
				  char m=mode.charAt(i+1);
					
					if((m=='k') || (m=='l'))
					{
						String st=mod+m;
						if(!((m=='l') && (mod.equals("-")))) st+=" "+params[2+targetUse++];
					  c.applyMode(st,nick);
					}
          else if(modeCount-targetCount-i>0)
          {
            c.applyMode(mod+m,nick);
          }
          else
          {
            c.applyUserMode(params[2+targetUse++],mod+m,nick);
          }
				}
      }
      else if(nick.equals(getNick()))
      {
        _mode.apply(params[1]);
        _status.modeChanged(getMode());
      }
    }
    else if(command.equals("nick"))
    {
      if(nick.equals(getNick()))
      {
        _nick=params[0];
        _status.nickChanged(getNick());
      }
      globalNickChange(nick,params[0]);
    }
    else if(command.equals("quit"))
    {
      if(params.length>0)
        globalNickRemove(nick,params[0]);
      else
        globalNickRemove(nick,"");
    }
    else if(command.equals("ping"))
    {
      sendString("pong "+params[0]);
   //   sendStatusMessage("\3"+"3"+"PING? PONG!");
    }
    else if(command.equals("error"))
    {
      sendStatusMessage(getText(TextProvider.SERVER_ERROR)+" : "+params[0]);
    }
    else
    {
      //   System.out.println("("+command+") "+prefix+" -> "+toSend);
    }
  }
  
  public String getNick()
  {
    return _nick;
  }
  
  public String getMode()
  {
    return _mode.getMode();
  }
  
  public void say(String destination,String str)
  {
    execute("PRIVMSG "+destination+" :"+str);
  }
  
  public void execute(String str)
  {
    sendString(str);
  }
  
  private void sendString(String str)
  {
    try
    {
      _protocol.sendString(str);
    }
    catch(Exception e)
    {
      sendStatusMessage(getText(TextProvider.SERVER_ERROR)+" : "+e.getMessage());		
    }
  }
  
}

