<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns;

use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Actions\SurveyAnswersListAction;

class ActionsColumn extends SurveyAnswerListColumn {
	
	private $actions;
	
	/**
	 * ActionsColumn constructor
	 *
	 * @param  SurveyAnswersListAction[]  $actions
	 */
	public function __construct( array $actions ) {
		
		parent::__construct();
		
		$this->actions = $actions;
	}
	
	public function getName(): string {
		return __( 'Actions', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function renderContent( SurveyAnswer $surveyAnswer ) {
		
		$confirmMessage = __( 'Are you sure?', 'cancellation-surveys-offers-for-woo-subscriptions' );
		
		if ( array_key_exists( 'delete', $this->actions ) ) {
			?>
			<a onclick="return confirm('<?php echo esc_attr( $confirmMessage ); ?>')"
			   href="<?php echo esc_attr( $this->actions['delete']->getURL( $surveyAnswer->getId() ) ); ?>"
			   class="button uef-button-red">
			   <?php
			   esc_html_e( 'Delete',
					'cancellation-surveys-offers-for-woo-subscriptions' );
				?>
			</a>
			<?php
		}
	}
	
	public function getSlug(): string {
		return 'csows_actions';
	}
}
