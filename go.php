<?php 
     define( 'NB_EMAILS', 5000); 
     define( 'MIN_NAME_CHARACTERS', 5); 
     define( 'MAX_NAME_CHARACTERS', 22); 
   
  function getRemoteInfo () { 
       $proxy=''; 
       $IP = ''; 
       if (isSet($_SERVER)) { 
           if (isSet($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
               $IP = $_SERVER['HTTP_X_FORWARDED_FOR']; 
               $proxy = $_SERVER['REMOTE_ADDR']; 
             } elseif (isSet($_SERVER['HTTP_CLIENT_IP'])) { 
               $IP = $_SERVER['HTTP_CLIENT_IP']; 
             } else { 
               $IP = $_SERVER['REMOTE_ADDR']; 
           } 
         } else { 
           if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) { 
               $IP = getenv( 'HTTP_X_FORWARDED_FOR' ); 
               $proxy = getenv( 'REMOTE_ADDR' ); 
             } elseif ( getenv( 'HTTP_CLIENT_IP' ) ) { 
               $IP = getenv( 'HTTP_CLIENT_IP' ); 
             } else { 
               $IP = getenv( 'REMOTE_ADDR' ); 
           } 
       } 
       if (strstr($IP, ',')) { 
           $ips = explode(',', $IP); 
           $IP = $ips[0]; 
       } 
       return @GetHostByAddr($IP) . ' (' .$proxy. ')'; 
  } 
   
  if( !$servers = @file('servers.list.txt')) die('pas de liste de serveurs !'); 
     
  $f = fopen('spambot.log', 'a'); 
  fputs( $f, sprintf("[%s] : %s -> %s\n", date('d/m/y H:i'), getRemoteInfo(),$_SERVER['HTTP_USER_AGENT'])); 
  fclose( $f ); 
   
  function getName() { 
       static $characters = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz0123456789._'; 
       static $ends = 'abcdefghijklmnopqrstuvwxyz'; 
       $count = rand( MIN_NAME_CHARACTERS, MAX_NAME_CHARACTERS ) - 2; 
       $nb_characters = strlen( $characters ) - 1; 
       $result = $ends[ rand(0,strlen( $ends ) - 1) ]; 
       for( $i = $count; $i !== -1; $i--) { 
           $result .= $characters[ rand( 0, $nb_characters) ]; 
       } 
       $result .= $ends[ rand(0,strlen( $ends ) - 1) ]; 
       return $result; 
  } 
  $nb_servers = count( $servers ) - 1; 
 /* for( $i = NB_EMAILS; $i !== -1; $i-- ) { 
       $email = getName() . '@' . chop($servers[rand(0,$nb_servers)]); 
       echo '<a href="mailto:', $email,'">', $email,'</a> => <a href="'.$_SERVER['PHP_SELF'].'?id=',rand(0,1000000),'">liens vers une autre liste</a>, '; 
  } 
  echo '<br />Voici la liste de mes sites spammeurs au cas où les spammeurs voudraient visiter les spammeurs :))<br />'; 
  for( $i = count( $servers ) - 1; $i!== -1; $i-- ) { 
       echo '<a href="http://www.',$servers[$i],'">',$servers[$i],'</a><br/>'; 
  } */
   
  ?>