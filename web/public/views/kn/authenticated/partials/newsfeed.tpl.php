<?php if ( $current_user->id === $profile_user->id ) { ?>
	<div class="status_textarea">
		<form action="blog.php?action=post_to_wall" method="post">
			<textarea id="status_updater" name="value"></textarea>

			<input type="hidden" name="current_user_fullname" value="<?php echo $current_user->full_name(); ?>" />
			<input type="hidden" name="wall_id" value="<?php echo $current_user->id; ?>" />

			<br />

			<input class="js-submit_post" id="post_to_wall" type="submit" name="submit" value="<?php echo $lang[ 'post_to_wall' ]; ?>" />
		</form>
	</div>

	<br><br>
<?php } ?>

<div id="wall" class="left">
	<?php foreach( $posts as $post ) { ?>
		<?php include( "post_type_{$post->post_type}.tpl.php" ); ?>
	<?php } ?>
</div>