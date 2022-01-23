<?php
// SECURITY ISSUES
if(!defined('IN_PHPSTATS')) die('Php-Stats internal file.');

function report(){
        global $db,$option,$style,$varie,$modulo,$mese_oggi;

        include("lang/$option[language]/domains_lang.php");
        include("lang/$option[language]/main_lang.inc");
        include('inc/admin_func.inc.php');

        // PREPARO LA MAIL
        $site=explode("\n",$option['server_url']);
        $site_url=$site[0];
        $mail_soggetto="Report settimanale statistiche su $site_url";
        $intestazioni=
        "From: Php-Stats at $site[0]<$option[user_mail]>\n".
        "X-Sender: <$option[user_mail]>\n".
        "X-Mailer: PHP-STATS\n". // mailer
        "X-Priority: 1\n". // Messaggio urgente!
        "Return-Path: <$option[user_mail]>\n";  // Indirizzo di ritorno per errori
        $ver=$option['phpstats_ver'];

        // VISITATORI E VISITE TOTALI
        $hits_this_week=$visite_this_week=0;
        // Contatori
        $result=sql_query("SELECT * FROM $option[prefix]_counters");
        list($hits,$visits)=@mysql_fetch_row($result);
        $hits_totali=$hits+$option['starthits'];
        $visite_totali=$visits+$option['startvisits'];

        list($date_G,$date_i,$date_m,$date_d,$date_Y,$date_w)=explode('-',date('G-i-m-d-Y-w'));

        // VISITARORI E PAGINE VISITATE NEGLI ULTIMI 7 GIORNI IN DETTAGLIO
        $dettagli="\n";
        for($i=0;$i<=7;++$i){
                $max=$lista_accessi[$i]=$lista_visite[$i]=0;
                $giorno=date('Y-m-d',mktime($date_G-$option['timezone'],$date_i,0,$date_m,$date_d-$i-1,$date_Y));
                $lista_giorni[$i]=$giorno;
                $result=sql_query("SELECT * FROM $option[prefix]_daily where data='$giorno'");
                while($row=@mysql_fetch_array($result,MYSQL_ASSOC)){
                        $lista_accessi[$i]=$row['hits']-$row['no_count_hits'];
                        $lista_visite[$i]=$row['visits']-$row['no_count_visits'];
                        $hits_this_week+=$row['hits']-$row['no_count_hits'];
                        $visite_this_week+=$row['visits']-$row['no_count_visits'];
                        if(($row['hits']-$row['no_count_hits'])>$max) $max=$row['hits']-$row['no_count_hits'];
                }
        }
        for($i=0;$i<=7;++$i){
                $data=explode('-',$lista_giorni[$i]);
                $giorno=str_replace(Array('%mount%','%day%','%year%'),Array(formatmount($data[1]),$data[2],$data[0]),$varie['date_format']);
                $dettagli.=
                str_pad($giorno,35,' ',STR_PAD_LEFT).
                str_pad($lista_accessi[$i],15,' ',STR_PAD_LEFT).
                str_pad($lista_visite[$i],15,' ',STR_PAD_LEFT).
                "\n";
        }

        // REFERER (TOP 25)
        $site_referers='';
        if($modulo[4]==2) $result=sql_query("SELECT data,visits FROM $option[prefix]_referer WHERE mese='$mese_oggi' ORDER BY visits DESC LIMIT 25");
        else $result=sql_query("SELECT data,visits FROM $option[prefix]_referer ORDER BY visits DESC LIMIT 25");
        while($row=@mysql_fetch_array($result,MYSQL_ASSOC)){
                $site_referers.=$row['data'].' ('.$row['visits'].")\n";
        }

        // MOTORI DI RICERCA (TOP 25)
        $site_engines='';
        if($modulo[4]==2) $result=sql_query("SELECT data,engine,domain,visits FROM $option[prefix]_query WHERE mese='$mese_oggi' ORDER BY visits DESC LIMIT 25");
        else $result=sql_query("SELECT data,engine,domain,visits FROM $option[prefix]_query ORDER BY visits DESC LIMIT 25");
        while($row=@mysql_fetch_array($result,MYSQL_ASSOC)){
                $site_engines.=$row['data'].' ('.$row['visits'].', '.$row['engine'].' '.$domain_name[$row['domain']].")\n";
        }

        // COMPILO IL TEMPLATE
        eval("\$mail_messaggio=\"".gettemplate("lang/$option[language]/report_weekly.tpl")."\";");

        // SPEDISCO LA MAIL
        $ok=FALSE;
        $ok=@mail($option['user_mail'],$mail_soggetto,$mail_messaggio,$intestazioni);
        // Alcuni server mail non accettano le intestazioni, provo a spedire senza intestazioni se l'invio è fallito
        if($ok==FALSE) $ok=@mail($option['user_mail'],$mail_soggetto,$mail_messaggio);
        if($ok==TRUE){
                // SE L'INVIO E' OK PROGRAMMO IL DATABASE PER IL PROSSIMO INVIO
                $next=mktime(0,0,0,$date_m,$date_d-$date_w+$option['report_w_day']+7,$date_Y);
                $oggi=time()-($option['timezone']*3600);
                if($next-$oggi>604800) $next=$next-604800;
                sql_query("UPDATE $option[prefix]_config SET value='$next' WHERE name='instat_report_w'");
        }
        else logerrors('Weekly Report|'.date('d/m/Y H:i:s').'|FAILED');
}
?>