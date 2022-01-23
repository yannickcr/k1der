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

public class PixxScrollBar extends PixxPanel implements MouseListener,MouseMotionListener,Runnable
{
  private double _min;
  private double _max;
  private double _val;
  private int _type;
  private boolean _mouseDown;
  private boolean _mouseDownUp;
  private boolean _mouseDownDown;
  private int _baseY;
  
  private final int _arrow=10;
  private double _view;
	
	private MouseEvent _repeatEvent;
	private int _repeatEventCount;
  private Thread _repeatThread;
	
  private Hashtable _listeners;
  public final static int HORIZONTAL=0;
  public final static int VERTICAL=0;
  
  
  public PixxScrollBar(IRCConfiguration config,int min,int max,int type,double view)
  {
    super(config);
    _mouseDown=false;
    _type=type;
    _view=view;
    _listeners=new Hashtable();
    setMinimum(min);
    setMaximum(max);
    setValue(min);
    addMouseListener(this);
    addMouseMotionListener(this);
  }
  
	public void run()
	{
	  boolean terminated=false;
		_repeatEventCount=0;
		while(!terminated)
		{
		  try
			{
			  if(_repeatEventCount++==0)
				  Thread.sleep(500);
				else
				  Thread.sleep(50);
				mousePressed(_repeatEvent);
			}
			catch(InterruptedException ex)
			{
			  terminated=true;
			}
		}
	}
	
  public void addPixxScrollBarListener(PixxScrollBarListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removePixxScrollBarListener(PixxScrollBarListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerValueChangedListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxScrollBarListener lis=(PixxScrollBarListener)e.nextElement();
      lis.valueChanged(this);
    }
  }

  private Color[] getColors(boolean invert)
  {
    Color[] c=new Color[5];
    if(!invert)
    {
      c[0]=getColor(COLOR_FRONT);
      c[1]=getColor(COLOR_BLACK);
      c[2]=getColor(COLOR_GRAY);
      c[3]=getColor(COLOR_LIGHT_GRAY);
      c[4]=getColor(COLOR_WHITE);
    }
    else
    {
      c[0]=getColor(COLOR_SELECTED);
      c[1]=getColor(COLOR_BLACK);
      c[2]=getColor(COLOR_GRAY);
      c[3]=getColor(COLOR_LIGHT_GRAY);
      c[4]=getColor(COLOR_WHITE);
    }
    
    return c;		
  }
  
  private void drawUp(Graphics g,int y,boolean invert)
  {
    int w=getSize().width;
    int h=getSize().height;
    
    Color c[]=getColors(invert);
    
    g.setColor(c[0]);
    for(int i=0;i<w-5;i++)
      g.drawLine(i+3,y-1,i+3,y-1-i);
    
    g.setColor(c[1]);
    g.drawLine(0,y-1,w-2,y-w+1);
    g.setColor(c[2]);
    g.drawLine(1,y-1,w-2,y-w+2);
    g.setColor(c[4]);
    g.drawLine(2,y-1,w-2,y-w+3);
    
    g.setColor(c[1]);
    g.drawLine(w-1,y-1,w-1,y-w);
    g.setColor(c[4]);
    g.drawLine(w-2,y-1,w-2,y+3-w);
    
  }
  
  private void drawDown(Graphics g,int y,boolean invert)
  {
    int w=getSize().width;
    int h=getSize().height;
    
    Color c[]=getColors(invert);
    
    g.setColor(c[0]);
    for(int i=0;i<w-5;i++)
      g.drawLine(w-1-i-3,y,w-1-i-3,y+i);
    
    g.setColor(c[1]);
    g.drawLine(0,y+w-1,w-1,y);
    g.setColor(c[2]);
    g.drawLine(1,y+w-3,w-2,y);
    g.setColor(c[4]);
    g.drawLine(1,y+w-4,w-3,y);
    
    g.setColor(c[3]);
    g.drawLine(0,y,0,y+w-2);
    g.setColor(c[4]);
    g.drawLine(1,y,1,y+w-4);
    
    
  }
  
  private void drawVert(Graphics g,int y,int lng,boolean invert)
  {
    int w=getSize().width;
    int h=getSize().height;
    Color c[]=getColors(invert);
    
    g.setColor(c[3]);
    g.drawLine(0,y,0,y+lng-1);
    g.setColor(c[4]);
    g.drawLine(1,y,1,y+lng-1);
    g.drawLine(w-2,y,w-2,y+lng-1);
    g.setColor(c[1]);
    g.drawLine(w-1,y,w-1,y+lng-1);
    g.setColor(c[0]);
    g.fillRect(2,y,w-4,lng);	
  }
  
  public Dimension getPreferredSize()
  {
    return new Dimension(16,16);
  }
  
  public void paint(Graphics g)
  {
    update(g);
  }
  
  private int getMargin()
  {
    int w=getSize().width;
    int h=getSize().height;
    int lrg=w;
    int lng=h;
    return _arrow+lrg;	
  }
  
  private int getCursorLong()
  {
    int w=getSize().width;
    int h=getSize().height;
    int margin=getMargin();
    if(_min==_max) return h-2*margin;
    double iSee=(h-2*margin)*_view;
    
    int cursorLong=(int)((iSee/(_max-_min+1))*(h-2*margin));
    if(cursorLong>(h-2*margin)/3) cursorLong=(h-2*margin)/3;
    return cursorLong;
  }
  
  private int getPos()
  {
    int w=getSize().width;
    int h=getSize().height;
    int lrg=w;
    int lng=h;
    
    int margin=getMargin();
    int cursorLong=getCursorLong();
    return (int)((_val*(lng-margin-cursorLong)+(_max-_val)*margin)/(_max)-margin);
  }
  
