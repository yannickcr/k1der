<?php
$download = new download();
$utils = new utils();

$template->setFile('centre','download/index.html');
$site->addToTitle(' - Téléchargements');
$site->addJs('modules/download/js/count.js');
$site->addCss('templates/'.THEME.'/download/style.css');

$template->setBlock('centre','new');
$template->setBlock('centre','sub');
$template->setBlock('centre','cats');

$template->setBlock('centre','star');
$template->setBlock('centre','halfstar');
$template->setBlock('centre','emptystar');

$id=$site->config('download_discover');

$info=$sql->fetchAssoc($sql->query('
	SELECT 
		d.id,
		d.nom,
		d.descr,
		d.illus,
		c.nom cat,
		d.cat catid,
		d.size,
		d.note,
		d.votes,
		d.dl,
		d.mirrors,
		d.active,
		COUNT(co.message) comms 
	FROM 
		mod_download d 
			LEFT JOIN mod_download_cats c ON d.cat=c.id
			LEFT JOIN mod_comments co ON d.id=co.resource_id 
	WHERE 
		d.id='.$id.'
	GROUP BY
		d.id
'));

$miroir=unserialize($info['mirrors']);

$template->setVar(array(
	'discId'		=>	$info['id'],
	'discNom'		=>	$info['nom'],
	'discCleanNom'	=>	$string->clean($info['nom']),
	'discCat'		=>	$string->clean($info['cat']),
	'discCatId'		=>	$info['catid'],
	'discDescr'		=>	nl2br($info['descr']),
	'discIllus'		=>	$info['illus'],
	'discSize'		=>	$utils->size($info['size'],true),
	'discVote'		=>	$info['votes'],
	'discComms'		=>	$info['comms'],
	'discDl'		=>	$info['dl'],
	'discLink'		=>	$miroir[$info['active']]
));

$star=5;

$reste=$info['note']-floor($info['note']);
if($reste!=0 && $reste>=0.3 && $reste<0.7) {
	$template->parse('halfstar');
	$star--;
}

for($i=0;$i<round($info['note']) && $star>0;$i++) {
	$template->parse('star',true);
	$star--;
}

for($star;$star>0;$star--) $template->parse('emptystar',true);

$res=$sql->query('
	SELECT 
		d.id,
		d.nom,
		d.descr,
		d.illus ,
		c.nom cat,
		d.cat catid
	FROM 
		mod_download d 
			LEFT JOIN mod_download_cats c ON d.cat=c.id
	WHERE 
		d.id!='.$id.' 
	ORDER BY 
		d.id 
	DESC
');

while($info=$sql->fetchAssoc($res)) {
	$descr=$string->unhtmlentities($info['descr']);
	$descr=explode('<br />',nl2br($descr));
	if(strlen($descr[0])>80) $descr[0]=htmlentities(substr($descr[0],0,80)).'...';
	$template->setVar(array(
		'newId'		=>	$info['id'],
		'newNom'		=>	$info['nom'],
		'newCleanNom'	=>	$string->clean($info['nom']),
		'newCat'		=>	$string->clean($info['cat']),
		'newCatId'		=>	$info['catid'],
		'newDescr'		=>	$descr[0],
		'newIllus'		=>	$utils->miniature($info['illus'],80,60),
	));
	$template->parse('new',true);
}

$cats=$download->createCatsTree();
foreach($cats as $i=>$var) {
	if($var['cat']==0) {
		$first=true;
		foreach($cats as $j=>$var2) {
			if($var2['cat']==$var['id']) {
				if($first==true) $txt='';
				else $txt=',&nbsp;';
				$first=false;
				$template->setVar(array(
					'catSubSep'			=>	$txt,
					'catSubId'			=>	$var2['id'],
					'catSubNom'			=>	$var2['nom'],
					'catSubCleanNom'	=>	$string->clean($var2['nom'])
				));
			$template->parse('sub',true);
			}
		$template->setVar(array(
			'catId'			=>	$var['id'],
			'catNom'		=>	$var['nom'],
			'catCleanNom'	=>	$string->clean($var['nom']),
			'catDescr'		=>	$var['descr']
		));
		}
		$template->parse('cats',true);
		$template->unsetVar(array('sub'));
	}
}
?>