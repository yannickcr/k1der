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

import irc.gui.*;
import irc.gui.pixx.*;
import irc.security.*;
import irc.ident.*;
import java.util.*;
import java.io.*;

import java.awt.*;
import java.awt.event.*;

public class IRCApplication extends IRCObject implements ServerListener,DCCListener,IdentListener,PixxMDIInterfaceListener,WindowListener,AWTSourceListener
{
  private IRCServer _server;
  private Hashtable _channels;
  private Hashtable _queries;
  private Hashtable _dccChats;
  
  private Hashtable _dccFiles;
  private Hashtable _lists;
  private BasicCTCPFilter _filter;
  
  private ChannelInterpretor _channelInter;
  private StatusInterpretor _statusInter;
  private QueryInterpretor _queryInter;
  private DCCChatInterpretor _dccinter;
  
  private IdentServer _ident;

  private String _nick;
  private String _host;
  private int _port;
  private String _name;
  private String[] _commands;
  
  private String _helpPage;
  
  private PixxTaskBar _task;
  private PixxMDIInterface _interface;
  
  private AWTStatus _status;	
  private Frame _frame;
  
  
  public IRCApplication(IRCConfiguration config,String nick,String name,String host,int port,Container source,String helpPage)
  {
    super(config);
    _helpPage=helpPage;
    _commands=new String[0];
    _host=host;
    _port=port;
    _task=new PixxTaskBar(config);
    _interface=new PixxMDIInterface(config,_task,source!=null);
    _nick=nick;
    _name=name;
    
    _interface.addPixxMDIInterfaceListener(this);
    if(source==null)
    {
      _frame=new Frame();
      _frame.addWindowListener(this);
      _frame.add(_interface);
      _frame.show();
      _frame.setSize(640,400);
      _frame.doLayout();
    }
    else
    {
      _frame=null;
      source.removeAll();
      source.setLayout(new GridLayout(1,1));
      source.add(_interface);
    }
  }
  
  public void init()
  {
    _ident=new IdentServer(_ircConfiguration);
    _ident.addIdentListener(this);
    _ident.setDefaultUser("JAVA",_nick);
    
    _channels=new Hashtable();
    _queries=new Hashtable();
    _dccChats=new Hashtable();
    _dccFiles=new Hashtable();
    _lists=new Hashtable();
    _filter=new BasicCTCPFilter(_ircConfiguration);
    _filter.addDCCListener(this);
    
    _channelInter=new ChannelInterpretor(_ircConfiguration,_filter);
    _statusInter=new StatusInterpretor(_ircConfiguration,_filter);
    _queryInter=new QueryInterpretor(_ircConfiguration,_filter);
    _dccinter=new DCCChatInterpretor(_ircConfiguration,_filter);
    
    _server=new IRCServer(_ircConfiguration,_nick,_name);
    _server.addServerListener(this);
    
    _server.getStatus().setInterpretor(_statusInter);
    _server.getStatus().setCTCPFilter(_filter);
    
    _status=new AWTStatus(_ircConfiguration,_server.getStatus());
    _status.addAWTSourceListener(this);
    _task.addStatus(_status,true);
    
    try
    {
      int port=113;
      _ident.start(113);
    }
    catch(Exception e)
    {
      _server.getStatus().report("\3"+"6"+"*** "+getText(TextProvider.IDENT_FAILED_LAUNCH)+" : "+e.getMessage());
    }
    
  }
  
  public void uninit()
  {
    _ident.removeIdentListener(this);
    _ident.stop();
  }
  
  public void connect(String commands[])
  {
    _commands=commands;
    _server.connect(_host,_port);			
  }
  
  public void disconnect()
  {
    _server.disconnect();
  }
  
  public boolean connected()
  {
    return _server.isConnected();
  }
  
  public void channelCreated(Channel chan)
  {
    chan.setInterpretor(_channelInter);
    chan.setCTCPFilter(_filter);
    AWTChannel awt=new AWTChannel(_ircConfiguration,chan);
    awt.addAWTSourceListener(this);
    _task.addChannel(awt,true);
    _channels.put(chan,awt);
  }
  
