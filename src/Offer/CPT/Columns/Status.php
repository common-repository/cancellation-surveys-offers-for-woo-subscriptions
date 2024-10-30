<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Columns;

use MeowCrew\CancellationOffers\Offer\Entity\Offer;

class Status {
	
	public function getName(): string {
		return __( 'Status', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function render( Offer $offer ) {
		if ( $offer->getSettings()->isSuspended() ) {
			?>
			<mark class="order-status status-on-hold">
				<span>
				<?php esc_html_e( 'Suspended', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
				</span>
			</mark>
			<?php
		} else {
			?>
			<mark class="order-status status-processing">
				<span>
				<?php esc_html_e( 'Active', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
				</span>
			</mark>
			<?php
		}
	}
}
