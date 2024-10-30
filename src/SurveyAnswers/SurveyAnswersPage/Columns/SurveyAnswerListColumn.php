<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;

abstract class SurveyAnswerListColumn {
	
	public function __construct() {
		add_action( 'cancellation_offers/admin/survey_answers_list/render_column',
			function ( $columnName, SurveyAnswer $surveyAnswer ) {
				if ( $columnName === $this->getSlug() ) {
					$this->renderContent( $surveyAnswer );
				}
			}, 10, 2 );
	}
	
	abstract public function getName();
	
	abstract public function getSlug();
	
	abstract public function renderContent( SurveyAnswer $surveyAnswer );
}
