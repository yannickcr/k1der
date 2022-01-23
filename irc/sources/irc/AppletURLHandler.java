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

import java.awt.*;
import java.applet.*;
import java.net.*;

public class AppletURLHandler implements URLHandler
{

  private AppletContext _ctx;

  public AppletURLHandler(AppletContext ctx)
  {
    _ctx=ctx;
  }

  private URL decodeURL(String u) throws MalformedURLException
  {
    if(u.indexOf("://")==-1) u="http://"+u;
    return new URL(u);
  }

  public void stateURL(String url)
  {
    try
    {
      _ctx.showStatus(decodeURL(url).toString());
    }
    catch(Exception e)
    {
    }
  }
  
  public void openURL(String url)
  {
    try
    {
      _ctx.showDocument(decodeURL(url),"_blank");
    }
    catch(Exception e)
    {
    }
  }
}

