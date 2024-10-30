<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;

class IsDiscountOfferAccepted extends SurveyAnswerListColumn {
	
	public function getName(): string {
		return __( 'Retain Status', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function renderContent( SurveyAnswer $surveyAnswer ) {
		
		if ( ! $surveyAnswer->isDiscountOfferEnabled() ) {
			?>
			<mark class="order-status status-failed">
				<span>
					<?php esc_html_e( 'Cancelled', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
				</span>
			</mark>
			<?php
			
			return;
		}
		
		if ( $surveyAnswer->isDiscountOfferAccepted() ) {
			?>
			<mark class="order-status status-processing">
				<span>
					<?php esc_html_e( 'Retained', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
				</span>
			</mark>
			<?php
		} else {
			?>
			<mark class="order-status status-failed">
				<span>
					<?php esc_html_e( 'Cancelled', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
				</span>
			</mark>
			<?php
		}
	}
	
	public function getSlug(): string {
		return 'csows_discount_offer_accepted';
	}
}
