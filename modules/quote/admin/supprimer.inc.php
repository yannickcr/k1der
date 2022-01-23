<?php
$quote = new quote();

if($this->action('delPhrase')) $quote->delPhrase($_GET['id']);
?>