  public void channelRemoved(Channel chan)
  {
    AWTChannel s=(AWTChannel)_channels.get(chan);
    s.removeAWTSourceListener(this);
    _task.removeChannel(s);
    _channels.remove(chan);
  }	
  
  public void queryCreated(Query query,boolean bring)
  {
    query.setInterpretor(_queryInter);
    query.setCTCPFilter(_filter);
    AWTQuery awt=new AWTQuery(_ircConfiguration,query);
    awt.addAWTSourceListener(this);
    _task.addQuery(awt,bring);
    _queries.put(query,awt);
  }
  
  public void queryRemoved(Query query)
  {
    AWTQuery q=(AWTQuery)_queries.get(query);
    q.removeAWTSourceListener(this);
    _task.removeQuery(q);
    _queries.remove(query);
  }
 
  public void chanListCreated(ChanList list)
  {
    AWTChanList cl=new AWTChanList(_ircConfiguration,list);
    _lists.put(list,cl);
  }
  
  public void chanListRemoved(ChanList list)
  {	
    AWTChanList l=(AWTChanList)_lists.get(list);
    _lists.remove(list);
    l.close();
  }
  
  public void DCCChatCreated(DCCChat chat,boolean bring)
  {
    chat.setInterpretor(_dccinter);
    chat.setCTCPFilter(_filter);
    AWTDCCChat awt=new AWTDCCChat(_ircConfiguration,chat);
    awt.addAWTSourceListener(this);
    _task.addDCCChat(awt,bring);
    _dccChats.put(chat,awt);
  }
  
  public void DCCChatRemoved(DCCChat chat)
  {
    AWTDCCChat c=(AWTDCCChat)_dccChats.get(chat);
    c.removeAWTSourceListener(this);
    _task.removeDCCChat(c);
    _dccChats.remove(chat);
  }

  public void DCCFileCreated(DCCFile file)
  {
    _dccFiles.put(file,new AWTDCCFile(_ircConfiguration,file));
  }
  
  public void DCCFileRemoved(DCCFile file)
  {
    AWTDCCFile f=(AWTDCCFile)_dccFiles.get(file);
    _dccFiles.remove(file);
    f.close();
  }
  
  public File DCCFileRequest(String nick,String fileName,int size)
  {
    return SecurityProvider.getSecurityProvider().getSaveFile(getText(TextProvider.FILE_SAVEAS));
  }
  
  public boolean DCCChatRequest(String nick)
  {
    return true;
  }
  
  public void serverConnected()
  {
    _interface.setConnected(true);
    for(int i=0;i<_commands.length;i++) _server.execute(_commands[i]);
  }
  
  public void serverDisconnected()
  {
    _interface.setConnected(false);
  }
  
  public void identRequested(String source,int result,String reply)
  {
    _server.getStatus().report("\3"+"6"+"*** "+getText(TextProvider.IDENT_REQUEST)+" "+source);
    String s="";
    switch(result)
    {
      case IdentListener.IDENT_ERROR:s=getText(TextProvider.IDENT_ERROR);break;
      case IdentListener.IDENT_OK:s=getText(TextProvider.IDENT_REPLIED)+" : "+reply;break;
      case IdentListener.IDENT_DEFAULT:s=getText(TextProvider.IDENT_REPLIED)+" "+getText(TextProvider.IDENT_DEFAULT_USER)+" : "+reply;break;
      case IdentListener.IDENT_NOT_FOUND:s=getText(TextProvider.IDENT_NO_USER);break;
      default: s=getText(TextProvider.IDENT_UNDEFINED);break;
    }
    _server.getStatus().report("\3"+"6"+"*** "+s);
    
  }
  
  public void identRunning(int port)
  {
    _server.getStatus().report("\3"+"6"+"*** "+getText(TextProvider.IDENT_RUNNING_ON_PORT)+" "+port);
  }
  
  public void identLeaving(String message)
  {
    _server.getStatus().report("\3"+"6"+"*** "+getText(TextProvider.IDENT_LEAVING)+" : "+message);	
  }
  
  public void activeChanged(AWTSource source,PixxMDIInterface mdi)
  {
    _status.setActiveSource(source);
    _interface.setTitle(source.getTitle(),source.getColorContext());
    if(_frame!=null) _frame.setTitle(source.getStrippedTitle());
  }

