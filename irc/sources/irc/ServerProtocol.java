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

import java.io.*;
import java.net.*;
import java.util.*;
import irc.security.*;

public class ServerProtocol extends IRCObject implements Runnable
{
  private Hashtable _listeners;
  private String _host;
  private int _port;
  private String _nick;
  private Socket _socket;
  private BufferedReader _reader;
  private BufferedWriter _writer;
  private Thread _thread;
  private boolean _connected;
  private boolean _connecting;
  
  public ServerProtocol(IRCConfiguration config)
  {
    super(config);
    _connected=false;
    _connecting=false;
    _listeners=new Hashtable();
  }
  
  public void connect(String host,int port)
  {
    if(_connected) disconnect();
    _connecting=true;
    _host=host;
    _port=port;
    _thread=new Thread(this,"Read thread");
    _thread.start();
  }
  
  public boolean connected()
  {
    return _connected;
  }
  
  public boolean connecting()
  {
    return _connecting;
  }
  
  public synchronized void disconnect()
  {
    if(!_connected) return;
    if(_connecting) return;
    try
    {
      _socket.close();
      _reader.close();
      _writer.close();
    }
    catch(Exception e)
    {
   //   System.out.println("disconnection");
   //   System.out.println(e);
    }
    _connected=false;
    triggerDisconnectedListeners();
  }	
  
  public int getLocalPort()
  {
    return _socket.getLocalPort();
  }
  
  private void decodeLine(String line)
  {
    Vector res=new Vector();
    while(line.length()!=0)
    {
      if(line.startsWith(":") && (res.size()!=0))
      {
        res.insertElementAt(line.substring(1),res.size());
        line="";
      }
      else
      {
        int pos=line.indexOf(' ');
        if(pos==-1)
        {
          res.insertElementAt(line,res.size());
          line="";
        }
        else
        {
          String part=line.substring(0,pos);
          line=line.substring(pos+1);
          res.insertElementAt(part,res.size());				
        }
      }
    }
    if(res.size()==0) return;
    
    String source="";
    if(((String)(res.elementAt(0))).startsWith(":"))
    {
      source=(String)res.elementAt(0);
      source=source.substring(1);
      res.removeElementAt(0);
    }
    if(res.size()==0) return;
    
    String cmd=(String)res.elementAt(0);
    res.removeElementAt(0);
    
    String[] param=new String[res.size()];
    for(int i=0;i<res.size();i++) param[i]=(String)res.elementAt(i);
    
    if((cmd.charAt(0)>='0') && (cmd.charAt(0)<='9'))
    {
      triggerReplyListeners(source,cmd,param);
    }
    else
    {
      triggerMessageListeners(source,cmd,param);		
    }
  }
  
  public void run()
  {
    try
    {
      _socket=SecurityProvider.getSecurityProvider().getSocket(_host,_port);
      
      _reader=new BufferedReader(new InputStreamReader(new BufferedInputStream(_socket.getInputStream())));
      _writer=new BufferedWriter(new OutputStreamWriter(new BufferedOutputStream(_socket.getOutputStream())));
      _connected=true;
      _connecting=false;
      triggerConnectedListeners();
    }
    catch(Exception e)
    {
      _connecting=false;
      triggerConnectionFailedListeners(e.getMessage());
      return;
    }
    boolean terminated=false;
    while(!terminated)
    {
      try
      {
        String line=_reader.readLine();
        if(line==null) throw new Exception();
    //    System.out.println("--> "+line);
        try
        {
          if(line!=null) decodeLine(line);
        }
        catch(Exception e)
        {
          System.err.println("internal error");
          e.printStackTrace();
        }
      }
      catch(Exception e)
      {
        terminated=true;
      }
    }
    disconnect();
  }
  
  private void triggerReplyListeners(String prefix,String id,String params[])
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerProtocolListener lis=(ServerProtocolListener)e.nextElement();
      lis.replyReceived(prefix,id,params);
    }
  }
  
  private void triggerMessageListeners(String prefix,String cmd,String params[])
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerProtocolListener lis=(ServerProtocolListener)e.nextElement();
      lis.messageReceived(prefix,cmd,params);
    }	
  }
  
  private void triggerConnectedListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerProtocolListener lis=(ServerProtocolListener)e.nextElement();
      lis.connected();
    }		
  }
  
  private void triggerConnectionFailedListeners(String message)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerProtocolListener lis=(ServerProtocolListener)e.nextElement();
      lis.connectionFailed(message);
    }		
  }
  
  private void triggerDisconnectedListeners()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      ServerProtocolListener lis=(ServerProtocolListener)e.nextElement();
      lis.disconnected();
    }		
  }

  public void addServerProtocolListener(ServerProtocolListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeServerProtocolListener(ServerProtocolListener lis)
  {
    _listeners.remove(lis);
  }
  
  public void sendString(String str) throws Exception
  {
    _writer.write(str+"\n");
  //  System.out.println("<-- "+str);
    _writer.flush();	
  }
  
  public void sendCommand(String command,String params[]) throws Exception
  {
    String toSend=command;
    
    for(int i=0;i<params.length;i++)
    {
      toSend+=" ";
      if(params[i].indexOf(' ')!=-1) toSend+=":";
      toSend+=params[i];
    }
    sendString(toSend);
  }
  
  
}

