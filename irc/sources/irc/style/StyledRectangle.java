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

class StyledRectangle
{
  public int x;
  public int y;
  public int width;
  public int height;
  
  public StyledRectangle(int x,int y,int w,int h)
  {
    this.x=x;
    this.y=y;
    width=w;
    height=h;
  }
  
  public boolean equals(Object o)
  {
    if(!(o instanceof StyledRectangle)) return false;
    StyledRectangle r=(StyledRectangle)o;
    return (r.x==x) && (r.y==y) && (r.width==width) && (r.height==height);	
  }
  
  public int hashCode()
  {
    return x+y+width+height;
  }
  
  public boolean contains(int px,int py)
  {
    return (px>=x) && (py>=y) && (px<x+width) && (py<y+height);
  }

}

