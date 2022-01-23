/*
Plugin Name: WP-Amazon
Version: 2.1
Plugin URI: http://manalang.com/wp-amazon/
Description: WP-Amazon adds the ability to search and include items from Amazon to your entries.
Author: Rich Manalang
Author URI: http://groups.google.com/group/wp-amazon

WP-Amazon Plugin for Wordpress 2.3+
Copyright (C) 2005-2007 Rich Manalang
Version 2.0  $Rev: 7794 $ $Date: 2007-02-03 20:58:29 -0800 (Sat, 03 Feb 2007) $

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
USA
*/

if (!wpa2AssociatesId) var wpa2AssociatesId = 'manalangcom-20';
if (!wpa2CountryTLD) var wpa2CountryTLD = 'com';	
var wpa2 = function() {
	var amazonRS = {};
	var pub = {};
	
	// base Amazon ECS REST endpoint
	var baseUrl =  'http://ecs.amazonaws.' + wpa2CountryTLD + '/onca/xml'
		+ '?Service=AWSECommerceService'
		+ '&AWSAccessKeyId=1N9AHEAQ2F6SVD97BE02'
		+ '&Operation=ItemSearch'
		+ '&SearchIndex=Blended'
		+ '&ContentType=text/javascript'
		+ '&XMLEscaping=Double'
		+ '&Version=2007-10-19'
		+ '&ResponseGroup=Small,Images,OfferSummary'
		+ '&Style=http://manalang.com/wp-amazon2/xsl/amazon.xsl'
		+ '&Callback=wpa2().process'
		+ '&Keywords=';
	
	// find objects in array by name
	function fo(a,n) {
		if (!a) return '';
		if (a[n]) return a[n];
		for (var i in a) if (a[i][n]) return a[i][n];
	}
	
	// sort by relevance
	function sbr(a,b) {
		return fo(a.SearchIndex,'RelevanceRank')
			- fo(b.SearchIndex,'RelevanceRank');
	}
	
	function findItem(ia,asin) {
		var i;
		while (i = ia.shift()) {
			if (i.Item.ASIN == asin) return i.Item;
		}
	}
	
	function getProductDescription(er) {
		// stop if no product descriptions avail
		if (!er.EditorialReviews) return '';
		
		var html = ' ';
		var rev;
		jQuery.each(er.EditorialReviews, function(i,n) {
			if (n.EditorialReview) rev = n.EditorialReview;
			else rev = n;
			if (rev.Source && rev.Content) 
				html += '<h5>' + rev.Source + '</h5>'
					+ '<p class="wpa-descr">' + rev.Content.replace(/(<([^>]+)>)/ig,''); + '</p>';
		});
		return '<div class="wpa-descrs">' + html + '</div>';
	}
	
	function getImage(i) {
		var simg = fo(i, 'SmallImage');
		var mimg = fo(i, 'MediumImage');
		var limg = fo(i, 'LargeImage');
		var oimg = '';
		if (mimg || limg) {
			oimg = '<p class="wpa-prod-imgs-other">Other sizes: ';
			if (mimg)
				oimg += '<a class="wpa-prod-img-other-med wpa-prod-img-other" href="' 
					+ mimg.URL + '" wpawidth="' + mimg.Width.content 
					+ '" wpaurl="' + i.DetailPageURL + '">M</a>';
			if (limg) {
				if (mimg) oimg += ' | ';
				oimg += '<a class="wpa-prod-img-other-lrg wpa-prod-img-other" href="' 
					+ limg.URL + '" wpawidth="' + limg.Width.content 
					+ '" wpaurl="' + i.DetailPageURL + '">L</a>'
			}
			oimg += '</p>';
		}
		if (simg) 
			return '<a href="' + i.DetailPageURL + '" target="_blank">'
				+ '<img src="' + simg.URL 
				+ '" alt="' + fo(i.ItemAttributes,'Title')
				+ '" height="' + simg.Height.content + 'px"/></a>' + oimg;
		else return 'Image not available';
	}
	
	function getPrice(i) {
		var os = fo(i,'OfferSummary');
		var html = '';
		if (os.LowestNewPrice) {
			html = '<p><strong>Price:</strong> ' + os.LowestNewPrice.FormattedPrice + ' ' + os.LowestNewPrice.CurrencyCode
				+ '</p>';
		}
		if (os.LowestUsedPrice) {
			html += '<p><strong>Lowest used price:</strong> ' 
				+ os.LowestUsedPrice.FormattedPrice + ' ' + os.LowestUsedPrice.CurrencyCode
				+ '</p>';
		}
		return html;
	}
	
	function getItems(si) {
		var asinArry, itemsArry, itemArry, asin;
		var html = '';
		asinArry = jQuery.grep(si, function(i) { if (i.ASIN) return true });
		itemsArry = jQuery.grep(amazonRS.ItemSearchResponse.Items, function(i) { if (i.Item) return true });
		jQuery.each(asinArry, function(i,n) {
			asin = fo(n,'ASIN');
			itemArry = findItem(itemsArry,asin);
			if (itemArry) {
				html += '<tr>'
					+ '<td class="wpa-prod-img">' + getImage(itemArry) + '</td>'
					+ '<td class="wpa-prod-title" ><a href="' + itemArry.DetailPageURL + '" target="_blank">' + fo(itemArry.ItemAttributes,'Title') + '</a>'
					+ getPrice(itemArry) + '</td>'
					+ getProductDescription(itemArry)
				+ '</tr>';
			}
		});
		if (html != '') return '<table class="wpa-prod">' + html + '</table>';
		else return;
	}
	
	// public methods
	pub.process = function(rs) {
		amazonRS = rs;
		jQuery('#wpa-blended').remove();
		jQuery('#wpa-prod-img-preview').remove();
		var rslt = '';
		var items = '';
		var srchRsltMap = fo(rs.ItemSearchResponse.Items,'SearchResultsMap');
		try { srchRsltMap.sort(sbr) } catch (e) {}
		for (var i in srchRsltMap) {
			// only one category returned
			if (i == 'SearchIndex') var srchIdx = srchRsltMap[i];
			// multiple categories returned
			else var srchIdx = fo(srchRsltMap[i],'SearchIndex');
			if (srchIdx) {
				var items = getItems(srchIdx);
				if (items)
					rslt += '<dt>' + fo(srchIdx,'IndexName') 
						+ ' (' + fo(srchIdx,'Results') + ')'
						+ '</dt><dd>' + items + '</dd>';
			};
		};
		var rslt = '<dl id="wpa-blended">' + rslt + '</dl>';
		jQuery(rslt).appendTo('#wpa').Accordion({
			headerSelector: 'dt',
			panelSelector: 'dd',
			activeClass: 'wpa-accordian-active',
			hoverClass: 'wpa-accordian-hover',
			panelHeight: 274,
			speed: 300
		});
		jQuery('#wpa dt').css('cursor','default');
		jQuery('.wpa-prod tr:nth-child(odd)').css("background",'#E0E9EF');
		var navTimer = null;
		jQuery('.wpa-prod-img-other').click(
			function() {
				jQuery('#wpa-prod-img-preview').remove();
				var top = jQuery(this).offset().top + jQuery(this).height();
				var left = jQuery(this).offset().left - ((parseInt(jQuery(this).attr('wpawidth'))+12)/2);
				if (jQuery(this).offset().left + ((parseInt(jQuery(this).attr('wpawidth'))+12)/2) + 30 > 
					jQuery(window).width())
					var hOffset = 'right:5';
				else
					var hOffset = 'left:' + left;
				jQuery('<div id="wpa-prod-img-preview" style="top:' + top + 'px;' + hOffset + 'px">'
					+ '<img id="wpa-preview-close" src="../wp-content/plugins/wp-amazon/images/close.gif" alt="Close preview"/>'
					+ '<div><a href="' + jQuery(this).attr('wpaurl') + '" title="Click and drag this image to the post editor">'
					+ '<img src="' + jQuery(this).attr('href') + '" width="' + jQuery(this).attr('wpawidth') + 'px" />'
					+ '</a></div></div>').appendTo(document.body);
				jQuery('#wpa-preview-close').click(function() { jQuery('#wpa-prod-img-preview').remove() });
				return false;
			}
		);
		jQuery('.wpa-loading').remove();
	};
	
	pub.exec = function() {
		var q = jQuery('#wpa-q').val();
		if (!q) return false;
		if (wpa2AssociatesId) var assocTag = '&AssociateTag=' + wpa2AssociatesId;
		else var assocTag = '';
		var url = baseUrl + q + assocTag + '&noCacheIE=' + (new Date()).getTime();
		jQuery('<div id="wpa-loading" class="wpa-loading">Loading...<div>').appendTo(document.body);
		jQuery('#wpa-script').remove();

		var scriptObj = document.createElement("script");
	    scriptObj.setAttribute("type", "text/javascript");
	    scriptObj.setAttribute("charset", "utf-8");
	    scriptObj.setAttribute("src", url);
	    scriptObj.setAttribute("id", "wpa-script");
		document.getElementsByTagName("head").item(0).appendChild(scriptObj);
		return false;
	};
	return pub;
};
jQuery(function() {
	jQuery('<div id="wpa-container"><div id="wpa">'
		+ '<form>'
			+ '<input id="wpa-q" name="q" type="text" />'
			+ '<input id="wpa-go" name="go" type="button" value="Search" />'
		+ '</form>'
	+ '</div>'
	+ '<a href="#" id="wpa-toggle" title="Search Amazon" class="wpa-open"></a></div>').appendTo(document.body);
	jQuery('#wpa-toggle').toggle(
		function() {
			jQuery('#wpa').css('width', jQuery(document).width() - 	jQuery('#moremeta').offset().left);
			jQuery('#wpa').animate({opacity: 'toggle'},'normal');
			jQuery(this).removeClass('wpa-open').addClass('wpa-close');
			jQuery('#wpa-q')[0].focus();
		},
		function() {
			jQuery('#wpa-prod-img-preview').remove();
			jQuery('#wpa').animate({opacity: 'toggle'},'normal');
			jQuery(this).removeClass('wpa-close').addClass('wpa-open');
		}
	);
	jQuery('#wpa-container').css('top',jQuery('#submenu').offset().top + jQuery('#submenu').height() + 4)
		.css('height',jQuery(document).height() - (jQuery('#submenu').offset().top + jQuery('#submenu').height()));
	jQuery(window).bind('resize',function() {
		jQuery('#wpa').css('width', jQuery(document).width() - 	jQuery('#moremeta').offset().left);
		jQuery('#wpa-container').css('top',jQuery('#submenu').offset().top + jQuery('#submenu').height() + 4)
			.css('height',jQuery(document).height() - (jQuery('#submenu').offset().top + jQuery('#submenu').height()));
	});
	jQuery('#wpa-go').bind('click',wpa2().exec);
	jQuery('#wpa form').bind('submit',wpa2().exec);
})
