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

package irc.gui.pixx;

import java.awt.*;
import java.awt.event.*;
import java.util.*;
import irc.style.*;
import irc.*;

public class PixxMenuBar extends PixxPanel implements MouseListener,MouseMotionListener
{
  
  private int _pressedIndex;
  private boolean _closePressed;
  private Hashtable _listeners;
  private boolean _connected;
  private boolean _title;
  private Image _buffer;
  private FormattedStringDrawer _drawer;
  private String _titleString;
  private int _connectIndex;
	private int _chanlistIndex;
	private int _aboutIndex;
	private int _helpIndex;
	private int _titleLeft;
	private int _mouseDownX;
	private boolean _mouseScroll;
	
  public PixxMenuBar(IRCConfiguration config)
  {
    this(config,false);
  }
  
  public PixxMenuBar(IRCConfiguration config,boolean title)
  {
    super(config);
		_titleLeft=0;
    _title=title;
		_mouseScroll=false;
    _titleString="";
    _drawer=new FormattedStringDrawer(new Font("Monospaced",Font.PLAIN,12),_ircConfiguration,_ircConfiguration.getColorContext(null));
    _connected=false;
    _pressedIndex=-1;
    _closePressed=false;
    _listeners=new Hashtable();
		int currentIndex=0;
		if(_ircConfiguration.getShowConnect()) _connectIndex=currentIndex++;
		if(_ircConfiguration.getShowChanlist()) _chanlistIndex=currentIndex++;
		if(_ircConfiguration.getShowAbout()) _aboutIndex=currentIndex++;
		if(_ircConfiguration.getShowHelp()) _helpIndex=currentIndex++;
    addMouseListener(this);
		addMouseMotionListener(this);
  }
  
  public void setTitle(String title,ColorContext context)
  {
    _titleString=title;
    _buffer=null;
		_drawer.setColorContext(context);
    repaint();
  }
  
  public void addPixxMenuBarListener(PixxMenuBarListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removePixxMenuBarListener(PixxMenuBarListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerConnectListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxMenuBarListener lis=(PixxMenuBarListener)e.nextElement();
      lis.connectionClicked(this);
    }
  }
  
