<?php get_header(); ?>
		<div id="main">
			<div id="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post" id="post-<?php the_ID(); ?>">
					<h2 class="post-title"><?php the_title(); ?></h2>
					<p class="post-info">Par <?php the_author() ?> le <?php the_time('l j F Y, H:i') ?> - <?php the_category(', ') ?></p>
					<?php if(strlen(STP_GetPostTags())>4) : ?>
					<ul class="post-tags">
						<?php STP_PostTags('<li><a href="%fulltaglink%" title="Search site for %tagname%" rel="tag">%tagname%</a></li>',false,'',''); ?>
					</ul>
					<?php endif; ?>
					<div class="post-content">
						<?php the_content('Lire la suite...'); ?>
					</div>
					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				</div>
			</div>
		<?php endwhile; endif; ?>
	<?php edit_post_link('Editer', '<p>', '</p>'); ?>
		</div>
<?php get_sidebar(); ?>
	</div>
<?php get_footer(); ?>
