Plouf's Java IRC Client Applet
------------------------------

Webmaster's manual
------------------

Files
-----

  Summary :
    irc.cab
    securedirc.cab
    irc.jar
    IRCApplet.class

  Details :
    irc.cab : Internet Explorer archive file - needed
    securedirc.cab : Internet Explorer signed archive file - optional
    irc.jar : Netscape (and other browsers) archive file - needed
    IRCApplet.class : program launcher - needed

Applet html fragment
--------------------

  <applet code=IRCApplet.class archive="irc.jar" width=640 height=400>
  <param name="CABINETS" value="irc.cab,securedirc.cab">

  ...
  optional parameters
  ...

  </applet>

  width and height may be adapted as needed
  securedirc.cab may be removed

Parameters
----------
  parameters are passed to the applet via the following syntax :
    <param name="name" value="value">

Mandatory parameters
--------------------

  nick : default nick to be used. '?' characters will be replaced by random numbers.
    Example :
      <param name="nick" value="Guest??">   will tell the applet to use nicks
                                            such as Guest47

  name : "real" user name, sent to IRC server
    Example :
      <param name="name" value="UserName">


  host : IRC server host
    Example :
      <param name="host" value="irc.server.net">

  port : IRC server port
    Example :
      <param name="port" value="6667">

Optional parameters
-------------------

  commandX, with X a figure : Tells the applet to execute this command once connected
  --------                    to the server.

                              The first command MUST be command1, and there can be no
                              "gap" in the numbers : the command14 MUST be after command13, 
                              and NOT after command12.

                              The commands are NOT passed through the interpretor, as a
                              result, only commands that are directly understood by the
                              server may be used. Commands should NOT begin with '/'.

    Example : 
      <param name="command1" value="nickserv identify password">
      <param name="command2" value="join #channel">

  basecolor : Tells the applet to compute the interface colors from three given values, in
  ---------   red, green and blue. These values goes through 0 to 1000.

    Example :
      <param name="basecolor" value="384,256,128">

  colorI, with I a figure : Tells the applet to modify the given color index to the given
  ------                    color. This command is processed AFTER the basecolor command.

                            The actual number of colors, as their meaning, may vary from
                            an interface to another.

    Example :
      <param name="color1" value="C0C000">

  helppage : configures the help page to be opened in a new browser window when the help
  --------   button is clicked by the user on the interface.

    Example :
      <param name="helppage" value="http://www.yahoo.com">

  timestamp : switches on or off the timestamp option. By default, the timestamping is
  ---------   not activated.

    Example :
      <param name="timestamp" value="true">

  language : sets the langage to be used. Supported langages are english, french and italian.
  --------   By default, the langage is english.

    Example :
      <param name="language" value="french">

  smileys : sets wether smileys should be replaced by graphical representations. By default,
  -------   graphical smileys are switched off.

    Example :
      <param name="smileys" value="true">

  highlight : enable or disable highlights. If highlights are not enabled, no highlight will
  ---------   be performed, regardless of any other highlight option. By default, highlights
              are disabled.

    Example :
      <param name="highlight" value="true">

  highlightnick : if highlight is enabled, any received message containing the current user
  -------------   nick will be highlighted. By default, nick highlight is switched off.

    Example :
      <param name="highlightnick" value="true">

  highlightcolor : if highlight is enabled, specifies the highlight color to be used. By
  --------------   default, highlight color is 5.

    Example :
      <param name="highlightcolor" value="9">

  highlightwords : if highlight is enabled, gives a list of words triggering highlight. Words
  --------------   are separated using spaces.

    Example :
      <param name="highlightwords" value="word1 word2 word3">

  quitmessage : sets the quit message. By default, this message is empty.
  -----------

    Example :
      <param name="quitmessage" value="PJIRC forever!">

  asl : enable or disable asl handling. Asl (for age, sex and localtion) is parsed from the full
  ---   user name. Other parts of the software may behave differently provided the nick is male
        or female, its age, and so on... The full name format is expected to be "age sex localtion",
        for instance "22 m Belgium". By default, asl is disabled.

    Example :
      <param name="asv" value="true">

  aslmale : set the string corresponding to the male gender in the full name for asl parsing. Default
  -------   value is "m".

    Example :
      <param name="aslmale" value="m">

  aslfemale : set the string corresponding to the female gender in the full name for asl parsing.
  ---------   Default value is "f".

    Example :
      <param name="aslfemale" value="f">

  showconnect : enable or disable connect menu button visibility. By default, the connect button is
  -----------   visible.

    Example :
      <param name="showconnect" value="true">

  showchanlist : enable or disable chanlist menu button visibility. By default, the chanlist button is
  ------------   visible.

    Example :
      <param name="showchanlist" value="true">

  showabout : enable or disable about menu button visibility. By default, the about button is
  ---------   visible.

    Example :
      <param name="showabout" value="true">

  showhelp : enable or disable help menu button visibility. By default, the help button is
  --------   visible.

    Example :
      <param name="showhelp" value="true">

  bitmapsmileys : enable or disable bitmap smileys. Once enabled, bitmaps are defined via the
  -------------   smiley parameter. By default, bitmap smileys are disabled. This parameter
                  has no effect if smileys are disabled.

    Example :
      <param name="bitmapsmileys" value="true">

  smileyX : set the Xnth smiley. A smiley is a pair of text->image. Each time the text is found
  -------   on a line, it will be replaced by the corresponding image. As for the command
            parameter, the first smiley must be smiley1 and there must'nt be any gap. The
            format of the parameter is "text image", where image is any URL the applet can
            access.

    Example :
      <param name="smiley1" value=":) img/smile.gif">
      <param name="smiley2" value=":( img/sad.gif">

  nicklistwidth : set the width, in pixel, of the right channels nicklist bar. Default value is
  -------------   130.

    Example :
      <param name="nicklistwidth" value="130">

  channelfont : set the font used for channel messages drawing. Format is "size name". By default,
  -----------   the font is "12 MonoSpaced".

    Example :
      <param name="channelfont" value="12 Monospaced">

  chanlistfont : set the font used for chanlist drawing. Format is "size name". By default,
  ------------   the font is "12 Monospaced".

    Example :
      <param name="chanlistfont" value="12 Monospaced">

  useinfo : replace the status window par the info window. The info window acts exactly as
  -------   the status window, but only shows motd and welcome messages. Since whois etc...
            results are no more shown, popup commands such as whois, finger, etc... are
            disabled. By default, the info window is disabled.

    Example :
      <param name="useinfo" value="false">

  nickfield : show a nick input field in the bottom right of the application. By default,
  ---------   this option is disabled.

    Example :
      <param name="nickfield" value="false">

  chanlisttextcolorX : modifiy the Xth (from 0 through 15) color of the chanlist text.
  ------------------

    Example :
      <param name="chanlisttextcolor4" value="FF00FF">

  defaultsourcetextcolorX : modify the Xth (from 0 through 15) default color of the
  -----------------------   source (channels, status, private, ...) text.

    Example :
      <param name="defaultsourcetextcolor0" value="00FF00">

  sourcecolorconfigN : advanced color configuration instruction number N. As for the command
  ------------------   parameter, N must be without gap. The syntax of the value is :
                       "SourceName ColorIndex ColorValue". SourceName is any source name,
                       such as #mychannel or nick. Status is a valid SourceName.

    EXample :
      <param name="sourcecolorconfig1" value="status 0 000000">
      <param name="sourcecolorconfig2" value="#channel 4 FFFF00">

