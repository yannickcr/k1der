<SCRIPT language=JavaScript 
src="dream.js"></SCRIPT>

<SCRIPT language=JavaScript 
src="schedule.js"></SCRIPT>

<META content="MSHTML 6.00.2600.0" name=GENERATOR></HEAD>
<BODY onLoad="initDate();updateImageMap('');">

<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="439" height="22" valign="baseline"> <div align="right"></div></td>
    <td width="38" height="42" rowspan="2"><img src="images/oeuf2.gif" width="31" height="42"></td>
  </tr>
  <tr> 
    <td width="439" height="20" background="images/fond.gif"><img src="images/courbe1.gif" width="7" height="20" align="absmiddle"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF">-=Nous 
      d&eacute;fier=-</font></b></font></td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
        <FORM name=challenge onSubmit="return verifDatas();" action=defier2.php 
      method=post>
  <TABLE cellSpacing=0 cellPadding=5 width="99%" border=0>
    <TBODY>
      <TR align="left"> 
        <TD colspan="2" vAlign=top noWrap> 
          <select 
            class=inputImage 
            onChange="initDate('m',this.options[selectedIndex].value);" 
            name=month>
            <option value=0 <? if (date("n") == 1) { echo "selected"; } ?>>Janvier</option>
            <option value=1 <? if (date("n") == 2) { echo "selected"; } ?>>Février</option>
            <option value=2 <? if (date("n") == 3) { echo "selected"; } ?>>Mars</option>
            <option value=3 <? if (date("n") == 4) { echo "selected"; } ?>>Avril</option>
            <option value=4 <? if (date("n") == 5) { echo "selected"; } ?>>Mai</option>
            <option value=5 <? if (date("n") == 6) { echo "selected"; } ?>>Juin</option>
            <option value=6 <? if (date("n") == 7) { echo "selected"; } ?>>Juillet</option>
            <option value=7 <? if (date("n") == 8) { echo "selected"; } ?>>Août</option>
            <option value=8 <? if (date("n") == 9) { echo "selected"; } ?>>Septembre</option>
            <option value=9 <? if (date("n") == 10) { echo "selected"; } ?>>Octobre</option>
            <option value=10 <? if (date("n") == 11) { echo "selected"; } ?>>Novembre</option>
            <option value=11 <? if (date("n") == 12) { echo "selected"; } ?>>Décembre</option>
          </select> <select class=inputImage onChange="initDate('y',this.options[selectedIndex].value);" name=year>
            <?
		  $lan = date(Y);
		  while ($lan != date(Y)+2)
		  {
		  ?>
            <option value=<? echo $lan; ?> <? if (date("Y") == $lan) { echo "selected"; } ?>><? echo $lan; ?></option>
            <?
		  $lan++;
		  }
		  ?>
          </select> &agrave; <select class=inputImage name=hour>
            <option value="" selected>HH</option>
            <option value=00>00</option>
            <option value=01>01</option>
            <option 
              value=02>02</option>
            <option value=03>03</option>
            <option 
              value=04>04</option>
            <option value=05>05</option>
            <option 
              value=06>06</option>
            <option value=07>07</option>
            <option 
              value=08>08</option>
            <option value=09>09</option>
            <option 
              value=10>10</option>
            <option value=11>11</option>
            <option 
              value=12>12</option>
            <option value=13>13</option>
            <option 
              value=14>14</option>
            <option value=15>15</option>
            <option 
              value=16>16</option>
            <option value=17>17</option>
            <option 
              value=18>18</option>
            <option value=19>19</option>
            <option 
              value=20>20</option>
            <option value=21>21</option>
            <option 
              value=22>22</option>
            <option value=23>23</option>
          </select>
          : 
          <select 
            class=inputImage name=minute>
            <option value="" 
              selected>MM</option>
            <option value=00>00</option>
            <option 
              value=01>01</option>
            <option value=02>02</option>
            <option 
              value=03>03</option>
            <option value=04>04</option>
            <option 
              value=05>05</option>
            <option value=06>06</option>
            <option 
              value=07>07</option>
            <option value=08>08</option>
            <option 
              value=09>09</option>
            <option value=10>10</option>
            <option 
              value=11>11</option>
            <option value=12>12</option>
            <option 
              value=13>13</option>
            <option value=14>14</option>
            <option 
              value=15>15</option>
            <option value=16>16</option>
            <option 
              value=17>17</option>
            <option value=18>18</option>
            <option 
              value=19>19</option>
            <option value=20>20</option>
            <option 
              value=21>21</option>
            <option value=22>22</option>
            <option 
              value=23>23</option>
            <option value=24>24</option>
            <option 
              value=25>25</option>
            <option value=26>26</option>
            <option 
              value=27>27</option>
            <option value=28>28</option>
            <option 
              value=29>29</option>
            <option value=30>30</option>
            <option 
              value=31>31</option>
            <option value=32>32</option>
            <option 
              value=33>33</option>
            <option value=34>34</option>
            <option 
              value=35>35</option>
            <option value=36>36</option>
            <option 
              value=37>37</option>
            <option value=38>38</option>
            <option 
              value=39>39</option>
            <option value=40>40</option>
            <option 
              value=41>41</option>
            <option value=42>42</option>
            <option 
              value=43>43</option>
            <option value=44>44</option>
            <option 
              value=45>45</option>
            <option value=46>46</option>
            <option 
              value=47>47</option>
            <option value=48>48</option>
            <option 
              value=49>49</option>
            <option value=50>50</option>
            <option 
              value=51>51</option>
            <option value=52>52</option>
            <option 
              value=53>53</option>
            <option value=54>54</option>
            <option 
              value=55>55</option>
            <option value=56>56</option>
            <option 
              value=57>57</option>
            <option value=58>58</option>
            <option 
              value=59>59</option>
          </select>
        </TD>
      </TR>
      <TR> 
        <TD vAlign=top align=middle width=250><DIV id=schedule></DIV>
          <BR> <BR> <DIV id=divMap style="WIDTH: 0px"><IMG border=0 name=imageMap></DIV>
          <BR></TD>
        <TD vAlign=top><TABLE cellSpacing=0 cellPadding=5 width=100% border=0>
            <TBODY>
              <TR> 
                <TD width=100><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pseudo 
                  : </font></TD>
                <TD align=left><INPUT class=inputImage maxLength=64 size=30 
                  name=pseudo id="pseudo"></TD>
              </TR>
              <TR> 
                <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Clan 
                  : </font></TD>
                <TD><INPUT class=inputImage maxLength=64 size=30 
              name=clan id="clan"></TD>
              </TR>
              <TR> 
                <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Chef 
                  du clan : </font></TD>
                <TD><INPUT class=inputImage maxLength=64 size=30 
                name=leader id="leader"></TD>
              </TR>
              <TR> 
                <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Map 
                  : </font></TD>
                <TD><SELECT class=inputImage 
                  onchange=updateImageMap(this.options[selectedIndex].value) 
                  name=map>
                    <OPTION value="" 
                    selected>-----------------------------------------------</OPTION>
                    <?php
					// ouvrir le répertoire
					$dir = opendir("images/cartes");
					// parcourir le répertoire en lisant le nom d'un fichier
					// à chaque itération
					while($fichier = readdir($dir)) {
					if ($fichier != '.' && $fichier != '..' && $fichier != 'Sais pas.jpg' )
					{
					$fichier = str_replace(".jpg", "", $fichier);
					echo "<option value='$fichier'>$fichier</option>";
					}
					}
					// ferme le répertoire
					closedir($dir);
					?>
                  </SELECT> </TD>
              </TR>
              <TR> 
                <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-mail 
                  :</font></TD>
                <TD><INPUT class=inputImage maxLength=128 size=30 
                name=mail id="mail"></TD>
              </TR>
              <TR>
                <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">IRC (optionel) : </font></TD>
                <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#
                  <input name="irc" type="text" id="irc" size="28">
                </font></TD>
              </TR>
              <TR>
                <TD nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">MSN (optionel) : </font></TD>
                <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                  <input name="msn" type="text" id="msn" size="30">
                </font></TD>
              </TR>
              <TR> 
                <TD><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Serveur 
                  : </font></TD>
                <TD><INPUT class=inputImage maxLength=128 size=30 
                name=server></TD>
              </TR>
              <TR> 
                <TD valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Commentaires 
                  :</font></TD>
                <TD><TEXTAREA name=comm cols=21 rows=8 id="comm" style="FONT-SIZE: 9pt;width=100%" VIRTUAL></TEXTAREA></TD>
              </TR>
              <TR> 
                <TD colspan="2"><INPUT id=dayFight type=hidden name=dayFight> 
                  <INPUT class=inputSubmit type=submit value="Valider" name=addChallenge></TD>
              </TR>
            </TBODY>
          </TABLE></TD>
      </TR>
    </TBODY>
  </TABLE>
</FORM></BODY></HTML>
