<?php namespace MeowCrew\CancellationOffers\Offer\CPT;

use Automattic\WooCommerce\Admin\PageController;
use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Offer\CPT\Actions\ReactivateAction;
use MeowCrew\CancellationOffers\Offer\CPT\Actions\SuspendAction;
use MeowCrew\CancellationOffers\Offer\CPT\Columns\LimitationsColumn;
use MeowCrew\CancellationOffers\Offer\CPT\Columns\OfferColumn;
use MeowCrew\CancellationOffers\Offer\CPT\Columns\SettingsColumn;
use MeowCrew\CancellationOffers\Offer\CPT\Columns\Status;
use MeowCrew\CancellationOffers\Offer\CPT\Columns\SurveyColumn;
use MeowCrew\CancellationOffers\Offer\CPT\Form\Form;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;
use WP_Post;

class CancellationOffersCPT {
	
	use ServiceContainerTrait;
	
	const SLUG = 'cancellation-offer';
	
	/**
	 * Cancellation Offer instance
	 *
	 * @var Offer
	 */
	private $offer;
	
	/**
	 * Table columns
	 *
	 * @var array
	 */
	private $columns;
	
	protected static $offers = null;
	
	public function __construct() {
		
		new Form();
		
		add_action( 'init', array( $this, 'register' ) );
		
		add_action( 'admin_head', function () {
			?>
            <style>
				.wp-submenu a[href="post-new.php?post_type=<?php echo esc_attr( self::SLUG ); ?>"] {
					display: none !important;
				}
            </style>
			<?php
		} );
		
		add_action( 'manage_posts_extra_tablenav', array( $this, 'renderBlankState' ) );
		
		add_filter( 'woocommerce_navigation_screen_ids', array( $this, 'addPageToWooCommerceScreen' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'addPageToWooCommerceScreen' ) );
		
		add_action( 'save_post_' . self::SLUG, array( $this, 'saveOffer' ), 10, 3 );
		
		add_filter( 'manage_edit-' . self::SLUG . '_columns', function ( $columns ) {
			unset( $columns['date'] );
			
			foreach ( $this->getColumns() as $key => $column ) {
				$columns[ $key ] = $column->getName();
			}
			
			return $columns;
		}, 999 );
		
		add_filter( 'manage_' . self::SLUG . '_posts_custom_column', function ( $column ) {
			global $post;
			
			$globalRule = Offer::build( $post->ID );
			
			if ( array_key_exists( $column, $this->getColumns() ) ) {
				$this->getColumns()[ $column ]->render( $globalRule );
				
				do_action( 'cancellation_offers/admin/offer/after_tab_render', $column, $globalRule );
			}
			
			return $column;
		}, 999 );
		
		add_filter( 'post_row_actions', function ( $actions, WP_Post $post ) {
			
			if ( self::SLUG !== $post->post_type ) {
				return $actions;
			}
			
			unset( $actions['inline hide-if-no-js'] );
			
			return $actions;
		}, 10, 2 );
		
		add_filter( 'disable_months_dropdown', function ( $state, $postType ) {
			if ( self::SLUG === $postType ) {
				return true;
			}
			
			return $state;
		}, 10, 2 );
		
		$this->initInlineActions();
	}
	
	public function initInlineActions() {
		new SuspendAction();
		new ReactivateAction();
	}
	
	public function getColumns(): array {
		
		if ( is_null( $this->columns ) ) {
			$this->columns = array(
				'offer'       => new OfferColumn(),
				'survey'      => new SurveyColumn(),
				'limitations' => new LimitationsColumn(),
				'status'      => new Status(),
			);
		}
		
		return $this->columns;
	}
	
	public function addPageToWooCommerceScreen( $ids ) {
		
		$ids[] = self::SLUG;
		$ids[] = 'edit-' . self::SLUG;
		
		return $ids;
	}
	
