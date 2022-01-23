<?php
        ////////////////////////////////////////////////
        //   ___ _  _ ___     ___ _____ _ _____ ___   //
        //  | _ \ || | _ \___/ __|_   _/_\_   _/ __|  //
        //  |  _/ __ |  _/___\__ \ | |/ _ \| | \__ \  //
        //  |_| |_||_|_|0.1.9|___/ |_/_/ \_\_| |___/  //
        //                                            //
   /////////////////////////////////////////////////////////
   //       Author: Roberto Valsania (Webmaster76)        //
   //   Staff:                                            //
   //         Matrix - Massimiliano Coppola               //
   //         Viewsource                                  //
   //         PaoDJ - Paolo Antonio Tremadio              //
   //         Fabry - Fabrizio Tomasoni                   //
   //         theCAS - Carlo Alberto Siti                 //
   //                                                     //
   //          Homepage: www.php-stats.com,               //
   //                    www.php-stats.it,                //
   //                    www.php-stat.com                 //
   /////////////////////////////////////////////////////////

//////////////////////
// CONFIGURAZIONE  //
/////////////////////
  $option['host']="localhost";                   // Indirizzo server MySQL o IP
  $option['database']="k1der1";                    // Nome database
  $option['user_db']="root";                        // Utente
  $option['pass_db']="";                    // Password
  $option['script_url']="http://www.k1der.net/phpstats"; // Indirizzo di installazione di Php-Stats
  $option['exc_pass']=''; // Password utilizzata da php-stats per abilitare l'esclusione degli ip dinamici.

////////////////////////
// VARIABILI AVANZATE //
////////////////////////
$option['prefix']='php_stats';    // Prefisso per le tabelle di Php-Stats (default "php_stats")
$option['callviaimg']=1;          // 1 richiama Php-Stats attraverso immagine trasparente 1x1 pixel, 0 attraverso javascript
$option['php_stats_safe']=0;      // 0 se avete MySQL 3.23 o superiore ; 1 per MySQL 3.22
$option['out_compress']=1;        // 1 compressione dell'output (PHP>4.0.4)
$option['persistent_conn']=0;     // 1 utilizza utilizza una connessione persistente a mysql
$option['autorefresh']=3;         // In MINUTI, aggiornamento automatico pagine dell'admin
$option['show_server_details']=1; // Visualizzati nella pagina principale
$option['show_average_user']=0;   // 1 visualizza l'utente medio nella pagina principale
$option['short_url']=1;           // 1 Mostra url brevi quando possibile
$option['lock_not_valid_url']=0;  // 1 Blocca url non presenti nei domini monitorati
$option['ext_whois']='';          // 'http://www.yourwhois.com/adress?ip=%IP%'; %IP% verr� tradotto nell'IP numerico
$option['online_timeout']=5;      // in minuti, 0 per conteggio dinamico
$option['page_title']=1;          // 1 memorizza i titoli delle pagine
$option['refresh_page_title']=0;  // 1 Aggiorna i titoli delle pagine
$option['log_host']=0;            // 1 registra l'host tra i dettagli (WARNING:SLOW!)
$option['clear_cache']=0;         // 1 ricoscimento cache continuo (WARNING:SLOW!)
$option['full_recn']=0;           // 1 motori e refers riconosciuti ad ogni pagina visitata (WARNING:SLOW!)
$option['logerrors']=0;           // 1 registra gli errori nel file php-stats.log (deve avere i permessi in scrittura)
$option['check_new_version']=1;   // 1 Effettua verifica disponibilit� nuova versione php-stats
$option['www_trunc']=0;           // 1 Trasforma http://www. in http:// per evitare di avere la stessa pagina monitorata 2 volte
$option['accept_ssi']=0;          // 1 Accetta richiamo tramite SSI
$option['compatibility_mode']=0;  // 1 Invio Query al server con path completo del database (usare nel caso di problemi con la stringa di include)

$default_pages=array('/','/index.htm','/index.html','/default.htm','/index.php','/index.asp','/default.asp'); // Pagine di default del server, troncate considerate come la stessa

//////////////////////
// OPZIONI SPECIALI //
//////////////////////
            $option['ip-zone']=0;   // 1 usa il database degli IP per il riconoscimento dei paesi
                                    // DEVE ESSERE INSTALLATO A PARTE!
                                    // 2 usa il riconoscimento dei paesi da files.
                                    // DEVE ESSERE INSTALLATO A PARTE!
          $option['down_mode']=0;   // 0 (redirect), 1 (forza download file), 2 (forza download file altervista)
        $option['check_links']=1;   // 1 check link, 0 non check :)


/////////////////////////////////////////////////
// NON MODIFICARE NULLA DA QUESTO PUNTO IN POI //
/////////////////////////////////////////////////
if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
if(!isset($_SERVER)) $_SERVER=$HTTP_SERVER_VARS;

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' && substr($option['script_url'],0,5)==='http:') $option['script_url']='https:'.substr($option['script_url'],5);
if(substr($option['script_url'],-1)==='/') $option['script_url']=substr($option['script_url'],0,-1);
?>