<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;

class SubscriptionColumn extends SurveyAnswerListColumn {
	
	public function getName(): string {
		return __( 'Subscription', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function renderContent( SurveyAnswer $surveyAnswer ) {
		$subscription = wcs_get_subscription( $surveyAnswer->getSubscriptionId() );
		
		if ( $subscription ) {
			?>
			<a target="_blank" href="<?php echo esc_attr( $subscription->get_edit_order_url() ); ?>">
				<?php echo esc_attr( '#' . $subscription->get_id() ); ?>
			</a>
			<?php
		} else {
			esc_html_e( '(Deleted)', 'cancellation-surveys-offers-for-woo-subscriptions' );
		}
	}
	
	public function getSlug() {
		return 'uxf_order';
	}
}
