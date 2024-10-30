<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage;

use Automattic\WooCommerce\Admin\PageController;
use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Offer\CPT\CancellationOffersCPT;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Actions\DeleteSurveyAnswerAction;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersRepository;

class SurveyAnswersPage {
	
	use ServiceContainerTrait;
	
	/**
	 * Row actions
	 *
	 * @var array
	 */
	private $rowActions;
	
	const PAGE_SLUG = 'cos-survey-answers';
	
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'registerPage' ) );
		add_filter( 'woocommerce_navigation_screen_ids', array( $this, 'addPageToWooCommerceScreen' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'addPageToWooCommerceScreen' ) );
		
		add_action( 'init', function () {
			if ( class_exists( '\Automattic\WooCommerce\Admin\PageController' ) ) {
				PageController::get_instance()->connect_page( array(
					'id'        => CancellationOffersCPT::SLUG . '_page_' . self::PAGE_SLUG,
					'title'     => array( 'Survey Answers' ),
					'screen_id' => CancellationOffersCPT::SLUG . '_page_' . self::PAGE_SLUG,
				) );
			}
		} );
		
		$this->rowActions = array( 'delete' => new DeleteSurveyAnswerAction(), );
	}
	
	public function addPageToWooCommerceScreen( $ids ) {
		
		$ids[] = CancellationOffersCPT::SLUG . '_page_' . self::PAGE_SLUG;
		
		return $ids;
	}
	
	public function registerPage() {
		
		$count = SurveyAnswersRepository::getUnseenSurveyAnswersCount();
		
		$unreadCount = '';
		
		if ( $count > 0 ) {
			$unreadCount = ' <span class="update-plugins count-' . $count . '"><span class="plugin-count">' . $count . '</span></span>';
		}
		
		add_submenu_page( 'edit.php?post_type=' . CancellationOffersCPT::SLUG,
			__( 'Survey Answers', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			__( 'Survey Answers', 'cancellation-surveys-offers-for-woo-subscriptions' ) . $unreadCount,
			'manage_options', self::PAGE_SLUG, array(
				$this,
				'renderPage',
			) );
		
	}
	
	public function renderPage() {
		
		$feedbacksTable = new SurveyAnswersWPListTable( $this->rowActions );
		$feedbacksTable->prepare_items();
		
		$this->getContainer()->getFileManager()->includeTemplate( 'admin/survey-answer-page.php', array(
			'feedbacks_table' => $feedbacksTable,
		) );
	}
}
