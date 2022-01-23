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
import java.util.*;
import java.awt.*;
import java.awt.geom.*;

class CharacterInfo
{
  
  public CharacterInfo()
  {
    frontColor=Color.black;
    backColor=Color.white;
    isBold=false;
    isUnderline=false;
    isReverse=false;
  }
  
  public CharacterInfo(CharacterInfo base)
  {
    frontColor=base.frontColor;
    backColor=base.backColor;
    isBold=base.isBold;
    isUnderline=base.isUnderline;
    isReverse=base.isReverse;
  }
  
  public boolean equals(Object o)
  {
    if(!(o instanceof CharacterInfo)) return false;
    CharacterInfo c=(CharacterInfo)o;
    if(!frontColor.equals(c.frontColor)) return false;
    if(!backColor.equals(c.backColor)) return false;
    if(isBold!=c.isBold) return false;
    if(isUnderline!=c.isUnderline) return false;
    return true;
  }
  
  public int hashCode()
  {
    int c=0;
    if(isBold) c++;
    if(isUnderline) c++;
    return c+frontColor.hashCode()+backColor.hashCode();
  }
  
  public Color frontColor;
  public Color backColor;
  public boolean isBold;
  public boolean isUnderline;
  public boolean isReverse;
}

class CharacterGroupItem
{
  public String s;
  public CharacterInfo info;
  
  public CharacterGroupItem(CharacterInfo nfo)
  {
    info=nfo;
    s="";
  }
}

class WordItem
{
  public CharacterGroupItem[] items;
  public String word;
  public CharacterInfo lastInfo;
  
  public WordItem(CharacterGroupItem[] itm,CharacterInfo lInfo)
  {
    lastInfo=lInfo;
    items=itm;
    word="";
    for(int i=0;i<items.length;i++) word+=items[i].s;
  }
}

class LineItem
{
  public WordItem[] words;
}

public class FormattedStringDrawer
{
  private Font _font;
  private Font _fontPlain;
  private Font _fontBold;
  private Color[] _cols;
  private CharactersDrawer _drawer;
	private IRCConfiguration _config;
  
  public FormattedStringDrawer(Font fnt,IRCConfiguration config,ColorContext context)
  {	
	  _config=config;
    setFont(fnt);
    _drawer=new CharactersDrawer(_config);
		setColorContext(context);
  }
	
	public void setColorContext(ColorContext context)
	{
	  _cols=_config.getStyleColors(context);
	}
	
	public String decodeLine(String str)
	{
	  return _drawer.decodeLine(str);
	}
	
	public int getHeight(String str,FontMetrics fm)
	{
	  return _drawer.getHeight(str,fm);
	}
	
	public int getWidth(String str,FontMetrics fm)
	{
	  return _drawer.getWidth(str,fm);	
	}
	
  private Font deriveFont(Font fnt,int style)
  {
    return new Font(fnt.getName(),style,fnt.getSize());
  }
  
  public void setColors(Color[] cols)
  {
    _cols=cols;
  }
  
	public Color getColor(int i)
	{
	  return _cols[i];
	}
	
  public void setFont(Font fnt)
  {
    _font=fnt;
    _fontPlain=deriveFont(_font,Font.PLAIN);
    _fontBold=deriveFont(_font,Font.BOLD);
  }
  
  public Font getFont()
  {
    return _font;
  }
  
