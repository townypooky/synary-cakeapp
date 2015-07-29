<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

//require 'webroot' . DIRECTORY_SEPARATOR . 'index.php';

/**
 * If this file is called from web,
 * there is some problem in directory installation.
 */
header('HTTP/1.0 500 Internal Server Error');
die('<h1>500 Internal Server Error</h1><p>Installation error: this page should NOT be called.</p>');

