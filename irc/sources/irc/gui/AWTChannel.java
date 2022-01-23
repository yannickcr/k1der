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

import java.util.*;
import irc.*;
import irc.gui.pixx.*;
import java.awt.*;
import java.awt.event.*;
import irc.tree.*;
import irc.style.*;

public class AWTChannel extends AWTSource implements ChannelListener,PixxNickListListener,irc.tree.Comparator
{
  private ScrollablePixxNickList _nicks;
	private Label _label;
  private String _selectedNick;
  private PopupMenu _menu;
  private SortedList _sortedList;
  private Hashtable _modeMapping;
  
  public AWTChannel(IRCConfiguration config,Channel c)
  {
    super(config,c);
    _menu=new PopupMenu();
    _nicks=new ScrollablePixxNickList(_ircConfiguration);
    c.addChannelListener(this);
    _nicks.addPixxNickListListener(this);
    add(_menu);
    _sortedList=new SortedList(this);
    _modeMapping=new Hashtable();
    _menu.addActionListener(this);
		_label=new Label("");
		_label.setBackground(_ircConfiguration.getColor(PixxColorModel.COLOR_BACK));
		_label.setForeground(_ircConfiguration.getColor(PixxColorModel.COLOR_WHITE));
		if(_ircConfiguration.getASL())
		{
		  Panel right=new Panel();
			right.setLayout(new BorderLayout());
			right.add(_nicks,"Center");			
			Panel outlabel=new Panel();
      outlabel.setLayout(new BorderLayout());
      outlabel.add(new PixxSeparator(PixxSeparator.BORDER_LEFT),"West");
      outlabel.add(new PixxSeparator(PixxSeparator.BORDER_RIGHT),"East");
      outlabel.add(new PixxSeparator(PixxSeparator.BORDER_UP),"North");
      outlabel.add(new PixxSeparator(PixxSeparator.BORDER_DOWN),"South");
			outlabel.add(_label,"Center");
			right.add(outlabel,"South");
			add(right,"East");
		}
		else
		{
      add(_nicks,"East");
		}
    doLayout();
    title();
  }
	
	public void doLayout()
	{
	  _label.setText("");
		super.doLayout();
	}
	
  public int compare(Object i1,Object i2)
  {
    String n1=(String)i1;
    String n2=(String)i2;
    if(n1.startsWith("+") && n2.startsWith("@")) return 1;
    if(n1.startsWith("%") && n2.startsWith("@")) return 1;
    if(n1.startsWith("+") && n2.startsWith("%")) return 1;
		
    if(n1.startsWith("@") && n2.startsWith("+")) return -1;
    if(n1.startsWith("@") && n2.startsWith("%")) return -1;
    if(n1.startsWith("%") && n2.startsWith("+")) return -1;
    return n1.toLowerCase().toUpperCase().compareTo(n2.toLowerCase().toUpperCase());
  }
  
  private String getFullModeNick(String nick,String mode)
  {
    ModeHandler h=new ModeHandler(mode);
    if(h.hasMode('o')) return '@'+nick;
    if(h.hasMode('v')) return '+'+nick;
    if(h.hasMode('h')) return '%'+nick;
    return nick;
  }
  
	private String getNickWOMode(String nick)
	{
	  if(nick.length()==0) return nick;
	  char c=nick.charAt(0);
    int p=0;
		if((c=='@') || (c=='%') || (c=='+')) p=1;
		return nick.substring(p);
	}
	
  private void setNicks(String[] nicks)
  {
    for(int i=0;i<nicks.length;i++) addNick(nicks[i]);
  }
  
  private void addNick(String nick)
  {
    String mode=((Channel)_source).getNickMode(nick);
    if(mode!=null)
    {		
      String full=getFullModeNick(nick,mode);
      _sortedList.add(full);
      _modeMapping.put(nick,full);
    }
  }
  
  private void removeNick(String nick)
  {
    String full=(String)_modeMapping.get(nick);
    if(full!=null)
    {
      _sortedList.remove(full);
      _modeMapping.remove(nick);
    }		
  }
  
  private void updateNick(String nick)
  {
    removeNick(nick);
    addNick(nick);
  }
  
