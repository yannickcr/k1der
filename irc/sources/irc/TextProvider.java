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

public interface TextProvider
{
  public static final int INTERPRETOR_NOT_ON_CHANNEL=0x001;
  public static final int INTERPRETOR_UNKNOWN_DCC=0x002;
  public static final int INTERPRETOR_INSUFFICIENT_PARAMETERS=0x003;
  public static final int INTERPRETOR_BAD_CONTEXT=0x004;
  public static final int INTERPRETOR_CANNOT_CTCP_IN_DCCCHAT=0x005;
  public static final int INTERPRETOR_UNKNOWN_CONFIG=0x006;
  public static final int INTERPRETOR_TIMESTAMP_ON=0x007;
  public static final int INTERPRETOR_TIMESTAMP_OFF=0x008;
  public static final int INTERPRETOR_SMILEYS_ON=0x009;
  public static final int INTERPRETOR_SMILEYS_OFF=0x00A;
	public static final int INTERPRETOR_IGNORE_ON=0x00B;
	public static final int INTERPRETOR_IGNORE_OFF=0x00C;

  public static final int DCC_WAITING_INCOMING=0x101;
  public static final int DCC_UNABLE_TO_OPEN_CONNECTION=0x102;
  public static final int DCC_CONNECTION_ESTABLISHED=0x103;
  public static final int DCC_CONNECTION_CLOSED=0x104;
  public static final int DCC_ERROR=0x105;
  public static final int DCC_UNABLE_TO_SEND_TO=0x106;
  public static final int DCC_BAD_CONTEXT=0x107;
  public static final int DCC_NOT_CONNECTED=0x108;
  public static final int DCC_UNABLE_PASSIVE_MODE=0x109;
  public static final int CTCP_SECONDS=0x10A;
  public static final int DCC_STREAM_CLOSED=0x10B;

  public static final int IDENT_FAILED_LAUNCH=0x201;
  public static final int IDENT_REQUEST=0x202;
  public static final int IDENT_ERROR=0x203;
  public static final int IDENT_REPLIED=0x204;
  public static final int IDENT_DEFAULT_USER=0x205;
  public static final int IDENT_NO_USER=0x206;
  public static final int IDENT_RUNNING_ON_PORT=0x207;
  public static final int IDENT_LEAVING=0x208;
  public static final int IDENT_NONE=0x209;
  public static final int IDENT_UNKNOWN=0x20A;
  public static final int IDENT_UNDEFINED=0x20B;

  public static final int FILE_SAVEAS=0x301;

  public static final int ABOUT_ABOUT=0x401;
  public static final int ABOUT_PROGRAMMING=0x402;
  public static final int ABOUT_DESIGN=0x403;
  public static final int ABOUT_THANKS=0x404;
  public static final int ABOUT_SUPPORT=0x405;
  public static final int ABOUT_HELP=0x406;
  public static final int ABOUT_GPL=0x407;

  public static final int SERVER_UNABLE_TO_CONNECT_TO=0x501;
  public static final int SERVER_TRYING_TO_CONNECT=0x502;
  public static final int SERVER_DISCONNECTING=0x503;
  public static final int SERVER_CONNECTING=0x504;
  public static final int SERVER_NOT_CONNECTED=0x505;
  public static final int SERVER_UNABLE_TO_CONNECT=0x506;
  public static final int SERVER_LOGIN=0x507;
  public static final int SERVER_DISCONNECTED=0x508;
  public static final int SERVER_ERROR=0x509;

  public static final int SOURCE_YOU_KICKED=0x601;
  public static final int SOURCE_BY=0x602;
  public static final int SOURCE_STATUS=0x603;
  public static final int SOURCE_CHANLIST=0x604;
  public static final int SOURCE_CHANLIST_RETREIVING=0x605;
  public static final int SOURCE_HAS_JOINED=0x606;
  public static final int SOURCE_HAS_LEFT=0x607;
  public static final int SOURCE_HAS_BEEN_KICKED_BY=0x608;
  public static final int SOURCE_HAS_QUIT=0x609;
  public static final int SOURCE_TOPIC_IS=0x60A;
  public static final int SOURCE_CHANGED_TOPIC=0x60B;
  public static final int SOURCE_CHANNEL_MODE=0x60C;
  public static final int SOURCE_CHANNEL_MODE_IS=0x60D;
  public static final int SOURCE_USER_MODE=0x60E;
  public static final int SOURCE_ON=0x60F;
  public static final int SOURCE_KNOWN_AS=0x610;
  public static final int SOURCE_YOUR_MODE=0x611;
  public static final int SOURCE_YOUR_NICK=0x612;
	public static final int SOURCE_INFO=0x613;

  public static final int GUI_WHOIS=0x701;
  public static final int GUI_QUERY=0x702;
  public static final int GUI_KICK=0x703;
  public static final int GUI_BAN=0x704;
  public static final int GUI_KICKBAN=0x705;
  public static final int GUI_OP=0x706;
  public static final int GUI_DEOP=0x707;
  public static final int GUI_VOICE=0x708;
  public static final int GUI_DEVOICE=0x709;
  public static final int GUI_PING=0x70A;
  public static final int GUI_VERSION=0x70B;
  public static final int GUI_TIME=0x70C;
  public static final int GUI_FINGER=0x70D;
  public static final int GUI_RETREIVING_FILE=0x70E;
  public static final int GUI_SENDING_FILE=0x70F;
  public static final int GUI_BYTES=0x710;
  public static final int GUI_TERMINATED=0x711;
  public static final int GUI_FAILED=0x712;
  public static final int GUI_CLOSE=0x713;
  public static final int GUI_DISCONNECT=0x714;
  public static final int GUI_CHANNELS=0x715;
  public static final int GUI_HELP=0x716;
  public static final int GUI_PRIVATE=0x717;
  public static final int GUI_PUBLIC=0x718;
  public static final int GUI_CONNECT=0x719;
  public static final int GUI_ABOUT=0x71A;
  public static final int GUI_CHANGE_NICK=0x71B;
	
  public static final int ASL_YEARS=0x801;
  public static final int ASL_MALE=0x802;
  public static final int ASL_FEMALE=0x803;

  public static final int ERROR_NOT_DEFINED=0xffff;

  public String getString(int code);
}

