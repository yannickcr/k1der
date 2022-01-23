<?php get_header(); ?>
		<div id="main">
			<div id="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<p id="navlinks">
					<?php previous_post_link('&laquo; %link') ?> | <?php next_post_link('%link &raquo;') ?>
				</p>
				<div class="post" id="post-<?php the_ID(); ?>">
   					<h2 class="post-title"><?php the_title(); ?></h2>
					<p class="post-info">Par <?php the_author() ?> le <?php the_time('l j F Y, H:i') ?> - <?php the_category(', ') ?> - <a href="<?php the_permalink() ?>" rel="bookmark" title="Lien permanent vers <?php the_title(); ?>">Lien permanent</a></p>
					<?php if(strlen(STP_GetPostTags())>4) : ?>
					<ul class="post-tags">
						<?php STP_PostTags('<li><a href="%fulltaglink%" title="Search site for %tagname%" rel="tag">%tagname%</a></li>',false,'',''); ?>
					</ul>
					<?php endif; ?>
					<div class="post-content">
						<?php the_content('Lire la suite...'); ?>
					</div>
					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php /*
					<p class="postmetadata alt">
						<small>
							This entry was posted
							<?php *//* This is commented, because it requires a little adjusting sometimes.
								You'll need to download this plugin, and follow the instructions:
								http://binarybonsai.com/archives/2004/08/17/time-since-plugin/ */
								/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); echo time_since($entry_datetime); echo ' ago'; *//* ?>
							on <?php the_time('l, F jS, Y') ?> at <?php the_time() ?>
							and is filed under <?php the_category(', ') ?>.
							You can follow any responses to this entry through the <?php comments_rss_link('RSS 2.0'); ?> feed.
	
							<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
								// Both Comments and Pings are open ?>
								You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(true); ?>" rel="trackback">trackback</a> from your own site.
	
							<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
								// Only Pings are Open ?>
								Responses are currently closed, but you can <a href="<?php trackback_url(true); ?> " rel="trackback">trackback</a> from your own site.
	
							<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
								// Comments are open, Pings are not ?>
								You can skip to the end and leave a response. Pinging is currently not allowed.
	
							<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
								// Neither Comments, nor Pings are open ?>
								Both comments and pings are currently closed.
	
							<?php } edit_post_link('Edit this entry.','',''); ?>
						</small>
					</p>
					*/ ?>
				</div>
		<?php comments_template(); ?>
			</div>
		</div>
	<?php endwhile; else: ?>
		<p>Sorry, no posts matched your criteria.</p>
<?php endif; ?>
<?php get_sidebar(); ?>
	</div>
<?php get_footer(); ?>