	public function saveOffer( $offerId, $post, $isUpdate ) {
		
		if ( ! $isUpdate ) {
			return;
		}
		
		if ( wp_verify_nonce( true, true ) ) {
			// as phpcs comments at Woo is not available, we have to do such a trash
			$woo = 'Woo, please add ignoring comments to your phpcs checker';
		}
		
		$offer = Offer::buildFromPOST( $offerId, $_POST );
		
		do_action( 'cancellation_offers/admin/offer/before_updating', $offer, $offerId );
		
		$offer->save();
	}
	
	public function renderBlankState( $which ) {
		
		global $post_type;
		
		if ( self::SLUG === $post_type && 'bottom' === $which ) {
			$counts = (array) wp_count_posts( $post_type );
			unset( $counts['auto-draft'] );
			$count = array_sum( $counts );
			
			if ( 0 < $count ) {
				return;
			}
			?>
            <style>
				#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub {
					display: none;
				}

				#posts-filter .tablenav.bottom {
					height: auto;
				}
            </style>

            <div class="woocommerce-BlankState" style="padding: 0">
                <img width="250px" style="filter: drop-shadow(1px 10px 10px #ccc);"
                     src="<?php echo esc_attr( $this->getContainer()->getFileManager()->locateAsset( 'admin/logo.png' ) ); ?>">
                <h2 class="woocommerce-BlankState-message" style="margin-top: 20px">
					<?php
						esc_html_e( 'There are no cancellation offers & surveys yet. To create the first offer rule click on the button below.',
							'cancellation-surveys-offers-for-woo-subscriptions' );
					?>
                </h2>

                <div class="woocommerce-BlankState-buttons">
                    <a class="woocommerce-BlankState-cta button-primary button"
                       href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . self::SLUG ) ); ?>">
						<?php
							esc_html_e( 'Create a cancellation offer',
								'cancellation-surveys-offers-for-woo-subscriptions' );
						?>
                    </a>
                </div>
            </div>
			<?php
		}
	}
	
	public function register() {
		
		if ( class_exists( '\Automattic\WooCommerce\Admin\PageController' ) ) {
			PageController::get_instance()->connect_page( array(
				'id'        => 'edit-' . self::SLUG,
				'title'     => array( 'Surveys & Offers' ),
				'screen_id' => 'edit-' . self::SLUG,
			) );
			
			PageController::get_instance()->connect_page( array(
				'id'        => self::SLUG,
				'title'     => array( 'Surveys & Offers' ),
				'screen_id' => self::SLUG,
			) );
		}
		
		register_post_type( self::SLUG, array(
			'labels'             => array(
				'name'              => __( 'Cancellation Offers',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'singular_name'     => __( 'Cancellation Offer',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'add_new'           => __( 'Add Cancellation Offer',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'add_new_item'      => __( 'Add Cancellation Offer',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'edit_item'         => __( 'Edit Cancellation Offer',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'new_item'          => __( 'New Cancellation Offer',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'parent_item_colon' => '',
				'menu_name'         => __( 'Offers & Surveys',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
			
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'product',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_icon'          => 'dashicons-clipboard',
			'menu_position'      => null,
			'supports'           => array( 'title' ),
		) );
	}
	
	/**
	 * Get offers instances
	 *
	 * @param  bool  $valid
	 *
	 * @return Offer[]
	 */
	public static function getOffers( bool $valid = true ): array {
		
		if ( ! is_null( self::$offers ) ) {
			$offers = self::$offers;
		} else {
			$offerIds = get_posts( array(
				'numberposts' => - 1,
				'post_type'   => self::SLUG,
				'post_status' => 'publish',
				'fields'      => 'ids',
			) );
			
			$offers = array_map( function ( $ruleId ) {
				return Offer::build( $ruleId );
			}, $offerIds );
			
			self::$offers = $offers;
		}
		
		if ( $valid ) {
			$offers = array_filter( $offers, function ( Offer $rule ) {
				return $rule->isValid();
			} );
		}
		
		return $offers;
	}
}
