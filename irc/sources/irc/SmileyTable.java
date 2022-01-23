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

import java.util.*;
import java.awt.*;

class SmileyItem
{
  public String match;
	public Image img;

  public SmileyItem(String match,Image img)
	{
	  this.match=match;
		this.img=img;
	}
}

public class SmileyTable
{
  private Vector _table;
	
	public SmileyTable()
	{
	  _table=new Vector();
	}
	
	public void addSmiley(String match,Image img)
	{
	  if(img!=null) _table.insertElementAt(new SmileyItem(match,img),_table.size());
	}
	
	public int getSize()
	{
	  return _table.size();
	}
	
	public String getMatch(int index)
	{
	  SmileyItem item=(SmileyItem)_table.elementAt(index);
		return item.match;
	}
	
	public Image getImage(int index)
	{
	  if(index<0) return null;
		if(index>=getSize()) return null;
	  SmileyItem item=(SmileyItem)_table.elementAt(index);
		return item.img;	
	}
}

