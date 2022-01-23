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

public class DCCChatInterpretor extends BasicInterpretor
{

  public DCCChatInterpretor(IRCConfiguration config,CTCPFilter filter)
  {
    super(config,filter);
  }

  protected void handleCommand(Source source,String cmd,String[] parts,String[] cumul)
  {
    Server server=source.getServer();
    if(cmd.equals("query"))
    {
      source.report("/query : "+getText(TextProvider.INTERPRETOR_BAD_CONTEXT));
    }
    else if(cmd.equals("ctcp"))
    {
      source.report("*** "+getText(TextProvider.INTERPRETOR_CANNOT_CTCP_IN_DCCCHAT));
    }
    else
    {
      super.handleCommand(source,cmd,parts,cumul);
    }
  }

  protected void say(Source source,String str)
  {
    Server server=source.getServer();
    if(source.talkable())
    {
      source.messageReceived(server.getNick(),str);
      server.say(source.getName(),str);
    }
    else
    {
      source.report(getText(TextProvider.INTERPRETOR_NOT_ON_CHANNEL));
    }
  }
}

