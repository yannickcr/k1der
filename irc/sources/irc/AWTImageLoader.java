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

public class AWTImageLoader implements ImageLoader
{
  public Image getImage(String source)
	{
	  Toolkit tk=Toolkit.getDefaultToolkit();
	  return tk.getImage(source);
	}
}

