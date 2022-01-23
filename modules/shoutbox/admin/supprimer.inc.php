<?php
$shoutbox = new shoutbox();

if($this->action('delMessage')) $shoutbox->delMessage($_GET['id'],1);
?>