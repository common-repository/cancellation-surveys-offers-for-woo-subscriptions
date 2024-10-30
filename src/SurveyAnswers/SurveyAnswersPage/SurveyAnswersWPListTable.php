<?php namespace MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage;

use Exception;
use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswer;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Actions\SurveyAnswersListAction;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns\ActionsColumn;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns\AnswerColumn;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns\CbColumn;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns\CouponCodeColumn;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns\DateCreatedColumn;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns\IsDiscountOfferAccepted;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns\OfferColumn;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns\SubscriptionColumn;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersPage\Columns\UserColumn;
use MeowCrew\CancellationOffers\SurveyAnswers\SurveyAnswersRepository;
use WP_List_Table;
use wpdb;

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SurveyAnswersWPListTable extends WP_List_Table {
	
	use ServiceContainerTrait;
	
	public $answersCount;
	
	/**
	 * List actions
	 *
	 * @var SurveyAnswersListAction[]
	 */
	private $lineActions;
	
	private $tableColumns;
	
	private $perPage;
	
	/**
	 * Initialize the log table list.
	 */
	public function __construct( $lineActions ) {
		$this->lineActions = $lineActions;
		$this->initTableColumns();
		
		parent::__construct( array(
			'singular' => __( 'Survey Answer', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			'plural'   => __( 'Survey Answer', 'cancellation-surveys-offers-for-woo-subscriptions' ),
			'ajax'     => false,
			'screen'   => null,
		) );
		
		$this->perPage = 10;
	}
	
	protected function initTableColumns() {
		/**
		 * Columns of survey answers list table
		 *
		 * @since 1.0.0
		 */
		$columns = apply_filters( 'cancellation_offers/survey_answers/columns', array(
			new CbColumn(),
			new AnswerColumn(),
			new UserColumn(),
			new SubscriptionColumn(),
			new OfferColumn(),
			
			new IsDiscountOfferAccepted(),
			new CouponCodeColumn(),
			new DateCreatedColumn(),
			new ActionsColumn( $this->lineActions ),
		) );
		
		foreach ( $columns as $column ) {
			$this->tableColumns[ $column->getSlug() ] = $column;
		}
	}
	
	
	public function no_items() {
		esc_attr_e( 'No survey answers yet.', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function single_row( $item ) {
		
		$class = ! $item->isSeen() ? 'cos-new-survey-answer-row' : '';
		
		echo '<tr class="' . esc_attr( $class ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
	
	/**
	 * Handles output for the default column.
	 *
	 * @param  SurveyAnswer  $item
	 * @param  string  $column_name  Identifier for the custom column.
	 */
	public function column_default( $item, $column_name ) {
		/**
		 * Fires for each custom column in the Feedbacks list Table in the administrative screen.
		 *
		 * @param  string  $column_name  Identifier for the custom column.
		 * @param  SurveyAnswer  $survey_answer
		 *
		 * @since 1.0.0
		 *
		 */
		do_action( 'cancellation_offers/admin/survey_answers_list/render_column', $column_name, $item );
	}
	
	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns(): array {
		
		$columns['cb'] = '<input type="checkbox" />';
		
		foreach ( $this->tableColumns as $column ) {
			$columns[ $column->getSlug() ] = $column->getName();
		}
		
		return $columns;
	}
	
	/**
	 * Column cb.
	 *
	 * @param  SurveyAnswer  $item
	 *
	 * @return string
	 */
	public function column_cb( $item ): string {
		return sprintf( '<input type="checkbox" name="survey_answers[]" value="%1$s" />', esc_attr( $item->getId() ) );
	}
	
	/**
	 * Get bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions(): array {
		return array(
			'delete' => __( 'Delete', 'cancellation-surveys-offers-for-woo-subscriptions' ),
		);
	}
	
	/**
	 * Get a list of sortable columns.
	 *
	 * @return array
	 */
	protected function get_sortable_columns(): array {
		return array();
	}
	
	protected function calculateFeedbacksCounts() {
		$this->answersCount = SurveyAnswersRepository::getAllSurveyAnswersCount();
	}
	
	public function process_bulk_action() {
		
		$action = $this->current_action();
		
		if ( $action ) {
			
			switch ( $action ) {
				case 'delete':
					$surveyAnswers = ! empty( $_GET['survey_answers'] ) ? array_map( 'intval',
						(array) $_GET['survey_answers'] ) : array();
					if ( ! empty( $surveyAnswers ) ) {
						SurveyAnswersRepository::bulkDelete( $surveyAnswers );
						
						$this->getContainer()->getAdminNotifier()->flash( __( 'Survey Answers have been deleted successfully',
							'cancellation-surveys-offers-for-woo-subscriptions' ) );
					}
					break;
				
				default:
					break;
			}
			
			return wp_safe_redirect( wp_get_referer() );
		}
		
		return false;
	}
	
	/**
	 * Prepare table list items.
	 *
	 * @global wpdb $wpdb
	 */
	public function prepare_items() {
		
		$this->calculateFeedbacksCounts();
		$this->process_bulk_action();
		
		$this->items = array();
		
		$totalItems = $this->answersCount;
		
		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $this->perPage,
			'total_pages' => ceil( $totalItems / $this->perPage ),
		) );
		
		$this->prepare_column_headers();
		
		$offset = $this->getOffset();
		
		try {
			$this->items = SurveyAnswersRepository::get( $offset, $this->perPage );
		} catch ( Exception $e ) {
			$this->items = array();
		}
		
		$ids = array_map( function ( $item ) {
			return $item->getId();
		}, $this->items );
		
		add_action( 'shutdown', function () use ( $ids ) {
			SurveyAnswersRepository::markAllAsSeen( $ids );
		} );
	}
	
	/**
	 * Get prepared OFFSET clause for items query
	 *
	 * @return int
	 */
	protected function getOffset(): int {
		
		$current_page = $this->get_pagenum();
		if ( 1 < $current_page ) {
			$offset = $this->perPage * ( $current_page - 1 );
		} else {
			$offset = 0;
		}
		
		return (int) $offset;
	}
	
	/**
	 * Get prepared ORDER BY clause for items query
	 *
	 * @return string Prepared ORDER BY clause for items query.
	 */
	protected function get_items_query_order(): string {
		$valid_orders = array( 'level', 'source', 'timestamp' );
		if ( ! empty( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], $valid_orders ) ) {
			$by = wc_clean( $_REQUEST['orderby'] );
		} else {
			$by = 'timestamp';
		}
		$by = esc_sql( $by );
		
		if ( ! empty( $_REQUEST['order'] ) && 'asc' === strtolower( sanitize_text_field( $_REQUEST['order'] ) ) ) {
			$order = 'ASC';
		} else {
			$order = 'DESC';
		}
		
		return "ORDER BY {$by} {$order}, log_id {$order}";
	}
	
	/**
	 * Set _column_headers property for table list
	 */
	protected function prepare_column_headers() {
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		);
	}
}
