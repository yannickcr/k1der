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
import irc.*;

public class AWTDCCFile implements DCCFileListener,WindowListener
{
  protected DCCFile _file;
  protected Frame _frame;
  protected AWTProgressBar _bar;
  private boolean _closed;
  private boolean _activated;
  private IRCConfiguration _ircConfiguration;

  public AWTDCCFile(IRCConfiguration config,DCCFile file)
  {
    _ircConfiguration=config;
    _activated=false;
    _closed=false;
    _file=file;
    _file.addDCCFileListener(this);
    
    String str="";
    if(file.isDownloading())
      str=getText(TextProvider.GUI_RETREIVING_FILE)+" "+_file.getName()+" ("+_file.getSize()+" "+getText(TextProvider.GUI_BYTES)+")";
    else
      str=getText(TextProvider.GUI_SENDING_FILE)+" "+_file.getName()+" ("+_file.getSize()+" "+getText(TextProvider.GUI_BYTES)+")";
    
    
    Label label=new Label(str);		
    
    _frame=new Frame();
    _frame.setBackground(Color.white);
    
    _frame.setLayout(new BorderLayout());
    _frame.addWindowListener(this);
    
    _bar=new AWTProgressBar();
    _frame.add(label,"North");
    _frame.add(_bar,"Center");
    
    _frame.setTitle(_file.getName());
    _frame.setSize(400,80);
    activate();
  }
 
  public String getText(int code)
  {
    return _ircConfiguration.getText(code);
  }

  public DCCFile getFile()
  {
    return _file;
  }
  
  public void activate()
  {
    if(_activated) return;
    _activated=true;
    if(_closed) return;
    _frame.show();
  }
  
  public void close()
  {
    _frame.hide();
    _frame.dispose();
    _closed=true;
  }

  public void transmitted(int count)
  {
    activate();
    if((count&32767)==0)
    {
      double pc=count;
      pc/=_file.getSize();
      _bar.setColor(Color.blue);
      _bar.setValue(pc);
      _bar.repaint();
      try
      {
        Thread.sleep(10);
      }
      catch(Exception e)
      {
      }
    }
  }
  
  public void finished()
  {
    activate();	
    _frame.setTitle(_file.getName()+" "+getText(TextProvider.GUI_TERMINATED));
    _bar.setColor(Color.green);
    _bar.repaint();
  }
  
  public void failed()
  {
    activate();
    _frame.setTitle(_file.getName()+" "+getText(TextProvider.GUI_FAILED));
    _bar.setColor(Color.red);
    _bar.repaint();
  
  }

  public void windowActivated(WindowEvent e) {}
  public void windowClosed(WindowEvent e) {}
  public void windowClosing(WindowEvent e)
  {
    _file.leave();
  }
  
  public void windowDeactivated(WindowEvent e) {}
  public void windowDeiconified(WindowEvent e) {}
  public void windowIconified(WindowEvent e) {}
  public void windowOpened(WindowEvent e) {}

}

