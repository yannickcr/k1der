<?php
/**
 * Classe de template permettant de sparer le code HTML du code PHP.
 *
 * @author	Kristian Koehntopp
 * @package	K1der
 */
class template {

	/**
	 * Serialization helper, the name of this class.
	 *
	 * @var       string
	 * @access    public
	 */
	private $className = 'template';

	/**
	 * The base directory from which template files are loaded.
	 *
	 * @var       string
	 * @access    private
	 * @see       setRoot
	 */
	public $root     = '.';
	 
	/**
	 * A hash of strings forming a translation table which translates variable names
	 * into names of files containing the variable content.
	 * $File[varname] = "filename";
	 *
	 * @var       array
	 * @access    private
	 * @see       set_file
	 */
	private $file     = array();
	
	/**
	 * A hash of strings forming a translation table which translates variable names
	 * into regular expressions for themselves.
	 * $VarKeys[varname] = "/varname/"
	 *
	 * @var       array
	 * @access    private
	 * @see       set_var
	 */
	private $varKeys  = array();
	
	/**
	 * A hash of strings forming a translation table which translates variable names
	 * into values for their respective varkeys.
	 * $VarVals[varname] = "value"
	 *
	 * @var       array
	 * @access    private
	 * @see       set_var
	 */
	private $varVals  = array();
	
	/**
	 * Determines how to output variable tags with no assigned value in templates.
	 *
	 * @var       string
	 * @access    private
	 * @see       set_unknowns
	 */
	private $Unknowns = 'remove';
	
	/**
	 * Determines how Template handles error conditions.
	 * "yes"      = the error is reported, then execution is halted
	 * "report"   = the error is reported, then execution continues by returning "false"
	 * "no"       = errors are silently ignored, and execution resumes reporting "false"
	 *
	 * @var       string
	 * @access    public
	 * @see       halt
	 */
	private $haltOnError  = 'yes';
	
	/**
	 * The last error message is retained in this variable.
	 *
	 * @var       string
	 * @access    public
	 * @see       halt
	 */
	private $lastError     = '';
	
	/**
	 * Class constructor. May be called with two optional parameters.
	 * The first parameter sets the template directory the second parameter
	 * sets the policy regarding handling of unknown variables.
	 *
	 * usage: Template([string $Root = "."], [string $Unknowns = "remove"])
	 *
	 * @param     $Root        path to template directory
	 * @param     $string      what to do with undefined variables
	 * @see       set_root
	 * @see       set_unknowns
	 * @access    public
	 * @return    void
	 */
	public function Template($Root = '.', $Unknowns = 'remove') {
		$this->setRoot($Root);
		$this->setUnknowns($Unknowns);
		$this->setVar(array('THEME'=>THEME,'CHARSET'=>CHARSET,'REQUEST_URI'=>htmlentities($_SERVER['REQUEST_URI'])));
	}

	/**
	 * Checks that $Root is a valid directory and if so sets this directory as the
	 * base directory from which templates are loaded by storing the value in
	 * $this->root. Relative filenames are prepended with the path in $this->root.
	 *
	 * Returns true on success, false on error.
	 *
	 * usage: setRoot(string $Root)
	 *
	 * @param     $Root         string containing new template directory
	 * @see       root
	 * @access    public
	 * @return    boolean
	 */
	public function setRoot($Root) {
		if (!is_dir($Root)) {
			$this->halt('set_root: '.$Root.' is not a directory.');
			return false;
		}
		$this->root = $Root;
		return true;
	}

	/**
	 * Sets the policy for dealing with unresolved variable names.
	 *
	 * unknowns defines what to do with undefined template variables
	 * "remove"   = remove undefined variables
	 * "comment"  = replace undefined variables with comments
	 * "keep"     = keep undefined variables
	 *
	 * Note: "comment" can cause unexpected results when the variable tag is embedded
	 * inside an HTML tag, for example a tag which is expected to be replaced with a URL.
	 *
	 * usage: setUnknowns(string $Unknowns)
	 *
	 * @param     $Unknowns         new value for unknowns
	 * @see       unknowns
	 * @access    public
	 * @return    void
	 */
	public function setUnknowns($Unknowns = 'remove') {
		$this->unknowns = $Unknowns;
	}

