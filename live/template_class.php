<?php
class temp {
	var $template;
	var $html;
	
	function temp($template) {
		$this->template = $template;
		$this->html = implode("", file("live/templates/".$this->template.".tpl"));
		return TRUE;
	}
	
	function replace($variable, $replace) {
		$this->html = str_replace("{\$".$variable."}", $replace, $this->html);
		return TRUE;
	}
	
	function show() {
		return $this->html;
	}
}
?>