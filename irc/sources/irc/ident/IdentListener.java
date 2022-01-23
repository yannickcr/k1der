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

package irc.ident;

public interface IdentListener
{
  public static final int IDENT_ERROR=-1;
  public static final int IDENT_OK=0;
  public static final int IDENT_DEFAULT=1;
  public static final int IDENT_NOT_FOUND=2;
  
  public void identRequested(String source,int result,String reply);
  public void identRunning(int port);
  public void identLeaving(String message);

}

