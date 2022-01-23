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

public class AppletImageLoader implements ImageLoader
{
  private Applet _app;
	
  public AppletImageLoader(Applet app)
	{
	  _app=app;
	}
	
  public Image getImage(String source)
	{
	  try
		{
	    return _app.getImage(new URL(_app.getCodeBase(),source));
		}
		catch(MalformedURLException ex)
		{
		  return null;
		}
	}
}

