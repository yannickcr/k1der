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

public class PixxSeparator extends Panel
{
  private int _type;
  public static final int BORDER_UP=0;
  public static final int BORDER_DOWN=1;
  public static final int BORDER_LEFT=2;
  public static final int BORDER_RIGHT=3;

  public PixxSeparator(int type)
  {
    super();
    _type=type;
  }

  public Dimension getPreferredSize()
  {
    
    switch(_type)
    {
      case BORDER_UP:
        return new Dimension(16,2);
      case BORDER_DOWN:
        return new Dimension(16,1);
      case BORDER_LEFT:
        return new Dimension(2,16);
      case BORDER_RIGHT:
        return new Dimension(1,16);
      default:
        return new Dimension(16,16);
    
    }
  }

  public void paint(Graphics g)
  {
    update(g);
  }
  
  public void update(Graphics g)
  {
    int w=getSize().width;
    int h=getSize().height;
    switch(_type)
    {
      case BORDER_UP:
        g.setColor(new Color(0x868686));
        g.drawLine(0,0,w-1,0);
        g.drawLine(0,0,0,1);
        g.drawLine(w-1,0,w-1,1);
        g.setColor(Color.black);
        g.drawLine(1,1,w-2,1);
        break;
      case BORDER_LEFT:
        g.setColor(new Color(0x868686));
        g.drawLine(0,0,0,h-1);
        g.setColor(Color.black);
        g.drawLine(1,0,1,h-1);
        break;
      case BORDER_DOWN:
        g.setColor(new Color(0xD7D3CB));
        g.drawLine(0,h-1,w-1,h-1);
        break;
      case BORDER_RIGHT:
        g.setColor(new Color(0xD7D3CB));
        g.drawLine(w-1,0,w-1,h-1);
        break;
    }
  }
}

