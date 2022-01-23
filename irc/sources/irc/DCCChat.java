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

public class DCCChat extends DCCSource
{
  private String _nick;

  public DCCChat(IRCConfiguration config,DCCChatServer s,String nick)
  {
    super(config,s);
    _nick=nick;
  }

  public String getName()
  {
    return _nick;
  }
  public boolean talkable()
  {
    return true;
  }

  public void leave()
  {
    getDCCChatServer().close();
  }

}

