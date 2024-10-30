<?php namespace MeowCrew\CancellationOffers\Utils;

use WC_Customer;

class Formatter {
	
	public static function formatCustomerString( WC_Customer $customer, $link = false, $includeEmail = true ): string {
		$firstName = $customer->get_billing_first_name() ? $customer->get_billing_first_name() : $customer->get_shipping_first_name();
		$lastName  = $customer->get_billing_last_name() ? $customer->get_billing_last_name() : $customer->get_shipping_last_name();
		$email     = $customer->get_billing_email() ? $customer->get_billing_email() : $customer->get_email();
		
		if ( ! $email ) {
			return 'Undefined';
		}
		if ( $includeEmail ) {
			$name = sprintf( '%s %s (%s)', $firstName, $lastName, $email );
		} else {
			$name = sprintf( '%s %s', $firstName, $lastName );
		}
		
		if ( $link ) {
			return sprintf( '<a class="cos-customer-link" href="%s">%s</a>', get_edit_user_link( $customer->get_id() ),
				$name );
		}
		
		return $name;
	}
	
	public static function formatRoleString( $roleSlug ) {
		$roles = wp_roles()->roles;
		
		if ( array_key_exists( $roleSlug, $roles ) ) {
			return $roles[ $roleSlug ]['name'];
		}
		
		return 'Undefined role';
	}
}