	/**
	 * Defines a filename for the initial value of a variable.
	 *
	 * It may be passed either a varname and a file name as two strings or
	 * a hash of strings with the key being the varname and the value
	 * being the file name.
	 *
	 * The new mappings are stored in the array $this->file.
	 * The files are not loaded yet, but only when needed.
	 *
	 * Returns true on success, false on error.
	 *
	 * usage: setFile(array $Filelist = (string $varname => string $Filename))
	 * or
	 * usage: setFile(string $varname, string $Filename)
	 *
	 * @param     $varname      either a string containing a varname or a hash of varname/file name pairs.
	 * @param     $Filename     if varname is a string this is the filename otherwise filename is not required
	 * @access    public
	 * @return    boolean
	 */
	public function setFile($varname, $Filename = '') {
		if (!is_array($varname)) {
			if ($Filename == '') {
				$this->halt('set_file: For varname '.$varname.' filename is empty.');
				return false;
			}
			$this->file[$varname] = $this->fileName($Filename);
		} else {
			reset($varname);
			while(list($v, $f) = each($varname)) {
				if ($f == '') {
					$this->halt('set_file: For varname ',$v,' filename is empty.');
					return false;
				}
				$this->file[$v] = $this->fileName($f);
			}
		}
		$this->lastFile=$varname;
		return true;
	}

	/**
	 * A variable $parent may contain a variable block defined by:
	 * &lt;!-- BEGIN $varname --&gt; content &lt;!-- END $varname --&gt;. This function removes
	 * that block from $parent and replaces it with a variable reference named $name.
	 * The block is inserted into the varkeys and varvals hashes. If $name is
	 * omitted, it is assumed to be the same as $varname.
	 *
	 * Blocks may be nested but care must be taken to extract the blocks in order
	 * from the innermost block to the outermost block.
	 *
	 * Returns true on success, false on error.
	 *
	 * usage: setBlock(string $parent, string $varname, [string $name = ""])
	 *
	 * @param     $parent       a string containing the name of the parent variable
	 * @param     $varname      a string containing the name of the block to be extracted
	 * @param     $name         the name of the variable in which to store the block
	 * @access    public
	 * @return    boolean
	 */
	public function setBlock($parent, $varname, $name = '') {
		if (!$this->loadFile($parent)) {
			$this->halt('set_block: unable to load '.$parent.'.');
			return false;
		}
		if ($name == '') {
			$name = $varname;
			$varname = strtoupper($varname);
		}
		
		$str = $this->getVar($parent);
		$reg = "/[ \t]*<!--\s+BEGIN $varname\s+-->\s*?\n?(\s*.*?\n?)\s*<!--\s+END $varname\s+-->\s*?\n?/sm";
		preg_match_all($reg, $str, $m);
		$str = preg_replace($reg,'{'.$name.'}', $str);
		if(isset($m[1][0])) $this->setVar($varname, $m[1][0]);
		$this->setVar($parent, $str);
		return true;
	}


	/**
	 * This functions sets the value of a variable.
	 *
	 * It may be called with either a varname and a value as two strings or an
	 * an associative array with the key being the varname and the value being
	 * the new variable value.
	 *
	 * The function inserts the new value of the variable into the $VarKeys and
	 * $VarVals hashes. It is not necessary for a variable to exist in these hashes
	 * before calling this function.
	 *
	 * An optional third parameter allows the value for each varname to be appended
	 * to the existing variable instead of replacing it. The default is to replace.
	 * This feature was introduced after the 7.2d release.
	 *
	 *
	 * usage: setVar(string $varname, [string $value = ""], [boolean $append = false])
	 * or
	 * usage: setVar(array $varname = (string $varname => string $value), [mixed $dummy_var], [boolean $append = false])
	 *
	 * @param     $varname      either a string containing a varname or a hash of varname/value pairs.
	 * @param     $value        if $varname is a string this contains the new value for the variable otherwise this parameter is ignored
	 * @param     $append       if true, the value is appended to the variable's existing value
	 * @access    public
	 * @return    void
	 */
	public function setVar($varname, $value = '', $append = false) {
		if (!is_array($varname)) {
			if (!empty($varname)) {
				$this->varKeys[$varname] = '/'.$this->varName($varname).'/';
				if ($append && isset($this->varVals[$varname])) {
					$this->varVals[$varname] .= $value;
				} else {
					$this->varVals[$varname] = $value;
				}
			}
		} else {
			reset($varname);
			while(list($k, $v) = each($varname)) {
				if (!empty($k)) {
					$this->varKeys[$k] = '/'.$this->varName($k).'/';
					if ($append && isset($this->varVals[$k])) {
						$this->varVals[$k] .= $v;
					} else {
						$this->varVals[$k] = $v;
					}
				}
			}
		}
	}

