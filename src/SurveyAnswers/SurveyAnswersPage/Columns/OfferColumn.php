<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\Offer\Entity\Offer;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;

class OfferColumn extends SurveyAnswerListColumn {
	
	public function getName(): string {
		return __( 'Survey & Offer', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function renderContent( SurveyAnswer $surveyAnswer ) {
		$offer = Offer::build( $surveyAnswer->getOfferId() );
		
		if ( $offer ) {
			$title = get_the_title( $offer->getId() );
			?>
			<a target="_blank" href="<?php echo esc_attr( get_edit_post_link( $offer->getId() ) ); ?>">
				<?php echo esc_attr( $title ? $title : __( 'No title', 'cancellation-surveys-offers-for-woo-subscriptions' ) ); ?>
			</a>
			<?php
		} else {
			esc_html_e( '(Deleted)', 'cancellation-surveys-offers-for-woo-subscriptions' );
		}
	}
	
	public function getSlug(): string {
		return 'csows_survey_offer';
	}
}
