<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Form;

use MeowCrew\CancellationOffers\Offer\CPT\CancellationOffersCPT;
use MeowCrew\CancellationOffers\Offer\CPT\Form\Tabs\DiscountsOfferTab;
use MeowCrew\CancellationOffers\Offer\CPT\Form\Tabs\GeneralSettingsTab;
use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Offer\CPT\Form\Tabs\LimitationsTab;
use MeowCrew\CancellationOffers\Offer\CPT\Form\Tabs\SurveyTab;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;
use WP_Post;

class Form {
	
	use ServiceContainerTrait;
	
	/**
	 * Tabs
	 *
	 * @var FormTab[]
	 */
	protected $tabs;
	
	protected $defaultTab = 'survey';
	
	public function __construct() {
		
		new LookupService();
		
		add_action( 'init', function () {
			$this->tabs = apply_filters( 'cancellation_offers/global_pricing/form_tabs', array(
				new SurveyTab( $this ),
				new DiscountsOfferTab( $this ),
				//  new GeneralSettingsTab( $this ),
				new LimitationsTab( $this ),
			) );
		} );
		
		add_action( 'edit_form_after_title', function ( WP_Post $post ) {
			if ( CancellationOffersCPT::SLUG !== $post->post_type ) {
				return;
			}
			
			$this->render( $post );
		} );
	}
	
