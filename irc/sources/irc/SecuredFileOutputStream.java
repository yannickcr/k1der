package irc;

import java.io.*;
import irc.security.*;

public class SecuredFileOutputStream
{

  private FileOutputStream _stream;

  public SecuredFileOutputStream(File f) throws FileNotFoundException
	{
	  _stream=SecurityProvider.getSecurityProvider().getFileOutputStream(f);
	}
	
	public FileOutputStream getFileOutputStream()
	{
	  return _stream;
	}

}

