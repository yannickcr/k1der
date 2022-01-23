		<div id="right">
			
			<div id="search">
			<form method="get" id="searchform" action="<?php bloginfo('home'); ?>">
			<div><input type="text" value="Rechercher..." onfocus="this.value='';" onblur="if(!this.value) this.value='Rechercher...';" name="s" id="s" /></div>
			</form>
			</div>
			
			<div id="cats">
				<h3>Catégories</h3>
				<ul>
					<?php wp_list_cats('sort_column=name&optioncount=0&hierarchical=0&hide_empty=0'); ?>
				</ul>
			</div>
			
			<div id="misc">
				
				<div id="recent">
				<h3>Articles Récents</h3>
				<ul>
				
				<?php
					$posts = get_posts('numberposts=5');
					foreach($posts as $post) :
					?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php endforeach; ?>

				</ul>				
				</div>
				
				
					<?php if ( !function_exists('dynamic_sidebar')
			|| !dynamic_sidebar(1) ) : ?>
				<div id="archive">
				<h3>Archives</h3>
					<ul>
						<?php wp_get_archives('type=monthly'); ?>
					</ul>
				</div>
			<?php endif; ?>
	
			</div>
			
			<div id="footlinks">
				<div id="links">
					<h3>Liens</h3>
					<ul>
						<?php get_links('-1', '<li>', '</li>', '<br />', FALSE, 'id', TRUE, 
						FALSE, -1, TRUE); ?>
					</ul>
				</div>
				<div id="buttons">
				<ul>
						<li><a href="<?php bloginfo('rss2_url'); ?>">Articles RSS</a></li>
						<li><a href="<?php bloginfo('comments_rss2_url'); ?>">Commentaires RSS</a></li>
				</ul>
				</div>
			</div>
		
		</div>