	protected function render( WP_Post $post ) {
		
		$offerInstance = $this->getOfferInstance( $post );
		
		$rulesCount = (int) wp_count_posts( CancellationOffersCPT::SLUG )->publish;
		
		if ( $this->isNewRule() && $rulesCount < 1 ) {
			$this->renderHelpingSteps();
		}
		
		if ( ! $this->isNewRule() ) {
			
			try {
				$offerInstance->validate();
			} catch ( \Exception $e ) {
				$this->renderHint( $e->getMessage(), array( 'custom_class' => 'cos-offer-and-survey-hint--red' ) );
			}
		}
		
		?>
        <div class="cos-offer-and-survey-freemius-block">

            <div style="display: flex;">
				
				<?php if ( csows_fs()->can_use_premium_code() ): ?>
                    <div class="cos-offer-and-survey-freemius-block__icon">
                        🎉
                    </div>
				<?php endif; ?>

                <div class="cos-offer-and-survey-freemius-block__title">
					<?php
						if ( csows_fs()->can_use_premium_code() ) {
							esc_html_e( 'You use the premium version of the plugin.',
								'cancellation-surveys-offers-for-woo-subscriptions' );
						} else {
							esc_html_e( 'You use the free version of the plugin.',
								'cancellation-surveys-offers-for-woo-subscriptions' );
						}
					?>
                </div>
            </div>

            <div class="cos-offer-and-survey-freemius-block__buttons">
				<?php
					if ( csows_fs()->can_use_premium_code() ) {
						?>
                        <a href="<?php echo esc_attr( csows_account_url() ); ?>" target="_blank"
                           class="button button-primary">
							<?php esc_html_e( 'Account', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
                        </a>
						<?php
					} else {
						?>
                        <a href="<?php echo esc_attr( csows_upgrade_url() ) ?>"
                           class="button button-primary cos-offer-and-survey-freemius-block__upgrade-button">
							<?php esc_html_e( 'Upgrade', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
                        </a>
						<?php
					}
				?>

                <a href="<?php echo esc_attr( csows_contact_us_url() ); ?>" class="button" target="_blank">
					<?php esc_html_e( 'Contact us', 'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
                </a>
            </div>
        </div>

        <div class="cos-offer-and-survey-form">
            <div class="cos-offer-and-survey-form__tabs">
				<?php foreach ( $this->tabs as $tab ) : ?>
                    <div class="cos-offer-and-survey-form-tab <?php echo esc_attr( $tab->getId() === $this->defaultTab ? 'cos-offer-and-survey-form-tab--active' : '' ); ?>"
                         data-target="cos-offer-and-survey-form-tab-<?php echo esc_attr( $tab->getId() ); ?>">

                        <div class="cos-offer-and-survey-form-tab__icon">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                        </div>

                        <div class="cos-offer-and-survey-form-tab__title">
                            <h3><?php echo esc_html( $tab->getTitle() ); ?></h3>
                            <div><?php echo esc_html( $tab->getDescription() ); ?></div>
                        </div>
                    </div>
				<?php endforeach; ?>
            </div>

            <div class="cos-offer-and-survey-form__content">
				<?php foreach ( $this->tabs as $tab ) : ?>
                    <div class="cos-offer-and-survey-form-tab-content <?php echo esc_attr( $tab->getId() === $this->defaultTab ? 'cos-offer-and-survey-form-tab-content--active' : '' ); ?>"
                         id="cos-offer-and-survey-form-tab-<?php echo esc_attr( $tab->getId() ); ?>">
						<?php
							
							do_action( 'cancellation_offers/tabs/begin', $tab, $offerInstance );
							
							$tab->render( $offerInstance );
							
							do_action( 'cancellation_offers/tabs/end', $tab, $offerInstance );
						?>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
		<?php
	}
	
	/**
	 * Get pricing rule instance
	 *
	 * @param  WP_Post  $post
	 *
	 * @return Offer
	 */
	public function getOfferInstance( WP_Post $post ): Offer {
		if ( empty( $this->offerInstance ) ) {
			$this->offerInstance = Offer::build( $post->ID );
		}
		
		return $this->offerInstance;
	}
	
	public function renderHint( $hint, $args = array() ) {
		
		$args = wp_parse_args( $args, array(
			'only_for_new_rules' => false,
			'show_icon'          => true,
			'custom_class'       => '',
		) );
		
		if ( ! $hint ) {
			return;
		}
		
		if ( $args['only_for_new_rules'] && ! $this->isNewRule() ) {
			return;
		}
		
		?>
        <div class="cos-offer-and-survey-hint <?php echo esc_attr( $args['custom_class'] ); ?>">
			<?php if ( $args['show_icon'] ) : ?>
                <div class="cos-offer-and-survey-hint__icon">
                    <span class="dashicons dashicons-info"></span>
                </div>
			<?php endif; ?>
            <div class="cos-offer-and-survey-hint__content">
				<?php echo wp_kses_post( $hint ); ?>
            </div>
        </div>
		<?php
	}
	
	public function renderHelpingSteps() {
		?>
		<?php
		$steps = array(
			array(
				'title'         => __( 'Create Survey', 'cancellation-surveys-offers-for-woo-subscriptions' ),
				'description'   => __( 'Define cancellation reasons.',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'icon'          => '<span class="dashicons dashicons-feedback"></span>',
				'has_next_step' => true,
			),
			array(
				'title'         => __( 'Offer discount', 'cancellation-surveys-offers-for-woo-subscriptions' ),
				'description'   => __( 'Select a coupon code for retention',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'icon'          => '<span class="dashicons dashicons-tickets-alt"></span>',
				'has_next_step' => true,
			),
			array(
				'title'         => __( 'Products & Roles', 'cancellation-surveys-offers-for-woo-subscriptions' ),
				'description'   => __( 'Limit survey and offers to specific products or user roles.',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'icon'          => '<span class="dashicons dashicons-admin-users"></span>',
				'has_next_step' => false,
			),
		);
		
		?>
        <div class="cos-offer-and-survey-helping">
            <div class="cos-offer-and-survey-helping__title">
				<?php
					esc_html_e( 'Create Survey Form and Offer Step-by-Step',
						'cancellation-surveys-offers-for-woo-subscriptions' );
				?>
            </div>
            <p>
				<?php
					esc_html_e( 'Complete this form with your survey questions, discount offers, and limitations. This will help you understand the reasons behind subscription cancellations and enable you to retain customers with targeted discount offers.',
						'cancellation-surveys-offers-for-woo-subscriptions' );
				?>
            </p>

            <div class="cos-offer-and-survey-helping__steps">
				
				<?php foreach ( $steps as $step ) : ?>

                    <div class="cos-offer-and-survey-helping-step">
                        <div class="cos-offer-and-survey-helping-step__icon">
							<?php echo wp_kses_post( $step['icon'] ); ?>
                        </div>

                        <div class="cos-offer-and-survey-helping-step__title">
							<?php echo esc_html( $step['title'] ); ?>
                        </div>

                        <div class="cos-offer-and-survey-helping-step__description">
							<?php echo esc_html( $step['description'] ); ?>
                        </div>
                    </div>
					
					<?php if ( $step['has_next_step'] ) : ?>
                        <div class="cos-offer-and-survey-helping-step cos-offer-and-survey-helping-step--arrow">
                            <span class="dashicons dashicons-arrow-right-alt"></span>
                        </div>
					<?php endif; ?>
				<?php endforeach; ?>
            </div>
            <div class="cos-offer-and-survey-helping__close">
                &times;
            </div>
        </div>
		<?php
	}
	
	public function isNewRule(): bool {
		global $pagenow;
		
		return 'post-new.php' == $pagenow;
	}
}