	/**
	 * This functions clears the value of a variable.
	 *
	 * It may be called with either a varname as a string or an array with the 
	 * values being the varnames to be cleared.
	 *
	 * The function sets the value of the variable in the $VarKeys and $VarVals 
	 * hashes to "". It is not necessary for a variable to exist in these hashes
	 * before calling this function.
	 *
	 *
	 * usage: clearVar(string $varname)
	 * or
	 * usage: clearVar(array $varname = (string $varname))
	 *
	 * @param     $varname      either a string containing a varname or an array of varnames.
	 * @access    public
	 * @return    void
	 */
	public function clearVar($varname) {
		if (!is_array($varname)) {
			if (!empty($varname)) {
				$this->setVar($varname, '');
			}
		} else {
			reset($varname);
			while(list($k, $v) = each($varname)) {
				if (!empty($v)) {
					$this->setVar($v, '');
				}
			}
		}
	}


	/**
	 * This functions unsets a variable completely.
	 *
	 * It may be called with either a varname as a string or an array with the 
	 * values being the varnames to be cleared.
	 *
	 * The function removes the variable from the $VarKeys and $VarVals hashes.
	 * It is not necessary for a variable to exist in these hashes before calling
	 * this function.
	 *
	 *
	 * usage: unsetVar(string $varname)
	 * or
	 * usage: unsetVar(array $varname = (string $varname))
	 *
	 * @param     $varname      either a string containing a varname or an array of varnames.
	 * @access    public
	 * @return    void
	 */
	public function unsetVar($varname) {
		if (!is_array($varname)) {
			if (!empty($varname)) {
				unset($this->varKeys[$varname]);
				unset($this->varVals[$varname]);
			}
		} else {
			reset($varname);
			while(list($k, $v) = each($varname)) {
				if (!empty($v)) {
					unset($this->varKeys[$v]);
					unset($this->varVals[$v]);
				}
			}
		}
	}

	/**
	 * This function fills in all the variables contained within the variable named
	 * $varname. The resulting value is returned as the function result and the
	 * original value of the variable varname is not changed. The resulting string
	 * is not "finished", that is, the unresolved variable name policy has not been
	 * applied yet.
	 *
	 * Returns: the value of the variable $varname with all variables substituted.
	 *
	 * usage: subst(string $varname)
	 *
	 * @param     $varname      the name of the variable within which variables are to be substituted
	 * @access    public
	 * @return    string
	 */
	public function subst($varname) {
		$VarVals_quoted = array();
		if (!$this->loadFile($varname)) {
			$this->halt('subst: unable to load '.$varname.'.');
		return false;
		}

	// quote the replacement strings to prevent bogus stripping of special chars
		reset($this->varVals);
		while(list($k, $v) = each($this->varVals)) {
			$VarVals_quoted[$k] = preg_replace(array('/\\\\/', '/\$/'), array('\\\\\\\\', '\\\\$'), $v);
		}
		
		$str = $this->getVar($varname);
		$str = preg_replace($this->varKeys, $VarVals_quoted, $str);
		return $str;
	}

	/**
	 * This is shorthand for print $this->subst($varname). See subst for further
	 * details.
	 *
	 * Returns: always returns false.
	 *
	 * usage: psubst(string $varname)
	 *
	 * @param     $varname      the name of the variable within which variables are to be substituted
	 * @access    public
	 * @return    false
	 * @see       subst
	 */
	public function psubst($varname) {
		echo $this->subst($varname);
		return false;
	}