  private WordItem decodeWord(CharacterInfo base,String str,Color[] cols)
  {
    Vector v=new Vector();
    CharacterInfo current=new CharacterInfo(base);
    CharacterGroupItem currentItem=new CharacterGroupItem(new CharacterInfo(current));
    int size=str.length();
    for(int pos=0;pos<size;pos++)
    {
      char c=str.charAt(pos);			
      if(c<' ')
      {
        int code=(int)c;
        if(code==15)
        {
          current.isBold=false;
          current.isUnderline=false;
          current.isReverse=false;
          current.frontColor=cols[1];
          current.backColor=cols[0];
        }
        else if(code==2)
        {
          current.isBold=!current.isBold;
        }
        else if(code==31)
        {
          current.isUnderline=!current.isUnderline;
        }
        else if(code==22)
        {
          current.isReverse=!current.isReverse;
          if(current.isReverse)
          {
            current.frontColor=cols[0];
            current.backColor=cols[1];
          }
          else
          {
            current.frontColor=cols[1];
            current.backColor=cols[0];										
          }
        }
        else if(code==3)
        {
          boolean front=true;
          String frontC="";
          String backC="";
          pos++;
          while(pos<size)
          {
            char d=str.charAt(pos);
            if((d>='0') && (d<='9'))
            {
              if(front)
              {
                if(frontC.length()==2)
                {
                  pos--;
                  break;
                }
                frontC+=d;
              }
              else
              {
                if(backC.length()==2)
                {
                  pos--;
                  break;
                }
                backC+=d;
              }
              pos++;
            }
            else if(d==',')
            {
              if(front)
              {
                front=false;
                pos++;
              }
              else
              {
                pos--;
                break;
              }
            }
            else
            {
              pos--;
              break;
            }
          }
          if(frontC.length()>0)
          {
            int col=Integer.parseInt(frontC);
            col%=_cols.length;
            current.frontColor=cols[col];
          }
          if(backC.length()>0)
          {
            int col=Integer.parseInt(backC);
            col%=_cols.length;
            current.backColor=cols[col];					
          }
          if((frontC.length()==0) && (backC.length()==0))
          {
            current.frontColor=cols[1];
            current.backColor=cols[0];					
          }
        }
        if(!current.equals(currentItem.info))
        {
          v.insertElementAt(currentItem,v.size());
          currentItem=new CharacterGroupItem(new CharacterInfo(current));
        }
      }
      else
      {
        currentItem.s+=c;
      }
    }
    v.insertElementAt(currentItem,v.size());
    
    CharacterGroupItem[] ans=new CharacterGroupItem[v.size()];
    for(int i=0;i<v.size();i++)
    {
      ans[i]=(CharacterGroupItem)v.elementAt(i);
    }
    
    return new WordItem(ans,current);
  }

  private FontMetrics getFontMetrics(Graphics g,CharacterInfo nfo)
	{
	  Font old=g.getFont();
    if(nfo.isBold) g.setFont(_fontBold);
	  else g.setFont(_fontPlain);
		FontMetrics res=g.getFontMetrics();
		g.setFont(old);
		return res;
	}

  private int drawPart(Graphics g,CharacterInfo nfo,String str,int x,int y,FontMetrics plainMetrics,int clipxl,int clipxr)
  {
    FontMetrics fm=plainMetrics;
    int up=plainMetrics.getAscent();
    int down=plainMetrics.getDescent();
    
    if(nfo.isBold) g.setFont(_fontBold);

    fm=g.getFontMetrics();
		
    int width=_drawer.getWidth(str,fm);
		if((x<=clipxr) && (x+width>clipxl))
		{
      int height=_drawer.getHeight(str,fm);
			Rectangle originalClip=g.getClipBounds();
		  g.setClip(clipxl,y-height,clipxr-clipxl+1,height);
		
      g.setColor(nfo.backColor);
			
      g.fillRect(x,y-height,width,height);
			
      y-=down;

      g.setColor(nfo.frontColor);
      _drawer.draw(str,g,fm,x,y);
			
      if(nfo.isUnderline)
        g.drawLine(x,y+1,x+width-1,y+1);
			if(originalClip!=null)
			  g.setClip(originalClip.x,originalClip.y,originalClip.width,originalClip.height);
			else
			  g.setClip(null);
    }
		
    if(nfo.isBold) g.setFont(_fontPlain);
    return width;
  }
  
  private void drawWord(Graphics g,WordItem word,int x,int y,boolean last,FontMetrics plainMetrics,int clipxl,int clipxr)
  {			
    for(int pos=0;pos<word.items.length;pos++)
    {
      CharacterGroupItem item=word.items[pos];
      x+=drawPart(g,item.info,item.s,x,y,plainMetrics,clipxl,clipxr);
    }
  }
  
  public String getStripped(String str)
  {
    CharacterInfo info=new CharacterInfo();
    info.frontColor=_cols[1];
    info.backColor=_cols[0];
    String res="";
    while(str.length()>0)
    {
      int pos=str.indexOf(' ');
      WordItem word;
      if(pos==-1)
      {
        word=decodeWord(info,str,_cols);
        str="";
      }
      else
      {
        String wrd=str.substring(0,pos);
        word=decodeWord(info,wrd+" ",_cols);
        str=str.substring(pos+1);
      }
      res+=" "+word.word;
    }
    return res;
  }
  
	private boolean isAlphaNum(char c)
	{
	  if((c=='(') || (c==')')) return false;
	  if((c=='<') || (c=='>')) return false;
	  if((c=='"') || (c=='"')) return false;
	  if((c=='{') || (c=='}')) return false;
	  if((c=='.') || (c==',')) return false;
		return true;
	}
	
