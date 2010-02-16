<?php

class RatingLib extends TikiDb_Bridge
{
	function record_vote( $type, $objectId, $score, $time = null ) {
		$target = $this->get_current_user();
		return $this->record_user_vote( $target, $type, $objectId, $score, $time );
	}

	function get_vote( $type, $objectId ) {
		$target = $this->get_current_user();
		return $this->get_user_vote( $target, $type, $objectId );
	}

	function get_token( $type, $objectId ) {
		switch( $type ) {
		case 'article':
			return "article$objectId";
		case 'comment':
			return "comment$objectId";
		case 'wiki page':
			if( is_numeric( $objectId ) ) {
				return "wiki$objectId";
			}

			break;
		case 'test':
			return "test.$objectId";
		}

		return null;
	}

	function record_user_vote( $user, $type, $objectId, $score, $time = null ) {
		global $tikilib;

		if( ! $this->is_valid( $type, $score ) ) {
			return false;
		}

		if( is_null( $time ) ) {
			$time = time();
		}

		$ip = $tikilib->get_ip_address();
		$token = $this->get_token( $type, $objectId );

		if( is_null( $token ) ) {
			return false;
		}

		$this->query( 'INSERT INTO `tiki_user_votings` ( `user`, `ip`, `id`, `optionId`, `time` ) VALUES( ?, ?, ?, ?, ? )',
			array( $user, $ip, $token, $score, $time ) );

		return true;
	}

	function record_anonymous_vote( $sessionId, $type, $objectId, $score, $time = null ) {
		return $this->record_user_vote( $this->session_to_user( $sessionId ), $type, $objectId, $score, $time );
	}

	function is_valid( $type, $value ) {
		$options = $this->get_options( $type );

		return in_array( $value, $options );
	}

	function get_options( $type ) {
		$pref = 'rating_default_options';

		switch( $type ) {
		case 'wiki page':
			$pref = 'wiki_simple_ratings_options';
			break;
		case 'article':
			$pref = 'article_user_rating_options';
			break;
		}

		global $tikilib;
		return $tikilib->get_preference( $pref, range( 1, 5 ), true );
	}

	function get_user_vote( $user, $type, $objectId ) {
		$result = $this->fetchAll( 'SELECT `optionId` FROM `tiki_user_votings` WHERE `user` = ? AND `id` = ? ORDER BY `time` DESC',
			array( $user, $this->get_token( $type, $objectId ) ), 1 );

		if( count( $result ) == 1 ) {
			return (float) $result[0]['optionId'];
		}
	}

	function get_anonymous_vote( $sessionId, $type, $objectId ) {
		return $this->get_user_vote( $this->session_to_user( $sessionId ), $type, $objectId );
	}

	private function session_to_user( $sessionId ) {
		return "anonymous\0$sessionId";
	}

	private function get_current_user() {
		global $user;

		if( $user ) {
			return $user;
		} else {
			return $this->session_to_user( session_id() );
		}
	}
}

global $ratinglib; $ratinglib = new RatingLib;

