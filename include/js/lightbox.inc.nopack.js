// coding:utf-8
/*
 * Class: Lightbox
 * Effet lightbox
 * 
 * Paramètres:
 * 
 *    options - Tableau d'options
 *    
 */
var Lightbox = function(options)
{
	this.init(options);
}
Lightbox.prototype = {
	/*
	 * Constructor: init
	 * Constructeur de la la class Lightbox
	 * 
	 * Paramètres:
	 * 
	 *    options - Tableau d'options
	 */	
	init: function(options)
	{
		// Initialise les options
		this.opt = Object.extend({
			loadImage : 'loading.gif',
			closeButton : 'close.gif',
			targets: '.lightbox',
			id: 'lightbox',
			opacity : 0.8,
			speed: 20,
			step: 0.2
		},options);
		
		// Construction de la lightbox
		var objOverlay=domEl('div','',{id:this.opt.id+'Overlay'})
		objOverlay.style.display='none';
		objOverlay.style.zIndex=90;
		objOverlay.onclick=(function(e) {
			this._hideLightbox();
			return false;
		}).bind(this);

		document.body.appendChild(objOverlay);
	
		// Précharge et créer l'image de chargement
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

			imgPreloader.onload=function(){};	//	Corrige un bug IE (arrêt des gifs animés)

			return false;
		}).bind(this);
	
		// Créer l'image
		var objImage = domEl('img','',{id:this.opt.id+'Image'});
		// Créer le lien
		var objLink = domEl('a',[objImage],{href:'#',title:'Cliquez pour fermer',id:'link'});
		// Créer la légende
		var objCaption = domEl('div','',{id:this.opt.id+'Caption'});
		objCaption.style.display='none';
		// Créer le div Details
		var objLightboxDetails = domEl('div',[objCaption],{id:this.opt.id+'Details'});
		// Créer la lightbox
		var objLightbox = domEl('div',[objLink,objLightboxDetails],{id:this.opt.id});
		objLightbox.style.display='none';
		objLightbox.style.position='absolute';
		objLightbox.style.zIndex=150;
		insertAfter(objLightbox, $(this.opt.id+'Overlay'));

		$('link').onclick=(function(e) {
			this._hideLightbox();
			return false;
		}).bind(this);

		// Précharge et créer l'image de fermeture
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
	
	/*
	 * Method: _setActions
	 * Assigne les actions aux liens de la page
	 */	
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
	
	/*
	 * Method: _showLightbox
	 * Affiche l'effet lightbox
	 */	
	_showLightbox : function(objLink)
	{
		// Centre l'image de chargement
		if ($(this.opt.id+'loadImg')) {
			$(this.opt.id+'loadImgLink').style.display='block';
			$(this.opt.id+'loadImg').style.top=getTopPosition($(this.opt.id+'loadImg')) + 'px';
			$(this.opt.id+'loadImg').style.left=(getPageWidth() - $(this.opt.id+'loadImg').width)/2 + 'px';
		}
	
		// Calcul la taille de l'overlay
		$(this.opt.id+'Overlay').style.height=getPageHeight() + 'px';
		$(this.opt.id+'Overlay').style.width=getPageWidth() + 'px';
		$(this.opt.id+'Overlay').style.opacity=0;
		if (document.all) $(this.opt.id+'Overlay').style.filter='alpha(opacity=0)';
		$(this.opt.id+'Overlay').style.display='block';
		
		fx.fade('in',$(this.opt.id+'Overlay'),this.opt.opacity,this.opt.speed,this.opt.step);
		
		// Précharge l'image
		imgPreload = new Image();
	
		imgPreload.onload=(function(e) {
			$(this.opt.id+'Image').src=objLink.href;
	
			// Centre la lightbox
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
			
			// Cache les <select> pour IE
			if(IE) {
				var selects = $$('select');
	       		for (var i=0;i!=selects.length;i++) selects[i].style.visibility = 'hidden';
			}
			
			$(this.opt.id).style.display='block';	
			return false;
		}).bind(this);
		
		imgPreload.src = objLink.href;
	},
	
	/*
	 * Method: _hideLightbox
	 * Cache l'effet lightbox
	 */	
	_hideLightbox : function()
	{
		// Cache la lightbox et l'overlay
		fx.fade('out',$(this.opt.id+'Overlay'),0,this.opt.speed,this.opt.step,function() {
			$(this.opt.id+'Overlay').style.display='none';
		}.bind(this));
		if($(this.opt.id+'loadImgLink')) $(this.opt.id+'loadImgLink').style.display='none';
		$(this.opt.id).style.display='none';
	
		// Affiche les <select> pour IE
		if(IE) {
			var selects = $$('select');
	    	for (var i=0;i!=selects.length;i++) selects[i].style.visibility = '';
		}
	}
}