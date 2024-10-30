<?php namespace MeowCrew\CancellationOffers\Offer;

use MeowCrew\CancellationOffers\Offer\Entity\Offer;
use MeowCrew\CancellationOffers\Offer\CPT\CancellationOffersCPT;

class OfferManager {
	
	public static function getOfferForSubscription( \WC_Subscription $subscription ): ?Offer {
		
		foreach ( CancellationOffersCPT::getOffers() as $offer ) {
			if ( $offer->isApplicableForUser( $subscription->get_user(), $subscription ) ) {
				return $offer;
			}
		}
		
		return null;
	}
}
