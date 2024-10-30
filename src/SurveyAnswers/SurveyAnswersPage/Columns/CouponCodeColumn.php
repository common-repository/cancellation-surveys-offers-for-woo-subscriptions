<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;

class CouponCodeColumn extends SurveyAnswerListColumn {
	
	public function getName(): string {
		return __( 'Offered Coupon', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function renderContent( SurveyAnswer $surveyAnswer ) {
		
		if ( ! $surveyAnswer->isDiscountOfferEnabled() ) {
			
			if ( ! csows_fs()->can_use_premium_code() ) {
				?>
                <i>
                    <a target="_blank" href="<?php echo esc_attr( csows_upgrade_url() ) ?>">
						<?php esc_html_e( 'Upgrade to premium',
							'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
                    </a>
					<?php esc_html_e( 'to enable this feature',
						'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
                </i>
				<?php
			} else {
				esc_html_e( 'Discount was not offered', 'cancellation-surveys-offers-for-woo-subscriptions' );
			}
			
			return;
		}
		
		$coupon = new \WC_Coupon( $surveyAnswer->getCouponId() );
		
		if ( $coupon->get_code() ) {
			?>    <a target="_blank" href="<?php echo esc_attr( get_edit_post_link( $coupon->get_id() ) ); ?>">
                <code style="display: block;
	text-align: center;
	border-radius: 6px;">
					<?php echo esc_html( $coupon->get_code() ); ?>
                </code>
            </a>
			<?php
		} else {
			echo esc_html( '-' );
		}
	}
	
	public function getSlug(): string {
		return 'csows_coupon_code';
	}
}
