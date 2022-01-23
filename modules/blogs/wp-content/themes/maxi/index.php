<?php get_header(); ?>
		<div id="main">
			<div id="content">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<div class="post" id="post-<?php the_ID(); ?>">
					<p class="day-date"><?php the_time('l j F Y') ?></p>
					<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Lien permanent vers <?php the_title(); ?>"><?php the_title(); ?></a></h2>
					<p class="post-info">Par <?php the_author() ?> le <?php the_time('l j F Y, H:i') ?> - <?php the_category(', ') ?></p>
					<?php if(strlen(STP_GetPostTags())>4) : ?>
					<ul class="post-tags">
						<?php STP_PostTags('<li><a href="%fulltaglink%" title="Search site for %tagname%" rel="tag">%tagname%</a></li>',false,'',''); ?>
					</ul>
					<?php endif; ?>
					<div class="post-content">
						<?php the_content('Lire la suite...'); ?>
					</div>
					<p class="post-info-co">
						<?php edit_post_link('Editer', '', ' | '); ?>
        				<a href="<?php comments_link(); ?>" class="comment_count"><?php comments_number('aucun commentaire','1 commentaire','% commentaires'); ?></a>
					</p>
				</div>
		<?php endwhile; ?>
				<p class="pagination">
					<?php posts_nav_link(' &#183; ', 'billets suivants &raquo;', '&laquo; billets précédents'); ?>
				</p>
		<?php else : ?>
				<h2>Not Found</h2>
				<p>Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>
	<?php endif; ?>
			</div>
		</div>
<?php get_sidebar(); ?>
	</div>
<?php get_footer(); ?>