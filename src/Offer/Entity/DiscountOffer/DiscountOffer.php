<?php

namespace MeowCrew\CancellationOffers\Offer\Entity\DiscountOffer;

use MeowCrew\CancellationOffers\Offer\OfferDataProvider;
use MeowCrew\CancellationOffers\Utils\Sanitizer;
use WC_Coupon;
class DiscountOffer {
    protected $isEnabled = true;

    protected $title = null;

    protected $description = '';

    protected $surveyOptions = array();

    protected $applyDiscountButtonLabel = '';

    protected $cancelSubscriptionButtonLabel = '';

    protected $couponId = null;

    protected $minimumRenewalsCount = null;

    protected $minimumSubscriptionDaysActive = null;

    protected $allowOfferMultipleTimes = false;

    public function isEnabled() : bool {
        return $this->isEnabled;
    }

    public function setIsEnabled( bool $isEnabled ) : void {
        $this->isEnabled = $isEnabled;
    }

    public function isApplicableForSubscription( \WC_Subscription $subscription ) : bool {
        return false;
    }

    public function getTitle() : ?string {
        return $this->title;
    }

    public function getCoupon() : WC_Coupon {
        return new WC_Coupon($this->getCouponId());
    }

    public function setTitle( ?string $title ) : void {
        $this->title = $title;
    }

    public function getDescription() : ?string {
        return $this->description;
    }

    public function setDescription( string $description ) : void {
        $this->description = $description;
    }

    public function getSurveyOptions() : array {
        return $this->surveyOptions;
    }

    public function setSurveyOptions( array $surveyOptions ) : void {
        $this->surveyOptions = $surveyOptions;
    }

    public function getApplyDiscountButtonLabel() : ?string {
        return $this->applyDiscountButtonLabel;
    }

    public function setApplyDiscountButtonLabel( string $applyDiscountButtonLabel ) : void {
        $this->applyDiscountButtonLabel = $applyDiscountButtonLabel;
    }

    public function getCancelSubscriptionButtonLabel() : ?string {
        return $this->cancelSubscriptionButtonLabel;
    }

    public function setCancelSubscriptionButtonLabel( string $cancelSubscriptionButtonLabel ) : void {
        $this->cancelSubscriptionButtonLabel = $cancelSubscriptionButtonLabel;
    }

    public function getCouponId() : ?int {
        return $this->couponId;
    }

    public function setCouponId( ?int $couponId ) : void {
        $this->couponId = $couponId;
    }

    public function getMinimumRenewalsCount() : ?int {
        return $this->minimumRenewalsCount;
    }

    public function setMinimumRenewalsCount( ?int $minimumRenewalsCount ) : void {
        $this->minimumRenewalsCount = $minimumRenewalsCount;
    }

    public function getMinimumSubscriptionDaysActive() : ?int {
        return $this->minimumSubscriptionDaysActive;
    }

    public function setMinimumSubscriptionDaysActive( ?int $minimumSubscriptionDaysActive ) : void {
        $this->minimumSubscriptionDaysActive = $minimumSubscriptionDaysActive;
    }

    public function isAllowOfferMultipleTimes() : bool {
        return $this->allowOfferMultipleTimes;
    }

    public function setAllowOfferMultipleTimes( bool $allowOfferMultipleTimes ) : void {
        $this->allowOfferMultipleTimes = $allowOfferMultipleTimes;
    }

