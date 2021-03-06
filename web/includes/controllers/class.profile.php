<?php
	class ProfileCtrl extends FriendshipCtrl {
		public static function load () {
			global $session, $lang, $page_title, $redirect_destination, $current_page;

			$theme = static::$theme;

			$current_user = User::find_by_id( $session->user_id );
			$profile_user = $user = null;
			
			$current_page = static::$current_page;
			$current_page_short = static::$current_page_short;

			if ( defined( 'PROFILE_USER' ) ) {
				$profile_user = User::find_by_id( PROFILE_USER );
			}
				
			if ( isset( $_GET[ 'profile_id' ] ) && ( $_GET[ 'profile_id' ] == $current_user->id ) || !isset( $_GET[ 'profile_id' ] ) ) {
				$profile_user = $current_user;
				$mode = isset( $_GET[ 'mode' ] ) ? $_GET[ 'mode' ] : null;
				$template = ( $mode === 'edit' ) ? 'profile_edit' : 'profile_self';
				$redirect_destination .= "profile_id={$profile_user->id}";
			} else {
				$profile_user = User::find_by_id( $_GET[ 'profile_id' ] );
				$template = 'profile';
			}

			$posts = $profile_user->get_wall_posts();
			$friends = Utils::randomize( $profile_user->get_friends() );
			$albums = Album::get_albums_for_user( $profile_user->id );

			foreach ( $albums as $album ) {
				$album->load_pictures();
			}

			foreach ( $posts as $post ) {
				$post->you_like = Likes::you_like( $current_user->id, $post );
				$post->comments = Comments::get_comments_for_item( $post );

				foreach ( $post->comments as $comment ) {
					$comment->you_like = Likes::you_like( $current_user->id, $comment );
				}
			}

			$user = $profile_user;

			// $author = User::find_by_id( $post->user_id );

			// $author_img = "UPS/{$author->id}/profile.jpg";
			$profile_img = "UPS/{$profile_user->id}/profile.jpg";
			$current_user_img = "UPS/{$current_user->id}/profile.jpg";

			$profile_img = file_exists( $profile_img ) ? $profile_img : "images/{$theme}/default_profile_pic.jpg";
			// $author_img = file_exists( $author_img ) ? $author_img : "images/{$theme}/default_profile_pic.jpg";
			$current_user_img = file_exists( $current_user_img ) ? $current_user_img : "images/{$theme}/default_profile_pic.jpg";

			if ( isset( $_POST[ 'submit' ] ) ) {
				foreach( $_POST as $key => $value ) {
					if ( $key !== 'submit' ) {
						$current_user->$key = $value;

						// $message = array(
						// 	"status"				=>			"error_message",
						// 	"prompt_code"			=>			"profile update failure"
						// );
					}
				}

				$current_user->save();

				$message = array(
					"status"				=>			"confirmation",
					"prompt_code"			=>			"profile update success"
				);

				if ( isset( $_FILES[ 'profile_picture' ] ) ) {
					Utils::upload_img( $_FILES[ 'profile_picture' ] );
				}

				$template = 'profile_self';
			}

			$page_title = ucwords( $profile_user->full_name() );

			include_once( static::load_template() );
		}

		public static function init () {
			global $session, $redirect_destination;

			static::check_session();

			if ( $session->is_logged_in() ) {
				$profile_user = $current_user = User::find_by_id( $session->user_id );

				if ( isset( $_GET[ 'profile_id' ] ) && ( $_GET[ 'profile_id' ] == $current_user->id ) || !isset( $_GET[ 'profile_id' ] ) ) {
					global $session;
				
					static::$current_page = Utils::current_page( $_SERVER[ 'REQUEST_URI' ] );
					static::$action_like_link = Utils::create_action_link( static::$current_page, 'like' );
					static::$action_unlike_link = Utils::create_action_link( static::$current_page, 'unlike' );
					static::$action_comment_link = Utils::create_action_link( static::$current_page, 'comment' );
					static::$action_delete_comment_link = Utils::create_action_link( static::$current_page, 'delete_comment' );
					static::$action_delete_post_link = Utils::create_action_link( static::$current_page, 'delete_post' );
					static::$action_share_link = Utils::create_action_link( static::$current_page, 'share' );

					if ( isset( $session->settings ) ) {
						static::$theme = $session->settings->theme;
						require_once( '../includes/lang/' . $session->settings->language . '.php' );
					} else {
						static::$theme = "facebook";
						require_once( '../includes/lang/en.php' );
					}

					$mode = isset( $_GET[ 'mode' ] ) ? $_GET[ 'mode' ] : null;
					static::$template = ( $mode === 'edit' ) ? 'profile_edit.tpl.php' : 'profile_self.tpl.php';
					$redirect_destination .= "profile_id={$profile_user->id}";

					$classname = get_called_class();

					if ( $classname !== 'LoginCtrl' ) {
						static::check_session();
					}

					static::check_authentication();

					if ( $session->is_logged_in() && isset( $_GET[ 'action' ] ) ) {
						if ( method_exists( $classname, $_GET[ 'action' ] ) ) {
							$classname::$_GET[ 'action' ]();
						}

						Utils::strip_query_string( $_SERVER[ 'REQUEST_URI' ] );
						$session->settings = static::get_settings_for( $session->user_id );
					}

					static::load();
				} else {
					parent::init();
				}
			} else {
				Utils::redirect_to( 'login.php' );
			}
		}

		public static function post_to_wall () {
			if ( isset( $_POST[ 'submit' ] ) ) {
				$post = new Post;

				$post->wall_id = $_POST[ 'wall_id' ];
				$post->user_id = $_POST[ 'author' ];
				$post->value = $_POST[ 'value' ];
				$post->post_type = 3;
				$post->post_date = Utils::mysql_datetime();

				$post->save();
			}
		}

		public static function delete_post () {
			$post = Post::find_by_id( $_GET[ 'post_id' ] );
			Comments::delete_comments_on_post( $post->id );
			$post->delete();
		}

		public static function send_message () {
			$convo = Conversations::start_new_conversation();
			$path = $_SERVER[ 'SCRIPT_FILENAME' ];
			Utils::redirect_to( "{$path}/conversation.php?convo_id={$convo->id}" );
		}

		private static function check_authentication () {
			global $session;

			if ( !$session->is_logged_in() ) {
				static::$authentication = "unauthenticated";
			} else {
				static::$authentication = "authenticated";
			}
		}
	}
?>