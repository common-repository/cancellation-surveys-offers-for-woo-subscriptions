<?php
	
	use MeowCrew\CancellationOffers\Core\ServiceContainer;
	use MeowCrew\CancellationOffers\Offer\CPT\CancellationOffersCPT;
	use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\SurveyAnswersPage;
	use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\SurveyAnswersWPListTable;
	
	defined( 'ABSPATH' ) || die;
	
	$container   = ServiceContainer::getInstance();
	$fileManager = $container->getFileManager();
	
	/**
	 * Available variables
	 *
	 * @var SurveyAnswersWPListTable $feedbacks_table
	 */
?>
<div class="wrap">
	<h1>Survey Answers</h1>
	<form id="reviews-filter" method="get" action="<?php admin_url( 'edit.php' ); ?>">
		<?php $currentPage = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : SurveyAnswersPage::PAGE_SLUG; ?>

		<input type="hidden" name="page" value="<?php echo esc_attr( $currentPage ); ?>"/>
		<input type="hidden" name="post_type" value="<?php echo esc_attr( CancellationOffersCPT::SLUG ); ?>"/>
		<?php $feedbacks_table->views(); ?>
		<?php $feedbacks_table->display(); ?>
	</form>
</div>
