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

import java.io.*;

public interface DCCListener
{
  public File DCCFileRequest(String nick,String fileName,int size);
  public void DCCFileCreated(DCCFile file);
  public void DCCFileRemoved(DCCFile file);
  
  public boolean DCCChatRequest(String nick);
  public void DCCChatCreated(DCCChat chat,boolean bring);
  public void DCCChatRemoved(DCCChat chat);
}

