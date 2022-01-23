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

import java.awt.*;
import java.awt.geom.*;

public class DrawResultItem
{
  public StyledRectangle rectangle;
  public String word;
  public String type;
  
  public boolean equals(Object o)
  {
    if(!(o instanceof DrawResultItem)) return false;
    DrawResultItem i=(DrawResultItem)o;
    return i.rectangle.equals(rectangle) && i.word.equals(word) && i.type.equals(type);
  }
  
  public int hashCode()
  {
    return rectangle.hashCode()+word.hashCode()+type.hashCode();
  }
}

