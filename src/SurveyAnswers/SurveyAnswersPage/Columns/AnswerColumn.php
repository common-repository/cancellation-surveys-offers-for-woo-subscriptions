<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;

class AnswerColumn extends SurveyAnswerListColumn {
	
	public function getName(): string {
		return __( 'Survey Answer', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function renderContent( SurveyAnswer $surveyAnswer ) {
		
		if ( ! $surveyAnswer->isSurveyEnabled() ) {
			?>
			<i>
				<?php esc_html_e( 'Survey is disabled', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
			</i>
			<?php
			return;
		}
		
		if ( $surveyAnswer->getSurveySelectedAnswer() ) {
			?>
			<b>
				<?php echo esc_html( $surveyAnswer->getSurveySelectedAnswer() ); ?>
			</b>
			<?php
			
			if ( $surveyAnswer->getSurveyTextAnswer() ) {
				?>
				<div class="survey-answer-column_text-answer">
					<?php echo esc_html( $surveyAnswer->getSurveyTextAnswer() ); ?>
				</div>
				<?php
			}
		} else {
			echo esc_html( '-' );
		}
	}
	
	public function getSlug(): string {
		return 'csows_answer';
	}
}
