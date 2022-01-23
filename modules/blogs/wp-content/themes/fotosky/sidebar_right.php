	<div id="sidebar2">
		<ul>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(2) ) :
?>
<?
 else : ?>
			<?php get_links_list(); ?>
						
			<li class="widget_meta widget_bg">
				<ul><h2>méta</h2>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valide <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
					<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
					<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
					<?php wp_meta(); ?>
				</ul>
				<div class="end">
				</div>
			</li>
			

<?php endif; ?>
		</ul>
	</div>
