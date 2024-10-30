<?php namespace MeowCrew\CancellationOffers\SurveyAnswers;

use DateTime;
use Exception;

class SurveyAnswer {
	
	private $subscriptionId;
	private $userId;
	private $offerId;
	
	private $isSurveyEnabled;
	private $surveySelectedAnswer;
	private $surveyTextAnswer;
	
	private $isDiscountOfferEnabled;
	private $isDiscountOfferAccepted;
	private $couponId;
	
	private $dateCreated;
	private $isSeen;
	private $id;
	
	/**
	 * Message constructor.
	 *
	 * @param  int  $offerId
	 * @param  int  $subscriptionId
	 * @param  int  $userId
	 *
	 * @param  bool  $isSurveyEnabled
	 * @param  ?string  $surveySelectedAnswer
	 * @param  ?string  $surveyTextAnswer
	 *
	 * @param  bool  $isDiscountOfferEnabled
	 * @param  ?bool  $isDiscountOfferAccepted
	 * @param  ?int  $couponId
	 *
	 * @param  bool  $isSeen
	 *
	 * @param  ?DateTime  $dateCreated
	 *
	 * @param  ?int  $id
	 */
	public function __construct(
		int $offerId,
		int $subscriptionId,
		int $userId,
		
		bool $isSurveyEnabled = false,
		?string $surveySelectedAnswer = null,
		?string $surveyTextAnswer = null,
		
		bool $isDiscountOfferEnabled = false,
		?bool $isDiscountOfferAccepted = null,
		?int $couponId = null,
		
		bool $isSeen = false,
		?DateTime $dateCreated = null,
		int $id = null
	) {
		$this->offerId        = $offerId;
		$this->subscriptionId = $subscriptionId;
		$this->userId         = $userId;
		
		$this->isSurveyEnabled      = $isSurveyEnabled;
		$this->surveySelectedAnswer = $surveySelectedAnswer;
		$this->surveyTextAnswer     = $surveyTextAnswer;
		
		$this->isDiscountOfferEnabled  = $isDiscountOfferEnabled;
		$this->isDiscountOfferAccepted = $isDiscountOfferAccepted;
		$this->couponId                = $couponId;
		
		$this->isSeen      = $isSeen;
		$this->dateCreated = $dateCreated ? $dateCreated : new DateTime( 'now' );
		$this->id          = $id;
	}
	
	public function getSubscriptionId(): int {
		return $this->subscriptionId;
	}
	
	public function setSubscriptionId( int $subscriptionId ): void {
		$this->subscriptionId = $subscriptionId;
	}
	
	public function getOfferId(): int {
		return $this->offerId;
	}
	
	public function setOfferId( int $offerId ): void {
		$this->offerId = $offerId;
	}
	
	public function isSurveyEnabled(): bool {
		return $this->isSurveyEnabled;
	}
	
	public function setIsSurveyEnabled( bool $isSurveyEnabled ): void {
		$this->isSurveyEnabled = $isSurveyEnabled;
	}
	
	public function getSurveySelectedAnswer(): ?string {
		return $this->surveySelectedAnswer;
	}
	
	public function setSurveySelectedAnswer( ?string $surveySelectedAnswer ): void {
		$this->surveySelectedAnswer = $surveySelectedAnswer;
	}
	
	public function getSurveyTextAnswer(): ?string {
		return $this->surveyTextAnswer;
	}
	
	public function setSurveyTextAnswer( ?string $surveyTextAnswer ): void {
		$this->surveyTextAnswer = $surveyTextAnswer;
	}
	
	public function isDiscountOfferEnabled(): bool {
		return $this->isDiscountOfferEnabled;
	}
	
	public function setIsDiscountOfferEnabled( bool $isDiscountOfferEnabled ): void {
		$this->isDiscountOfferEnabled = $isDiscountOfferEnabled;
	}
	
	public function isDiscountOfferAccepted(): ?bool {
		return $this->isDiscountOfferAccepted;
	}
	
	public function setIsDiscountOfferAccepted( ?bool $isDiscountOfferAccepted ): void {
		$this->isDiscountOfferAccepted = $isDiscountOfferAccepted;
	}
	
	public function getCouponId(): ?int {
		return $this->couponId;
	}
	
	public function setCouponId( ?int $couponId ): void {
		$this->couponId = $couponId;
	}
	
	/**
	 * Get Date created
	 *
	 * @param  bool  $localTimeZone
	 *
	 * @return DateTime
	 */
	public function getDateCreated( bool $localTimeZone = true ): DateTime {
		
		if ( $localTimeZone ) {
			$localDateSend = clone $this->dateCreated;
			$localDateSend->setTimezone( wp_timezone() );
			
			return $localDateSend;
		}
		
		return $this->dateCreated;
	}
	
	/**
	 * Set date sent
	 *
	 * @param  DateTime  $dateCreated
	 */
	public function setDateCreated( DateTime $dateCreated ) {
		$this->dateCreated = $dateCreated;
	}
	
	/**
	 * Is notified
	 *
	 * @return bool
	 */
	public function isSeen(): bool {
		return $this->isSeen;
	}
	
	/**
	 * Set is notified
	 *
	 * @param  bool  $isSeen
	 */
	public function setIsSeen( bool $isSeen ) {
		$this->isSeen = $isSeen;
	}
	
