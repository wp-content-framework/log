<?php
/**
 * WP_Framework_Log Classes Controller Admin Logs
 *
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Log\Classes\Controllers\Admin;

use WP_Framework_Admin\Classes\Controllers\Admin\Base;
use WP_Framework_Log\Traits\Package;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Logs
 * @package WP_Framework_Log\Classes\Controllers\Admin
 */
class Logs extends Base {

	use Package;

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return $this->app->log->is_valid() ? $this->apply_filters( 'logs_page_priority', 999 ) : -1;
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return $this->apply_filters( 'logs_page_title', 'Logs' );
	}

	/**
	 * @return array
	 */
	protected function get_view_args() {
		$query = $this->apply_filters( 'log_page_query_name', 'p' );
		$total = $this->table( '__log' )->count();
		$limit = $this->apply_filters( 'get___log_limit', 20 );
		if ( $limit < 1 ) {
			$limit = 1;
		}
		$total_page = max( 1, ceil( $total / $limit ) );
		$page       = max( 1, min( $total_page, $this->app->input->get( $query, 1 ) ) );
		$offset     = ( $page - 1 ) * $limit;
		$start      = $total > 0 ? $offset + 1 : 0;
		$end        = $total > 0 ? min( $offset + $limit, $total ) : 0;

		return [
			'logs'       => $this->table( '__log' )->limit( $limit )->offset( $offset )->order_by_desc( 'created_at' )->order_by_desc( 'id' )->get(),
			'total'      => $total,
			'total_page' => $total_page,
			'page'       => $page,
			'offset'     => $offset,
			'p'          => $query,
			'start'      => $start,
			'end'        => $end,
		];
	}
}
