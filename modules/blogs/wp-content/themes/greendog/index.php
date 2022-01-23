<?php get_header(); ?>

<div id="centerTL">
<div id="centerTR">
<div id="centerBR">
<div id="centerBL">
	<div id="container">
		<div id="left">
		
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
			
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="postHeader">
				<p class="cal"><?php the_time('j') ?><small><?php the_time('F') ?></small></p>
				<h2><a href="<?php the_permalink() ?>" title="Lien Permanent vers &laquo;<?php the_title(); ?>&raquo;"><?php the_title(); ?></a></h2>
				</div>
				
				<div class="postBody">
				<?php the_content('Lire plus'); ?>
				</div>
			
				<div class="postFooter">
				<?php comments_popup_link('Aucun Commentaire', '1 Commentaire', 'Commentaires: %'); ?>
				</div>
			</div>
			
			<?php comments_template(); ?>
			
			<?php endwhile; ?>
			
			<ul class="postScroll">
				<li class="next"><?php next_posts_link('Précédents') ?></li>
				<li class="prev"><?php previous_posts_link('Suivants') ?></li>
			</ul>
			
		<?php else : ?>
		
			<h3>Erreur 404. Non Trouvé.</h3>
			
		<?php endif; ?>
			
		</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>