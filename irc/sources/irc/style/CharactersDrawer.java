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

import irc.*;
import java.awt.*;
import java.awt.image.*;

public class CharactersDrawer implements ImageObserver
{
  private IRCConfiguration _ircConfiguration;
	private boolean _bitmapError;
	
  public CharactersDrawer(IRCConfiguration config)
  {
	  _ircConfiguration=config;
  }
  
  private int getSmileyWidth(char c,FontMetrics fm)
  {
	  if(c>255) return getBitmapSmileyWidth(c-256);
    return fm.getFont().getSize();
  }
  
  private int getSmileyHeight(char c,FontMetrics fm)
  {
	  if(c>255) return getBitmapSmileyHeight(c-256);
    return fm.getFont().getSize();
  }
	
	private void check(Image img)
	{
	  _bitmapError=false;
	  Toolkit tk=Toolkit.getDefaultToolkit();
		int timeout=50;
		while(tk.prepareImage(img,-1,-1,this)==false)
		{
		  try
			{
		    Thread.sleep(100);
				timeout--;
			}
			catch(InterruptedException ex)
			{
			}
			if(_bitmapError) return;
			if(timeout==0) return;
		}
	}
	
	private int getBitmapSmileyWidth(int c)
	{
	  Image img=_ircConfiguration.getSmileyTable().getImage(c);
		if(img==null) return 0;
		check(img);
		return img.getWidth(this);
	}
	
	private int getBitmapSmileyHeight(int c)
	{
	  Image img=_ircConfiguration.getSmileyTable().getImage(c);
		if(img==null) return 0;
		check(img);
		return img.getHeight(this);
	}
	
  private void drawBase(Graphics g,int x,int y,int r)
  {
    r--;
    int mr=r/2;
    g.drawLine(x-r,y-mr,x-r,y+mr);
    g.drawLine(x+r,y-mr,x+r,y+mr);
    g.drawLine(x-mr,y-r,x+mr,y-r);
    g.drawLine(x-mr,y+r,x+mr,y+r);
    
    g.drawLine(x-r+1,y-mr-1,x-mr-1,y-r+1);
    g.drawLine(x+mr+1,y-r+1,x+r-1,y-mr-1);
    g.drawLine(x+r-1,y+mr+1,x+mr+1,y+r-1);
    g.drawLine(x-r+1,y+mr+1,x-mr-1,y+r-1);		
  }
  
	private void drawBitmapSmiley(Graphics g,FontMetrics fm,int smiley,int x,int y)
	{
	  Image img=_ircConfiguration.getSmileyTable().getImage(smiley);
		if(img==null) return;
		check(img);
		int h=getBitmapSmileyHeight(smiley);
		y-=h;
    y+=fm.getDescent();
		
	  g.drawImage(img,x,y,this);
	}
	
  private void drawSmiley(Graphics g,FontMetrics fm,char smiley,int x,int y)
  {
	  if(smiley>255)
		{
		  drawBitmapSmiley(g,fm,smiley-256,x,y);
			return;
		}
    int s=fm.getFont().getSize();
    
    y=y-s+fm.getDescent();
    s/=2;
    x+=s-1;
    y+=s-1;
    drawBase(g,x,y,s);
    int r=s-1;
    int mr=r/2;
    int lex=x-mr;
    int rex=x+mr;
    int ey=y-mr;
    int my=y+mr;
    switch(smiley&0x0F)
    {
    case 0x00:
      g.drawLine(lex,ey,lex,ey+1);
      g.drawLine(rex,ey,rex,ey+1);
      break;
    case 0x01:
      g.drawLine(lex,ey+1,lex+1,ey+1);
      g.drawLine(rex,ey,rex,ey+1);
      break;
    }
    
    switch(smiley&0xF0)
    {
    case 0x00:
      g.drawLine(lex+1,my+1,rex-1,my+1);
      g.drawLine(lex+1,my+1,lex,my);
      g.drawLine(rex-1,my+1,rex,my);
      break;
    case 0x10:
      g.drawLine(lex,my,rex,my);
      break;
    case 0x20:
      g.drawLine(lex+1,my,rex-1,my);
      g.drawLine(lex+1,my,lex,my+1);
      g.drawLine(rex-1,my,rex,my+1);
      break;
    case 0x30:
      g.drawLine(lex+1,my-1,rex-1,my-1);
      g.drawLine(lex+1,my+1,rex-1,my+1);
      g.drawLine(lex+1,my-1,lex+1,my+1);
      g.drawLine(rex-1,my-1,rex-1,my+1);
      break;
    case 0x40:
      g.drawLine(lex,my,rex,my);
      Color old=g.getColor();
      g.setColor(Color.red);
      g.drawLine(lex+1,my+1,rex-1,my+1);
      g.setColor(old);
      break;
    }
      
  }
  
