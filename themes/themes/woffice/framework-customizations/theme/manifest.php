<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['id'] = 'woffice';

$manifest['name']         = __('Woffice', 'woffice');
$manifest['uri']          = 'themeforest.net/user/alkaweb';
$manifest['description']  = __('Another awesome WordPress theme', 'woffice');
$manifest['version']      = '2.8.4';
$manifest['author']       = 'Alkalab';
$manifest['author_uri']   = '//themeforest.net/user/alkaweb';

$manifest['supported_extensions'] = array(
	'megamenu' => array(),
	'backups' => array(),
);
