<?php 
/* 	This is comment.phps by Christian Montoya, http://www.christianmontoya.com

	Available to you under the do-whatever-you-want license. If you like it, 
	you are totally welcome to link back to me. 
	
	Use of this code does not grant you the right to use the design or any of the 
	other files on my site. Beyond this file, all rights are reserved, unless otherwise noted. 
	
	Enjoy!
*/
?>

<!-- Comments code provided by christianmontoya.com -->

<?php if (!empty($post->post_password) && $_COOKIE['wp-postpass_'.COOKIEHASH]!=$post->post_password) : ?>
	<p id="comments-locked" class="h3comm">Entrez le mot de passe pour voir les commentaires.</p>
<?php return; endif; ?>

<?php /* if (pings_open()) : ?>
	<p id="respond"><span id="trackback-link">
		<a href="<?php trackback_url() ?>" rel="trackback">Get a Trackback link</a>
	</span></p>
<?php endif; */ ?>

<?php if ($comments) : ?>

<?php 

	/* Author values for author highlighting */
	/* Enter your email and name as they appear in the admin options */
	$author = array(
			"highlight" => "highlight",
			"email" => "VOTRE EMAIL ICI",
			"name" => "VOTRE NOM ICI"
	); 

	/* Count the totals */
	$numPingBacks = 0;
	$numComments  = 0;

	/* Loop throught comments to count these totals */
	foreach ($comments as $comment) {
		if (get_comment_type() != "comment") { $numPingBacks++; }
		else { $numComments++; }
	}
	
	/* Used to stripe comments */
	$thiscomment = 'odd'; 
?>

<?php

	/* This is a loop for printing pingbacks/trackbacks if there are any */
	if ($numPingBacks != 0) : ?>

	<div class="trackback">
		<h3><?php _e($numPingBacks); ?> Rétroliens</h3>
		<ul>

<?php foreach ($comments as $comment) : ?>
<?php if (get_comment_type()!="comment") : ?>

			<li id="comment-<?php comment_ID() ?>" class="<?php _e($thiscomment); ?>">
			<?php comment_type(__('Commentaire'), __('Rétrolien'), __('Pingback')); ?>: 
			<?php comment_author_link(); ?> le <?php comment_date(); ?>
			</li>
	
	<?php if('odd'==$thiscomment) { $thiscomment = 'even'; } else { $thiscomment = 'odd'; } ?>
	
<?php endif; endforeach; ?>

		</ul>
	</div>


<?php endif; ?>

<?php 

	/* This is a loop for printing comments */
	if ($numComments != 0) : ?>

	<h3 class="h3comm"><?php _e($numComments); ?> Commentaires</h3>
<!-- -->
	
	<?php foreach ($comments as $comment) : ?>
	<?php if (get_comment_type()=="comment") : ?>
	
		<div id="comment-<?php comment_ID(); ?>" class="<?php 
		
		/* Highlighting class for author or regular striping class for others */
		
		/* Get current author name/e-mail */
		$this_name = $comment->comment_author;
        $this_email = $comment->comment_author_email;
        
        /* Compare to $author array values */
        if (strcasecmp($this_name, $author["name"])==0 && strcasecmp($this_email, $author["email"])==0)
			_e($author["highlight"]); 
		else 
			_e($thiscomment); 
		
		?>">
			<p class="info">
<?php /* If you want to use gravatars, they go somewhere around here */ ?>
				<b><?php comment_author_link() ?></b> le <?php comment_date('j F Y') ?> @ <?php comment_date('G:i') ?>
			</p>
<?php /* Or maybe put gravatars here. The typical thing is to float them in the CSS */ 
	/* Typical gravatar call: 
		<img src="<?php gravatar("R", 80, "YOUR DEFAULT GRAVATAR URL"); ?>" 
		alt="" class="gravatar" width="80" height="80">
	*/ ?>
				<?php comment_text(); ?>
		</div>
		
	<?php if('odd'==$thiscomment) { $thiscomment = 'even'; } else { $thiscomment = 'odd'; } ?>
	
	<?php endif; endforeach; ?>
	
<!-- -->
	
	<?php endif; ?>
	
<?php else : 

	/* No comments at all means a simple message instead */ 
?>

<!--	<h3 class="h3comm">No Comments Yet</h3>  -->

<?php endif; ?>

<?php if (comments_open()) : ?>

<?php /* This would be a good place for live preview... 
	<div id="live-preview">
		<h2 class="comments-header">Live Preview</h2>
		<?php live_preview(); ?>
	</div>
 */ ?>

	<div id="comments-form">
	
	<h3 class="h3comm">Laisser une Réponse</h3>
	
	<?php if (get_option('comment_registration') && !$user_ID ) : ?>
		<p id="comments-blocked">Vous devez être <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=
		<?php the_permalink(); ?>">connectés</a> pour poster une réponse.</p>
	<?php else : ?>

	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

	<?php if ($user_ID) : ?>
	
		<p class="skinnedArea">
		<label for="comment">Connecté en tant que<a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php">
		<?php echo $user_identity; ?></a>. 
		<a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Déconnexion">Déconnexion</a></label>
	
	<?php else : ?>
	
		<p class="skinned">
		<label for="author">Nom:<?php if ($req) _e(' (requis)'); ?></label>
		<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" />
		</p>
		
		<p class="skinned">
		<label for="email">E-mail (ne sera pas publié)<?php if ($req) _e(' (requis)'); ?></label>
		<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="" />
		</p>
		
		<p class="skinned">
		<label for="url">Site Web:</label>
		<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="" />
		</p>
		
		<p class="skinnedArea">
		<label for="comment">Votre Commentaire:</label>
	
	<?php endif; ?>

	<?php /* You might want to display this: 
		<p>XHTML: Вы можете использовать следующие теги: <?php echo allowed_tags(); ?></p> */ ?>

		<textarea name="comment" id="comment" rows="" cols=""></textarea>
		</p>
		
		<?php /* Buttons are easier to style than input[type=submit], 
				but you can replace: 
				<button type="submit" name="submit" id="sub">Submit</button>
				with: 
				<input type="submit" name="submit" id="sub" value="Submit" />
				if you like */ 
		?>
		<input type="submit" name="submit" id="sub" value="Poster" />
		<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>">
	
	<?php do_action('comment_form', $post->ID); ?>

	</form>
	</div>

<?php endif; // If registration required and not logged in ?>

<?php else : // Comments are closed ?>
	<h3 id="comments-closed"  class="h3comm">Les commentaires sont clos.</h3>
<?php endif; ?>