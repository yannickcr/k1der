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

package irc.style;

import java.util.*;
import java.awt.*;
import java.awt.event.*;
import irc.*;

public class StyledList extends Panel implements MouseListener,MouseMotionListener
{
  private Vector _list;
  private Vector _heights;
  private boolean _wrap;
  private int _last;
  private int _left;
  private int _width;
  private int _toScrollX;
  private int _toScrollY;
  private FormattedStringDrawer _drawer;
  private Image _buffer;
  private int _bufferWidth;
  private int _bufferHeight;
  private Image _tmp;
  private Image _tmpBack;
  private FontMetrics _fm;
  private Font _fonts;
  private Hashtable _ritems;	
  private Vector _addedRitems;
  private MultipleWordCatcher _catcher;
  private WordListRecognizer _wordListRecognizer;
  private IRCConfiguration _ircConfiguration;
  
  private Hashtable _listeners;
  
  public StyledList(IRCConfiguration config,Font fnt,ColorContext context)
  {
    this(config,fnt,true,context);
  }
  
  public StyledList(IRCConfiguration config,Font fnt,boolean wrap,ColorContext context)
  {
    super();
    _ircConfiguration=config;
    _toScrollX=0;
    _toScrollY=0;
    _left=0;
    _fonts=fnt;
    _fm=null;
    _wrap=wrap;
    _buffer=null;
    _drawer=new FormattedStringDrawer(_fonts,_ircConfiguration,context);
    _catcher=new MultipleWordCatcher();
    _wordListRecognizer=new WordListRecognizer();
    _catcher.addRecognizer(new ChannelRecognizer());
    _catcher.addRecognizer(new URLRecognizer());
    _catcher.addRecognizer(_wordListRecognizer);
    _ritems=new Hashtable();
    _listeners=new Hashtable();
    addMouseListener(this);
    addMouseMotionListener(this);
    clear();
  }
	
  public synchronized void setNickList(String[] list)
  {
    _wordListRecognizer.setList(list);
  }
  
  public synchronized void addStyledListListener(StyledListListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public synchronized void removeStyledListListener(StyledListListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerChannelEvent(String chan,MouseEvent ev)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      StyledListListener lis=(StyledListListener)e.nextElement();
      lis.channelEvent(this,chan,ev);		
    }	
  }
  
