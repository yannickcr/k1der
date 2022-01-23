<?php
// On nettoye les valeurs des trucs qui n'ont rien  faire l
$_POST['pseudo']=eregi_replace('[^a-z0-9]','',$_POST['pseudo']);
$_POST['nbpartmin']=eregi_replace('[^0-9]','',$_POST['nbpartmin']);
$_POST['nbpartmax']=eregi_replace('[^0-9]','',$_POST['nbpartmax']);

/**
 * On parcours les valeurs envoyes afin de construire la requte adquat :
 * - Si les champs ont ts remplis
 * - Si leurs checkbox respectives sont coches 
 */
$param='';

// Pseudo
if($_POST['checkedpseudo']=='true' && !empty($_POST['pseudo'])) $param.='&& pseudo LIKE "%'.$_POST['pseudo'].'%" ';
// Participation
if($_POST['checkednbpart']=='true' && !empty($_POST['nbpartmin']) && !empty($_POST['nbpartmax'])) $param.='&& part BETWEEN '.$_POST['nbpartmin'].' AND '.$_POST['nbpartmax'].' ';
if($_POST['checkednbpart']=='true' && !empty($_POST['nbpartmin'])) $param.='&& part>='.$_POST['nbpartmin'].' ';
if($_POST['checkednbpart']=='true' && !empty($_POST['nbpartmax'])) $param.='&& part<='.$_POST['nbpartmax'].' ';
// Date d'inscription
if($_POST['checkedins']=='true') {
	switch($_POST['ins']) {
		case "aujourdhui":
			$supat=time()-(strtotime (date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& date_ins>='.$supat.' ';
			break;
		case "hier":
			$infat=time()-(strtotime (date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$supat=time()-(strtotime ('1 day '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& date_ins BETWEEN '.$supat.' AND '.$infat.' ';
			break;
		case "semaine":
			$supat=time()-(strtotime ('7 week '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& date_ins>='.$supat.' ';
			break;
		case "mois":
			$supat=time()-(strtotime ('1 month '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& date_ins>='.$supat.' ';
			break;
		case "6mois":
			$supat=time()-(strtotime ('6 months '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& date_ins>='.$supat.' ';
			break;
		case "an":
			$supat=time()-(strtotime ('1 year '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& date_ins>='.$supat.' ';
			break;
	}
}
// Dernière visite
if($_POST['checkedvisit']=='true') {
	switch($_POST['visit']) {
		case "aujourdhui":
			$supat=time()-(strtotime (date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& last_visit>='.$supat.' ';
			break;
		case "hier":
			$infat=time()-(strtotime (date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$supat=time()-(strtotime ('1 day '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& last_visit BETWEEN '.$supfat.' AND '.$infat.' ';
			break;
		case "semaine":
			$supat=time()-(strtotime ('7 week '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& last_visit>='.$supat.' ';
			break;
		case "mois":
			$supat=time()-(strtotime ('1 month '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& last_visit>='.$supat.' ';
			break;
		case "6mois":
			$supat=time()-(strtotime ('6 months '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& last_visit>='.$supat.' ';
			break;
		case "an":
			$supat=time()-(strtotime ('1 year '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& last_visit>='.$supat.' ';
			break;
	}
}
// Groupe
if($_POST['checkedgroupe']=='true') $param.='&& groupes.id='.$_POST['groupe'].' ';

// Encodage
header('Content-Type: '.CONTENTTYPE.'; charset='.CHARSET);

/**
 * Si il n'y a aucun paramètres, alors on ne renvoie rien (sinon on renvrait toutes les entres de la base ce qui peut tre asser long)
 */
if(empty($param)) {
echo '                <tr style="background-color:#F5F5F5; ">
                    <td colspan="5" style="border:1px solid #DDDDDD; text-align:center; ">Aucun résultat</td>
                </tr>';
exit();
}
/**
 * Sinon on effectue la requte
 */
$res=$sql->query('
	SELECT 
		mod_membres.id,
		pseudo,
		groupes.name AS groupe,
		part 
	FROM 
		mod_membres 
	LEFT JOIN 
		groupes ON mod_membres.groupe=groupes.id 
	WHERE 
		'.trim($param,' &').' 
	ORDER BY 
		pseudo
');

/**
 * Si il n'y a aucun rsultats, alors on fait comme plus haut
 */
if($sql->numRows($res)==0) {
echo '                <tr style="background-color:#F5F5F5; ">
                    <td colspan="5" style="border:1px solid #DDDDDD; text-align:center; ">Aucun résultat</td>
                </tr>';
exit();
}

/**
 * Sinon, boucle d'affichage des pseudos
 */
while($info=$sql->fetchAssoc($res)) {
?>
<tr>
<td><?php echo $info['pseudo'];?></td>
<td><?php echo $info['groupe'];?></td>
<td><?php echo $info['part'];?></td>
<td><a href="admin/membres/modifier-id<?php echo $info['id'];?>.html">Modifier</a></td>
<td><a href="admin/membres/supprimer-id<?php echo $info['id'];?>.html">Supprimer</a></td>
</tr>
<?php
}
exit();
?>