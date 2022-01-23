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

public class ModeHandler
{
  private String _mode;
  private String _limit;
  private String _password;
  
  public ModeHandler()
  {
    this("");
  }
  
  public ModeHandler(String mode)
  {
    _mode="";
    if(mode.startsWith("+")) mode=mode.substring(1);
    apply("+"+mode);
  }
  
  public void reset()
  {
    _mode="";
  }
  
  public String getPassword()
  {
    return _password;
  }
  
  public int getLimit()
  {
    return new Integer(_limit).intValue();
  }
  
  private void addMode(char mode)
  {
    if(hasMode(mode)) return;
    _mode+=mode;
  }
  
  private void removeMode(char mode)
  {
    if(!hasMode(mode)) return;
    int pos=_mode.indexOf(mode);
    _mode=_mode.substring(0,pos)+_mode.substring(pos+1);		
  }
  
  public void apply(String mode)
  {
	  boolean hadK=hasMode('k');
    String[] params=(new StringParser()).parseString(mode);
    int operation=1;
    for(int i=0;i<params[0].length();i++)
    {
      char c=params[0].charAt(i);
      if(c=='+')
      {
        operation=+1;
      }
      else if(c=='-')
      {
        operation=-1;
      }
      else
      {
        if(operation>0) addMode(c);
        if(operation<0) removeMode(c);
      }
    }
    boolean hasK=hasMode('k');
    boolean hasL=hasMode('l');
    
    int until=0;
    if(hasK && hasL)
    {
      if(params.length<=1) return;
      _limit=params[1];
      if(params.length<=2) return;
      _password=params[2];
      until=2;
    }
    else if(hasL)
    {
      if(params.length<=1) return;
      _limit=params[1];
      until=1;
    }
    else if(hasK)
    {
      if(params.length<=1) return;
      _password=params[1];
      until=1;
    }

    if(hadK && !hasK) until++;

    if(params.length>until+1)
    {
      String toApply="";
      for(int i=until+1;i<params.length;i++) toApply=toApply+params[i];
      apply(toApply);
    }	
  }
  
  public boolean hasMode(char mode)
  {
    return _mode.indexOf(mode)!=-1;
  }
  
  public String getMode()
  {
    if(_mode.length()==0) return "";
    byte[] mode=_mode.getBytes();
    for(int i=0;i<mode.length;i++)
    {
      int small=i;
      for(int j=i+1;j<mode.length;j++)
      {
        if(mode[j]<mode[small]) small=j;
      }
      byte tmp=mode[small];
      mode[small]=mode[i];
      mode[i]=tmp;
    }
    String ans="+"+new String(mode);
    if(hasMode('l')) ans+=" "+_limit;
    if(hasMode('k')) ans+=" "+_password;
    return ans;
  }	
}

