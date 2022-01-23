<?php

class cssource {
 
  var $ip;
  var $port;
  var $timeout;
  var $sock;
  var $data;
  var $output = array();
 
  function cssource($ip, $port="", $timeout=200) {
     $this->ip = $ip;
     $this->port = (empty($port)) ? 27015 : $port;
     $this->timeout = $timeout;
     
     $this->status();
     
  }
 
  function status() {
     if(!$this->sock = @fsockopen("udp://".$this->ip, $this->port)) {
        die("Could not connect to the socket."); 
     }
     else {
		$querystring = "\xFF\xFF\xFF\xFFW";
		$wait = 0;
		fwrite($this->sock, $querystring);
		while($wait < $this->timeout) {
		$string = fread($this->sock, 4096);
		$wait += 10;
		if(!empty($string)) {
		 $this->Challenge[] = $string;
		 if(strlen($string) < 4096)
		  break;
		}
		}
		$status = $this->Challenge[0];
		$i = 5;
		$Challenge = $this->readString($status, $i);
		
		$querys[] = "\xFF\xFF\xFF\xFF\x54Source Engine Query";
		$querys[] = "\xFF\xFF\xFF\xFF\x55" . $Challenge;
		$querys[] = "\xFF\xFF\xFF\xFF\x56" . $Challenge;
       
        $wait = 0;
        foreach($querys AS $querystring) {
           fwrite($this->sock, $querystring);
           while($wait < $this->timeout) {
              $string = fread($this->sock, 4096);
              $wait += 10;
             
              if(!empty($string)) {
                 $this->data[] = $string;
                 if(strlen($string) < 4096)
                    break;
              }
           }
        }
        @fclose($this->sock);
       
        if(empty($this->data[0]))
           die("No data was returned from the query.");
       
        $this->parseStatus();
        $this->parsePlayers();
        $this->parseRules();
       
     }
  }
 
  function parseStatus() {
     $status = $this->data[0];
     $i = 5;

	if(ord($status{$i})!=7) {
		 $this->output['net_protocol'] = ord($status{$i++});
		 $this->output['ip']           = $this->ip;
		 $this->output['port']         = $this->port;
		 $this->output['hostname']     = $this->readString($status, $i);
		 $this->output['game_dir']     = $this->readString($status, $i);
		 $this->output['map']          = $this->readString($status, $i);
		 $this->output['game_type']    = $this->readString($status, $i);
		 $this->output['game_type']    = $this->readString($status, $i);
		 //$this->output['appid']        = ord($status{$i++}.$status{$i++});
		 $this->output['num_players']  = ord($status{$i++});
		 $this->output['max_players']  = ord($status{$i++});
		 /*$this->output['bot_players']  = ord($status{$i++});
		 $this->output['dedicated']    = $status{$i++};
		 $this->output['server_os']    = $status{$i++};
		 $this->output['needpass']     = ord($status{$i++});
		 $this->output['secure']       = ord($status{$i++});*/
	 } else {
	 	 $this->output['net_protocol'] = ord($status{$i++});
		 $this->output['ip']           = $this->ip;
		 $this->output['port']         = $this->port;
		 $this->output['hostname']     = $this->readString($status, $i);
		 $this->output['map']          = $this->readString($status, $i);
		 $this->output['game_dir']     = $this->readString($status, $i);
		 $this->output['game_type']    = $this->readString($status, $i);
		 $this->output['appid']        = ord($status{$i++}.$status{$i++});
		 $this->output['num_players']  = ord($status{$i++});
		 $this->output['max_players']  = ord($status{$i++});
		 $this->output['bot_players']  = ord($status{$i++});
		 $this->output['dedicated']    = $status{$i++};
		 $this->output['server_os']    = $status{$i++};
		 $this->output['needpass']     = ord($status{$i++});
		 $this->output['secure']       = ord($status{$i++});
	}
	return TRUE;
  }
 
  function parsePlayers() {
     $players = $this->data[1];
     $i = 5;
     $num_players = ord($players{$i++});
     
     $this->output['players'] = array();
     if($num_players > 0) {
        for($p = 0; $p < $num_players; $p++) {
           if(!empty($players{$i+1})) {
              $this->output['players'][$p]['index'] = ord($players{$i++});
              $this->output['players'][$p]['name'] = $this->readString($players, $i);
             
//              $frags = unpack("L", substr($players, $i, $i+4)); $i+=4;
              $this->output['players'][$p]['frags'] = $frags[1];
             
//              $time = unpack("f", substr($players, $i, $i+4)); $i+=4;
              $time = mktime(0, 0, $time[1]);
              //$time = date("H:i:s", $time);
              $this->output['players'][$p]['time'] = $time;
           }
        } 
     }
     
     $this->sortPlayers("frags");
     
     return TRUE;
  }
 
  function parseRules() {
     $rules = $this->data[2];
     $rule = explode("\x00", $rules);
     $num_rules = count($rule);
     
     for($i = 6; $i < $num_rules; $i+=2)
        $this->output['rules'][$rule[$i-1]] = $rule[$i];
     
     return TRUE;
  }
 
  function readString($string, &$i) {
     $begin = $i;
     $strlen = strlen($string);
     for ($i; ($i < $strlen) && ($string{$i} != chr(0)); $i++);
     $result = substr($string, $begin, $i-$begin);
     $i++;
     
     return $result;
  }
 
  function sortPlayers($sortvar="index") {
     $players = $this->output['players'];
     $num_players = count($players);
     
     for($i = 0; $i != $num_players; $i++) {
        $a = $i;
        $b = $num_players-1;
        while ($a != $b){
           if ($players[$a][$sortvar] > $players[$b][$sortvar])
              $b--;
           else
              $a++;
        }
        $h = $players[$i];
        $players[$i] = $players[$a];
        $players[$a] = $h;
     }
     $this->output['players'] = $players;
     
     return TRUE;
  }


  /* get_X()
     These functions serve as proxies to make it a little
     easier to find and use server variables.  At least it
     does for me. Wink
  */
  function get_ip() { return $this->output['ip']; }
  function get_port() { return $this->output['port']; }
  function get_server_name() { return $this->output['hostname']; }
  function get_map() { return $this->output['map']; }
  function get_game_dir() { return $this->output['game_dir']; }
  function get_game_type() { return $this->output['game_type']; }
  function get_players() { return $this->output['players']; }
  function get_num_players() { return $this->output['num_players']; }
  function get_max_players() { return $this->output['max_players']; }
  function get_protocol() { return $this->output['net_protocol']; }
  function get_dedicated() { return $this->output['dedictated']; }
  function get_server_os() { return $this->output['server_os']; }
  function get_needpass() { return $this->output['needpass']; }
 
  /* get_player_list()
     outputs the players connected to the server in a comma-separated list
     
     ie.
     playername1, team_killer X, player3, ...
     
  */
  function get_player_list() {
     $res = "";
     $players = $this->get_players();
     if (is_array($players)) {
        foreach ($players as $player) {
           if (strlen($res)) $res.= ", ";
           $res .= $player['name'];
        }
     }
     else {
        $res = "No players";
     }
     return $res;
  }

} 
?>