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

import java.awt.*;
import java.util.*;

public class IRCConfiguration
{
  private boolean _timeStamp;
  private boolean _smileys;
  private TextProvider _textProvider;
  private IRCColorModel _colorModel;
	private ImageLoader _loader;
  private URLHandler _handler;
  private boolean _highLight;
  private int _highLightColor;
  private boolean _highLightNick;
  private Vector _highLightWords;
  private String _quitMessage;
	private boolean _showConnect;
	private boolean _showChanlist;
	private boolean _showHelp;
	private boolean _showAbout;
	private boolean _handleASL;
	private String _aslMale;
	private String _aslFemale;
	private SmileyTable _table;
  private boolean _bitmap;
	private int _chanlistFontSize;
	private String _chanlistFontName;
	private int _channelFontSize;
	private String _channelFontName;
	private int _nickListWidth;
	private boolean _info;
	private boolean _nickField;
	private Color[] _chanlistColors;
	private Color[] _defaultColors;
	private Hashtable _colors;
 
  public IRCConfiguration(boolean timeStamp,boolean smileys,TextProvider text,IRCColorModel model,URLHandler handler,ImageLoader loader)
  {
	  _colors=new Hashtable();
		_defaultColors=new Color[16];
		_chanlistColors=new Color[16];
		loadDefaultColors(_defaultColors);
		loadDefaultColors(_chanlistColors);
		
	  _loader=loader;
    _timeStamp=timeStamp;
    _smileys=smileys;
    _textProvider=text;
    _colorModel=model;
    _handler=handler;
    setQuitMessage("");
    enableHighLight(false);
		setShowConnect(true);
		setShowChanlist(true);
		setShowHelp(true);
		setShowAbout(true);
		setASL(false);
		setASLMale("m");
		setASLFemale("f");
		setBitmapSmileys(false);
		_table=new SmileyTable();
		setChannelFontSize(12);
		setChanlistFontSize(12);
		setChannelFontName("Monospaced");
		setChanlistFontName("Monospaced");
		setNickListWidth(130);
		setInfo(false);
		setNickField(false);		
  }

  public ColorContext getDefaultColorContext()
	{
	  return getColorContext(null);
	}

  public ColorContext getColorContext(String name)
	{
	  ColorContext ctx=new ColorContext();
		ctx.chanlist=false;
		ctx.name=name;
	  return ctx;
	}
	
	public ColorContext getChanlistColorContext()
	{
	  ColorContext ctx=new ColorContext();
		ctx.chanlist=true;
		ctx.name="";
	  return ctx;
	}
	
	private void loadDefaultColors(Color[] cols)
	{
    cols[0]=new Color(0xFFFFFF);
    cols[1]=new Color(0x000000);
    cols[2]=new Color(0x00007F);
    cols[3]=new Color(0x009300);
    cols[4]=new Color(0xFF0000);
    cols[5]=new Color(0x7F0000);
    cols[6]=new Color(0x9C009C);
    cols[7]=new Color(0xFC7F00);
    cols[8]=new Color(0xFFFF00);
    cols[9]=new Color(0x00FC00);
    cols[10]=new Color(0x009393);
    cols[11]=new Color(0x00FFFF);
    cols[12]=new Color(0x0000FC);
    cols[13]=new Color(0xFF00FF);
    cols[14]=new Color(0x7F7F7F);
    cols[15]=new Color(0xD2D2D2);
	}
	
	public void setChanlistColor(int i,Color c)
	{
	  _chanlistColors[i]=c;
	}
	
	public void setDefaultColor(int i,Color c)
	{
	  _defaultColors[i]=c;
	}
	
	public void setSourceColor(String name,int index,Color c)
	{
	  if(_colors.get(name.toLowerCase())==null)
		{
		  Color[] n=new Color[16];
			for(int i=0;i<16;i++) n[i]=_defaultColors[i];
			_colors.put(name.toLowerCase(),n);
		}
	  Color[] cols=(Color[])_colors.get(name.toLowerCase());
		cols[index]=c;
	}
	
  public Color[] getStyleColors(ColorContext context)
	{
	  if(context.chanlist) return _chanlistColors;
		if(context.name==null) return _defaultColors;
		Color[] cols=(Color[])_colors.get(context.name.toLowerCase());
		if(cols==null) return _defaultColors;
		return cols;
	}

  public void setInfo(boolean s)
	{
	  _info=s;
	}
	
