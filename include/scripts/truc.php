<?php
for($i=0;$i<513;$i++) {
	if(file_exists('../../modules/membres/avatars/'.$i.'.png')) chmod('../../modules/membres/avatars/'.$i.'.png',0777);
}

?>