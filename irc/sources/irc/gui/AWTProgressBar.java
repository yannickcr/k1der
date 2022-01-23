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

package irc.gui;

import java.awt.*;
import irc.*;

public class AWTProgressBar extends Panel
{

  private double _v;
  private Color _c;


  public void setValue(double v)
  {
    _v=v;
  }

  public void setColor(Color c)
  {
    _c=c;
  }

  public void paint(Graphics g)
  {
    super.paint(g);
    int w=getSize().width;
    int h=getSize().height;
    
    int pos=(int)(_v*w);
    g.setColor(_c);
    g.fillRect(0,0,pos,h);
    g.setColor(Color.white);
    g.fillRect(pos,0,w-pos,h);
  }

}