    public static function getDataSchema() : array {
        return array(
            'is_enabled'                       => array(
                'default'  => 'yes',
                'sanitize' => 'wc_string_to_bool',
            ),
            'title'                            => array(
                'default'  => __( 'Your special offer', 'cancellation-surveys-offers-for-woo-subscriptions' ),
                'sanitize' => 'sanitize_text_field',
            ),
            'description'                      => array(
                'default'  => __( 'As a valued subscriber, you have the opportunity to receive an exclusive 10% discount on your subscription. Accept this offer now; the discount will automatically apply to your next billing cycle.', 'cancellation-surveys-offers-for-woo-subscriptions' ),
                'sanitize' => 'sanitize_text_field',
            ),
            'survey_options'                   => array(
                'default'  => array(),
                'sanitize' => array(Sanitizer::class, 'sanitizeStringArray'),
            ),
            'coupon_id'                        => array(
                'default'  => null,
                'sanitize' => 'intval',
            ),
            'apply_discount_button_label'      => array(
                'default'  => __( 'Accept offer', 'cancellation-surveys-offers-for-woo-subscriptions' ),
                'sanitize' => 'sanitize_text_field',
            ),
            'cancel_subscription_button_label' => array(
                'default'  => __( 'Cancel subscription', 'cancellation-surveys-offers-for-woo-subscriptions' ),
                'sanitize' => 'sanitize_text_field',
            ),
            'minimum_renewals_count'           => array(
                'default'  => null,
                'sanitize' => function ( $value ) {
                    if ( !$value || '' == $value ) {
                        return null;
                    }
                    return intval( $value );
                },
            ),
            'minimum_subscription_days_active' => array(
                'default'  => null,
                'sanitize' => function ( $value ) {
                    if ( !$value || '' == $value ) {
                        return null;
                    }
                    return intval( $value );
                },
            ),
            'allow_offer_multiple_times'       => array(
                'default'  => 'no',
                'sanitize' => 'wc_string_to_bool',
            ),
        );
    }

    public static function build( $offerId ) : self {
        $offerDataProvider = new OfferDataProvider($offerId, 'discounts');
        $data = array();
        foreach ( self::getDataSchema() as $key => $schemaItem ) {
            $value = $offerDataProvider->getMeta( $key, $schemaItem['default'] );
            if ( !is_null( $value ) && is_callable( $schemaItem['sanitize'] ) ) {
                $value = call_user_func( $schemaItem['sanitize'], $value );
            }
            $data[$key] = $value;
        }
        return self::buildFromArray( $data );
    }

    public static function buildFromPOST( $postData ) : self {
        $data = array();
        foreach ( self::getDataSchema() as $key => $schemaItem ) {
            $value = $postData['discount_offer_' . $key] ?? false;
            if ( is_callable( $schemaItem['sanitize'] ) ) {
                $value = call_user_func( $schemaItem['sanitize'], $value );
            }
            $data[$key] = $value;
        }
        return self::buildFromArray( $data );
    }

    public function save( $offerId ) : void {
        $offerDataProvider = new OfferDataProvider($offerId, 'discounts');
        $offerDataProvider->setMeta( 'is_enabled', wc_bool_to_string( $this->isEnabled() ) );
        $offerDataProvider->setMeta( 'coupon_id', $this->getCouponId() );
        $offerDataProvider->setMeta( 'survey_options', $this->getSurveyOptions() );
        $offerDataProvider->setMeta( 'title', $this->getTitle() );
        $offerDataProvider->setMeta( 'description', $this->getDescription() );
        $offerDataProvider->setMeta( 'apply_discount_button_label', $this->getApplyDiscountButtonLabel() );
        $offerDataProvider->setMeta( 'cancel_subscription_button_label', $this->getCancelSubscriptionButtonLabel() );
        $offerDataProvider->setMeta( 'minimum_subscription_days_active', $this->getMinimumSubscriptionDaysActive() );
        $offerDataProvider->setMeta( 'minimum_renewals_count', $this->getMinimumRenewalsCount() );
        $offerDataProvider->setMeta( 'allow_offer_multiple_times', wc_bool_to_string( $this->isAllowOfferMultipleTimes() ) );
    }

    protected static function buildFromArray( array $data ) : self {
        $discountOffer = new self();
        $discountOffer->setIsEnabled( $data['is_enabled'] ?? true );
        $discountOffer->setCouponId( $data['coupon_id'] ?? true );
        $discountOffer->setSurveyOptions( $data['survey_options'] ?? array() );
        $discountOffer->setTitle( $data['title'] ?? '' );
        $discountOffer->setDescription( $data['description'] ?? '' );
        $discountOffer->setApplyDiscountButtonLabel( $data['apply_discount_button_label'] ?? '' );
        $discountOffer->setCancelSubscriptionButtonLabel( $data['cancel_subscription_button_label'] ?? '' );
        $discountOffer->setMinimumSubscriptionDaysActive( $data['minimum_subscription_days_active'] );
        $discountOffer->setMinimumRenewalsCount( $data['minimum_renewals_count'] );
        $discountOffer->setAllowOfferMultipleTimes( $data['allow_offer_multiple_times'] ?? false );
        return $discountOffer;
    }

}
