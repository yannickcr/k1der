package irc;

import java.io.*;
import irc.security.*;

public class SecuredFileInputStream
{
  private FileInputStream _stream;

  public SecuredFileInputStream(File f) throws FileNotFoundException
	{
	  _stream=SecurityProvider.getSecurityProvider().getFileInputStream(f);
	}
	
	public FileInputStream getFileInputStream()
	{
	  return _stream;
	}

}

