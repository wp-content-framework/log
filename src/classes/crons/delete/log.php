<?php
/**
 * WP_Framework_Core Crons Delete Log
 *
 * @version 0.0.1
 * @author technote-space
 * @since 0.0.1
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Classes\Crons\Delete;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Log
 * @package WP_Framework_Core\Classes\Crons\Delete
 */
class Log extends \WP_Framework_Core\Classes\Crons\Base {

	/**
	 * @return int
	 */
	protected function get_interval() {
		if ( ! $this->app->log->is_valid() ) {
			return - 1;
		}

		return $this->apply_filters( 'delete___log_interval' );
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
