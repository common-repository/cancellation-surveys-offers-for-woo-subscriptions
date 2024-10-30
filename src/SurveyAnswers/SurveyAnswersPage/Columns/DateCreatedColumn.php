<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;

class DateCreatedColumn extends SurveyAnswerListColumn {
	
	public function getName(): string {
		return __( 'Date Created', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function renderContent( SurveyAnswer $surveyAnswer ) {
		?>
		<span class="exact-date"
			  title="<?php echo esc_attr( $surveyAnswer->getDateCreated()->format( 'y-m-d H:i:s' ) ); ?>">
			<?php
				echo esc_html( date_i18n( wc_date_format(),
					strtotime( $surveyAnswer->getDateCreated()->format( 'y-m-d H:i:s' ) ) ) )
			?>
		</span>
		<?php
	}
	
	public function getSlug() {
		return 'csows_date_created';
	}
}
