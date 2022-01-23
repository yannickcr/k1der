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

package irc.ident;

import java.io.*;
import java.net.*;
import java.util.*;
import irc.*;
import irc.security.*;

class LocalInfo
{
  
  public LocalInfo(int localPort,String system,String id)
  {
    this.localPort=localPort;
    this.system=system;
    this.id=id;
  }
  
  public int localPort;
  public String system;
  public String id;
}

public class IdentServer extends IRCObject implements Runnable
{  
  private Thread _thread;
  private boolean _running;
  private Hashtable _table;
  private ServerSocket _serverSocket;
  private boolean _defaultUser;
  private String _system;
  private String _id;
  private Hashtable _listeners;
  private int _port;
  
  public IdentServer(IRCConfiguration config)
  {
    super(config);
    resetDefaultUser();
    _table=new Hashtable();
    _listeners=new Hashtable();
  }
  
  public void start() throws Exception
  {
    start(113);
  }
  
  public void resetDefaultUser()
  {
    _defaultUser=false;
  }
  
  public void setDefaultUser(String system,String id)
  {
    _defaultUser=true;
    _system=system;
    _id=id;
  }
  
  public void start(int port) throws Exception
  {
    _port=port;
    _running=false;
    _serverSocket=SecurityProvider.getSecurityProvider().getServerSocket(0);
    _thread=new Thread(this,"IDENT server");
    _thread.start();
    while(!_running) Thread.yield();
  }
  
  public void stop()
  {
    try
    {
      _serverSocket.close();
      _thread.join();
    }
    catch(Exception e)
    {
     // e.printStackTrace();
    }
  }
  
  public synchronized void registerLocalConnection(int localPort,String system,String id)
  {
    _table.put(new Integer(localPort),new LocalInfo(localPort,system,id));
  }
  
  public synchronized void unregisterLocalConnection(int localPort)
  {
    _table.remove(new Integer(localPort));
  }
  
  private synchronized LocalInfo processRequest(int localPort)
  {
    return (LocalInfo)_table.get(new Integer(localPort));
  }
  
  public synchronized void addIdentListener(IdentListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public synchronized void removeIdentListener(IdentListener lis)
  {
    _listeners.remove(lis);
  }
  
  public synchronized void triggerIdentListeners(String from,int result,String reply)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      IdentListener lis=(IdentListener)e.nextElement();
      lis.identRequested(from,result,reply);		
    }
  }
  
  public synchronized void triggerRunningListeners(int port)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      IdentListener lis=(IdentListener)e.nextElement();
      lis.identRunning(port);		
    }
  }
  
  public synchronized void triggerLeavingListeners(String message)
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      IdentListener lis=(IdentListener)e.nextElement();
      lis.identLeaving(message);		
    }
  }
  
  public void run()
  {
    boolean terminated=false;
    while(!terminated)
    {
      try
      {
        _running=true;
        triggerRunningListeners(_port);
        Socket s=_serverSocket.accept();
        String from=getText(TextProvider.IDENT_UNKNOWN);
        int result=IdentListener.IDENT_ERROR;
        String reply=getText(TextProvider.IDENT_NONE);
        try
        {
          try
          {
            from=SecurityProvider.getSecurityProvider().resolve(s.getInetAddress());
          }
          catch(Exception e)
          {
            from=s.getInetAddress().getHostAddress() ;
          }
          BufferedReader reader=new BufferedReader(new InputStreamReader(s.getInputStream()));
          BufferedWriter writer=new BufferedWriter(new OutputStreamWriter(s.getOutputStream()));
          String request=reader.readLine();
          int pos=request.indexOf(',');
          String serverSide=request.substring(0,pos).trim();
          String clientSide=request.substring(pos+1).trim();		
        
          LocalInfo info=processRequest(new Integer(serverSide).intValue());
          reply=serverSide+" , "+clientSide+" : ";
          if(info==null)
          {
            if(!_defaultUser)
            {
              result=IdentListener.IDENT_NOT_FOUND;
              reply+="ERROR : NO-USER";
            }
            else
            {
              result=IdentListener.IDENT_DEFAULT;
              reply+="USERID : "+_system+" : "+_id;
            }
          }
          else
          {
            result=IdentListener.IDENT_OK;
            reply+="USERID : "+info.system+" : "+info.id;
          }
        
          writer.write(reply+"\n");
          writer.flush();
          reader.close();
          writer.close();
          s.close();
          triggerIdentListeners(from,result,reply);
        }
        catch(Exception e)
        {
          triggerIdentListeners(from,IdentListener.IDENT_ERROR,e.getMessage());
        }
      }
      catch(Exception e)
      {
        triggerLeavingListeners(e.getMessage());
        terminated=true;
      }
    }
  }
  
}

