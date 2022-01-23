<?php
$clans = new clans();

$site->addToTitle(' - Profil');
$template->setFile('centre','membres/mon-profil.html');
$site->addCss('templates/'.THEME.'/membres/style.css');
$site->addJs('modules/clans/js/messages.js');

if(!$membres->infos('id')) {
		$message='<p>Vous devez tre identifi pour accder  cette page.</p>';
		if(!$membres->infos('id')) {
			$message.='<p>Si vous possdez un compte vous pouvez vous connecter avec le formulaire ci-contre.</p><p>Dans le cas contraire, vous pouvez créer un nouveau compte <a href="membres/inscription.html">en cliquant ici</a>.</p>';
		}
	$site->error($message);
}

$template->setBlock('centre','haveblog');
$template->setBlock('centre','havenotblog');

$template->setBlock('centre','clan');
$template->setBlock('centre','noclan');
$template->setBlock('centre','clanleader');

if($clans->isClanLeader()) $template->parse('clanleader', true);
else if ($membres->infos('clan_nom')=='' || ereg('postul_(.*)',$membres->infos('clan_nom'))) $template->parse('noclan', true);
else $template->parse('clan', true);


if($membres->checkBlog($membres->infos('pseudo'))) $template->parse('haveblog', true);
else $template->parse('havenotblog', true);

$template->setVar('pseudo',strtolower($membres->infos('pseudo')));

$sub_template = new template("templates/".THEME."/");
if(!isset($_GET['action'])) require_once('modules/membres/infos.inc.php');
else if($_GET['action']=='rediger-message') require_once('modules/membres/rediger-message.inc.php');
else if($_GET['action']=='boite-de-reception') require_once('modules/membres/boite-de-reception.inc.php');
else if(eregi('^read-([a-z0-9\-]+)-id([0-9]+)$',$_GET['action'])) require_once('modules/membres/read.inc.php');
else if(eregi('^rediger-message-quote([0-9]+)$',$_GET['action'])) require_once('modules/membres/rediger-message.inc.php');
else if(eregi('^rediger-message-reply([0-9]+)$',$_GET['action'])) require_once('modules/membres/rediger-message.inc.php');
else if(eregi('^rediger-message-([a-z0-9]+)$',$_GET['action'])) require_once('modules/membres/rediger-message.inc.php');
else if(eregi('^message-del([0-9]+)$',$_GET['action'])) $membres->delMessage(eregi_replace('^message-del','',$_GET['action']));

else if($_GET['action']=='editinfos') require_once('modules/membres/editinfos.inc.php');
else if($_GET['action']=='changepass') require_once('modules/membres/changepass.inc.php');
else if($_GET['action']=='changeavatar') require_once('modules/membres/changeavatar.inc.php');
else if($_GET['action']=='editsignature') require_once('modules/membres/editsignature.inc.php');

else if($_GET['action']=='clancreate') require_once('modules/clans/create.inc.php');
else if($_GET['action']=='clanedit') require_once('modules/clans/edit.inc.php');
else if($_GET['action']=='clanlineup') require_once('modules/clans/lineup.inc.php');
else if(eregi('^clanlineup-del([0-9]+)$',$_GET['action'])) $clans->delLineUp(eregi_replace('^clanlineup-del','',$_GET['action']));
else if($_GET['action']=='clanpostul') require_once('modules/clans/postul.inc.php');
else if(eregi('^clanpostul-accepter([0-9]+)$',$_GET['action'])) $clans->postulOk(eregi_replace('^clanpostul-accepter','',$_GET['action']));
else if(eregi('^clanpostul-refuser([0-9]+)$',$_GET['action'])) $clans->postulNok(eregi_replace('^clanpostul-refuser','',$_GET['action']));
else if($_GET['action']=='clanjoin') require_once('modules/clans/join.inc.php');
else if($_GET['action']=='clanquit') require_once('modules/clans/quit.inc.php');
else if($_GET['action']=='clanban') require_once('modules/clans/ban.inc.php');
else if($_GET['action']=='clanclose') require_once('modules/clans/close.inc.php');
else if($_GET['action']=='clanmembres') require_once('modules/clans/membres.inc.php');
else if(eregi('^clanmembres-renvoyer([0-9]+)$',$_GET['action'])) $clans->renvoyer(eregi_replace('^clanmembres-renvoyer','',$_GET['action']));
else if($_GET['action']=='clanhisto') require_once('modules/clans/histo.inc.php');

else if($_GET['action']=='creer-blog') require_once('modules/membres/creer-blog.inc.php');
else if($_GET['action']=='creer-blog-fin') require_once('modules/membres/creer-blog-fin.inc.php');


$template->setVar('res_centredroite',$sub_template->globalParse('parse','centredroite',true));
?>