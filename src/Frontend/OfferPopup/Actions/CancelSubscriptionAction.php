<?php namespace MeowCrew\CancellationOffers\Frontend\OfferPopup\Actions;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;
use WC_Subscription;
use WCS_User_Change_Status_Handler;
use WP_User;

/**
 * Class CancelSubscriptionAction
 *
 * @package MeowCrew\CancellationOffers\Frontend\Actions
 */
class CancelSubscriptionAction extends PopupActionAbstract {
	
	const ACTION_NAME = 'csows_cancel_subscription';
	
	public function run() {
		try {
			$this->validateRequest();
		} catch ( \Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			wp_die();
		}
		
		$subscription = $this->getSubscription();
		$offer        = $this->getOffer();
		$user         = $this->getUser();
		
		if ( ! $offer->isApplicableForUser( $user, $subscription ) ) {
			wc_add_notice( __( 'Access Denied', 'cancellation-surveys-offers-for-woo-subscriptions' ), 'error' );
			wp_die();
		}
		
		$surveyItemTitle = $this->getSurveyTitle();
		$surveyText      = $this->getSurveyText();
		
		$couponId        = null;
		$discountOffered = false;
		
		if ( $offer->getDiscountOffer()->isApplicableForSubscription( $subscription ) ) {
			$couponId        = $offer->getDiscountOffer()->getCouponId();
			$discountOffered = true;
		}
		
		$surveyAnswerInstance = new SurveyAnswer( $offer->getId(), $subscription->get_id(), $user->ID,
			$offer->getSurvey()->isEnabled(), $surveyItemTitle, $surveyText, $discountOffered, false, $couponId );
		
		try {
			if ( $this->cancelSubscription( $user, $subscription ) ) {
				$surveyAnswerInstance->save();
				
				if ( $surveyText || $surveyItemTitle ) {
					// translators: %s: the survey answer
					$cancellationNote = sprintf( __( 'Subscription canceled with reason: %s',
						'cancellation-surveys-offers-for-woo-subscriptions' ),
						$surveyItemTitle . ' (' . $surveyText . ')' );
					
					$subscription->add_order_note( $cancellationNote );
				}
			}
		} catch ( \Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			wp_die();
		}
	}
	
	protected function cancelSubscription( WP_User $user, WC_Subscription $subscription ): bool {
		
		if ( ! wcs_is_subscription( $subscription ) ) {
			wc_add_notice( __( 'That subscription does not exist. Please contact us if you need assistance.',
				'cancellation-surveys-offers-for-woo-subscriptions' ), 'error' );
			
			return false;
			
		} elseif ( isset( $wpnonce ) && wp_verify_nonce( $wpnonce,
				$subscription->get_id() . $subscription->get_status() ) === false ) {
			wc_add_notice( __( 'Security error. Please contact us if you need assistance.',
				'cancellation-surveys-offers-for-woo-subscriptions' ), 'error' );
			
			return false;
			
		} elseif ( ! user_can( $user->ID, 'edit_shop_subscription_status', $subscription->get_id() ) ) {
			wc_add_notice( __( 'That doesn\'t appear to be one of your subscriptions.', 'cancellation-surveys-offers-for-woo-subscriptions' ),
				'error' );
			
			return false;
			
		} elseif ( ! $subscription->can_be_updated_to( 'cancelled' ) ) {
			// translators: placeholder is subscription's new status, translated
			wc_add_notice( sprintf( __( 'That subscription can not be changed to %s. Please contact us if you need assistance.',
				'cancellation-surveys-offers-for-woo-subscriptions' ), wcs_get_subscription_status_name( 'cancelled' ) ), 'error' );
			
			return false;
		}
		
		WCS_User_Change_Status_Handler::change_users_subscription( $subscription, 'cancelled' );
		
		return true;
	}
	
	public function getActionName(): string {
		return self::ACTION_NAME;
	}
}
