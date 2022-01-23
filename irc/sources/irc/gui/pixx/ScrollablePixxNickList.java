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

import java.awt.*;
import irc.*;

public class ScrollablePixxNickList extends PixxPanel implements PixxScrollBarListener
{

  private PixxNickList _list;
  private PixxScrollBar _scroll;

  public ScrollablePixxNickList(IRCConfiguration config)
  {
    super(config);
    setLayout(new BorderLayout());
    Panel p=new Panel();
    p.setLayout(new BorderLayout());
    
    _list=new PixxNickList(config);
    _scroll=new PixxScrollBar(config,0,0,PixxScrollBar.VERTICAL,0.1);
    _scroll.addPixxScrollBarListener(this);
    p.add(_list,"Center");
    p.add(_scroll,"East");
    
    add(p,"Center");
    add(new PixxSeparator(PixxSeparator.BORDER_LEFT),"West");
    add(new PixxSeparator(PixxSeparator.BORDER_RIGHT),"East");
    add(new PixxSeparator(PixxSeparator.BORDER_UP),"North");
    add(new PixxSeparator(PixxSeparator.BORDER_DOWN),"South");
  }

  public void addPixxNickListListener(PixxNickListListener lis)
  {
    _list.addPixxNickListListener(lis);
  }

  public void removePixxNickListListener(PixxNickListListener lis)
  {
    _list.removePixxNickListListener(lis);
  }
  
  public void set(String[] nicks)
  {
    _list.set(nicks);
    _scroll.setMaximum(_list.getNickCount()-1);		
  }

  public void add(String nick)
  {
    _list.add(nick);
    _scroll.setMaximum(_list.getNickCount()-1);
  }
  
  public void removeAll()
  {
    _list.removeAll();
    _scroll.setMaximum(_list.getNickCount()-1);
  }

  public void valueChanged(PixxScrollBar pixScrollBar)
  {
    _list.setBase(pixScrollBar.getValue());
  }
  
  
}

