<?php namespace MeowCrew\CancellationOffers\Frontend\OfferPopup\Actions;

use Exception;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;
use WC_Subscription;
use WP_User;

/**
 * Class  CancelSubscriptionAction
 *
 * @package MeowCrew\CancellationOffers\Frontend\Actions
 */
abstract class PopupActionAbstract {
	
	public function __construct() {
		add_action( 'wc_ajax_' . $this->getActionName(), array( $this, 'run' ) );
	}
	
	abstract public function run();
	
	abstract public function getActionName();
	
	public function getURL( $subscriptionID, $offerId ): string {
		return add_query_arg( array(
			'wc-ajax'         => $this->getActionName(),
			'subscription_id' => $subscriptionID,
			'offer_id'        => $offerId,
			'nonce'           => wp_create_nonce( $this->getActionName() ),
		), home_url() );
	}
	
	protected function checkNonce() {
		return wp_verify_nonce( $this->getNonce(), $this->getActionName() );
	}
	
	public function getNonce() {
		return ! empty( $_GET['nonce'] ) ? sanitize_text_field( $_GET['nonce'] ) : false;
	}
	
	public function getSubscription(): ?WC_Subscription {
		
		$subscriptionID = ! empty( $_GET['subscription_id'] ) ? intval($_GET['subscription_id']) : null;
		$subscription   = wcs_get_subscription( $subscriptionID );
		
		if ( ! $subscription ) {
			return null;
		}
		
		return $subscription;
	}
	
	public function getOffer(): ?Offer {
		$offerId = ! empty( $_GET['offer_id'] ) ? intval( $_GET['offer_id'] ) : false;
		$offer   = Offer::build( $offerId );
		
		return $offer ? $offer : null;
	}
	
	public function getSurveyTitle(): ?string {
		
		$offer = $this->getOffer();
		
		if ( $offer->getSurvey()->isEnabled() ) {
			$surveyItemSlug = ! empty( $_GET['survey_selected_answer_slug'] ) ? sanitize_text_field( $_GET['survey_selected_answer_slug'] ) : false;
			
			$surveyItem = $offer->getSurvey()->getItemBySlug( $surveyItemSlug );
			
			return $surveyItem ? $surveyItem->getTitle() : null;
		}
		
		return null;
	}
	
	public function getSurveyText() {
		$offer = $this->getOffer();
		
		if ( $offer->getSurvey()->isEnabled() ) {
			$surveyText = ! empty( $_GET['survey_text_answer'] ) ? sanitize_text_field( $_GET['survey_text_answer'] ) : false;
			
			return $surveyText ? substr( $surveyText, 0, 3000 ) : null;
		}
		
		return null;
	}
	
	public function getUser(): ?WP_User {
		return wp_get_current_user();
	}
	
	public function validateRequest(): bool {
		
		if ( ! $this->checkNonce() ) {
			throw new Exception( 'Invalid nonce' );
		}
		
		if ( ! $this->getSubscription() ) {
			throw new Exception( 'Invalid subscription' );
		}
		
		if ( ! $this->getOffer() ) {
			throw new Exception( 'Invalid offer' );
		}
		
		if ( ! $this->getUser() ) {
			throw new Exception( 'Invalid user' );
		}
		
		if ( ! $this->getOffer()->isApplicableForUser( $this->getUser(), $this->getSubscription() ) ) {
			throw new Exception( 'Offer is not applicable for this user' );
		}
		
		return true;
	}
	
}
