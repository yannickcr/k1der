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

public class ChanList extends IRCObject
{
  private Hashtable _listeners;
  private Vector _channels;
  private IRCServer _server;
  private String _name;

  public ChanList(IRCConfiguration config,IRCServer server,String name)
  {
    super(config);
    _name=name;
    _server=server;
    _listeners=new Hashtable();
    _channels=new Vector();
  }
  
  public String getName()
  {
    return _name;
  }
  
  public ChannelInfo[] getChannels()
  {
    ChannelInfo[] ans=new ChannelInfo[_channels.size()];
    for(int i=0;i<_channels.size();i++) ans[i]=(ChannelInfo)_channels.elementAt(i);
    return ans;
  }
  
  public void addChannel(ChannelInfo nfo)
  {
    _channels.insertElementAt(nfo,_channels.size());
    triggerChanListListeners(nfo);
  }	
  
  public void begin()
  {
    _channels=new Vector();
    triggerChanListBeginListeners();
  }
  
  public void end()
  {
    triggerChanListEndListeners();
  }
  
  public IRCServer getServer()
  {
    return _server;
  }
  
  public void addChanListListener(ChanListListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeChanListListeners(ChanListListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerChanListListeners(ChannelInfo nfo)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChanListListener lis=(ChanListListener)e.nextElement();
      lis.channelAdded(nfo);		
    }
  }
  
  private void triggerChanListBeginListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChanListListener lis=(ChanListListener)e.nextElement();
      lis.channelBegin();
    }
  }
  
  private void triggerChanListEndListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ChanListListener lis=(ChanListListener)e.nextElement();
      lis.channelEnd();
    }
  }
  
  public void leave()
  {
    _server.leaveChanList(_name);
  }	
}

