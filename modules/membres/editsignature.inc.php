<?php
$site->addToTitle(' - &Eacute;diter ma signature');
$sub_template->setFile('centredroite','membres/editsignature.html');
$site->barreMiseEnForme($sub_template,'lite');

/**
 * Action envoi formulaire
 */
if($this->action('editSignature')) $membres->editSignature($_POST);

if($membres->infos('signature')!='') {
	$bbcode = new bbcode;
	$signatureHtml=$bbcode->stripBBCode($bbcode->BBCodeToHtml($membres->infos('signature')));
	$sub_template->setVar(array('signatureHtml'=>$signatureHtml,'signature'=>$membres->infos('signature')));
	$sub_template->setBlock('centredroite','signature-set');
	$sub_template->parse('signature-set',true);
} else {
	$sub_template->setBlock('centredroite','signature-notset');
	$sub_template->parse('signature-notset',true);
}
?>