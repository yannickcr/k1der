package irc.style;

import java.awt.*;
import irc.*;

public class FormattedLabel extends Panel
{
  private FormattedStringDrawer _drawer;
  private String _str;
  private Font _fnt;
  
  public FormattedLabel(String s)
  {
    _fnt=new Font("Arial",Font.PLAIN,12);
    _drawer=new FormattedStringDrawer(_fnt,new SmileyTable());
    setString(s);		
  }
  
  public void setString(String s)
  {
    _str=s;
    repaint();
  }
  
  public void paint(Graphics g)
  {
    super.paint(g);
		int py=getHeight();
		int width=getWidth();
    _drawer.draw(_str,g,0,py,width,new MultipleWordCatcher(),0,width-1);
  }

  public static void main(String[] args)
  {
    Frame f=new Frame();
    FormattedLabel l=new FormattedLabel("\2Nous recherchons un mot de 7 lettres commançant par un D");
  //  FormattedLabel l=new FormattedLabel("h\31ello world");
    f.add(l);
    f.setSize(200,200);
    f.show();
  }
}

