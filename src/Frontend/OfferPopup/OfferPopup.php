<?php namespace MeowCrew\CancellationOffers\Frontend\OfferPopup;

use MeowCrew\CancellationOffers\CancellationOffersPlugin;
use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Frontend\OfferPopup\Actions\CancelSubscriptionAction;
use MeowCrew\CancellationOffers\Frontend\OfferPopup\Actions\TakeDiscountAction;
use MeowCrew\CancellationOffers\Offer\OfferManager;
use WC_Subscription;

/**
 * Class  OfferPopup
 *
 * @package MeowCrew\CancellationOffers\Frontend
 */
class OfferPopup {
	
	use ServiceContainerTrait;
	
	public $offers = [];
	
	/**
	 * Action handler for the cancel subscription.
	 *
	 * @var CancelSubscriptionAction
	 */
	protected $cancelSubscriptionAction;
	
	/**
	 * Action handler for the saving subscription.
	 *
	 * @var TakeDiscountAction
	 */
	protected $takeDiscountAction;
	
	public function __construct() {
		
		$this->cancelSubscriptionAction = new CancelSubscriptionAction();
		$this->takeDiscountAction       = new TakeDiscountAction();
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueAssets' ) );
		add_action( 'woocommerce_subscription_after_actions', array( $this, 'renderPopup' ) );
		
		add_filter( 'wcs_view_subscription_actions', array( $this, 'filterSubscriptionActions' ), 99999, 3 );
	}
	
	public function enqueueAssets() {
		
		wp_register_script( 'cancellation-offers__popup.js',
			$this->getContainer()->getFileManager()->locateAsset( 'frontend/popup.min.js' ), array( 'jquery' ),
			CancellationOffersPlugin::VERSION, true );
		
		wp_register_script( 'cancellation-offers__frontend.js',
			$this->getContainer()->getFileManager()->locateJSAsset( 'frontend/main.js' ),
			array( 'jquery', 'cancellation-offers__popup.js' ), CancellationOffersPlugin::VERSION, true );
		
		wp_register_style( 'cancellation-offers__popup.css',
			$this->getContainer()->getFileManager()->locateAsset( 'frontend/popup.css' ), array(),
			CancellationOffersPlugin::VERSION );
		
		wp_add_inline_style( 'cancellation-offers__popup.css', '
            :root {
                --cos-accent-color: ' . esc_html( $this->getContainer()->getSettings()->getAccentColor() ) . ';
            }' );
		
		wp_enqueue_script( 'cancellation-offers__frontend.js' );
		wp_enqueue_script( 'cancellation-offers__popup.js' );
		wp_enqueue_style( 'cancellation-offers__popup.css' );
	}
	
	public function getSubscriptionOffer( $subscription ) {
		if ( ! array_key_exists( $subscription->get_id(), $this->offers ) ) {
			$this->offers[ $subscription->get_id() ] = OfferManager::getOfferForSubscription( $subscription );
		}
		
		return $this->offers[ $subscription->get_id() ];
	}
	
	public function filterSubscriptionActions( $actions, WC_Subscription $subscription ) {
		$offer = $this->getSubscriptionOffer( $subscription );
		
		if ( ! $offer ) {
			return $actions;
		}
		
		if ( ! empty( $actions['cancel'] ) ) {
			$actions['cancel']['url']      = '#cancellation-popup';
			$actions['cancel']['block_ui'] = false;
		}
		
		return $actions;
	}
	
	public function renderPopup( WC_Subscription $subscription ) {
		
		$offer = $this->getSubscriptionOffer( $subscription );
		
		if ( ! $offer ) {
			return;
		}
		?>

        <div id="cancellation-offer-popup-wrapper" style="display: none">
			<?php
				$this->getContainer()->getFileManager()->includeTemplate( 'frontend/popup.php', array(
					'cancel_subscription_url' => $this->cancelSubscriptionAction->getURL( $subscription->get_id(),
						$offer->getId() ),
					'take_discount_url'       => $this->takeDiscountAction->getURL( $subscription->get_id(),
						$offer->getId() ),
					'offer'                   => $offer,
					'subscription'            => $subscription,
				) );
			?>
        </div>
		<?php
	}
}