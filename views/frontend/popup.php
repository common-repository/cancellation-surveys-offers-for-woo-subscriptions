<?php use MeowCrew\CancellationOffers\Offer\Entity\Offer;
	
	defined( 'ABSPATH' ) || exit;
	
	/**
	 * Available variables
	 *
	 * @var string $take_discount_url
	 * @var string $cancel_subscription_url
	 * @var Offer $offer
	 * @var WC_Subscription $subscription
	 */
	
	$labels = apply_filters( 'cancellation_offers/frontend/popup/labels', array(
		'survey' => array(
			'continue_button'      => __( 'Continue', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			'never_mind_button'    => __( 'Never mind, I\'ve changed my mind',
				'cancellation-surveys-offers-for-woo-subscriptions' ),
			'textarea_placeholder' => __( 'Please specify', 'cancellation-surveys-offers-for-woo-subscriptions' ),
		),
	), $offer, $subscription );
	?>

<div id="cancellation-offer-popup"
	 data-is-survey-enabled="<?php echo esc_html(wc_bool_to_string( $offer->getSurvey()->isEnabled() )); ?>"
	 data-is-discount-offer-enabled="<?php echo esc_html(wc_bool_to_string( $offer->getDiscountOffer()->isApplicableForSubscription( $subscription ) )); ?>"
	 data-cancel-subscription-url="<?php echo esc_attr( $cancel_subscription_url ); ?>"
	 data-take-discount-url="<?php echo esc_attr( $take_discount_url ); ?>">

	<div style="display: none" class="cancellation-offer-popup-survey">
		
        <?php do_action('cancellation_offers/frontend/popup/before_title', $offer, $subscription, $labels); ?>
        
		<h2 class="cancellation-offer-popup-survey__title" style="text-align: center">
			<?php echo esc_html( $offer->getSurvey()->getTitle() ); ?>
		</h2>
        
        <?php do_action('cancellation_offers/frontend/popup/after_title', $offer, $subscription, $labels); ?>

		<p class="cancellation-offer-popup-survey__description" style="text-align: center">
			<?php echo esc_html( $offer->getSurvey()->getDescription() ); ?>
		</p>

		<div class="cancellation-offer-popup-survey__questions">
			<div class="cancellation-offer-popup-survey-items">
				<?php foreach ( $offer->getSurvey()->getItems() as $item ) : ?>
					<div class="cancellation-offer-popup-survey-item"
						 tabindex="0"
						 role="button"
						 aria-pressed="false"
						 data-slug="<?php echo esc_attr( $item->getSlug() ); ?>"
						 data-is-text-answer-enabled="<?php echo esc_attr( wc_bool_to_string( $item->isTextAnswerEnabled() ) ); ?>"
						 data-is-discount-offer-enabled="<?php echo esc_attr( wc_bool_to_string( $item->isDiscountOfferEnabled( $offer ) ) ); ?>">

						<div class="cancellation-offer-popup-survey-item__checkbox">
							<div class="cancellation-offer-popup-survey-item-checkbox cancellation-offer-popup-survey-item--active">
							</div>
						</div>

						<div class="cancellation-offer-popup-survey-item__text">
							<b><?php echo esc_attr( $item->getTitle() ); ?></b>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
   
			<?php do_action('cancellation_offers/frontend/popup/after_survey_questions', $offer, $subscription, $labels); ?>
   
			<div style="margin-top: 20px; display:none; width: 100%" class="cancellation-offer-popup-survey-items__textarea">
				<textarea placeholder="<?php echo esc_html( $labels['survey']['textarea_placeholder'] ); ?>"
						  rows="3"></textarea>
			</div>
		</div>
		
		<?php do_action('cancellation_offers/frontend/popup/after_survey_block', $offer, $subscription, $labels); ?>

        <div class="cancellation-offer-popup-survey__buttons">
			<div>
				<button class="button primary-button cancellation-offer-popup-survey__continue-button" disabled>
					<?php echo esc_html( $labels['survey']['continue_button'] ); ?>
				</button>
			</div>
			<div>
				<a href="#" role="button" class="cancellation-offer-popup-survey__dismiss-button">
					<?php echo esc_html( $labels['survey']['never_mind_button'] ); ?>
				</a>
			</div>
	        
	        <?php do_action('cancellation_offers/frontend/popup/survey_buttons', $offer, $subscription, $labels); ?>
        </div>
	</div>

	<div style="display: none" class="cancellation-offer-popup-discount-offer">
		
		<?php do_action('cancellation_offers/frontend/popup/before_discount_title', $offer, $subscription, $labels); ?>
        
        <h2 class="cancellation-offer-popup-discount-offer__title" style="text-align: center">
			<?php echo esc_html( $offer->getDiscountOffer()->getTitle() ); ?>
		</h2>
		
		<?php do_action('cancellation_offers/frontend/popup/after_discount_title', $offer, $subscription, $labels); ?>

        <p class="cancellation-offer-popup-discount-offer__description" style="text-align: center">
			<?php echo esc_html( $offer->getDiscountOffer()->getDescription() ); ?>
		</p>
		
		<?php do_action('cancellation_offers/frontend/popup/after_discount_description', $offer, $subscription, $labels); ?>

        <div class="cancellation-offer-popup-discount-offer__buttons" style="text-align: center; margin-top: 20px">
			<div>
				<button class="button primary-button cancellation-offer-popup-discount-offer__continue-button">
					<?php echo esc_html( $offer->getDiscountOffer()->getApplyDiscountButtonLabel() ); ?>
				</button>
			</div>
			<br>
			<div>
				<a href="#" class="cancellation-offer-popup-discount-offer__dismiss-button">
					<?php echo esc_html( $offer->getDiscountOffer()->getCancelSubscriptionButtonLabel() ); ?>
				</a>
			</div>
	        
	        <?php do_action('cancellation_offers/frontend/popup/discount_buttons', $offer, $subscription, $labels); ?>
        </div>

	</div>
</div>