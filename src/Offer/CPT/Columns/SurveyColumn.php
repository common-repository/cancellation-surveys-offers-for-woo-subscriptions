<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Columns;

use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;

class SurveyColumn {
	
	use ServiceContainerTrait;
	
	public function getName(): string {
		return __( 'Survey', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function render( Offer $offer ) {
		
		if ( $offer->getSurvey()->isEnabled() ) {
			?>
			<b>Enabled</b>
			<?php
			
		} else {
			esc_html_e( 'Survey is not enabled', 'cancellation-surveys-offers-for-woo-subscriptions' );
		}
	}
}