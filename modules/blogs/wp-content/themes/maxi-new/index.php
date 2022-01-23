<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Lien Permanent vers <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_time('l j F Y') ?>  par <?php the_author() ?></small>

				<div class="entry">
					<?php the_content('Lire la suite &raquo;'); ?>
				</div>

				<p class="postmetadata">Posté dans <?php the_category(', ') ?> | <?php edit_post_link('Editer', '', ' | '); ?>  <?php comments_popup_link('Aucun Commentaire &#187;', '1 Commentaire &#187;', '% Commentaires &#187;'); ?></p>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Billets précédents') ?></div>
			<div class="alignright"><?php previous_posts_link('Billets suivants &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
