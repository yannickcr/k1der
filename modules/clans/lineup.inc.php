<?php
/**
 * Vérification des accès
 */
if($clans->isClanLeader()==false) $site->error('Seul le leader peut modifier les lines up du clan.');

/**
 * Construction de la page
 */
$sub_template->setFile('centredroite','clans/lineup.html');
$site->addToTitle(' - Gérer les lines up du clan');
$site->addJs('modules/clans/js/conflineup.js');

/**
 * Action envoi formulaire
 */
if($this->action('addLineUp','clans')) $clans->addLineUp($_POST['nom'],$sub_template);
else if($this->action('majLineUp','clans')) $clans->majLineUp($_POST);


$sub_template->setBlock('centredroite','membre');
$sub_template->setBlock('centredroite','lineup');
$sub_template->setBlock('centredroite','lineups');

/**
 * Récupération des lines up
 */
$info=$sql->fetchAssoc($sql->query('SELECT lineup FROM mod_membres_clans WHERE id="'.$membres->infos('clan_id').'" && nom="'.$membres->infos('clan_nom').'"'));
$lineup=unserialize($info['lineup']);
if(!is_array($lineup)) $lineup=array();
else $sub_template->parse('lineups', true);

/**
 * Récupération des de la liste des membres du clan
 */
$req=$sql->query('SELECT id,pseudo FROM mod_membres WHERE clan_id="'.$membres->infos('clan_id').'" && clan_nom="'.$membres->infos('clan_nom').'"');
while($info=$sql->fetchAssoc($req)) $tab[$info['id']]=$info['pseudo'];

$k=0;
foreach($lineup as $i=>$var) {
	$sub_template->setVar('lineupName',$i);
	foreach($tab as $j=>$var2) {
		$sub_template->setVar(array(
			'membreId'		=>	$j,
			'membrePseudo'	=>	$var2,
			'line'			=>	$string->clean($i),
			'linenum'		=>	$k
		));
		if(in_array($var2,$var)===true) $sub_template->setVar('checked',' checked="checked"');
		else $sub_template->setVar('checked','');
		$sub_template->parse('membre', true);
	}
	/*if($info['ircserver']==$var) $sub_template->setVar('selected2',' selected="selected"');
	else $sub_template->setVar('selected2','');*/
	$sub_template->parse('lineup', true);
	$sub_template->clearVar(array('membre'));
	$k++;
}

if(isset($_POST['nom'])) $sub_template->setVar('nom',$_POST['nom']);
?>
