<?php namespace MeowCrew\CancellationOffers\Offer\Entity\Settings;

use MeowCrew\CancellationOffers\Offer\OfferDataProvider;

class Settings {
	
	protected $minimumRenewalsCount = null;
	protected $minimumSubscriptionDaysActive = null;
	protected $isSuspended = false;
	
	public function getMinimumRenewalsCount(): ?int {
		return $this->minimumRenewalsCount;
	}
	
	public function isSuspended(): bool {
		return $this->isSuspended;
	}
	
	public function setIsSuspended( bool $isSuspended ): void {
		$this->isSuspended = $isSuspended;
	}
	
	public function setMinimumRenewalsCount( ?int $minimumRenewalsCount ): void {
		$this->minimumRenewalsCount = $minimumRenewalsCount;
	}
	
	public function getMinimumSubscriptionDaysActive(): ?int {
		return $this->minimumSubscriptionDaysActive;
	}
	
	public function setMinimumSubscriptionDaysActive( ?int $minimumSubscriptionDaysActive ): void {
		$this->minimumSubscriptionDaysActive = $minimumSubscriptionDaysActive;
	}
	
	public function matchRequirements( \WP_User $user, \WC_Subscription $subscription ): bool {
		return !$this->isSuspended();
	}
	
	public static function getDataSchema(): array {
		return array(
			'is_suspended' => array(
				'default'  => false,
				'sanitize' => 'wc_string_to_bool',
			),
		);
	}
	
	public static function build( $offerId ): self {
		$offerDataProvider = new OfferDataProvider( $offerId, 'settings' );
		
		$data = array();
		
		foreach ( self::getDataSchema() as $key => $schemaItem ) {
			$value = $offerDataProvider->getMeta( $key, $schemaItem['default'] );
			
			if ( is_callable( $schemaItem['sanitize'] ) ) {
				$value = call_user_func( $schemaItem['sanitize'], $value );
			}
			
			$data[ $key ] = $value;
		}
		
		return self::buildFromArray( $data );
	}
	
	public static function buildFromPOST( $postData ): self {
		
		$data = array();
		
		foreach ( self::getDataSchema() as $key => $schemaItem ) {
			$value = $postData[ 'settings_' . $key ] ?? null;
			
			if ( is_callable( $schemaItem['sanitize'] ) ) {
				$value = call_user_func( $schemaItem['sanitize'], $value );
			}
			
			$data[ $key ] = $value;
		}
		
		return self::buildFromArray( $data );
	}
	
	public static function buildFromArray( $data ): self {
		$settings = new self();
		
		$settings->setIsSuspended( $data['is_suspended'] );
		
		return $settings;
	}
	
	public function save( $offerId ): void {
		
		$settings = new OfferDataProvider( $offerId, 'settings' );
		
		$settings->setMeta( 'is_suspended', wc_bool_to_string( $this->isSuspended() ) );
	}
}
