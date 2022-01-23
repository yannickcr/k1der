<?php get_header(); ?>
  <div id="content">
   <h2><?php the_title(); ?></h2>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
   <div class="post" id="post-<?php the_ID(); ?>">
<?php the_content('Lire la suite &raquo;'); ?>
<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
   </div>
<?php endwhile; endif; ?>
<?php edit_post_link('Editer cette page.', '<p>', '</p>'); ?>
  </div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
