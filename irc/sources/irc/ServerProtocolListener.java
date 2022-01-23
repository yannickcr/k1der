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

public interface ServerProtocolListener
{
  public void replyReceived(String prefix,String id,String params[]);
  public void messageReceived(String prefix,String command,String params[]);
  public void connected();
  public void connectionFailed(String message);
  public void disconnected();
}

