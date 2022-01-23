<?php
class search {

	function search($search,$modules) {
		global $sql;
		$this->search=$search;
		$this->modules=$modules;
		
		foreach($modules as $module) {
			include('modules/'.$module.'/friends/search.inc.php');
			$this->searchInfos[$module]=$searchInfos[$module];
			$res=$sql->query($searchInfos[$module]['query']);
			$this->results[$module]=$sql->numRows($res);
		}
	}
}
?>