  private void update()
  {
    String[] n=new String[_modeMapping.size()];
    Enumeration e=_modeMapping.keys();
    int i=0;
    while(e.hasMoreElements()) n[i++]=(String)e.nextElement();
    
    _list.setNickList(n);
    _textField.setCompleteList(n);
    
    n=new String[_sortedList.getSize()];
    e=_sortedList.getItems();
    i=0;
    while(e.hasMoreElements())
		{
      String nick=(String)e.nextElement();
			String whois=((Channel)_source).whois(getNickWOMode(nick));
		  n[i++]=nick+":"+whois;
		}
   
    _nicks.set(n);
    title();
  }
  
  public synchronized void nickSet(String[] nicks,String[] modes)
  {
    setNicks(nicks);
    //updateNicks();	
    update();
  }
  
  public synchronized void nickJoin(String nick,String mode)
  {
    addNick(nick);
    //  updateNicks();
    update();
    print("*** "+nick+" "+getText(TextProvider.SOURCE_HAS_JOINED)+" "+_source.getName(),3);
  }
  
  public synchronized void nickPart(String nick,String reason)
  {
    //  updateNicks();	
    removeNick(nick);
    update();
    if(reason.length()>0)
      print("*** "+nick+" "+getText(TextProvider.SOURCE_HAS_LEFT)+" "+_source.getName()+" ("+reason+")",3);
    else
      print("*** "+nick+" "+getText(TextProvider.SOURCE_HAS_LEFT)+" "+_source.getName(),3);
  }
  
  public synchronized void nickKick(String nick,String by,String reason)
  {
    //  updateNicks();	
    removeNick(nick);
    update();
    if(reason.length()>0)
      print("*** "+nick+" "+getText(TextProvider.SOURCE_HAS_BEEN_KICKED_BY)+" "+by+" ("+reason+")",3);
    else
      print("*** "+nick+" "+getText(TextProvider.SOURCE_HAS_BEEN_KICKED_BY)+" "+by,3);
  }
  
  public synchronized void nickQuit(String nick,String reason)
  {
    //  updateNicks();
    removeNick(nick);
    update();
    if(reason.length()>0)
      print("*** "+nick+" "+getText(TextProvider.SOURCE_HAS_QUIT)+" ("+reason+")",2);
    else
      print("*** "+nick+" "+getText(TextProvider.SOURCE_HAS_QUIT),2);
  }
  
  private void title()
  {
    int count=_sortedList.getSize();
    setTitle(_source.getName()+" ["+count+"]"+" ["+((Channel)_source).getMode()+"]: "+((Channel)_source).getTopic());
  }
  
  public synchronized void topicChanged(String topic,String by)
  {
    if(by.length()==0)
      print("*** "+getText(TextProvider.SOURCE_TOPIC_IS)+" "+topic,3);
    else
      print("*** "+by+" "+getText(TextProvider.SOURCE_CHANGED_TOPIC)+" "+topic,3);
    title();
  }
  
  public synchronized void modeApply(String mode,String from)
  {
    if(from.length()>0)
      print("*** "+from+" "+getText(TextProvider.SOURCE_CHANNEL_MODE)+" "+mode,3);		
    else
      print("*** "+getText(TextProvider.SOURCE_CHANNEL_MODE_IS)+" "+mode,3);				
    title();
  }
  
  public synchronized void nickModeApply(String nick,String mode,String from)
  {
    print("*** "+from+" "+getText(TextProvider.SOURCE_USER_MODE)+" "+mode+" "+getText(TextProvider.SOURCE_ON)+" "+nick,3);
    updateNick(nick);
    update();
  }
  
  public synchronized void nickChanged(String oldNick,String newNick)
  {
    print("*** "+oldNick+" "+getText(TextProvider.SOURCE_KNOWN_AS)+" "+newNick,3);
    removeNick(oldNick);
    addNick(newNick);
    update();
    // updateNicks();
  }
 
  public void nickWhoisUpdated(String nick,String whois)
	{
    update();
	}
	