	private String trimAlphaNum(String s)
	{
		int index=0;
		while((index<s.length()) && !isAlphaNum(s.charAt(index))) index++;
		if(index==s.length()) return "";
		s=s.substring(index);
		index=s.length()-1;
		while((index>=0) && !isAlphaNum(s.charAt(index))) index--;
		if(index==-1) return "";
		s=s.substring(0,index+1);
		return s;
	}
	
	private int getWordItemWidth(Graphics g,WordItem item)
	{
	  int res=0;
		for(int i=0;i<item.items.length;i++)
		{
		  FontMetrics fm=getFontMetrics(g,item.items[i].info);
			res+=_drawer.getWidth(item.items[i].s,fm);
		}
		return res;
	}
	
	private int getWordItemHeight(Graphics g,WordItem item)
	{
	  int res=0;
		for(int i=0;i<item.items.length;i++)
		{
		  FontMetrics fm=getFontMetrics(g,item.items[i].info);			
			int h=_drawer.getHeight(item.items[i].s,fm);
			if(h>res) res=h;
		}
		return res;
	}
	
  public DrawResult draw(String str,Graphics g,int x,int y,int wmax,WordCatcher catcher,int clipxl,int clipxr)
  {	
    //séparation en mots
    Vector words=new Vector();
    CharacterInfo info=new CharacterInfo();
    info.frontColor=_cols[1];
    info.backColor=_cols[0];
    
    while(str.length()>0)
    {
      int pos=str.indexOf(' ');
      WordItem word;
      if(pos==-1)
      {
        word=decodeWord(info,str,_cols);
        str="";
      }
      else
      {
        String wrd=str.substring(0,pos);
        word=decodeWord(info,wrd+" ",_cols);
        str=str.substring(pos+1);
      }
      words.insertElementAt(word,words.size());
      info=word.lastInfo;
    }
    
    //séparation en lignes
    Font currFont=_fontPlain;
    g.setFont(currFont);
    FontMetrics plainFm=g.getFontMetrics();
    
    Vector lines=new Vector();
    Vector currentLine=new Vector();
    int w=0;
    for(int i=0;i<words.size();i++)
    {
      WordItem word=(WordItem)words.elementAt(i);
		  int wordWidth=getWordItemWidth(g,word);
      if(w+wordWidth>wmax)
      {
        w=_drawer.getWidth("  ",plainFm);
        LineItem newLine=new LineItem();
        newLine.words=new WordItem[currentLine.size()];
        for(int j=0;j<newLine.words.length;j++) newLine.words[j]=(WordItem)currentLine.elementAt(j);
        currentLine=new Vector();
        lines.insertElementAt(newLine,lines.size());
      }
      currentLine.insertElementAt(word,currentLine.size());
      w+=wordWidth;
    }
    if(currentLine.size()!=0)
    {
      LineItem newLine=new LineItem();
      newLine.words=new WordItem[currentLine.size()];
      for(int j=0;j<newLine.words.length;j++) newLine.words[j]=(WordItem)currentLine.elementAt(j);
      lines.insertElementAt(newLine,lines.size());
    }
    
    //affichage
    Vector ritems=new Vector();
    int maxWidth=0;
		int h=0;
    for(int i=lines.size()-1;i>=0;i--)
    {
      int px=0;
			int maxHeight=0;
      if(i!=0) px+=_drawer.getWidth("  ",plainFm);
      LineItem line=(LineItem)lines.elementAt(i);
      for(int j=0;j<line.words.length;j++)
      {
		    int wordWidth=getWordItemWidth(g,line.words[j]);
		    int trimmedWidth=wordWidth;
				int wordHeight=getWordItemHeight(g,line.words[j]);
				if(maxHeight<wordHeight) maxHeight=wordHeight;
        drawWord(g,line.words[j],px+x,y,j==line.words.length-1,plainFm,clipxl,clipxr);
        if(catcher!=null)
        {
				  String wrd=trimAlphaNum(line.words[j].word.trim());
          String type=catcher.getType(wrd);
          if(type!=null)
          {
            DrawResultItem ritem=new DrawResultItem();
            ritem.type=type;
            ritem.word=wrd;
            ritem.rectangle=new StyledRectangle(px+x,y-wordHeight,trimmedWidth,wordHeight);
            ritems.insertElementAt(ritem,ritems.size());
          }
        }
        px+=wordWidth;
        if(px>maxWidth) maxWidth=px;
      }
      y-=maxHeight;
			h+=maxHeight;
    }
    
    DrawResult res=new DrawResult();
    res.dimension=new Dimension(maxWidth,h);
    res.items=new DrawResultItem[ritems.size()];
    for(int i=0;i<ritems.size();i++) res.items[i]=(DrawResultItem)ritems.elementAt(i);
    
    return res;
  }	
}

