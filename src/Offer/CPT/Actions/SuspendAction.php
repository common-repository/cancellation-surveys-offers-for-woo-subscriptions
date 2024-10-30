<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Actions;

use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Offer\CPT\CancellationOffersCPT;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;
use MeowCrew\CancellationOffers\Core\AdminNotifier;
use WP_Post;

class SuspendAction {
	
	const ACTION = 'csows_suspend_offer';
	
	use ServiceContainerTrait;
	
	public function __construct() {
		
		add_action( 'admin_post_' . self::ACTION, array( $this, 'handle' ) );
		
		add_filter( 'post_row_actions', function ( $actions, WP_Post $post ) {
			
			if ( CancellationOffersCPT::SLUG !== $post->post_type ) {
				return $actions;
			}
			
			$rule = Offer::build( $post->ID );
			
			if ( ! $rule->getSettings()->isSuspended() ) {
				$actions['suspend'] = sprintf( '<a href="%s">%s</a>', $this->getRunLink( $post->ID ),
					$this->getName() );
			}
			
			return $actions;
		}, 10, 2 );
	}
	
	public function getName(): string {
		return __( 'Suspend', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function handle(): bool {
		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : false;
		
		if ( wp_verify_nonce( $nonce, self::ACTION ) ) {
			$ruleId = isset( $_GET['rule_id'] ) ? intval( $_GET['rule_id'] ) : false;
			
			if ( $ruleId ) {
				$offer = Offer::build( $ruleId );
				
				if ( false !== get_post_status( $ruleId ) ) {
					$offer->getSettings()->setIsSuspended( true );
					
					$offer->getSettings()->save( $offer->getId() );
					
					$this->getContainer()->getAdminNotifier()->flash( __( 'The offer suspended successfully.',
						'cancellation-surveys-offers-for-woo-subscriptions' ), AdminNotifier::SUCCESS, true );
				}
				
			}
			
		} else {
			wp_die( 'You\'re not allowed to run this action' );
		}
		
		return wp_safe_redirect( wp_get_referer() );
	}
	
	public function getRunLink( $id ): string {
		return add_query_arg( array(
			'rule_id' => $id,
			'action'  => self::ACTION,
		), wp_nonce_url( admin_url( 'admin-post.php' ), self::ACTION ) );
	}
}