  private void triggerURLEvent(String url,MouseEvent ev)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      StyledListListener lis=(StyledListListener)e.nextElement();
      lis.URLEvent(this,url,ev);		
    }	
  }
  
  private void triggerNickEvent(String nick,MouseEvent ev)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      StyledListListener lis=(StyledListListener)e.nextElement();
      lis.nickEvent(this,nick,ev);		
    }	
  }
  
  public synchronized void setLeft(int left)
  {
	  int w=getSize().width;
    int oldLeft=_left;
    _left=left;
    if(_left<0) _left=0;
    if(_left>=getLogicalWidth()) _left=getLogicalWidth()-1;
    if(_left!=oldLeft)
    {
      addToScroll(_left-oldLeft,0);
      repaint();
    }	
  }
  
  public int getLeft()
  {
    return _left;
  }
  
	public synchronized void setFirst(int first)
	{
	  if(_fm==null) return;
		int y=0;
	  int height=getSize().height;

    int i=first;
		while(y<height)
		{
		  if(i>=getLineCount()) break;
		  String str=(String)_list.elementAt(i);
			int h=_drawer.getHeight(_drawer.getStripped(str),_fm);
			y+=h;
			i++;
		}
		i-=2;
		if(i<0) i=0;
		if(i>=getLineCount()) i=getLineCount()-1;
		setLast(i);
	}
	
  public synchronized void setLast(int last)
  {
    int oldLast=_last;
    _last=last;
    if(_last<0) _last=0;
    if(_last>=_list.size()) _last=_list.size()-1;
    if(_last!=oldLast)
    {
      addToScroll(0,_last-oldLast);
      repaint();
    }
  }
  
	public int getLogicalWidth()
	{
	  return _width;
	}
	
  public int getLast()
  {
    return _last;
  }
  
  public synchronized int getLineCount()
  {
    return _list.size();
  }
  
  public synchronized void addLine(String line)
  {
    line=_drawer.decodeLine(line);
    _list.insertElementAt(line,_list.size());		
    _heights.insertElementAt(new Integer(0),_heights.size());
    
    
    if(!_wrap)
    {
      String str=_drawer.getStripped(line);
      int w=str.length()*_fonts.getSize();
      if(_fm!=null) w=_fm.stringWidth(str);
      if(w>_width)
      {
        _width=w;
        reinit();
        repaint();
      }
    }
    
    if(_last==_list.size()-2) setLast(_last+1);		
  }
  
  private void reinit()
  {
    _buffer=null;
    _ritems=new Hashtable();
  }
  
  public synchronized void clear()
  {
    _list=new Vector();
    _heights=new Vector();
    _last=_list.size()-1;
    setLeft(0);
		_width=getSize().width;
    reinit();
    repaint();
  }
  
  public void paint(Graphics g)
  {
    update(g);
  }
  
  private int draw(Graphics g,int from,int to,int y,int debx,int finx)
  {
    int w=getSize().width;
    int h=getSize().height;
    Image back=_tmpBack;
    Graphics gra=back.getGraphics();
    gra.setColor(_drawer.getColor(0));
    gra.fillRect(0,0,w,h);
    int index=to;
    int wrapPos=w;
    if(!_wrap) wrapPos=Integer.MAX_VALUE;
    _addedRitems=new Vector();
    
    while((index>=from) && (y>=0))
    {
      String str=(String)_list.elementAt(index);
      DrawResult res=_drawer.draw(str,gra,-_left,y,wrapPos,_catcher,debx,finx);
      Dimension d=res.dimension;
      for(int i=0;i<res.items.length;i++) _addedRitems.insertElementAt(res.items[i],_addedRitems.size());
      _heights.setElementAt(new Integer(d.height),index);
      y-=d.height;
      index--;
    }
    _fm=gra.getFontMetrics();
    g.drawImage(back,0,0,this);
    return y;
  }
  
  private void addToScroll(int vx,int vy)
  {
    _toScrollX+=vx;
    _toScrollY+=vy;
  }
  
  private int getScrollX()
  {
    int res=_toScrollX;
    _toScrollX=0;
    return res;
  }
  
  private int getScrollY()
  {
    int res=_toScrollY;
    _toScrollY=0;
    return res;
  }
	
  private void swap()
  {
    Image temp=_tmp;
    _tmp=_buffer;
    _buffer=temp;
  }
  
  private void scrollDrawItems(int dx,int dy)
  {
    int h=getSize().height;
    Hashtable nh=new Hashtable();
    Enumeration e=_ritems.elements();
    while(e.hasMoreElements())
    {
      DrawResultItem item=(DrawResultItem)e.nextElement();
      item.rectangle.x+=dx;
      item.rectangle.y+=dy;
      if((item.rectangle.y+item.rectangle.height>=0) && (item.rectangle.y<h))
      {
        nh.put(item,item);
      }		
    }
    _ritems=nh;
  }
  
  private void combineItems()
  {
    for(int i=0;i<_addedRitems.size();i++)
    {
      DrawResultItem item=(DrawResultItem)_addedRitems.elementAt(i);
      _ritems.put(item,item);
    }
  }
  
  private DrawResultItem findItem(int x,int y)
  {
    Enumeration e=_ritems.elements();
    while(e.hasMoreElements())
    {
      DrawResultItem item=(DrawResultItem)e.nextElement();
      if(item.rectangle.contains(x,y)) return item;		
    }
    return null;
  }
  
  public synchronized void update(Graphics g)
  {
    int w=getSize().width;
    int h=getSize().height;
    if(_buffer!=null)
    {
      if((_bufferWidth!=w) || (_bufferHeight!=h)) reinit();
    }
    
    int scrx=getScrollX();
    int scry=getScrollY();
		
    if(_buffer==null)
    {
      try
      {
        _buffer=createImage(w,h);	
        _tmp=createImage(w,h);
        _tmpBack=createImage(w,h);
        _bufferWidth=w;
        _bufferHeight=h;				
        draw(_buffer.getGraphics(),0,_last,h,0,w-1);
        combineItems();
      }
      catch(Throwable e)
      {
        repaint();
        return;
      }
      scrx=0;
      scry=0;
    }
    
		
    if(scrx!=0)
    { 
      if(_tmp==null)
      {
        repaint();
        return;
      }
      Graphics gra=_tmp.getGraphics();
      gra.setColor(_drawer.getColor(0));
      gra.fillRect(0,0,w,h);
			
			if(scrx<0)
			{
        draw(gra,0,_last,h,0,-scrx);
			}
			else
			{
			  draw(gra,0,_last,h,w-scrx,w-1);
			}
			
      scrollDrawItems(-scrx,0);
      combineItems();
			
      gra.drawImage(_buffer,-scrx,0,this);
      swap();
    }
		
    if(scry>0)
    { 
      if(_tmp==null)
      {
        repaint();
        return;
      }
      Graphics gra=_tmp.getGraphics();
      gra.setColor(_drawer.getColor(0));
      gra.fillRect(0,0,w,h);
      int baseY=h-draw(gra,_last-scry+1,_last,h,0,w-1);
      scrollDrawItems(0,-baseY);
      combineItems();
      gra.drawImage(_buffer,0,-baseY,this);
      swap();
    }
    else if(scry<0)
    {
      //il s'agit de descendre les lignes de _last+1 à _last-toScroll
      int baseY=0;
      for(int i=_last+1;i<=_last-scry;i++)
      {
        baseY+=((Integer)_heights.elementAt(i)).intValue();
      }
      scrollDrawItems(0,baseY);
      if(_tmp==null)
      {
        repaint();
        return;				
      }
      Graphics gra=_tmp.getGraphics();
      gra.setColor(_drawer.getColor(0));
      gra.fillRect(0,0,w,h);
      
      int first=_last;
      int posY=h;
      while((posY>baseY) && (first>0)) posY-=((Integer)_heights.elementAt(first--)).intValue();
      posY+=((Integer)_heights.elementAt(++first)).intValue();
      draw(gra,0,first,posY,0,w-1);
      combineItems();
      gra.drawImage(_buffer,0,baseY,this);
      swap();
    }
    
    if(_buffer!=null)
    {
      g.drawImage(_buffer,0,0,this);
    }
    else
    {
      repaint();
    }
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
  
  public synchronized void mousePressed(MouseEvent e)
  {
    DrawResultItem item=findItem(e.getX(),e.getY());
    if(item!=null)
    {
      if(item.type.equals("channel"))
      {
        triggerChannelEvent(item.word,e);
      }
      else if(item.type.equals("url"))
      {
        triggerURLEvent(item.word,e);			
      }
      else if(item.type.equals("wordlist"))
      {
        triggerNickEvent(item.word,e);
      }
    }
  }
  
  public void mouseReleased(MouseEvent e)
  {
  }
  
  public void mouseDragged(MouseEvent e)
  {
  }
  
  public synchronized void mouseMoved(MouseEvent e)
  {
    DrawResultItem item=findItem(e.getX(),e.getY());
    if(item!=null)
    {
      if(!getCursor().equals(new Cursor(Cursor.HAND_CURSOR)))
        setCursor(new Cursor(Cursor.HAND_CURSOR));
    }
    else
    {
      if(!getCursor().equals(new Cursor(Cursor.DEFAULT_CURSOR)))
        setCursor(new Cursor(Cursor.DEFAULT_CURSOR));
    }
    
  } 
}

