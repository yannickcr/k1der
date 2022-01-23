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
import irc.security.*;

public class BasicInterpretor extends IRCObject implements Interpretor
{
  protected StringParser _parser;
  protected CTCPFilter _filter;
  
  public BasicInterpretor(IRCConfiguration config,CTCPFilter filter)
  {
    super(config);
    _filter=filter;
    _parser=new StringParser();
  }
  
  protected void test(String cmd,String[] parts,int params) throws NotEnoughParameters
  {
    if(parts.length<=params) throw new NotEnoughParameters(cmd);
  }
  
  protected boolean isChannel(String name)
  {
    if(name.length()==0) return false;
    if(name.startsWith("@")) name=name.substring(1);
    if(name.length()==0) return false;
    char f=name.charAt(0);
    return (f=='!') || (f=='+') || (f=='#') || (f=='&');
  }

  
  protected void handleCommand(Source source,String cmd,String[] parts,String[] cumul)
  {
    try
    {
      Server server=source.getServer();
      if(cmd.equals("config"))
      {
        test(cmd,parts,1);
        if(parts[1].toLowerCase().equals("timestamp"))
        {
          test(cmd,parts,2);
          if(parts[2].toLowerCase().equals("on") || parts[2].toLowerCase().equals("1") || parts[2].toLowerCase().equals("true"))
          {
            _ircConfiguration.setTimeStamp(true);
            source.report(getText(TextProvider.INTERPRETOR_TIMESTAMP_ON));
          }
          else
          {
            _ircConfiguration.setTimeStamp(false);
            source.report(getText(TextProvider.INTERPRETOR_TIMESTAMP_OFF));
          }
        }
        if(parts[1].toLowerCase().equals("smileys"))
        {
          test(cmd,parts,2);
          if(parts[2].toLowerCase().equals("on") || parts[2].toLowerCase().equals("1") || parts[2].toLowerCase().equals("true"))
          {
            _ircConfiguration.setSmileys(true);
            source.report(getText(TextProvider.INTERPRETOR_SMILEYS_ON));
          }
          else
          {
            _ircConfiguration.setSmileys(false);
            source.report(getText(TextProvider.INTERPRETOR_SMILEYS_OFF));
          }
        }				
        else
        {
          source.report(parts[1]+" : "+getText(TextProvider.INTERPRETOR_UNKNOWN_CONFIG));				
        }				
      }
      else if(cmd.equals("topic"))
      {
        test(cmd,parts,2);
        server.execute("TOPIC "+parts[1]+" :"+cumul[2]);
      }
      else if(cmd.equals("url"))
      {
        test(cmd,parts,1);
        _ircConfiguration.openURL(cumul[1]);
      }
      else if(cmd.equals("clear"))
      {
        source.clear();
      }
      else if(cmd.equals("away"))
      {
        if(parts.length<=1)
          server.execute("AWAY");
        else
          server.execute("AWAY :"+cumul[1]);
      }			
      else if(cmd.equals("quit"))
      {
        if(parts.length>1)
          server.execute("QUIT :"+cumul[1]);
        else
          server.execute("QUIT");
      }
      else if(cmd.equals("leave"))
      {
        source.leave();
      }
      else if(cmd.equals("part"))
      {
        test(cmd,parts,1);
        if(parts.length==2)
        {
          server.execute("PART "+parts[1]);
        }
        else
        {
          server.execute("PART "+parts[1]+" :"+cumul[2]);
        }
      }
      else if(cmd.equals("kick"))
      {
        test(cmd,parts,2);
        if(parts.length==3)
        {
          server.execute("KICK "+parts[1]+" "+parts[2]);
        }
        else
        {
          server.execute("KICK "+parts[1]+" "+parts[2]+" :"+cumul[3]);				
        }
      }
      else if(cmd.equals("me"))
      {
        test(cmd,parts,1);
        if(source.talkable())
        {
          _filter.action(source.getServer(),source.getName(),cumul[1]);
          source.action(server.getNick(),cumul[1]);
        }
        else
        {
          source.report(getText(TextProvider.INTERPRETOR_NOT_ON_CHANNEL));
        }
        
      }
      else if(cmd.equals("msg"))
      {
        test(cmd,parts,2);
        server.say(parts[1],cumul[2]);
      }
      else if(cmd.equals("notice"))
      {
        test(cmd,parts,2);
        server.execute("NOTICE "+parts[1]+" :"+cumul[2]);
        source.report("-> -"+parts[1]+"- "+cumul[2]);
      }
      else if(cmd.equals("onotice"))
      {
        test(cmd,parts,2);
        sendString(source,"/notice @"+parts[1]+" "+cumul[2]);
      }
      else if(cmd.equals("join"))
      {
        test(cmd,parts,1);
        String chan=parts[1];
        if(!chan.startsWith("#") && !chan.startsWith("!") && !chan.startsWith("&") && !chan.startsWith("@"))
          chan='#'+chan;
        server.execute("JOIN "+chan);
      }
      else if(cmd.equals("j"))
      {
        sendString(source,"/join "+cumul[1]);
      }
      else if(cmd.equals("ping"))
      {
        test(cmd,parts,1);
        sendString(source,"/ctcp ping "+cumul[1]);
      }
      else if(cmd.equals("dcc"))
      {
        test(cmd,parts,1);
        sendString(source,"/ctcp dcc "+cumul[1]);
      }
      else if(cmd.equals("ctcp"))
      {
        test(cmd,parts,1);
        if(parts[1].toLowerCase().equals("ping"))
        {
          test(cmd,parts,2);
          _filter.ping(source.getServer(),parts[2]);
        }
        else if(parts[1].toLowerCase().equals("dcc"))
        {
          test(cmd,parts,2);
          if(parts[2].toLowerCase().equals("chat"))
          {
            test(cmd,parts,3);
            _filter.chat(source.getServer(),parts[3]);
          }
          else if(parts[2].toLowerCase().equals("send"))
          {
            test(cmd,parts,4);
            String file=cumul[4];
            _filter.sendFile(source.getServer(),parts[3],file);
          }
          else
          {
            source.report(parts[2]+" : "+getText(TextProvider.INTERPRETOR_UNKNOWN_DCC));
          }
        }
        else if(parts[1].toLowerCase().equals("raw"))
        {
          test(cmd,parts,3);
          _filter.genericSend(source.getServer(),parts[2],cumul[3]);
        }
        else
        {
          test(cmd,parts,2);
          _filter.genericSend(source.getServer(),parts[2],parts[1]);
        }
      }
      else if(cmd.equals("raw"))
			{
			  server.execute(cumul[1]);
			}
      else
      {
        server.execute(cumul[0]);
      }
    }
    catch(NotEnoughParameters ex)
    {
      source.report(ex.getMessage()+" : "+getText(TextProvider.INTERPRETOR_INSUFFICIENT_PARAMETERS));
    }
  }
  
  public void sendString(Source source,String str)
  {
    if(str.length()==0) return;
    
    if(str.startsWith("/"))
    {
      str=str.substring(1);
      String[] parts=_parser.parseString(str);
      String[] cumul=new String[parts.length];
      for(int i=0;i<cumul.length;i++)
      {
        cumul[i]="";
        for(int j=i;j<parts.length;j++) cumul[i]+=parts[j]+" ";
        cumul[i]=StringParser.trim(cumul[i]);
      }   
      String cmd=parts[0];
      handleCommand(source,cmd.toLowerCase(),parts,cumul);
    }
    else
    {
      say(source,str);		
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

