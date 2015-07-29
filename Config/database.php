<?php
/**
 * Database connection configuration
 *
 * This switches the values between test mode and real mode.
 */

use \Com\WordPress\TownyPooky\Network\VirtualHost;

/**
 * Detect on which mode the service is running.
 *
 * @see SERVER_NAME_FORCE You can force one with defining it on db-init.php
 * @access private This variable should be used only on this file
 * @var string You can't use integer nor static name because of SERVER_NAME_FORCE
 */
$__server_name = call_user_func(function(){
    if(defined('SERVER_NAME_FORCE')) return SERVER_NAME_FORCE;
    $host = env('SERVER_NAME');
    if(VirtualHost::isLocal($host)) return 'test';
    return 'real';
});

/**
 * If $__server_name is invalid, take it `test`.
 */
switch($__server_name){
	case 'real':
		class DATABASE_CONFIG {
			public $default = array(
				'datasource' => 'Database/Mysql',
				'persistent' => false,
				'host' => 'host',
				'login' => 'username',
				'password' => 'password',
				'database' => 'synary',
				'prefix' => '',
				'encoding' => 'utf8'
			);
		}
		break;
	default: // case 'test':
		class DATABASE_CONFIG {
			public $default = array(
				'datasource' => 'Database/Mysql',
				'persistent' => false,
				'host' => 'host',
				'login' => 'username',
				'password' => 'password',
				'database' => 'synary',
				'prefix' => '',
				'encoding' => 'utf8'
			);
		}
		break;
}


