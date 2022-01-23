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

public class ChannelInterpretor extends IRCInterpretor
{
  public ChannelInterpretor(IRCConfiguration config,CTCPFilter filter)
  {
    super(config,filter);
  }	
  
  protected void handleCommand(Source source,String cmd,String[] parts,String[] cumul)
  {
    try
    {
      Server server=source.getServer();
      if(cmd.equals("part"))
      {
        if(parts.length==1)
        {
          sendString(source,"/part "+source.getName());
        }
        else
        {			
          if(isChannel(parts[1]))
            super.handleCommand(source,cmd,parts,cumul);
          else
            sendString(source,"/part "+source.getName()+" "+cumul[1]);
        }
      }
      else if(cmd.equals("hop"))
      {
        sendString(source,"/part");
        sendString(source,"/join "+source.getName());
      }
      
   /*   else if(cmd.equals("notice"))
      {
        test(cmd,parts,1);
        if(isChannel(parts[1]))
          super.handleCommand(source,cmd,parts,cumul);
        else
          sendString(source,"/notice "+source.getName()+" "+cumul[1]);
      }*/
      else if(cmd.equals("onotice"))
      {
        test(cmd,parts,1);
        if(isChannel(parts[1]))
          super.handleCommand(source,cmd,parts,cumul);
        else				
          sendString(source,"/onotice "+source.getName()+" "+cumul[1]);
      }
      else
      {
        super.handleCommand(source,cmd,parts,cumul);
      }
    }
    catch(NotEnoughParameters ex)
    {
      source.report(ex.getMessage()+" : "+getText(TextProvider.INTERPRETOR_INSUFFICIENT_PARAMETERS));
    }
    
  }
}

