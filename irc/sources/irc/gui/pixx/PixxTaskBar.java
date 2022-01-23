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

import irc.*;
import irc.gui.*;

import java.util.*;
import java.awt.*;
import java.awt.event.*;

class TaskBarItem
{
  public TaskBarItem(AWTSource src,int row,boolean bring)
  {
    source=src;
    eventWaiting=false;
    this.row=row;
    this.bring=bring;
  }
  
  public AWTSource source;
  public int row;
  public boolean eventWaiting;
  public boolean bring;
	public int zorder;
}

public class PixxTaskBar extends PixxPanel implements MouseListener,AWTSourceListener
{
  private Hashtable _listeners;
  private TaskBarItem _active;
  private TaskBarItem _pressed;
  private Vector[] _itemsPos;
  private int[] _itemCount;
  private Vector _items;
  private Hashtable _pendingItems;
  private Font _font;
  private Image _buffer;
  private int _iwidth;
  private int _ileft;
	private int _zorder;
  
  public PixxTaskBar(IRCConfiguration config)
  {
    super(config);
    _font=new Font("",0,12);
    _listeners=new Hashtable();
    _pendingItems=new Hashtable();
    _active=null;
    _pressed=null;
    _items=new Vector();
    _itemCount=new int[2];
    _itemCount[0]=0;
    _itemCount[1]=0;
    _itemsPos=new Vector[2];	
    _itemsPos[0]=new Vector();
    _itemsPos[1]=new Vector();
    _ileft=60;
		_zorder=0;
    computeWidth();
    
    addMouseListener(this);
  }
  
  public void addPixxTaskBarListener(PixxTaskBarListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removePixxTaskBarListener(PixxTaskBarListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerActivatedListeners(AWTSource source)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxTaskBarListener lis=(PixxTaskBarListener)e.nextElement();
      lis.AWTSourceActivated(this,source);
    }
  }
  
