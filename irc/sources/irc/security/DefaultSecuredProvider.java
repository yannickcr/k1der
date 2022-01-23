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


class DefaultSecuredProvider implements SecuredProvider
{

  public Socket getSocket(String host,int port) throws UnknownHostException,IOException
  {
    return new Socket(host,port);
  }
  
  public ServerSocket getServerSocket(int port) throws IOException
  {
    return new ServerSocket(port);
  }
  
  public FileInputStream getFileInputStream(File file) throws FileNotFoundException
  {
    return new FileInputStream(file);
  }
  
  public FileOutputStream getFileOutputStream(File file) throws FileNotFoundException
  {
    return new FileOutputStream(file);
  }
  
  public int getFileSize(File file)
  {
    return (int)file.length();
  }

  public File getLoadFile(String title)
  {
    FileDialog dlg=new FileDialog(new Frame(),title,FileDialog.LOAD);
    dlg.show();
    return new File(dlg.getDirectory()+dlg.getFile());
  }
  
  public File getSaveFile(String title)
  {
    FileDialog dlg=new FileDialog(new Frame(),title,FileDialog.SAVE);
    dlg.show();
    return new File(dlg.getDirectory()+dlg.getFile());
  }

  public InetAddress getLocalHost() throws UnknownHostException
  {
    InetAddress[] addresses=InetAddress.getAllByName(InetAddress.getLocalHost().getHostName());
    return addresses[addresses.length-1];
  }

  public String resolve(InetAddress addr)
  {
    return addr.getHostName();	
  }

  public boolean tryProvider()
  {
    return true;
  }
  
  public String getName()
  {
    return "Default Security Provider";
  }

}

