<?php
class galeries {
	
	function date($debut,$fin) {
		global $string;
		if(date('d',$debut)==date('d',$fin)) return 'le '.$string->formatDate('%d %B %Y',$debut);
		if(date('m',$debut)==date('m',$fin)) $format='%d';
		else if(date('Y',$debut)==date('Y',$fin)) $format='%d %B';
		else return $format='%d %B %Y';
		
		return 'du '.$string->formatDate($format,$debut).' au '.$string->formatDate('%d %B %Y',$fin);
	}
	
	function formatTags($tags) {
		$tags=explode(',',$tags);
		$list='';
		foreach($tags as $tag) {
			$list.='<a href="galeries/tags/'.urlencode($tag).'/">'.$tag.'</a>, ';
		}
		return trim($list,', ');
	}
	
	function getPhotosInfos($galery,$file) {
		global $string,$sql;
		
		$res=$sql->query('
			SELECT
				p.id, 
				nom,
				title,
				p.note,
				votes,
				views,
				COUNT(c.message) comms 
			FROM 
				mod_galeries_photos p 
					JOIN mod_galeries g ON id_galerie=g.id
					LEFT JOIN mod_comments c ON p.id=c.resource_id
			WHERE 
				id_galerie='.$galery.' AND 
				file="'.$file.'" AND
				(c.module="galeries" || p.votes=0)
			GROUP BY
				p.id
		');
		if($sql->numRows($res)==0) {
			$sql->query('INSERT INTO mod_galeries_photos (file,id_galerie,title) VALUES ("'.$file.'",'.$galery.',"'.$file.'")');
			$info=$sql->fetchAssoc($sql->query('SELECT nom FROM mod_galeries WHERE id='.$galery));
			$photo=array(
				'id'	=>	$sql->getId(),
				'nom'	=>	$info['nom'],
				'title'	=>	$file,
				'note'	=>	0,
				'votes'	=>	0,
				'views'	=>	1,
				'comms'	=>	0
			);
		} else {
			$sql->query('UPDATE mod_galeries_photos SET views=views+1 WHERE id_galerie='.$galery.' AND file="'.$file.'"');
			$photo=$sql->fetchAssoc($res);
			$photo['views']++;
		}
		
		// Récupération des infos du fichier
		$path='medias/galeries/'.$string->clean($photo['nom']).'/photos/'.$file;
		
		if(function_exists('exif_read_data')) {
			$exif=exif_read_data($path);
			$photo['weight']=$exif['FileSize'];
			$photo['height']=$exif['COMPUTED']['Height'];
			$photo['width']=$exif['COMPUTED']['Width'];
			$photo['model']=(isset($exif['Model'])?$exif['Model']:'Inconnu');
			$photo['flash']=(isset($exif['Flash'])?($exif['Flash']?'Oui':'Non'):'Inconnu');
			
			if(isset($exif['DateTimeOriginal'])) {
				$dateA=explode(':',$exif['DateTimeOriginal']);
				$dateB=explode(' ',$dateA[2]);
				$photo['date']=mktime($dateB[1],$dateA[3],$dateA[4],$dateA[1],$dateB[0],$dateA[0]);
			} else $photo['date']=0;
		
		} else {
			list($photo['height'],$photo['width'])=getimagesize($path);
			$photo['weight']=filesize($path);
			$photo['date']=filemtime($path);
			$photo['model']=$photo['flash']='Inconnu';
		}
		
		return $photo;
	}
	
	function countPhotos($nom) {
		if(function_exists('glob')) {
			$files = glob('medias/galeries/'.$nom.'/photos/*.jpg');
			$files = count($files);
		} else {
			$files=-2;
			if ($fp = opendir('medias/galeries/'.$nom.'/photos')) {
				while (false !== ($file = readdir($fp))) {
					$files++;
				}
				closedir($fp);
			}
		}
		return $files;
	}
	function countVideos($nom) {
		if(function_exists('glob')) {
			$files = glob('medias/galeries/'.$nom.'/videos/*.flv');
			$files = count($files);
		} else {
			$files=-2;
			if ($fp = opendir('medias/galeries/'.$nom.'/videos')) {
				while (false !== ($file = readdir($fp))) {
					$files++;
				}
				closedir($fp);
			}
		}
		return $files;
	}
	function countDivers($nom) {
		if(function_exists('glob')) {
			$files = glob('medias/galeries/'.$nom.'/divers/*.*');
			$files = count($files);
		} else {
			$files=-2;
			if ($fp = opendir('medias/galeries/'.$nom.'/divers')) {
				while (false !== ($file = readdir($fp))) {
					$files++;
				}
				closedir($fp);
			}
		}
		return $files;
	}

	function addComm($gallery,$file,$pseudo,$message,$note) {
		global $membres,$sql,$site,$string;
		if(!$membres->verifAcces('galeries_post_comm')) return false;
		
		$message=$site->clear4Sql($message);

		if($membres->infos('id')) $id=$membres->infos('id');
		else $id=0;

		
		# Vérification des informations saisies
		if(empty($pseudo)) return 2;
		if(eregi('[^A-Z0-9]',$pseudo)) return 3;
		if(empty($message)) return 1;
		if(!in_array($note,array(0,1,2,3,4,5))) $note=-1;
		# The End
		
		// Récupération des informations du fichier
		$info=$sql->fetchAssoc($sql->query('
			SELECT 
				id,
				note,
				votes
			FROM 
				mod_galeries_photos
			WHERE 
				file="'.$file.'" AND
				id_galerie='.$gallery.'
		'));

		// Postage du commentaire
		$sql->query('
			INSERT INTO 
				mod_comments (module,resource_id,note,author_name,author_id,date,message)
			VALUES (
				"galeries",
				"'.$info['id'].'",
				"'.$note.'",
				"'.$pseudo.'",
				"'.$id.'",
				"'.time().'",
				"'.$message.'"
		)');
		
		// Mise  jour de la note
		if(in_array($note,array(0,1,2,3,4,5))) {
			$newnote=round((($info['note']*$info['votes'])+$note)/($info['votes']+1),2);
			$sql->query('UPDATE mod_galeries_photos SET votes=votes+1,note="'.$newnote.'" WHERE file="'.$file.'" AND id_galerie='.$gallery);
		}

		// Redirection
		header("Cache-control: private, no-cache");
		header('location:'.$file.'#comms');
		exit();
	}

	function editComm($gallery,$file,$id,$message,$note) {
		global $membres,$sql,$site,$string;
		
		$info=$sql->fetchAssoc($sql->query('SELECT author_id,note FROM mod_comments WHERE id='.$id));
		$oldnote=$info['note'];
		
		if($membres->infos('groupe')!=4 && ($info['author_id']==0 || ($info['author_id']!=$membres->infos('id') && $membres->verifAcces('galeries_post_comm')))) return false;
		
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
				id,
				note,
				votes
			FROM 
				mod_galeries_photos
			WHERE 
				file="'.$file.'" AND
				id_galerie='.$gallery.'
		'));

		
		// Mise  jour de la note
		if(in_array($note,array(0,1,2,3,4,5))) {
			$newnote=round((($info['note']*$info['votes'])-$oldnote+$note)/($info['votes']),2);
			$sql->query('UPDATE mod_galeries_photos SET note="'.$newnote.'" WHERE file="'.$file.'" AND id_galerie='.$gallery);
		}

		// Redirection
		header("Cache-control: private, no-cache");
		header('location:'.$file.'#comms');
		exit();
	}
	
	function delComm($gallery,$file,$id) {
		global $membres,$sql,$site,$string;
		
		$info=$sql->fetchAssoc($sql->query('SELECT author_id,note FROM mod_comments WHERE id='.$id));
		$oldnote=$info['note'];
		
		if($membres->infos('groupe')!=4 && ($info['author_id']==0 || ($info['author_id']!=$membres->infos('id') && $membres->verifAcces('galeries_post_comm')))) return false;
		
		// Suppression du commentaire
		$info=$sql->fetchAssoc($sql->query('DELETE FROM mod_comments WHERE id='.$id));

		// Récupération des informations du fichier
		$info=$sql->fetchAssoc($sql->query('
			SELECT 
				id,
				note,
				votes
			FROM 
				mod_galeries_photos
			WHERE 
				file="'.$file.'" AND
				id_galerie='.$gallery.'
		'));

		
		// Mise  jour de la note
		if(in_array($oldnote,array(0,1,2,3,4,5))) {
			if($info['votes']-1==0) $newnote=0;
			else $newnote=round((($info['note']*$info['votes'])-$oldnote)/($info['votes']-1),2);
			$sql->query('UPDATE mod_galeries_photos SET note="'.$newnote.'",votes=votes-1 WHERE file="'.$file.'" AND id_galerie='.$gallery);
		}
		// Redirection
		header("Cache-control: private, no-cache");
		header('location:'.$file.'#comms');
		exit();
	}
	
	function addGallery($nom,$descr,$jour,$mois,$annee,$jour2,$mois2,$annee2) {
		global $site,$sql,$membres,$string;
		
		/**
		 * Vérifications des informations saisies
		 */
		$erreur=array();
		$nom		=	$site->clear4Sql($nom);
		$cleanNom	=	$string->clean($nom);
		$descr		=	$site->clear4Sql($descr);
		$datedebut	=	mktime(0,0,0,$mois,$jour,$annee);
		$datefin	=	mktime(0,0,0,$mois2,$jour2,$annee2);
		
		if(empty($nom))								$erreur[]='nom';
		if(is_dir('medias/galeries/'.$cleanNom)) 	$erreur[]='nom2';
		if($datedebut>$datefin)						$erreur[]='date';
		
		if(count($erreur)>0) return $erreur;
		/* Fin Vérifications */
		
		// Création des dossiers
		if(!mkdir('medias/galeries/'.$cleanNom)) {
			$membres->sendMessage(1,'Country','Erreur d\'criture','Erreur de Création du dossier "medias/galeries/'.$cleanNom.'", vrifiez les droits d\'criture du dossier parent.',1);
			header("Cache-control: private, no-cache");
			header('location:list.html#err0');
			exit();
		}
		else {
			chmod('medias/galeries/'.$cleanNom,0777);
			
			mkdir('medias/galeries/'.$cleanNom.'/photos');
			mkdir('medias/galeries/'.$cleanNom.'/videos');
			mkdir('medias/galeries/'.$cleanNom.'/divers');
			
			chmod('medias/galeries/'.$cleanNom.'/photos',0777);
			chmod('medias/galeries/'.$cleanNom.'/videos',0777);
			chmod('medias/galeries/'.$cleanNom.'/divers',0777);
		
			$sql->query('
				INSERT INTO 
					mod_galeries (nom,descr,datedebut,datefin,tags) 
				VALUES (
					"'.$nom.'",
					"'.$descr.'",
					"'.$datedebut.'",
					"'.$datefin.'",
					""
			)');
			header("Cache-control: private, no-cache");
			header('location:list.html#mess0');
			exit();
		}
	}
}
?>