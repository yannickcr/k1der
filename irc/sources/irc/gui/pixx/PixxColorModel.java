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

public class PixxColorModel implements IRCColorModel
{
  public static final int COLOR_BLACK=0;
  public static final int COLOR_WHITE=1;
  public static final int COLOR_DARK_GRAY=2;
  public static final int COLOR_GRAY=3;
  public static final int COLOR_LIGHT_GRAY=4;
  public static final int COLOR_FRONT=5;
  public static final int COLOR_BACK=6;
  public static final int COLOR_SELECTED=7;
  public static final int COLOR_EVENT=8;
  public static final int COLOR_CLOSE=9;
  public static final int COLOR_VOICE=10;
  public static final int COLOR_OP=11;
  public static final int COLOR_SEMIOP=12;
  public static final int COLOR_MALE=13;
  public static final int COLOR_FEMEALE=14;
  public static final int COLOR_UNDEF=15;

  private Color[] _colors;

  public PixxColorModel()
  {
    Color[] cols=new Color[16];
    cols[0]=Color.black;
    cols[1]=Color.white;
    cols[2]=new Color(0x868686);
    cols[3]=Color.gray;
    cols[4]=new Color(0xD0D0D0);
    cols[5]=new Color(0x336699);
    cols[6]=new Color(0x084079);
    cols[7]=new Color(0x003167);
    cols[8]=new Color(0xa40000);
    cols[9]=new Color(0x4B8ECE);
    cols[10]=new Color(0x008000);
    cols[11]=new Color(0x336699);
    cols[12]=new Color(0x336699);
    cols[13]=new Color(0x4040ff);
    cols[14]=new Color(0xff40ff);
    cols[15]=new Color(0x336699);
    init(cols);
  }

  public void setColor(int i,Color c)
  {
    if((i>=0) && (i<_colors.length)) _colors[i]=c;
  }

  public int getColorCount()
  {
    return _colors.length;
  }

  private Color computeColor(int r,int g,int b,int i)
  {
    r*=i;
    g*=i;
    b*=i;
    r/=256;
    g/=256;
    b/=256;
    if(r>255) r=255;
    if(g>255) g=255;
    if(b>255) b=255;
    return new Color(r,g,b);
  }

  public PixxColorModel(int r,int g,int b)
  {
    Color[] cols=new Color[16];
    cols[0]=Color.black;
    cols[1]=Color.white;
    cols[2]=new Color(0x868686);
    cols[3]=Color.gray;
    cols[4]=new Color(0xD0D0D0);
    cols[8]=new Color(0xa40000);
    cols[10]=new Color(0x008000);
    cols[13]=new Color(0x4040ff);
    cols[14]=new Color(0xff40ff);
  
    cols[5]=computeColor(r,g,b,0x66);
    cols[6]=computeColor(r,g,b,0x55);
    cols[7]=computeColor(r,g,b,0x4B);
    cols[9]=computeColor(r,g,b,0x80);
    cols[11]=computeColor(r,g,b,0x66);
    cols[12]=computeColor(r,g,b,0x66);
    cols[15]=computeColor(r,g,b,0x66);
    init(cols);	
  }

  public PixxColorModel(Color[] cols)
  {
    init(cols);
  }

  private void init(Color[] cols)
  {
    _colors=new Color[cols.length];
    for(int i=0;i<cols.length;i++) _colors[i]=cols[i];	
  }

  public Color getColor(int i)
  {
    return _colors[i];
  }
}

