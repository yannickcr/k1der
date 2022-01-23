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

import java.util.*;

public class StringParser
{

  public static String trim(String t)
  {
    String res="";
    int a=0;
    while((a<t.length()) && (t.charAt(a)==' ')) a++;
    if(a==t.length()) return "";
    int b=t.length()-1;
    while((b>=0) && (t.charAt(b)==' ')) b--;
    if(b<0) return "";
    return t.substring(a,b+1);			
  }

  public String[] parseString(String line)
  {
    Vector res=new Vector();
    while(line.length()!=0)
    {
      int pos=line.indexOf(' ');
      if(pos==-1)
      {
        res.insertElementAt(line,res.size());
        line="";
      }
      else
      {
        String part=trim(line.substring(0,pos));
        line=trim(line.substring(pos));
        res.insertElementAt(part,res.size());				
      }
    }
    
    String[] param=new String[res.size()];
    for(int i=0;i<res.size();i++) param[i]=(String)res.elementAt(i);
    return param;
  
  }

}