	public boolean getInfo()
	{
	  return _info;
	}

  public void setNickField(boolean s)
	{
	  _nickField=s;
	}
	
	public boolean getNickField()
	{
	  return _nickField;
	}

  public void setNickListWidth(int w)
	{
	  _nickListWidth=w;
	}
	
	public int getNickListWidth()
	{
	  return _nickListWidth;
	}

  public void setChannelFontSize(int s)
	{
	  _channelFontSize=s;
	}

  public void setChanlistFontSize(int s)
	{
	  _chanlistFontSize=s;
	}
	
	public void setChannelFontName(String s)
	{
	  _channelFontName=s;
	}
	
	public void setChanlistFontName(String s)
	{
	  _chanlistFontName=s;
	}
	
	public int getChannelFontSize()
	{
	  return _channelFontSize;
	}
	
	public int getChanlistFontSize()
	{
	  return _chanlistFontSize;
	}
	
	public String getChannelFontName()
	{
	  return _channelFontName;
	}
	
	public String getChanlistFontName()
	{
	  return _chanlistFontName;
	}
	
	
  public void addSmiley(String match,String file)
	{
	  _table.addSmiley(match,_loader.getImage(file));
	}

  public ImageLoader getImageLoader()
	{
	  return _loader;
	}

  public void setBitmapSmileys(boolean s)
	{
	  _bitmap=s;
	}
	
	public boolean getBitmapSmileys()
	{
	  return _bitmap;
	}

  public SmileyTable getSmileyTable()
	{
	  return _table;
	}

  public void setASLMale(String m)
	{
	  _aslMale=m;
	}

  public void setASLFemale(String f)
	{
	  _aslFemale=f;
	}
	
  public String getASLMale()
	{
	  return _aslMale;
	}

  public String getASLFemale()
	{
	  return _aslFemale;
	}
	
  public void setShowConnect(boolean s)
	{
	  _showConnect=s;
	}
	
	public boolean getShowConnect()
	{
	  return _showConnect;
	}
	
  public void setShowChanlist(boolean s)
	{
	  _showChanlist=s;
	}
	
	public boolean getShowChanlist()
	{
	  return _showChanlist;
	}
	
  public void setShowHelp(boolean s)
	{
	  _showHelp=s;
	}
	
	public boolean getShowHelp()
	{
	  return _showHelp;
	}
	
  public void setShowAbout(boolean s)
	{
	  _showAbout=s;
	}
	
	public boolean getShowAbout()
	{
	  return _showAbout;
	}
	
	public void setASL(boolean s)
	{
	  _handleASL=s;
	}
	
	public boolean getASL()
	{
	  return _handleASL;
	}

  public void setQuitMessage(String str)
  {
    _quitMessage=str;
  }
  
  public String quitMessage()
  {
    return _quitMessage;
  }

  public void enableHighLight(boolean b)
  {
    _highLight=b;
  }
  
  public void setHighLightConfig(int color,boolean nick,Vector words)
  {
    _highLightColor=color;
    _highLightNick=nick;
    _highLightWords=new Vector();
    for(int i=0;i<words.size();i++) _highLightWords.insertElementAt(words.elementAt(i),_highLightWords.size());
  }
  
  public boolean highLight()
  {
    return _highLight;
  }
  
  public boolean highLightNick()
  {
    return _highLightNick && _highLight;
  }
  
  public Enumeration getHighLightWords()
  {
    if(!_highLight) return new Vector().elements();
    return _highLightWords.elements();
  }
  
  public int highLightColor()
  {
    return _highLightColor;
  }
  
  public URLHandler getURLHandler()
  {
    return _handler;
  }
  
  public void setTimeStamp(boolean ts)
  {
    _timeStamp=ts;
  }
  
  public boolean getTimeStamp()
  {
    return _timeStamp;
  }
  
  public void setSmileys(boolean sm)
  {
    _smileys=sm;
  }
  
  public boolean getSmileys()
  {
    return _smileys;
  }
  
  public TextProvider getTextProvider()
  {
    return _textProvider;
  }
  
  public IRCColorModel getIRCColorModel()
  {
    return _colorModel;
  }
  
  public String getText(int code)
  {
    return _textProvider.getString(code);
  }

  public Color getColor(int i)
  {
    return _colorModel.getColor(i);
  }

  public void openURL(String str)
  {
    _handler.openURL(str);
  }

}

