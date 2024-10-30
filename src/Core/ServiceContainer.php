<?php namespace MeowCrew\CancellationOffers\Core;

use Exception;
use MeowCrew\CancellationOffers\Settings\Settings;

class ServiceContainer {
	
	private $services = array();
	
	private static $instance;
	
	private function __construct() {}
	
	public static function getInstance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	public function add( $name, $instance, $args = array() ) {
		
		$instance = apply_filters( 'cancellation_offers/container/service_instance', $instance, $name );
		
		$this->services[ $name ] = new $instance( ...$args );
	}
	
	/**
	 * Get service
	 *
	 * @param $name
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function get( $name ) {
		if ( ! empty( $this->services[ $name ] ) ) {
			return $this->services[ $name ];
		}
		
		throw new Exception( 'Undefined service' );
	}
	
	/**
	 * Get fileManager
	 *
	 * @return ?FileManager
	 */
	public function getFileManager(): ?FileManager {
		try {
			return $this->get( 'fileManager' );
		} catch ( Exception $e ) {
			return null;
		}
	}
	
	/**
	 * Get Settings
	 *
	 * @return ?Settings
	 */
	public function getSettings(): ?Settings {
		try {
			return $this->get( 'settings' );
		} catch ( Exception $e ) {
			return null;
		}
	}
	
	/**
	 * Get AdminNotifier
	 *
	 * @return ?AdminNotifier
	 */
	public function getAdminNotifier(): ?AdminNotifier {
		try {
			return $this->get( 'adminNotifier' );
		} catch ( Exception $e ) {
			return null;
		}
	}
}
