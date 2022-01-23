<?php get_header(); ?>
   <div id="content">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <h2 id="post-<?php the_ID(); ?>"><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Lien Permanent vers <?php the_title(); ?>"><?php the_title(); ?></a></h2>
	<p class="infos">Par <?php the_author() ?> le <?php the_time('l j F Y à H:i') ?> - <?php the_category(', ') ?></p>
<?php if(strlen(STP_GetPostTags())>4) : ?>
    <ul class="post-tags">
<?php STP_PostTags('     <li><a href="%fulltaglink%" title="Search site for %tagname%" rel="tag">%tagname%</a></li>',false,'',''); ?>
    </ul>
<?php endif; ?>
	<div class="post">
<?php the_content('Lire la suite...'); ?>
<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
    </div>
<?php comments_template(); ?>
<?php endwhile; else: ?>
    <p>Sorry, no posts matched your criteria.</p>
<?php endif; ?>
   </div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
