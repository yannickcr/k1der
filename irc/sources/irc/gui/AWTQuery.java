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

package irc.gui;
import irc.*;

import irc.gui.pixx.*;

public class AWTQuery extends AWTSource implements QueryListener
{
  public AWTQuery(IRCConfiguration config,Query query)
  {
    super(config,query);
    query.addQueryListener(this);
    title();
  }

  private void title()
  {
    setTitle(_source.getName());
  }

  public void nickChanged(String newNick)
  {
    title();
  }
  
  
}

