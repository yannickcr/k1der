<?php
class serveur {

	public $cache=true;
	public $cachetime=150;

	function Serveur($ip,$port) {
		$this->ip=$ip;
		$this->port=$port;
		$this->cachefile		='modules/serveur/cache/'.$this->ip.'-'.$this->port.'.txt';
		$this->cachefilePlayers	='modules/serveur/cache/'.$this->ip.'-'.$this->port.'-players.txt';
	}

	function getChallenge() {
		$fp=fsockopen('udp://'.$this->ip,$this->port,$errno,$errstr,2);
		if (!$fp) {
			trigger_error('Le serveur ne répond pas',E_USER_NOTICE);
			return false;
		} else {
			$out = "\xFF\xFF\xFF\xFFW";
			fwrite($fp, $out);
			stream_set_timeout($fp,2);
			$challenge='';
			do {
				$challenge .= fread ($fp,1);
				$fpstatus = socket_get_status($fp);
			} while ($fpstatus["unread_bytes"]);
			fclose($fp);
			return substr($challenge,5);
		}

	}

	function getInfosHL($usecache=true) {
		// Vérification de l'état du cache
		if($this->cache && $usecache && file_exists($this->cachefile)) {
			$time=time()-filemtime($this->cachefile);
			// if($time<$this->cachetime)
			return $this->getCache($this->cachefile);
		}

		$fp=@fsockopen('udp://'.$this->ip,$this->port,$errno,$errstr,2);
		if (!$fp) {
			if(!file_exists($this->cachefile)) {
				trigger_error('Le serveur ne répond pas',E_USER_NOTICE);
				return false;
			}
			return $this->getCache($this->cachefile);
		} else {
			$out = "\xFF\xFF\xFF\xFFTSource Engine Query\x00";
			fwrite($fp, $out);
			stream_set_timeout($fp,2);
			$infos='';
			do {
				$infos .= fread ($fp,1);
				$fpstatus = socket_get_status($fp);
			} while ($fpstatus["unread_bytes"]);
			fclose($fp);

			//  Détermine le protocole utilisé
			$protocol=hexdec(substr(bin2hex($infos),8,2));

			if($protocol==109) return $this->getInfosHL1($infos);
			else if($protocol==73) return $this->getInfosHL2($infos);

			if(!file_exists($this->cachefile)) {
				trigger_error('Type de serveur inconnu',E_USER_NOTICE);
				return false;
			}
			return $this->getCache($this->cachefile);
		}
	}

	function getInfosHL1($infos) {
		//  Découpage des informations
		$infos=chunk_split(substr(bin2hex($infos),10),2,'\\');
		@list($serveur['ip'],$serveur['name'],$serveur['map'],$serveur['mod'],$serveur['modname'],$serveur['params'])=explode('\\00',$infos,6);

		// Découpages des paramètres
		$serveur['params']=substr($serveur['params'],0,18);

		$serveur['params']=chunk_split(str_replace('\\','',$serveur['params']),2,' ');
		list($params['players'],$params['places'],$params['protocol'],$params['dedie'],$params['os'],$params['pass'])=explode(' ',$serveur['params']);
		$params=array(
			'players'	=>	hexdec($params['players']),
			'places'	=>	hexdec($params['places']),
			'protocol'	=>	hexdec($params['protocol']),
			'dedie'		=>	hexdec($params['dedie']),
			'os'		=>	hexdec($params['os']),
			'pass'		=>	hexdec($params['pass'])
		);
		unset($serveur['params']);

		foreach($serveur as $i=>$val) $serveur[$i]=pack("H*", str_replace('\\','',$val));

		$infos=($serveur+$params);
		$this->writeCache($infos,$this->cachefile);
		return $infos;
	}

