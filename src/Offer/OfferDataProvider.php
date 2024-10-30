<?php namespace MeowCrew\CancellationOffers\Offer;

use MeowCrew\CancellationOffers\Utils\StringUtil;

class OfferDataProvider {
	
	protected $offerId;
	
	protected $unitSuffix;
	
	const META_DATA_PREFIX = '_cancellation_offers_';
	
	public function __construct( $offerId, $unitSuffix = '' ) {
		$this->offerId    = $offerId;
		$this->unitSuffix = $unitSuffix && ! StringUtil::endsWith( '_', $unitSuffix ) ? $unitSuffix . '_' : $unitSuffix;
	}
	
	public function getMeta( string $key, $default = false ) {
		
		$key = self::META_DATA_PREFIX . $this->unitSuffix . $key;
		
		if ( ! metadata_exists( 'post', $this->offerId, $key ) ) {
			return $default;
		}
		
		return get_post_meta( $this->offerId, $key, true );
	}
	
	public function setMeta( $key, $value ) {
		$key = self::META_DATA_PREFIX . $this->unitSuffix . $key;
		
		update_post_meta( $this->offerId, $key, $value );
	}
}
