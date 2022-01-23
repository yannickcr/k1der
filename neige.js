  var isNS = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 4);
  var div1 = (isNS) ? document.obj1 : document.all.obj1.style;
  var div2 = (isNS) ? document.obj2 : document.all.obj2.style;
  var div3 = (isNS) ? document.obj3 : document.all.obj3.style;
  var div4 = (isNS) ? document.obj4 : document.all.obj4.style;
  var div5 = (isNS) ? document.obj5 : document.all.obj5.style;
  var div6 = (isNS) ? document.obj6 : document.all.obj6.style;
  var div7 = (isNS) ? document.obj7 : document.all.obj7.style;
  var div8 = (isNS) ? document.obj8 : document.all.obj8.style;
  var div9 = (isNS) ? document.obj9 : document.all.obj9.style;
  var div10 = (isNS) ? document.obj10 : document.all.obj10.style;
  var objet;
  var coord;var speed;var temx;var decx;
  var coordb = 800;
  objet = new Array(div1,div2,div3,div4,div5,div6,div7,div8,div9,div10);
  coord = new Array();speed = new Array();temx=new Array();decx=new Array();
  if (navigator.appName=="Microsoft Internet Explorer") {
  tailley = document.body.clientHeight;taillex = document.body.clientWidth;offsety = document.body.scrollTop;offsetx = document.body.scrollLeft;}
  else {
  tailley = window.innerHeight;taillex = window.innerWidth;offsety = window.pageYOffset;offsetx = window.pageXOffset;}
 
  coord[0]=((Math.random()*taillex)-160)+80;coord[1]=0;
  coord[2]=((Math.random()*taillex)-160)+80;coord[3]=(tailley/2);
  coord[4]=((Math.random()*taillex)-160)+80;coord[5]=0;
  coord[6]=((Math.random()*taillex)-160)+80;coord[7]=(tailley/2);
  coord[8]=((Math.random()*taillex)-160)+80;coord[9]=0;
  coord[10]=((Math.random()*taillex)-160)+80;coord[11]=(tailley/2);

  coord[12]=((Math.random()*taillex)-160)+80;coord[13]=0;
  coord[14]=((Math.random()*taillex)-160)+80;coord[15]=(tailley/2);
  coord[16]=((Math.random()*taillex)-160)+80;coord[17]=0;
  coord[18]=((Math.random()*taillex)-160)+80;coord[19]=(tailley/2);
  coord[20]=((Math.random()*taillex)-160)+80;coord[21]=0;
  coord[22]=((Math.random()*taillex)-160)+80;coord[23]=(tailley/2);
  coord[24]=((Math.random()*taillex)-160)+80;coord[25]=0;
  coord[26]=((Math.random()*taillex)-160)+80;coord[27]=(tailley/2);

  speed[0]=0.5;speed[1]=0.5;speed[2]=1;speed[3]=1;speed[4]=3;speed[5]=3;speed[6]=2;speed[7]=2;speed[8]=2;speed[9]=2;

  for (var i = 0; i < 10; i++) {
   temx[i]=(Math.random()*19);
   decx[i]=0;
  }

  function placeObj(i,px,py) {
    objet[i].left=px;
    objet[i].top=py;
  }

function neige() {
 if (navigator.appName=="Microsoft Internet Explorer") {
  tailley = document.body.clientHeight;taillex = document.body.clientWidth;offsety = document.body.scrollTop;offsetx = document.body.scrollLeft;}
 else {
  tailley = window.innerHeight;taillex = window.innerWidth;offsety = window.pageYOffset;offsetx = window.pageXOffset;}
 var off=0;
 for (var i = 0; i < 10; i++) {
  off=(i*2)
  placeObj(i,coord[off],coord[off+1]);
  temx[i]+=1
  if (temx[i]>20) {
    decx[i]=1-(Math.random()*2);
    temx[i]=0;
  }
  coord[off]+=decx[i];
  coord[off+1]+=speed[i];
  maxi=tailley+offsety;
  if (coord[off+1]>maxi) {coord[off]=((Math.random()*taillex)-160)+80;coord[off+1]=-100;}
 }
 tempo = setTimeout("neige()", 15);
}

neige();