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

public abstract class IRCSource extends Source
{
  public IRCSource(IRCConfiguration config,IRCServer s)
  {
    super(config,s);
  }

  public IRCServer getIRCServer()
  {
    return (IRCServer)_server;
  }  

}

