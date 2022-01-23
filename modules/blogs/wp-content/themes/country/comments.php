<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>

			<p class="nocomments">This post is password protected. Enter the password to view comments.</p>

			<?php
			return;
		}
	}

	/* This variable is for alternating comment background */
	//$oddcomment = 'class="alt" ';
?>

<!-- You can start editing here. -->

<?php if ($comments) : ?>
				<div id="comments">
					<h3><?php comments_number('Aucun commentaire', 'un commentaire', '% commentaires' );?></h3>
					<dl>
					<?php $i=1; foreach ($comments as $comment) : ?>
						<?php $me=(get_the_author()==get_comment_author())?' class="me"':''; ?>
						<dt id="comment-<?php comment_ID() ?>"<?php echo $me; ?>>
						<a href="#comment-<?php comment_ID() ?>" class="comment-number"><?php echo $i++; ?>. </a>Le <?php comment_date('l j F Y, H:i'); ?> par <?php comment_author_link(); ?><?php edit_comment_link('Editer',' | ',''); ?></dt>
						<?php if ($comment->comment_approved == '0') : ?>
						<dd><em>Your comment is awaiting moderation.</em></dd>
						<?php endif; ?>
						<dd<?php echo $me; ?>>
							<img class="gravatar" src="<?php echo gravatar("R", 40, "http://www.k1der.net/blog/country/wp-content/themes/country/images/blank.gif", "000000"); ?>" alt="" />
							<?php comment_text() ?>
						</dd>
						<?php
							/* Changes every other comment to a different class */
							//$oddcomment = ( empty( $oddcomment ) ) ? 'class="alt" ' : '';
						?>
						<?php endforeach; /* end for each comment */ ?>
					</dl>
				</div>
				<?php else : // this is displayed if there are no comments so far ?>
				<?php if ('open' == $post->comment_status) : ?>
					<!-- If comments are open, but there are no comments. -->
				<?php else : // comments are closed ?>
					<!-- If comments are closed. -->
					<p class="nocomments">Comments are closed.</p>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ('open' == $post->comment_status) : ?>
				<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="comment-form">
					<h3>Ajouter un commentaire</h3>
					<fieldset>
						<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
						<p class="field">You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to post a comment.</p>
						<?php else : ?>
							<?php if ( $user_ID ) : ?>
						<p class="field">Connecté en tant que <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Se déconnecter">Déconnexion</a></p>
							<?php else : ?>
						<p class="field">
							<label for="author">Nom <?php if ($req) echo "(requis)"; ?></label>
							<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
						</p>
						<p class="field">
							<label for="email">E-mail (non publié) <?php if ($req) echo "(requis)"; ?></label>
							<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
						</p>
						<p class="field">
							<label for="url">Site web</label>
							<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
						</p>
					<?php endif; ?>
					<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->
						<p class="field">
							<textarea name="comment" id="comment" cols="65" rows="10" tabindex="4"></textarea>
						</p>
						<p class="field">
							<input name="submit" type="submit" class="submit" tabindex="5" value="Envoyer" />
							<input name="acp-preview" type="button" id="acp-preview" tabindex="6" value="Aper&ccedil;u" />
							<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
						</p>
					</fieldset>
					<?php do_action('comment_form', $post->ID); ?>
				</form>
<?php endif; // If registration required and not logged in ?>
<?php endif; // if you delete this the sky will fall on your head ?>