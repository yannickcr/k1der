<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;


### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms')) {
	die(__('Access Denied','cforms'));
}

?>

<div class="wrap" id="top">
<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p4-title.jpg" alt=""/>

		<p><?php _e('Here you\'ll find plenty of examples and documentation that should help you configure <strong>cforms II</strong>.', 'cforms'); ?></p>

		<p class="cftoctitle"><?php _e('Table of Contents', 'cforms'); ?></p>
		<ul class="cftoc">
			<li><?php echo str_replace('[url]','#guide',__('<a href="[url]">Basic steps, a small guide &raquo;</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#inserting',__('<a href="[url]">Inserting a form &raquo;</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#fields',__('<a href="[url]">Supported form input fields &raquo;</a>', 'cforms')); ?></li>
			<ul>
				<li><?php echo str_replace('[url]','#qa',__('<a href="[url]">SPAM protection: Q & A &raquo;</a>', 'cforms')); ?></li>
				<li><?php echo str_replace('[url]','#captcha',__('<a href="[url]">SPAM protection: Captcha &raquo;</a>', 'cforms')); ?></li>
				<li><?php echo str_replace('[url]','#hfieldsets',__('<a href="[url]">Fieldsets &raquo;</a>', 'cforms')); ?></li>
				<li><?php echo str_replace('[url]','#regexp',__('<a href="[url]">Using regular expressions with form fields &raquo;</a>', 'cforms')); ?></li>
			</ul>
			<li><?php echo str_replace('[url]','#hook',__('<a href="[url]">Advanced: Post-processing of submitted data &raquo;</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#dynamicforms',__('<a href="[url]">Advanced: Real-time creation of dynamic forms &raquo;</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#variables',__('<a href="[url]">Using variables in email subjects & messages &raquo;</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#CSS',__('<a href="[url]">Styling your forms &raquo;</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#troubles',__('<a href="[url]">Need more help? &raquo;</a>', 'cforms')); ?></li>
		</ul>

	    <h3 id="guide" style="margin-top:55px;"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><span class="h3title-no">1.</span><?php _e('Basic steps, a small guide', 'cforms'); ?></h3>

		<p><?php _e('Admittedly, <strong>cforms</strong> is not the easiest form mailer plugin but it may be the most flexible. The below outline should help you get started with the default form.', 'cforms'); ?></p>
		<ol style="margin:10px 0 0 100px;">
			<li><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#anchorfields',__('First take a look at the <a href="[url]">default form</a>', 'cforms')); ?>
				<ul style="margin:10px 0 0 30px;">
					<li><?php echo __('Verify that it contains all the fields you need, are they in the right order?', 'cforms'); ?></li>
					<li><?php echo __('Check the field labels (field names).', 'cforms'); ?></li>
					<li><?php echo __('Check "Is Required", "Is Email" (<em>if an email address is expected for input</em>) and/or "Auto Clear" (<em>if the field default value needs to be cleared upon focus</em>).', 'cforms'); ?></li>
					<li><?php echo str_replace(array('[url1]','[url2]','[url3]'),array('#qa','#captcha','?page=' . $plugindir . '/cforms-global-settings.php#visitorv'),__('Want to include SPAM protection? Choose between <a href="[url1]">Q&A</a>, <a href="[url2]">captcha</a> add an input field accordingly and configure <a href="[url3]">here</a>.', 'cforms')); ?></li>
				</ul>
			</li>
			<li><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#anchoremail',__('Check if the <a href="[url]">email admin</a> for your form is configured correctly.', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#autoconf',__('Decide if you want the visitor to receive an <a href="[url]">auto confirmation message</a> upon form submission.', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-global-settings.php#tracking',__('Would you like <a href="[url]">to track</a> form submission via the database?', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#inserting',__('<a href="[url]">Add the default form</a> to a post or page.', 'cforms')); ?></li>
			<li><?php echo __('Give it a whirl.', 'cforms'); ?></li>
		</ol>


	    <h3 id="inserting" style="margin-top:55px;"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><span class="h3title-no">2.</span><?php _e('Inserting a form', 'cforms'); ?></h3>

		<p><strong><?php _e('In posts and pages:', 'cforms'); ?></strong></p>
		<p><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-global-settings.php',__('Please use <code class="codehighlight">&lt;!--cforms--&gt;</code> for the first form and/or <code class="codehighlight">&lt;!--cforms<span style="color:red; font-weight:bold;">X</span>--&gt;</code> for your other forms to include them in your <em>Pages/Posts</em>. You can apply the aforementioned code either manually or via the editor button (if turned in the <a href="[url]">Plugin Settings</a>).', 'cforms')); ?></p>
		<p><strong><?php _e('Via PHP function call:', 'cforms'); ?></strong></p>
		<p><?php _e('Alternatively, you can specifically insert a form (into the sidebar for instance etc.) per the PHP function call <code class="codehighlight">insert_cform();</code> for the default/first form and/or <code class="codehighlight">insert_cform(\'<span style="color:red; font-weight:bold;">X</span>\');</code> for any other form. ', 'cforms'); ?></p>
		<p><?php _e('Note: "<span style="color:red; font-weight:bold;">X</span>" represents the number of the form, starting with <span style="color:red; font-weight:bold;">2</span>, 3,4 ..and so forth.', 'cforms'); ?></p>



	    <h3 id="fields"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><span class="h3title-no">3.</span><?php _e('Supported form input fields', 'cforms'); ?></h3>

		<p><?php _e('All supported input fields are listed below, highlighting the expected <em>formats</em> for their associated <em>Field Names</em>.', 'cforms'); ?></p>
		<p class="ex"><?php _e('Note: While the <em>Field Names</em> are usually just the label of a field (e.g. "Your Name"), they can contain additional information to support special functionality (e.g. default values, regular expressions for extended field validation etc.):', 'cforms'); ?></p>
		
		<ul style="margin:10px 0 0 100px; list-style:square;">
			<li><?php echo str_replace('[url]','#textonly',__('<a href="[url]">Text only</a> elements', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#single',__('<a href="[url]">Single-, Multi-line fields</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#select',__('<a href="[url]">Select / drop down box & radio buttons</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#multiselect',__('<a href="[url]">Multi-select box</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#check',__('<a href="[url]">Check boxes</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#checkboxgroup',__('<a href="[url]">Check box groups</a>', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#ccme',__('<a href="[url]">CC:me</a> check box', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#multirecipients',__('<a href="[url]">Multiple recipients</a> drop down box', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#qa',__('SPAM protection: <a href="[url]">Q&A</a> input field', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#captcha',__('SPAM protection: <a href="[url]">Captcha</a> input field', 'cforms')); ?></li>
			<li><?php echo str_replace('[url]','#upload',__('<a href="[url]">File attachments / upload</a>', 'cforms')); ?></li>
		</ul>


		<br style="clear:both;"/>


		<h4 id="textonly">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Text only elements (no input)', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-text.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('text paragraph<span style="color:red; font-weight:bold;"> | </span>css class<span style="color:red; font-weight:bold;"> | </span>optional style', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright"><code class="codehighlight"><?php _e('Please make sure...|mytextclass|font-size:9x; font-weight:bold;', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2"><?php _e('The above expression applies the custom class "<code class="codehighlight">mytextclass</code>" <strong>AND</strong> the specific styles "<code class="codehighlight">font-size:9x; font-weight:bold;</code>" to the paragraph.', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="ball" colspan="2"><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-css.php',__('If you specific a <code class="codehighlight">css class</code>, you also need to define it in your current form theme file, <a href="[url]">here</a>.', 'cforms')); ?></td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<h4 id="single">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Single/Multi line input fields', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-single.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('field name<span style="color:red; font-weight:bold;"> | </span>default value<span style="color:red; font-weight:bold;"> | </span><a href="#regexp">regular expression</a>', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('Reference #|xxx-xx-xxx|^[0-9A-Z-]+$', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('If you need to omit the <em>default value</em>, the syntax would be: <code class="codehighlight">Your name||^[a-zA-Z \.]+$</code>', 'cforms'); ?>
				</td>
			</tr>
		</table>
		
		
		<br style="clear:both;"/>


		<h4 id="select">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Select boxes & radio buttons', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-dropdown.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('field name<span style="color:red; font-weight:bold;"> # </span>option1<span style="color:red; font-weight:bold;">|</span>value1<span style="color:red; font-weight:bold;"> # </span>option2<span style="color:red; font-weight:bold;">|</span>value2<span style="color:red; font-weight:bold;"> # </span>option3...', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('Your age#12-18|kiddo#19 to 30|young#31 to 45#45+ |older', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Omitting the <code class="codehighlight">field name</code> will result in not showing a label to the left of the field.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('The <strong>option</strong> placeholder determins the text displayed to the visitor, <strong>value</strong> what is being sent in the email.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Is no <strong>value</strong> explicitly given, then the shown option text is the value sent in the email.', 'cforms'); ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('<strong>Select box marked "Is Required":</strong> Using a minus symbol <code class="codehighlight">-</code> as the value (after <span style="color:red; font-weight:bold;">|</span>), will mark an option as "not valid"! Example:<br /><code class="codehighlight">Your age#Please pick your age group|-#12 to 18|kiddo#19 to 30|young#31 to 45#45+ |older</code>. <br />"Please pick..." is shown but not considered a valid value.', 'cforms'); ?>
				</td>
			</tr>			
		</table>


		<br style="clear:both;"/>

		<h4 id="multiselect">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Multi select boxes', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-ms.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('field name<span style="color:red; font-weight:bold;"> # </span>option1<span style="color:red; font-weight:bold;">|</span>value1<span style="color:red; font-weight:bold;"> # </span>option2<span style="color:red; font-weight:bold;">|</span>value2<span style="color:red; font-weight:bold;"> # </span>option3...', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('Pick#red#blue#green#yellow#orange', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Multi select fields can be set to <strong>Is Required</strong>. If so and unless at least one option is selected the form won\'t validate.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('If <code class="codehighlight">value1,2,..</code> are not specfified, the values delivered in the email default to <code class="codehighlight">option1,2,...</code>.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Examples for specific values could be the matching color codes: e.g. <code class="codehighlight">red|#ff0000</code>', 'cforms'); ?>
				</td>
			</tr>			
		</table>


		<br style="clear:both;"/>


		<h4 id="check">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Check boxes', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-checkbox.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('field name <u>left</u><span style="color:red; font-weight:bold;"> # </span>field name <u>right</u>', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('#please check if you\'d like more information', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('You can freely choose on which side of the check box the label appears (e.g. <code class="codehighlight">#label-right-only</code>).', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('If <strong>both</strong> left and right labels are provided, only the <strong>right one</strong> will be considered.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Checkboxes can be flagged "<strong>Is Required</strong>" to support special use cases, e.g.: when you require the visitor to confirm that he/she has read term & conditions, before submitting the form.', 'cforms'); ?>
				</td>
			</tr>			
		</table>
		

		<br style="clear:both;"/>


		<h4 id="checkboxgroup">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Check box groups', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-grp.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('field name<span style="color:red; font-weight:bold;"> # </span>chk box1 label<span style="color:red; font-weight:bold;">|</span>chk box1 value<span style="color:red; font-weight:bold;"> # </span>chk box2 label<span style="color:red; font-weight:bold;"> ## </span>chk box3...', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('Select Color#green|00ff00 #red|ff0000 #purple|8726ac #yellow|fff90f', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Two # (<code class="codehighlight">##</code>) in a row will force a new line! This helps to better structure your check box group.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Similar to <strong>multi-select boxes</strong> (see above), <strong>Check box groups</strong> allow you to deploy several check boxes (with their labels and corresponding values) that form one logical field. The result submitted via the form email is a single line including all checked options.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('If no explicit <strong>value</strong> (text after the pipe symbol \'<span style="color:red; font-weight:bold;">|</span>\') is specified, the provided check box label is both label & submitted value.', 'cforms'); ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('None of the check boxes within a group can be made "required".', 'cforms'); ?>
				</td>
			</tr>			
		</table>
		

		<br style="clear:both;"/>


		<h4 id="ccme">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('CC: option for visitors', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-cc.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('field name <u>left</u><span style="color:red; font-weight:bold;">#</span>field name <u>right</u>', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('#please cc: me', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#autoconf',__('If the visitor chooses to be CC\'ed, <strong>no</strong> additional auto confirmation email (<a href="[url]">if configured</a>) is sent out!', 'cforms')); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Please also see <em>check boxes</em> above.', 'cforms'); ?>
				</td>
			</tr>
		</table>
		

		<br style="clear:both;"/>


		<h4 id="multirecipients">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Multiple form mail recipients', 'cforms'); ?>
			<span style="font-size:10px; color:#ffeaef; margin-left:15px"><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-options.php#anchoremail',__('Note: This requires corresponding email addresses <a href="[url]">here</a>!!', 'cforms')); ?></span>
		</h4>
		
		
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-multi.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('field name<span style="color:red; font-weight:bold;"> # </span>Name1<span style="color:red; font-weight:bold;"> # </span>Name2<span style="color:red; font-weight:bold;"> # </span>Name3...', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('Send to#Joe#Pete#Hillary', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#anchoremail',__('The order of the names (1,2,3...) provided in the input field <strong>directly</strong> corresponds with the order of email addresses configured <a href="[url]">here</a>.', 'cforms')); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<h4 id="qa">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Visitor verification (Q&A)', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-vv.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('--', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('--', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-global-settings.php#visitorv',__('No <code class="codehighlight">field name</code> required, the field has no configurable label per se, as it is determined at run-time from the list of <strong>Question & Answers</strong> provided <a href="[url]">here</a>.', 'cforms')); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('It makes sense to encapsulate this field inside a FIELDSET, to do that simply add a <code class="codehighlight">New Fieldset</code> field before this one.', 'cforms'); ?>
				</td>
			</tr>		
		</table>


		<br style="clear:both;"/>


		<h4 id="captcha">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Captcha', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-cap.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('field name', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('Enter code', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Alternatively or in addition to the above <strong>Visitor verification</strong> feature, you can have the visitor provide a captcha response.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
		</table>


		<br style="clear:both;"/>


		<h4 id="upload">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Attachments / File Upload Box', 'cforms'); ?>
		</h4>
		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-upload.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('form label', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('Please select a file', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php echo str_replace('[url]','?page='.$plugindir.'/cforms-global-settings.php#upload',__('Please double-check the <a href="[url]">general settings</a> for proper configuration of the <code class="codehighlight">File Upload</code> functionality (allowed extensions, file size etc.).', 'cforms')); ?>
				</td>
			</tr>
		</table>


		<br style="clear:both;"/>


		<h4 id="hfieldsets">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Fieldsets', 'cforms'); ?>
		</h4>

   		<p style="margin:10px 30px;"><?php _e('Fieldsets are definitely part of good form design, they are form elements that are used to create individual sections of content within a given form.', 'cforms'); ?></p>

		<img class="helpimg" src="<?php echo $cforms_root; ?>/images/example-fieldsets.png"  alt=""/>
		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('fieldset name', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('My Fieldset', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Fieldsets can begin anywhere, simply add a <strong>New Fieldset</strong> field between or before your form elements.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Fieldsets do not need to explicitly be closed, a <strong>New Fieldset</strong> element will automatically close the existing (if there is one to close) and reopen a new one.', 'cforms'); ?>
				</td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('<strong>End Fieldset</strong> <u>can</u> be used, but it works without just as well.', 'cforms'); ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('If there is no closing <strong>End Fieldset</strong> element, the plugin assumes that it needs to close the set just before the submit button', 'cforms'); ?>
				</td>
			</tr>			
		</table>


		<br style="clear:both; "/>


		<h4 id="regexp">
			<span class="h4ff"><?php _e('form<br />field', 'cforms'); ?></span>
			<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<?php _e('Using regular expressions with form fields', 'cforms'); ?>
		</h4>
		
		<p style="margin:10px 30px;"><?php _e('A regular expression (regex or regexp for short) is a special text string for describing a search pattern, according to certain syntax rules. Many programming languages support regular expressions for string manipulation, you can use them here to validate user input. Single/Multi line input fields:', 'cforms'); ?></p>

		<!-- no img for regexps-->
		<table class="hf" cellspacing="2" border="4" width="95%">
			<tr>
				<td class="bleft"><span class="abbr" title="<?php _e('Entry format for Field Name', 'cforms'); ?>"><?php _e('Format:', 'cforms'); ?></span></td>
				<td class="bright"><?php _e('field name<span style="color:red; font-weight:bold;"> | </span>default value<span style="color:red; font-weight:bold;"> | </span>regular expression', 'cforms'); ?></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:<br />US zip code', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('<code class="codehighlight">^\d{5}$)|(^\d{5}-\d{4}$</code>', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('Example:<br />US phone #', 'cforms'); ?></td><td class="bright">
					<code class="codehighlight"><?php _e('<code class="codehighlight">^[\(]?(\d{0,3})[\)]?[\s]?[\-]?(\d{3})[\s]?[\-]?(\d{4})[\s]?[x]?(\d*)$</code>', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('<strong>NOTE:</strong>', 'cforms'); ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('<code class="codehighlight">^</code> and <code class="codehighlight">$</code> define the start and the end of the input', 'cforms'); ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('"<code class="codehighlight">ab*</code>": matches a string that has an "a" followed by zero or more "b\'s" ("a", "ab", "abbb", etc.);', 'cforms'); ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('"<code class="codehighlight">ab+</code>": same, but there\'s at least one b ("ab", "abbb", etc.);', 'cforms'); ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('"<code class="codehighlight">[a-d]</code>": a string that has lowercase letters "a" through "d"', 'cforms'); ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('More information can be found <a href="http://weblogtoolscollection.com/regex/regex.php">here</a>, a great regexp repository <a href="http://regexlib.com">here</a>.', 'cforms'); ?>
				</td>
			</tr>			
		</table>
		


	    <h3 id="hook"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><span class="h3title-no">4.</span><?php _e('Advanced: Post-processing of submitted data', 'cforms'); ?></h3>

		<p><?php _e('This is really for hard core deployments, where <em>real-time manipulation</em> of a form & fields are required.', 'cforms'); ?></p>

		<p><?php _e('If you require the submitted data to be sent to a 3rd party or would like to make use of the data otherwise, here is how:', 'cforms'); ?></p>
		<ol>
			<li><?php _e('Edit your WP theme\'s <strong>function.php</strong> file and add a <em>new action</em>', 'cforms');?></li>
			<li><?php _e('Take the below code sample and modify as required', 'cforms');?></li>
		</ol>

        <strong><?php _e('Example:', 'cforms'); ?></strong>
		<code class="codehighlight"><pre style="font-size: 11px;">
function process_data($cformsdata) {

	$formID = $cformsdata['id'];      // <?php _e('Note: <code class="codehighlight">$formID</code> = <strong>\'\'</strong> (empty) for the first form!', 'cforms');?> 
	$form   = $cformsdata['data'];    // & form data
	
	if ( $formID == '3' ) {           // your third form (1st form would be '' !)
		
		foreach ( array_keys($form) as $key ) {                 // go through all keys (field names)
			if ( $key=="Your Name" )                        // if specific one found, 
				$form[$key] = 'Mr./Mrs. '.$form[$key];  // do someting with it
		}
		
		// send to 3d party or do something else
		
	}
	
}
add_action('cforms_data','process_data');</pre></code>


	    <h3 id="dynamicforms"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><span class="h3title-no">5.</span><?php _e('Advanced: Real-time creation of dynamic forms', 'cforms'); ?></h3>

		<p><?php _e('Again, this is for the advanced user who requires ad-hoc creation of forms.', 'cforms'); ?></p>

		
		<p><strong><?php _e('A few things to note on dynamic forms:', 'cforms'); ?></strong></p>
		<ol>
			<li><?php _e('Dynamic forms only work in <strong>non-Ajax</strong> mode.', 'cforms');?></li>
			<li><?php _e('Each dynamic form references and thus requires <strong>a base form defined</strong> in the cforms form settings. All its settings will be used, except the form (&field) definition.', 'cforms');?></li>
			<li><?php _e('Any of the form fields described in the plugins\' <strong>HELP!</strong> section can be dynamically generated.', 'cforms');?></li>
			<li><?php _e('Function call to generate dynamic forms: <code class="codehighlight">insert_custom_cform($fields:array,$form-no:int);</code> with', 'cforms');?>

                <br /><br />
                <code class="codehighlight">$form-no</code>: <?php _e('empty string for the first (default) form and <strong>2</strong>,3,4... for any subsequent form', 'cforms'); ?><br />
                <code class="codehighlight">$fields</code> :

                <code class="codehighlight"><pre style="font-size: 11px;">
            $fields['label'][n]      = '<?php _e('field name', 'cforms'); ?>';           <?php _e('<em>field name</em> format described above', 'cforms'); ?>

            $fields['type'][n]       = 'input field type';     default: 'textfield';
            $fields['isreq'][n]      = true|false;             default: false;
            $fields['isemail'][n]    = true|false;             default: false;
            $fields['isclear'][n]    = true|false;             default: false;
            $fields['isdisabled'][n] = true|false;             default: false;

            n = 0,1,2...</pre></code></li>
    		</ol>


        <strong><?php _e('Form input field types (\'type\'):', 'cforms'); ?></strong>
        <ul style="list-style:none;">
        <li>
            <table>
                <tr><td><?php _e('text paragraph:', 'cforms'); ?></td><td> <code class="codehighlight">textonly</code></td></tr>
                <tr><td><?php _e('single input field:', 'cforms'); ?></td><td> <code class="codehighlight">textfield</code></td></tr>
                <tr><td><?php _e('multi line field:', 'cforms'); ?></td><td> <code class="codehighlight">textarea</code></td></tr>
                <tr><td><?php _e('check boxes:', 'cforms'); ?></td><td> <code class="codehighlight">checkbox</code></td></tr>
                <tr><td><?php _e('check boxes groups:', 'cforms'); ?></td><td> <code class="codehighlight">checkboxgroup</code></td></tr>
                <tr><td><?php _e('drop down fields:', 'cforms'); ?></td><td> <code class="codehighlight">selectbox</code></td></tr>
                <tr><td><?php _e('multi select boxes:', 'cforms'); ?></td><td> <code class="codehighlight">multiselectbox</code></td></tr>
                <tr><td><?php _e('radio buttons:', 'cforms'); ?></td><td> <code class="codehighlight">radiobuttons</code></td></tr>
                <tr><td><?php _e('\'CC\' check box', 'cforms'); ?> <sup>*)</sup>:</td><td> <code class="codehighlight">ccbox</code></td></tr>
                <tr><td><?php _e('multi-recipients field', 'cforms'); ?> <sup>*)</sup>:</td><td> <code class="codehighlight">emailtobox</code></td></tr>
                <tr><td><?php _e('spam/Q&A verification', 'cforms'); ?> <sup>*)</sup>:</td><td> <code class="codehighlight">verification</code></td></tr>
                <tr><td><?php _e('spam/captcha verification', 'cforms'); ?> <sup>*)</sup>:</td><td> <code class="codehighlight">captcha</code></td></tr>
                <tr><td><?php _e('file upload fields', 'cforms'); ?> <sup>*)</sup>:</td><td> <code class="codehighlight">upload</code></td></tr>
                <tr><td><?php _e('begin of a fieldset:', 'cforms'); ?></td><td> <code class="codehighlight">fieldsetstart</code></td></tr>
                <tr><td><?php _e('end of a fieldset:', 'cforms'); ?></td><td> <code class="codehighlight">fieldsetend</code></td></tr>
            </table>
        </li>
        <li><sup>*)</sup> <?php _e('<em>should only be used <strong>once</strong> per generated form!</em>', 'cforms'); ?></li>
        </ul>


        <br />


		<a id="ex1"></a>
        <strong><?php _e('Simple example:', 'cforms'); ?></strong>
        <ul style="list-style:none;">
        <li>
        <code class="codehighlight"><pre style="font-size: 11px;">
$fields = array();

$fields['label'][0]   ='<?php _e('Your Name|Your Name', 'cforms'); ?>';
$fields['type'][0]    ='textfield';
$fields['isreq'][0]   ='1';
$fields['isemail'][0] ='0';
$fields['isclear'][0] ='1';

$fields['label'][1]   ='<?php _e('Your Email', 'cforms'); ?>';
$fields['type'][1]    ='textfield';
$fields['isreq'][1]   ='0';
$fields['isemail'][1] ='1';

insert_custom_cform($fields,'');    //<?php _e('call default form with new fields (2)', 'cforms'); ?></pre></code>
        </li>
        </ul>


        <br />


		<a id="ex2"></a>
        <?php _e('<strong>More advanced example</strong> (file access)<strong>:</strong>', 'cforms'); ?>
        <ul style="list-style:none;">
        <li>
        <code class="codehighlight"><pre style="font-size:11px">
$fields['label'][0]  ='<?php _e('Your Name|Your Name', 'cforms'); ?>';
$fields['type'][0]   ='textfield';
$fields['isreq'][0]  ='1';
$fields['isemail'][0]='0';
$fields['isclear'][0]='1';
$fields['label'][1]  ='<?php _e('Email', 'cforms'); ?>';
$fields['type'][1]   ='textfield';
$fields['isreq'][1]  ='0';
$fields['isemail'][1]='1';
$fields['label'][2]  ='<?php _e('Please pick a month for delivery:', 'cforms'); ?>||font-size:14px; padding-top:12px; text-align:left;';
$fields['type'][2]   ='textonly';

$fields['label'][3]='<?php _e('Deliver on#Please pick a month', 'cforms'); ?>|-#';

$fp = fopen(dirname(__FILE__).'/months.txt', "r"); // <?php _e('need to put this file into your themes dir!', 'cforms'); ?> 

while ($nextitem = fgets($fp, 512))
	$fields['label'][3] .= $nextitem.'#';

fclose ($fp);

$fields['label'][3]  = substr( $fields['label'][3], 0, strlen($fields['label'][3])-1 );  //<?php _e('remove the last \'#\'', 'cforms'); ?> 
$fields['type'][3]   ='selectbox';
$fields['isreq'][3]  ='1';
$fields['isemail'][3]='0';

insert_custom_cform($fields,5);    //<?php _e('call form #5 with new fields (4)', 'cforms'); ?></pre></code>
        </li>
        </ul>

        <?php _e('With <code class="codehighlight">month.txt</code> containing all 12 months of a year:', 'cforms'); ?>
        <ul style="list-style:none;">
        <li>
        <code class="codehighlight"><pre>
<?php _e('January', 'cforms'); ?>

<?php _e('February', 'cforms'); ?>

<?php _e('March', 'cforms'); ?>

...</pre></code>
        </li>
        </ul>        



	    <h3 id="variables"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><span class="h3title-no">6.</span><?php _e('Using variables in email subject and messages', 'cforms'); ?></h3>
		<p>
			<?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#anchoremail',__('Email <strong>subjects and messages</strong> for emails both to the <a href="[url]">form admin</a> as well as to the ', 'cforms')); ?>
			<?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#autoconf',__('<a href="[url]">visitor</a> (auto confirmation, CC:) support insertion of pre-defined variables and/or any of the form input fields.', 'cforms')); ?>
		</p>
		<p class="ex"><?php _e('Note that the variable names are case sensitive!', 'cforms'); ?></p>

		<table class="hf" cellspacing="2" border="4">
			<tr>
				<td class="bright" colspan="2"><span class="abbr" title="<?php _e('Case sensitive!', 'cforms'); ?>"><?php _e('Predefined variables:', 'cforms'); ?></span></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('{Form Name}', 'cforms'); ?></td>
				<td class="bright"><code class="codehighlight"><?php _e('Inserts the form name (per your configuration).', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('{Page}', 'cforms'); ?></td>
				<td class="bright"><code class="codehighlight"><?php _e('Inserts the WP page the form was submitted from.', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('{Date}', 'cforms'); ?></td>
				<td class="bright"><code class="codehighlight"><?php _e('Inserts date & time of form submission (per your general WP settings).', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('{IP}', 'cforms'); ?></td>
				<td class="bright"><code class="codehighlight"><?php _e('Inserts visitor IP address.', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="bleft"><?php _e('{ID}', 'cforms'); ?></td>
				<td class="bright"><code class="codehighlight"><?php _e('Inserts a unique and referenceable form ID (provided that DB Tracking is enabled!)', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="bleft"><em><?php _e('Special:', 'cforms'); ?></em></td>
				<td class="bright"><code class="codehighlight"><?php _e('A single "<code class="codehighlight">.</code>" (period) on a line inserts a blank line.', 'cforms'); ?></code></td>
			</tr>
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Alternatively, you can also include any of your form input fields by referring to the exact (!) <strong>field name</strong>:<br /><u>Example:</u> The default form comes with a <span style="font-weight:bold; background:#f2f2f2;">Your Name</span> and <span style="font-weight:bold; background:#f2f2f2;">Website</span> field.', 'cforms'); ?>
					<br />
					<?php _e('The corresponding variables to be used would be: <span style="font-weight:bold; background:#f2f2f2;">{Your Name}</span> & <span style="font-weight:bold; background:#f2f2f2;">{Website}</span>.', 'cforms'); ?>					
				</td>
			</tr>			
		</table>
		<br />
		<table class="hf" cellspacing="2" border="4" width="75%"> 
			<tr>
				<td class="bright" colspan="2"><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#cforms_header_html',__('Here is an example for a simple <a href="[url]">Admin HTML message</a> <em>(you can copy and paste the below code or change to your liking)</em>:', 'cforms')); ?></td>
			</tr>

			<tr>
				<td class="ball" colspan="2">
					<strong><?php _e('HTML code:', 'cforms'); ?></strong><br />
					<?php echo '<p>&lt;p style="background:#fafafa; text-align:center; font:10px arial"&gt;' . __('a form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms') . '&lt;/p&gt;</p>'; ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<strong><?php _e('Output:', 'cforms'); ?></strong><br />
					<?php echo '<p style="background:#fafafa; text-align:center; font:10px arial">' . __('a form has been submitted on June 13, 2007 @ 9:38 pm, via: / [IP 184.153.91.231]', 'cforms') . '</p>'; ?>					
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Note: With this limited message you\'d want to enable the option "Include pre formatted form data table in HTML part"', 'cforms'); ?><br />
				</td>
			</tr>			
		</table>
		<br />
		<table class="hf" cellspacing="2" border="4" width="75%"> 
			<tr>
				<td class="bright" colspan="2"><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#cforms_header_html',__('Here is another example for a more detailed <a href="[url]">Admin HTML message</a>:', 'cforms')); ?></td>
			</tr>

			<tr>
				<td class="ball" colspan="2">
					<strong><?php _e('HTML code:', 'cforms'); ?></strong><br />
					<?php echo '<p>&lt;p&gt;'.__('{Your Name} just submitted {Form Name}. You can get in touch with him/her via &lt;a href="mailto:{Email}"&gt;{Email}&lt;/a&gt; and might want to check out his/her web page at &lt;a href="{Website}"&gt;{Website}&lt;/a&gt;', 'cforms') . '&lt;/p&gt;</p><p>&lt;p&gt;' .  __('The message is:', 'cforms') . '&lt;br/ &gt;<br/ >'.__('{Message}', 'cforms') . '&lt;/p&gt;</p>'; ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<strong><?php _e('Output:', 'cforms'); ?></strong><br />
					<?php echo '<p>' . __('John Doe just submitted MY NEW FORM. You can get in touch with him/her via <a href="mailto:#">john.doe@doe.com</a> and might want to check out his/her web page at <a href="#">http://website.com</a>', 'cforms') . '</p>'; ?>					
					<?php echo '<p>' . __('The message is:', 'cforms') . '<br />'; ?>					
					<?php echo  __('Hey there! Just wanted to get in touch. Give me a ring at 555-...', 'cforms') . '</p>'; ?>					
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<?php _e('Note: With this more detailed message you can disable the option "Include pre formatted form data table in HTML part" since you already have all fields covered in the actual message/header.', 'cforms'); ?><br />
				</td>
			</tr>			
		</table>
		<br />
		<table class="hf" cellspacing="2" border="4" width="75%"> 
			<tr>
				<td class="bright" colspan="2"><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-options.php#cforms_cmsg_html',__('And a final example for a <a href="[url]">HTML auto confirmation message</a>:', 'cforms')); ?></td>
			</tr>

			<tr>
				<td class="ball" colspan="2">
					<strong><?php _e('HTML code:', 'cforms'); ?></strong><br />
					<?php echo '<p>&lt;div style="text-align:center; color:#aaa; border-bottom:1px solid #aaa"&gt; &lt;strong&gt;' . __('auto confirmation message, {Date}', 'cforms') . '&lt;/strong&gt; &lt;/div&gt;&lt;br /&gt;</p>'; ?>
					<?php echo '&lt;p&gt;&lt;strong&gt;' . __('Dear {Your Name},', 'cforms') . '&lt;/strong&gt;&lt;/p&gt;<br />'; ?>
					<?php echo '&lt;p&gt;' . __('Thank you for your note!', 'cforms') . '&lt;/p&gt;<br />'; ?>
					<?php echo '&lt;p&gt;' . __('We will get back to you as soon as possible.', 'cforms') . '&lt;/p&gt;<br />'; ?>
				</td>
			</tr>			
			<tr>
				<td class="ball" colspan="2">
					<strong><?php _e('Output:', 'cforms'); ?></strong><br />				
					<?php echo '<div style="text-align:center; color:#aaa; border-bottom:1px solid #aaa"><strong>' . __('auto confirmation message, June 13, 2007 @ 5:03 pm', 'cforms') . '</strong></div><br />'; ?>					
					<?php echo '<p><strong>' . __('Dear John Doe,', 'cforms') . '</strong></p>'; ?>					
					<?php echo '<p>' . __('Thank you for your note!', 'cforms') . '</p>'; ?>					
					<?php echo '<p>' . __('We will get back to you as soon as possible.', 'cforms') . '</p>'; ?>					
				</td>
			</tr>			
		</table>

	    <h3 id="CSS"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><span class="h3title-no">7.</span><?php _e('Styling Your Forms (CSS theme files)', 'cforms'); ?></h3>
		<p><?php echo str_replace('[url]','?page=' . $plugindir . '/cforms-css.php',__('Please see the <a href="[url]">Styling page</a> for theme selection and editing options.', 'cforms')); ?></p>
		<p><?php _e('cforms comes with a few theme examples (some of the may require adjustments to work with <strong>your</strong> forms!) but you can of course create your own theme file -based on the default <strong>cforms.css</strong> file- and put it in the <code class="codehighlight">/styling</code> directory.', 'cforms'); ?></p>
		<p class="ex"><?php _e('Your form <strong>doesn\'t</strong> look like the preview image, or your individual changes don\'t take effect, check your global WP theme CSS! It may overwrite some or many cforms CSS declarations. If you don\'t know how to trouble shoot, take a look at the Firefox extension "Firebug" - an excellent CSS troubleshooting tool!', 'cforms'); ?></p>


	    <h3 id="troubles"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><span class="h3title-no">8.</span><?php _e('Need more help?', 'cforms'); ?></h3>
		<p><?php _e('For up-to-date information check the <a href="http://www.deliciousdays.com/cforms-forum">cforms forum</a> and comment section on the plugin homepage.', 'cforms'); ?></p>


	<?php cforms_footer(); ?>
</div>
