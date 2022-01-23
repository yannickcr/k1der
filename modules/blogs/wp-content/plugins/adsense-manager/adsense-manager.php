<?php
/*
Plugin Name: AdSense Manager
PLugin URI: http://wordpress.org/extend/plugins/adsense-manager/
Description: Control and arrange your AdSense & Referral blocks on your Wordpress blog. With Widget and inline post support, configurable colours. 
Author: Martin Fitzpatrick
Version: 2.4
Author URI: http://www.mutube.com/
*/

/*
TODO:

Defaults
* highlight the fields for which there are default settings
* is this making code overcomplicated? rearrange into functions/etc.

*/

@define("ADSENSEM_VERSION", "2.3");
@define('ADSENSEM_DIRPATH','/wp-content/plugins' . strrchr(dirname(__FILE__),'/') . "/");

/*
	CONSTANTS FOR CONFIGURATION
*/

//Currently not used
@define("ADSENSEM_MAX_ADS", 7); //Max Google Ad units
@define("ADSENSEM_MAX_REFERRALS", 7); //Max Google Referral units

/*
AdSense Manager is a plugin to help manage generating income from your Wordpress blogs. Because it's a money-generating plugin it would be nice (for me) if some of that income found
it's way back to supporting the development and hosting of my free plugins.

By default AdSense Manager will donate the equivalent of 1 days worth of Google Ads to
mutube.com each month. This money is used to keep servers up and provide ongoing free support.

However, if you don't want to donate this income you can change the value below to 0 to disable this behaviour, no hard feelings.  Please consider donating instead.

Thanks, 

Martin Fitzpatrick
Developer, mutube.com

*/

/*  Copyright 2006  MARTIN FITZPATRICK  (email : martin.fitzpatrick@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/*

   STANDARD OUTPUT FUNCTIONS
   These are out of the main function block below so they can be called
   from outside "widget-space".  This means we can re-use code for widget
   and non-widget versions

*/

//Kept external for backward compatibility
if(!function_exists('adsensem_status')) {

function adsensem_ad($name=false) {

	global $adsensem;

	$options = get_option('plugin_adsensem');
	if($name===false)
		{$ad=$options['ads'][$options['defaults']['ad']];}	
	else
		{$ad=$options['ads'][$name];}

	echo $adsensem->get_ad($ad,$name);
}

}



