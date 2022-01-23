<?php
/**
 * Classe d'abstration  la base de donne.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class mysql {

	var $i=0;
	var $query=array();
	/**
	 * Constructeur de la classe Sql.
	 * Récupère les données de connexion et les place dans le tableau $def
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function mysql($host, $user, $pass, $base) {
		$this->host=$host;
		$this->user=$user;
		$this->pass=$pass;
		$this->base=$base;
	}
	
	/**
	 * Connexion  la base de donne Sql.
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function connection() {
		$this->db=@mysql_connect($this->host, $this->user, $this->pass) or die(mysql_error());
		@mysql_select_db($this->base,$this->db) or die(mysql_error());
		
		if (version_compare(mysql_get_server_info(),'4.1','>=')) {
			mysql_query("SET NAMES 'utf8'");
			mysql_query('SET CHARACTER SET utf8');
		}
	}
	
	/**
	 * Donnection de la base de donne Sql.
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function disconnection() {
		@mysql_close() or die(mysql_error());
	}
	
	/**
	 * Se connecte  la base de donne, effecture la requete demande, se dconnecte puis renvoie le rsultat.
	 *
	 * @todo Pour la version finale : placer les erreurs dans un fichier log et ne rien afficher  l'cran
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Requete sql
	 * @return	resource
	 */
	function query($query) {
		static $i;
		$this->query[]=$query;
		$this->connection();
		//$this->req=mysql_query($query) or die('<strong>ERROR</strong><br />'.$query.'<br />'.mysql_error());
		$this->req=mysql_query($query) or die('
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta name="robots" content="noindex, nofollow" />
			<title>Site en maintenance</title>
			</head>
			<body>
				<p>Le site est tout cassé, revenez plus tard.</p>
			</body>
			</html>');
		$this->disconnection();
		$this->i++;
		return $this->req;
	}
	
	/**
	 * Retourne une ligne de rsultat MySQL sous la forme d'un tableau associatif, d'un tableau index, ou les deux.
	 * Fonctionnement similaire  mysql_fetch_array()
	 *
	 * @author    Yannick Croissant
	 * @access    public
	 * @param     resource Requete sql
	 * @param     integer Constante qui peut prendre les valeurs suivantes : MYSQL_ASSOC, MYSQL_NUM et MYSQL_BOTH
	 * @return    array
	 */
	function fetchArray($req,$mode='') { 
        if (!empty($mode)) return @mysql_fetch_array($req,$mode); 
        else return @mysql_fetch_array($req);
    } 

	/**
	 * Lit une ligne de rsultat MySQL dans un tableau associatif.
	 * Fonctionnement similaire  mysql_fetch_assoc()
	 *
	 * @author    Yannick Croissant
	 * @access    public
	 * @param     resource Requete sql
	 * @return    array
	 */
	function fetchAssoc($req) { 
        return @mysql_fetch_assoc($req); 
    } 

	/**
	 * Lit une ligne de rsultat MySQL dans un tableau index numriquement.
	 * Fonctionnement similaire  mysql_fetch_row()
	 *
	 * @author    Yannick Croissant
	 * @access    public
	 * @param     resource Requete sql
	 * @return    array
	 */
	function fetchRow($req) { 
        return @mysql_fetch_row($req); 
    } 
	
	/**
	 * Retourne le dernier identifiant gnr par un champ de type AUTO_INCREMENT.
	 * Fonctionnement similaire  mysql_insert_id()
	 *
	 * @author    Yannick Croissant
	 * @access    public
	 * @return    int
	 */
	function getId() { 
        return @mysql_insert_id($this->db);     
    } 
	
	/**
	 * Retourne le nombre de lignes d'un rsultat.
	 * Fonctionnement similaire  mysql_num_rows()
	 *
	 * @author    Yannick Croissant
	 * @param     resource Requete sql
	 * @access    public
	 * @return    int
	 */
	function numRows($req='') { 
        if(!empty($req)) return $this->numRows = @mysql_num_rows($req);
		else return $this->numRows = @mysql_num_rows($this->req); 
    }     
     
	/**
	 * Retourne le nombre de lignes affectes lors de la dernire requte INSERT, UPDATE ou DELETE.
	 * Fonctionnement similaire  mysql_affected_rows()
	 *
	 * @author    Yannick Croissant
	 * @access    public
	 * @return    int
	 */
    function affectedRows() { 
        return $this->affectedRows = @mysql_affected_rows($this->db); 
    }     
	
}
?>