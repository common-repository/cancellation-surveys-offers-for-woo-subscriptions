<?php namespace MeowCrew\CancellationOffers\Offer\Entity\MatchingRule;

use MeowCrew\CancellationOffers\Offer\OfferDataProvider;
use MeowCrew\CancellationOffers\Utils\Sanitizer;
use WC_Subscription;
use WP_User;

class MatchingRule {
	
	protected $includedCategories = array();
	
	protected $excludedCategories = array();
	
	protected $includedProducts = array();
	
	protected $excludedProducts = array();
	
	protected $includedUsers = array();
	
	protected $excludedUsers = array();
	
	protected $includedUserRoles = array();
	
	protected $excludedUserRoles = array();
	
	public function getIncludedCategories(): array {
		return $this->includedCategories;
	}
	
	public function setIncludedCategories( array $includedCategories ): void {
		$this->includedCategories = $includedCategories;
	}
	
	public function getExcludedCategories(): array {
		return $this->excludedCategories;
	}
	
	public function setExcludedCategories( array $excludedCategories ): void {
		$this->excludedCategories = $excludedCategories;
	}
	
	public function getIncludedProducts(): array {
		return $this->includedProducts;
	}
	
	public function setIncludedProducts( array $includedProducts ): void {
		$this->includedProducts = $includedProducts;
	}
	
	public function getExcludedProducts(): array {
		return $this->excludedProducts;
	}
	
	public function setExcludedProducts( array $excludedProducts ): void {
		$this->excludedProducts = $excludedProducts;
	}
	
	public function getIncludedUsers(): array {
		return $this->includedUsers;
	}
	
	public function setIncludedUsers( array $includedUsers ): void {
		$this->includedUsers = $includedUsers;
	}
	
	public function getExcludedUsers(): array {
		return $this->excludedUsers;
	}
	
	public function setExcludedUsers( array $excludedUsers ): void {
		$this->excludedUsers = $excludedUsers;
	}
	
	public function getIncludedUserRoles(): array {
		return $this->includedUserRoles;
	}
	
	public function setIncludedUserRoles( array $includedUserRoles ): void {
		$this->includedUserRoles = $includedUserRoles;
	}
	
	public function getExcludedUserRoles(): array {
		return $this->excludedUserRoles;
	}
	
	public function setExcludedUserRoles( array $excludedUserRoles ): void {
		$this->excludedUserRoles = $excludedUserRoles;
	}
	
	public static function getDataSchema(): array {
		return array(
			'included_categories' => array(
				'default'  => array(),
				'sanitize' => array( Sanitizer::class, 'sanitizeIntegerArray' ),
			),
			'excluded_categories' => array(
				'default'  => array(),
				'sanitize' => array( Sanitizer::class, 'sanitizeIntegerArray' ),
			),
			
			'included_products' => array(
				'default'  => array(),
				'sanitize' => array( Sanitizer::class, 'sanitizeIntegerArray' ),
			),
			'excluded_products' => array(
				'default'  => array(),
				'sanitize' => array( Sanitizer::class, 'sanitizeIntegerArray' ),
			),
			
			'included_users' => array(
				'default'  => array(),
				'sanitize' => array( Sanitizer::class, 'sanitizeIntegerArray' ),
			),
			'excluded_users' => array(
				'default'  => array(),
				'sanitize' => array( Sanitizer::class, 'sanitizeIntegerArray' ),
			),
			
			'included_user_roles' => array(
				'default'  => array(),
				'sanitize' => array( Sanitizer::class, 'sanitizeStringArray' ),
			),
			'excluded_user_roles' => array(
				'default'  => array(),
				'sanitize' => array( Sanitizer::class, 'sanitizeStringArray' ),
			),
		);
	}
	
