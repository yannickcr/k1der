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

public interface ChannelListener extends SourceListener
{
  public void nickSet(String nicks[],String modes[]);
  public void nickJoin(String nick,String mode);
  public void nickQuit(String nick,String reason);
  public void nickPart(String nick,String reason);
  public void nickKick(String nick,String by,String reason);
  public void topicChanged(String topic,String by);
  public void modeApply(String mode,String from);
  public void nickModeApply(String nick,String mode,String from);
  public void noticeReceived(String source,String message);
  public void nickChanged(String oldNick,String newNick);
  public void nickWhoisUpdated(String nick,String whois);
}

