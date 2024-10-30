<?php namespace MeowCrew\CancellationOffers\Offer\Entity\Survey;

use MeowCrew\CancellationOffers\Offer\OfferDataProvider;
use MeowCrew\CancellationOffers\Utils\Sanitizer;

class Survey {
	
	protected $isEnabled = true;
	protected $title = null;
	protected $description = null;
	protected $settings = array();
	protected $items = array();
	
	public static function getDataSchema(): array {
		
		return array(
			'is_enabled'  => array(
				'default'  => 'yes',
				'sanitize' => 'wc_string_to_bool',
			),
			'title'       => array(
				'default'  => __( 'What is the reason for canceling your subscription?', 'cancellation-surveys-offers-for-woo-subscriptions' ),
				'sanitize' => 'sanitize_text_field',
			),
			'description' => array(
				'default'  => __( 'Please tell us why you\'re canceling your subscription. Your feedback helps us improve.',
					'cancellation-surveys-offers-for-woo-subscriptions' ),
				'sanitize' => 'sanitize_text_field',
			),
			'items'       => array(
				'default'  => array(
					array(
						'title' => __( 'I no longer need the product', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'slug'  => 'no-longer-using',
						'meta'  => array(),
					),
					array(
						'title' => __( 'I found a better alternative', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'slug'  => 'found-a-better-alternative',
						'meta'  => array(),
					),
					array(
						'title' => __( 'The product is too expensive', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'slug'  => 'too-expensive',
						'meta'  => array(),
					),
					array(
						'title' => __( 'I\'m not satisfied with the quality of the product', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'slug'  => 'no-satisfied',
						'meta'  => array(),
					),
					array(
						'title' => __( 'The product did not meet my expectations', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'slug'  => 'did-not-meet-expectations',
						'meta'  => array(),
					),
					array(
						'title' => __( 'Other (please specify)', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'slug'  => 'other',
						'meta'  => array(
							'text_answer_enabled' => true,
						),
					),
				),
				'build'    => function ( $value ) {
					
					if ( ! is_array( $value ) ) {
						return array();
					}
					
					$items = array();
					
					foreach ( $value as $item ) {
						$title = $item['title'] ?? '';
						$slug  = $item['slug'] ?? '';
						$meta  = isset( $item['meta'] ) && is_array( $item['meta'] ) ? $item['meta'] : array();
						
						$items[] = new SurveyItem( $title, $slug, $meta );
					}
					
					return $items;
				},
				'sanitize' => function ( $array ) {
					return array_filter( $array, function ( $item ) {
						if ( ! ( $item instanceof SurveyItem ) ) {
							return false;
						}
						
						if ( ! $item->isValid() ) {
							return false;
						}
						
						return true;
					} );
				},
			),
			'settings'    => array(
				'default'  => array(),
				'sanitize' => array( Sanitizer::class, 'sanitizeArray' ),
			),
		);
	}
	
	public function getTitle(): string {
		return $this->title;
	}
	
	public function setTitle( ?string $title ): void {
		$this->title = $title;
	}
	
	public function getDescription(): ?string {
		return $this->description;
	}
	
	public function isEnabled(): bool {
		return $this->isEnabled;
	}
	
	public function setIsEnabled( bool $isEnabled ): void {
		$this->isEnabled = $isEnabled;
	}
	
	public function setDescription( ?string $description ): void {
		$this->description = $description;
	}
	
	public function getSettings(): array {
		return $this->settings;
	}
	
	public function setSettings( array $settings ): void {
		$this->settings = $settings;
	}
	
	/**
	 * Get survey items
	 *
	 * @return SurveyItem[]
	 */
	public function getItems(): array {
		return $this->items;
	}
	
	public function setItems( array $items ): void {
		$this->items = $items;
	}
	
	public function getItemBySlug( $slug ): ?SurveyItem {
		foreach ( $this->items as $item ) {
			if ( $item->getSlug() === $slug ) {
				return $item;
			}
		}
		
		return null;
	}
	
	public static function buildFromPOST( $postData ): self {
		$isEnabled         = isset( $postData['survey_is_enabled'] );
		$surveyTitle       = isset( $postData['survey_title'] ) ? sanitize_text_field( $postData['survey_title'] ) : '';
		$surveyDescription = isset( $postData['survey_description'] ) ? sanitize_text_field( $postData['survey_description'] ) : '';
		
		$surveyOptionsTitles = isset( $postData['survey_options']['title'] ) ? (array) $postData['survey_options']['title'] : array();
		$surveyOptions       = array();
		
		foreach ( $surveyOptionsTitles as $surveyOptionsTitleKey => $surveyOptionsTitle ) {
			$surveyOptionTitle = sanitize_text_field( $surveyOptionsTitle );
			$surveyOptionSlug  = ! empty( $postData['survey_options']['slug'][ $surveyOptionsTitleKey ] ) ? sanitize_text_field( $postData['survey_options']['slug'][ $surveyOptionsTitleKey ] ) : false;
			$surveyOptionSlug  = $surveyOptionSlug ? $surveyOptionSlug : wp_generate_password( 10 );
			
			$surveyIsOtherOptionEnabled = isset( $postData['survey_options']['text_answer_enabled'][ $surveyOptionsTitleKey ] );
			
			$surveyOptionMeta = array(
				'text_answer_enabled' => $surveyIsOtherOptionEnabled,
			);
			
			$surveyOptions[] = new SurveyItem( $surveyOptionTitle, $surveyOptionSlug, $surveyOptionMeta );
		}
		
		$survey = new self();
		
		$survey->setIsEnabled( $isEnabled );
		$survey->setTitle( $surveyTitle );
		$survey->setDescription( $surveyDescription );
		$survey->setItems( $surveyOptions );
		
		return $survey;
	}
	
	public static function build( $offerId ): self {
		$offerDataProvider = new OfferDataProvider( $offerId, 'survey' );
		
		$survey = new self();
		
		$data = array();
		
		foreach ( self::getDataSchema() as $key => $schemaItem ) {
			$value = $offerDataProvider->getMeta( $key, $schemaItem['default'] );
			
			if ( isset( $schemaItem['build'] ) && is_callable( $schemaItem['build'] ) ) {
				$value = call_user_func( $schemaItem['build'], $value );
			}
			
			if ( is_callable( $schemaItem['sanitize'] ) ) {
				$value = call_user_func( $schemaItem['sanitize'], $value );
			}
			
			$data[ $key ] = $value;
		}
		
		$survey->setIsEnabled( $data['is_enabled'] );
		$survey->setTitle( $data['title'] );
		$survey->setDescription( $data['description'] );
		$survey->setItems( $data['items'] );
		
		return $survey;
	}
	
	public function save( $offerId ) {
		
		$offerDataProvider = new OfferDataProvider( $offerId, 'survey' );
		
		$offerDataProvider->setMeta( 'is_enabled', wc_bool_to_string( $this->isEnabled() ) );
		$offerDataProvider->setMeta( 'title', $this->getTitle() );
		$offerDataProvider->setMeta( 'description', $this->getDescription() );
		$offerDataProvider->setMeta( 'items', array_map( function ( SurveyItem $item ) {
			return $item->asArray();
		}, $this->getItems() ) );
	}
}
