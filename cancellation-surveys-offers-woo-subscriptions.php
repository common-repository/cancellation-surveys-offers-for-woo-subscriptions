<?php

/**
 *
 * Plugin Name:       Cancellation Surveys & Offers for Woo Subscriptions
 * Requires Plugins:  woocommerce
 * Plugin URI:        https://meow-crew.com/plugin/cancellation-surveys-offers-for-woocommerce-subscriptions
 * Description:       Offer targeted discounts and collect feedback through surveys when users attempt to cancel their subscriptions.
 * Version:           1.0.0
 * Author:            Meow Crew
 * Author URI:        https://meow-crew.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cancellation-surveys-offers-for-woo-subscriptions
 * Domain Path:       /languages
 */
use MeowCrew\CancellationOffers\CancellationOffersPlugin;
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( version_compare( phpversion(), '7.2.0', '<' ) ) {
    add_action( 'admin_notices', function () {
        ?>
            <div class='notice notice-error'>
                <p>
                    Cancellation Surveys & Offers for WooCommerce Subscriptions requires PHP version to be <b>7.2 or
                        higher</b>. You run PHP
                    version <?php 
        echo esc_attr( phpversion() );
        ?>
                </p>
            </div>
			<?php 
    } );
    return;
}
// Include the Composer autoload file
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
if ( function_exists( 'csows_fs' ) ) {
    csows_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'csows_fs' ) ) {
        // Create a helper function for easy SDK access.
        function csows_fs() {
            global $csows_fs;
            if ( !isset( $csows_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $csows_fs = fs_dynamic_init( array(
                    'id'             => '15589',
                    'slug'           => 'cancellation-surveys-offers-woo-subscriptions',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_910f26603666c248f236911fb978a',
                    'is_premium'     => false,
                    'premium_suffix' => 'Premium',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 7,
                        'is_require_payment' => true,
                    ),
                    'menu'           => array(
                        'first-path' => 'plugins.php',
                        'contact'    => false,
                        'support'    => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $csows_fs;
        }

        // Init Freemius.
        csows_fs();
        // Signal that SDK was initiated.
        do_action( 'csows_fs_loaded' );
        if ( !function_exists( 'csows_upgrade_url' ) ) {
            function csows_upgrade_url() : string {
                return ( csows_fs()->is_activation_mode() ? csows_fs()->get_activation_url() : csows_fs()->get_upgrade_url() );
            }

        }
        if ( !function_exists( 'csows_contact_us_url' ) ) {
            function csows_contact_us_url() : string {
                return admin_url( 'admin.php?page=csows-contact-us' );
            }

        }
        if ( !function_exists( 'csows_account_url' ) ) {
            function csows_account_url() : string {
                return admin_url( 'admin.php?page=csows-account' );
            }

        }
    }
    call_user_func( function () {
        $main = new CancellationOffersPlugin(__FILE__);
        register_activation_hook( __FILE__, [$main, 'activate'] );
        register_uninstall_hook( __FILE__, [CancellationOffersPlugin::class, 'uninstall'] );
    } );
}
define( 'COS_PRODUCTION', true );