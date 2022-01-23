<?php
/**
 * Classe d'abstration � la base de donn�e.
 *
 * @author	Yannick Croissant
 * @package	K1der
 */
class Sql {

	/**
	 * Constructeur PHP5 de la classe Sql.
	 * Ex�cute le constructeur PHP4
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function __construct() {
		$this->Sql();
	}
	
	/**
	 * Constructeur PHP4 de la classe Sql.
	 * R�cup�re les donn�es de connection et les place dans le tableau $def
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function Sql() {
		global $string;
		$BDD=array(
			'Host'=>'localhost',
			'User'=>'k1der1',
			'Pass'=>'',
			'Base'=>'k1der1'
		);
		$this->sql=$BDD;
	}
	
	/**
	 * Connection � la base de donn�e Sql.
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function connection() {
		$this->db=@mysql_connect($this->sql['Host'], $this->sql['User'], $this->sql['Pass']) or die(mysql_error());
		@mysql_select_db($this->sql['Base'],$this->db) or die(mysql_error());
	}
	
	/**
	 * D�onnection de la base de donn�e Sql.
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 */
	function deconnection() {
		@mysql_close() or die(mysql_error());
	}
	
	/**
	 * Se connecte � la base de donn�e, effecture la requete demand�e, se d�connecte puis renvoie le r�sultat.
	 *
	 * @author	Yannick Croissant
	 * @access	public
	 * @param	string Requete sql
	 * @return	resource
	 */
	function query($requete) {
		$this->connection();
		$this->req=mysql_query($requete) or die('<strong>ERROR</strong><br />'.$requete.'<br />'.mysql_error());
		$this->deconnection();
		return $this->req;
	}
	
	/**
	 * Retourne le r�sultat de la requete $req sous forme de tableau.
	 * Fonctionnement similaire � mysql_fetch_array()
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
	 * Retourne le dernier identifiant g�n�r� par un champ de type AUTO_INCREMENT.
	 * Fonctionnement similaire � mysql_insert_id()
	 *
	 * @author    Yannick Croissant
	 * @access    public
	 * @return    int
	 */
	function getId() { 
        return @mysql_insert_id($this->db);     
    } 
	
	/**
	 * Retourne le nombre de lignes d'un r�sultat.
	 * Fonctionnement similaire � mysql_num_rows()
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
	 * Retourne le nombre de lignes affect�es lors de la derni�re requ�te INSERT, UPDATE ou DELETE.
	 * Fonctionnement similaire � mysql_affected_rows()
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