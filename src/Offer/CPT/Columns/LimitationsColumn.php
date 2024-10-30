<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Columns;

use MeowCrew\CancellationOffers\Utils\Formatter;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;
use WC_Customer;
use WC_Product;
use WP_Term;

class LimitationsColumn {
	
	public function getName(): string {
		return __( 'Users & Products Limitations', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function render( Offer $offer ) {
		
		$hasCustomers = $this->showCustomers( $offer->getMatchingRule()->getIncludedUsers() );
		$hasRoles     = $this->showUserRoles( $offer->getMatchingRule()->getIncludedUserRoles() );
		
		$hasProducts   = $this->showProducts( $offer->getMatchingRule()->getIncludedProducts() );
		$hasCategories = $this->showCategories( $offer->getMatchingRule()->getIncludedCategories() );
		
		
		if ( ! $hasProducts && ! $hasCategories && ! $hasRoles && ! $hasCustomers ) {
			?>
			<b style="color:#d63638">
				<?php esc_html_e( 'Applied to every product and user', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
			</b>
			<br>
			<br>
			<?php
		} else {
			if ( ! $hasProducts && ! $hasCategories ) {
				?>
				<b style="color:#d63638">
					<?php esc_html_e( 'Applied to every product', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
				</b>
				<br>
				<br>
				<?php
			}
			
			if ( ! $hasRoles && ! $hasCustomers ) {
				?>
				<b style="color:#d63638">
					<?php esc_html_e( 'Applied to every user', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
				</b>
				<br>
				<br>
				<?php
			}
		}
		
		$this->showProducts( $offer->getMatchingRule()->getExcludedProducts(), false );
		$this->showCategories( $offer->getMatchingRule()->getExcludedCategories(), false );
		
		$this->showCustomers( $offer->getMatchingRule()->getExcludedUsers(), false );
		$this->showUserRoles( $offer->getMatchingRule()->getExcludedUserRoles(), false );
	}
	
	public function showProducts( array $productsIds, $included = true ): bool {
		
		$moreThanCanBeShown = count( $productsIds ) > 10;
		
		$productsIds = array_slice( $productsIds, 0, 5 );
		
		$products = array_filter( array_map( function ( $productId ) {
			return wc_get_product( $productId );
		}, $productsIds ) );
		
		if ( ! empty( $products ) ) {
			
			if ( $included ) {
				esc_html_e( 'Products: ', 'cancellation-surveys-offers-for-woo-subscriptions' );
			} else {
				esc_html_e( 'Excluded products: ', 'cancellation-surveys-offers-for-woo-subscriptions' );
			}
			
			$productsString = array_map( function ( WC_Product $product ) {
				return sprintf( '<a href="%s" target="_blank">%s</a>',
					get_edit_post_link( $product->get_parent_id() ? $product->get_parent_id() : $product->get_id() ),
					$product->get_name() );
			}, $products );
			
			echo wp_kses_post( implode( ', ', $productsString ) . ( $moreThanCanBeShown ? '<span> ...</span>' : '' ) );
			
			echo '<br><br>';
			
			return true;
		}
		
		return false;
	}
	
	public function showCategories( array $categoriesIds, $included = true ): bool {
		$moreThanCanBeShown = count( $categoriesIds ) > 10;
		$categoriesIds      = array_slice( $categoriesIds, 0, 10 );
		
		$categories = array_filter( array_map( function ( $categoryId ) {
			return get_term( $categoryId );
		}, $categoriesIds ) );
		
		$categories = array_filter( $categories, function ( $category ) {
			return $category instanceof WP_Term;
		} );
		
		if ( ! empty( $categories ) ) {
			
			if ( $included ) {
				esc_html_e( 'Categories: ', 'cancellation-surveys-offers-for-woo-subscriptions' );
			} else {
				esc_html_e( 'Excluded categories: ', 'cancellation-surveys-offers-for-woo-subscriptions' );
			}
			
			$categoriesString = array_map( function ( WP_Term $category ) {
				return sprintf( '<a href="%s" target="_blank">%s</a>', get_edit_term_link( $category->term_id ),
					$category->name );
			}, $categories );
			
			echo wp_kses_post( implode( ', ',
					$categoriesString ) . ( $moreThanCanBeShown ? '<span> ...</span>' : '' ) );
			
			echo '<br><br>';
			
			return true;
		}
		
		return false;
	}
	
	public function showCustomers( array $customersIds, $included = true ): bool {
		$customersMoreThanCanBeShown = count( $customersIds ) > 10;
		
		$customersIds = array_slice( $customersIds, 0, 5 );
		
		$customers = array_filter( array_map( function ( $customerId ) {
			try {
				return new WC_Customer( $customerId );
			} catch ( \Exception $e ) {
				return false;
			}
		}, $customersIds ) );
		
		if ( ! empty( $customers ) ) {
			
			if ( $included ) {
				esc_html_e( 'Customers: ', 'cancellation-surveys-offers-for-woo-subscriptions' );
			} else {
				esc_html_e( 'Excluded customers: ', 'cancellation-surveys-offers-for-woo-subscriptions' );
			}
			
			$customersString = array_map( function ( WC_Customer $customer ) {
				return Formatter::formatCustomerString( $customer, true );
			}, $customers );
			
			echo wp_kses_post( implode( ', ',
					$customersString ) . ( $customersMoreThanCanBeShown ? '<span> ...</span>' : '' ) );
			
			echo '<br><br>';
			
			return true;
		}
		
		return false;
	}
	
	public function showUserRoles( array $roles, $included = true ): bool {
		
		if ( ! empty( $roles ) ) {
			if ( $included ) {
				esc_html_e( 'Roles: ', 'cancellation-surveys-offers-for-woo-subscriptions' );
			} else {
				esc_html_e( 'Excluded roles: ', 'cancellation-surveys-offers-for-woo-subscriptions' );
			}
			
			$rolesString = array_map( function ( $role ) {
				return sprintf( '<span>%s</span>', Formatter::formatRoleString( $role ) );
			}, $roles );
			
			echo wp_kses_post( implode( ', ', $rolesString ) );
			
			echo '<br><br>';
			
			return true;
		}
		
		return false;
	}
}