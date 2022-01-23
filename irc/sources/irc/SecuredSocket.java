package irc;

import java.io.*;
import java.net.*;
import irc.security.*;

public class SecuredSocket
{
  private Socket _sock;

  public SecuredSocket(String host,int port) throws UnknownHostException,IOException
  {
    _sock=SecurityProvider.getSecurityProvider().getSocket(host,port);
  }

  public Socket getSocket()
  {
    return _sock;
  }
}