	/**
	 * The function substitutes the values of all defined variables in the variable
	 * named $varname and stores or appends the result in the variable named $target.
	 *
	 * It may be called with either a target and a varname as two strings or a
	 * target as a string and an array of variable names in varname.
	 *
	 * The function inserts the new value of the variable into the $VarKeys and
	 * $VarVals hashes. It is not necessary for a variable to exist in these hashes
	 * before calling this function.
	 *
	 * An optional third parameter allows the value for each varname to be appended
	 * to the existing target variable instead of replacing it. The default is to
	 * replace.
	 *
	 * If $target and $varname are both strings, the substituted value of the
	 * variable $varname is inserted into or appended to $target.
	 *
	 * If $handle is an array of variable names the variables named by $handle are
	 * sequentially substituted and the result of each substitution step is
	 * inserted into or appended to in $target. The resulting substitution is
	 * available in the variable named by $target, as is each intermediate step
	 * for the next $varname in sequence. Note that while it is possible, it
	 * is only rarely desirable to call this function with an array of varnames
	 * and with $append = true. This append feature was introduced after the 7.2d
	 * release.
	 *
	 * Returns: the last value assigned to $target.
	 *
	 * usage: parse(string $target, [boolean $append])
	 * or
	 * usage: parse(string $target, [boolean $append])
	 *
	 * @param     $target      a string containing the name of the variable into which substituted $varnames are to be stored
	 * @param     $append      if true, the substituted variables are appended to $target otherwise the existing value of $target is replaced
	 * @access    public
	 * @return    string
	 * @see       subst
	 */
	public function parse($target,$append = false) {
		if(is_array($target)) {
			$varname=array();
			foreach($target as $i=>$var) $varname[$i]=strtoupper($var);
		} else $varname=strtoupper($target);
		if (!is_array($varname)) {
			$str = $this->subst($varname);
			if ($append) {
				$this->setVar($target, $this->getVar($target) . $str);
			} else {
				$this->setVar($target, $str);
			}
		} else {
			reset($varname);
			while(list($i, $v) = each($varname)) {
				$str = $this->subst($v);
				if ($append) {
					$this->setVar($target, $this->getVar($target) . $str);
				} else {
					$this->setVar($target, $str);
				}
			}
		}
		return $str;
	}

	/**
	 * The function substitutes the values of all defined variables in the variable
	 * named $varname and stores or appends the result in the variable named $target.
	 *
	 * It may be called with either a target and a varname as two strings or a
	 * target as a string and an array of variable names in varname.
	 *
	 * The function inserts the new value of the variable into the $VarKeys and
	 * $VarVals hashes. It is not necessary for a variable to exist in these hashes
	 * before calling this function.
	 *
	 * An optional third parameter allows the value for each varname to be appended
	 * to the existing target variable instead of replacing it. The default is to
	 * replace.
	 *
	 * If $target and $varname are both strings, the substituted value of the
	 * variable $varname is inserted into or appended to $target.
	 *
	 * If $handle is an array of variable names the variables named by $handle are
	 * sequentially substituted and the result of each substitution step is
	 * inserted into or appended to in $target. The resulting substitution is
	 * available in the variable named by $target, as is each intermediate step
	 * for the next $varname in sequence. Note that while it is possible, it
	 * is only rarely desirable to call this function with an array of varnames
	 * and with $append = true. This append feature was introduced after the 7.2d
	 * release.
	 *
	 * Returns: the last value assigned to $target.
	 *
	 * usage: parse(string $target, string $varname, [boolean $append])
	 * or
	 * usage: parse(string $target, array $varname = (string $varname), [boolean $append])
	 *
	 * @param     $target      a string containing the name of the variable into which substituted $varnames are to be stored
	 * @param     $varname     if a string, the name the name of the variable to substitute or if an array a list of variables to be substituted
	 * @param     $append      if true, the substituted variables are appended to $target otherwise the existing value of $target is replaced
	 * @access    public
	 * @return    string
	 * @see       subst
	 */
	public function globalParse($target,$varname,$append = false) {
		if (!is_array($varname)) {
			$str = $this->subst($varname);
			if ($append) {
				$this->setVar($target, $this->getVar($target) . $str);
			} else {
				$this->setVar($target, $str);
			}
		} else {
			reset($varname);
			while(list($i, $v) = each($varname)) {
				$str = $this->subst($v);
				if ($append) {
					$this->setVar($target, $this->getVar($target) . $str);
				} else {
					$this->setVar($target, $str);
				}
			}
		}
		return $str;
	}


