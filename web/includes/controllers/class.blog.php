<?php
	class BlogCtrl extends ActionCtrl {
		public static function load () {
			global $session, $lang, $page_title, $current_page;

			$theme = static::$theme;

			$current_page = static::$current_page;
			$current_page_short = static::$current_page_short;

			$current_user_img = "images/{$theme}/default_profile_pic.jpg";

			if ( isset( $session->user_id ) ) {
				$current_user = User::find_by_id( $session->user_id );

				$current_user_img = "UPS/{$current_user->id}/profile.jpg";
				$current_user_img = file_exists( $current_user_img ) ? $current_user_img : "views/{$theme}/authenticated/images/default_profile_pic.jpg";

				$posts = $current_user->get_newsfeed_posts();

				// var_dump( $current_user );
				// exit;

				foreach ( $posts as $post ) {
					$post->you_like = Likes::you_like( $current_user->id, $post );
					$post->comments = Comments::get_comments_for_item( $post );

					foreach ( $post->comments as $comment ) {
						$comment->you_like = Likes::you_like( $current_user->id, $comment );
					}
				}
			} else if ( defined( 'PROFILE_USER' ) ) {
				$current_user = User::find_by_id( PROFILE_USER );

				$current_user_img = "UPS/{$current_user->id}/profile.jpg";
				$current_user_img = file_exists( $current_user_img ) ? $current_user_img : "views/{$theme}/authenticated/images/default_profile_pic.jpg";

				$posts = $current_user->get_newsfeed_posts();

				foreach ( $posts as $post ) {
					$post->you_like = Likes::you_like( $current_user->id, $post );
					$post->comments = Comments::get_comments_for_item( $post );

					foreach ( $post->comments as $comment ) {
						$comment->you_like = Likes::you_like( $current_user->id, $comment );
					}
				}
			}

			include_once( static::load_template() );
		}
	}
?>