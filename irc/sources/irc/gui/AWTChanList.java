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

package irc.gui;

import irc.*;
import irc.style.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
import irc.tree.*;

public class AWTChanList implements ChanListListener,WindowListener,AdjustmentListener,StyledListListener,irc.tree.Comparator
{
  protected ChanList _list;
  protected Frame _frame;
  protected java.awt.Scrollbar _hscroll;
  protected java.awt.Scrollbar _vscroll;
  protected StyledList _slist;
  protected Panel _panel;
  
  private boolean _closed;
  private boolean _activated;
  
  private SortedList _sortedList;
  private IRCConfiguration _ircConfiguration;

  public AWTChanList(IRCConfiguration config,ChanList list)
  {
    _ircConfiguration=config;
    _sortedList=new SortedList(this);
    _closed=false;
    _activated=false;
    _list=list;
    _list.addChanListListener(this);
    _frame=new Frame();
    _frame.setBackground(Color.white);
    _panel=new Panel();
    _hscroll=new java.awt.Scrollbar(java.awt.Scrollbar.HORIZONTAL,0,60,0,0);
    _vscroll=new java.awt.Scrollbar(java.awt.Scrollbar.VERTICAL,0,60,0,0);
    _hscroll.addAdjustmentListener(this);
    _vscroll.addAdjustmentListener(this);
    _frame.setLayout(new BorderLayout());
    _frame.addWindowListener(this);
    _slist=new StyledList(_ircConfiguration,new Font(_ircConfiguration.getChanlistFontName(),Font.PLAIN,_ircConfiguration.getChanlistFontSize()),false,_ircConfiguration.getChanlistColorContext());
    _slist.addStyledListListener(this);
    
    _frame.add(_panel,"Center");
    _panel.setLayout(new BorderLayout());
    _panel.add(_slist,"Center");
    _panel.add(_vscroll,"East");
    _frame.add(_hscroll,"South");
    
    setTitle(getText(TextProvider.SOURCE_CHANLIST)+" "+_list.getName());
    _frame.setSize(640,400);
  }
  
  public String getText(int code)
  {
    return _ircConfiguration.getText(code);
  }
  
  public void setTitle(String title)
  {
    _frame.setTitle(title);
  }
  
  public ChanList getChanList()
  {
    return _list;
  }
 
  public void channelBegin()
  {
    activate();
    _slist.clear();	
    _slist.addLine(getText(TextProvider.SOURCE_CHANLIST_RETREIVING));
    _sortedList=new SortedList(this);
  }
  
  public void channelEnd()
  {
    _slist.clear();
    Enumeration e=_sortedList.getItems();
    while(e.hasMoreElements())
    {
      ChannelInfo ci=(ChannelInfo)e.nextElement();
      _slist.addLine(format(ci));
      _slist.setLast(0);
    }
    _slist.setFirst(0);
    _vscroll.setMaximum(_slist.getLineCount());
    _vscroll.setValue(_slist.getLast());
    _hscroll.setMaximum(_slist.getLogicalWidth()/10);

  }
  
  private String format(ChannelInfo item)
  {
    String msg=item.name;
    String count=""+item.userCount;
    for(int i=0;i<20-item.name.length();i++) msg+=" ";
    msg+="   "+item.userCount;
    for(int i=0;i<5-count.length();i++) msg+=" ";
    msg+="   "+item.topic;
    return msg;
  }
  
  public void channelAdded(ChannelInfo item)
  {
    _sortedList.add(item);
  }
  
  private void activate()
  {
    if(_activated) return;
    _activated=true;
    _closed=false;
    _frame.show();
  }
  
  public int compare(Object o1,Object o2)
  {
    ChannelInfo info1=(ChannelInfo)o1;
    ChannelInfo info2=(ChannelInfo)o2;
    return info2.userCount-info1.userCount;
  }
  
  public void close()
  {
    if(_closed) return;
    _closed=true;
    _activated=false;
    _frame.hide();
    _frame.removeWindowListener(this);
    _frame.dispose();
  }

  public void windowActivated(WindowEvent e) {}
  public void windowClosed(WindowEvent e) {}
  public void windowClosing(WindowEvent e)
  {
    _list.leave();
  }
  
  public void windowDeactivated(WindowEvent e) {}
  public void windowDeiconified(WindowEvent e) {}
  public void windowIconified(WindowEvent e) {}
  public void windowOpened(WindowEvent e) {}

  public void adjustmentValueChanged(AdjustmentEvent e)
  {
    if(e.getSource()==_vscroll)
    {
      _slist.setLast(_vscroll.getValue());
    }
    else
    {
      _slist.setLeft(_hscroll.getValue()*10);
    }
  }
  
  public void channelEvent(StyledList lis,String chan,MouseEvent e)
  {
    if(e.getClickCount()>1)
    {
      _list.getServer().execute("join "+chan);
    }	
  }
  
  public void URLEvent(StyledList lis,String url,MouseEvent e)
  {
    if(e.getClickCount()>1)
    {
      _ircConfiguration.openURL(url);
    }
  }
  
  public void nickEvent(StyledList lis,String nick,MouseEvent e)
  {
  }
  
}

