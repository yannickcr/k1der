<?php

require_once 'HTMLPurifier/AttrDef.php';

/**
 * Validates the border property as defined by CSS.
 */
class HTMLPurifier_AttrDef_Border extends HTMLPurifier_AttrDef
{
    
    /**
     * Local copy of properties this property is shorthand for.
     */
    var $info = array();
    
    function HTMLPurifier_AttrDef_Border($config) {
        $def = $config->getCSSDefinition();
        $this->info['border-width'] = $def->info['border-width'];
        $this->info['border-style'] = $def->info['border-style'];
        $this->info['border-top-color'] = $def->info['border-top-color'];
    }
    
    function validate($string, $config, &$context) {
        $string = $this->parseCDATA($string);
        // we specifically will not support rgb() syntax with spaces
        $bits = explode(' ', $string);
        $done = array(); // segments we've finished
        $ret = ''; // return value
        foreach ($bits as $bit) {
            foreach ($this->info as $propname => $validator) {
                if (isset($done[$propname])) continue;
                $r = $validator->validate($bit, $config, $context);
                if ($r !== false) {
                    $ret .= $r . ' ';
                    $done[$propname] = true;
                    break;
                }
            }
        }
        return rtrim($ret);
    }
    
}

?>