Certification
-------------

  securedirc.cab is a signed cabinet, with full permission scoping. It is needed to use
  specific features such as dcc and ident server. If this file is missing, the user
  won't be prompted to accept the certificate, but these features will be disabled.

  Certification can only be enabled on Microsoft Internet Explorer.

  Note : If certification is disabled, the applet won't be able to contact the IRC server
         unless this server is on the same host than the http server the applet is loaded
         from.

  You're STRONGLY ENCOURAGED to replace the securedirc.cab file by a secured file using
  your own certificate (a securedirc-unisgned.cab file should be provided in the package),
  or not to use it.

Features
--------

  CTCP codes
    ACTION VERSION PING TIME FINGER USERINFO CLIENTINFO DCC

  DCC support
    DCC file transfert (certification only)
    DCC chat (certification only)

  Nick-completion

  Ident server (certification only)

  URL detection

  HighLight

  ASL parsing

  ... much more :)


Minimal html fragment
---------------------

<applet code=IRCApplet.class archive="irc.jar" width=640 height=400>
<param name="CABINETS" value="irc.cab,securedirc.cab">

<param name="nick" value="Anonymous???">
<param name="name" value="Java User">
<param name="host" value="irc.dal.net">
<param name="port" value="6667">

</applet>

Contacts
--------

PJIRC is developped by Plouf - theplouf@yahoo.com
Have a look at http://groups.yahoo.com/group/pjirc/ for news about PJIRC.

Version history
---------------

  1.4b : 31/05/2002
  ----
    Initial version

  1.41b : 14/06/2002
  -----
    User count on channels
    Highlight support
    Quit message

  1.411b : 05/08/2002
  ------
    Fixed /notice alias bug

  1.42b : 01/10/2002
  -----
    Handling semi-op (%) user flag

  1.5b : 13/10/2002
  ----
    ASL handling
    ShowXXX commands
    Problems with autojoin or other "on connect" commands fixed

  1.6b : 17/10/2002
  ----
    More complete ASL handling
    Bitmap graphical smileys
    New nicklistwidth, channelfont, chanlistfont and useinfo commands
    Chanlist horizontal scrolling bug with incorrect mouse hit-box fixed
    Moved unicode special characters to 0xE000 user-defined bank, in order
    to avoid problems with mac users
    Nick input field
    Some other minor changes, fixes and updates
    More than 12,000 lines of java so far...

  1.61 : 23/10/2002
  ----
    Scrollbars now scroll when mouse boutton is maintained down
    Chanlist window now optimized (expect dramatic speed increase)
    Topic may be scrolled from left to right using mouse
    Topic now contains smileys
    Chanlist window now shows first channels when openned
    Word and url catcher is now more clever, it understands that 
    <nick> matches nick. Same apply for (nick), "nick", etc...
    Some bugs fixed

  1.611 : 24/10/2002
  -----
    Deadlock bug that might occured with scrollbars is now fixed

  1.62 : 28/10/2002
  ----
    [ and ] characters are no more ignored in word catcher
    Sources (Channels, Status, Privates, ...) and Chanlist text color is now
    configurable.
