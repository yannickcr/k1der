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

public interface ServerListener 
{
  public void serverConnected();
  public void serverDisconnected();

  public void channelCreated(Channel chan);
  public void channelRemoved(Channel chan);
  public void queryCreated(Query query,boolean bring);
  public void queryRemoved(Query query);
  public void chanListCreated(ChanList list);
  public void chanListRemoved(ChanList list);
}

