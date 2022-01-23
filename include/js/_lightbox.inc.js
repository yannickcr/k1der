var Lightbox = function(options)
{
this.init(options);
}
Lightbox.prototype = {
init: function(options)
{
this.opt = Object.extend({
loadImage : 'loading.gif',
closeButton : 'close.gif',
targets: '.lightbox',
id: 'lightbox',
opacity : 0.8,
speed: 20,
step: 0.2
},options);
var objOverlay=domEl('div','',{id:this.opt.id+'Overlay'})
objOverlay.style.display='none';
objOverlay.style.zIndex=90;
objOverlay.onclick=(function(e) {
this._hideLightbox();
return false;
}).bind(this);
document.body.appendChild(objOverlay);
var imgPreloader = new Image();
imgPreloader.src = this.opt.loadImage;
imgPreloader.onload = (function(e) {
var objLoadingImage = domEl('img','',{src:imgPreloader.src,id:this.opt.id+'loadImg'});
objLoadingImage.style.position='absolute';
objLoadingImage.style.zIndex=100;
objLoadingImage.style.width=imgPreloader.width+'px';
var objLoadingImageLink = domEl('a',[objLoadingImage],{href:'#',id:this.opt.id+'loadImgLink'});
objLoadingImageLink.style.display='none';
objLoadingImageLink.onclick=(function(e) {
this._hideLightbox();
return false;
}).bind(this);
document.body.appendChild(objLoadingImageLink);
imgPreloader.onload=function(){};
return false;
}).bind(this);
var objImage = domEl('img','',{id:this.opt.id+'Image'});
var objLink = domEl('a',[objImage],{href:'#',title:'Cliquez pour fermer',id:'link'});
var objCaption = domEl('div','',{id:this.opt.id+'Caption'});
objCaption.style.display='none';
var objLightboxDetails = domEl('div',[objCaption],{id:this.opt.id+'Details'});
var objLightbox = domEl('div',[objLink,objLightboxDetails],{id:this.opt.id});
objLightbox.style.display='none';
objLightbox.style.position='absolute';
objLightbox.style.zIndex=150;
insertAfter(objLightbox, $(this.opt.id+'Overlay'));
$('link').onclick=(function(e) {
this._hideLightbox();
return false;
}).bind(this);
var imgPreloadCloseButton = new Image();
imgPreloadCloseButton.onload=(function(e) {
var objCloseButton = domEl('img','',{src:this.opt.closeButton,id:this.opt.id+'CloseButton'});
objCloseButton.style.position='absolute';
objCloseButton.style.zIndex=200;
$('link').appendChild(objCloseButton);
return false;
}).bind(this);
imgPreloadCloseButton.src = this.opt.closeButton;
this._setActions();
},
_setActions : function()
{
var liens=$$('a'+this.opt.targets);
for(var i=0;i<liens.length;i++) {
liens[i].onclick=(function(e) {
var object=getTarget(e?e:event,'A');
this._showLightbox(object);
object.blur();
return false;
}).bind(this);
}
},
_showLightbox : function(objLink)
{
if ($(this.opt.id+'loadImg')) {
$(this.opt.id+'loadImgLink').style.display='block';
$(this.opt.id+'loadImg').style.top=getTopPosition($(this.opt.id+'loadImg')) + 'px';
$(this.opt.id+'loadImg').style.left=(getPageWidth() - $(this.opt.id+'loadImg').width)/2 + 'px';
}
$(this.opt.id+'Overlay').style.height=getPageHeight() + 'px';
$(this.opt.id+'Overlay').style.width=getPageWidth() + 'px';
$(this.opt.id+'Overlay').style.opacity=0;
if (document.all) $(this.opt.id+'Overlay').style.filter='alpha(opacity=0)';
$(this.opt.id+'Overlay').style.display='block';
fx.fade('in',$(this.opt.id+'Overlay'),this.opt.opacity,this.opt.speed,this.opt.step);
imgPreload = new Image();
imgPreload.onload=(function(e) {
$(this.opt.id+'Image').src=objLink.href;
var lightboxTop = getTopPosition(imgPreload,'height');
var lightboxLeft = (getPageWidth() - imgPreload.width)/2;
$(this.opt.id).style.top=(lightboxTop < 0) ? "0px" : lightboxTop + "px";
$(this.opt.id).style.left=(lightboxLeft < 0) ? "0px" : lightboxLeft + "px";
$(this.opt.id+'Details').style.width=imgPreload.width + 'px';
if(objLink.title){
$(this.opt.id+'Caption').style.display='block';
$(this.opt.id+'Caption').innerHTML=objLink.getAttribute('title');
} else {
$(this.opt.id+'Caption').style.display='none';
}
if(IE) {
var selects = $$('select');
for (var i=0;i!=selects.length;i++) selects[i].style.visibility = 'hidden';
}
$(this.opt.id).style.display='block';	
return false;
}).bind(this);

imgPreload.src = objLink.href;
},
_hideLightbox : function()
{
fx.fade('out',$(this.opt.id+'Overlay'),0,this.opt.speed,this.opt.step,function() {
$(this.opt.id+'Overlay').style.display='none';
}.bind(this));
if($(this.opt.id+'loadImgLink')) $(this.opt.id+'loadImgLink').style.display='none';
$(this.opt.id).style.display='none';
if(IE) {
var selects = $$('select');
for (var i=0;i!=selects.length;i++) selects[i].style.visibility = '';
}
}
}