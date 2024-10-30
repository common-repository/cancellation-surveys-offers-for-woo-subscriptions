<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Columns;

use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;

class OfferColumn {
	
	use ServiceContainerTrait;
	
	public function getName(): string {
		return __( 'Discount Offer', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function render( Offer $offer ) {
		
		if ( $offer->getDiscountOffer()->isEnabled() ) {
			?>
			<b>
				<?php
					esc_html_e( 'Coupon code: ', 'cancellation-surveys-offers-for-woo-subscriptions' );
				?>
			</b>
			<br>

			<a href="<?php echo esc_html( get_edit_post_link( $offer->getDiscountOffer()->getCoupon()->get_id() ) ); ?>">
				<code style="display: block;
	text-align: center;
	border-radius: 6px;">
					<b><?php echo esc_html( $offer->getDiscountOffer()->getCoupon()->get_code() ); ?></b>
				</code>
			</a>
			<?php
			
			$minimumDaysActive    = $offer->getDiscountOffer()->getMinimumSubscriptionDaysActive();
			$minimumRenewalsCount = $offer->getDiscountOffer()->getMinimumRenewalsCount();
			
			$minimumDaysActive    = $minimumRenewalsCount ? $minimumDaysActive : __( 'Not set',
				'cancellation-surveys-offers-for-woo-subscriptions' );
			$minimumRenewalsCount = $minimumRenewalsCount ? $minimumRenewalsCount : __( 'Not set',
				'cancellation-surveys-offers-for-woo-subscriptions' );
			
			?>
			<br>
			<b>
				<?php esc_html_e( 'Minimum renewals number', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>:
			</b>
			<br>
			<?php echo esc_html( $minimumRenewalsCount ); ?>
			<br>
			<br>
			<b>
				<?php
				esc_html_e( 'Minimum subscription days active',
					'cancellation-surveys-offers-for-woo-subscriptions' );
				?>
					:
			</b>
			<br>
			<?php echo esc_html( $minimumDaysActive ); ?>
			<?php
			
		} else {
			esc_html_e( 'Discount Offer is not enabled', 'cancellation-surveys-offers-for-woo-subscriptions' );
		}
	}
}