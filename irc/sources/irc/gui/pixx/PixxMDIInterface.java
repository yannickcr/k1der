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

import java.util.*;
import java.awt.*;
import java.awt.event.*;
import irc.gui.*;
import irc.*;

class MDILayout implements LayoutManager
{
  private Hashtable _components;

  public MDILayout()
  {
    _components=new Hashtable();
  }

  public void addLayoutComponent(String name, Component comp) 
  {
    _components.put(comp,comp);
  }
  
  private Component getVisible(Container parent)
  {
    Component[] c=parent.getComponents();
    for(int i=0;i<c.length;i++) if(c[i].isVisible()) return c[i];
    return null;
  }
  
  public void layoutContainer(Container parent) 
  {
    Component c=getVisible(parent);
    if(c==null) return;
    int w=parent.getSize().width;
    int h=parent.getSize().height;
    c.setBounds(0,0,w,h);
  }
  
  public Dimension minimumLayoutSize(Container parent) 
  {
    return new Dimension(0,0);
  }
  
  public Dimension preferredLayoutSize(Container parent) 
  {
    Component visible=getVisible(parent);
    if(visible!=null) return visible.getPreferredSize();
    return new Dimension(0,0);
  }
  
  public void removeLayoutComponent(Component comp) 
  {
    _components.remove(comp);
  }
}

public class PixxMDIInterface extends PixxPanel implements PixxTaskBarListener,PixxMenuBarListener,ActionListener
{
  private PixxMenuBar _menu;
  private PixxTaskBar _task;
  private Panel _mdi;
  private PopupMenu _popMenu;
  private Hashtable _listeners;
  private AWTSource _lastActive;
  private AWTSource _selectedSource;
	private TextField _nickField;

  public PixxMDIInterface(IRCConfiguration config,PixxTaskBar task,boolean title)
  {
    super(config);
    _popMenu=new PopupMenu();
    _lastActive=null;
    _listeners=new Hashtable();
    setLayout(new BorderLayout());
    _mdi=new Panel();
    _mdi.setLayout(new MDILayout());
    _task=task;
    _task.add(_popMenu);
    _popMenu.addActionListener(this);		
    _task.addPixxTaskBarListener(this);
    _menu=new PixxMenuBar(_ircConfiguration,title);
    _menu.addPixxMenuBarListener(this);
    add(_menu,"North");		
    add(_mdi,"Center");
		_nickField=new TextField("");
		_nickField.addActionListener(this);
		if(!_ircConfiguration.getNickField())
		{
      add(_task,"South");
		}
		else
		{
		  Panel bottom=new Panel();
			bottom.setLayout(new BorderLayout());
			bottom.add(_task,"Center");
			Panel nickConfig=new Panel();
			nickConfig.setLayout(new BorderLayout());
			Label label=new Label(getText(TextProvider.GUI_CHANGE_NICK));
		  label.setBackground(_ircConfiguration.getColor(PixxColorModel.COLOR_BACK));
		  label.setForeground(_ircConfiguration.getColor(PixxColorModel.COLOR_WHITE));
			Panel outerNickLabel=new Panel();
			outerNickLabel.setLayout(new BorderLayout());
			outerNickLabel.add(label,"Center");
      outerNickLabel.add(new PixxSeparator(PixxSeparator.BORDER_LEFT),"West");
      outerNickLabel.add(new PixxSeparator(PixxSeparator.BORDER_RIGHT),"East");
      outerNickLabel.add(new PixxSeparator(PixxSeparator.BORDER_UP),"North");
      outerNickLabel.add(new PixxSeparator(PixxSeparator.BORDER_DOWN),"South");
			
			nickConfig.add(outerNickLabel,"North");
			nickConfig.add(_nickField,"Center");

      bottom.add(nickConfig,("East"));
			add(bottom,"South");
		}
    validate();
  }
  
  public void setTitle(String title,ColorContext context)
  {
    _menu.setTitle(title,context);
  }
  
  public void addPixxMDIInterfaceListener(PixxMDIInterfaceListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removePixxMDIInterfaceListener(PixxMDIInterfaceListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerChangedListeners(AWTSource source)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxMDIInterfaceListener lis=(PixxMDIInterfaceListener)e.nextElement();
      lis.activeChanged(source,this);
    }
  }

  private void triggerConnectListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxMDIInterfaceListener lis=(PixxMDIInterfaceListener)e.nextElement();
      lis.connectTriggered(this);
    }
  }
  
  private void triggerAboutListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxMDIInterfaceListener lis=(PixxMDIInterfaceListener)e.nextElement();
      lis.aboutTriggered(this);
    }
  }
  
  private void triggerHelpListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      PixxMDIInterfaceListener lis=(PixxMDIInterfaceListener)e.nextElement();
      lis.helpTriggered(this);
    }
  }
  
  public void setConnected(boolean b)
  {
    _menu.setConnected(b);
  }
  
  private void test()
  {
    if(_task.getActive()==_lastActive) return;
    _lastActive=_task.getActive();
    triggerChangedListeners(_lastActive);
  }
  
  public AWTSource getActive()
  {
    return _task.getActive();
  }
  
  public void AWTSourceAdded(PixxTaskBar bar,AWTSource source)
  {
    _mdi.add(source);
    source.setVisible(false);
    validate();
    test();
  }
  
  public void AWTSourceRemoved(PixxTaskBar bar,AWTSource source)
  {
    _mdi.remove(source);
    validate();
    test();
  }
  
  public void AWTSourceDesactivated(PixxTaskBar bar,AWTSource source)
  {
    source.setVisible(false);
    validate();
    test();
  }
  
  public void AWTSourceActivated(PixxTaskBar bar,AWTSource source)
  {
    if(source!=null)
    {
      source.setVisible(true);
      validate();
      source.requestFocus();
    }
    test();
  }
  
  public void eventOccured(PixxTaskBar bar,AWTSource source,MouseEvent e)
  {
    if((e.getModifiers() & MouseEvent.BUTTON1_MASK)==0)
    {
      _selectedSource=source;
      
      _popMenu.removeAll();      
      _popMenu.add(new MenuItem(getText(TextProvider.GUI_CLOSE)));
      
      _popMenu.show(_task,e.getX(),e.getY());
    }
    source.requestFocus();
  }
  
  public void actionPerformed(ActionEvent e)
  {
    if(e.getActionCommand().equals(getText(TextProvider.GUI_CLOSE)))
    {
      _selectedSource.leave();
    }
		else if(e.getSource()==_nickField)
		{
      AWTSource src=_task.getActive();
      if(src==null) return;		
		  src.getSource().sendString("/nick "+_nickField.getText());
		}
  }
 
  public void connectionClicked(PixxMenuBar bar)
  {
    triggerConnectListeners();
  }
  
  public void chanListClicked(PixxMenuBar bar)
  {
    AWTSource src=_task.getActive();
    if(src==null) return;
    src.getSource().sendString("/list");
  }
  
  public void aboutClicked(PixxMenuBar bar)
  {
    triggerAboutListeners();
  }
  
  public void helpClicked(PixxMenuBar bar)
  {
    triggerHelpListeners();
  }
  
  public void closeClicked(PixxMenuBar bar)
  {
    AWTSource src=_task.getActive();
    if(src==null) return;
    src.leave();
  }
  
}