  private String handleSmiley(String line,String ascii,char code)
  {
    int pos=line.indexOf(ascii);
    if(pos==-1) return line;
    
    String previous=line.substring(0,pos);
    String after=line.substring(pos+ascii.length());
    char toAdd=(char)(code+0xE000);
    line=previous+toAdd+after;
    return handleSmiley(line,ascii,code);
  }
  
  public String decodeLine(String line)
  {
    if(!_ircConfiguration.getSmileys()) return line;
		if(!_ircConfiguration.getBitmapSmileys())
		{
			line=handleSmiley(line,":)",(char)0x00);
			line=handleSmiley(line,":-)",(char)0x00);
			line=handleSmiley(line,"(:",(char)0x00);
			line=handleSmiley(line,"(-:",(char)0x00);
			
			line=handleSmiley(line,":-|",(char)0x10);
			line=handleSmiley(line,"|-:",(char)0x10);
			
			line=handleSmiley(line,":(",(char)0x20);
			line=handleSmiley(line,":-(",(char)0x20);
			line=handleSmiley(line,"):",(char)0x20);
			line=handleSmiley(line,")-:",(char)0x20);
			
			line=handleSmiley(line,":-o",(char)0x30);
			line=handleSmiley(line,"o-:",(char)0x30);
			
			line=handleSmiley(line,":p",(char)0x40);
			line=handleSmiley(line,":-p",(char)0x40);
			line=handleSmiley(line,"d-:",(char)0x40);
			
			line=handleSmiley(line,";)",(char)0x01);
			line=handleSmiley(line,";-)",(char)0x01);
			
			line=handleSmiley(line,";-|",(char)0x11);
			
			line=handleSmiley(line,";(",(char)0x21);
			line=handleSmiley(line,";-(",(char)0x21);
			
			line=handleSmiley(line,";-o",(char)0x31);
			
			line=handleSmiley(line,";p",(char)0x41);
			line=handleSmiley(line,";-p",(char)0x41);
			
			return line;
		}
		else
		{
			SmileyTable table=_ircConfiguration.getSmileyTable();
			int s=table.getSize();
			for(int i=0;i<s;i++)
			{
			  String m=table.getMatch(i);
				line=handleSmiley(line,m,(char)(i+256));
			}
			return line;
		}
  }
	
  public int getWidth(String str,FontMetrics fm)
  {
    String current="";
    int w=0;
    for(int i=0;i<str.length();i++)
    {
      char c=str.charAt(i);
      if((c>=0xE000) && (c<=0xF8FF))
      {
        c-=0xE000;
        w+=fm.stringWidth(current);
        current="";
        w+=getSmileyWidth(c,fm);
      }
      else
      {
        current+=c;
      }
    }
    w+=fm.stringWidth(current);
    return w;
  }
  
	public int getHeight(String str,FontMetrics fm)
	{
    String current="";
    int h=0;
		int mh=0;
    for(int i=0;i<str.length();i++)
    {
      char c=str.charAt(i);
      if((c>=0xE000) && (c<=0xF8FF))
      {
        c-=0xE000;
        current="";
        h=getSmileyHeight(c,fm);
				if(h>mh) mh=h;
      }
      else
      {
        current+=c;
      }
    }
    h=fm.getFont().getSize()+1;
		if(h>mh) mh=h;
    return mh;	
	}
	
  public void draw(String str,Graphics g,FontMetrics fm,int x,int y)
  {
    String current="";
    for(int i=0;i<str.length();i++)
    {
      char c=str.charAt(i);
      if((c>=0xE000) && (c<=0xF8FF))
      {
        c-=0xE000;
        g.drawString(current,x,y);
        //System.out.println("drawn "+current+" at "+x);
        x+=fm.stringWidth(current);
        current="";
        drawSmiley(g,fm,c,x,y);
        //System.out.println("drawn smiley at "+x);
        x+=getSmileyWidth(c,fm);
        
      }
      else
      {
        current+=c;
      }
    }
    g.drawString(current,x,y);
    //System.out.println("drawn "+current+" at "+x);
  }
	
  public boolean imageUpdate(Image img,int infoflags,int x,int y,int width,int height)
	{
	  if((infoflags&ImageObserver.ABORT)!=0)
		{
		  _bitmapError=true;
		}
	  return true;
	}
}

