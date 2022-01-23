<?php
/**
 * Classe de gestion des news.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class news {

    /**
     * Similarit d'une news par rapport au titre
     *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string	Titre du sujet  comparer
	 * @param	integer	Id du sujet  comparer
	 * @return	resource
     */ 
	function similar($titre,$id) {
		global $sql,$site;
		
		$req=$sql->query('
		SELECT 
			id,
			titre,
			descr,
			posts,
			starter_id,
			start_date,
			last_poster_id,
			last_post,
			starter_name,
			last_poster_name,
			views,
			special,
			MATCH (titre) AGAINST ("'.$titre.'") AS score
		FROM 
			mod_forum_topics 
		WHERE 
			id!="'.$id.'" && 
			forum_id="'.$site->config('news_forum').'"
		ORDER BY score DESC,start_date DESC
		LIMIT 0,5
		');
		
		return $req;
	}

}
?>