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

import java.awt.event.*;
import irc.gui.*;

public interface PixxTaskBarListener
{
  public void AWTSourceDesactivated(PixxTaskBar bar,AWTSource source);
  public void AWTSourceActivated(PixxTaskBar bar,AWTSource source);
  public void AWTSourceAdded(PixxTaskBar bar,AWTSource source);
  public void AWTSourceRemoved(PixxTaskBar bar,AWTSource source);
  public void eventOccured(PixxTaskBar bar,AWTSource source,MouseEvent e);
}

