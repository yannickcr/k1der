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

import java.util.*;
import irc.*;
import irc.style.*;
import java.awt.*;
import java.awt.event.*;
import irc.gui.pixx.*;

public class AWTSource extends Panel implements SourceListener,ActionListener,PixxScrollBarListener,FocusListener,StyledListListener
{
  protected Source _source;
 // protected Frame _frame;
  protected PixxScrollBar _scroll;
  protected Panel _panel;
  protected StyledList _list;
  protected AWTIrcTextField _textField;
  protected FormattedStringDrawer _styler;
  protected String _title;
  protected String _strippedTitle;
  private Hashtable _listeners;
  protected IRCConfiguration _ircConfiguration;

  public AWTSource(IRCConfiguration config,Source source)
  {
    _ircConfiguration=config;
    _listeners=new Hashtable();
    _source=source;
    addFocusListener(this);
    _source.addSourceListener(this);
  //  _frame=new Frame();
    _panel=new Panel();
    _panel.addFocusListener(this);
    _panel.setBackground(Color.white);
    _scroll=new PixxScrollBar(_ircConfiguration,0,0,PixxScrollBar.VERTICAL,0.1);
    _scroll.addPixxScrollBarListener(this);
    /*_frame.*/setLayout(new BorderLayout());
  //  _frame.addWindowListener(this);
    _list=new StyledList(_ircConfiguration,new Font(_ircConfiguration.getChannelFontName(),Font.PLAIN,_ircConfiguration.getChannelFontSize()),_ircConfiguration.getColorContext(source.getName()));
    _list.addFocusListener(this);
    _list.addStyledListListener(this);
    _styler=new FormattedStringDrawer(new Font("Monospaced",Font.PLAIN,12),_ircConfiguration,getColorContext());
    _textField=new AWTIrcTextField();
    _textField.addFocusListener(this);
    //_textField.addKeyListener(this);
    Panel p=new Panel();
    p.setLayout(new BorderLayout());
    p.add(_panel,"Center");
    p.add(new PixxSeparator(PixxSeparator.BORDER_LEFT),"West");
    p.add(new PixxSeparator(PixxSeparator.BORDER_RIGHT),"East");
    p.add(new PixxSeparator(PixxSeparator.BORDER_UP),"North");
    p.add(new PixxSeparator(PixxSeparator.BORDER_DOWN),"South");
    
    
    /*_frame.*/add(p,"Center");
    _panel.setLayout(new BorderLayout());
    _panel.add(_list,"Center");
    _panel.add(_scroll,"East");
    /*_frame.*/add(_textField,"South");
    
    _textField.addActionListener(this);
    setTitle(_source.getName());
    /*_frame.*/setSize(640,400);
    //_frame.add(this);
  }
  
	public ColorContext getColorContext()
	{
    return _ircConfiguration.getColorContext(_source.getName());
	}
	
  public String getText(int code)
  {
    return _ircConfiguration.getText(code);
  }
  
  public void addAWTSourceListener(AWTSourceListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeAWTSourceListener(AWTSourceListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerTitleChangedListeners()
  {
    triggerEventListeners();
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      AWTSourceListener lis=(AWTSourceListener)e.nextElement();
      lis.titleChanged(this);
    }
  }
  
  private void triggerEventListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      AWTSourceListener lis=(AWTSourceListener)e.nextElement();
      lis.eventOccured(this);
    }
  }
  
  private void triggerActivatedListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      AWTSourceListener lis=(AWTSourceListener)e.nextElement();
      lis.activated(this);
    }
  }
  
  public void setTitle(String title)
  {
	  if(title.equals(_title)) return;
    _title=title;
    _strippedTitle=_styler.getStripped(title);
    triggerTitleChangedListeners();
  }
  
  public String getStrippedTitle()
  {
    return _strippedTitle;
  }
  
  public String getTitle()
  {
    return _title;
  }
  
  public String getShortTitle()
  {
    return _source.getName();
  }
  
  public Source getSource()
  {
    return _source;
  }
  
  public void actionPerformed(ActionEvent e)
  {
    _source.sendString(_textField.getText());
  }
  
  public void clear()
  {
    _list.clear();
    _scroll.setMaximum(_list.getLineCount()-1);
    _scroll.setValue(_list.getLast());
    triggerEventListeners();
  }
  
  protected boolean needHighLight(String msg)
  {
    msg=msg.toLowerCase();
    if(_ircConfiguration.highLightNick())
    {
      String myNick=_source.getServer().getNick().toLowerCase();
      if(msg.indexOf(myNick)!=-1) return true;
    }
    
    Enumeration e=_ircConfiguration.getHighLightWords();
    while(e.hasMoreElements())
    {
      String word=((String)(e.nextElement())).toLowerCase();
      if(msg.indexOf(word)!=-1) return true;			
    }
    return false;
  }
  
  protected void print(String msg,int color,boolean bold,boolean underline)
  {
    _source.activate();
    if(color!=1) msg="\3"+color+msg;
    if(bold) msg="\2"+msg;
    if(underline) msg=((char)31)+msg;
    
  
    
    if(_ircConfiguration.getTimeStamp())
    {
      Calendar cal=Calendar.getInstance();
      String hour=""+cal.get(Calendar.HOUR_OF_DAY);
      if(hour.length()==1) hour="0"+hour;
      String min=""+cal.get(Calendar.MINUTE);
      if(min.length()==1) min="0"+min;
      msg="["+hour+":"+min+"] "+msg;
    }
    _list.addLine(msg);
    _scroll.setMaximum(_list.getLineCount()-1);
    _scroll.setValue(_list.getLast());
    triggerEventListeners();
  }
  
  protected void print(String msg,int color)
  {
    print(msg,color,false,false);
  }
  
  protected void print(String msg)
  {
    print(msg,1,false,false);
  }
  
  public void messageReceived(String source,String str)
  {
    if(needHighLight(str))
    {
      print("<"+source+"> "+str,_ircConfiguration.highLightColor());
    }
    else
    {
      print("<"+source+"> "+str);		
    }
  }
  
  public void reportReceived(String msg)
  {
    print(msg);
  }
  
  public void noticeReceived(String from,String msg)
  {
    print("-"+from+"- "+msg,5);
  }
  
  public void action(String nick,String msg)
  {
    print("* "+nick+" "+msg,6);
  }
  
  public void activate()
  {
    triggerActivatedListeners();
  }
  
  public boolean isActive()
  {
    return _source.isActive();
  }
  
  public void leave()
  {
    _source.leave();
  }

  public void valueChanged(PixxScrollBar pixScrollBar)
  {
    _list.setLast(_scroll.getValue());
  }
 
  public void focusGained(FocusEvent e)
  {
    if(e.getComponent()!=_textField)
      _textField.requestFocus();
  }
  
  public void focusLost(FocusEvent e)
  {
  }
  
  public void channelEvent(StyledList lis,String chan,MouseEvent e)
  {
    if(e.getClickCount()>1)
    {
      _source.sendString("/join "+chan);
    }	
  }
  
  public void URLEvent(StyledList lis,String url,MouseEvent e)
  {
    if(e.getClickCount()>1)
    {
      _source.sendString("/url "+url);		
    }	
  }
  
  public void nickEvent(StyledList lis,String nick,MouseEvent e)
  {
    if(e.getClickCount()>1)
    {
      _source.sendString("/query "+nick);
    }	
  }
	
	public String toString()
	{
	  return "AWTSource : "+getStrippedTitle();
	}
}

