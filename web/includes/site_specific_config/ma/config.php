<?php
	defined( 'SITE_NAME' ) ? null : define( 'SITE_NAME', 'Member\'s Area' );
	defined( 'DEFAULT_THEME' ) ? null : define( 'DEFAULT_THEME', 'ma' );
	defined( 'PROFILE_USER' ) ? null : define( 'PROFILE_USER', 89 );
	defined( 'AUTHENTICATION_REQUIRED' ) ? null : define( 'AUTHENTICATION_REQUIRED', true );


	$page_title = SITE_NAME;

	require_once( "{$environment}_config.php" );
?>