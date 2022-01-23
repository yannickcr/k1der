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

package irc.style;

import java.util.*;

public class MultipleWordCatcher implements WordCatcher
{
  private Vector _recognizers;

  public MultipleWordCatcher()
  {
    _recognizers=new Vector();
  }
  
  public void addRecognizer(WordRecognizer wr)
  {
    _recognizers.insertElementAt(wr,_recognizers.size());
  }
  
  public String getType(String word)
  {
    Enumeration e=_recognizers.elements();
    while(e.hasMoreElements())
    {
      WordRecognizer wr=(WordRecognizer)e.nextElement();
      if(wr.recognize(word)) return wr.getType();
    }	
    return null;
  }	
}

