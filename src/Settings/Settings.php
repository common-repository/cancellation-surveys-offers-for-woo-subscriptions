<?php namespace MeowCrew\CancellationOffers\Settings;

use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Offer\CPT\CancellationOffersCPT;

/**
 * Class Settings
 *
 * @package Settings
 */
class Settings {
	
	use ServiceContainerTrait;
	
	const SETTINGS_PREFIX = 'csows_settings_';
	
	/**
	 * Settings constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_subscription_settings', function ( $settings ) {
			return $this->addSettings( $settings );
		}, 999 );
		
		
		add_action( 'admin_menu', function () {
			
			add_submenu_page( 'edit.php?post_type=' . CancellationOffersCPT::SLUG,
				__( 'Settings', 'cancellation-surveys-offers-for-woo-subscriptions' ),
				__( 'Settings', 'cancellation-surveys-offers-for-woo-subscriptions' ), 'manage_options',
				$this->getLink() );
		}, 999 );
		
	}
	
	protected function addSettings( $settings ) {
		$_settings = array(
			array(
				'name' => _x( 'Offers & Surveys', 'options section heading',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'type' => 'title',
				'desc' => __( 'Settings dedicated to Cancellation Surveys & Offers',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'id'   => self::SETTINGS_PREFIX . 'section_start',
			),
			array(
				'id'      => self::SETTINGS_PREFIX . 'accent_color',
				'name'    => __( 'Accent Color', 'cancellation-surveys-offers-for-woo-subscriptions' ),
				'default' => '#2271b1',
				'css'     => 'width: 100px',
				'type'    => 'color',
			),
			array(
				'type' => 'sectionend',
			),
		);
		
		return array_merge( $settings, $_settings );
	}
	
	public function getAccentColor() {
		return $this->get( 'accent_color', '#2271b1' );
	}
	
	/**
	 * Get setting by name
	 *
	 * @param  string  $optionName
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	protected function get( string $optionName, $default = null ) {
		return get_option( self::SETTINGS_PREFIX . $optionName, $default );
	}
	
	/**
	 * Get url to settings page
	 *
	 * @return string
	 */
	public function getLink(): string {
		return admin_url( 'admin.php?page=wc-settings&tab=subscriptions#' . self::SETTINGS_PREFIX . 'section_start-description' );
	}
}
