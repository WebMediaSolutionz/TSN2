<?php

	class Item extends DatabaseObject {
		public function get_likers () {
			global $session;

			return Likes::likers( $this );
		}

		public function get_commenters () {
			global $session;

			return Comments::commenters( $this );
		}

		public function get_stakeholders () {
			global $session;

			$stakeholders = $likers = Likes::likers( $this, $session->user_id );
			$commenters = Comments::commenters( $this, $session->user_id );

			$found = false;

			foreach ( $commenters as $commenter ) {
				foreach ( $stakeholders as $stakeholder ) {
					if ( $commenter->id === $stakeholder->id ) {
						$found = true;
					}
				}

				if ( !$found ) {
					$stakeholders[] = $commenter;
				}
			}

			if ( $session->user_id != $this->user_id ) {
				$user = User::find_by_id( $this->user_id );
				$stakeholders[] = $user;
			}
			
			return $stakeholders;
		}

		public static function get_last_few_of_user ( $user_id, $number = 5) {
			global $DB;

			$sql = "SELECT * FROM " . static::$table_name . " ";
			$sql .= "WHERE user_id=" . $DB->escape_value( $user_id ) . " ";
			$sql .= "LIMIT " . $DB->escape_value( $number );

			$items = static::find_by_sql( $sql );

			return $items;
		}
	}

?>