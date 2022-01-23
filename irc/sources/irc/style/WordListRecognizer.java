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

public class WordListRecognizer implements WordRecognizer
{
  private String[] _list;

  public WordListRecognizer()
  {
    setList(new String[0]);
  }
  
  public void setList(String[] list)
  {
    _list=new String[list.length];
    for(int i=0;i<list.length;i++) _list[i]=list[i].toLowerCase();
  }
  
  public boolean recognize(String word)
  {
    String lcase=word.toLowerCase();
    for(int i=0;i<_list.length;i++) if(lcase.equals(_list[i])) return true;
    return false;
  }
  
  public String getType()
  {
    return "wordlist";
  }

}

