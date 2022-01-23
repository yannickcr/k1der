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
import java.awt.event.*;
import java.util.*;

public class AWTIrcTextField extends TextField implements ActionListener,KeyListener
{
  private int _index;
  private int _tabCount;
  private String _completing;
  private String[] _completeList;
  private Vector _historic;
  private Hashtable _listeners;

  public AWTIrcTextField()
  {
    super();
    _completeList=new String[0];
    setFont(new Font("Monospaced",Font.PLAIN,12));
    _tabCount=0;
    _completing="";
    _index=0;
    _listeners=new Hashtable();
    _historic=new Vector();
    super.addActionListener(this);
    addKeyListener(this);		
  }

  public void addActionListener(ActionListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeActionListener(ActionListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerActionListeners(ActionEvent ae)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ActionListener lis=(ActionListener)e.nextElement();
      lis.actionPerformed(ae);
    }
  }

  public void setCompleteList(String[] list)
  {
    _completeList=new String[list.length];
    for(int i=0;i<list.length;i++) _completeList[i]=list[i];
  }

  private void type(int c)
  {
    int selA=getSelectionStart();
    int selB=getSelectionEnd();
    String t=getText();
    if(selA!=selB)
    {
      t=t.substring(0,selA)+t.substring(selB);
      setCaretPosition(selA);
    }
    int p=getCaretPosition();
    String before=t.substring(0,p);
    String after=t.substring(p);
    setText(before+((char)c)+after);
    setCaretPosition(p+1);
  }

  private void getCompleting()
  {
    _completing="";
    String t=getText();
    
    if((getCaretPosition()==t.length()) || (t.charAt(getCaretPosition())==' '))
    {
      for(int i=getCaretPosition()-1;i>=0;i--)
      {
        if(t.charAt(i)==' ') break;
        _completing=t.charAt(i)+_completing;
      }
    }
  }

  private void complete()
  {
    if(_completing.length()==0) return;
    String begin=_completing.toLowerCase();
    Vector match=new Vector();
    for(int i=0;i<_completeList.length;i++)
    {
      if(_completeList[i].toLowerCase().startsWith(begin))
      {
        match.insertElementAt(_completeList[i],match.size());
      }
    }
    if(match.size()>0)
    {
      String completeItem=(String)match.elementAt(_tabCount%match.size());
      int p=getCaretPosition();
      String t=getText();
      String before=t.substring(0,p);
      String after=t.substring(p);
      //supprimer le dernier mot de before (garder l'espace)
      int space=before.lastIndexOf(' ');
      if(space==-1)
        before="";
      else
        before=before.substring(0,space+1);
      before+=completeItem;
      setText(before+after);
      setCaretPosition(before.length());
    }
  }

  public void keyPressed(KeyEvent e)
  {
    if(e.getKeyCode()==KeyEvent.VK_TAB)
    {
      if(_tabCount==0) getCompleting();
      complete();
      _tabCount++;
    }
    else
    {
      _tabCount=0;
    }
    if(e.getKeyCode()==KeyEvent.VK_UP)
    {
      if(_historic.size()>0)
      {
        _index--;
        if(_index==-1) _index=0;
        setText((String)_historic.elementAt(_index));
      }
    }
    else if(e.getKeyCode()==KeyEvent.VK_DOWN)
    {
      if(_historic.size()>0)
      {
        _index++;
        if(_index>_historic.size()) _index=_historic.size();
        if(_index<_historic.size())
        {
          setText((String)_historic.elementAt(_index));
        }
        else
        {
          setText("");
        }
      }
    }
    else if((e.getKeyCode()==e.VK_K) && e.isControlDown())
    {
      type(3);
    }
    else if((e.getKeyCode()==e.VK_B) && e.isControlDown())
    {
      type(2);
    }
    else if((e.getKeyCode()==e.VK_U) && e.isControlDown())
    {
      type(31);
    }
    else if((e.getKeyCode()==e.VK_R) && e.isControlDown())
    {
      type(22);
    }
    else if((e.getKeyCode()==e.VK_O) && e.isControlDown())
    {
      type(15);
    }
 }
  
  public void keyReleased(KeyEvent e)
  {
  }
  
  public void keyTyped(KeyEvent e)
  {
  }
  
  public void actionPerformed(ActionEvent e)
  {
    if(getText().length()>0)
    {
      _historic.insertElementAt(getText(),_historic.size());
      _index=_historic.size();
    }
    triggerActionListeners(e);
    setText("");
  } 
}

