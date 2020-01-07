<?php
/**
 * WP_Framework_Log Classes Models Log
 *
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Log\Classes\Models;

use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Log\Traits\Package;
use WP_Framework_Presenter\Traits\Presenter;
use WP_User;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Log
 * @package WP_Framework_Log\Classes\Models
 */
class Log implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * @var bool $is_logging
	 */
	private $is_logging = false;

	/**
	 * setup shutdown
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function setup_shutdown() {
		if ( $this->apply_filters( 'capture_shutdown_error' ) && $this->is_valid() ) {
			add_action( 'shutdown', function () {
				$this->shutdown();
			}, 0 );
		}
	}

	/**
	 * setup settings
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function setup_settings() {
		if ( ! $this->is_valid() ) {
			$this->app->setting->remove_setting( 'save_log_term' );
			$this->app->setting->remove_setting( 'delete_log_interval' );
			$this->app->setting->remove_setting( 'capture_shutdown_error' );
		}
	}

	/**
	 * shutdown
	 */
	private function shutdown() {
		$error = error_get_last();
		if ( is_null( $error ) ) {
			return;
		}

		if ( $error['type'] & $this->app->get_config( 'config', 'target_shutdown_error' ) ) {
			$suppress = $this->app->get_config( 'config', 'suppress_log_messages' );
			$message  = str_replace( [ "\r\n", "\r", "\n" ], "\n", $error['message'] );
			$messages = explode( "\n", $message );
			$message  = reset( $messages );
			if ( empty( $suppress ) || ( is_array( $suppress ) && ! in_array( $message, $suppress, true ) ) ) {
				$this->app->log( $message, $error, 'error' );
			}
		}
	}

	/**
	 * @since 0.0.2 #2
	 * @return bool
	 */
	public function is_valid() {
		return $this->apply_filters( 'is_valid_log' );
	}

	/**
	 * @param string $message
	 * @param mixed $context
	 * @param string $level
	 *
	 * @return bool
	 */
	public function log( $message, $context = null, $level = '' ) {
		if ( ! $this->is_valid() ) {
			return false;
		}
		if ( $this->is_logging ) {
			return true;
		}
		$this->is_logging = true;

		$log_level = $this->app->get_config( 'config', 'log_level' );
		$level     = $this->get_log_level( $level, $log_level );
		if ( empty( $log_level[ $level ] ) ) {
			$this->is_logging = false;

			return false;
		}

		$data                       = $this->get_called_info();
		$data['message']            = is_string( $message ) ? $this->translate( $message ) : $this->app->utility->json_encode( $message );
		$data['framework_version']  = $this->app->get_framework_version();
		$data['plugin_version']     = $this->app->get_plugin_version();
		$data['php_version']        = phpversion();
		$data['wordpress_version']  = $this->wp_version();
		$data['level']              = $level;
		$data['framework_packages'] = $this->app->utility->json_encode( $this->app->get_package_versions() );
		if ( isset( $context ) ) {
			$data['context'] = $this->app->utility->json_encode( $context );
		}

		$this->send_mail( $level, $log_level, $message, $data );
		$this->insert_log( $level, $log_level, $data );

		$this->is_logging = false;

		return true;
	}

	/**
	 * @param string $level
	 * @param array $log_level
	 *
	 * @return string
	 */
	private function get_log_level( $level, array $log_level ) {
		if ( ! isset( $log_level[ $level ] ) && ! isset( $log_level[''] ) ) {
			return 'info';
		}
		if ( '' === $level || ! isset( $log_level[ $level ] ) ) {
			$level = $log_level[''];
		}
		if ( empty( $log_level[ $level ] ) ) {
			return 'info';
		}

		return $level;
	}

	/**
	 * @param string $level
	 * @param array $log_level
	 * @param array $data
	 */
	private function insert_log( $level, array $log_level, array $data ) {
		if ( ! $this->is_valid_package( 'db' ) ) {
			return;
		}
		if ( empty( $log_level[ $level ]['is_valid_log'] ) ) {
			return;
		}
		if ( $this->apply_filters( 'save_log_term' ) <= 0 ) {
			return;
		}
		$this->table( '__log' )->insert( $data );
	}

	/**
	 * @param string $level
	 * @param array $log_level
	 * @param string $message
	 * @param array $data
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	private function send_mail( $level, array $log_level, $message, array $data ) {
		if ( ! $this->is_valid_package( 'mail' ) ) {
			return;
		}
		if ( empty( $log_level[ $level ]['is_valid_mail'] ) ) {
			return;
		}

		$level   = $log_level[ $level ];
		$roles   = $this->app->array->get( $level, 'roles', [] );
		$emails  = $this->app->array->get( $level, 'emails', [] );
		$filters = $this->app->array->get( $level, 'filters', [] );
		if ( empty( $roles ) ) {
			$roles = [];
		}
		if ( empty( $emails ) ) {
			$emails = [];
		}
		if ( empty( $filters ) ) {
			$filters = [];
		}
		if ( empty( $roles ) && empty( $emails ) && empty( $filters ) ) {
			return;
		}

		if ( ! is_array( $roles ) ) {
			$roles = [ $roles ];
		}
		if ( ! is_array( $emails ) ) {
			$emails = [ $emails ];
		}
		if ( ! is_array( $filters ) ) {
			$filters = [ $filters ];
		}
		$emails = array_unique( $emails );
		$emails = array_combine( $emails, $emails );
		foreach ( $roles as $role ) {
			foreach ( get_users( [ 'role' => $role ] ) as $user ) {
				/** @var WP_User $user */
				if ( ! empty( $user->user_email ) ) {
					$emails[ $user->user_email ] = $user->user_email;
				}
			}
		}
		foreach ( $filters as $filter ) {
			$items = $this->apply_filters( $filter );
			if ( empty( $items ) ) {
				continue;
			}
			if ( ! is_array( $items ) ) {
				$items = $this->app->string->explode( $items );
			}
			foreach ( $items as $item ) {
				if ( ! empty( $item ) && is_string( $item ) && is_email( $item ) ) {
					$emails[ $item ] = $item;
				}
			}
		}

		foreach ( $emails as $email ) {
			$this->app->send_mail( $email, $message, $this->dump( $data, false ) );
		}
	}

	/**
	 * @return array
	 */
	private function get_called_info() {
		$next = false;
		foreach ( $this->app->utility->get_debug_backtrace() as $item ) {
			if ( $next ) {
				$return = [];
				if ( isset( $item['file'] ) ) {
					$return['file'] = preg_replace( '/' . preg_quote( ABSPATH, '/' ) . '/A', '', $item['file'] );
				}
				if ( isset( $item['line'] ) ) {
					$return['line'] = $item['line'];
				}

				return $return;
			}
			if ( ! empty( $item['class'] ) && __CLASS__ === $item['class'] && 'log' === $item['function'] ) {
				$next = true;
			}
		}

		return [];
	}

	/**
	 * @return int
	 */
	public function delete_old_logs() {
		$term  = $this->apply_filters( 'save_log_term' );
		$count = $this->table( '__log' )->where( 'created_at', '<', $this->raw( 'NOW() - INTERVAL ' . (int) $term . ' SECOND' ) )->delete();

		return $count;
	}
}
