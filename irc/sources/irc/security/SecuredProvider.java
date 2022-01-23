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


import java.awt.*;
import java.io.*;
import java.net.*;

interface SecuredProvider
{
  public Socket getSocket(String host,int port) throws UnknownHostException,IOException;	
  public ServerSocket getServerSocket(int port) throws IOException;
  public FileInputStream getFileInputStream(File file) throws FileNotFoundException;
  public FileOutputStream getFileOutputStream(File file) throws FileNotFoundException;
  public int getFileSize(File file);
  public File getLoadFile(String title);
  public File getSaveFile(String title);
  public InetAddress getLocalHost() throws UnknownHostException;
  public String resolve(InetAddress addr);
  
  public boolean tryProvider();
  public String getName();
}

