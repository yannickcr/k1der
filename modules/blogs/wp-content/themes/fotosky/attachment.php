<?php get_header(); ?>

  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php $attachment_link = get_the_attachment_link($post->ID, true, array(450, 800)); // This also populates the iconsize for the next line ?>
<?php $_post = &get_post($post->ID); $classname = ($_post->iconsize[0] <= 128 ? 'small' : '') . 'attachment'; // This lets us style narrow icons specially ?>

				<div class="item_class">
					<div class="item_class_title">
						<div class="item_class_title_text">
						
							<div class="date">
								<div class="date_month"><?php the_time('M') ?></div>
								<div class="date_day"><?php the_time('d') ?></div>
							</div>
							<div class="titles">
								<div class="top_title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Lien Permanent vers <?php the_title(); ?>"><?php the_title(); ?></a></div>
								<div class="end_title">Classé dans (<?php the_category(', ') ?>) par <?php the_author() ?> le <?php the_time('d-m-Y') ?></div>
							</div>
							
						</div>
					</div>
					<div class="item_class_text">
						<?php the_content('Lire la suite de cet article &raquo;'); ?>
					</div>
					<div class="item_class_panel">
						<div>
							<div class="links_left">
								<span class="panel_comm"><?php comments_popup_link('(0) Commentaire', '(1) Commentaire', '(%) Commentaires'); ?></span>&nbsp;&nbsp;&nbsp;
						
							<?php edit_post_link('Editer', '', ''); ?>
							</div>
							<div class="links_right">
								<a href="<?php the_permalink() ?>" class="panel_read">Lire Plus</a>&nbsp;&nbsp;&nbsp;	</div>
						</div>
					</div>
				</div>

	<?php comments_template(); ?>

	<?php endwhile; else: ?>

		<p>Désolé, rien ne correspond à votre critère.</p>

<?php endif; ?>

<?php get_footer(); ?>