  public void update(Graphics g)
  {
    int w=getSize().width;
    int h=getSize().height;
    int margin=getMargin();
    int cursorLong=getCursorLong();
    
    Image buffer;
    Graphics gra;
    try
    {
      buffer=createImage(w,h);
      gra=buffer.getGraphics();
    }
    catch(Throwable e)
    {
      return;
    }
    
    gra.setColor(getColor(COLOR_BACK));
    gra.fillRect(0,0,w,h);
    
    //fleche du haut
    drawVert(gra,2,_arrow-2,_mouseDownUp);
    drawDown(gra,margin-w,_mouseDownUp);
    
    Color c[]=getColors(_mouseDownUp);
    gra.setColor(c[3]);
    gra.drawLine(1,0,w-2,0);
    gra.drawLine(0,0,0,1);		
    gra.setColor(c[4]);
    gra.drawLine(1,1,w-2,1);
    gra.setColor(c[1]);
    gra.drawLine(w-1,0,w-1,1);
    
    gra.setColor(c[4]);
    gra.drawLine(w/2,4,w/4+1,4+w/4-1);
    gra.drawLine(w/2,4,3*w/4-1,4+w/4-1);
    
    //fleche du bas
    drawVert(gra,h-_arrow,_arrow-2,_mouseDownDown);
    drawUp(gra,h-margin+w,_mouseDownDown);
    
    c=getColors(_mouseDownDown);
    gra.setColor(c[3]);
    gra.drawLine(0,h-2,0,h-1);
    gra.setColor(c[1]);
    gra.drawLine(w-1,h-2,w-1,h-1);
    gra.drawLine(1,h-1,w-2,h-1);
    gra.setColor(c[4]);
    gra.drawLine(1,h-2,w-2,h-2);

    gra.setColor(c[4]);
    gra.drawLine(w/2,h-5,w/4+1,h-5-w/4+1);
    gra.drawLine(w/2,h-5,3*w/4-1,h-5-w/4+1);

    //curseur
    int pos=getPos()+margin;
    drawVert(gra,pos,cursorLong,_mouseDown);
    drawUp(gra,pos,_mouseDown);
    drawDown(gra,pos+cursorLong,_mouseDown);
    
    g.drawImage(buffer,0,0,this);
  }
  
  public int getType()
  {
    return _type;
  }
  
  public void setMinimum(int v)
  {
    _min=v;
    if(_min>_max) _min=_max;
    repaint();
  }
  
  public void setMaximum(int v)
  {
    _max=v;
    if(_max<_min) _max=_min;
    repaint();
  }
  
  public void setValue(int v)
  {
    _val=v;
    if(_val<_min) _val=_min;
    if(_val>_max) _val=_max;
    repaint();
  }
  
  public int getValue()
  {
    return (int)(_val+0.5);
  }
  
  private boolean inCursor(int x,int y)
  {
    int w=getSize().width;
    int h=getSize().height;
    int l=getCursorLong();
    y-=getMargin();
    y-=getPos();
    
    return (x+y>=-1) && (y+x-l-w<=-1);		
  }
  
  private boolean inUpArrow(int x,int y)
  {
    y-=getMargin();
    return (x+y<=-1);
  }
  
  private boolean inDownArrow(int x,int y)
  {
    int w=getSize().width;
    int h=getSize().height;
    return (y+x-h+getMargin()-w>=-1);
  }
  
  private double getValue(int x,int y)
  {
    int w=getSize().width;
    int h=getSize().height;
    int lrg=w;
    int lng=h;
    int margin=getMargin();
      
    lng-=margin*2+getCursorLong();
      
    int py=y-margin-_baseY;
      
    return (_max-_min)*py/lng+_min;	
  }
  
  private void updateValue(double v)
  {
    int oldVal=getValue();
    _val=v;
    if(_val<_min) _val=_min;
    if(_val>_max) _val=_max;
    repaint();
    if(getValue()!=oldVal)
    {
      triggerValueChangedListeners();
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

  private synchronized void beginRepeat(MouseEvent e)
	{
		_repeatEvent=e;
		_repeatThread=new Thread(this,"Scrolling thread");
	  _repeatThread.start();	
	}

  private synchronized void endRepeat()
	{
	  if(_repeatThread!=null)
		{
	    _repeatThread.interrupt();
	    try
	    {
	      _repeatThread.join();
	    }
	    catch(InterruptedException ex)
	    {
      }
      _repeatThread=null;
		}	
	}
  
  public void mousePressed(MouseEvent e)
  {
    if(inCursor(e.getX(),e.getY()))
    {
      _baseY=e.getY()-getMargin()-getPos();
      _mouseDown=true;
      repaint();
			return;
    }
    else if(inUpArrow(e.getX(),e.getY()))
    {
      _mouseDownUp=true;
      updateValue(_val-1);
      repaint();
    }
    else if(inDownArrow(e.getX(),e.getY()))
    {		
      _mouseDownDown=true;
      updateValue(_val+1);
      repaint();
    }
    else if(getValue(e.getX(),e.getY())<_val)
    {
      updateValue(_val-10);
      repaint();
    }
    else if(getValue(e.getX(),e.getY())>_val)
    {
      updateValue(_val+10);
      repaint();
    }
		if(_repeatThread==null) beginRepeat(e);
  }
  
  public void mouseReleased(MouseEvent e)
  {
		endRepeat();
    _mouseDown=false;
    _mouseDownUp=false;
    _mouseDownDown=false;
    repaint();
  }
  
  public void mouseDragged(MouseEvent e)
  {
    mouseMoved(e);
  }
  
  public void mouseMoved(MouseEvent e)
  {
	  _repeatEvent=e;
    if(_mouseDown)
    {
      updateValue(getValue(e.getX(),e.getY()));
    }
  }
}

