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
import irc.*;

public class PixxNickList extends PixxPanel implements MouseListener,MouseMotionListener
{
  private Vector _nicks;
  private Image _buffer;
  private Font _font;
  private int _base;
  private int _selected;
	private int _overindex;
  private Hashtable _listeners;
	
  public PixxNickList(IRCConfiguration config)
  {
    super(config);
    _selected=-1;
		_overindex=-1;
    _listeners=new Hashtable();
    addMouseListener(this);
    addMouseMotionListener(this);
    _base=0;
    _nicks=new Vector();
    _font=new Font("",0,12);
  }
  
  public void addPixxNickListListener(PixxNickListListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removePixxNickListListener(PixxNickListListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerListeners(String nick,MouseEvent ev)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxNickListListener lis=(PixxNickListListener)e.nextElement();
      lis.eventOccured(nick,ev);
    }
  }

  private void triggerASLListeners(String nick,String info)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxNickListListener lis=(PixxNickListListener)e.nextElement();
      lis.ASLEventOccured(nick,info);
    }
  }
  
  public Dimension getPreferredSize()
  {
    return new Dimension(_ircConfiguration.getNickListWidth(),16);
  }
  
  public void setBase(int b)
  {
    _base=b;
    _buffer=null;
    repaint();		
  }
  
  public int getBase()
  {
    return _base;
  }
  
  public int getNickCount()
  {
    return _nicks.size();
  }
  
  public void add(String nick)
  {
    _nicks.insertElementAt(nick,_nicks.size());
    _buffer=null;
    repaint();
  }
  
  public void remove(String nick)
  {
    for(int i=0;i<_nicks.size();i++)
    {
      String s=(String)_nicks.elementAt(i);
      if(s.equals(nick))
      {
        _nicks.removeElementAt(i);
        break;
      }
    }
    _buffer=null;
    repaint();
  }
  
  public void set(String[] nicks)
  {
    _nicks=new Vector();
    for(int i=0;i<nicks.length;i++) _nicks.insertElementAt(nicks[i],_nicks.size());
    _buffer=null;
    repaint();
  }
  
  public void removeAll()
  {
    _nicks=new Vector();
    _buffer=null;
    repaint();
  }
  
  public void paint(Graphics g)
  {
    update(g);
  }
  
	private Color findColor(String info)
	{
	  int pos=info.indexOf(" ");
		if(pos==-1) return getColor(COLOR_UNDEF);
		info=info.substring(pos).trim();
		pos=info.indexOf(" ");
		if(pos==-1) return getColor(COLOR_UNDEF);
		info=info.substring(0,pos).trim().toLowerCase();
		if(info.equals(_ircConfiguration.getASLMale())) return getColor(COLOR_MALE);
		if(info.equals(_ircConfiguration.getASLFemale())) return getColor(COLOR_FEMEALE);
		return getColor(COLOR_UNDEF);
	}
	
  public void update(Graphics g)
  {
    int w=getSize().width;
    int h=getSize().height;
    
    if(_buffer!=null)
    {
      if((_buffer.getWidth(this)!=w) || (_buffer.getHeight(this)!=h)) _buffer=null;
    }
    
    if(_buffer==null)
    {
      Graphics gra;
      try
      {
        _buffer=createImage(w,h);
        gra=_buffer.getGraphics();
      }
      catch(Throwable e)
      {
        return;
      }
      
      
			//   gra.setColor(new Color(0x084079));
      gra.setColor(getColor(COLOR_BACK));
      gra.fillRect(0,0,w,h);
			//   gra.setColor(Color.black);
      gra.setColor(getColor(COLOR_BLACK));
      gra.drawRect(0,0,w,h);
      gra.setFont(_font);
      
      int y=8;
      int fh=_font.getSize();
      FontMetrics fm=gra.getFontMetrics();
      
      int i=_base;
      while((i<_nicks.size()) && (y<h))
      {
        String nick=(String)_nicks.elementAt(i);
				String info="";
				int pos=nick.indexOf(":");
        Color back=getColor(COLOR_FRONT);
				if(pos!=-1)
				{
				  info=nick.substring(pos+1);
					nick=nick.substring(0,pos);
					back=findColor(info);
				}
        if(_selected==i) back=getColor(COLOR_SELECTED);
        int type=0;
        if(nick.startsWith("@")) type=1;
        if(nick.startsWith("+")) type=2;
        if(nick.startsWith("%")) type=3;
        if(type>0) nick=nick.substring(1);
        
        int sw=fm.stringWidth(nick);
        
        gra.setColor(back);
        gra.fillRect(20,y-1,w-28,fh+2);
				//    gra.setColor(Color.white);				
        gra.setColor(getColor(COLOR_WHITE));
        gra.drawRect(20,y-1,w-28,fh+2);
        
				//    gra.setColor(Color.white);
        gra.setColor(getColor(COLOR_WHITE));
        Shape sh=gra.getClip();
        gra.setClip(20,y-1,w-28,fh+2);
        int px=w-sw-12;
        if(px<22) px=22;
        gra.drawString(nick,px,y+fh-1);
        gra.setClip(sh);
        
        if(type>0)
        {
          if(type==1)
            gra.setColor(getColor(COLOR_OP));
          else if(type==2)
            gra.setColor(getColor(COLOR_VOICE));
					else
					  gra.setColor(getColor(COLOR_SEMIOP));
          gra.fillRect(20-fh-6,y-1,fh+2,fh+2);
          gra.setColor(getColor(COLOR_WHITE));
          gra.drawRect(20-fh-6,y-1,fh+2,fh+2);
					String str="";
          if(type==1) str="@";
          else if(type==2) str="+";
					else if(type==3) str="%";
          
					//          gra.setColor(Color.white);
          gra.setColor(getColor(COLOR_WHITE));
          sw=fm.stringWidth(str);
          if(type==2)
          {
            int x=20-fh-6+(fh+2-sw)/2+1;
            gra.drawString(str,x,y+fh-1);
          }
          else if(type==1)
          {
            int x=20-fh-6+(fh+2-sw)/2;
            gra.drawString(str,x,y+fh-2);					
          }
					else if(type==3)
					{
            int x=20-fh-6+(fh+2-sw)/2+1;
            gra.drawString(str,x,y+fh-1);
					}
        }
        
        y+=fh+6;
        i++;
      }
    }
    
    if(_buffer!=null) g.drawImage(_buffer,0,0,this);
  }
	
  private int getIndex(int x,int y)
  {
    y-=8;
    int fh=_font.getSize();
    y/=fh+6;
    y+=_base;
    if(y<0) y=-1;
    if(y>=_nicks.size()) y=-1;
    return y;
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
    int index=getIndex(e.getX(),e.getY());
    _selected=index;
    _buffer=null;
    repaint();
    if(_selected!=-1)
    {
      String nick=(String)_nicks.elementAt(_selected);
      if(nick.startsWith("@") || nick.startsWith("+") || nick.startsWith("%")) nick=nick.substring(1);
		  int pos=nick.indexOf(":");
			if(pos!=-1) nick=nick.substring(0,pos);
      triggerListeners(nick,e);
    }
  }
  
  public void mouseReleased(MouseEvent e)
  {
  }
	
  public void mouseDragged(MouseEvent e)
  {
  }
	
  public void mouseMoved(MouseEvent e)
  {
	  if(!_ircConfiguration.getASL()) return;
    int index=getIndex(e.getX(),e.getY());
		if(index==_overindex) return;
		_overindex=index;
    if(index!=-1)
		{
			String nick=(String)_nicks.elementAt(index);
			if(nick.startsWith("@") || nick.startsWith("+") || nick.startsWith("%")) nick=nick.substring(1);
			int pos=nick.indexOf(":");
			String info="";
			if(pos!=-1)
			{
				info=nick.substring(pos+1);
				nick=nick.substring(0,pos);
			}
			triggerASLListeners(nick,info);
			//System.out.println(nick+" is "+info);
			//triggerListeners(nick,e);
		}
  }
}

