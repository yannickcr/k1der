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

public interface PixxMenuBarListener
{
  public void connectionClicked(PixxMenuBar bar);
  public void chanListClicked(PixxMenuBar bar);
  public void aboutClicked(PixxMenuBar bar);
  public void helpClicked(PixxMenuBar bar);
  public void closeClicked(PixxMenuBar bar);
}

