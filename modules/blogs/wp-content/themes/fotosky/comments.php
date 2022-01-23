<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>

			<p class="nocomments">Cet article est protégé par mot de passe. Entrer le mot de passe pour voir les commentaires.<p>

			<?php
			return;
		}
	}

	/* This variable is for alternating comment background */
	$oddcomment = 'alt';
?>

<!-- You can start editing here. -->
<a id="comments"></a>
<div id="blog_comm">
<?php if ($comments) : ?>
	<h2>Commentaires</h2>

	<?php foreach ($comments as $comment) : ?>
		<div class="comm_panel">
			<b><?php comment_author_link() ?></b> le <?php comment_date('j F, Y') ?> à <?php comment_time() ?> #
		</div>
		<div class="comm_text">
			<?php if ($comment->comment_approved == '0') : ?>
				Votre commentaire est en attente de modération.
			<?php endif; ?>
			<?php comment_text() ?>
		</div>
		<br />

	<?php endforeach; /* end for each comment */ ?>

 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments">Les commentaires sont clos.</p>

	<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<div id="comments_from_bg">
<a id="#respond"></a>
	<div id="comm_post_title">
		Poster un Commentaire
	</div>
	
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	<p>Vous devez être <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">connectés</a> pour poster un commentaire.</p>
	<?php else : ?>
	
	<div id="comm_post_form">
	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
	
	
	<table cellspacing="0" cellpadding="0">
	<?php if ( $user_ID ) : ?>
		<tr>
			<td colspan="2">
				<p>Connecté en tant que <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Déconnexion">Déconnexion &raquo;</a></p>
			</td>
		</tr>
	<?php else : ?>
		<tr>
			<td class="right">
				Nom:
			</td>
			<td>
				<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
			</td>
		</tr>
		<tr>
			<td class="right">
				E-mail:
			</td>
			<td>
				<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
			</td>
		</tr>
		<tr>
			<td class="right">
				Site Web:
			</td>
			<td>
				<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
			</td>
		</tr>
	
	<?php endif; ?>
	
	<!--<p><small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small></p>-->
		<tr>
			<td class="right">
				Commentaire:
			</td>
			<td>
				<textarea name="comment" id="comment" cols="40" rows="5" tabindex="4"></textarea>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<input type="image" class="sub" src="<?php bloginfo('stylesheet_directory'); ?>/images/sub.gif" />
			</td>
		</tr>
	</table>
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	
	<?php do_action('comment_form', $post->ID); ?>
	
	</form>
	</div>
</div>
<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>
</div>