  private void triggerDesactivatedListeners(AWTSource source)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxTaskBarListener lis=(PixxTaskBarListener)e.nextElement();
      lis.AWTSourceDesactivated(this,source);
    }	
  }
  
  private void triggerAddedListeners(AWTSource source)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxTaskBarListener lis=(PixxTaskBarListener)e.nextElement();
      lis.AWTSourceAdded(this,source);
    }	
  }
  
  private void triggerRemovedListeners(AWTSource source)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxTaskBarListener lis=(PixxTaskBarListener)e.nextElement();
      lis.AWTSourceRemoved(this,source);
    }	
  }
  
  private void triggerEventListeners(AWTSource source,MouseEvent me)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxTaskBarListener lis=(PixxTaskBarListener)e.nextElement();
      lis.eventOccured(this,source,me);
    }	
  }
  
  private TaskBarItem findItem(AWTSource source)
  {
    if(source==null) return null;
    for(int i=0;i<_items.size();i++) if(((TaskBarItem)_items.elementAt(i)).source==source) return (TaskBarItem)_items.elementAt(i);
    return null;
  }
  
  private void removeFromVector(Vector v,Object o)
  {
    for(int i=0;i<v.size();i++) if(v.elementAt(i)==o) v.removeElementAt(i);
  }
  
  private synchronized void enter(AWTSource source,int row,boolean bring)
  {
    TaskBarItem item=new TaskBarItem(source,row,bring);
    source.addAWTSourceListener(this);
    if(source.isActive())
    {		
      _items.insertElementAt(item,_items.size());
      _itemCount[row]++;
      triggerAddedListeners(source);
      if(bring) activate(source);
      _buffer=null;
      repaint();
    }
    else
    {
      _pendingItems.put(source,item);
    }
  }
  
  private synchronized void leave(AWTSource source,int row)
  {
    TaskBarItem item=findItem(source);
    _itemCount[row]--;
		boolean change=getActive()==source;
    source.removeAWTSourceListener(this);
    removeFromVector(_items,item);
    triggerRemovedListeners(source);
    if(change) activate(null);		
    _buffer=null;
    repaint();
  }
  
  public int getCount()
  {
    return _itemCount[0]+_itemCount[1];
  }
  
  
  public void addChannel(AWTChannel chan,boolean bring)
  {
    enter(chan,0,bring);
  }
  
  public void removeChannel(AWTChannel chan)
  {
    leave(chan,0);
  }
  
  public void addStatus(AWTStatus status,boolean bring)
  {
    enter(status,1,bring);
  }
  
  public void removeStatus(AWTStatus status)
  {
    leave(status,1);
  }
  
  public void addQuery(AWTQuery query,boolean bring)
  {
    enter(query,1,bring);
  }
  
  public void removeQuery(AWTQuery query)
  {
    leave(query,1);
  }
  
  public void addDCCChat(AWTDCCChat chat,boolean bring)
  {
    enter(chat,1,bring);
  }
  
  public void removeDCCChat(AWTDCCChat chat)
  {
    leave(chat,1);
  }
  
  private AWTSource findFirst()
  {
	  TaskBarItem first=null;
		int maxz=-1;
		for(int i=0;i<_items.size();i++)
		{
		  TaskBarItem item=(TaskBarItem)_items.elementAt(i);
			if(item.zorder>maxz)
			{
			  maxz=item.zorder;
				first=item;
			}
		}
		if(first==null) return null;
		return first.source;
  }
  
  public void activate(AWTSource source)
  {
    if(source==null) source=findFirst();
    TaskBarItem item=findItem(source);
    if(item==_active) return;
    if(_active!=null) triggerDesactivatedListeners(_active.source);
    _active=item;
    if(_active!=null)
    {
		  _active.zorder=_zorder++;
      _active.eventWaiting=false;
      triggerActivatedListeners(_active.source);
    }
    else
    {
      triggerActivatedListeners(null);
    }
  }
  
  public AWTSource getActive()
  {
    if(_active==null) return null;
    return _active.source;
  }
  
  public void paint(Graphics g)
  {
    update(g);
  }
  
  public Dimension getPreferredSize()
  {
    return new Dimension(16,2*getItemHeight()+16);
  }
  
  private int getItemWidth()
  {
    return _iwidth;
  //  return 100;
  }
  
  private int getItemHeight()
  {
    return _font.getSize()+4;
  }
  
  private synchronized void computeWidth()
  {
    int w=getSize().width-63;
    int n=Math.max(_itemCount[0],_itemCount[1]);
    w-=9*n;
    if(n!=0)
      _iwidth=Math.min(100,w/n);
    else
      _iwidth=100;
  }
  
  private int getX(int col)
  {
    return col*(getItemWidth()+9)+_ileft;
  }
  
  private int getCol(int x)
  {
    return (x-_ileft)/(9+getItemWidth());
  }
  
  private int getY(int row)
  {
    return 4+(getItemHeight()+8)*row;	
  }
  
  private int getRow(int y)
  {
    return (y-4)/(8+getItemHeight());;
  }
  
  
  
  private void drawItem(Graphics g,int col,int row,Color c,String s)
  {
    int x=getX(col);
    int y=getY(row);
    int w=getItemWidth();
    int h=getItemHeight();
    Shape sh=g.getClip();
    g.setClip(x+1,y+1,w-1,h-1);
    g.setColor(c);
    g.fillRect(x,y,w,h);
  //  g.setColor(Color.black);
    g.setColor(getColor(COLOR_BLACK));
    g.drawRect(x,y,w,h);
 //   g.setColor(Color.white);
    g.setColor(getColor(COLOR_WHITE));
    g.drawRect(x+1,y+1,w-2,h-2);
    y+=h;
    int strw=g.getFontMetrics().stringWidth(s);
    y-=(h-_font.getSize())/2;
    g.drawString(s,x+(w-strw)/2,y-1);
    g.setClip(sh);
  }
  
  private void drawItem(Graphics g,TaskBarItem item,int col)
  {
    int row=item.row;
    _itemsPos[row].insertElementAt(item,col);
    Color c=getColor(COLOR_FRONT);
    if((item==_active) || (item==_pressed)) c=getColor(COLOR_SELECTED);
    if((item!=_active) && (item.eventWaiting)) c=getColor(COLOR_EVENT);
    drawItem(g,col++,row,c,item.source.getShortTitle());
  }
  
  public void update(Graphics ug)
  {
    int col=0;
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
      
      int sw=Math.max(g.getFontMetrics().stringWidth(getText(TextProvider.GUI_PRIVATE)),g.getFontMetrics().stringWidth(getText(TextProvider.GUI_PUBLIC)));
      _ileft=25+sw;
      computeWidth();
      
      _itemsPos[0]=new Vector();
      _itemsPos[1]=new Vector();
      
   //   g.setColor(new Color(0x084079));
      g.setColor(getColor(COLOR_BACK));
      g.fillRect(0,0,w,h);
      int col0=0;
      int col1=0;
      Enumeration el=_items.elements();
      while(el.hasMoreElements())
      {
        TaskBarItem item=(TaskBarItem)el.nextElement();
        if(item.row==0)
          drawItem(g,item,col0++);
        else
          drawItem(g,item,col1++);
        
      }
      
   //   g.setColor(new Color(0x336699));
      g.setColor(getColor(COLOR_FRONT));
      g.fillRect(4,5,sw+2,h-9);
      
      for(int y=3;y<h/4;y++)
      {
        g.drawLine(sw+3+y,y+3,sw+3+y,h/2+2-y);
        g.drawLine(sw+3+y,h-3-y,sw+3+y,h/2-1+y);
      }
      
      
   //   g.setColor(Color.black);
      g.setColor(getColor(COLOR_BLACK));
      g.drawLine(4,h/2-1,w-1,h/2-1);
      g.drawLine(4,h/2+1,w-1,h/2+1);
//      g.setColor(Color.white);
      g.setColor(getColor(COLOR_WHITE));
      g.drawLine(4,h/2,w-1,h/2);
      
      int y=getY(0)+getItemHeight();
      y-=(getItemHeight()-_font.getSize())/2;
      
      g.drawString(getText(TextProvider.GUI_PUBLIC),8,y+1);
      
      y=getY(1)+getItemHeight();
      y-=(getItemHeight()-_font.getSize())/2;
      
      g.drawString(getText(TextProvider.GUI_PRIVATE),8,y-4);
      
   //   g.setColor(Color.black);
      g.setColor(getColor(COLOR_BLACK));
      g.drawLine(4,5,4,h-5);
      
      g.drawLine(4,5,sw+6,5);
      g.drawLine(4,h-5,sw+6,h-5);
      
      g.drawLine(sw+6,5,sw+3+h/4,h/4+2);
      g.drawLine(sw+3+h/4,h/4+2,sw+6,h/2-1);
      
      g.drawLine(sw+6,h-5,sw+3+h/4,h-1-h/4-1);
      g.drawLine(sw+3+h/4,h-h/4-2,sw+6,h-3-h/2+4);
      
      
   //   g.setColor(Color.white);
      g.setColor(getColor(COLOR_WHITE));
      g.drawLine(5,6,5,h-6);
      
      g.drawLine(5,6,sw+5,6);
      g.drawLine(5,h-6,sw+5,h-6);
      
      g.drawLine(sw+6,6,sw+2+h/4,h/4+2);
      g.drawLine(sw+2+h/4,h/4+2,sw+5,h/2-1);
      
      g.drawLine(sw+6,h-6,sw+2+h/4,h-1-h/4-1);
      g.drawLine(sw+2+h/4,h-2-h/4,sw+5,h-1-h/2+2);
      
    }
    
    if(_buffer!=null) ug.drawImage(_buffer,0,0,this);		
  }
  
  private TaskBarItem getItemAt(int x,int y)
  {
    int row=getRow(y);
    int col=getCol(x);
    if((row<0) || (row>1)) return null;
    if((col<0) || (col>=_itemsPos[row].size())) return null;
    x-=getX(col);
    y-=getY(row);
    if((x>=getItemWidth()) || (y>=getItemHeight())) return null;
    if((x<0) || (y<0)) return null;
    
    return (TaskBarItem)_itemsPos[row].elementAt(col);
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
    TaskBarItem np=getItemAt(e.getX(),e.getY());
    _pressed=np;
    _buffer=null;
    repaint();
    if(_pressed!=null) triggerEventListeners(_pressed.source,e);
  }
  
  public void mouseReleased(MouseEvent e) 
  {
    _pressed=null;
    TaskBarItem src=getItemAt(e.getX(),e.getY());
    if((e.getModifiers() & MouseEvent.BUTTON1_MASK)!=0) if(src!=null) activate(src.source);
    _buffer=null;
    repaint();
  }
  
  public void titleChanged(AWTSource source)
  {
    _buffer=null;
    repaint();
  }
  
  public void eventOccured(AWTSource source)
  {
    TaskBarItem item=findItem(source);
    if(item==null) return;
    if(item==_active) return;
    item.eventWaiting=true;
    _buffer=null;
    repaint();
  }
  
  public void activated(AWTSource source)
  {
    TaskBarItem item=(TaskBarItem)_pendingItems.get(source);
    if(item==null) return;
    _pendingItems.remove(source);
    enter(source,item.row,item.bring);
  }
}

