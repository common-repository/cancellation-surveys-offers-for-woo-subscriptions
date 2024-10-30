<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Actions;

use Exception;
use MeowCrew\CancellationOffers\Core\AdminNotifier;
use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;

abstract class SurveyAnswersListAction {
	
	use ServiceContainerTrait;
	
	abstract public function handle();
	
	abstract public function getActionSlug();
	
	abstract public function getName();
	
	public function __construct() {
		add_action( 'admin_post_' . $this->getActionSlug(), array( $this, 'execute' ) );
	}
	
	public function getURL( $feedbackId = 0 ): string {
		return wp_nonce_url( add_query_arg( array(
			'action'      => $this->getActionSlug(),
			'feedback_id' => $feedbackId,
		), admin_url( 'admin-post.php' ) ), $this->getActionSlug() );
	}
	
	public function execute() {
		
		try {
			$this->validate();
			
			$this->handle();
			
		} catch ( Exception $exception ) {
			$this->getContainer()->getAdminNotifier()->flash( $exception->getMessage(), AdminNotifier::ERROR );
		}
		
		wp_redirect( wp_get_referer() );
	}
	
	/**
	 * Validate request
	 *
	 * @throws Exception
	 */
	public function validate() {
		$this->validateNonce();
	}
	
	/**
	 * Validate nonce
	 *
	 * @throws Exception
	 */
	public function validateNonce() {
		$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : null;
		
		if ( ! wp_verify_nonce( $nonce, $this->getActionSlug() ) ) {
			throw new Exception( esc_html__( 'Invalid Nonce', 'cancellation-surveys-offers-for-woo-subscriptions' ) );
		}
	}
}
