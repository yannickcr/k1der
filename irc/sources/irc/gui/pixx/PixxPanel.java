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
import irc.*;

public class PixxPanel extends Panel
{
  public static final int COLOR_BLACK=PixxColorModel.COLOR_BLACK;
  public static final int COLOR_WHITE=PixxColorModel.COLOR_WHITE;
  public static final int COLOR_DARK_GRAY=PixxColorModel.COLOR_DARK_GRAY;
  public static final int COLOR_GRAY=PixxColorModel.COLOR_GRAY;
  public static final int COLOR_LIGHT_GRAY=PixxColorModel.COLOR_LIGHT_GRAY;
  public static final int COLOR_FRONT=PixxColorModel.COLOR_FRONT;
  public static final int COLOR_BACK=PixxColorModel.COLOR_BACK;
  public static final int COLOR_SELECTED=PixxColorModel.COLOR_SELECTED;
  public static final int COLOR_EVENT=PixxColorModel.COLOR_EVENT;
  public static final int COLOR_CLOSE=PixxColorModel.COLOR_CLOSE;
  public static final int COLOR_VOICE=PixxColorModel.COLOR_VOICE;
  public static final int COLOR_OP=PixxColorModel.COLOR_OP;
  public static final int COLOR_SEMIOP=PixxColorModel.COLOR_SEMIOP;
  public static final int COLOR_MALE=PixxColorModel.COLOR_MALE;
  public static final int COLOR_FEMEALE=PixxColorModel.COLOR_FEMEALE;
  public static final int COLOR_UNDEF=PixxColorModel.COLOR_UNDEF;
  
  protected IRCConfiguration _ircConfiguration;
  
  public PixxPanel(IRCConfiguration config)
  {
    _ircConfiguration=config;
  }
  
  public String getText(int code)
  {
    return _ircConfiguration.getText(code);
  }
  
  protected void drawSeparator(Graphics g,int x,int y,int w,int h)
  {
    g.setColor(new Color(0x868686));
    g.drawLine(x+0,y+0,x+w-1,y+0);
    g.drawLine(x+0,y+0,x+0,y+1);
    g.drawLine(x+w-1,y+0,x+w-1,y+1);
    g.setColor(Color.black);
    g.drawLine(x+1,y+1,x+w-2,y+1);
    
    g.setColor(new Color(0x868686));
    g.drawLine(x+0,y+0,x+0,y+h-1);
    g.setColor(Color.black);
    g.drawLine(x+1,y+1,x+1,y+h-1);
    
    g.setColor(new Color(0xD7D3CB));
    g.drawLine(x+0,y+h-1,x+w-1,y+h-1);
    
    g.setColor(new Color(0xD7D3CB));
    g.drawLine(x+w-1,y+1,x+w-1,y+h-1);	    
  }
  
  public Color getColor(int col)
  {
    return _ircConfiguration.getIRCColorModel().getColor(col);
  }
  
  public IRCColorModel getIRCColorModel()
  {
    return _ircConfiguration.getIRCColorModel();
  }
}