  public void titleChanged(AWTSource source)
  {
    if(source!=_interface.getActive()) return;
    _interface.setTitle(source.getTitle(),source.getColorContext());
    if(_frame!=null) _frame.setTitle(source.getStrippedTitle());
  }
  
  public void eventOccured(AWTSource source)
  {
  }

  public void activated(AWTSource source)
  {
  }

  public void connectTriggered(PixxMDIInterface mdi)
  {
    if(connected())
      disconnect();
    else
      connect(_commands);
  }
  
  public void aboutTriggered(PixxMDIInterface mdi)
  {
    Frame frame=new Frame();
    frame.setTitle(getText(TextProvider.ABOUT_ABOUT));
    frame.setLayout(new BorderLayout());
      
    Panel text=new Panel();
    
    text.setLayout(new GridLayout(19,1));
    text.add(new Label("Java IRC Client V1.62",Label.CENTER));
    text.add(new Panel());
    text.add(new Label(getText(TextProvider.ABOUT_GPL),Label.CENTER));
    text.add(new Panel());
    text.add(new Label(getText(TextProvider.ABOUT_PROGRAMMING)+" : Philippe Detournay alias Plouf (theplouf@yahoo.com)",Label.CENTER));
    text.add(new Label(getText(TextProvider.ABOUT_DESIGN)+" : Raphael Seegmuller chez pixxservices.com (pixxservices@pixxservices.com)",Label.CENTER));
    text.add(new Panel());
    text.add(new Label(getText(TextProvider.ABOUT_THANKS),Label.CENTER));
    text.add(new Panel());
    text.add(new Label("Mandragor : www.mandragor.org",Label.CENTER));
    text.add(new Label("DWChat : www.dwchat.net",Label.CENTER));
    text.add(new Label("Kombat",Label.CENTER));
    text.add(new Label("Falcon.be",Label.CENTER));
    text.add(new Label("Jerarckill",Label.CENTER));
    text.add(new Label("Devis Lucato",Label.CENTER));
    text.add(new Panel());
    text.add(new Label(getText(TextProvider.ABOUT_SUPPORT),Label.CENTER));
    text.add(new Panel());
		text.add(new Label("http://groups.yahoo.com/group/pjirc",Label.CENTER));
    frame.addWindowListener(this);
    frame.add(text,"Center");
    
    frame.setSize(500,300);
    frame.setResizable(false);
    frame.show();
  }
  
  public void helpTriggered(PixxMDIInterface mdi)
  {
    if(_helpPage!=null) _ircConfiguration.openURL(_helpPage);	
  }
  
  public void windowActivated(WindowEvent e)
  {
  }
  
  public void windowClosed(WindowEvent e)
  {
  }
  
  public void windowClosing(WindowEvent e)
  {
    if(e.getSource()==_frame)
    {
      disconnect();
    }
    ((Frame)e.getSource()).hide();
    ((Frame)e.getSource()).dispose();
  }
  
  public void windowDeactivated(WindowEvent e)
  {
  }
  
  public void windowDeiconified(WindowEvent e)
  {
  }
  
  public void windowIconified(WindowEvent e)
  {
  }
  
  public void windowOpened(WindowEvent e)
  {
  }
 
  public static void main(String[] args)
  {
    //IRCApplication application=new IRCApplication(new IRCConfiguration(false,new EnglishTextProvider(),new PixxColorModel(),null),"Plouf","Plouf","liberator.DAL.net",7000,null,null);
  //  IRCConfiguration config=new IRCConfiguration(false,false,new EnglishTextProvider(),new PixxColorModel(),new NullURLHandler(),new AWTImageLoader());
    IRCConfiguration config=new IRCConfiguration(false,false,new FrenchTextProvider(),new PixxColorModel(),new NullURLHandler(),new AWTImageLoader());
    config.setHighLightConfig(5,true,new Vector());
    config.enableHighLight(true);
    config.setASL(true);
		//config.setNickField(true);
		//config.setShowChanlist(false);
		//config.setInfo(true);
		
    IRCApplication application=new IRCApplication(config,"Plouf?","22 m Namur","irc.dwchat.net",6667,null,null);
    application.init();
		String[] cmd=new String[0];
    application.connect(cmd);
  }
  
}


