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

import java.util.*;

class NotEnoughParameters extends Exception
{
  public NotEnoughParameters(String msg)
  {
    super(msg);
  }
}

public class IRCInterpretor extends BasicInterpretor
{
  
  public IRCInterpretor(IRCConfiguration config,CTCPFilter filter)
  {
    super(config,filter);
  }
  
  protected void handleCommand(Source source,String cmd,String[] parts,String[] cumul)
  {
    try
    {
      
      IRCServer server=(IRCServer)source.getServer();
      if(cmd.equals("amsg"))
      {
        test(cmd,parts,1);
        Enumeration e=server.getChannels();
        while(e.hasMoreElements())
        {
          ((Channel)e.nextElement()).sendString(cumul[1]);
        }
      }
      else if(cmd.equals("ame"))
      {
        test(cmd,parts,1);			
        Enumeration e=server.getChannels();
        while(e.hasMoreElements())
        {
          ((Channel)e.nextElement()).sendString("/me "+cumul[1]);
        }
      }			
      else if(cmd.equals("query"))
      {
        test(cmd,parts,1);
        Query q=server.getQuery(parts[1],true);
        q.activate();
      }
			else if(cmd.equals("ignore"))
			{
			  test(cmd,parts,1);
				if(!server.ignore(parts[1]))
				{
			    server.addIgnore(parts[1]);
					source.report(getText(TextProvider.INTERPRETOR_IGNORE_ON)+" : "+parts[1]);
				}
			}
			else if(cmd.equals("unignore"))
			{
			  test(cmd,parts,1);
				if(server.ignore(parts[1]))
				{
				  server.removeIgnore(parts[1]);
					source.report(getText(TextProvider.INTERPRETOR_IGNORE_OFF)+" : "+parts[1]);
				}
			}
      else if(cmd.equals("server"))
      {
        test(cmd,parts,1);
        int port=6667;
        if(parts.length>2) port=(new Integer(parts[2])).intValue();
        String host=parts[1];
        server.connect(host,port);
      }
      else if(cmd.equals("disconnect"))
      {
        server.disconnect();
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

