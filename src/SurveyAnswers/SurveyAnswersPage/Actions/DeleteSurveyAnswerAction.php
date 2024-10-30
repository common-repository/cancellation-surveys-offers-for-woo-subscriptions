<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Actions;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersRepository;

class DeleteSurveyAnswerAction extends SurveyAnswersListAction {
	
	public function handle() {
		$feedbackId = $this->getEntityId();
		
		$feedback = SurveyAnswersRepository::getById( $feedbackId );
		
		if ( $feedback ) {
			$feedback->delete();
			
			$this->getContainer()->getAdminNotifier()->flash( __( 'Survey answer has been deleted successfully',
				'cancellation-surveys-offers-for-woo-subscriptions' ) );
		}
	}
	
	public function getEntityId() {
		return isset( $_REQUEST['feedback_id'] ) ? sanitize_text_field( $_REQUEST['feedback_id'] ) : false;
	}
	
	public function getActionSlug(): string {
		return 'csows_delete_survey_answer';
	}
	
	public function getName(): string {
		return __( 'Delete', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
}
