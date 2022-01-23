<?php
/**
 * Classe du forum.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class download {

	function addCat($cat,$nom,$descr) {
		global $site,$sql;
		$cat=(int)$cat;
		$nom=$site->clear4Sql($nom);
		$descr=$site->clear4Sql($descr);
		if(empty($nom)) return 1;
		else if($cat!=0 && $sql->numRows($sql->query('SELECT id FROM mod_download_cats WHERE id="'.$cat.'"'))==0) return 2;
		
		$ordre=$sql->numRows($sql->query('SELECT id FROM mod_download_cats WHERE cat="'.$cat.'"'))+1;
		
		$sql->query('
			INSERT INTO 
				mod_download_cats (nom,descr,cat,ordre) 
			VALUES (
				"'.$nom.'",
				"'.$descr.'",
				"'.$cat.'",
				"'.$ordre.'"
		)');
		
		header("Cache-control: private, no-cache");
		header('location:gerercat.html#mess0');
		exit();
	}

	function editCat($cat,$nom,$descr) {
		global $site,$sql;
		$cat=(int)$cat;
		$nom=$site->clear4Sql($nom);
		$descr=$site->clear4Sql($descr);
		if(empty($nom)) return 1;
		else if($cat!=0 && $sql->numRows($sql->query('SELECT id FROM mod_download_cats WHERE id="'.$cat.'"'))==0) return 2;

		$info=$sql->fetchAssoc($sql->query('SELECT cat,ordre FROM mod_download_cats WHERE id="'.$_GET['id'].'"'));
		if($info['cat']!=$cat) {
			$ordre=$sql->numRows($sql->query('SELECT id FROM mod_download_cats WHERE cat="'.$cat.'"'))+1;
			$ordre='ordre="'.$ordre.'",';
			$sql->query('UPDATE mod_download_cats SET ordre=ordre-1 WHERE cat='.$info['cat'].' && ordre>"'.$info['ordre'].'" && id!="'.$_GET['id'].'"');
		} else $ordre='';
		
		$sql->query('
			UPDATE 
				mod_download_cats
			SET 
				nom="'.$nom.'",
				descr="'.$descr.'",
				'.$ordre.'
				cat="'.$cat.'"
			WHERE
				id="'.$_GET['id'].'"
		');
		
		header("Cache-control: private, no-cache");
		header('location:gerercat.html#mess2');
		exit();
	}
	
	function createCatsTree() {
		global $sql;
		$res=$sql->query('SELECT c.id,c.nom,c.cat,c.descr,COUNT(d.cat) nb FROM mod_download_cats c LEFT JOIN mod_download d ON c.id=d.cat GROUP BY c.id ORDER BY c.ordre');
		$cats=array();
		while($info=$sql->fetchArray($res)) {
			list(,$cats[$info['id']]['nom'],$cats[$info['id']]['cat'],$cats[$info['id']]['descr'],$cats[$info['id']]['nb'])=$info;
		}
		// Dtermination de leurs niveaux
		foreach($cats as $i=>$var) {
			if($var['cat']!=0) {
				$cat=$var['cat'];
				while($cat!=0) {
					if($cats[$i]['cat']==$cat) $cats[$cat]['childs'][]=$i;
					$cat=$cats[$cat]['cat'];
				}
			}
		}
		// Classement
		$tree=array();
		$j=0;
		foreach($cats as $i=>$var) {
			if($var['cat']==0) {
				$tree[$j++]=array('id'=>$i,'nom'=>$var['nom'],'level'=>1,'cat'=>$var['cat'],'descr'=>$var['descr'],'nbFile'=>$var['nb']);
				if(isset($var['childs'])) $this->getChilds($tree,$cats,$var['childs'],$j,2);
			}
		}
		return $tree;
	}
	
	function getChilds(&$tree,$cats,$childs,&$j,$i) {
		foreach($childs as $var) {
			$tree[$j++]=array('id'=>$var,'nom'=>$cats[$var]['nom'],'level'=>$i,'cat'=>$cats[$var]['cat'],'descr'=>$cats[$var]['descr'],'nbFile'=>$cats[$var]['nb']);
			if(isset($cats[$var]['childs'])) $this->getChilds($tree,$cats,$cats[$var]['childs'],$j,($i+1));
		}
	}

	function addFile($nom,$cat,$descr,$files,$miroir) {
		global $site,$sql,$string;
		if(isset($_POST['image'])) $image=$_POST['image'];
		$cat=(int)$cat;
		$nom=$site->clear4Sql($nom);
		$descr=$site->clear4Sql($descr);
		foreach($miroir as $i=>$var) $miroir[$i]=$site->clear4Sql($var);
		$miroir=$string->delEmptyEntry($miroir);
		if(count($miroir)==0) return 4;
		$size=$site->remoteInfos($miroir[0],'taille');
		$miroir=serialize($miroir);
		if(empty($nom)) return 1;
		else if($cat!=0 && $sql->numRows($sql->query('SELECT id FROM mod_download_cats WHERE id="'.$cat.'"'))==0) return 2;
		
		
		// Illustration
		if(isset($files['newimg']) && !empty($files['newimg']['name'])) {
			$types=array('image/x-png','image/gif','image/pjpeg','image/jpeg','image/png');
			if(!in_array($files['newimg']['type'],$types)) return 3;
			$file=pathinfo($files['newimg']['name']);
			$name=ereg_replace('-'.$file['extension'].'$','',$string->clean($file['basename']));
			$ext=$string->clean($file['extension']);
			if(!file_exists('images/'.$name.'.'.$ext)) {
				move_uploaded_file($files['newimg']['tmp_name'],'images/'.$name.'.'.$ext);
				$illus='images/'.$name.'.'.$ext;
			} else {
				$i=0;
				do {
					$i++;
					if(!file_exists('images/'.$name.'-'.$i.'.'.$ext)) {
						move_uploaded_file($files['newimg']['tmp_name'],'images/'.$name.'-'.$i.'.'.$ext);
						break;
					}
				} while(file_exists('images/'.$name.'-'.$i.'.'.$ext));
				$illus='images/'.$name.'-'.$i.'.'.$ext;
			}
		} else if(isset($image) && file_exists($image)) $illus=$image;
		else $illus='';

		$ordre=$sql->numRows($sql->query('SELECT id FROM mod_download WHERE cat="'.$cat.'"'))+1;

		// Insertion
		$sql->query('
			INSERT INTO 
				mod_download (nom,descr,cat,ordre,illus,size,dl,mirrors,active) 
			VALUES (
				"'.$nom.'",
				"'.$descr.'",
				"'.$cat.'",
				"'.$ordre.'",
				"'.$illus.'",
				"'.$size.'",
				"0",
				"'.$site->clear4Sql($miroir).'",
				"0"
		)');
		
		header("Cache-control: private, no-cache");
		header('location:gerercat.html#mess1');
		exit();
	}


	function editFile($nom,$cat,$descr,$files,$miroir) {
		global $site,$sql,$string;
		if(isset($_POST['image'])) $image=$_POST['image'];
		$cat=(int)$cat;
		$nom=$site->clear4Sql($nom);
		$descr=$site->clear4Sql($descr);
		foreach($miroir as $i=>$var) $miroir[$i]=$site->clear4Sql($var);
		$miroir=$string->delEmptyEntry($miroir);
		if(count($miroir)==0) return 4;
		$size=$site->remoteInfos($miroir[0],'taille');
		$miroir=serialize($miroir);
		if(empty($nom)) return 1;
		else if($cat!=0 && $sql->numRows($sql->query('SELECT id FROM mod_download_cats WHERE id="'.$cat.'"'))==0) return 2;
		
		
		// Illustration
		if(isset($files['newimg']) && !empty($files['newimg']['name'])) {
			$types=array('image/x-png','image/gif','image/pjpeg','image/jpeg','image/png');
			if(!in_array($files['newimg']['type'],$types)) return 3;
			$file=pathinfo($files['newimg']['name']);
			$name=ereg_replace('-'.$file['extension'].'$','',$string->clean($file['basename']));
			$ext=$string->clean($file['extension']);
			if(!file_exists('images/'.$name.'.'.$ext)) {
				move_uploaded_file($files['newimg']['tmp_name'],'images/'.$name.'.'.$ext);
				$illus='images/'.$name.'.'.$ext;
			} else {
				$i=0;
				do {
					$i++;
					if(!file_exists('images/'.$name.'-'.$i.'.'.$ext)) {
						move_uploaded_file($files['newimg']['tmp_name'],'images/'.$name.'-'.$i.'.'.$ext);
						break;
					}
				} while(file_exists('images/'.$name.'-'.$i.'.'.$ext));
				$illus='images/'.$name.'-'.$i.'.'.$ext;
			}
		} else if(isset($image) && file_exists($image)) $illus=$image;
		else $illus='';
		
		$info=$sql->fetchAssoc($sql->query('SELECT cat,ordre FROM mod_download WHERE id="'.$_GET['id'].'"'));
		if($info['cat']!=$cat) {
			$ordre=$sql->numRows($sql->query('SELECT id FROM mod_download WHERE cat="'.$cat.'"'))+1;
			$ordre='ordre="'.$ordre.'",';
			$sql->query('UPDATE mod_download SET ordre=ordre-1 WHERE cat='.$info['cat'].' && ordre>"'.$info['ordre'].'" && id!="'.$_GET['id'].'"');
		} else $ordre='';

		// Insertion
		$sql->query('
			UPDATE 
				mod_download
			SET 
				nom="'.$nom.'",
				descr="'.$descr.'",
				cat="'.$cat.'",
				'.$ordre.'
				illus="'.$illus.'",
				mirrors="'.$site->clear4Sql($miroir).'",
				size="'.$size.'",
				active=0
			WHERE
				id='.$_GET['id'].'
		');
		
		header("Cache-control: private, no-cache");
		header('location:gerercat.html#mess3');
		exit();
	}

	function delCat($id) {
		global $sql;
		
		$info=$sql->fetchAssoc($sql->query('SELECT cat,ordre FROM mod_download_cats WHERE id="'.$id.'"'));
		$sql->query('UPDATE mod_download_cats SET ordre=ordre-1 WHERE cat='.$info['cat'].' && ordre>"'.$info['ordre'].'" && id!="'.$id.'"');
		
		$sql->query('DELETE FROM mod_download_cats WHERE id="'.$id.'"');
		header("Cache-control: private, no-cache");
		header('location:gerercat.html#mess4');
		exit();
	}
	
	function delFile($id) {
		global $sql;
		
		$info=$sql->fetchAssoc($sql->query('SELECT cat,ordre FROM mod_download WHERE id="'.$id.'"'));
		$sql->query('UPDATE mod_download SET ordre=ordre-1 WHERE cat='.$info['cat'].' && ordre>"'.$info['ordre'].'" && id!="'.$id.'"');
		
		$sql->query('DELETE FROM mod_download WHERE id="'.$id.'"');
		header("Cache-control: private, no-cache");
		header('location:gerercat.html#mess5');
		exit();
	}
	
	function moveCat($id,$action) {
		global $sql,$site;
		$info=$sql->fetchAssoc($sql->query('SELECT cat,ordre FROM mod_download_cats WHERE id="'.$id.'"'));
		$cat=$info['cat'];
		$info2=$sql->fetchAssoc($sql->query('SELECT MAX(ordre) FROM mod_download_cats WHERE cat='.$cat));
		$max=$info2['0'];
		if($action=='up' && $info['ordre']!=1) { 				// Déplace la catégorie vers le haut
			$sql->query('UPDATE mod_download_cats SET ordre=ordre-1 WHERE cat='.$cat.' && id="'.$id.'"');
			$sql->query('UPDATE mod_download_cats SET ordre="'.$info['ordre'].'" WHERE cat='.$cat.' && ordre="'.($info['ordre']-1).'" && id!="'.$id.'"');
		} else if($action=='down' && $info['ordre']!=$max) { 	// Déplace la catégorie vers le bas
			$sql->query('UPDATE mod_download_cats SET ordre=ordre+1 WHERE cat='.$cat.' && id="'.$id.'"');
			$sql->query('UPDATE mod_download_cats SET ordre="'.$info['ordre'].'" WHERE cat='.$cat.' && ordre="'.($info['ordre']+1).'" && id!="'.$id.'"');
		} else if($action=='top' && $info['ordre']!=1) { 		// Déplace la catégorie tout en haut
			$sql->query('UPDATE mod_download_cats SET ordre=1 WHERE cat='.$cat.' && id="'.$id.'"');
			$sql->query('UPDATE mod_download_cats SET ordre=ordre+1 WHERE cat='.$cat.' && ordre<="'.($info['ordre']-1).'" && id!="'.$id.'"');
		} else if($action=='bottom' && $info['ordre']!=$max) { // Déplace la catégorie tout en bas
			$sql->query('UPDATE mod_download_cats SET ordre="'.$max.'" WHERE cat='.$cat.' && id="'.$id.'"');
			$sql->query('UPDATE mod_download_cats SET ordre=ordre-1 WHERE cat='.$cat.' && ordre>"'.$info['ordre'].'" && id!="'.$id.'"');
		}
		header("Cache-control: private, no-cache");
		header('location:gerercat.html');
		exit();
	}


	function moveFile($id,$action) {
		global $sql,$site;
		$info=$sql->fetchAssoc($sql->query('SELECT cat,ordre FROM mod_download WHERE id="'.$id.'"'));
		$cat=$info['cat'];
		$info2=$sql->fetchAssoc($sql->query('SELECT MAX(ordre) FROM mod_download WHERE cat='.$cat));
		$max=$info2['0'];
		if($action=='up' && $info['ordre']!=1) { 				// Déplace la catégorie vers le haut
			$sql->query('UPDATE mod_download SET ordre=ordre-1 WHERE cat='.$cat.' && id="'.$id.'"');
			$sql->query('UPDATE mod_download SET ordre="'.$info['ordre'].'" WHERE cat='.$cat.' && ordre="'.($info['ordre']-1).'" && id!="'.$id.'"');
		} else if($action=='down' && $info['ordre']!=$max) { 	// Déplace la catégorie vers le bas
			$sql->query('UPDATE mod_download SET ordre=ordre+1 WHERE cat='.$cat.' && id="'.$id.'"');
			$sql->query('UPDATE mod_download SET ordre="'.$info['ordre'].'" WHERE cat='.$cat.' && ordre="'.($info['ordre']+1).'" && id!="'.$id.'"');
		} else if($action=='top' && $info['ordre']!=1) { 		// Déplace la catégorie tout en haut
			$sql->query('UPDATE mod_download SET ordre=1 WHERE cat='.$cat.' && id="'.$id.'"');
			$sql->query('UPDATE mod_download SET ordre=ordre+1 WHERE cat='.$cat.' && ordre<="'.($info['ordre']-1).'" && id!="'.$id.'"');
		} else if($action=='bottom' && $info['ordre']!=$max) { // Déplace la catégorie tout en bas
			$sql->query('UPDATE mod_download SET ordre="'.$max.'" WHERE cat='.$cat.' && id="'.$id.'"');
			$sql->query('UPDATE mod_download SET ordre=ordre-1 WHERE cat='.$cat.' && ordre>"'.$info['ordre'].'" && id!="'.$id.'"');
		}
		header("Cache-control: private, no-cache");
		header('location:gererfile-id'.$cat.'.html');
		exit();
	}

	function addComm($file,$pseudo,$message,$note) {
		global $membres,$sql,$site,$string;
		if(!$membres->verifAcces('download_post_comm')) return false;
		
		$message=$site->clear4Sql($message);

		if($membres->infos('id')) $id=$membres->infos('id');
		else $id=0;

		
		# Vérification des informations saisies
		if(empty($pseudo)) return 2;
		if(eregi('[^A-Z0-9]',$pseudo)) return 3;
		if(empty($message)) return 1;
		if(!in_array($note,array(0,1,2,3,4,5))) $note=-1;
		# The End
		
		// Postage du commentaire
		$sql->query('
			INSERT INTO 
				mod_comments (module,resource_id,note,author_name,author_id,date,message)
			VALUES (
				"download",
				"'.$file.'",
				"'.$note.'",
				"'.$pseudo.'",
				"'.$id.'",
				"'.time().'",
				"'.$message.'"
		)');

		// Récupération des informations du fichier
		$info=$sql->fetchAssoc($sql->query('
			SELECT 
				d.id,
				d.nom,
				d.note,
				d.votes
			FROM 
				mod_download d LEFT JOIN mod_download_cats c ON d.cat=c.id 
			WHERE 
				d.id='.$file.'
		'));

		
		// Mise  jour de la note
		if(in_array($note,array(0,1,2,3,4,5))) {
			$newnote=round((($info['note']*$info['votes'])+$note)/($info['votes']+1),2);
			$sql->query('UPDATE mod_download SET votes=votes+1,note="'.$newnote.'" WHERE id='.$file);
		}

		// Redirection
		header("Cache-control: private, no-cache");
		header('location:'.$string->clean($info['nom']).'-id'.$info['id'].'.html#comms');
		exit();
	}

	function editComm($file,$id,$message,$note) {
		global $membres,$sql,$site,$string;
		
		$info=$sql->fetchAssoc($sql->query('SELECT author_id,note FROM mod_comments WHERE id='.$id));
		$oldnote=$info['note'];
		
		if($membres->infos('groupe')!=4 && ($info['author_id']==0 || ($info['author_id']!=$membres->infos('id') && $membres->verifAcces('download_post_comm')))) return false;
		
		$message=$site->clear4Sql($message);
		
		# Vérification des informations saisies
		if(empty($message)) return 1;
		if(!in_array($note,array(0,1,2,3,4,5))) $note=-1;
		# The End
		
		// Postage du commentaire
		$sql->query('
			UPDATE 
				mod_comments
			SET 
				note="'.$note.'",
				message="'.$message.'"
			WHERE
				id='.$id
		);

		// Récupération des informations du fichier
		$info=$sql->fetchAssoc($sql->query('
			SELECT 
				d.id,
				d.nom,
				d.note,
				d.votes
			FROM 
				mod_download d LEFT JOIN mod_download_cats c ON d.cat=c.id 
			WHERE 
				d.id='.$file.'
		'));

		
		// Mise  jour de la note
		if(in_array($note,array(0,1,2,3,4,5))) {
			$newnote=round((($info['note']*$info['votes'])-$oldnote+$note)/($info['votes']),2);
			$sql->query('UPDATE mod_download SET note="'.$newnote.'" WHERE id='.$file);
		}

		// Redirection
		header("Cache-control: private, no-cache");
		header('location:'.$string->clean($info['nom']).'-id'.$info['id'].'.html#comms');
		exit();
	}
	
	function delComm($file,$id) {
		global $membres,$sql,$site,$string;
		
		$info=$sql->fetchAssoc($sql->query('SELECT author_id,note FROM mod_comments WHERE id='.$id));
		$oldnote=$info['note'];
		
		if($membres->infos('groupe')!=4 && ($info['author_id']==0 || ($info['author_id']!=$membres->infos('id') && $membres->verifAcces('download_post_comm')))) return false;
		
		// Suppression du commentaire
		$info=$sql->fetchAssoc($sql->query('DELETE FROM mod_comments WHERE id='.$id));

		// Récupération des informations du fichier
		$info=$sql->fetchAssoc($sql->query('
			SELECT 
				d.id,
				d.nom,
				d.note,
				d.votes
			FROM 
				mod_download d LEFT JOIN mod_download_cats c ON d.cat=c.id 
			WHERE 
				d.id='.$file.'
		'));

		
		// Mise  jour de la note
		if(in_array($oldnote,array(0,1,2,3,4,5))) {
			if($info['votes']-1==0) $newnote=0;
			else $newnote=round((($info['note']*$info['votes'])-$oldnote)/($info['votes']-1),2);
			$sql->query('UPDATE mod_download SET note="'.$newnote.'",votes=votes-1 WHERE id='.$file);
		}
		// Redirection
		header("Cache-control: private, no-cache");
		header('location:'.$string->clean($info['nom']).'-id'.$info['id'].'.html#comms');
		exit();
	}


}
?>