  private void triggerChanListListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxMenuBarListener lis=(PixxMenuBarListener)e.nextElement();
      lis.chanListClicked(this);
    }
  }
  
  private void triggerAboutListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxMenuBarListener lis=(PixxMenuBarListener)e.nextElement();
      lis.aboutClicked(this);
    }
  }
  
  private void triggerHelpListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxMenuBarListener lis=(PixxMenuBarListener)e.nextElement();
      lis.helpClicked(this);
    }
  }
  
  private void triggerCloseListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxMenuBarListener lis=(PixxMenuBarListener)e.nextElement();
      lis.closeClicked(this);
    }
  }
  
  public void setConnected(boolean b)
  {
    _connected=b;
    _buffer=null;
    repaint();
  }
  
  public Dimension getPreferredSize()
  {
    if(_title)
      return new Dimension(16,getItemHeight()+getTitleHeight()+4);
    else
      return new Dimension(16,getItemHeight()+4);
  }
  
  private int getClosePositionX()
  {
    int w=getSize().width;
    return w-18;
  }
  
  private int getClosePositionY()
  {
    return getY(0)+1;
  }
  
  private boolean isClosePressed(int x,int y)
  {
    x-=getClosePositionX();
    if(x<0) return false;
    if(x>=16) return false;
    y-=getClosePositionY();
    if(y<0) return false;
    if(y>=16) return false;
    return true;
  }
  
  private int getItemWidth()
  {
    return 100;
  }
  
  private int getItemHeight()
  {
    return 17;
  }
  
  private int getIconWidth()
  {
    return 16;
  }
  
  private int getIconHeight()
  {
    return getItemHeight()-4;
  }
  
  private int getX(int pos)
  {
    return pos*(getItemWidth()+8)+2;
  }
  
  private int getPos(int x)
  {
    return (x-2)/(getItemWidth()+8);
  }
  
  private int getY(int pos)
  {
    if(!_title)
      return 2;
    else
      return 2+getTitleHeight()*0;
  }
  
  private int getTitleY()
  {
   // return 0;
    return getItemHeight()+4;
  }
  
  private int getTitleHeight()
  {
    return 18;
  }
  
  
  private int getIndex(int x)
  {
    int pos=getPos(x);
    if(pos<0) return -1;
    if(pos>4) return -1;
    x-=getX(pos);
    if(x>=getItemWidth()) return -1;
    return pos;		
  }
  
  private int getIndex(int x,int y)
  {
    if(y<getY(0)) return -1;
    y-=getY(0);
    if(y>=getItemHeight()) return -1;
    return getIndex(x);
  }
  
  private void drawTitle(Graphics g,int y)
  {
    int w=getSize().width;
    
    g.setColor(_drawer.getColor(0));
    g.fillRect(0,y,w,getTitleHeight());
    _drawer.draw(_drawer.decodeLine(_titleString),g,5+_titleLeft,y+getTitleHeight()-2,Integer.MAX_VALUE/2,null,0,w-1);
    
    drawSeparator(g,0,y,w,getTitleHeight());
  }
  
  private void drawDisconnectIcon(Graphics g,int x,int y)
  {
    int w=getIconWidth();
    int h=getIconHeight();
    g.setColor(new Color(0xEFEFEF));
    g.fillRect(x,y,w,h);
    
    g.setColor(new Color(0xAFAFAF));
    g.drawLine(x,y+h/2-1,x+5,y+h/2-1);
    g.drawLine(x+w-1,y+h/2-1,x+w-5,y+h/2-1);
    g.setColor(Color.black);
    g.drawLine(x,y+h/2,x+4,y+h/2);
    g.drawLine(x+w-1,y+h/2,x+w-6,y+h/2);
    
    g.drawLine(x+4,y+h/2+1,x+7,y+h/2-2);
    g.drawLine(x+8,y+h/2+1,x+11,y+h/2-2);
  }
  
  private void drawConnectIcon(Graphics g,int x,int y)
  {
    int w=getIconWidth();
    int h=getIconHeight();
    g.setColor(new Color(0xEFEFEF));
    g.fillRect(x,y,w,h);
    
    g.setColor(new Color(0xA2A2A2));		
    g.drawLine(x,y+h/2-1,x+w-1,y+h/2-1);
    g.setColor(Color.black);
    g.drawLine(x,y+h/2,x+w-1,y+h/2);
    
    
    g.setColor(new Color(0x960000));
    g.drawLine(x+2,y+2,x+14,y+2);
    g.drawLine(x+12,y,x+14,y+2);
    g.drawLine(x+12,y+4,x+14,y+2);
    
    g.setColor(new Color(0x2A5B90));
    g.drawLine(x+2,y+9,x+14,y+9);
    g.drawLine(x+2,y+9,x+4,y+7);
    g.drawLine(x+2,y+9,x+4,y+11);				
  }
  
  private void drawChanListIcon(Graphics g,int x,int y)
  {
    int w=getIconWidth();
    int h=getIconHeight();
    g.setColor(new Color(0xEFEFEF));
    g.fillRect(x,y,w,h);
    g.setColor(Color.black);
    x++;
    g.drawLine(x,y+1,x+9,y+1);
    g.drawLine(x,y+3,x+5,y+3);
    g.drawLine(x,y+5,x+12,y+5);
    g.drawLine(x,y+7,x+11,y+7);
    g.drawLine(x,y+9,x+9,y+9);
    g.drawLine(x,y+11,x+13,y+11);
  }
  
  private void drawHelpIcon(Graphics g,int x,int y)
  {
    int w=getIconWidth();
    int h=getIconHeight();
    g.setColor(new Color(0xEFEFEF));
    g.fillRect(x,y,w,h);
    g.setColor(Color.black);
    x+=4;
    y++;
    g.fillRect(x+0,y+0,2,3);
    g.fillRect(x+2,y+0,4,2);
    g.fillRect(x+6,y+0,2,6);
    g.fillRect(x+3,y+4,3,2);
    g.fillRect(x+3,y+6,2,2);
    g.fillRect(x+3,y+9,2,2);
  }
  
  private void drawAboutIcon(Graphics g,int x,int y)
  {
    int w=getIconWidth();
    int h=getIconHeight();
    g.setColor(new Color(0xEFEFEF));
    g.fillRect(x,y,w,h);
    g.setColor(Color.black);
    g.drawLine(x+5,y+4,x+8,y+4);
    g.drawLine(x+5,y+11,x+10,y+11);
    g.fillRect(x+7,y+4,2,7);
    g.fillRect(x+7,y+1,2,2);		
  }
  
  
  private void drawCloseButtonCross(Graphics g,int x,int y)
  {
    int w=13;
    int h=11;
    g.setColor(getColor(COLOR_CLOSE));
    g.fillRect(x,y,w,h);
    g.setColor(getColor(COLOR_BLACK));
    for(int i=0;i<4;i++)
    {
      g.drawLine(x+3+i,y+2+i,x+4+i,y+2+i);
      g.drawLine(x+9-i,y+2+i,x+10-i,y+2+i);
      
      g.drawLine(x+3+i,y+8-i,x+4+i,y+8-i);
      g.drawLine(x+9-i,y+8-i,x+10-i,y+8-i);
    }
  }
  
  private void drawItem(Graphics g,int x,int y,boolean selected,String s)
  {
    int w=getItemWidth();
    int h=getItemHeight();
    int iw=getIconWidth();
    g.setColor(getColor(COLOR_FRONT));
    if(selected) g.setColor(getColor(COLOR_SELECTED));
    g.fillRect(x,y,w,h);
    g.setColor(getColor(COLOR_BLACK));
    g.drawRect(x,y,w-1,h-1);
    g.setColor(getColor(COLOR_WHITE));
    g.drawRect(x+1,y+1,w-3,h-3);
    g.drawLine(x+iw+2,y+1,x+iw+2,y+h-2);
    int sw=g.getFontMetrics().stringWidth(s);
    w-=(5+iw);
    g.drawString(s,x+iw+3+(w-sw)/2,y+h-4);
  }
  
  private void drawDisconnectItem(Graphics g,int x,int y,boolean pressed)
  {
    drawItem(g,x,y,pressed,getText(TextProvider.GUI_DISCONNECT));
    drawDisconnectIcon(g,x+2,y+2);
  }
  
  private void drawConnectItem(Graphics g,int x,int y,boolean pressed)
  {
    drawItem(g,x,y,pressed,getText(TextProvider.GUI_CONNECT));
    drawConnectIcon(g,x+2,y+2);
  }
  
  private void drawChanListItem(Graphics g,int x,int y,boolean pressed)
  {
    drawItem(g,x,y,pressed,getText(TextProvider.GUI_CHANNELS));
    drawChanListIcon(g,x+2,y+2);
  }
  
  private void drawAboutItem(Graphics g,int x,int y,boolean pressed)
  {
    drawItem(g,x,y,pressed,getText(TextProvider.GUI_ABOUT));
    drawAboutIcon(g,x+2,y+2);
  }
  
  private void drawHelpItem(Graphics g,int x,int y,boolean pressed)
  {
    drawItem(g,x,y,pressed,getText(TextProvider.GUI_HELP));
    drawHelpIcon(g,x+2,y+2);
  }
  
  private void drawCloseButtonItem(Graphics g,int x,int y,boolean pressed)
  {
    int w=16;
    int h=16;
    if(!pressed)
    {
  //    g.setColor(Color.white);
      g.setColor(getColor(COLOR_WHITE));
      g.drawLine(x+0,y+1,x+w-2,y+1);
      g.drawLine(x+0,y+1,x+0,y+h-2);
     //.setColor(Color.black);
      g.setColor(getColor(COLOR_BLACK));
      g.drawLine(x+w-1,y+h-2,x+w-1,y+1);
      g.drawLine(x+w-1,y+h-2,x+0,y+h-2);
 //     g.setColor(new Color(0x868686));
      g.setColor(getColor(COLOR_DARK_GRAY));
      g.drawLine(x+w-2,y+h-3,x+w-2,y+2);
      g.drawLine(x+w-2,y+h-3,x+1,y+h-3);
      drawCloseButtonCross(g,x+1,y+2);
    }
    else
    {
   //   g.setColor(Color.black);
      g.setColor(getColor(COLOR_BLACK));
      g.drawLine(x+0,y+1,x+w-2,y+1);
      g.drawLine(x+0,y+1,x+0,y+h-2);
   //   g.setColor(Color.white);
      g.setColor(getColor(COLOR_WHITE));
      g.drawLine(x+w-1,y+h-2,x+w-1,y+1);
      g.drawLine(x+w-1,y+h-2,x+0,y+h-2);
      g.setColor(getColor(COLOR_DARK_GRAY));
//      g.setColor(new Color(0x868686));
      g.drawLine(x+1,y+2,x+1,y+h-3);
      g.drawLine(x+1,y+2,x+w-2,y+2);
      
      drawCloseButtonCross(g,x+2,y+3);
    }
  }
  
  public void paint(Graphics g)
  {
    update(g);
  }
  
  public void update(Graphics ug)
  {
    int w=getSize().width;
    int h=getSize().height;
    
    if(_buffer!=null)
    {
      if((_buffer.getWidth(this)!=w) || (_buffer.getHeight(this)!=h)) _buffer=null;
    }
    
    if(_buffer==null)
    {
      Graphics g;
      try
      {
        _buffer=createImage(w,h);
        g=_buffer.getGraphics();
      }
      catch(Throwable e)
      {
        return;
      }
      
      
   //   g.setColor(new Color(0x084079));
      g.setColor(getColor(COLOR_BACK));
      g.fillRect(0,0,w,h);			
      
      //drawSeparator(g,0,0,w,getItemHeight()+4);
      
			if(_ircConfiguration.getShowConnect())
			{
        if(!_connected)
          drawConnectItem(g,getX(_connectIndex),getY(0),_pressedIndex==_connectIndex);
        else
          drawDisconnectItem(g,getX(_connectIndex),getY(0),_pressedIndex==_connectIndex);
			}
      if(_ircConfiguration.getShowChanlist()) drawChanListItem(g,getX(_chanlistIndex),getY(0),_pressedIndex==_chanlistIndex);
      if(_ircConfiguration.getShowAbout()) drawAboutItem(g,getX(_aboutIndex),getY(0),_pressedIndex==_aboutIndex);
      if(_ircConfiguration.getShowHelp()) drawHelpItem(g,getX(_helpIndex),getY(0),_pressedIndex==_helpIndex);
      
      drawCloseButtonItem(g,getClosePositionX(),getClosePositionY(),_closePressed);
      
      if(_title)
        drawTitle(g,getTitleY());
    }
    
    if(_buffer!=null) ug.drawImage(_buffer,0,0,this);		
    
  }
  
  public void mouseClicked(MouseEvent e)
  {
  }
  
  public void mouseEntered(MouseEvent e)
  {
  }
  
  public void mouseExited(MouseEvent e)
  {
  }
  
  
  public void mousePressed(MouseEvent e)
  {
    _pressedIndex=getIndex(e.getX(),e.getY());
    _closePressed=isClosePressed(e.getX(),e.getY());
    _buffer=null;
	  if(_title && (e.getY()>=getTitleY()))
		{
		  _mouseDownX=e.getX();
			_mouseScroll=true;
		}
    repaint();
  }
  
  public void mouseReleased(MouseEvent e)
  {
	  _mouseScroll=false;
    int index=getIndex(e.getX(),e.getY());
    boolean close=isClosePressed(e.getX(),e.getY());
    if(index==_connectIndex) if(_ircConfiguration.getShowConnect()) triggerConnectListeners();
    if(index==_chanlistIndex) if(_ircConfiguration.getShowChanlist()) triggerChanListListeners();
    if(index==_aboutIndex) if(_ircConfiguration.getShowAbout()) triggerAboutListeners();
    if(index==_helpIndex) if(_ircConfiguration.getShowHelp()) triggerHelpListeners();
    if(close) triggerCloseListeners();
    _closePressed=false;
    _pressedIndex=-1;
    _buffer=null;
    repaint();
  }
	
	public void mouseMoved(MouseEvent e)
	{
	  if(_title && (e.getY()>=getTitleY()))
		{
      if(!getCursor().equals(new Cursor(Cursor.E_RESIZE_CURSOR)))
        setCursor(new Cursor(Cursor.E_RESIZE_CURSOR));
    }
		else
		{
      if(!getCursor().equals(new Cursor(Cursor.DEFAULT_CURSOR)))
        setCursor(new Cursor(Cursor.DEFAULT_CURSOR));
		}
	}
	
	public void mouseDragged(MouseEvent e)
	{
	  if(!_mouseScroll) return;
    int deltaX=_mouseDownX-e.getX();
    _titleLeft-=deltaX;
		if(_titleLeft>0) _titleLeft=0;
		_mouseDownX=e.getX();
    _buffer=null;
    repaint();
	}
  
}

