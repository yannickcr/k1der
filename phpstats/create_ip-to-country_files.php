<?php
define('INDEXSTEP',100000);
define('IPIDX','ip-to-country.idx');
define('IPCSV','ip-to-country.csv');
define('IPDB','ip-to-country.db');

function codeInt($number){
	return pack('S',$number);
}

if(!file_exists(IPCSV)) die('ERRORE: Il file '.IPCSV.' non è stato trovato.');
if(file_exists(IPIDX)) die('ERRORE: Il file '.IPIDX.' è già stato trovato, eliminarlo per rigenerarlo.');
if(file_exists(IPDB)) die('ERRORE: Il file '.IPDB.' è già stato trovato, eliminarlo per rigenerarlo.');

$ipcsv=fopen(IPCSV,'r')
or die('ERRORE: Impossibile aprire '.IPCSV.' in lettura.');

$ipdb=fopen(IPDB,'w')
or die('ERRORE: Impossibile aprire '.IPDB.' in scrittura.');

$ipidx=fopen(IPIDX,'w')
or die('ERRORE: Impossibile aprire '.IPIDX.' in scrittura.');

$ipdbData='';
$count=1;//tutti gli indici precedenti al primo valore avranno indice zero
$curindex=-1;
$idxarray=Array();
while($tmp=fgetcsv($ipcsv,150,',')){
    $tmpstartindex=intval(($tmp[0]-0)/INDEXSTEP); //calcolo l'indice di start
    $tmpendindex=intval(($tmp[1]-0)/INDEXSTEP); //calcolo l'indice di end

    $ipdbData.=str_pad($tmp[0],10,'0',STR_PAD_LEFT).str_pad($tmp[1],10,'0',STR_PAD_LEFT).strtolower($tmp[2])."\n";//preparo il db

    if($tmpstartindex>$curindex || $tmpendindex>$curindex){
	    $idxarray['i'.$tmpendindex]=$count;
	    $curindex=$tmpendindex;
    }
    ++$count;
}
fclose($ipcsv);
fwrite($ipdb,$ipdbData);
fclose($ipdb);
unset($ipdbData);

$ipidxData='';
$lastcount=0;
for($i=0;$i<$count;++$i){
	if(isSet($idxarray['i'.$i])) $lastcount=$idxarray['i'.$i];
	$ipidxData.=codeInt($lastcount)."\n";
}
fwrite($ipidx,$ipidxData);
fclose($ipidx);

die('Creazione file completata');
?>