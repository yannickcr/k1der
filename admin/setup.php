<?php
include "secu.php";

///////////////////////////////////////////////
//                                           //
// Uploader v.1.1                            //
// ----------------------------------------- //
// by Graeme (webmaster@phpscriptcenter.com) //
// http://www.phpscriptcenter.com            //
//                                           //////////////////////////////
// PHP Script CENTER offers no warranties on this script.                //
// The owner/licensee of the script is solely responsible for any        //
// problems caused by installation of the script or use of the script    //
//                                                                       //
// All copyright notices regarding Uploader, must remain                 //
// intact on the scripts and in the HTML for the scripts.                //
//                                                                       //
// (c) Copyright 2001 PHP Script CENTER                                  //
//                                                                       //
// For more info on Uploader,                                            //
// see http://www.phpscriptcenter.com/uploader.php                       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

$ADMIN[RequirePass] = "No";   // Checks to see if upload has a vaild password
$ADMIN[Password] = "password";   // This is the password if the above option is Yes
$ADMIN[UploadNum] = "1";  // Number of upload feilds to put on the html page
$ADMIN[directory] = "images/dessins/$cat";  // The directory the files will be uploaded to (must be chmoded to 777)

?>