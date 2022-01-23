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

public class NullCTCPFilter implements CTCPFilter
{
  public NullCTCPFilter()
  {
  }

  public void perform(String nick,Source source,String msg)
  {
  }
  
  public void action(Server s,String destination,String msg)
  {
  }
  
  public void ping(Server s,String nick)
  {
  }
  
  public void chat(Server s,String nick)
  {
  }
  
  public void sendFile(Server s,String nick,String file)
  {
  }
  
  public void genericSend(Server s,String nick,String message)
  {
  }
  
  public void CTCPReply(String nick,Source source,String msg)
  {
  }
}

