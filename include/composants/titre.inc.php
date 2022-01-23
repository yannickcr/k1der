<?php
/**
 * Titre de la page.
 *
 * @author    Yannick Croissant
 * @package   K1der
 */
$template->setFile('titre','titre.html');  

if($module->toUse("droite")==TRUE) $template->setVar('titre.page','page');
else $template->setVar('titre.page','pageclean');

/*if($this->is_naked_day()) $template->setVar('naked','<h2>Bordel ! Qu\'est-il arrivé au design ?</h2>
<p>Pour connaître la raison de la désactivation des styles sur ce site, visite le site de l\'<a href="http://naked.dustindiaz.com" title="Web Standards Naked Day Host Website">Annual CSS Naked Day</a>.</p>
');*/

$this->toparse[]='titre';
?>