	function getInfosHL2($infos) {
		//  Découpage des informations
		$infos=chunk_split(substr(bin2hex($infos),12),2,'\\');
		@list($serveur['name'],$serveur['map'],$serveur['mod'],$serveur['modname'],$serveur['params'])=explode('\\00',$infos,5);

		// Découpages des paramètres
		$serveur['params']=substr($serveur['params'],0);

		$serveur['params']=chunk_split(str_replace('\\','',$serveur['params']),2,' ');
		list($params['id1'],$params['id2'],$params['players'],$params['places'],$params['bots'],$params['dedie'],$params['os'],$params['pass'])=explode(' ',$serveur['params']);
		$params=array(
			'id'		=>  hexdec($params['id2'].$params['id1']),
			'ip'		=>	$this->ip.':'.$this->port,
			'players'	=>	hexdec($params['players']),
			'places'	=>	hexdec($params['places']),
			'bots'		=>	hexdec($params['bots']),
			'protocol'	=>	73,
			'dedie'		=>	hexdec($params['dedie']),
			'os'		=>	hexdec($params['os']),
			'pass'		=>	hexdec($params['pass'])
		);
		unset($serveur['params']);

		foreach($serveur as $i=>$val) $serveur[$i]=pack("H*", str_replace('\\','',$val));

		$infos=($serveur+$params);
		$this->writeCache($infos,$this->cachefile);
		return $infos;
	}

	function writeCache($infos,$file) {
		if(count($infos)<1) return false;
		$infos=var_export($infos,true);			// Tranformation du tableau pour exportation
		$fp=@fopen($file,"w");					// Ecriture du fichier
		@flock($fp, LOCK_EX);					// Verrou criture
		if (!@file_exists($file)) {				// a-t-il ete supprime par le locker ?
			@fclose($fp);
			return false;
		}
		@fputs($fp,$infos);
		@flock($fp, LOCK_UN);					// Liberer le verrou
		@fclose($fp);
		return true;
	}

	function getCache($file) {
		$infos = implode('',file($file));
		if(!empty($infos)) eval('$infos='.$infos.';');
		else $infos=array();
		if(!is_array($infos)) $infos=array();
		return $infos;
	}

	function getPlayers($usecache=true) {
		// Vérification de l'état du cache
		if($this->cache && $usecache && file_exists($this->cachefilePlayers)) {
			$time=time()-filemtime($this->cachefilePlayers);
			// if($time<$this->cachetime)
			return $this->getCache($this->cachefilePlayers);
		}

		$challenge=$this->getChallenge();
		$fp=@fsockopen('udp://'.$this->ip,$this->port,$errno,$errstr,2);
		if (!$fp) {
			if(!file_exists($this->cachefilePlayers)) {
				trigger_error('Le serveur ne répond pas',E_USER_NOTICE);
				return false;
			}
			return $this->getCache($this->cachefilePlayers);
		}
		$out = "\xFF\xFF\xFF\xFFU".$challenge;
		fwrite($fp, $out);
		stream_set_timeout($fp,2);
		$infos='';
		do {
			$infos .= fread ($fp,1);
			$fpstatus = socket_get_status($fp);
		} while ($fpstatus["unread_bytes"]);
		fclose($fp);
		$infos=chunk_split(substr(bin2hex($infos),12),2,'\\');

		$infos=explode('\\',$infos);

		$players=array();
		for($i=0;isset($infos[$i+1]);$i=$j+9) {

			// Pseudo
			$name='';
			for($j=$i+1;isset($infos[$j]) && $infos[$j]!='00';$j++) $name.=chr(hexdec($infos[$j]));

			if(!isset($infos[$j+8])) break;

			// Temps de jeu
			eval('$time="\x'.trim(chunk_split($infos[$j+5].$infos[$j+6].$infos[$j+7].$infos[$j+8],2,"\x"),"\x").'";');
			list(,$time)=unpack('f',$time);

			// Score
			$score=ltrim($infos[$j+4].$infos[$j+3].$infos[$j+2].$infos[$j+1],'0');

			$players[]=array(
				'id'	=>	hexdec($infos[$i]),
				'name'	=>	$name,
				'score'	=>	empty($score)?0:hexdec($score),
				'time'	=>	$time
			);
		}
		$this->writeCache($players,$this->cachefilePlayers);
		return $players;
	}
}
?>
