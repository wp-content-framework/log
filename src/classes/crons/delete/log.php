<?php
/**
 * WP_Framework_Log Crons Delete Log
 *
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Log\Classes\Crons\Delete;

use WP_Framework_Cron\Classes\Crons\Base;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Log
 * @package WP_Framework_Log\Classes\Crons\Delete
 */
class Log extends Base {

	/**
	 * @return int
	 */
	protected function get_interval() {
		if ( ! $this->app->log->is_valid() ) {
			return - 1;
		}

		return $this->apply_filters( 'delete_log_interval' );
	}

	/**
	 * @return string
	 */
	protected function get_hook_name() {
		return $this->get_hook_prefix() . 'delete_log';
	}

	/**
	 * execute
	 */
	protected function execute() {
		$this->app->log( 'delete logs', $this->app->log->delete_old_logs() );
	}
}
