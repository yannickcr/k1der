<script language="JavaScript">
function AddComment(data){
window.open(data,'Sondage','toolbar=0,location=0,directories=0,menuBar=0,scrollbars=0,resizable=1,width=430,height=335,left=0,right=0');
}
</script>
<?

// READ README.TXT

/* Chemin d'accès à data.dat / Path to data.dat */ 
$data="data.dat"; 

/* Chemin d'accès à votes.dat / Path to votes.dat */ 
$votes="votes.dat"; 

/* Chemin d'accès au fichier image / Path to the image file */ 
$path_img="images/red.gif"; 

/* Nom de votre sondage / Name of your poll */ 

require("config.inc.php3");

$db = mysql_connect("$dbhost", "$dblogi", "$dbpass") or Die("Base Down !");
mysql_select_db("$dbbase",$db) or Die("Base Down !");


$req = MYSQL_QUERY("SELECT * FROM poll WHERE nom='titre'");

$poll_name       = stripslashes(mysql_result($req,0,"valeur"));

/////////////////////////////// 
// NOTHING TO CHANGE BELOW // 
/////////////////////////////// 

$dataf=file("data.dat"); 

if ($go !=1) { 
     
     /* Impression des choix */ 
     echo "<center><b><font face='Verdana' size='1'>$poll_name</font></b></center>"; 
     echo "<form method=post>"; 
     for ($i=0; $i<=count($dataf)-1; $i++) { 
	   $dataf[$i] = stripslashes($dataf[$i]) ;
         echo "<table width=134 cellpadding=0 align=center><tr><td width=20 valign=top><input type=radio name=\"vote\" value=\"$i\"></td><td><div align=left><font face='Verdana' size='1'> $dataf[$i]</font></div></td><tr></table>"; 
     } 
     echo "<input type=hidden name=go value=1>"; 
     echo "<p><center><input type=submit value=Vote>"; 
     echo "</form>"; 
     echo "<font face='Verdana' size='1'><a href='javascript:resultats()'>Resultats</a></font></center>"; 
} 

else { 
     
     $file_votes=fopen($votes, "r"); 
     $line_votes=fgets($file_votes, 255); 
     $single_vote=explode("|", $line_votes); 
     fclose($file_votes); 
     
     if ($result!=1) { 
         
         /* Log du vote */ 
         $file_votes=file($votes, "r"); 
         if ($REMOTE_ADDR == $file_votes[1]) { 
         } 
         
         $ficdest=fopen($votes, "w"); 
         for ($i=0; $i<=count($dataf)-1; $i++) { 
             if ($i == $vote) { 
                 $single_vote[$i]+=1; 
             } 
             fputs($ficdest, "$single_vote[$i]|"); 
         } 
         fclose($ficdest); 
         $ficdest=fopen($votes, "a"); 
         fputs($ficdest, "\n$REMOTE_ADDR"); 
         fclose($ficdest); 
         $result=1; 
     } 
     
     if ($result==1) { 
         
         /* Affichage des résultats */ 
		 echo "<center><b><font <font face='Verdana' size='1'>$poll_name</b></center><br>\n"; 
         echo "<table width=134 cellpadding=0 align=center cellpadding=0>"; 
         echo "<tr><td align=center><font face=Verdana size=1>"; 
         echo "<i>Choix</i></font>"; 
         echo "</td><td align=center><font face=Verdana size=1>"; 
         echo "<i> </i></font></td>"; 
         echo "<td align=center><font face=Verdana size=1>"; 
         echo "<i>Votes</i></font></td></tr>"; 
         for ($i=0; $i<=count($dataf)-1; $i++) { 
             $tot_votes+=$single_vote[$i]; 
         } 
         for ($i=0; $i<=count($dataf)-1; $i++) { 
//             $stat[$i]=$single_vote[$i]/$tot_votes*100; 
             echo "<tr valign=top><td valign=top><font face=Verdana size=1>";
			 $dataf[$i] = stripslashes($dataf[$i]) ; 
             echo "$dataf[$i]<br><br></font></td><td align=left><font face=Verdana size=1>"; 
//             echo "<img src=\"$path_img\" height=10 width=$stat[$i] align=middle>&nbsp;"; 
//             printf("%.1f", "$stat[$i]"); 
             echo " </font></td><td align=center valign=top><font face=Verdana size=1>"; 
             echo "$single_vote[$i]</font>"; 
             echo "</td></tr>"; 
         } 
         echo "</table><p>"; 
         echo "<center><font face=Verdana size=1>Total: $tot_votes votes</center><XML style='DISPLAY: none'></XML>";      } 
} 

?>