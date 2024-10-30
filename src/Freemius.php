<?php namespace MeowCrew\CancellationOffers;


/**
 * Class Freemius
 *
 */
class Freemius {
	
	/**
	 * License
	 *
	 * @var \Freemius
	 */
	private $instance;
	
	/**
	 * Freemius constructor.
	 *
	 */
	public function __construct() {
		
		$this->init();
		
		if ( $this->isValid() ) {
			$this->hooks();
		}
	}
	
	public function hooks() {
		add_action( 'admin_menu', [ $this, 'initPages' ] );
	}
	
	public function isValid(): bool {
		return $this->instance instanceof \Freemius;
	}
	
	public function init() {
		if ( function_exists( 'csows_fs' ) ) {
			$this->instance = csows_fs();
		}
	}
	
	public function initPages() {
		// Account
		add_submenu_page( '__freemius',
			__( 'Freemius Account', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			__( 'Freemius Account', 'cancellation-surveys-offers-for-woo-subscriptions' ), 'manage_options',
			'csows-account', [ $this, 'renderAccountPage' ] );
		// Contact us
		add_submenu_page( '__freemius', __( 'Contact Us', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			__( 'Contact Us', 'cancellation-surveys-offers-for-woo-subscriptions' ), 'manage_options',
			'csows-contact-us', [ $this, 'renderContactUsPage' ] );
	}
	
	public function renderAccountPage() {
		if ( $this->instance->is_activation_mode() || $this->instance->is_anonymous() ) {
			header( 'Location: ' . $this->instance->get_activation_url() );
			wp_die();
		} else {
			$this->instance->_account_page_load();
			$this->instance->_account_page_render();
		}
	}
	
	public function renderContactUsPage() {
		$this->instance->_contact_page_render();
	}
}
