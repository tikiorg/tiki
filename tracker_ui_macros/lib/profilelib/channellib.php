<?php

class Tiki_Profile_ChannelList
{
	private $channels = array();

	public static function fromConfiguration( $string ) // {{{
	{
		$list = new self;

		$string = str_replace( "\r", '', $string );
		$lines = explode( "\n", $string );

		foreach( $lines as $line ) {
			$parts = explode( ',', $line );
			if( count( $parts ) < 3 )
				continue;
			elseif( count( $parts ) == 3 )
				$parts[] = 'Admins';

			$parts = array_map( 'trim', $parts );
			list( $name, $domain, $profile ) = array_slice( $parts, 0, 3 );
			$groups = array_slice( $parts, 3 );

			$list->channels[ $name ] = array(
				'domain' => $domain,
				'profile' => $profile,
				'groups' => $groups,
			);
		}

		return $list;
	} // }}}

	function canExecuteChannels( array $channelNames, array $groups ) // {{{
	{
		foreach( $channelNames as $channel ) {
			if( ! array_key_exists( $channel, $this->channels ) )
				return false;
			
			// At least one match is required
			if( count( array_intersect( $groups, $this->channels[$channel]['groups'] ) ) == 0 )
				return false;
		}

		return true;
	} // }}}

	function getProfiles( array $channelNames ) // {{{
	{
		$profiles = array();

		foreach( $channelNames as $channelName ) {
			$info = $this->channels[$channelName];
			
			if( $profile = Tiki_Profile::fromNames( $info['domain'], $info['profile'] ) )
				$profiles[$channelName] = $profile;
		}

		return $profiles;
	} // }}}
}

?>
