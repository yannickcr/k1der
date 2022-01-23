<?php
/**
 * Steam community profile parser class
 *
 * Get all infos from a steam community profile webpage
 *
 * @copyright  Copyright (c) 2007 Yannick Croissant
 * @license    MIT-style license
 * @version    0.7
 * @link       http://www.k1der.net/blog/country/tag/steam
 */
class steamCommunityProfile {
	
	const URL = 'http://steamcommunity.com';
	
	public function __construct($profil) {
		if(preg_match('/^([0-9]+)$/',$profil)) $this->profil = '/profiles/'.$profil;
		else $this->profil = '/id/'.$profil;
		$html = file_get_contents(self::URL.$this->profil);
		$data = new DOMDocument();
		@$data->loadHTML($html); // Avoid parse errors
		$this->xpath = new Domxpath($data);
	}
	
	/******************************
	 *         User methods
	 ******************************/
	public function getUserSteamID() {
		return $this->xpath->query("//h1")->item(0)->nodeValue;
	}
	
	public function getUserAvatar() {
		return $this->xpath->query("//div[@class='avatarFull']/img")->item(0)->getAttribute('src');
	}
	
	public function getUserHeadline() {
		return $this->xpath->query("//h1")->item(1)->nodeValue;
	}
	
	public function getUserRealName() {
		return $this->xpath->query("//h2")->item(1)->nodeValue;
	}
	
	public function getUserCity() {
		$string = $this->xpath->query("//h2")->item(2)->nodeValue;
		return trim(join(', ',explode(', ',$string,-1)));
	}
	
	public function getUserCountry() {
		$array = explode(', ',$this->xpath->query("//h2")->item(2)->nodeValue);
		return trim($array[count($array)-1]);
	}
	
	public function getUserSummary() {
		return $this->xpath->query("//p[@class='sectionText']")->item(0)->nodeValue;
	}
	
	public function getUserLinks() {
		$links = $this->xpath->query("//div[@id='profileAvatar']/following-sibling::a[@class='linkBlue externalLink']");
		$array = array();
		for($i=0;$i<$links->length;$i++) {
			$array[] = array(
				'label' => $links->item($i)->nodeValue,
				'link'  => $links->item($i)->getAttribute('href')
			);
		}
		return $array;
	}
	
	public function getUserStatus() {
		$img  = $this->xpath->query("//div[@id='OnlineStatus']/img");
		$icon = $this->xpath->query("//div[@id='currentlyPlayingIcon']");
		if($icon->length>0) return 'in-game';
		if($img->length>0) return 'online';
		return 'offline';
	}
	
	public function getUserLastOnline() {
		if ($this->getUserStatus()!='offline') return '0:0:0';
		
		$text = $this->xpath->query("//div[@id='OnlineStatus']/p[not(@class)]");
		$text = explode(', ',$text->item(0)->nodeValue);
		if(ereg('day',$text[0])) {
			$hour = $min = 0;
			$day = (int)preg_replace('/([^0-9\.]*)/','',$text[0]);
		} else {
			$day = 0;
			$hour = (int)preg_replace('/([^0-9\.]*)/','',$text[0]);
			$min = (int)preg_replace('/([^0-9\.]*)/','',$text[1]);
		}
		return $day.':'.$hour.':'.$min;
	}
	
	public function getUserCurrentGame() {
		if ($this->getUserStatus()!='in-game') return array();
		
		$link = $this->xpath->query("//div[@id='currentlyPlayingIcon']//div[@class='avatarIcon']/a")->item(0)->getAttribute('href');
		$icon = $this->xpath->query("//div[@id='currentlyPlayingIcon']//div[@class='avatarIcon']/a/img")->item(0)->getAttribute('src');
		$name = $this->xpath->query("//p[@id='statusInGameText']")->item(0)->nodeValue;
		$join = $this->xpath->query("//div[@class='actionItem']/a[@class='linkGreen']");
		if($join->length==0) $join = '';
		else $join = $join->item(0)->getAttribute('href');
		
		return array(
			'link' => $link,
			'icon' => $icon,
			'name' => $name,
			'join' => $join
		);
	}
	
	public function getUserAddLink() {
		return $this->xpath->query("//div[@class='actionItem']/a[not(@class='linkGreen')]")->item(0)->getAttribute('href');
	}
	
