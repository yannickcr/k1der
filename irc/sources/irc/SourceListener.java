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

public interface SourceListener
{
  public void messageReceived(String source,String msg);
  public void reportReceived(String message);
  public void noticeReceived(String nick,String message);
  public void action(String nick,String msg);
  public void clear();
  public void activate();
}

