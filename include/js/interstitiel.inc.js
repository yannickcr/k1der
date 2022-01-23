// coding:utf-8
/*
 * Class: Interstitiel
 * Effet d'intersticiel
 * 
 * Paramètres:
 * 
 *    options - Tableau d'options
 *    
 * Requière :
 * 
 *    lib/const.js
 *    lib/css.js
 *    lib/dom.js
 *    lib/events.js
 *    lib/prototype.js
 *    lib/effects.js
 *    utils.js
 * 
 */
//# Ce code est sous license Creative Commons : http://creativecommons.org/licenses/by-sa/2.0/fr/
//# Auteurs : e-TF1 (2006) - Yannick Croissant (2006)
var Interstitiel = function(options)
{
	this.init(options);
}
Interstitiel.prototype = {
	/*
	 * Constructor: init
	 * Constructeur de la la class Interstitiel
	 * 
	 * Paramètres:
	 * 
	 *    options - Tableau d'options
	 */
	init: function(options)
	{
		// Initialise les options
		this.opt = Object.extend({
			targets: '.interstitiel',
			overlay: 'overlay',
			classShow: 'zoneShow',
			classHide: 'zoneHide',
			classClose: 'close',
			opacity : 0.8,
			speed: 20,
			step: 0.2
		},options);
		
		// Overlay
		domEl('div','',{id:this.opt.overlay},document.body);
		this.overlay = $(this.opt.overlay);
		this.overlay.style.display = 'none';
		this.overlay.style.opacity = 0;
		this.overlay.style.filter = 'alpha(opacity=0)';
		
		// Ajoute l'évènement sur l'overlay : disparition
		this.overlay.onclick=(function(e) {
			this._hideInterstitiel(); // Cache l'overlay
		}).bind(this);

		// Ajoute l'évènement sur les liens : affichage
		var targets = $$(this.opt.targets);
		for (i = 0; i < targets.length; i++ ) {
			id=targets[i].href.split('#')[targets[i].href.split('#').length-1];
			this.zone=$(id);							// Récupère la zone cible
			if(this.zone) {				
				this.zone.className=this.opt.classHide;	// Cache la zone au chargement de la page
				
				// Ajout du lien de fermeture de la zone (si besoin)
				var closeLinks = $$('#'+id+' .'+this.opt.classClose);
				if(closeLinks.length==0) domEl('a','Fermer',{className:'close',href:'#'},this.zone);
				var closeLinks = $$('#'+id+' .'+this.opt.classClose);
				for (j = 0; j < closeLinks.length; j++ ) {
					closeLinks[0].onclick=(function(e) {
						this._hideInterstitiel();		// Cache l'overlay
						return false;
					}).bind(this);
				}
				
				// Ajout de l'événement sur les liens
				targets[i].onclick=(function(e) {
					getTarget(e?e:event,'A').blur();	// Supprime le focus du lien
					this._showInterstitiel();			// Affiche l'overlay
					return false;
				}).bind(this);
			}
		}
	},

	/*
	 * Method: _showInterstitiel
	 * Affiche l'interstitiel
	 */
	_showInterstitiel: function(d)
	{
		if(IE) {
			var selects = $$('select');
	      		for (var i=0;i!=selects.length;i++) selects[i].style.visibility = 'hidden';
		}
		this.overlay.style.width = getPageWidth()+'px';
		this.overlay.style.height = getPageHeight()+'px';	
		this.overlay.style.display = 'block';			// Affiche l'overlay
		fx.fade('in',this.overlay,this.opt.opacity,this.opt.speed,this.opt.step,function() {
			this.zone.style.visibility='hidden';
			this.zone.className=this.opt.classShow;		// Affiche la zone
			this.zone.style.left=(getPageWidth()-this.zone.offsetWidth)/2+'px';
			this.zone.style.top=getTopPosition(this.zone)+'px';
			this.zone.style.visibility='';
		}.bind(this));		
	},
	
	/*
	 * Method: _hideInterstitiel
	 * Cache l'interstitiel
	 */
	_hideInterstitiel: function()
	{
		this.zone.className=this.opt.classHide;			// Cache la zone
		fx.fade('out',this.overlay,0,this.opt.speed,this.opt.step,function() {
			if(IE) {
				var selects = $$('select');
		    	for (var i=0;i!=selects.length;i++) selects[i].style.visibility = '';
			}
			this.overlay.style.display = 'none';		// Cache l'overlay
		}.bind(this));
	}
}
