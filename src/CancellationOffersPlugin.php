<?php namespace MeowCrew\CancellationOffers;

use MeowCrew\CancellationOffers\Core\AdminNotifier;
use MeowCrew\CancellationOffers\Core\FileManager;
use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Offer\CPT\CancellationOffersCPT;
use MeowCrew\CancellationOffers\Frontend\OfferPopup\OfferPopup;
use MeowCrew\CancellationOffers\Settings\Settings;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\SurveyAnswersPage;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersTable;

/**
 * Class  CancellationOffersPlugin
 *
 * @package MeowCrew\CancellationOffers
 */
class CancellationOffersPlugin {
	
	use ServiceContainerTrait;
	
	public static $mainFile = null;
	
	public const VERSION = '1.0.0';
	
	public function __construct( $mainFile ) {
		
		add_action( 'admin_enqueue_scripts', function () {
			wp_enqueue_style( 'cancellation-offers-general-admin-style',
				$this->getContainer()->getFileManager()->locateAsset( 'admin/general-style.css' ), array(),
				self::VERSION );
			wp_enqueue_script( 'cancellation-offers-general-admin-script',
				$this->getContainer()->getFileManager()->locateJSAsset( 'admin/general-script' ), array( 'jquery' ),
				self::VERSION );
		} );
		
		self::$mainFile = $mainFile;
		
		$this->initializeCoreServices();
		
		if ( $this->checkRequirements() ) {
			$this->initializePlugin();
		}
		
		add_filter( 'plugin_row_meta', array( $this, 'addPluginsMeta' ), 10, 2 );
		add_action( 'plugins_loaded', [ $this, 'loadTextDomain' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( $this->getContainer()->getFileManager()->getMainFile() ),
			array( $this, 'addPluginActions' ), 10, 4 );
	}
	
	protected function initializeCoreServices() {
		$this->getContainer()->add( 'fileManager', FileManager::class, array( self::$mainFile ) );
		$this->getContainer()->add( 'adminNotifier', AdminNotifier::class );
		$this->getContainer()->add( 'settings', Settings::class );
		
		new Freemius();
	}
	
	protected function initializePlugin() {
		$this->getContainer()->add( CancellationOffersCPT::class, CancellationOffersCPT::class );
		$this->getContainer()->add( OfferPopup::class, OfferPopup::class );
		$this->getContainer()->add( SurveyAnswersPage::class, SurveyAnswersPage::class );
	}
	
	/**
	 * Load plugin translations
	 */
	public function loadTextDomain() {
		$name = $this->getContainer()->getFileManager()->getPluginName();
		load_plugin_textdomain( 'cancellation-surveys-offers-for-woo-subscriptions', false, $name . '/languages/' );
	}
	
	public function checkRequirements(): bool {
		
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		
		// Check if WooCommerce is active
		if ( ! ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) ) {
			/* translators: %s: required plugin */
			$message = sprintf( __( '<b>Cancellation Surveys & Offers for WooCommerce Subscriptions</b> plugin requires %s to be installed and activated.',
				'cancellation-surveys-offers-for-woo-subscriptions' ),
				'<a target="_blank" href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a>' );
			
			$this->getContainer()->getAdminNotifier()->push( $message, AdminNotifier::ERROR );
			
			return false;
		}
		
		// Check if WooCommerce Subscriptions is active
		if ( ! ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) || is_plugin_active_for_network( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) ) {
			/* translators: %s: required plugin */
			$message = sprintf( __( '<b>Cancellation Surveys & Offers for WooCommerce Subscriptions</b> plugin requires %s to be installed and activated.',
				'cancellation-surveys-offers-for-woo-subscriptions' ),
				'<a target="_blank" href="https://woocommerce.com/products/woocommerce-subscriptions/">WooCommerce Subscriptions</a>' );
			
			$this->getContainer()->getAdminNotifier()->push( $message, AdminNotifier::ERROR );
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add setting to plugin actions at plugins list
	 *
	 * @param  array  $actions
	 *
	 * @return array
	 */
	public function addPluginActions( array $actions ): array {
		$actions[] = '<a href="' . $this->getContainer()->getSettings()->getLink() . '">' . __( 'Settings',
				'cancellation-surveys-offers-for-woo-subscriptions' ) . '</a>';
		
		if ( ! csows_fs()->can_use_premium_code() ) {
			$actions[] = '<a href="' . csows_upgrade_url() . '"><b style="color: red">' . __( 'Go Premium',
					'cancellation-surveys-offers-for-woo-subscriptions' ) . '</b></a>';
		}
		
		return $actions;
	}
	
	public function addPluginsMeta( $links, $file ) {
		
		if ( strpos( $file, 'cancellation-surveys-offers' ) === false ) {
			return $links;
		}
		
		$links['docs'] = '<a target="_blank" href="' . self::getDocumentationURL() . '">' . __( 'Documentation',
				'cancellation-surveys-offers-for-woo-subscriptions' ) . '</a>';
		
		$links['contact-us'] = '<a href="' . self::getContactUsURL() . '"><b style="color: green">' . __( 'Contact Us',
				'cancellation-surveys-offers-for-woo-subscriptions' ) . '</b></a>';
		
		if ( ! csows_fs()->is_anonymous() && csows_fs()->is_installed_on_site() ) {
			$links['account'] = '<a href="' . self::getAccountPageURL() . '"><b>' . __( 'Account',
					'cancellation-surveys-offers-for-woo-subscriptions' ) . '</b></a>';
		}
		
		return $links;
	}
	
	/**
	 * Fired when the plugin is activated
	 */
	public function activate() {
		SurveyAnswersTable::create();
	}
	
	public static function getDocumentationURL() {
		return 'https://meow-crew.com/documentation/cancellation-surveys-offers-for-woo-subscriptions-documentation ';
	}
	
	public static function getContactUsURL() {
		return admin_url( 'admin.php?page=csows-contact-us' );
	}
	
	public static function getAccountPageURL(): string {
		return admin_url( 'admin.php?page=csows-account' );
	}
	
	/**
	 * Fired during plugin uninstall
	 */
	public static function uninstall() {}
}
