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

public class ChannelInfo
{

  public ChannelInfo(String n,String t,int c)
  {
    name=n;
    topic=t;
    userCount=c;
  }

  public String name;
  public String topic;
  public int userCount;
}

