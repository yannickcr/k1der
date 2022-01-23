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

package irc.security;

import java.io.*;
import java.net.*;
import java.awt.*;
import com.ms.security.*;

class SpecificSecuredProvider implements SecuredProvider
{
  
  public Socket getSocket(String host,int port) throws UnknownHostException,IOException
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.NETIO);
      return new Socket(host,port);
    }
    catch(Throwable e)
    {
      return new Socket(host,port);
    }
  }
  
  public ServerSocket getServerSocket(int port) throws IOException
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.NETIO);
      return new SpecificSecuredServerSocket(port);
    }
    catch(Throwable e)
    {
      return new SpecificSecuredServerSocket(port);
    }
  }
  
  public FileInputStream getFileInputStream(File file) throws FileNotFoundException
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.FILEIO);
      return new FileInputStream(file);
    }
    catch(Throwable e)
    {
      return new FileInputStream(file);
    }
  }
 
  public int getFileSize(File file)
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.FILEIO);
      return (int)file.length();
    }
    catch(Throwable e)
    {
      return (int)file.length();
    }			
  }
  
  public FileOutputStream getFileOutputStream(File file) throws FileNotFoundException
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.FILEIO);
      return new FileOutputStream(file);
    }
    catch(Throwable e)
    {
      return new FileOutputStream(file);
    }
  }
  
  public FileDialog getFileDialog(Frame top,String title,int type)
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.UI);
      return new FileDialog(top,title,type);
    }
    catch(Throwable e)
    {
      return new FileDialog(top,title,type);
    }
  }
 
  public File getLoadFile(String title)
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.UI);
      FileDialog dlg=new FileDialog(new Frame(),title,FileDialog.LOAD);
      dlg.show();
      return new File(dlg.getDirectory()+dlg.getFile());
    }
    catch(Throwable e)
    {
      FileDialog dlg=new FileDialog(new Frame(),title,FileDialog.LOAD);
      dlg.show();
      return new File(dlg.getDirectory()+dlg.getFile());
    }
  }
  
  public File getSaveFile(String title)
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.UI);
      FileDialog dlg=new FileDialog(new Frame(),title,FileDialog.SAVE);
      dlg.show();
      return new File(dlg.getDirectory()+dlg.getFile());
    }
    catch(Throwable e)
    {
      FileDialog dlg=new FileDialog(new Frame(),title,FileDialog.SAVE);
      dlg.show();
      return new File(dlg.getDirectory()+dlg.getFile());
    }
  }
  
  public InetAddress getLocalHost() throws UnknownHostException
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.NETIO);
      InetAddress[] addresses=InetAddress.getAllByName(InetAddress.getLocalHost().getHostName());
      return addresses[addresses.length-1];
    }
    catch(Throwable e)
    {
      InetAddress[] addresses=InetAddress.getAllByName(InetAddress.getLocalHost().getHostName());
      return addresses[addresses.length-1];		
    }
  }
  
  public String resolve(InetAddress addr)
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.NETIO);
      return addr.getHostName();
    }
    catch(Throwable e)
    {
      return addr.getHostName();
    }

  }
  
  public boolean tryProvider()
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.FILEIO);
      PolicyEngine.assertPermission(PermissionID.NETIO);
      PolicyEngine.assertPermission(PermissionID.UI);
      return true;
    }
    catch(Throwable e)
    {
      return false;
    }
  }
  
  public String getName()
  {
    return "Microsoft Internet Explorer Security Provider";
  }		
}

