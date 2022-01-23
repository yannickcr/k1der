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

package irc.tree;

import java.util.*;

class GroupItem
{
  private Hashtable t;
  
  public GroupItem(Object o)
  {
    t=new Hashtable();
    add(o);
  }
  
  public void add(Object o)
  {
    t.put(o,o);
  }	
  
  public void remove(Object o)
  {
    t.remove(o);
  }
  
  public int size()
  {
    return t.size();
  }
  
  public Object getFirstItem()
  {
    return t.elements().nextElement();
  }
  
  public Enumeration elements()
  {
    return t.elements();
  }
}

class TreeNode
{
  public TreeNode left;
  public TreeNode right;
  public GroupItem item;
  private Comparator _comparator;
  
  public TreeNode(GroupItem itm,Comparator comparator)
  {
    _comparator=comparator;
    item=itm;
    left=new TreeNode(comparator);
    right=new TreeNode(comparator);
  }
  
  public TreeNode(Comparator comparator)
  {
    _comparator=comparator;
    item=null;
    left=null;
    right=null;
  }
  
  public boolean external()
  {
    return((left==null) || (right==null));
  }
  
  public TreeNode remove(Object itm) throws Exception
  {
    if(external()) throw new Exception();

    int compare=_comparator.compare(itm,item.getFirstItem());
    if(compare==0)
    {
      item.remove(itm);
      if(item.size()==0)
      {
        if(left.external()) return right;
        if(right.external()) return left;
        return right.addTree(left);
      }
      return this;
    }
    else if(compare<0)
    {
      left=left.remove(itm);
      return this;
    }
    else
    {
      right=right.remove(itm);
      return this;
    }
  }
  
  private TreeNode addTree(TreeNode tree) throws Exception
  {
    if(external())
    {
      return tree;
    }
    int compare=_comparator.compare(tree.item.getFirstItem(),item.getFirstItem());
    if(compare==0)
    {
      //n'est pas supposé arriver
      throw new Exception();
    }
    else if(compare<0)
    {
      left=left.addTree(tree);
      return this;
    }
    else
    {
      right=right.addTree(tree);
      return this;
    }		
  }
  
  public TreeNode add(Object itm) throws Exception
  {
    if(external())
    {
      return new TreeNode(new GroupItem(itm),_comparator);
    }

    int compare=_comparator.compare(itm,item.getFirstItem());
    if(compare==0)
    {
      item.add(itm);
      return this;
    }
    else if(compare<0)
    {
      left=left.add(itm);
      return this;
    }
    else
    {
      right=right.add(itm);
      return this;
    }		
  }
  
  public void inorder(TreeTraversalListener lis,Object param)
  {
    if(external()) return;
    left.inorder(lis,param);
    Enumeration e=item.elements();
    while(e.hasMoreElements()) lis.nextItem(e.nextElement(),param);
    right.inorder(lis,param);
  }
}

public class SortedList implements TreeTraversalListener
{
  private TreeNode _root;
  private Vector _items;
  private Comparator _comparator;
  private boolean _upToDate;
  
  public SortedList(Comparator comparator)
  {
    _comparator=comparator;
    _root=new TreeNode(_comparator);
    _items=new Vector();
    _upToDate=false;
  }
  
  public int getSize()
  {
    if(!_upToDate) computeVector();
    return _items.size();
  }
  
  public void add(Object item)
  {
    try
    {
      _root=_root.add(item);
    }
    catch(Exception e)
    {
    }
    _upToDate=false;
  }
  
  public void remove(Object item)
  {	
    try
    {
      _root=_root.remove(item);
    }
    catch(Exception e)
    {
    }
    _upToDate=false;
  }
 
  public void begin(Object param)
  {
    _items=new Vector();	
  }
  
  public void nextItem(Object item,Object param)
  {
    _items.insertElementAt(item,_items.size());
  }
  
  public void end(Object param)
  {
    _upToDate=true;
  }
  
  private void computeVector()
  {
    getItems(this,null);
  }
  
  public Enumeration getItems()
  {
    if(!_upToDate) computeVector();
    return _items.elements();
  }
  
  public Object getItemAt(int i)
  {
    if(!_upToDate) computeVector();
    return _items.elementAt(i);	
  }
  
  public void getItems(TreeTraversalListener lis,Object param)
  {
    lis.begin(param);
    _root.inorder(lis,param);
    lis.end(param);
  }

}

