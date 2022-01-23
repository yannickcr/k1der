<?php   ////////////////////////////////////////////////
        //   ___ _  _ ___     ___ _____ _ _____ ___   //
        //  | _ \ || | _ \___/ __|_   _/_\_   _/ __|  //
        //  |  _/ __ |  _/___\__ \ | |/ _ \| | \__ \  //
        //  |_| |_||_|_|0.1.9|___/ |_/_/ \_\_| |___/  //
        //                                            //
   /////////////////////////////////////////////////////////
   //       Author: Roberto Valsania (Webmaster76)        //
   //   Staff: Matrix, Viewsource, PaoDJ, Fabry, theCAS   //
   //          Homepage: www.php-stats.com,               //
   //                    www.php-stats.it,                //
   //                    www.php-stat.com                 //
   /////////////////////////////////////////////////////////

// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');

/////////////////////////////////////////////
// Preparazione varibili HTML del template //
/////////////////////////////////////////////
$option['nomesito']=stripcslashes($option['nomesito']);
$meta='<META NAME="ROBOTS" CONTENT="NONE">';
$phpstats_title="Php-Stats $option[phpstats_ver] - $phpstats_title";


//////////////////////////////////
// Generazione HTML da template //
//////////////////////////////////
eval("\$template=\"".gettemplate("$template_path/details.tpl")."\";");


?>