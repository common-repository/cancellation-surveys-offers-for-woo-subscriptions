<?php namespace MeowCrew\CancellationOffers\Frontend\OfferPopup\Actions;

use Exception;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;
use WC_Coupon;
use WP_Error;

/**
 * Class  TakeDiscountAction
 *
 * @package MeowCrew\CancellationOffers\Frontend\Actions
 */
class TakeDiscountAction extends PopupActionAbstract {
	
	const ACTION_NAME = 'csows_take_discount';
	
	public function run() {
		
		try {
			$this->validateRequest();
			
			$subscription    = $this->getSubscription();
			$offer           = $this->getOffer();
			$user            = $this->getUser();
			$surveyItemTitle = $this->getSurveyTitle();
			$surveyText      = $this->getSurveyText();
			
			$surveyAnswerInstance = new SurveyAnswer( $offer->getId(), $subscription->get_id(), $user->ID,
				$offer->getSurvey()->isEnabled(), $surveyItemTitle, $surveyText,
				$offer->getDiscountOffer()->isEnabled(), true, $offer->getDiscountOffer()->getCouponId() );
			
			$surveyAnswerInstance->save();
			
			$coupon = new WC_Coupon( $offer->getDiscountOffer()->getCouponId() );
			
			$result = $subscription->apply_coupon( $coupon );
			
			if ( $result instanceof WP_Error ) {
				throw new Exception( $result->get_error_message() );
			}
			
			$subscription->add_meta_data( '_csows_offer_discount_applied', $offer->getId(), true );
			
			// translators: %s: the survey answer
			$cancellationNote = sprintf( __( 'Subscription retention achieved through discount offer (%s)',
				'cancellation-surveys-offers-for-woo-subscriptions' ), $coupon->get_code() );
			
			$subscription->add_order_note( $cancellationNote );
			
			$subscription->save();
			
		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			wp_die();
		}
		
		wc_add_notice( __( 'Discount has been applied', 'cancellation-surveys-offers-for-woo-subscriptions' ) );
	}
	
	public function getActionName(): string {
		return self::ACTION_NAME;
	}
}
