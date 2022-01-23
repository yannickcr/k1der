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

package irc.style;

import java.awt.event.*;

public interface StyledListListener
{
  public void channelEvent(StyledList list,String channel,MouseEvent e);
  public void URLEvent(StyledList list,String url,MouseEvent e);
  public void nickEvent(StyledList list,String nick,MouseEvent e);
}

