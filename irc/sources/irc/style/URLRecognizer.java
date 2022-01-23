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

public class URLRecognizer implements WordRecognizer
{
  public boolean recognize(String word)
  {
    if(word.startsWith("http://")) return true;
    if(word.startsWith("ftp://")) return true;
    if(word.startsWith("www.")) return true;
    if(word.startsWith("ftp.")) return true;
    int a=word.indexOf('.');
    if(a==-1) return false;
    int b=word.lastIndexOf('.');
    if(a==b) return false;
    String ext=word.substring(b);
    if((ext.length()==2) || (ext.length()==3)) return true;
    return false;
  }
  
  public String getType()
  {
    return "url";
  }


}

