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
import java.util.*;
import irc.security.*;

public class DCCFile
{
  private OutputStream _os;
  private InputStream _is;
  private File _file;
  private Hashtable _listeners;
  private boolean _down=false;
  private int _size;
  private int _count;
  private DCCFileHandler _handler;

  public DCCFile(File f,DCCFileHandler handler)
  {
    _listeners=new Hashtable();
    _handler=handler;
    _count=0;
    _file=f;
  }
  
  public void addDCCFileListener(DCCFileListener lis)
  {
    _listeners.put(lis,lis);
  }
  
  public void removeDCCFileListener(DCCFileListener lis)
  {
    _listeners.remove(lis);
  }
  
  private void triggerTransmitted()
  {
    if((_count&4095)!=0) return;
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      DCCFileListener lis=(DCCFileListener)e.nextElement();
      lis.transmitted(_count);
    }
  }
  
  private void triggerFinished()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      DCCFileListener lis=(DCCFileListener)e.nextElement();
      lis.finished();
    }
  }
  
  private void triggerFailed()
  {
    Enumeration e=_listeners.elements();
    while(e.hasMoreElements())
    {
      DCCFileListener lis=(DCCFileListener)e.nextElement();
      lis.failed();
    }
  }
  
  public void prepareSend()
  {
    try
    {
      _size=SecurityProvider.getSecurityProvider().getFileSize(_file);
   //   _size=(int)_file.length();		
      _is=new BufferedInputStream(SecurityProvider.getSecurityProvider().getFileInputStream(_file));
      _down=false;
    }
    catch(Exception e)
    {
    }
  }
  
  public byte readByte()
  {
    try
    {
      _count++;
      triggerTransmitted();
      return (byte)_is.read();
    }
    catch(Exception e)
    {
      return -1;
    }
  }
  
  public int getSize()
  {
    return _size;
  }
  
  public boolean isUploading()
  {
    return !isDownloading();
  }
  
  public boolean isDownloading()
  {
    return _down;
  }
  
  public void fileSent()
  {
    try
    {
      triggerFinished();
      _is.close();
    }
    catch(Exception e)
    {
    }
  }
  
  public void fileSentFailed()
  {
    try
    {
      triggerFailed();
      _is.close();
    }
    catch(Exception e)
    {
    }
  }
  
  public void prepareReceive(int size)
  {
    _down=true;
    _size=size;
    try
    {
      _os=new BufferedOutputStream(SecurityProvider.getSecurityProvider().getFileOutputStream(_file));
    }
    catch(Exception e)
    {
      _os=null;
    }
  }
  
  public void byteReceived(byte b)
  {
    try
    {
      _count++;
      triggerTransmitted();
      _os.write(b);
    }
    catch(Exception e)
    {
    }
  }
  
  public void fileReceived()
  {
    try
    {
      triggerFinished();
      _os.close();
    }
    catch(Exception e)
    {
    }

  }
  
  public void fileReceiveFailed()
  {
    try
    {
      triggerFailed();
      _os.close();	
    }
    catch(Exception e)
    {
    }
  }
  
  public String getName()
  {
    return _file.getName();
  }
  
  public void leave()
  {
    _handler.close();
  }
}

