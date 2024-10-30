<?php

namespace MeowCrew\CancellationOffers\Offer\Entity;

use Exception;
use MeowCrew\CancellationOffers\Offer\CPT\CancellationOffersCPT;
use MeowCrew\CancellationOffers\Offer\Entity\DiscountOffer\DiscountOffer;
use MeowCrew\CancellationOffers\Offer\Entity\MatchingRule\MatchingRule;
use MeowCrew\CancellationOffers\Offer\Entity\Settings\Settings;
use MeowCrew\CancellationOffers\Offer\Entity\Survey\Survey;
use WC_Subscription;
use WP_User;
class Offer {
    protected $id;

    protected $matchingRule = null;

    protected $survey = null;

    protected $discountOffer = null;

    protected $offerSettings = null;

    public function __construct(
        ?int $id,
        MatchingRule $matchingRule,
        Survey $survey,
        DiscountOffer $discountOffer,
        Settings $offerSettings
    ) {
        $this->id = $id;
        $this->matchingRule = $matchingRule;
        $this->survey = $survey;
        $this->discountOffer = $discountOffer;
        $this->offerSettings = $offerSettings;
    }

    public function getId() : int {
        return $this->id;
    }

    public function getMatchingRule() : ?MatchingRule {
        return $this->matchingRule;
    }

    public function getSurvey() : ?Survey {
        return $this->survey;
    }

    public function getDiscountOffer() : ?DiscountOffer {
        return $this->discountOffer;
    }

    public function getSettings() : ?Settings {
        return $this->offerSettings;
    }

    public static function build( int $offerId ) {
        if ( !get_post_status( $offerId ) || get_post_type( $offerId ) !== CancellationOffersCPT::SLUG ) {
            return false;
        }
        $matchingRule = MatchingRule::build( $offerId );
        $discounts = DiscountOffer::build( $offerId );
        $offerSettings = Settings::build( $offerId );
        $offerSurvey = Survey::build( $offerId );
        return new self(
            $offerId,
            $matchingRule,
            $offerSurvey,
            $discounts,
            $offerSettings
        );
    }

    public static function buildFromPOST( int $offerId, array $postedData ) : self {
        $matchingRule = MatchingRule::buildFromPOST( $postedData );
        $discounts = DiscountOffer::buildFromPOST( $postedData );
        $offerSettings = Settings::buildFromPOST( $postedData );
        $offerSurvey = Survey::buildFromPOST( $postedData );
        return new self(
            $offerId,
            $matchingRule,
            $offerSurvey,
            $discounts,
            $offerSettings
        );
    }

    public function isApplicableForUser( ?WP_User $user, ?WC_Subscription $subscription ) : bool {
        if ( !$user || !$subscription ) {
            return false;
        }
        if ( !$this->isValid() ) {
            return false;
        }
        if ( !$this->getSettings()->matchRequirements( $user, $subscription ) ) {
            return false;
        }
        if ( !$this->getMatchingRule()->matchRequirements( $user, $subscription ) ) {
            return false;
        }
        if ( !$this->getSurvey()->isEnabled() && !$this->getDiscountOffer()->isApplicableForSubscription( $subscription ) ) {
            return false;
        }
        return true;
    }

    public function isValid() : bool {
        try {
            $this->validate();
        } catch ( Exception $e ) {
            return false;
        }
        return true;
    }

    /**
     * Validate offer
     *
     * @throws Exception
     */
    public function validate() : void {
        if ( !csows_fs()->can_use_premium_code() ) {
            if ( !$this->getSurvey()->isEnabled() ) {
                throw new Exception(esc_html__( 'At least one of the offer types (discount or survey) must be enabled', 'cancellation-surveys-offers-for-woo-subscriptions' ));
            }
        } else {
            if ( !$this->getDiscountOffer()->isEnabled() && !$this->getSurvey()->isEnabled() ) {
                throw new Exception(esc_html__( 'At least one of the offer types (discount or survey) must be enabled', 'cancellation-surveys-offers-for-woo-subscriptions' ));
            }
        }
        if ( $this->getDiscountOffer()->isEnabled() && !$this->getDiscountOffer()->getCouponId() ) {
            throw new Exception(esc_html__( 'Coupon code is required for discount offer', 'cancellation-surveys-offers-for-woo-subscriptions' ));
        }
        if ( $this->getSurvey()->isEnabled() && !$this->getSurvey()->getItems() ) {
            throw new Exception(esc_html__( 'Survey items are required for survey offer', 'cancellation-surveys-offers-for-woo-subscriptions' ));
        }
    }

    public function save() {
        $this->matchingRule->save( $this->getId() );
        $this->offerSettings->save( $this->getId() );
        $this->survey->save( $this->getId() );
    }

}
