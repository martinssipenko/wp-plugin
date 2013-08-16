<?php

/*
Plugin Name: Metro Play
Description: Adds Games post type
Author: Martins Sipenko
*/

if ( !defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

include __DIR__ . '/autoload.php';

$metro_play_plugin = new Metro_Play_Plugin( __FILE__ );