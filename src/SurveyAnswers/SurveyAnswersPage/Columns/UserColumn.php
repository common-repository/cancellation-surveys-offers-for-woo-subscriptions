<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;
use MeowCrew\CancellationOffers\Utils\Formatter;
use WC_Customer;

class UserColumn extends SurveyAnswerListColumn {
	
	public function getName(): string {
		return __( 'User', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function renderContent( SurveyAnswer $surveyAnswer ) {
		
		$user = get_user_by( 'ID', $surveyAnswer->getUserId() );
		
		if ( ! $user ) {
			echo '-';
			
			return;
		}
		try {
			$customer = new WC_Customer( $user->ID );
		} catch ( \Exception $e ) {
			echo '-';
			
			return;
		}
		
		echo wp_kses_post( Formatter::formatCustomerString( $customer, true, false ) );
	}
	
	public function getSlug(): string {
		return 'csows_user';
	}
}
