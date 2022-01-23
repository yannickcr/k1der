<?php
// On nettoye les valeurs des trucs qui n'ont rien à faire là
$_POST['nom']=eregi_replace('[^a-z0-9]','',$_POST['nom']);
$_POST['leader']=eregi_replace('[^a-z0-9]','',$_POST['leader']);
$_POST['tag']=$site->clear4sql($_POST['tag']);
$_POST['create']=eregi_replace('[^a-z0-9]','',$_POST['create']);

/**
 * On parcours les valeurs envoyées afin de construire la requète adéquat :
 * - Si les champs ont étés remplis
 * - Si leurs checkbox respectives sont cochées 
 */
$param='';

// Pseudo
if($_POST['checkednom']=='true' && !empty($_POST['nom'])) $param.='&& mod_membres_clans.nom LIKE "%'.$_POST['nom'].'%" ';
// Participation
if($_POST['checkedleader']=='true' && !empty($_POST['leader'])) $param.='&& leader_pseudo LIKE "%'.$_POST['leader'].'%" ';
if($_POST['checkedtag']=='true' && !empty($_POST['tag'])) $param.='&& tag LIKE "%'.$_POST['tag'].'%" ';
// Date d'inscription
if($_POST['checkedcreate']=='true') {
	switch($_POST['create']) {
		case "aujourdhui":
			$supat=time()-(strtotime (date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& datecrea>='.$supat.' ';
			break;
		case "hier":
			$infat=time()-(strtotime (date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$supat=time()-(strtotime ('1 day '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& datecrea BETWEEN '.$supat.' AND '.$infat.' ';
			break;
		case "semaine":
			$supat=time()-(strtotime ('7 week '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& datecrea>='.$supat.' ';
			break;
		case "mois":
			$supat=time()-(strtotime ('1 month '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& datecrea>='.$supat.' ';
			break;
		case "6mois":
			$supat=time()-(strtotime ('6 months '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& datecrea>='.$supat.' ';
			break;
		case "an":
			$supat=time()-(strtotime ('1 year '.date('H').' hours '.date('i').' minutes '.date('s').' seconds')-time());
			$param.='&& datecrea>='.$supat.' ';
			break;
	}
}

// Encodage
header('Content-Type: '.CONTENTTYPE.'; charset='.CHARSET);

/**
 * Si il n'y a aucun paramètres, alors on ne renvoie rien (sinon on renvrait toutes les entres de la base ce qui peut être assez long)
 */
if(empty($param)) {
echo '                <tr>
                    <td colspan="5">Aucun résultat</td>
                </tr>';
exit();
}
/**
 * Sinon on effectue la requte
 */
$res=$sql->query('
	SELECT 
		mod_membres_clans.id,
		mod_membres_clans.nom,
		leader_pseudo AS leader,
		count(mod_membres.id) AS membres
	FROM 
		mod_membres_clans
	LEFT JOIN 
		mod_membres ON mod_membres_clans.id=mod_membres.clan_id
	WHERE 
		'.trim($param,' &').' 
	GROUP BY mod_membres.clan_id
	ORDER BY 
		pseudo
');

/**
 * Si il n'y a aucun rsultats, alors on fait comme plus haut
 */
if($sql->numRows($res)==0) {
echo '                <tr>
                    <td colspan="5">Aucun résultat</td>
                </tr>';
exit();
}

/**
 * Sinon, boucle d'affichage des pseudos
 */
while($info=$sql->fetchAssoc($res)) {
?>
<tr>
<td><a href="clans/<?php echo str_replace(' ','-',$info['nom']);?>"><?php echo $info['nom'];?></a></td>
<td><a href="membres/<?php echo $info['leader'];?>"><?php echo $info['leader'];?></a></td>
<td><?php echo $info['membres'];?></td>
<td><a href="admin/clans/modifier-id<?php echo $info['id'];?>.html">Fiche</a> / <a href="admin/clans/banniere-id<?php echo $info['id'];?>.html">Bannière</a></td>
<td><a class="confdel" href="admin/clans/supprimer-id<?php echo $info['id'];?>.html">Supprimer</a></td>
</tr>
<?php
}
exit();
?>