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
import java.io.*;

public class BasicCTCPFilter extends IRCObject implements CTCPFilter,DCCChatClosingListener,DCCFileClosingListener
{

  private Hashtable _listeners;

  public BasicCTCPFilter(IRCConfiguration config)
  {
    super(config);
    _listeners=new Hashtable();
  }
  
  public void addDCCListener(DCCListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeDCCListener(DCCListener lis)
  {
    _listeners.remove(lis);
  }
  
  private File triggerDCCSendRequest(String nick,String name,int size)
  {
    File ans=null;
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      DCCListener lis=(DCCListener)e.nextElement();
      ans=lis.DCCFileRequest(nick,name,size);
    }	
    return ans;
  }
  
  private void triggerDCCFileCreatedListeners(DCCFile f)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      DCCListener lis=(DCCListener)e.nextElement();
      lis.DCCFileCreated(f);
    }	
  }
  
  private void triggerDCCFileRemovedListeners(DCCFile f)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      DCCListener lis=(DCCListener)e.nextElement();
      lis.DCCFileRemoved(f);
    }	
  }
  
  private boolean triggerDCCChatRequest(String nick)
  {
    boolean ans=false;
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      DCCListener lis=(DCCListener)e.nextElement();
      ans=lis.DCCChatRequest(nick);
    }	
    return ans;
  }
  
  private void triggerDCCChatCreatedListeners(DCCChat c,boolean bring)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      DCCListener lis=(DCCListener)e.nextElement();
      lis.DCCChatCreated(c,bring);
    }
  }

  private void triggerDCCChatRemovedListeners(DCCChat c)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      DCCListener lis=(DCCListener)e.nextElement();
      lis.DCCChatRemoved(c);
    }
  }
  
  public void chatClosing(DCCChat c)
  {
    triggerDCCChatRemovedListeners(c);
  }
  
  public void fileClosing(DCCFile c)
  {
    triggerDCCFileRemovedListeners(c);
  }
  
  private void send(Server s,String destination,String msg)
  {
    s.say(destination,"\1"+msg+"\1");
  }	
  
  public void action(Server s,String destination,String msg)
  {
    send(s,destination,"ACTION "+msg);
  }
  
  public void ping(Server s,String nick)
  {
    send(s,nick,"PING "+(new Date()).getTime());
  }
  
  public void chat(Server s,String nick)
  {
    DCCChatServer cserver=new DCCChatServer(_ircConfiguration,s.getNick(),nick,this);
    DCCChat chat=new DCCChat(_ircConfiguration,cserver,nick);
    triggerDCCChatCreatedListeners(chat,true);
    String arg=cserver.openPassive(chat);
    if(arg.length()==0)
    {
      chat.report(getText(TextProvider.DCC_UNABLE_PASSIVE_MODE));
    }
    else
    {
      send(s,nick,"DCC CHAT chat "+arg);
    }
  }
  
  public void sendFile(Server s,String nick,String fname)
  {
    DCCFileHandler fhandler=new DCCFileHandler(_ircConfiguration,nick,this);
    File f=new File(fname);
    DCCFile file=new DCCFile(f,fhandler);
    char guil=34;
    String filename=f.getName();
    if(filename.indexOf(" ")!=-1) filename=guil+filename+guil;
    String arg=filename+" "+fhandler.send(file);
    send(s,nick,"DCC SEND "+arg);
    triggerDCCFileCreatedListeners(file);
  }
  
  public void genericSend(Server s,String nick,String message)
  {
    send(s,nick,message);
  }
  
  
  public void perform(String nick,Source source,String msg)
  {	
    String cmd="";
    String param="";
    int pos=msg.indexOf(' ');
    if(pos==-1)
    {
      cmd=msg.toLowerCase();
    }
    else
    {
      cmd=msg.substring(0,pos).toLowerCase();
      param=msg.substring(pos+1);
    }
    
    boolean show=true;
    if(cmd.equals("action"))
    {
    
      source.action(nick,param);
      show=false;
    }
    else if(cmd.equals("version"))
    {
      String data="Plouf's IRC Client de la mort qui tue en Java";
      source.getServer().execute("NOTICE "+nick+" :\1VERSION "+data+"\1");
    }
    else if(cmd.equals("ping"))
    {
      source.getServer().execute("NOTICE "+nick+" :\1PING "+param+"\1");
    }
    else if(cmd.equals("time"))
    {
      String data=new Date().toString();
      source.getServer().execute("NOTICE "+nick+" :\1TIME "+data+"\1");
    }
    else if(cmd.equals("finger"))
    {
      String data="A lucky Plouf's IRC user";
      source.getServer().execute("NOTICE "+nick+" :\1FINGER "+data+"\1");		
    }
    else if(cmd.equals("userinfo"))
    {
      String data="A lucky Plouf's IRC user";
      source.getServer().execute("NOTICE "+nick+" :\1USERINFO "+data+"\1");		
    }
    else if(cmd.equals("clientinfo"))
    {
      String data="This client is a Java application supporting the following CTCP tags : ACTION VERSION PING TIME FINGER USERINFO CLIENTINFO DCC";
      source.getServer().execute("NOTICE "+nick+" :\1CLIENTINFO "+data+"\1");		
    }
    else if(cmd.equals("dcc"))
    {
      StringParser sp=new StringParser();
      String[] args=sp.parseString(param.toLowerCase());
      if(args.length>=2)
      {		
        if(args[0].equals("chat") && args[1].equals("chat"))
        {
          if(args.length>=4)
          {
            if(triggerDCCChatRequest(nick))
            {
              DCCChatServer cserver=new DCCChatServer(_ircConfiguration,source.getServer().getNick(),nick,this);
              DCCChat chat=new DCCChat(_ircConfiguration,cserver,nick);
              cserver.openActive(chat,args[2],args[3]);
              triggerDCCChatCreatedListeners(chat,false);
            }
          }
        }
        if(args[0].equals("send"))
        {
          if(args.length>=5)
          {
            String fname=args[1];
            String ip=args[2];
            String port=args[3];
            String size=args[4];
            File dest=triggerDCCSendRequest(nick,fname,(new Integer(size)).intValue());
            if(dest!=null)
            {
              DCCFileHandler fhandler=new DCCFileHandler(_ircConfiguration,nick,this);
              DCCFile file=new DCCFile(dest,fhandler);
              fhandler.receive(file,ip,port,size);
              triggerDCCFileCreatedListeners(file);
            }
          }
        }
      }
    }
    else if(cmd.equals(""))
    {
    
    }
    if(show) source.getServer().sendStatusMessage("\2\3"+"4"+"["+nick+" "+cmd.toUpperCase()+"]");				
  }
  
  public void CTCPReply(String nick,Source source,String msg)
  {
    String cmd="";
    String param="";
    int pos=msg.indexOf(' ');
    if(pos==-1)
    {
      cmd=msg.toLowerCase();
    }
    else
    {
      cmd=msg.substring(0,pos).toLowerCase();
      param=msg.substring(pos+1);
    }
  
    if(cmd.equals("ping"))
    {
      long d=(new Long(param)).longValue();
      long delta=(new Date()).getTime()-d;
      source.report("\2\3"+"4"+"["+nick+" PING reply] : "+(delta/1000.0)+ " "+getText(TextProvider.CTCP_SECONDS));
    }
    else
    {
      source.report("\2\3"+"4"+"["+nick+" "+cmd.toUpperCase()+" reply] : "+param);
    }
  }
}

