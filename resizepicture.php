<?

////////////////////////////////////////////////
// Coded by mmaxuel for                      //
// http://xportal.free.fr/imode.php          //
//                                          //
// le site imode doivent avoir des images    //
// deja reduite, en effet, meme en utlisant  //
// les balises <img src="..." width="120">  //
// l'image lorsqu'elle est lu par le            //
// navigateur i-mode il verifie la taille    //
// et il la refuse si elle est trop grande  //
// d'ou l'utilité de reduire l'image avant  //
// de l'envoyer...                          //
// voila!                                    //
// pour toute question:                      //
//          rmoummed @ hotmail . com        //
//                                          //
//                                          //
//                                          //
// exemple d'utilisation:                    //
// vous desirez reduire l'image Bateau.jpg  //
// avec une largeur de 120, la syntaxe sera  //
// resizepicture.php?img=down/wall/korner-wall.jpg&img_x=120 //
///////////////////////////////////////////////

//on enverra au navigateur un fichier de type image au format jpeg:
    header("Content-type: image/jpeg");

//on charge l'image $img dans $img_big:
    $img_big = imagecreatefromjpeg($img);
//on demande les dimension de l'image $img:
    $size = getimagesize($img);
    
$img_y = floor($size[1] * $img_x / $size[0]);

//on fait une nouvelle image ayant pour dimension: largeur=img_x et hauteur=img_y :
$img_mini = imagecreatetruecolor($img_x, $img_y); 

//on copie l'image d'origine contenu dans img_big dans img_mini en la reduisant a $img_x pour la largeur et $img_y pour la hauteur:
imagecopyresized($img_mini,$img_big,0,0,0,0,$img_x,$img_y,$size[0],$size[1]);

//on envoie l'image reduire au navigateur:
imagejpeg($img_mini,'',100);

?>