package irc;

import java.io.*;
import java.net.*;
import irc.security.*;

public class SecuredServerSocket
{
  private ServerSocket _socket;

  public SecuredServerSocket(int port) throws IOException
	{
	  _socket=SecurityProvider.getSecurityProvider().getServerSocket(port);
	}
	
	public ServerSocket getServerSocket()
	{
	  return _socket;
	}
}