	public static function build( $offerId ): self {
		
		$offerDataProvider = new OfferDataProvider( $offerId, 'matching_rule' );
		
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
	
	/**
	 * Wrapper for the main "match" function to provide the hook for 3rd party devs
	 *
	 * @param  WP_User  $user
	 * @param  WC_Subscription  $subscription
	 *
	 * @return bool
	 */
	public function matchRequirements( WP_User $user, WC_Subscription $subscription ): bool {
		$matched = $this->_matchRequirements( $user, $subscription );
		
		return apply_filters( 'cancellation_offers/offer/subscription_matched', $matched, $this, $user, $subscription );
	}
	
	protected function isEmpty(): bool {
		return empty( $this->getIncludedCategories() ) && empty( $this->getExcludedCategories() ) && empty( $this->getIncludedProducts() ) && empty( $this->getExcludedProducts() ) && empty( $this->getIncludedUsers() ) && empty( $this->getExcludedUsers() ) && empty( $this->getIncludedUserRoles() ) && empty( $this->getExcludedUserRoles() );
	}
	
	protected function _matchRequirements( WP_User $user, WC_Subscription $subscription ): bool {
		
		/*
		 * If the rule is empty - match immediately for any subscription
		 */
		if ( $this->isEmpty() ) {
			return true;
		}
		
		/**
		 * 1. Check for users exclusion
		 *
		 * If users in exclusion -  it does not match immediately
		 */
		if ( in_array( $user->ID, $this->getExcludedUsers() ) ) {
			return false;
		}
		
		foreach ( $this->getExcludedUserRoles() as $role ) {
			if ( in_array( $role, $user->roles ) ) {
				return false;
			}
		}
		
		/**
		 * Foreach each product item in the subscription to check if it matches the rule
		 */
		
		$productMatched     = false;
		$productLimitations = false;
		
		foreach ( $subscription->get_items( 'line_item' ) as $subscriptionItem ) {
			
			if ( ! ( $subscriptionItem instanceof \WC_Order_Item_Product ) ) {
				continue;
			}
			
			$product = $subscriptionItem->get_product();
			
			$parentProduct = $product->is_type( array(
				'variation',
				'subscription-variation',
			) ) ? wc_get_product( $product->get_parent_id() ) : $product;
			
			/**
			 * 1. Check for product exclusion
			 *
			 * If product in exclusion - pricing rule does not match immediately
			 */
			if ( ! empty( $this->getExcludedProducts() ) ) {
				if ( in_array( $product->get_id(), $this->getExcludedProducts() ) || in_array( $parentProduct->get_id(),
						$this->getExcludedProducts() ) ) {
					return false;
				}
			}
			
			if ( ! empty( $this->getExcludedCategories() ) ) {
				if ( ! empty( array_intersect( $parentProduct->get_category_ids(),
					$this->getExcludedCategories() ) ) ) {
					return false;
				}
			}
			
			/**
			 * 2. Check for rule limitation for specific products
			 *
			 * If yes - match rule only for selected product/product categories
			 */
			if ( ! empty( $this->getIncludedProducts() ) ) {
				$productLimitations = true;
				
				if ( in_array( $product->get_id(), $this->getIncludedProducts() ) || in_array( $parentProduct->get_id(),
						$this->getIncludedProducts() ) ) {
					$productMatched = true;
				}
			}
			
			if ( ! empty( $this->getIncludedCategories() ) ) {
				$productLimitations = true;
				
				if ( ! empty( array_intersect( $parentProduct->get_category_ids(),
					$this->getIncludedCategories() ) ) ) {
					$productMatched = true;
				}
			}
		}
		
		// There is product limitation and the product/category does not match the rule
		if ( $productLimitations && ! $productMatched ) {
			return false;
		}
		
		/**
		 * 4. If there is no users limits - match the rule immediately
		 */
		if ( empty( $this->getIncludedUserRoles() ) && empty( $this->getIncludedUsers() ) ) {
			return true;
		}
		
		/**
		 * 4. If there is users limits - check for user ID and user role.
		 */
		if ( in_array( $user->ID, $this->getIncludedUsers() ) ) {
			return true;
		}
		
		foreach ( $this->getIncludedUserRoles() as $role ) {
			if ( in_array( $role, $user->roles ) ) {
				return true;
			}
		}
		
		return false;
	}
	
	public static function buildFromPOST( $postData ): self {
		
		$data = array();
		
		foreach ( self::getDataSchema() as $key => $schemaItem ) {
			$value = $postData[ 'matching_rule_' . $key ] ?? array();
			
			if ( is_callable( $schemaItem['sanitize'] ) ) {
				$value = call_user_func( $schemaItem['sanitize'], $value );
			}
			
			$data[ $key ] = $value;
		}
		
		return self::buildFromArray( $data );
	}
	
	public function save( $offerId ) {
		$offerDataProvider = new OfferDataProvider( $offerId, 'matching_rule' );
		
		$offerDataProvider->setMeta( 'included_products', $this->getIncludedProducts() );
		$offerDataProvider->setMeta( 'excluded_products', $this->getExcludedProducts() );
		
		$offerDataProvider->setMeta( 'included_categories', $this->getIncludedCategories() );
		$offerDataProvider->setMeta( 'excluded_categories', $this->getExcludedCategories() );
		
		$offerDataProvider->setMeta( 'included_users', $this->getIncludedUsers() );
		$offerDataProvider->setMeta( 'excluded_users', $this->getExcludedUsers() );
		
		$offerDataProvider->setMeta( 'included_user_roles', $this->getIncludedUserRoles() );
		$offerDataProvider->setMeta( 'excluded_user_roles', $this->getExcludedUserRoles() );
	}
	
	protected static function buildFromArray( array $data ): self {
		
		$matchingRule = new self();
		
		$matchingRule->setIncludedProducts( $data['included_products'] ?? array() );
		$matchingRule->setExcludedProducts( $data['excluded_products'] ?? array() );
		
		$matchingRule->setIncludedCategories( $data['included_categories'] ?? array() );
		$matchingRule->setExcludedCategories( $data['excluded_categories'] ?? array() );
		
		$matchingRule->setIncludedUsers( $data['included_users'] ?? array() );
		$matchingRule->setExcludedUsers( $data['excluded_users'] ?? array() );
		
		$matchingRule->setIncludedUserRoles( $data['included_user_roles'] ?? array() );
		$matchingRule->setExcludedUserRoles( $data['excluded_user_roles'] ?? array() );
		
		return $matchingRule;
	}
}