	public function getUserStats() {
		$items   = $this->xpath->query("//div[@class='statsItem']");
		$array   = array();
		// Item 0 : Member since
		$text    = explode(' ',trim($items->item(0)->lastChild->nodeValue));
		$months  = array('','January','February','March','April','May','June','July','August','September','October','November','December');
		$month   = array_search($text[0],$months);
		$day     = (int)$text[1];
		$yeah    = (int)$text[2];
		$array['since']= mktime(0,0,0,$month,$day,$yeah);
		
		// Item 1 : Steam Rating
		$text    = explode('-',trim($items->item(1)->lastChild->nodeValue));
		$array['rating']= array((float)$text[0],trim($text[1]));
		
		// Item 2 : Playing time
		$array['playtime']    = (float)trim($items->item(2)->lastChild->nodeValue);
		
		return $array;
	}
	
	public function getUserMostPlayed() {
		$links = $this->xpath->query("//div[@class='mostPlayedBlockIcon']//a");
		$names = $this->xpath->query("//div[@class='mostPlayedBlock']");
		$times = $this->xpath->query("//div[@class='mostPlayedBlock']/span");
		
		$rank = array();
		
		for($i=0;$i<$times->length;$i++) {
			$name = explode('.',$names->item($i)->childNodes->item(2)->nodeValue,2);
			
			$rank[]=array(
				'link'	=>	$links->item($i)->getAttribute('href'),
				'img'	=>	$links->item($i)->getElementsByTagName('img')->item(0)->getAttribute('src'),
				'name'	=>	trim($name[1]),
				'time'	=>	(float)$times->item($i)->nodeValue
			);
		}
		return $rank;
	}
	
	public function getUserFriends() {
		// Parsing the full friends page
		$html = file_get_contents(self::URL.$this->profil.'/friends');
		$data = new DOMDocument();
		@$data->loadHTML($html); // Avoid parse errors
		$this->friends = new Domxpath($data);
		
		
		$links   = $this->friends->query("//div[@class='friendBlockIcon']//a");
		$names   = $this->friends->query("//div[@class='friendBlockIcon']/following-sibling::p/a");
		$state   = $this->friends->query("//div[@class='friendBlockIcon']/following-sibling::p/span");
		
		$rank = array();
		
		for($i=0;$i<$names->length;$i++) {
			$status = explode('_',$names->item($i)->getAttribute('class'));
			$status = $status[1];
			$statu = trim($state->item($i)->nodeValue);
			if($status=='online') {
				$last = 0;
				$game = $join = '';
			} else if($status=='offline') {
				$text = explode(', ',$statu);
				if(count($text)>1) {
					$day = 0;
					$hour = (int)preg_replace('/([^0-9\.]*)/','',$text[0]);
					$min = (int)preg_replace('/([^0-9\.]*)/','',$text[1]);
				} else if(ereg('day',$text[0])) {
					$hour = $min = 0;
					$day = (int)preg_replace('/([^0-9\.]*)/','',$text[0]);
				} else {
					$day = $hour = 0;
					$min = (int)preg_replace('/([^0-9\.]*)/','',$text[0]);
				}
				$last = $day.':'.$hour.':'.$min;
				$game = $join = '';
			} else if($status=='in-game') {
				$last = 0;
				$game = trim($state->item($i)->getElementsByTagName('span')->item(0)->childNodes->item(2)->nodeValue,' -');
				$join = $state->item($i)->getElementsByTagName('a');
				if($join->length==0) $join = '';
				else $join = $join->item(0)->getAttribute('href');
			}
			
			$rank[]=array(
				'link'	=>	$links->item($i)->getAttribute('href'),
				'img'	=>	$links->item($i)->getElementsByTagName('img')->item(0)->getAttribute('src'),
				'name'	=>	$names->item($i)->nodeValue,
				'status'=>	$status,
				'last'	=>	$last,
				'game'	=>	$game,
				'join'	=>	$join
			);
		}
		return $rank;
	}
	
	public function getUserFriendsLink() {
		return self::URL.$this->profil.'/friends';	
	}
	
