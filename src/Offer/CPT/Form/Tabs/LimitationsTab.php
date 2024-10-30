<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Form\Tabs;

use MeowCrew\CancellationOffers\Offer\CPT\Form\FormTab;
use MeowCrew\CancellationOffers\Offer\CPT\Form\LookupService;
use MeowCrew\CancellationOffers\Utils\Formatter;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;

class LimitationsTab extends FormTab {
	
	public function getId(): string {
		return 'limitations';
	}
	
	public function getTitle(): string {
		return __( 'Limits by Products And Users', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function getDescription(): string {
		return __( 'Select products or product categories the rule will work for.', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function render( Offer $offer ) {
		?>
		<div id="cancellation-offer-limitations-tab">
			<?php
				
			if ( empty( $offer->getMatchingRule()->getIncludedCategories() ) && empty( $offer->getMatchingRule()->getIncludedProducts() ) ) {
				$this->form->renderHint( __( 'If you do not specify products or product categories, the this offer will work for all products in your store. (excluding products selected in the exclusions section)',
					'cancellation-surveys-offers-for-woo-subscriptions' ) );
			}
				
				FormTab::renderInputRow( array(
					'id'          => 'matching_rule_included_categories',
					'type'        => 'multiple-select',
					'action'      => 'woocommerce_json_search_csows_categories',
					'placeholder' => 'Search for a category...',
					'label'       => __( 'Included Product Categories', 'cancellation-surveys-offers-for-woo-subscriptions' ),
					'options'     => ( function () use ( $offer ) {
						
						$options = array();
						
						foreach ( $offer->getMatchingRule()->getIncludedCategories() as $categoryId ) {
							$category = get_term_by( 'id', $categoryId, 'product_cat' );
							
							if ( ! $category ) {
								continue;
							}
							
							$options[ $categoryId ] = array(
								'is_selected' => true,
								'label'       => LookupService::getCategoryLabel( $category ),
							);
						}
						
						return $options;
						
					} )(),
				) );
				
				FormTab::renderInputRow( array(
					'id'          => 'matching_rule_included_products',
					'type'        => 'multiple-select',
					'action'      => 'woocommerce_json_search_products',
					'placeholder' => 'Search for a product...',
					'label'       => __( 'Included Products', 'cancellation-surveys-offers-for-woo-subscriptions' ),
					'options'     => ( function () use ( $offer ) {
						
						$options = array();
						
						foreach ( $offer->getMatchingRule()->getIncludedProducts() as $productID ) {
							$product = wc_get_product( $productID );
							
							if ( ! $product ) {
								continue;
							}
							
							$options[ $productID ] = array(
								'is_selected' => true,
								'label'       => $product->get_name(),
							);
						}
						
						return $options;
					} )(),
				) );
			
			?>

			<hr class="cancellation-offers-components__separator" data-title="
			<?php
			esc_html_e( 'Users',
				'cancellation-surveys-offers-for-woo-subscriptions' );
			?>
				">
		  
			<?php
			if ( empty( $offer->getMatchingRule()->getIncludedUsers() ) && empty( $offer->getMatchingRule()->getIncludedUserRoles() ) ) {
				$this->form->renderHint( __( 'The offer will work for all users if you do not specify user roles or specific customers. (excluding users selected in the exclusions section)',
					'cancellation-surveys-offers-for-woo-subscriptions' ) );
			}
				
				FormTab::renderInputRow( array(
					'id'          => 'matching_rule_included_user_roles',
					'type'        => 'multiple-select',
					'css_class'   => 'cos-select-woo',
					'placeholder' => 'Search for a user role...',
					'label'       => __( 'Included User Roles', 'cancellation-surveys-offers-for-woo-subscriptions' ),
					'options'     => ( function () use ( $offer ) {
						$roles = array();
						
						foreach ( wp_roles()->roles as $key => $WPRole ) {
							$roles[ $key ] = array(
								'label'       => Formatter::formatRoleString( $key ),
								'is_selected' => in_array( $key, $offer->getMatchingRule()->getIncludedUserRoles() ),
							);
						}
						
						return $roles;
						
					} )(),
				) );
				
				FormTab::renderInputRow( array(
					'id'          => 'matching_rule_included_users',
					'type'        => 'multiple-select',
					'action'      => 'woocommerce_json_search_csows_customers',
					'placeholder' => 'Search for a user...',
					'label'       => __( 'Included Users', 'cancellation-surveys-offers-for-woo-subscriptions' ),
					'options'     => ( function () use ( $offer ) {
						$options = array();
						
						foreach ( $offer->getMatchingRule()->getIncludedUsers() as $userId ) {
							$user = get_user_by( 'id', $userId );
							
							if ( ! $user ) {
								continue;
							}
							
							$options[ $userId ] = array(
								'is_selected' => true,
								'label'       => Formatter::formatCustomerString( new \WC_Customer( $userId ) ),
							);
						}
						
						return $options;
						
					} )(),
				) );
			
			?>

			<div id="cancellation-offer-limitations-tab--exclusions-toggle">
				<?php esc_html_e( 'Exclusions', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
				<span id="cancellation-offer-limitations-tab--exclusions-toggle-down">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
				</span>
				<span id="cancellation-offer-limitations-tab--exclusions-toggle-up">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</span>
			</div>
			
			<?php $displayed = $this->hasExclusion( $offer ) ? 'block' : 'none'; ?>

			<div style="margin-top: 20px; display: <?php echo esc_html( $displayed ); ?>"
				 id="cancellation-offer-limitations-tab-exclusions">
				<?php
					FormTab::renderInputRow( array(
						'id'          => 'matching_rule_excluded_categories',
						'type'        => 'multiple-select',
						'action'      => 'woocommerce_json_search_csows_categories',
						'placeholder' => 'Search for a category...',
						'label'       => __( 'Excluded Product Categories', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'options'     => ( function () use ( $offer ) {
							
							$options = array();
							
							foreach ( $offer->getMatchingRule()->getExcludedCategories() as $categoryId ) {
								$category = get_term_by( 'id', $categoryId, 'product_cat' );
								
								if ( ! $category ) {
									continue;
								}
								
								$options[ $categoryId ] = array(
									'is_selected' => true,
									'label'       => LookupService::getCategoryLabel( $category ),
								);
							}
							
							return $options;
							
						} )(),
					) );
					
					FormTab::renderInputRow( array(
						'id'          => 'matching_rule_excluded_products',
						'type'        => 'multiple-select',
						'action'      => 'woocommerce_json_search_products',
						'placeholder' => 'Search for a product...',
						'label'       => __( 'Excluded Products', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'options'     => ( function () use ( $offer ) {
							$options = array();
							
							foreach ( $offer->getMatchingRule()->getExcludedProducts() as $productID ) {
								$product = wc_get_product( $productID );
								
								if ( ! $product ) {
									continue;
								}
								
								$options[ $productID ] = array(
									'is_selected' => true,
									'label'       => $product->get_name(),
								);
							}
							
							return $options;
						} )(),
					) );
				
				
				?>

				<hr class="cancellation-offers-components__separator" data-title="
				<?php
				esc_html_e( 'Users',
					'cancellation-surveys-offers-for-woo-subscriptions' );
				?>
					">
				
				<?php
					FormTab::renderInputRow( array(
						'id'          => 'matching_rule_excluded_user_roles',
						'type'        => 'multiple-select',
						'css_class'   => 'cos-select-woo',
						'placeholder' => 'Search for a user role...',
						'label'       => __( 'Excluded User Roles', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'options'     => ( function () use ( $offer ) {
							$roles = array();
							
							foreach ( wp_roles()->roles as $key => $WPRole ) {
								$roles[ $key ] = array(
									'label'       => Formatter::formatRoleString( $key ),
									'is_selected' => in_array( $key,
										$offer->getMatchingRule()->getExcludedUserRoles() ),
								);
							}
							
							return $roles;
							
						} )(),
					) );
					
					FormTab::renderInputRow( array(
						'id'          => 'matching_rule_excluded_users',
						'type'        => 'multiple-select',
						'action'      => 'woocommerce_json_search_csows_customers',
						'placeholder' => 'Search for a user...',
						'label'       => __( 'Excluded Users', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'options'     => ( function () use ( $offer ) {
							$options = array();
							
							foreach ( $offer->getMatchingRule()->getExcludedUsers() as $userId ) {
								$user = get_user_by( 'id', $userId );
								
								if ( ! $user ) {
									continue;
								}
								
								$options[ $userId ] = array(
									'is_selected' => true,
									'label'       => Formatter::formatCustomerString( new \WC_Customer( $userId ) ),
								);
							}
							
							return $options;
						} )(),
					) );
				
				?>
			</div>
		</div>
		<?php
	}
	
	public function hasExclusion( Offer $offer ): bool {
		return ! empty( $offer->getMatchingRule()->getExcludedCategories() ) || ! empty( $offer->getMatchingRule()->getExcludedProducts() ) || ! empty( $offer->getMatchingRule()->getExcludedUsers() ) || ! empty( $offer->getMatchingRule()->getExcludedUserRoles() );
	}
}