  private void popup(String nick,MouseEvent e,Component c)
  {
    _selectedNick=nick;
    
    _menu.removeAll();
    
    if(!_ircConfiguration.getInfo()) _menu.add(new MenuItem(getText(TextProvider.GUI_WHOIS)));
    _menu.add(new MenuItem(getText(TextProvider.GUI_QUERY)));
    _menu.addSeparator();
    _menu.add(new MenuItem(getText(TextProvider.GUI_KICK)));
    _menu.add(new MenuItem(getText(TextProvider.GUI_BAN)));
    _menu.add(new MenuItem(getText(TextProvider.GUI_KICKBAN)));
    _menu.addSeparator();
    _menu.add(new MenuItem(getText(TextProvider.GUI_OP)));
    _menu.add(new MenuItem(getText(TextProvider.GUI_DEOP)));
    _menu.add(new MenuItem(getText(TextProvider.GUI_VOICE)));
    _menu.add(new MenuItem(getText(TextProvider.GUI_DEVOICE)));
    if(!_ircConfiguration.getInfo()) _menu.addSeparator();
    if(!_ircConfiguration.getInfo()) _menu.add(new MenuItem(getText(TextProvider.GUI_PING)));
    if(!_ircConfiguration.getInfo()) _menu.add(new MenuItem(getText(TextProvider.GUI_VERSION)));
    if(!_ircConfiguration.getInfo()) _menu.add(new MenuItem(getText(TextProvider.GUI_TIME)));
    if(!_ircConfiguration.getInfo()) _menu.add(new MenuItem(getText(TextProvider.GUI_FINGER)));
    
    
    _menu.show(c,e.getX(),e.getY());	
  }
  
  public void nickEvent(StyledList lis,String nick,MouseEvent e)
  {
    if((e.getClickCount()==1) && ((e.getModifiers() & MouseEvent.BUTTON1_MASK)==0))
    {
      popup(nick,e,_list);
    }	
    else
    {
      super.nickEvent(lis,nick,e);
    }
  }
  
  public void eventOccured(String nick,MouseEvent e)
  {
    if(e.getClickCount()>1)
    {
      _source.sendString("/query "+nick);
    }
    else if((e.getModifiers() & MouseEvent.BUTTON1_MASK)==0)
    {
      popup(nick,e,_nicks);
    }
  }
  
	public void ASLEventOccured(String nick,String info)
	{
	  String orig=info;
	  int pos=info.indexOf(" ");
		if(pos==-1)
		{
		  _label.setText(orig);
			return;
		}
		
		String age=info.substring(0,pos).trim();
		info=info.substring(pos+1).trim();
		pos=info.indexOf(" ");
		if(pos==-1)
		{
		  _label.setText(orig);
			return;
		}
		String gender=info.substring(0,pos).trim().toLowerCase();
		String location=info.substring(pos+1).trim();
		
		if(gender.equals(_ircConfiguration.getASLMale())) gender=getText(TextProvider.ASL_MALE);
		else if(gender.equals(_ircConfiguration.getASLFemale())) gender=getText(TextProvider.ASL_FEMALE);
		else
		{
		  _label.setText(orig);
			return;
		}
		
	  _label.setText(gender+", "+age+" "+getText(TextProvider.ASL_YEARS)+", "+location);
	}
	
  public void actionPerformed(ActionEvent e)
  {
    if(e.getSource() instanceof PopupMenu)
    {
      if(e.getActionCommand().equals(getText(TextProvider.GUI_WHOIS)))
      {
        _source.sendString("/whois "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_QUERY)))
      {
        _source.sendString("/query "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_KICK)))
      {
        _source.sendString("/kick "+getSource().getName()+" "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_BAN)))
      {
        _source.sendString("/mode "+getSource().getName()+" +b "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_KICKBAN)))
      {
        _source.sendString("/mode "+getSource().getName()+" -o "+_selectedNick);
        _source.sendString("/mode "+getSource().getName()+" +b "+_selectedNick);
        _source.sendString("/kick "+getSource().getName()+" "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_OP)))
      {
        _source.sendString("/mode "+getSource().getName()+" +o "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_DEOP)))
      {
        _source.sendString("/mode "+getSource().getName()+" -o "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_VOICE)))
      {
        _source.sendString("/mode "+getSource().getName()+" +v "+_selectedNick);			
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_DEVOICE)))
      {
        _source.sendString("/mode "+getSource().getName()+" -v "+_selectedNick);			
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_PING)))
      {
        _source.sendString("/ctcp ping "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_VERSION)))
      {
        _source.sendString("/ctcp version "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_VERSION)))
      {
        _source.sendString("/ctcp time "+_selectedNick);
      }
      else if(e.getActionCommand().equals(getText(TextProvider.GUI_FINGER)))
      {
        _source.sendString("/ctcp finger "+_selectedNick);
      }
      else
      {
      }
    }
    else
    {
      super.actionPerformed(e);		
    }
  }
  
}

