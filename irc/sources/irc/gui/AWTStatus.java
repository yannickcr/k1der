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
import irc.gui.pixx.*;

public class AWTStatus extends AWTSource implements StatusListener
{
  
  private AWTSource _activeSource;
  
  public AWTStatus(IRCConfiguration config,Status s)
  {
    super(config,s);
    setActiveSource(this);
    s.addStatusListener(this);
    title();
  }

  public void setActiveSource(AWTSource a)
  {
    _activeSource=a;
  }
  
  public AWTSource getActiveSource()
  {
    return _activeSource;
  }

  private void title()
  {
    setTitle(_source.getName()+": "+ ((Status)_source).getNick()+" ["+((Status)_source).getMode()+"]");
  }
  
  public String getShortTitle()
  {
	  if(_ircConfiguration.getInfo())
      return getText(TextProvider.SOURCE_INFO);
		else
      return getText(TextProvider.SOURCE_STATUS);
  }

  public void actionPerformed(ActionEvent e)
  {
    _source.sendString(_textField.getText());
    _textField.setText("");
  }
  
  public void noticeReceived(String from,String msg)
  {
    _activeSource.print("-"+from+"- "+msg,5);
  }
 
  public void leave()
  {
  }
  
  public void nickChanged(String nick)
  {
    print("*** "+getText(TextProvider.SOURCE_YOUR_NICK)+" "+nick,3);
    title();
  }
  
  public void modeChanged(String mode)
  {
    print("*** "+getText(TextProvider.SOURCE_YOUR_MODE)+" "+mode,3);
    title();
  }
  
}