	/**
	 * This is shorthand for print $this->parse(...) and is functionally identical.
	 * See parse for further details.
	 *
	 * Returns: always returns false.
	 *
	 * usage: pparse(string $target, string $varname, [boolean $append])
	 * or
	 * usage: pparse(string $target, array $varname = (string $varname), [boolean $append])
	 *
	 * @param     $target      a string containing the name of the variable into which substituted $varnames are to be stored
	 * @param     $varname     if a string, the name the name of the variable to substitute or if an array a list of variables to be substituted
	 * @param     $append      if true, the substituted variables are appended to $target otherwise the existing value of $target is replaced
	 * @access    public
	 * @return    false
	 * @see       parse
	 */
	public function pparse($target, $varname, $append = false) {
		echo $this->finish($this->globalParse($target, $varname, $append));
		return false;
	}

	/**
	 * This function returns an associative array of all defined variables with the
	 * name as the key and the value of the variable as the value.
	 *
	 * This is mostly useful for debugging. Also note that $this->debug can be used
	 * to echo all variable assignments as they occur and to trace execution.
	 *
	 * Returns: a hash of all defined variable values keyed by their names.
	 *
	 * usage: GetVars()
	 *
	 * @access    public
	 * @return    array
	 * @see       $Debug
	 */
	public function GetVars() {
		reset($this->varKeys);
		while(list($k, $v) = each($this->varKeys)) {
			$result[$k] = $this->getVar($k);
		}
		return $result;
	}

	/**
	 * This function returns the value of the variable named by $varname.
	 * If $varname references a file and that file has not been loaded yet, the
	 * variable will be reported as empty.
	 *
	 * When called with an array of variable names this function will return a a
	 * hash of variable values keyed by their names.
	 *
	 * Returns: a string or an array containing the value of $varname.
	 *
	 * usage: getVar(string $varname)
	 * or
	 * usage: getVar(array $varname)
	 *
	 * @param     $varname     if a string, the name the name of the variable to get the value of, or if an array a list of variables to return the value of
	 * @access    public
	 * @return    string or array
	 */
	public function getVar($varname) {
		if (!is_array($varname)) {
			if (isset($this->varVals[$varname])) {
				$str = $this->varVals[$varname];
			} else {
				$str = '';
			}
			return $str;
		} else {
			reset($varname);
			while(list($k, $v) = each($varname)) {
				if (isset($this->varVals[$v])) {
					$str = $this->varVals[$v];
				} else {
					$str = '';
				}
				$result[$v] = $str;
			}
			return $result;
		}
	}

