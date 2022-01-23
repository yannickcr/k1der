package irc.tree;

import java.util.*;

class StringNode
{
  public StringNode left;
  public StringNode right;
  public String string;
  private StringComparator _comparator;
  
  public StringNode(String str,StringComparator comparator)
  {
    _comparator=comparator;
    string=str;
    left=new StringNode(comparator);
    right=new StringNode(comparator);
  }
  
  public StringNode(StringComparator comparator)
  {
    _comparator=comparator;
    string="";
    left=null;
    right=null;
  }
  
  public boolean external()
  {
    return((left==null) || (right==null));
  }
  
  public StringNode remove(String item) throws Exception
  {
    if(external()) throw new Exception();
    
    int compare=_comparator.compareStrings(item,string);
    if(compare==0)
    {
      if(left.external()) return right;
      if(right.external()) return left;
      return right.add(left);
    }
    else if(compare<0)
    {
      left=left.remove(item);
      return this;
    }
    else
    {
      right=right.remove(item);
      return this;
    }
  }
  
  
  public StringNode add(StringNode n) throws Exception
  {
    if(external()) return n;

    int compare=_comparator.compareStrings(n.string,string);
    if(compare==0)
    {
      throw new Exception();
    }
    else if(compare<0)
    {
      left=left.add(n);
      return this;
    }
    else
    {
      right=right.add(n);
      return this;
    }		
  }
  
  public void inorder(TreeTraversalListener lis,Object param)
  {
    if(external()) return;
    left.inorder(lis,param);
    lis.nextItem(string,param);
    right.inorder(lis,param);
  }
}

public class SortedStringList implements TreeTraversalListener
{
  private StringNode _root;
  private Vector _items;
  private StringComparator _comparator;
  private boolean _upToDate;
  private int _size;
  
  public SortedStringList(StringComparator comparator)
  {
    _comparator=comparator;
    _root=new StringNode(_comparator);
    _items=new Vector();
    _upToDate=false;
    _size=0;
  }
  
  public int getSize()
  {
    return _size;
  }
  
  public void add(String str)
  {
    try
    {
      _root=_root.add(new StringNode(str,_comparator));
      _size++;
    }
    catch(Exception e)
    {
    }
    _upToDate=false;
  }
  
  public void remove(String str)
  {	
    try
    {
      _root=_root.remove(str);
      _size--;
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
  
  public void nextItem(String item,Object param)
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
  
  public String getItemAt(int i)
  {
    if(!_upToDate) computeVector();
    return (String)_items.elementAt(i);	
  }
  
  public void getItems(TreeTraversalListener lis,Object param)
  {
    lis.begin(param);
    _root.inorder(lis,param);
    lis.end(param);
  }

}