/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class adsensem {

	function get_ad_inline($ad=false,$name /* passthru */) {

		$options = get_option('plugin_adsensem');

		if($ad===false){$ad=$options['ads'][$options['defaults']['ad']];}
		$ad=$this->merge_defaults($ad); //Apply defaults

		$code='';
			/* This test block determines whether to display the Ad in the current location.
				The home,single,archive,search tests only apply to Ads displayed inline, 
				so we test for that here */
			if(	(($ad['show-home']=='yes') && is_home()) ||
				(($ad['show-post']=='yes') && (is_single() || is_page())) ||
				(($ad['show-archive']=='yes') && is_archive()) ||
				(($ad['show-search']=='yes') && is_search()) )
			{ /* We passed the test, display the Ad */
				$code.=$this->get_ad($ad,$name);
			}
		return $code;
	}

	function get_ad($ad=false,$name='') {
		global $ADSENSEM_PUBLISHER;

		$options = get_option('plugin_adsensem');

		if($ad===false){$ad=$options['ads'][$options['defaults']['ad']];}
		$ad=$this->merge_defaults($ad); //Apply defaults
				
			$code='';

			/* Valid entry */
			if(is_array($ad)){

				//Default to Ad
				if($ad['product']=='link'){
					$format = $ad['linkformat'] . $ad['linktype'];
					list($width,$height,$null)=split('[x]',$ad['linkformat']);
				} else if($ad['product']=='referral-image') {
					$format = $ad['referralformat'] . '_as_rimg';
					list($width,$height,$null)=split('[x]',$ad['referralformat']);
				} else if($ad['product']=='referral-text') {
					$format = 'ref_text';
				} else {
					$format = $ad['adformat'] . '_as';
					list($width,$height,$null)=split('[x]',$ad['adformat']);
				}
				
				$code .= $ad['html-before'];


				if($_GET['preview']){
					
					/* We are in the editor, output fake */
					if($ad['product']=='code'){
						//Complicated to extract dimensions from code, for now just output "default" sized object.
						$code .= '<div style="text-align:center;border:1px solid #000;font-size:25px;width:250px;height:100px">';
						$code .= 'AdSense Code Unit<br />#' . $name;
						$code .= '</div>';
					} else if($ad['product']=='referral-text'){
						//Text link referral
						$code .= '<span style="text-decoration:underline;color:#00f">AdSense Referral Link #' . $name . '</span>';
					} else {
						/* We are in the editor, output fake */
						$code .= '<div style="text-align:center;border:1px solid #000;font-size:' . round($height/4,0) . 'px;width:' . $width . 'px;height:' . $height . 'px">';
						$code .= 'AdSense ' . ucwords(str_replace("-"," ",$ad['product'])) . '<br />#' . $name;
						$code .= '</div>';
					}
				
				} else {

					if($ad['product']=='code'){
						$code.=$ad['code'];
					} else {

					$code .= '<script type="text/javascript"><!--' . "\n";
					$code.= 'google_ad_client = "pub-' . $options['adsense-account'] . '";' . "\n";
					
					if($ad['channel']!==''){ $code.= 'google_ad_channel = "' . $ad['channel'] . '";' . "\n"; }

					if(($ad['product']=='ad') || ($ad['product']=='link') || ($ad['product']=='referral-image')){
						$code.= 'google_ad_width = ' . $width . ";\n";
						$code.= 'google_ad_height = ' . $height . ";\n";
					}
					
					$code.= 'google_ad_format = "' . $format . '"' . ";\n";

					if($ad['alternate-url']!=''){ $code.= 'google_alternate_ad_url = "' . $ad['alternate-url'] . '";' . "\n";}
					else if($ad['alternate-color']!=''){ $code.= 'google_alternate_color = "' . $ad['alternate-color'] . '";' . "\n";}
					
					//Default to Ads
					if($ad['product']=='ad'){ $code.= 'google_ad_type = "' . $ad['adtype'] . '"' . ";\n"; }
					
					if($ad['product']=='referral-text'){$code.='google_ad_output = "textlink"' . ";\n";}
					if(($ad['product']=='referral-text') || ($ad['product']=='referral-image')){$code.='google_cpa_choice = "' . $ad['referral'] . '"' . ";\n";}
					
					if(($ad['product']=='ad') || ($ad['product']=='link')){
						$code.= 'google_color_border = "' . $ad['colors']['border'] . '"' . ";\n";
						$code.= 'google_color_bg = "' . $ad['colors']['bg'] . '"' . ";\n";
						$code.= 'google_color_link = "' . $ad['colors']['link'] . '"' . ";\n";
						$code.= 'google_color_text = "' . $ad['colors']['text'] . '"' . ";\n";
						$code.= 'google_color_url = "' . $ad['colors']['url'] . '"' . ";\n";
					}
					
					$code.= "\n" . '//--></script>' . "\n";

					$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";
					
					}
					
				}
				
				$code.=$ad['html-after'];

			}


			return $code;
    }

/* ADMIN ELEMENTS - REUSABLE PAGE ELEMENTS FOR CONFIGURATION */

	function set_defaults($options,$wipebasic=false) {

		$options['defaults'] = array (
				'colors' => array(
						'border'=> 'FFFFFF',
						'link'	=> '0000FF',
						'bg' 	=> 'FFFFFF',
						'text'	=> '000000',
						'url'	=> '008000'	
								),
				'channel' => '',

				'adformat' => '160x600',
				'adtype' => 'text',

				'linkformat' => '728x15',
				'linktype' => '_0ads_al_s',

				'show-home' => "yes",
				'show-post' => "yes",
				'show-archive' => "yes",
				'show-search' => "yes",

				'html-before' => '',
				'html-after' => '',

				'alternate-url' => '',
				'alternate-color' => ''

								);

		if($wipebasic===true){
			$options['ads'] = array(
						'demo-advert' => array()
					);
			$options['adsense-account']="";
			$options['be-nice']=ADSENSEM_BE_NICE;
			$options['defaults']['ad'] = 'demo-advert';
		}

		return $options;
	}


	/* MERGE DEFAULTS - Merge in default settings to fill gaps in Ad setup */
	function merge_defaults($settings) {

		$options = get_option('plugin_adsensem');

		if(is_array($options['defaults'])){
		foreach($options['defaults'] as $key=>$value) {
			if($settings[$key]==''){$settings[$key]=$value;}
		}
		}
		
		if(is_array($options['defaults']['colors'])){
		foreach($options['defaults']['colors'] as $key=>$value){
			if($settings['colors'][$key]==''){$settings['colors'][$key]=$value;} 
		}
		}
	
		return $settings;

	}

	function output_select($list,$selected)
	{
		foreach($list as $key=>$value)
		{
			?><option <?php if($key==$selected){ echo "selected"; }?> value="<?php echo $key; ?>"> <?php echo $value; ?></option><?php
		}
	}
				
	/* ADMIN Settings - Editing form for each Ad and defaults, reusable */
	function admin_settingsform($settings=array('colors'=>array()),$name=false)
	{

			$colors = $settings['colors'];
		
			if($settings['product']==''){$settings['product']='ad';}
				
			//Google AdSense data
			
			$formats['default']=array('' => 'Use Default');

			$formats['ads']['horizontal']=array('728x90' => '728 x 90 Leaderboard', '468x60' => '468 x 60 Banner', '234x60' => '234 x 60 Half Banner');
			$formats['ads']['vertical']=array('120x600' => '120 x 600 Skyscraper', '160x600' => '160 x 600 Wide Skyscraper', '120x240' => '120 x 240 Vertical Banner');
			$formats['ads']['square']=array('336x280' => '336 x 280 Large Rectangle', '300x250' => '300 x 250 Medium Rectangle', '250x250' => '250 x 250 Square', '200x200' => '200 x 200 Small Square', '180x150' => '180 x 150 Small Rectangle', '125x125' => '125 x 125 Button');

			$adtypes=array('text_image' => 'Text &amp; Image', 'image' => 'Image Only', 'text' => 'Text Only');
			
			$formats['links']['horizontal']=array('728x15' => '728 x 15',  '468x15' => '468 x 15');
			$formats['links']['square']=array('200x90' => '200 x 90',  '180x90' => '180 x 90',  '160x90' => '160 x 90',  '120x90' => '120 x 90');

			$linktypes=array('_0ads_al' => '4 Ads Per Unit', '_0ads_al_s' => '5 Ads Per Unit');
			
			$formats['referrals']['horizontal']=array('110x32' => '110 x 32',  '120x60' => '120 x 60',  '180x60' => '180 x 60',  '468x60' => '468 x 60');
			$formats['referrals']['square']=array('125x125' => '125 x 125');
			$formats['referrals']['vertical']=array('120x240' => '120 x 240');

			$yesno=array("yes" => 'Yes', "no" => 'No');
			$default=array('' => 'Use Default');

			$products=array('ad' => 'Ad Unit','link' => 'Link Unit','referral-text' => "Referral (Text)",'referral-image' => "Referral (Image)");	
			$productsextra=array('code' => 'Direct Code Ad');
?>	


		<script type="text/javascript">

		function adsensem_set_color(element,id,what){
			target=document.getElementById(id);
			switch(what) {
				case 'bg':	target.style.background='#' + element.value; break;
				case 'color':	target.style.color='#' + element.value; break;
				case 'border':	target.style.border='1px solid #' + element.value; break;
			}
		}

		function adsensem_product_options(product){
		
		all=Array('ad','link','color','referral','referral-format','code','alternate');
	
		switch (product){
			case 'link':	show = Array('link','color','alternate');
							break;
			case 'ad':		show = Array('ad','color','alternate');
							break;
			case 'referral-text':show = Array('referral');
							break;
			case 'referral-image':show = Array('referral','referral-format');
							break;
			case 'code':	show = Array('code');
							break;
					}
		
		for(a=0;a<all.length;a++){
			found=false;
			for(s=0;s<show.length;s++){if(show[s]==all[a]){found=true;}}
			if(found){document.getElementById('adsensem-settings-' + all[a]).style.display=''}
			else {document.getElementById('adsensem-settings-' + all[a]).style.display='none'}
		}

	}		
		
		</script>

	<table style="width:100%;">
	<tr valign="top"><td class="wrap">

		<h4>Basic</h4>
		<table>
<?php if($name!==false){ ?>
		<tr><td><label for="adsensem-name">Name:</label></td>
			<td><input style="border:2px solid #000" name="adsensem-name" size="15" value="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>" />
				<input name="adsensem-name-old" type="hidden" value="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>" />
			</td></tr>
<?php } ?>
		<tr><td><label for="adsensem-channel">Channel:</label></td>
			<td><input name="adsensem-channel" size="15" title="Enter multiple Channels seperated by + signs" value="<?php echo htmlspecialchars($settings['channel'], ENT_QUOTES); ?>" /></td></tr>

<?php if($name!==false){ ?>
		<tr><td><label for="adsensem-product">Product:</label></td>
			<td><select name="adsensem-product" onchange="adsensem_product_options(this.value);">
			<optgroup label="Google">
			<?php $this->output_select($products,$settings['product']);?>
			</optgroup>	
			<optgroup label="Extra">
			<?php $this->output_select($productsextra,$settings['product']);?>
			</optgroup>
			</select></tr>
<?php } ?>
		</table>

		<div id="adsensem-settings-ad">
		<h4>Ad Unit Settings</h4>
		<table style="padding-bottom:1em;">
		<tr id="row-adformat"><td><label for="adsensem-adformat"><a href="https://www.google.com/adsense/adformats" target="_new">Format:</a></label></td>
			<td>
			<select name="adsensem-adformat">
			<!-- Options taken from Google AdSense setup pages -->
			<?php if($name!==false){ ?><optgroup label="Default"><?php $this->output_select($formats['default'],$settings['adformat']);?></optgroup><?php } ?>
			<optgroup label="Horizontal"><?php $this->output_select($formats['ads']['horizontal'],$settings['adformat']);?></optgroup>
			<optgroup label="Vertical"><?php $this->output_select($formats['ads']['vertical'],$settings['adformat']);?></optgroup>
			<optgroup label="Square"><?php $this->output_select($formats['ads']['square'],$settings['adformat']);?></optgroup> 
			</select>
			</td></tr>

		<tr id="row-adtype"><td><label for="adsensem-adtype">Ad Type:</label></td>
			<td>
			<select name="adsensem-adtype">
			<!-- Options taken from Google AdSense setup pages -->
			<?php if($name!==false){ ?><?php $this->output_select($default,$settings['adtype']);?><?php } ?>
			<?php $this->output_select($adtypes,$settings['adtype']);?>
			</select>
			</td></tr>
		</table>
		</div>

		<div  id="adsensem-settings-link">
		<h4>Link Unit Settings</h4>
		<table style="padding-bottom:1em;">
		<tr id="row-linkformat"><td><label for="adsensem-linkformat"><a href="https://www.google.com/adsense/adformats" target="_new">Format:</a></label></td>
			<td>
			<select name="adsensem-linkformat">
			<!-- Options taken from Google AdSense setup pages -->
			<?php if($name!==false){ ?><optgroup label="Default"><?php $this->output_select($formats['default'],$settings['adformat']);?></optgroup><?php } ?>
			<optgroup label="Horizontal"><?php $this->output_select($formats['links']['horizontal'],$settings['adformat']);?></optgroup>
			<optgroup label="Square"><?php $this->output_select($formats['links']['square'],$settings['adformat']);?></optgroup> 
			</select>
			</td></tr>

		<tr id="row-linktype"><td><label for="adsensem-linktype">Link Type:</label></td>
			<td>
			<select name="adsensem-linktype">
			<!-- Options taken from Google AdSense setup pages -->
			<?php if($name!==false){ ?><?php $this->output_select($default,$settings['linktype']);?><?php } ?>
			<?php $this->output_select($linktypes,$settings['linktype']);?>
			</select>
			</td></tr>
		</table>
		</div>

		<?php  //Referral units & code have no "defaults" so cannot be set up in this way
			if($name!==false){ ?>

		<div id="adsensem-settings-referral">
		<h4>Referral Settings</h4>
		<table  style="padding-bottom:1em;">
		<tr id="adsensem-settings-referral-format"><td style="width:7em;"><label for="adsensem-referralformat"><a href="https://www.google.com/adsense/adformats" target="_new">Format:</a></label></td>
			<td>
			<select name="adsensem-referralformat" id="adsensem-referralformat">
			<!-- Options taken from Google AdSense setup pages -->
			<optgroup label="Horizontal"><?php $this->output_select($formats['referrals']['horizontal'],$settings['referralformat']);?></optgroup>
			<optgroup label="Square"><?php $this->output_select($formats['referrals']['square'],$settings['referralformat']);?></optgroup> 
			<optgroup label="Vertical"><?php $this->output_select($formats['referrals']['vertical'],$settings['referralformat']);?></optgroup> 
			</select>
			</td></tr>
			
			<tr><td><label for="adsensem-referral">Referral Code:</label></td>
			<td><input name="adsensem-referral" size="25" title="Enter referral code from Google AdSense site" value="<?php echo htmlspecialchars($settings['referral'], ENT_QUOTES); ?>" /> (CPA)</td></tr>
		</table>
		</div>

		<div id="adsensem-settings-code">
		<h4>Code Settings</h4>
		<table id="adsensem-settings-code" style="padding-bottom:1em;">
		
		<tr id="row-code"><td style="width:7em;"><label for="adsensem-code"><a href="https://www.google.com/adsense/adsense-products" target="_new">Ad Unit Code:</a></label></td>
		<td>
		<textarea rows="5" cols="30" name="adsensem-code"><?php echo htmlspecialchars($settings['code'], ENT_QUOTES); ?></textarea>
		</td></tr>				
	
		<tr id="row-code-convert"><td></td><td><input type="checkbox" name="adsensem-code-convert" value="1"> <label for="adsensem-codeconvert">convert to AdSense Manager unit [<a href="#" title="Convert AdSense code into an editable AdSense Manager unit.">?</a>]</label></td></tr>
		</table>
		</div><?php } ?>
					
		</td><td class="wrap" id="adsensem-settings-color">		
		
		<h4>Ad Colours</h4>
		<table>
		<tr><td><label for="adsensem-colors-border">Border:</label></td>
			<td>#<input name="adsensem-colors-border" onChange="adsensem_set_color(this,'ad-color-border','border');" size="6" value="<?php echo htmlspecialchars($colors['border'], ENT_QUOTES); ?>" /></td></tr>
		<tr><td><label for="adsensem-colors-link">Title:</label></td>
			<td>#<input name="adsensem-colors-link" onChange="adsensem_set_color(this,'ad-color-link','color');" size="6" value="<?php echo htmlspecialchars($colors['link'], ENT_QUOTES); ?>" /></td></tr>
		<tr><td><label for="adsensem-colors-bg">Background:</label></td>
			<td>#<input name="adsensem-colors-bg" onChange="adsensem_set_color(this,'ad-color-bg','bg');" size="6" value="<?php echo htmlspecialchars($colors['bg'], ENT_QUOTES); ?>" /></td></tr>
		<tr><td><label for="adsensem-colors-text">Text:</label></td>
			<td>#<input name="adsensem-colors-text" onChange="adsensem_set_color(this,'ad-color-text','color');" size="6" value="<?php echo htmlspecialchars($colors['text'], ENT_QUOTES); ?>" /></td></tr>
		<tr><td><label for="adsensem-colors-url">URL:</label></td>
			<td>#<input name="adsensem-colors-url" onChange="adsensem_set_color(this,'ad-color-url','color');" size="6" value="<?php echo htmlspecialchars($colors['url'], ENT_QUOTES); ?>" /></td></tr>
		<tr><td colspan="2">
		
		<?php

 			//Add in default settings for display
			if($name!==false){
				$temp=$this->merge_defaults($settings);
				$colors = $temp['colors'];
			} else {
				$colors = $settings['colors'];
			}

		?>
		<div id="ad-color-bg" style="margin-top:1em;width:160px;background: #<?php echo htmlspecialchars($colors['bg'], ENT_QUOTES); ?>;">
		<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($colors['border'], ENT_QUOTES); ?>" class="linkunit-wrapper">
		<div id="ad-color-link" style="color: #<?php echo htmlspecialchars($colors['link'], ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
			<b><u>Linked Title</u></b><br /></div>
		<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($colors['text'], ENT_QUOTES); ?>; padding: 2px;" class="text">
			Advertiser's ad text here<br /></div>
		<div id="ad-color-url" style="color: #<?php echo htmlspecialchars($colors['url'], ENT_QUOTES); ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">
			www.advertiser-url.com<br /></div>
		<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">
			&nbsp;<u>Ads by Google</u></div>
		</div>
		</div>
		</td></tr>
		</table>

		</td><td class="adsensemcol wrap">

		<h4>Show Inline Ads</h4>

		<table>
		<tr><td><label for="adsensem-show-home">On Homepage</label></td>
			<td><select name="adsensem-show-home">

			<?php if($name!==false){ ?><?php $this->output_select($default,$settings['show-home']);?><?php } ?>
			<?php $this->output_select($yesno,$settings['show-home']);?>
			</select></td></tr>
		<tr><td><label for="adsensem-show-post">On Posts/Pages</label></td>
			<td><select name="adsensem-show-post">
			<?php if($name!==false){ ?><?php $this->output_select($default,$settings['show-post']);?><?php } ?>
			<?php $this->output_select($yesno,$settings['show-post']);?>
			</select></td></tr>
		<tr><td><label for="adsensem-show-archive">On Archives</label></td>
			<td><select name="adsensem-show-archive">
			<?php if($name!==false){ ?><?php $this->output_select($default,$settings['show-archive']);?><?php } ?>
			<?php $this->output_select($yesno,$settings['show-archive']);?>
			</select></td></tr>
		<tr><td><label for="adsensem-show-search">On Search</label></td>
			<td><select name="adsensem-show-search">
			<?php if($name!==false){ ?><?php $this->output_select($default,$settings['show-search']);?><?php } ?>
			<?php $this->output_select($yesno,$settings['show-search']);?>
			</select></td></tr>
		</table>


		<h4>HTML Markup (Optional)</h4>
		<table>
		<tr><td><label for="adsensem-html-before">Before:</label></td>
			<td><input name="adsensem-html-before" size="15" title="Enter HTML to be included before Ad unit" value="<?php echo htmlspecialchars($settings['html-before'], ENT_QUOTES); ?>" /></td></tr>
		<tr><td><label for="adsensem-html-after">After:</label></td>
			<td><input name="adsensem-html-after" size="15" title="Enter HTML to be included after Ad unit" value="<?php echo htmlspecialchars($settings['html-after'], ENT_QUOTES); ?>" /></td></tr>
		</table>

		<div id="adsensem-settings-alternate">
		<h4>Alternate Ads (Optional)</h4>
		<table>		
		<tr><td><label for="adsensem-alternate-url">URL:</label></td>
			<td><input name="adsensem-alternate-url" size="20" title="Enter URL to alternate Ad for display when Google Ad unavailable" value="<?php echo htmlspecialchars($settings['alternate-url'], ENT_QUOTES); ?>" /></td></tr>
		<tr><td><label for="adsensem-alternate-color">Color:</label></td>
			<td>#<input name="adsensem-alternate-color" size="6" title="Enter #COLOUR to display when Google Ad unavailable" value="<?php echo htmlspecialchars($settings['alternate-color'], ENT_QUOTES); ?>" /></td></tr>
		</table>
		</div>



		</td></tr>
		</table>
		<?php if($name!==false){ ?>
		<script>adsensem_product_options('<?php echo $settings['product']; ?>');</script>
		<?php } 
	}

/*
         ADMIN FORM FUNCTIONS

*/

/* 		Define basic settings for the AdSense Manager - for block control use admin_manage */

		function admin_options() {

		// Get our options and see if we're handling a form submission.
		$options = get_option('plugin_adsensem');

		if ( $_POST['adsensem-submit'] ) {
		
			$options['adsense-account']=preg_replace('/\D/','',$_POST['adsensem-adsense-account']);

			update_option('plugin_adsensem', $options);

		}

		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		
?>
        <form action="" method="post" id="adsensem-manage" enctype="multipart/form-data">

		<div class="wrap">
         <h2>AdSense Manager Options</h2>
		
		<p>Configure your basic settings here, such as default colours &amp; Google account settings. 
		Once you have set up the basics, click <strong>Save &raquo;</strong> to save changes, then go to <a href="<?php echo get_bloginfo('wpurl');?>/wp-admin/edit.php?page=adsense-manager">Manage &raquo; Ads</a> to create your Ad blocks.</p>

		<h3>Basic Setup</h3>
		<p>Enter your <label for="adsensem-adsense-account">Google AdSense <strong>Account ID</strong></label>: <input name="adsensem-adsense-account" value="<?php echo strip_tags(stripslashes($options['adsense-account'])); ?>"
		style="<?php if($options['adsense-account']!=''){echo "font-weight:bold; color:green; ";} else { echo "border:1px solid red;"; }?>">

		<br />You can find this number at the bottom of your <a href="https://www.google.com/adsense/account-settings" target="_new">Account Settings</a> page.</p>
		</p>
		
		 <p class="submit"><input type="submit" value="Save changes &raquo;"></p>
		</div>
		<input type="hidden" id="adsensem-submit" name="adsensem-submit" value="1" />
         </form>

		<?php
           }
		   
		function admin_adsense_code_convert() {
			
			$code=stripslashes($_POST['adsensem-code']);
		
			if(preg_match('/google_ad_channel = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-channel'] = $matches[1]; }
			if(preg_match('/google_alternate_ad_url = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-alternate-url'] = $matches[1]; }
			if(preg_match('/google_alternate_color = "(.*)"/', $code, $matches)!=0){ $_POST['adsensem-alternate-color'] = $matches[1]; }
			
			$width=false; $height=false;
			if(preg_match('/google_ad_width = (\d*);/', $code, $matches)!=0){ $width = $matches[1]; }
			if(preg_match('/google_ad_height = (\d*);/', $code, $matches)!=0){ $height = $matches[1]; }
			if( ($width!==false) && ($height!==false) ) { $format = $width . "x" . $height; }
			
			//as_rimg - Referral (Image)
			//ref_text - Referral (Text)
			//_0ads_al - Link Unit
			//_as - Ad Unit
			
			if(preg_match('/google_ad_format = "(.*)"/', $code, $matches)!=0){
				
				$adformat=$matches[1];
				if(strstr($adformat,'_as_rimg')!==false){
					preg_match('/google_cpa_choice = "(.*)"/', $code, $matches);
					$_POST['adsensem-referral']=$matches[1];
					$_POST['adsensem-product']='referral-image';					
					$_POST['adsensem-referralformat']=$format; }
				else if(strstr($adformat,'ref_text')!==false){
					preg_match('/google_cpa_choice = "(.*)"/', $code, $matches);
					$_POST['adsensem-referral']=$matches[1];
					$_POST['adsensem-product']='referral-text';					
				} else if(strstr($adformat,'_0adsl_al')!==false){
					$_POST['adsensem-product']='link';
					$_POST['adsensem-linkformat']=$format;
					if(strstr($adformat,'_0adsl_al_s')){ $_POST['linktype']="_0adsl_al_s"; } else { $_POST['linktype']="_0adsl_al"; }
				} else if(strstr($adformat,'_as')!==false){
					$_POST['adsensem-product']='ad';
					$_POST['adsensem-adformat']=$format;
				}
			}
			
			if(preg_match('/google_color_border = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-colors-border']=$matches[1];}
			if(preg_match('/google_color_bg = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-colors-link']=$matches[1];}
			if(preg_match('/google_color_link = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-colors-bg']=$matches[1];}
			if(preg_match('/google_color_text = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-colors-text']=$matches[1];}
			if(preg_match('/google_color_url = "(.*)"/', $code, $matches)!=0){$_POST['adsensem-colors-url']=$matches[1];}
			
		}

		function admin_save_settings($ad=array()) {
			
			$ad['channel']=strip_tags(stripslashes($_POST['adsensem-channel']));

			$ad['product']=$_POST['adsensem-product'];
			
			$ad['adformat']=strip_tags(stripslashes($_POST['adsensem-adformat']));
			$ad['adtype']=strip_tags(stripslashes($_POST['adsensem-adtype']));

			$ad['linkformat']=strip_tags(stripslashes($_POST['adsensem-linkformat']));
			$ad['linktype']=strip_tags(stripslashes($_POST['adsensem-linktype']));

			$ad['referralformat']=strip_tags(stripslashes($_POST['adsensem-referralformat']));
			$ad['referral']=strip_tags(stripslashes($_POST['adsensem-referral']));

			$ad['code']=stripslashes($_POST['adsensem-code']);
			
			$ad['html-before']=stripslashes($_POST['adsensem-html-before']);
			$ad['html-after']=stripslashes($_POST['adsensem-html-after']);

			$ad['alternate-url']=stripslashes($_POST['adsensem-alternate-url']);
			$ad['alternate-color']=stripslashes($_POST['adsensem-alternate-color']);
			
			$colors['border']=strip_tags(stripslashes($_POST['adsensem-colors-border']));
			$colors['link']=strip_tags(stripslashes($_POST['adsensem-colors-link']));
			$colors['bg']=strip_tags(stripslashes($_POST['adsensem-colors-bg']));
			$colors['text']=strip_tags(stripslashes($_POST['adsensem-colors-text']));
			$colors['url']=strip_tags(stripslashes($_POST['adsensem-colors-url']));
			
			$ad['colors'] = $colors;

			$ad['show-home']=$_POST['adsensem-show-home'];
			$ad['show-post']=$_POST['adsensem-show-post'];
			$ad['show-archive']=$_POST['adsensem-show-archive'];
			$ad['show-search']=$_POST['adsensem-show-search'];

			return $ad;

		}


		/* Define and manage AdSense ad blocks for your Wordpress setup */
		function admin_manage() {

		// Get our options and see if we're handling a form submission.
		$options = get_option('plugin_adsensem');

		if ( $_POST['adsensem-submit'] ) {

			if($_POST['adsensem-edit-default']) {
			
				if($_POST['adsensem-restore-defaults']){
					$options = $this->set_defaults($options);
				} else {
					$options['defaults']=$this->admin_save_settings($options['defaults']);
				}

			} else {

				if( $_POST['adsensem-code-convert'] ){
					//Extract code into $_POST variables, to simulate normal submission	
					$this->admin_adsense_code_convert();
				}
				
				if($_POST['adsensem-name']==''){
					$a=0; do { $a++; $_POST['adsensem-name']='Ad-' . $a; } while (isset($options['ads'][$_POST['adsensem-name']]));
				}

				$name=sanitize_title($_POST['adsensem-name']);

				/* Changing the name of an Ad, copy and delete old */
				if($_POST['adsensem-name']!=$_POST['adsensem-name-old']){
					$options['ads'][$name]=$options['ads'][$_POST['adsensem-name-old']];
					unset($options['ads'][$_POST['adsensem-name-old']]);
					/* We can now use the new $name from this point forward, lovely */

					/* Update default if neccessary */
					if($options['defaults']['ad']==$_POST['adsensem-name-old']){$options['defaults']['ad']=$name;}
					}

				$options['ads'][$name]=$this->admin_save_settings($options['ads'][$name]);
			} 
			
			update_option('plugin_adsensem', $options);

		}

		if ( $_POST['adsensem-delete'] ) {
			//Delete selected advert
			if($_POST['adsensem-delete-name']!=$options['defaults']['ad']){
				unset($options['ads'][$_POST['adsensem-delete-name']]);
				update_option('plugin_adsensem', $options);
			}
		}

		if ( $_POST['adsensem-default'] ) {
			//Set selected advert as default
			$options['defaults']['ad']=$_POST['adsensem-default-name'];
			update_option('plugin_adsensem', $options);
		}

		if( ( $options['defaults']['ad']=='' ) && ( count($options['ads']>0) ) ){
			$options['defaults']['ad']=key($options['ads']);
			update_option('plugin_adsensem', $options);
		}

		if ( $_POST['adsensem-copy'] ) {
			//Copy selected advert
			$a=0; do { $a++; $copyto=$_POST['adsensem-copy-name'] . '-' . $a; } while (isset($options['ads'][$copyto]));
			$options['ads'][$copyto]=$options['ads'][$_POST['adsensem-copy-name']];
			update_option('plugin_adsensem', $options);
		}
		
			?>
			<div class="wrap">
			<h2>Manage Ads</h2>
			<p>Below are your currently created Ads. Remember to set your <strong>Google Adsense ID</strong> at <a href="<?php echo get_bloginfo('wpurl');?>/wp-admin/options-general.php?page=adsense-manager">Options &raquo; AdSense Manager</a></p>
			
			<form action="" method="post" id="adsensem-config" enctype="multipart/form-data">

				<input type="hidden" name="adsensem-copy-name" id="adsensem-copy-name">
				<input type="hidden" name="adsensem-delete-name" id="adsensem-delete-name">
				<input type="hidden" name="adsensem-default-name" id="adsensem-default-name">
				<input type="hidden" name="adsensem-edit-name" id="adsensem-edit-name">

				<style type="text/css">
					th {border-bottom:2px solid #000;}
					#default-options td{border-bottom:1px solid #000;}
				</style>

				<table style="width:100%;" cellspacing="0">
				<tr style="height:2em;"><th>Name</th><th>Channel</th><th>Product</th><th colspan="5" style="width:80px;">Colours</th><th>Format</th><th colspan="3">Admin</th></tr>

					<tr style="background-color:#ddd;" id="default-options">
					<td style="font-weight:bold;">Default Settings</td>
					<td><?php echo htmlspecialchars($options['defaults']['channel'], ENT_QUOTES); ?></td>
					<td></td>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($options['defaults']['colors']['border'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($options['defaults']['colors']['link'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($options['defaults']['colors']['bg'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($options['defaults']['colors']['text'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($options['defaults']['colors']['url'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:100px;text-align:center;"><?php echo htmlspecialchars($options['defaults']['adformat'], ENT_QUOTES); ?><br /><?php echo htmlspecialchars($options['defaults']['linkformat'], ENT_QUOTES); ?></td>
					<td style="width:10px;"><input name="adsensem-edit" type="submit" value="Edit" onClick="document.getElementById('adsensem-edit-name').value='';"></td>
					<td colspan="3"></td>
					</tr>
	
                <?php

				if(sizeof($options['ads'])!=0){
				foreach($options['ads'] as $name=>$ad)
				{	
					?><tr <?php if($name==$options['defaults']['ad']){?>style="background-color:#eee;"<?php } ?>>
					<td><?php echo htmlspecialchars($name, ENT_QUOTES); ?></td>
					<td><?php echo htmlspecialchars($ad['channel'], ENT_QUOTES); ?></td>
					<td style="text-align:center;"><?php echo htmlspecialchars(ucwords(str_replace("-"," ",$ad['product'])), ENT_QUOTES); ?></td>
					
					<?php if(($ad['product']=='ad') || ($ad['product']=='link')) { ?>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($ad['colors']['border'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($ad['colors']['link'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($ad['colors']['bg'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($ad['colors']['text'], ENT_QUOTES); ?>">&nbsp;</td>
					<td style="width:16px;background-color:#<?php echo htmlspecialchars($ad['colors']['url'], ENT_QUOTES); ?>">&nbsp;</td>
					<?php } else { ?><td colspan="5"></td><?php } ?>
								
					<td style="width:100px;text-align:center;"><?php if($ad['product']=='link'){echo htmlspecialchars($ad['linkformat'], ENT_QUOTES);} else if ($ad['product']=='ad') {echo htmlspecialchars($ad['adformat'], ENT_QUOTES);} else if ($ad['product']=='referral-image') {echo htmlspecialchars($ad['referralformat'], ENT_QUOTES);} ?></td>
					<td style="width:10px;"><input name="adsensem-edit" type="submit" value="Edit" onClick="document.getElementById('adsensem-edit-name').value='<?php echo $name; ?>';"></td>
					<td style="width:30px;"><input name="adsensem-copy" type="submit" value="+" onClick="document.getElementById('adsensem-copy-name').value='<?php echo $name; ?>';" title="Copy to new Ad unit"></td>
					
					<?php if($name!=$options['defaults']['ad']){?>
						<td style="width:30px"><input name="adsensem-default" type="submit" value="Set Default" onClick="document.getElementById('adsensem-default-name').value='<?php echo $name; ?>';"></td>
						<td style="width:30px"><input name="adsensem-delete" type="submit" value="Delete" onClick="if(confirm('Delete <?php echo $name; ?>?')){document.getElementById('adsensem-delete-name').value='<?php echo $name; ?>';} else {return false;}"></td>
					<?php } else { ?><td style="width:50px"><strong>Default&nbsp;Ad</strong></td><td></td><?php } ?>
					
					</tr>
					<?php

				}
				} else { ?><tr><td>None.</td></tr><?php }
				?>

				</table>

<p>By changing the <strong>Default settings</strong> (shown on the top line) you can update all Ads at once.
<br /><strong>Default Ad</strong> indicates which Ad will be displayed when no specific ID is used. </p>
<p>Ads can be included in <strong>templates</strong> using <code>&lt;?php adsensem_ad('name'); ?&gt;</code> or <code>&lt;?php adsensem_ad(); ?&gt;</code> for the default Ad.<br />
Ads can be inserted into <strong>posts / pages</strong> using <code>&lt;!--adsense#name--&gt;</code> or <code>&lt;!--adsense--&gt;</code> for the default Ad.</p>

<!--adsense-->

			</form>

		<br /><br />

			<form action="" method="post" id="adsensem-config-edit">
	
		<?php 

			if( ($_POST['adsensem-edit']) && ($_POST['adsensem-edit-name'])=="")
			{ $EDIT_DEFAULT = true; }
			else
			{ $EDIT_DEFAULT = false; }

			if($_POST['adsensem-edit']){ ?>

			<h2>Edit '<?php echo ( $EDIT_DEFAULT )?'Default Settings':$_POST['adsensem-edit-name']; ?>'</h2>
			<p>Edit the settings for your Ad below. If you want to use the default settings for any element,
			simply leave that section unchanged.</p>
				<?php
					if( $EDIT_DEFAULT ) {
					 	$this->admin_settingsform($options['defaults'],false);
					} else {
					 	$this->admin_settingsform($options['ads'][$_POST['adsensem-edit-name']],$_POST['adsensem-edit-name']);
					}

				?>

			<?php } else { ?>
			<h2>Create New</h2>
			<p>Enter the settings for your new Ad below. <br />If you want to use the default settings for any element,
			simply leave that section unchanged.</p>
				<?php $this->admin_settingsform(null,''); ?>


			<?php } ?>

			<input type="hidden" id="adsensem-submit" name="adsensem-submit" value="1" />
			<p class="submit">

<?php if( $EDIT_DEFAULT ) { ?>
<input name="adsensem-restore-defaults" type="submit" value="Restore Defaults &raquo;">
<input type="hidden" id="adsensem-edit-default" name="adsensem-edit-default" value="1" />
<?php } ?>
<input type="submit" value="Save changes &raquo;">

</p>

			</form>

		</div>
		<?php



		}



/*
           IS THE WIDGET PLUGIN LOADED?
           If it is, then we provide Widget positioning for ease of use.
*/


 	    // This is the function that outputs adsensem widget.
	    function widget($args, $n=0) {

		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);
		
		// Each widget can store its own options. We keep strings here.
		$options = get_option('plugin_adsensem');
		$title = $options['ads'][$n]['title'];
		
		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget;
			if($title!=''){ echo $before_title . $title . $after_title; }
			echo $this->get_ad($options['ads'][$n]); //Output the selected ad
		echo $after_widget;
	    }



		/* Widget admin block for each Ad element on the page, allows
			movement of them around the sidebar */
	    function widget_control($name)
		{
			$options = get_option('plugin_adsensem');

			if ( $_POST['adsensem-' . $name . '-submit'] ) {
				// Remember to sanitize and format use input appropriately.
				$options['ads'][$name]['title'] = strip_tags(stripslashes($_POST['adsensem-' . $name . '-title']));
				update_option('plugin_adsensem', $options);
			}

			?>
			<label for="adsensem-<?php echo $name; ?>-title" >Title:</label><input style="width: 200px;" id="adsensem-<?php echo $name; ?>-title" name="adsensem-<?php echo $name; ?>-title" type="text" value="<?php echo htmlspecialchars($options['ads'][$name]['title'], ENT_QUOTES);?>" />
			<input type="hidden" name="adsensem-<?php echo $name; ?>-submit" value="1">
			<?php

        }

		function admin_add_pages()
		{
			$options = get_option('plugin_adsensem');
			
			add_submenu_page('edit.php',"Ads", "Ads", 10, "adsense-manager", array(&$this,'admin_manage'));
			add_options_page("AdSense Manager Options", "AdSense Manager", 10, "adsense-manager", array(&$this,'admin_options'));
		
			/* ADMIN SECTION: WIDGET CONTROL CODE
			/* If SBM installed output the Kludge functions for compatibility */
			/* These use the #id pased via the module name because of inability to pass
				references to functions using class definitions under SBM */
			
			if (function_exists('sbm_get_option') ) 
			{register_widget_control('AdSense Ad', 'adsensem_sbm_widget_control', 300, 80);}
			/* Add the blocks to the Widget panel for positioning */
			else if (function_exists('wp_register_widget_control') )
			{

				/* Loop through available ads and generate widget one at a time */
				if(is_array($options['ads'])){

					foreach($options['ads'] as $name => $ad){
						$args = array('n' => $name, 'height' => 80, 'width' => 300);
						wp_register_widget_control('adsensem-' . $name,'Ad #' . $name, array(&$this,'widget_control'), $args, $name);
					}
				}

        	} 

		
		}


		/* This filter parses post content and replaces markup with the correct ad,
			<!--adsense#name--> for named ad or <!--adsense--> for default */
		function filter_ads($content) {

    		$options = get_option('plugin_adsensem');

			if(is_array($options['ads'])){

				//Insert default ad first
				$content=str_replace("<!--adsense-->",$this->get_ad_inline(false,'default'),$content);

				foreach($options['ads'] as $name=>$ad)
				{	
        			$content = str_replace("<!--adsense#" . $name .  "-->", $this->get_ad_inline($ad,$name), $content);
    			}
				
			}

    		return $content;
		}


/* Editor functions */


		/* Add button to simple editor to include AdSense code */
		function admin_callback_editor()
		{

			$options = get_option('plugin_adsensem');

			//Editor page, so we need to output this editor button code
  			if(
				strpos($_SERVER['REQUEST_URI'], 'post.php')
			||	strpos($_SERVER['REQUEST_URI'], 'post-new.php')
			||	strpos($_SERVER['REQUEST_URI'], 'page.php')
			||	strpos($_SERVER['REQUEST_URI'], 'page-new.php')
			||	strpos($_SERVER['REQUEST_URI'], 'bookmarklet.php'))
			{
			?>
			  <script language="JavaScript" type="text/javascript">
			    <!--
				    var ed_adsensem = document.createElement("select");
	
					ed_adsensem.setAttribute("onchange", "add_adsensem(this)");
					
					

			    	ed_adsensem.setAttribute("class", "ed_button");
			    	ed_adsensem.setAttribute("title", "Select AdSense to Add to Content");
			    	ed_adsensem.setAttribute("id", "ed_adsensem");					

					adh = document.createElement("option");
					adh.value='';
					adh.innerHTML='AdSense...';
					adh.style.fontWeight='bold';
					ed_adsensem.appendChild(adh);

					def = document.createElement("option");
					def.value='';
					def.innerHTML='Default Ad';

					ed_adsensem.appendChild(def);
					<?php 

					if(sizeof($options['ads'])!=0){
					foreach($options['ads'] as $name=>$ad)
					{
						?>	var opt = document.createElement("option");
							opt.value='<?php echo $name; ?>';
							opt.innerHTML='#<?php echo $name; ?>';
							ed_adsensem.appendChild(opt);
						<?php
					}
					}

					?>
			    	document.getElementById("ed_toolbar").insertBefore(ed_adsensem, document.getElementById("ed_spell"));
			    
					/* Below is a Kludge for IE, which causes it to re-read the state of onChange etc. set above. Tut tut tut */
					if (navigator.appName == 'Microsoft Internet Explorer') {
						document.getElementById("ed_toolbar").innerHTML=document.getElementById("ed_toolbar").innerHTML; 
					}
				
			    function add_adsensem(element)
			    {
					if(element.selectedIndex!=0){
	
					if(element.value=='')
						{adsensem_code = '<!--adsense-->';}
					else
						{adsensem_code = '<!--adsense#' + element.value + '-->';}

					contentField = document.getElementById("content");
					if (document.selection && !window.opera) {
						// IE compatibility
						contentField.value += adsensem_code;
					} else
					if (contentField.selectionStart || contentField.selectionStart == '0') {

						var startPos = contentField.selectionStart;
						var endPos = contentField.selectionEnd;
						contentField.value = contentField.value.substring(0, startPos) + adsensem_code + contentField.value.substring(endPos, contentField.value.length);

					} else {

						contentField.value += adsensem_code;
					}
						element.selectedIndex=0;

					}
				}
			  // -->
			</script>
	  <?php
	}
		
		}

/* Initilisation */

		function init()
		{
			$options = get_option('plugin_adsensem');
			if ( !is_array($options) )
				{
					$options = $this->set_defaults($options,true);
					update_option('plugin_adsensem', $options);
				}

			add_action('admin_menu', array(&$this,'admin_add_pages'));
			add_filter('the_content', array(&$this,'filter_ads')); 

			add_action('admin_footer', array(&$this,'admin_callback_editor'));
 
			/* SITE SECTION: WIDGET DISPLAY CODE
			/* If SBM installed output the Kludge functions for compatibility */
			/* These use the #id pased via the module name because of inability to pass
				references to functions using class definitions under SBM */
			if (function_exists('sbm_get_option') ) 
			{register_sidebar_widget('AdSense Ad', 'adsensem_sbm_widget');}
			/* Add the blocks to the Widget panel for positioning WP2.2+*/
			else if (function_exists('wp_register_sidebar_widget') )
			{   
				/* Loop through available ads and generate widget one at a time */
				if(is_array($options['ads'])){
					foreach($options['ads'] as $name => $ad){
						$args = array('n' => $name);
						//$id, $name, $output_callback, $options = array()
						wp_register_sidebar_widget('adsensem-' . $name,'Ad #' . $name, array(&$this,'widget'), $args, $name);
						wp_register_widget_control('adsensem-' . $name,'Ad #' . $name, array(&$this,'widget_control'), 300, 80);
					}
				}

        	} else if (function_exists('register_sidebar_widget') )
			/* Add the blocks to the Widget panel for positioning pre WP2.2*/
			{   
				/* Loop through available ads and generate widget one at a time */
				if(is_array($options['ads'])){
					foreach($options['ads'] as $name => $ad){
						$args = array('n' => $name);
						//$id, $name, $output_callback, $options = array()
						$widget=array('Ad #%s', '', $name);
						register_sidebar_widget($widget, array(&$this,'widget'), $name);
						register_widget_control($widget, array(&$this,'widget_control'), 300, 80, $name);
					}
				}

        	}

			
			
		}


}


$adsensem = new adsensem();

/*	
	SIDEBAR MODULES COMPATIBILITY KLUDGE 
	These functions are external to the class above to allow compatibility with SBM
	which does not allow calls to be passed to a class member.
	These functions are dummy passthru's for the real functions above

	SBM also does not pass any details about the module (e.g. module name, id, etc.) to
	the callback function and it is impossible to determine anything about itself.

	For this reason the name is extracted out of the title given to the SBM and 
	it is not possible to modify individual modules from within the module panel.
	On the upside, it does work.

*/

	function adsensem_sbm_widget($args){
		global $adsensem;
		extract($args);
		/* 	The module's title is in 'title'
			we can extract the "name" from this
			and use it to display the correct Ad.
			
			If no matchable title is found, use 
			the default Ad */
		
		if( preg_match ("/#(.+)(\W+|$)/", $title, $matches) > 0 )
				{ $adsensem->widget($args,$matches[1]); }
		else 	{ $adsensem->widget($args); }
	}

	function adsensem_sbm_widget_control(){
		/*Null function, unable to edit options for multiple Widgets under SBM*/
	}

/*
	END DUMMY KLUDGE
*/

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', array(&$adsensem,'init'));
?>
