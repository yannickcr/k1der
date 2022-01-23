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

import java.awt.*;

public class IRCObject
{
  protected IRCConfiguration _ircConfiguration;
  
  public IRCObject(IRCConfiguration ircConfiguration)
  {
    _ircConfiguration=ircConfiguration;
  }
  
  public IRCConfiguration getIRCConfiguration()
  {
    return _ircConfiguration;
  }
  
  public String getText(int code)
  {
    return _ircConfiguration.getText(code);
  }
  
  public Color getColor(int i)
  {
    return _ircConfiguration.getColor(i);
  }
  
  public boolean getTimeStamp()
  {
    return _ircConfiguration.getTimeStamp();
  }
}

