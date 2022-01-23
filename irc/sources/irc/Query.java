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

public class Query extends IRCSource
{
  private String _nick;
  
  private Hashtable _listeners;
  
  public Query(IRCConfiguration config,String nick,IRCServer s)
  {
    super(config,s);
    _listeners=new Hashtable();
    _nick=nick;
  }
  
  private void queryChangeNickListeners(String nick)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      QueryListener ql=(QueryListener)e.nextElement();
      ql.nickChanged(nick);
    }
  }
  
  public void addQueryListener(QueryListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeQueryListeners(QueryListener lis)
  {
    _listeners.remove(lis);
  }
  
  public String getName()
  {
    return _nick;
  }
  
  public boolean talkable()
  {
    return true;
  }
  
  public void leave()
  {
    getIRCServer().leaveQuery(getName());
  }
  
  public void changeNick(String newNick)
  {
    _nick=newNick;
    queryChangeNickListeners(newNick);
  }
}

