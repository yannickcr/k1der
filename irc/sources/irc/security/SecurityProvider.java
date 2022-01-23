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

public class SecurityProvider
{

  private SecuredProvider _provider;

  private static final SecurityProvider _securityProvider=new SecurityProvider();

  protected SecurityProvider()
  {
    try
    {
      Class cl=Class.forName("irc.security.SpecificSecuredProvider");
      _provider=(SecuredProvider)cl.newInstance();
      if(!_provider.tryProvider()) throw new Exception();
    }
    catch(Exception ex)
    {
      _provider=new DefaultSecuredProvider();
    }
  }

  public static final SecurityProvider getSecurityProvider()
  {
    return _securityProvider;
  }

  public String getProviderName()
  {
    return _provider.getName();
  }

  public Socket getSocket(String host,int port) throws UnknownHostException,IOException
  {
    return _provider.getSocket(host,port);
  }
  
  public ServerSocket getServerSocket(int port) throws IOException
  {
    return _provider.getServerSocket(port);
  }
  
  public FileInputStream getFileInputStream(File file) throws FileNotFoundException
  {
    return _provider.getFileInputStream(file);
  }
  
  public FileOutputStream getFileOutputStream(File file) throws FileNotFoundException
  {
    return _provider.getFileOutputStream(file);
  }
  
  public int getFileSize(File file)
  {
    return _provider.getFileSize(file);
  }

  public File getLoadFile(String title)
  {
    return _provider.getLoadFile(title);
  }
  
  public File getSaveFile(String title)
  {
    return _provider.getSaveFile(title);
  }

  public InetAddress getLocalHost() throws UnknownHostException
  {
    return _provider.getLocalHost();
  }
  
  public String resolve(InetAddress addr)
  {
    return _provider.resolve(addr);
  }
  

}

