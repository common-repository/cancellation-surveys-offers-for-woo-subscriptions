<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Form;

use Exception;
use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Utils\Formatter;
use WC_Customer;
use WP_Term;
use WP_User_Query;

class LookupService {
	
	use ServiceContainerTrait;
	
	const CATEGORIES_SEARCH_ACTION = 'woocommerce_json_search_csows_categories';
	const CUSTOMERS_SEARCH_ACTION = 'woocommerce_json_search_csows_customers';
	
	public function __construct() {
		add_action( 'wp_ajax_' . self::CATEGORIES_SEARCH_ACTION, array( $this, 'categoriesSearchHandler' ) );
		add_action( 'wp_ajax_' . self::CUSTOMERS_SEARCH_ACTION, array( $this, 'customersSearchHandler' ) );
	}
	
	public function customersSearchHandler() {
		
		if ( ! current_user_can( 'manage_options' ) ) {

			wp_send_json( array() );
		}

		$term = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : false;
		
		if ( $term ) {
			$wp_user_query = new WP_User_Query( array(
				'search'         => '*' . $term . '*',
				'search_columns' => array(
					'user_login',
					'user_nicename',
					'user_email',
				),
				'fields'         => 'ID',
			) );
			
			$users = $wp_user_query->get_results();
			
			if ( $users ) {
				$_users = array();
				
				foreach ( $users as $userId ) {
					try {
						$customer = new WC_Customer( $userId );
					} catch ( Exception $e ) {
						continue;
					}
					
					if ( $customer instanceof WC_Customer ) {
						$_users[ $userId ] = Formatter::formatCustomerString( $customer );
					}
					
				}
				
				wp_send_json( $_users );
			}
		}
		
		wp_send_json( array() );
	}
	
	public function categoriesSearchHandler() {
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( array() );
		}
		
		$term = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : false;
		
		if ( $term ) {
			$args = array(
				'taxonomy'   => array( 'product_cat' ),
				'orderby'    => 'id',
				'order'      => 'ASC',
				'limit'      => 5,
				'hide_empty' => false,
				'fields'     => 'all',
				'name__like' => $term,
			);
			
			$terms = get_terms( $args );
			
			if ( $terms ) {
				$_terms = array();
				
				foreach ( $terms as $term ) {
					if ( $term instanceof WP_Term ) {
						$_terms[ $term->term_id ] = self::getCategoryLabel( $term );
					}
				}
				
				wp_send_json( $_terms );
			}
		}
		
		wp_send_json( array() );
	}
	
	public static function getCategoryLabel( WP_Term $category ): string {
		$parentTermName = '';
		
		if ( $category->parent ) {
			$parentTerm = get_term( $category->parent );
			
			if ( $parentTerm ) {
				$parentTermName = ' (' . $parentTerm->name . ')';
			}
		}
		
		return $category->name . $parentTermName;
	}
}