	/**
	 * Get user id
	 *
	 * @return int
	 */
	public function getUserId(): int {
		return $this->userId;
	}
	
	public function setUserId( int $userId ) {
		$this->userId = $userId;
	}
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function setId( int $id ) {
		$this->id = $id;
	}
	
	/**
	 * Save entity
	 *
	 * @throws Exception
	 */
	public function save() {
		if ( ! empty( $this->getId() ) ) {
			$this->update();
			do_action( 'cancellation_offers/survey_answers/updated', $this );
		} else {
			$this->create();
			do_action( 'cancellation_offers/survey_answers/created', $this );
		}
	}
	
	public function delete() {
		global $wpdb;
		
		$wpdb->delete( SurveyAnswersTable::getTableName(), array( 'id' => $this->getId() ) );
	}
	
	public function unread() {
		global $wpdb;
		
		$wpdb->update( SurveyAnswersTable::getTableName(), array( 'date_read' => null ),
			array( 'id' => $this->getId() ) );
	}
	
	public function getAsArray( bool $includeID = false, bool $localTimeZone = true ): array {
		$data = array(
			'subscription_id' => $this->getSubscriptionId(),
			'user_id'         => $this->getUserId(),
			'offer_id'        => $this->getOfferId(),
			
			'is_survey_enabled'      => $this->isSurveyEnabled(),
			'survey_selected_answer' => $this->getSurveySelectedAnswer(),
			'survey_text_answer'     => $this->getSurveyTextAnswer(),
			
			'is_discount_offer_enabled'  => $this->isDiscountOfferEnabled(),
			'is_discount_offer_accepted' => $this->isDiscountOfferAccepted(),
			'coupon_id'                  => $this->getCouponId(),
			
			'date_created' => $this->getDateCreated( $localTimeZone )->format( 'Y-m-d H:i:s' ),
			'is_seen'      => $this->isSeen(),
		);
		
		if ( $includeID ) {
			$data['id'] = $this->getId();
		}
		
		return $data;
	}
	
	protected function update() {
		global $wpdb;
		
		$wpdb->update( SurveyAnswersTable::getTableName(), $this->getAsArray( false, false ),
			array( 'id' => $this->getId() ) );
	}
	
	/**
	 * Create message
	 *
	 * @throws Exception
	 */
	protected function create() {
		global $wpdb;
		
		$result = $wpdb->insert( SurveyAnswersTable::getTableName(), $this->getAsArray( false, false ) );
		
		if ( ! $result ) {
			throw new Exception( esc_html( $wpdb->last_error ) );
		}
		
		$this->setId( $wpdb->insert_id );
	}
	
	public static function build( $id ): ?self {
		global $wpdb;
		
		$database = $wpdb;
		
		$feedback = $database->get_row( $database->prepare( 'SELECT * FROM %i WHERE id = %d',
			SurveyAnswersTable::getTableName(), $id ), ARRAY_A );
		
		if ( ! $feedback ) {
			throw new Exception( 'Survey answer not found' );
		}
		
		try {
			return self::buildFromArray( $feedback );
		} catch ( Exception $e ) {
			return null;
		}
	}
	
	/**
	 * Build instance from array
	 *
	 * @throws Exception
	 */
	public static function buildFromArray( $data ): self {
		
		$id             = isset( $data['id'] ) ? intval( $data['id'] ) : null;
		$offerId        = isset( $data['offer_id'] ) ? intval( $data['offer_id'] ) : null;
		$subscriptionId = isset( $data['subscription_id'] ) ? intval( $data['subscription_id'] ) : null;
		$userId         = isset( $data['user_id'] ) ? intval( $data['user_id'] ) : null;
		
		$isSurveyEnabled      = isset( $data['is_survey_enabled'] ) ? boolval( $data['is_survey_enabled'] ) : null;
		$surveySelectedAnswer = isset( $data['survey_selected_answer'] ) ? strval( $data['survey_selected_answer'] ) : null;
		$surveyTextAnswer     = isset( $data['survey_text_answer'] ) ? strval( $data['survey_text_answer'] ) : null;
		
		$isDiscountOfferEnabled  = isset( $data['is_discount_offer_enabled'] ) ? boolval( $data['is_discount_offer_enabled'] ) : null;
		$isDiscountOfferAccepted = isset( $data['is_discount_offer_accepted'] ) ? boolval( $data['is_discount_offer_accepted'] ) : null;
		$couponId                = isset( $data['coupon_id'] ) ? intval( $data['coupon_id'] ) : null;
		
		$isSeen      = isset( $data['is_seen'] ) && boolval( $data['is_seen'] );
		$dateCreated = isset( $data['date_created'] ) ? new DateTime( $data['date_created'] ) : null;
		
		
		if ( ! $offerId || ! $subscriptionId || ! $userId ) {
			throw new Exception( 'Wrong survey answer data' );
		}
		
		return new self( $offerId, $subscriptionId, $userId, $isSurveyEnabled, $surveySelectedAnswer, $surveyTextAnswer,
			$isDiscountOfferEnabled, $isDiscountOfferAccepted, $couponId, $isSeen, $dateCreated, $id );
	}
}
