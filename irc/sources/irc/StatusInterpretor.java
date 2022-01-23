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

public class StatusInterpretor extends IRCInterpretor
{
  public StatusInterpretor(IRCConfiguration config,CTCPFilter filter)
  {
    super(config,filter);
  }

  protected void handleCommand(Source source,String cmd,String[] parts,String[] cumul)
  {
    Server server=source.getServer();
    if(cmd.equals("leave"))
    {
      source.report("/leave : "+getText(TextProvider.INTERPRETOR_BAD_CONTEXT));
    }
    else
    {
      super.handleCommand(source,cmd,parts,cumul);
    }
  }
}