	/**
	 * This function returns a hash of unresolved variable names in $varname, keyed
	 * by their names (that is, the hash has the form $a[$name] = $name).
	 *
	 * Returns: a hash of varname/varname pairs or false on error.
	 *
	 * usage: getUndefined(string $varname)
	 *
	 * @param     $varname     a string containing the name the name of the variable to scan for unresolved variables
	 * @access    public
	 * @return    array
	 */
	public function getUndefined($varname) {
		if (!$this->loadFile($varname)) {
			$this->halt('get_undefined: unable to load '.$varname.'.');
			return false;
		}
		
		preg_match_all('/{([^ \t\r\n}]+)}/', $this->getVar($varname), $m);
		$m = $m[1];
		if (!is_array($m)) return false;
		
		reset($m);
		while(list($k, $v) = each($m)) {
			if (!isset($this->varKeys[$v])) {
				$result[$v] = $v;
			}
		}
		
		if (count($result)) {
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * This function returns the finished version of $str. That is, the policy
	 * regarding unresolved variable names will be applied to $str.
	 *
	 * Returns: a finished string derived from $str and $this->unknowns.
	 *
	 * usage: finish(string $str)
	 *
	 * @param     $str         a string to which to apply the unresolved variable policy
	 * @access    public
	 * @return    string
	 * @see       set_unknowns
	 */
	public function finish($str) {
		switch ($this->unknowns) {
			case 'keep':
			break;
			
			case 'remove':
			$str = preg_replace('/{[^ \t\r\n}]+}/','', $str);
			$str = preg_replace("/[ \t]*<!--\s+BEGIN [^ \t\r\n}]+\s+-->\s*?\n?(\s*.*?\n?)\s*<!--\s+END [^ \t\r\n}]+\s+-->\s*?\n?/sm",'', $str);
			break;
			
			case 'comment':
			$str = preg_replace("/{([^ \t\r\n}]+)}/', '<!-- Template variable \\1 undefined -->", $str);
			break;
		}
		return $str;
	}

	/**
	 * This function prints the finished version of the value of the variable named
	 * by $varname. That is, the policy regarding unresolved variable names will be
	 * applied to the variable $varname then it will be printed.
	 *
	 * usage: p(string $varname)
	 *
	 * @param     $varname     a string containing the name of the variable to finish and print
	 * @access    public
	 * @return    void
	 * @see       set_unknowns
	 * @see       finish
	 */
	public function p($varname,$return=false) {
		if($return) return $this->finish($this->getVar($varname));
		echo $this->finish($this->getVar($varname));
	}

	/**
	 * This function returns the finished version of the value of the variable named
	 * by $varname. That is, the policy regarding unresolved variable names will be
	 * applied to the variable $varname and the result returned.
	 *
	 * Returns: a finished string derived from the variable $varname.
	 *
	 * usage: get(string $varname)
	 *
	 * @param     $varname     a string containing the name of the variable to finish
	 * @access    public
	 * @return    void
	 * @see       set_unknowns
	 * @see       finish
	 */
	public function get($varname) {
		return $this->finish($this->getVar($varname));
	}

	/**
	 * When called with a relative pathname, this function will return the pathname
	 * with $this->root prepended. Absolute pathnames are returned unchanged.
	 *
	 * Returns: a string containing an absolute pathname.
	 *
	 * usage: fileName(string $Filename)
	 *
	 * @param     $Filename    a string containing a filename
	 * @access    private
	 * @return    string
	 * @see       set_root
	 */
	public function fileName($Filename) {
		if (substr($Filename, 0, 1) != '/') {
			$Filename = $this->root.'/'.$Filename;
		}
		
		if (!file_exists($Filename)) {
			$this->halt('filename: file '.$Filename.' does not exist.');
		}
		return $Filename;
	}

	/**
	 * This function will construct a regexp for a given variable name with any
	 * special chars quoted.
	 *
	 * Returns: a string containing an escaped variable name.
	 *
	 * usage: varName(string $varname)
	 *
	 * @param     $varname    a string containing a variable name
	 * @access    private
	 * @return    string
	 */
	public function varName($varname) {
		return preg_quote('{'.$varname.'}');
	}


	/**
	 * If a variable's value is undefined and the variable has a filename stored in
	 * $this->file[$varname] then the backing file will be loaded and the file's
	 * contents will be assigned as the variable's value.
	 *
	 * Note that the behaviour of this function changed slightly after the 7.2d
	 * release. Where previously a variable was reloaded from file if the value
	 * was empty, now this is not done. This allows a variable to be loaded then
	 * set to "", and also prevents attempts to load empty variables. Files are
	 * now only loaded if $this->varVals[$varname] is unset.
	 *
	 * Returns: true on success, false on error.
	 *
	 * usage: loadFile(string $varname)
	 *
	 * @param     $varname    a string containing the name of a variable to load
	 * @access    private
	 * @return    boolean
	 * @see       set_file
	 */
	public function loadFile($varname) {
		if (!isset($this->file[$varname])) return true;
		
		if (isset($this->varVals[$varname])) return true;
		$Filename = $this->file[$varname];
		
		/* use @file here to avoid leaking filesystem information if there is an error */
		$str = implode('', @file($Filename));
		if (empty($str)) {
			$this->halt('loadfile: While loading ',$varname,', ',$Filename,' does not exist or is empty.');
			return false;
		}
		
		$this->setVar($varname, $str);
		
		return true;
	}


	/**
	 * This function is called whenever an error occurs and will handle the error
	 * according to the policy defined in $this->haltOnError. Adéditionally the
	 * error message will be saved in $this->last_error.
	 *
	 * Returns: always returns false.
	 *
	 * usage: halt(string $msg)
	 *
	 * @param     $msg         a string containing an error message
	 * @access    private
	 * @return    void
	 * @see       $HaltOnError
	 */
	public function halt($msg) {
		$this->last_error = $msg;
		
		if ($this->haltOnError != 'no') {
			$this->haltMsg($msg);
		}
		
		if ($this->haltOnError == 'yes') {
			die('<b>Halted.</b>');
		}
		
		return false;
	}

	/**
	 * This function prints an error message.
	 * It can be overridden by your subclass of Template. It will be called with an
	 * error message to display.
	 *
	 * usage: haltMsg(string $msg)
	 *
	 * @param     $msg         a string containing the error message to display
	 * @access    public
	 * @return    void
	 * @see       halt
	 */
	public function haltMsg($msg) {
		echo "<b>Template Error:</b> ",$msg,"<br>\n";
	}

}
?>
