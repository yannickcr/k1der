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
import com.ms.security.*;

class SpecificSecuredServerSocket extends ServerSocket
{	

  public SpecificSecuredServerSocket(int port) throws IOException
  {
    super(port);
  }

  public Socket accept() throws IOException
  {
    try
    {
      PolicyEngine.assertPermission(PermissionID.NETIO);
      return super.accept();
    }
    catch(Throwable e)
    {
      return super.accept();
    }
  
  }

}

