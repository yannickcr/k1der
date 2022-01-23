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

public abstract class Source extends IRCObject
{
  protected Server _server;
  private Hashtable _listeners;
  protected Interpretor _in;
  private CTCPFilter _filter;
  private boolean _activated;

  public Source(IRCConfiguration config,Server s)
  {
    super(config);
    _activated=false;
    _listeners=new Hashtable();
    _in=new NullInterpretor();
    _server=s;
    _filter=new NullCTCPFilter();
  }

  public abstract String getName();
  public abstract boolean talkable();

  public abstract void leave();

  public void setInterpretor(Interpretor in)
  {
    _in=in;
  }
  
  public void setCTCPFilter(CTCPFilter filter)
  {
    _filter=filter;
  }

  public void sendString(String str)
  {
    _in.sendString(this,str);	
  }
  
  public Interpretor getInterpretor()
  {
    return _in;
  }

  public void clear()
  {
    triggerClearListeners();
  }

  public void messageReceived(String source,String msg)
  {
    if(msg.startsWith("\1"))
    {
      msg=msg.substring(1);
      msg=msg.substring(0,msg.length()-1);
      _filter.perform(source,this,msg);
    }
    else
    {
      triggerListeners(source,msg);
    }
  }

  public void noticeReceived(String source,String msg)
  {
    if(msg.startsWith("\1"))
    {
      msg=msg.substring(1);
      msg=msg.substring(0,msg.length()-1);
      _filter.CTCPReply(source,this,msg);		
    }
    else
    {
      triggerNoticeListeners(source,msg);
    }
  }
  
  public void action(String nick,String msg)
  {
    triggerActionListeners(nick,msg);
  }
  
  public void report(String msg)
  {
    triggerReportListeners(msg);
  }
  
  public void addSourceListener(SourceListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeSourceListener(SourceListener lis)
  {
    _listeners.remove(lis);
  }
  
  protected void triggerListeners(String source,String msg)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      SourceListener lis=(SourceListener)e.nextElement();
      lis.messageReceived(source,msg);
    }
  }

  protected void triggerReportListeners(String msg)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      SourceListener lis=(SourceListener)e.nextElement();
      lis.reportReceived(msg);
    }
  }
  
  protected void triggerNoticeListeners(String from,String msg)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      SourceListener lis=(SourceListener)e.nextElement();
      lis.noticeReceived(from,msg);
    }
  }
  
  protected void triggerActionListeners(String nick,String msg)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      SourceListener lis=(SourceListener)e.nextElement();
      lis.action(nick,msg);
    }
  }
  
  protected void triggerClearListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      SourceListener lis=(SourceListener)e.nextElement();
      lis.clear();
    }
  }
  
  public void activate()
  {
    if(!_activated)
    {
      _activated=true;
      triggerActivateListeners();
    }
  }
  
  public boolean isActive()
  {
    return _activated;
  }
  
  protected void triggerActivateListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      SourceListener lis=(SourceListener)e.nextElement();
      lis.activate();
    }
  }
  
  public Server getServer()
  {
    return _server;
  }

}

