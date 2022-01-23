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

package irc.gui.pixx;

import irc.gui.*;

public interface PixxMDIInterfaceListener
{
  public void activeChanged(AWTSource source,PixxMDIInterface mdi);
  public void connectTriggered(PixxMDIInterface mdi);
  public void aboutTriggered(PixxMDIInterface mdi);
  public void helpTriggered(PixxMDIInterface mdi);
}

