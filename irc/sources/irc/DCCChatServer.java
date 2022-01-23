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

public class DCCChatServer extends IRCObject implements Runnable,Server
{
  private Socket _socket;
  private ServerSocket _serverSocket;
  private BufferedReader _reader;
  private BufferedWriter _writer;
  private Thread _thread;
  private DCCChat _chat;
  private String _remoteNick;
  private String _thisNick;
  private DCCChatClosingListener _lis;
  private boolean _listening;
  private int _action;
  

  public DCCChatServer(IRCConfiguration config,String thisNick,String remoteNick,DCCChatClosingListener lis)
  {
    super(config);
    _action=0;
    _lis=lis;
    _remoteNick=remoteNick;
    _thisNick=thisNick;
  }
  
  public void openActive(DCCChat chat,String ip,String port)
  {
    _chat=chat;
    _serverSocket=null;
    _action=1;
    long iip=new Long(ip).longValue();
    int b1=(int)(iip&255);
    int b2=(int)((iip>>8)&255);
    int b3=(int)((iip>>16)&255);
    int b4=(int)((iip>>24)&255);
    ip=b4+"."+b3+"."+b2+"."+b1;
    
    try
    {
      _socket=SecurityProvider.getSecurityProvider().getSocket(ip,new Integer(port).intValue());
    
      _reader=new BufferedReader(new InputStreamReader(_socket.getInputStream()));
      _writer=new BufferedWriter(new OutputStreamWriter(_socket.getOutputStream()));
      _thread=new Thread(this,"DCCChat thread");
      _thread.start();
    }
    catch(Exception e)
    {
    }
    
  }
  
  public String openPassive(DCCChat chat)
  {
    _chat=chat;
    _action=2;
    _socket=null;
    try
    {
      _serverSocket=SecurityProvider.getSecurityProvider().getServerSocket(0);
      int port=_serverSocket.getLocalPort();
      
      InetAddress addr=SecurityProvider.getSecurityProvider().getLocalHost();
      byte[] ip=addr.getAddress();

      int b1=ip[0];if(b1<0) b1+=256;
      int b2=ip[1];if(b2<0) b2+=256;
      int b3=ip[2];if(b3<0) b3+=256;
      int b4=ip[3];if(b4<0) b4+=256;
  
      long high=(b1<<24)+(b2<<16)+(b3<<8)+b4;
      
      String sip=""+high;
      _listening=false;			
      _thread=new Thread(this,"DCCChat thread");
      _thread.start();
      while(!_listening) Thread.yield();
      return sip+" "+port;
    }
    catch(Exception e)
    {
      e.printStackTrace();
      return "";
    }		
  }
  
  public void run()
  {
    boolean terminated=false;
    if(_action==2)
    {
      try
      {
        _listening=true;
        _chat.report(getText(TextProvider.DCC_WAITING_INCOMING));
        _serverSocket.setSoTimeout(30000);
        _socket=_serverSocket.accept();
        _reader=new BufferedReader(new InputStreamReader(_socket.getInputStream()));
        _writer=new BufferedWriter(new OutputStreamWriter(_socket.getOutputStream()));
      }
      catch(Exception e)
      {
        _chat.report(getText(TextProvider.DCC_UNABLE_TO_OPEN_CONNECTION)+" : "+e.getMessage());
        return;
      }
    }
    _chat.report(getText(TextProvider.DCC_CONNECTION_ESTABLISHED));
    while(!terminated)
    {
      try
      {
        String line=_reader.readLine();
        if(line==null) throw new Exception(getText(TextProvider.DCC_CONNECTION_CLOSED));
        try
        {
          _chat.messageReceived(_remoteNick,line);
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
        _chat.report(getText(TextProvider.DCC_ERROR)+" : "+e.getMessage());
      }
    }
    cleanup();
  }

  public void say(String destination,String str)
  {
    if(destination.equals(_remoteNick))
      sendString(str);
    else
      _chat.report(getText(TextProvider.DCC_ERROR)+" : "+getText(TextProvider.DCC_UNABLE_TO_SEND_TO) +" "+destination);
  }
  
  public void execute(String str)
  {
    _chat.report(getText(TextProvider.DCC_BAD_CONTEXT));
   // sendString(str);
  }

  private void sendString(String str)
  {
    try
    {
      if(_writer==null) throw new Exception(getText(TextProvider.DCC_NOT_CONNECTED));
      _writer.write(str+"\n");
      _writer.flush();		
    }
    catch(Exception e)
    {
      _chat.report(getText(TextProvider.DCC_ERROR)+" : "+e.getMessage());
    }
  }
  
  public void sendStatusMessage(String str)
  {
    _chat.report(str);	
  }

  private void cleanup()
  {
    try
    {
      if(_socket!=null) _socket.close();
      if(_serverSocket!=null) _serverSocket.close();
      _reader.close();
      _writer.close();
    }
    catch(Exception e)
    {
    }
  }

  public void close()
  {
    cleanup();
    _lis.chatClosing(_chat);
  }

  public String getNick()
  {
    return _thisNick;
  }
}

