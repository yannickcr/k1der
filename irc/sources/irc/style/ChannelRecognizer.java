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

public class ChannelRecognizer implements WordRecognizer
{
  public boolean recognize(String word)
  {
    return word.startsWith("#") && (word.length()>1);
  }
  
  public String getType()
  {
    return "channel";
  }

}
