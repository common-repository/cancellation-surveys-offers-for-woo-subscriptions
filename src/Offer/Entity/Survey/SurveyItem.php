<?php namespace MeowCrew\CancellationOffers\Offer\Entity\Survey;

use MeowCrew\CancellationOffers\Offer\Entity\Offer;

class SurveyItem {
	
	protected $title;
	protected $slug;
	protected $meta = array();
	
	public function __construct( string $title, string $slug, array $meta = array() ) {
		$this->title = $title;
		$this->slug  = $slug;
		$this->meta  = $meta;
	}
	
	public function isTextAnswerEnabled(): bool {
		return $this->meta['text_answer_enabled'] ?? false;
	}
	
	public function getTitle(): string {
		return $this->title;
	}
	
	public function getSlug(): string {
		return $this->slug;
	}
	
	public function getMeta(): array {
		return $this->meta;
	}
	
	public function isValid(): bool {
		return ! empty( $this->slug ) && ! empty( $this->title );
	}
	
	public function isDiscountOfferEnabled( Offer $offer ): bool {
		
		if ( empty( $offer->getDiscountOffer()->getSurveyOptions() ) ) {
			return true;
		}
		
		return in_array( $this->getSlug(), $offer->getDiscountOffer()->getSurveyOptions() );
	}
	
	public static function build( array $data ): self {
		$title = $data['title'] ?? '';
		$slug  = $data['slug'] ?? '';
		$meta  = $data['meta'] && is_array( $data['meta'] ) ? $data['meta'] : array();
		
		return new self( $title, $slug, $meta );
	}
	
	public function asArray(): array {
		return array(
			'title'               => $this->getTitle(),
			'slug'                => $this->getSlug(),
			'text_answer_enabled' => $this->isTextAnswerEnabled(),
			'meta'                => $this->getMeta(),
		);
	}
}
