<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Form\Tabs;

use MeowCrew\CancellationOffers\Offer\CPT\Form\FormTab;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;

class GeneralSettingsTab extends FormTab {
	
	public function getId(): string {
		return 'general-settings';
	}
	
	public function getTitle(): string {
		return __( 'Settings', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function getDescription(): string {
		return __( 'Set eligibility criteria for offers.', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function render( Offer $offer ) {
		$this->form->renderHint( __( 'These settings apply only for current survey\offer to help target the offers to more committed subscribers and prevent any fraudulent activity.',
			'cancellation-surveys-offers-for-woo-subscriptions' ) );
		
		FormTab::renderInputRow( array(
			'id'          => 'settings_minimum_renewals_count',
			'type'        => 'number',
			'value'       => $offer->getSettings()->getMinimumRenewalsCount(),
			'label'       => __( 'Minimum renewals number', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			'placeholder' => __( 'Leave empty to don\'t make any renewals number restrictions', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			'description' => __( 'Don\'t offer the discount if the user\'s number of subscription renewals is below the minimum required threshold.',
				'cancellation-surveys-offers-for-woo-subscriptions' ),
		) );
		
		FormTab::renderInputRow( array(
			'id'          => 'settings_minimum_subscription_days_active',
			'type'        => 'number',
			'value'       => $offer->getSettings()->getMinimumSubscriptionDaysActive(),
			'label'       => __( 'Minimum subscription days active', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			'placeholder' => __( 'Leave empty to don\'t make any restrictions by date.', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			'description' => __( 'Don\'t offer discounts to users whose subscriptions were created less than the specified number of days.',
				'cancellation-surveys-offers-for-woo-subscriptions' ),
		) );
	}
}
