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

public abstract class DCCSource extends Source
{
  public DCCSource(IRCConfiguration config,DCCChatServer s)
  {
    super(config,s);
  }
  
  public DCCChatServer getDCCChatServer()
  {
    return (DCCChatServer)_server;
  }


}

