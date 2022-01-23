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

import java.net.*;
import java.io.*;
import irc.security.*;

public class DCCFileHandler extends IRCObject implements Runnable
{
  private Socket _socket;
  private ServerSocket _serverSocket;
  private Thread _thread;
  
  private OutputStream _os;
  private InputStream _is;
  private DCCFile _file;
  
  private String _remoteNick;
  private DCCFileClosingListener _lis;
  private int _action;
  private int _size;
  private boolean _listening;
  

  public DCCFileHandler(IRCConfiguration config,String remoteNick,DCCFileClosingListener lis)
  {
    super(config);
    _action=0;
    _size=0;
    _lis=lis;
    _remoteNick=remoteNick;
  }
  
  public void receive(DCCFile file,String ip,String port,String size)
  {
    _file=file;
    _size=new Integer(size).intValue();
    _file.prepareReceive(_size);
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
      _is=new BufferedInputStream(_socket.getInputStream());
      _os=new BufferedOutputStream(_socket.getOutputStream());
      _thread=new Thread(this,"DCCFile thread");
      _thread.start();
    }
    catch(Exception e)
    {
    }
    
  }
  
  public String send(DCCFile file)
  {
    _action=2;
    _file=file;
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
      
      _file.prepareSend();
      int size=file.getSize();
      String sip=""+high;
      _listening=false;			
      _thread=new Thread(this,"DCCFile thread");
      _thread.start();
      while(!_listening) Thread.yield();
      return sip+" "+port+" "+size;
    }
    catch(Exception e)
    {
      return "";
    }
  }
  
  private void writeConf(OutputStream os,int v) throws IOException
  {
    os.write((v>>24)&255);
    os.write((v>>16)&255);
    os.write((v>>8)&255);
    os.write((v)&255);
    os.flush();
  }
  
  private int readConf(InputStream is) throws IOException
  {
    int b1=is.read();if(b1<0) b1+=256;
    int b2=is.read();if(b2<0) b2+=256;
    int b3=is.read();if(b3<0) b3+=256;
    int b4=is.read();if(b4<0) b4+=256;
    return (b1<<24)+(b2<<16)+(b3<<8)+b4;
  }
  
  public void run()
  {
    if(_action==1) //receive
    {
      try
      {
        for(int i=0;i<_size;i++)
        {
          int ib=_is.read();
          if(ib==-1) throw new Exception(getText(TextProvider.DCC_STREAM_CLOSED));
          byte b=(byte)ib;
          _file.byteReceived(b);
          if((i+1)%4096==0)
          {
            Thread.yield();
            writeConf(_os,i+1);
          }
        }
        writeConf(_os,_size);
        _file.fileReceived();
      }
      catch(Exception e)
      {
    //    e.printStackTrace();
        _file.fileReceiveFailed();
      }
      cleanup();
    }
    else if(_action==2) //send
    {
      _listening=true;
      try
      {
        _serverSocket.setSoTimeout(30000);
        _socket=_serverSocket.accept();
        _os=new BufferedOutputStream(_socket.getOutputStream());
        _is=_socket.getInputStream();
        int size=_file.getSize();
        for(int i=0;i<size;i++)
        {
          byte b=_file.readByte();
          _os.write(b);
        }
        _os.flush();
        int rec=0;
        while(rec!=size)
        {
          rec=readConf(_is);
        }
        _os.close();
        _file.fileSent();
      }
      catch(Exception e)
      {
    //    e.printStackTrace();
        _file.fileSentFailed();
      }
      cleanup();
      
    }		
  }

  private void cleanup()
  {
    try
    {
      if(_socket!=null) _socket.close();
      if(_serverSocket!=null) _serverSocket.close();
      _is.close();
      _os.close();
    }
    catch(Exception e)
    {
    }
  }

  public void close()
  {
    cleanup();
    _lis.fileClosing(_file);
  }
  
}