	/******************************
	 * User Favorite Game methods
	 ******************************/
	public function getUserFavoriteGameLogo() {
		$img = $this->xpath->query("//div[@class='gameLogo']/a/img");
		if($img->length==0) return '';
		return $img->item(0)->getAttribute('src');
	}
	
	public function getUserFavoriteGameLink() {
		$a = $this->xpath->query("//div[@class='gameLogo']/a");
		if($a->length==0) return '';
		return $a->item(0)->getAttribute('href');
	}
	
	public function getUserFavoriteGameName() {
		$name = $this->xpath->query("//div[@id='favoriteGameBlock']/p/a");
		if($name->length==0) return '';
		return $name->item(0)->nodeValue;
	}
	
	public function getUserFavoriteGameTime() {
		$text = $this->xpath->query("//div[@id='favoriteGameBlock']/p/span");
		if($text->length==0) return 0;
		return (float)preg_replace('/([^0-9\.]*)/','',$text->item(0)->nodeValue);
	}
	
	/******************************
	 *     Primary Group methods
	 ******************************/
	public function getPrimaryGroupAvatar() {
		return $this->xpath->query("//div[@class='avatarFull']/a/img")->item(0)->getAttribute('src');
	}
	
	public function getPrimaryGroupLink() {
		return $this->xpath->query("//div[@class='avatarFull']/a")->item(0)->getAttribute('href');
	}
	
	public function getPrimaryGroupHeadline() {
		return $this->xpath->query("//div[@id='primaryGroupBlock']/h1")->item(0)->nodeValue;
	}
	
	public function getPrimaryGroupSummary() {
		return $this->xpath->query("//div[@id='primaryGroupBlock']/p[@class='sectionText']")->item(0)->nodeValue;
	}
	
	public function getPrimaryGroupName() {
		return $this->xpath->query("//div[@id='primaryGroupBlock']/a[@class='linkGrey']")->item(0)->nodeValue;
	}
	
	public function getPrimaryGroupMemberCount() {
		return (int)$this->xpath->query("//div[@id='primaryGroupBlock']/h2")->item(0)->nodeValue;
	}
	
	public function getPrimaryGroupMemberChatCount() {
		$text = explode(', ',$this->xpath->query("//p[@id='primaryMemberText']")->item(0)->nodeValue);
		return (int)$text[0];
	}
	
	public function getPrimaryGroupMemberInGameCount() {
		$text = explode(', ',$this->xpath->query("//p[@id='primaryMemberText']")->item(0)->nodeValue);
		return (int)$text[1];
	}
	
	public function getPrimaryGroupMemberOnlineCount() {
		$text = explode(', ',$this->xpath->query("//p[@id='primaryMemberText']")->item(0)->nodeValue);
		return (int)$text[2];
	}
	
	public function getPrimaryGroupChatLink() {
		return $this->xpath->query("//p[@id='primaryMemberText']/a")->item(0)->getAttribute('href');
	}
		
	/******************************
	 *   Secondary Group methods
	 ******************************/
	public function getSecondaryGroups() {
		$avatars       = $this->xpath->query("//div[@class='groupBlockIcon']//a/img");
		$links         = $this->xpath->query("//div[@class='groupBlockIcon']//a");
		$names         = $this->xpath->query("//div[@class='groupBlock']//a[@class='linkGrey']");
		$members       = $this->xpath->query("//div[@class='groupBlock']//span[@class='groupSmallText']");
		$chatLinks     = $this->xpath->query("//div[@class='groupBlock']//span[@class='groupSmallText']/a");
	
		$groups = array();
		for($i=0;$i<$names->length;$i++) {
			$text = trim(str_replace('-',',',$members->item($i)->nodeValue));
			
			list($membersTotal,$membersChat,$membersInGame,$membersOnline) = explode(', ',$text);
			
			$groups[] = array(
				'avatar'   => $avatars->item($i)->getAttribute('src'),
				'link'     => $links->item($i)->getAttribute('href'),
				'name'     => $names->item($i)->nodeValue,
				'members'  => array(
					'chat'   => (int)$membersChat,
					'ingame' => (int)$membersInGame,
					'online' => (int)$membersOnline,
					'total'  => (int)$membersTotal
				),
				'chatLink' => $chatLinks->item($i)->getAttribute('href')
				
			);
		}
		return $groups;
	}
}