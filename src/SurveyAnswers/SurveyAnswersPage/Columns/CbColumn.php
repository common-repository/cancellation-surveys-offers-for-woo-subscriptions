<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;

class CbColumn extends SurveyAnswerListColumn {
	
	public function getName(): string {
		return '<input type="checkbox" />';
	}
	
	public function renderContent( SurveyAnswer $surveyAnswer ) {
		?>
		<input type="checkbox" name="survey_answers[]"
			   value="<?php echo esc_attr( esc_attr( $surveyAnswer->getId() ) ); ?>"/>
		<?php
	}
	
	public function getSlug(): string {
		return 'cb';
	}
}
