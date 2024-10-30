<?php

namespace MeowCrew\CancellationOffers\Offer\CPT\Form\Tabs;

use MeowCrew\CancellationOffers\Offer\CPT\Form\FormTab;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;
class DiscountsOfferTab extends FormTab {
    public function getId() : string {
        return 'discount-offer';
    }

    public function getTitle() : string {
        return __( 'Discount Offer', 'cancellation-surveys-offers-for-woo-subscriptions' );
    }

    public function getDescription() : string {
        return __( 'Select a coupon code for retention.', 'cancellation-surveys-offers-for-woo-subscriptions' );
    }

    public function render( Offer $offer ) {
        if ( !csows_fs()->can_use_premium_code() ) {
            ?>
            <div class="cos-offer-and-survey-hint" style="color: red;
				margin-top: -16px;
				margin-left: -16px;
				width: calc(100% + 32px);
				padding: 15px;
				box-sizing: border-box;
				font-size: 1.1em;">
                <div class="cos-offer-and-survey-hint__icon">
                    <span class="dashicons dashicons-info"></span>
                </div>
                <div class="cos-offer-and-survey-hint__content">
					<?php 
            esc_html_e( 'Discount offer is available only in the premium version.', 'cancellation-surveys-offers-for-woo-subscriptions' );
            ?>

                    <a href="<?php 
            echo esc_url( csows_upgrade_url() );
            ?>"
                       target="_blank"><?php 
            esc_html_e( 'Upgrade your plan', 'cancellation-surveys-offers-for-woo-subscriptions' );
            ?>
                        <svg style="
							width: 0.8rem;
							height: 0.8rem;
							stroke: currentColor;
							fill: none;"
                             xmlns='http://www.w3.org/2000/svg'
                             stroke-width='10' stroke-dashoffset='0'
                             stroke-dasharray='0' stroke-linecap='round'
                             stroke-linejoin='round' viewBox='0 0 100 100'>
                            <polyline fill="none" points="40 20 20 20 20 90 80 90 80 60"/>
                            <polyline fill="none" points="60 10 90 10 90 40"/>
                            <line fill="none" x1="89" y1="11" x2="50" y2="50"/>
                        </svg>
                    </a>
                </div>
            </div>
			<?php 
        }
        ?>
		
		<?php 
        $premiumOnlyClass = 'cos-offer-and-survey--premium-only';
        ?>

        <div id="cancellation-offer-discount-offer" class="<?php 
        echo esc_attr( $premiumOnlyClass );
        ?>">
			
			<?php 
        $this->form->renderHint( __( 'Link a specific coupon code that will be offered to subscribers as a reason to retain their subscription. Customize the second pop-up step here, or disable if you don\'t want to offer discounts.', 'cancellation-surveys-offers-for-woo-subscriptions' ) );
        ?>

            <div id="cancellation-offer-discount-offer-tab__is-enabled">
				<?php 
        FormTab::renderInputRow( array(
            'id'      => 'discount_offer_is_enabled',
            'type'    => 'checkbox',
            'checked' => $offer->getDiscountOffer()->isEnabled(),
            'label'   => __( 'Enable Discount Offer', 'cancellation-surveys-offers-for-woo-subscriptions' ),
        ) );
        ?>
            </div>

            <div id="cancellation-offer-discount-offer-tab__inner-options">
				<?php 
        FormTab::renderInputRow( array(
            'id'    => 'discount_offer_title',
            'type'  => 'text',
            'value' => $offer->getDiscountOffer()->getTitle(),
            'label' => __( 'Title', 'cancellation-surveys-offers-for-woo-subscriptions' ),
        ) );
        ?>
				<?php 
        FormTab::renderInputRow( array(
            'id'    => 'discount_offer_description',
            'type'  => 'textarea',
            'value' => $offer->getDiscountOffer()->getDescription(),
            'label' => __( 'Description', 'cancellation-surveys-offers-for-woo-subscriptions' ),
        ) );
        ?>

                <div id="cancellation-offer-discount-offer-tab__survey-option">
                    <div class="cancellation-offers-components-row">
                        <div class="cancellation-offers-components-row__label">
                            <label for="discount_offer_survey_options">
								<?php 
        esc_html_e( 'Cancellation reasons', 'cancellation-surveys-offers-for-woo-subscriptions' );
        ?>
                            </label>
                        </div>
                        <div class="cancellation-offers-components-row__value">
                            <select name="discount_offer_survey_options[]" id="discount_offer_survey_options" multiple
                                    class="cos-select-woo">
								<?php 
        foreach ( $offer->getSurvey()->getItems() as $item ) {
            ?>
                                    <option value="<?php 
            echo esc_attr( $item->getSlug() );
            ?>"
										<?php 
            selected( in_array( $item->getSlug(), $offer->getDiscountOffer()->getSurveyOptions() ) );
            ?>
                                    >
										<?php 
            echo esc_html( $item->getTitle() );
            ?>
                                    </option>
								<?php 
        }
        ?>
                            </select>

                            <p class="description" style="color: red; display: none;"
                               id="cancellation-offer-discount-offer-tab__survey-options-notice">
								<?php 
        esc_html_e( 'Please note, if you\'ve changed any survey option (1st step), you must re-save this form to pull updated survey options here.', 'cancellation-surveys-offers-for-woo-subscriptions' );
        ?>
                            </p>

                            <p class="description"
                               id="cancellation-offer-discount-offer-tab__survey-options-notice">
								<?php 
        esc_html_e( 'Choose which survey answers will enable a discount offer. Leave empty to offer the discount for any cancellation reasons the user chooses.', 'cancellation-surveys-offers-for-woo-subscriptions' );
        ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div id="cancellation-offer-discount-offer-tab__coupon-code">
                    <div class="cancellation-offers-components-row">
                        <div class="cancellation-offers-components-row__label">
                            <label for="discount_offer_coupon_code">Coupon Code</label>
                        </div>
                        <div class="cancellation-offers-components-row__value">
                            <select name="discount_offer_coupon_id" id="discount_offer_coupon_id"
                                    class="cos-select-woo">
								<?php 
        foreach ( $this->getAllAvailableCoupons() as $couponId ) {
            ?>
									
									<?php 
            $coupon = new \WC_Coupon($couponId);
            ?>

                                    <option <?php 
            selected( $couponId, $offer->getDiscountOffer()->getCouponId() );
            ?>
                                            value="<?php 
            echo esc_attr( $couponId );
            ?>"><?php 
            echo esc_html( $coupon->get_code() );
            ?></option>
								<?php 
        }
        ?>
                            </select>

                            <p class="description" style="margin-top: 5px">
								<?php 
        esc_html_e( 'Visit the Coupons page to create a new coupon if you haven\'t already done so. Make sure to select the \'recurring\' type of coupon.', 'cancellation-surveys-offers-for-woo-subscriptions' );
        ?>
                                <a href="<?php 
        echo esc_attr( admin_url( 'edit.php?post_type=shop_coupon' ) );
        ?>"
                                   target="_blank">
									<?php 
        esc_html_e( 'Create a new coupon', 'cancellation-surveys-offers-for-woo-subscriptions' );
        ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="cancellation-offers-components__separator" data-title="
				<?php 
        esc_html_e( 'Offer Settings', 'cancellation-surveys-offers-for-woo-subscriptions' );
        ?>
					">
				<?php 
        $this->form->renderHint( __( 'These settings apply only for current discount offer to help target the offers to more committed subscribers and prevent any fraudulent activity.', 'cancellation-surveys-offers-for-woo-subscriptions' ) );
        FormTab::renderInputRow( array(
            'id'          => 'discount_offer_minimum_renewals_count',
            'type'        => 'number',
            'value'       => $offer->getDiscountOffer()->getMinimumRenewalsCount(),
            'label'       => __( 'Minimum renewals number', 'cancellation-surveys-offers-for-woo-subscriptions' ),
            'placeholder' => __( 'Leave empty to don\'t make any renewals number restrictions', 'cancellation-surveys-offers-for-woo-subscriptions' ),
            'description' => __( 'Don\'t offer the discount if the user\'s number of subscription renewals is below the minimum required threshold.', 'cancellation-surveys-offers-for-woo-subscriptions' ),
        ) );
        FormTab::renderInputRow( array(
            'id'          => 'discount_offer_minimum_subscription_days_active',
            'type'        => 'number',
            'value'       => $offer->getDiscountOffer()->getMinimumSubscriptionDaysActive(),
            'label'       => __( 'Minimum subscription days active', 'cancellation-surveys-offers-for-woo-subscriptions' ),
            'placeholder' => __( 'Leave empty to don\'t make any restrictions by date.', 'cancellation-surveys-offers-for-woo-subscriptions' ),
            'description' => __( 'Don\'t offer discounts to users whose subscriptions were created less than the specified number of days.', 'cancellation-surveys-offers-for-woo-subscriptions' ),
        ) );
        FormTab::renderInputRow( array(
            'id'          => 'discount_offer_allow_offer_multiple_times',
            'type'        => 'checkbox',
            'checked'     => $offer->getDiscountOffer()->isAllowOfferMultipleTimes(),
            'label'       => __( 'Allow offer be applied multiple times', 'cancellation-surveys-offers-for-woo-subscriptions' ),
            'description' => __( 'The discount offer will be valid again after the initial discount was expired.', 'cancellation-surveys-offers-for-woo-subscriptions' ),
        ) );
        ?>

                <hr class="cancellation-offers-components__separator"
                    data-title="<?php 
        esc_html_e( 'Labels', 'cancellation-surveys-offers-for-woo-subscriptions' );
        ?>">
				
				<?php 
        FormTab::renderInputRow( array(
            'id'    => 'discount_offer_apply_discount_button_label',
            'type'  => 'text',
            'value' => $offer->getDiscountOffer()->getApplyDiscountButtonLabel(),
            'label' => __( 'Apply discount button label', 'cancellation-surveys-offers-for-woo-subscriptions' ),
        ) );
        ?>
				<?php 
        FormTab::renderInputRow( array(
            'id'    => 'discount_offer_cancel_subscription_button_label',
            'type'  => 'text',
            'value' => $offer->getDiscountOffer()->getCancelSubscriptionButtonLabel(),
            'label' => __( 'Cancel subscription button label', 'cancellation-surveys-offers-for-woo-subscriptions' ),
        ) );
        ?>
            </div>
        </div>
		<?php 
    }

    public function getAllAvailableCoupons() : array {
        $couponIds = get_posts( array(
            'posts_per_page' => -1,
            'orderby'        => 'name',
            'order'          => 'asc',
            'post_type'      => 'shop_coupon',
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ) );
        $couponIds = array_map( 'intval', $couponIds );
        return array_filter( $couponIds );
    }

}
