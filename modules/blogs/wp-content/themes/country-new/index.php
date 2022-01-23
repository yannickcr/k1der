<?php get_header(); ?>
   <div id="content">
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
    <h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Lien Permanent vers <?php the_title(); ?>"><?php the_title(); ?></a></h2>
	<p class="infos">Par <?php the_author() ?> le <?php the_time('l j F Y à H:i') ?> - <?php the_category(', ') ?></p>
<?php if(strlen(STP_GetPostTags())>4) : ?>
    <ul class="post-tags">
<?php STP_PostTags('     <li><a href="%fulltaglink%" title="Search site for %tagname%" rel="tag">%tagname%</a></li>',false,'',''); ?>
    </ul>
<?php endif; ?>
	<div class="post">
<?php the_content('Lire la suite &raquo;'); ?>
    </div>
    <p class="post-info-co">
<?php edit_post_link('Editer', '', ' | '); ?>
     <a href="<?php comments_link(); ?>" class="comment_count"><?php comments_number('aucun commentaire','1 commentaire','% commentaires'); ?></a>
    </p>
<?php endwhile; ?>
    <ul id="pagination">
     <li class="prev"><?php next_posts_link('&laquo; Billets précédents') ?></li><!--
     --><li class="next"><?php previous_posts_link('Billets suivants &raquo;') ?></li>
    </ul>
<?php else : ?>
    <h3 class="center">Not Found</h3>
	<p class="center">Sorry, but you are looking for something that isn't here.</p>
<?php include (TEMPLATEPATH . "/searchform.php"); ?>
<?php endif; ?>
   </div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>