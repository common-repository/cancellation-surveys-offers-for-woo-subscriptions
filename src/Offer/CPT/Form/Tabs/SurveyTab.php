<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Form\Tabs;

use MeowCrew\CancellationOffers\Offer\CPT\Form\FormTab;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;

class SurveyTab extends FormTab {
	
	public function getId(): string {
		return 'survey';
	}
	
	public function getTitle(): string {
		return __( 'Survey', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function getDescription(): string {
		return __( 'Define cancellation reasons.', 'cancellation-surveys-offers-for-woo-subscriptions' );
	}
	
	public function render( Offer $offer ) {
		$this->form->renderHint( __( 'Customize the survey by adding various reasons that customers might choose when canceling their subscription.',
			'cancellation-surveys-offers-for-woo-subscriptions' ) );
		?>

        <div id="cancellation-offer-survey-tab">

            <div id="cancellation-offer-survey-tab__is-enabled">
				<?php
					FormTab::renderInputRow( array(
						'id'      => 'survey_is_enabled',
						'type'    => 'checkbox',
						'checked' => $offer->getSurvey()->isEnabled(),
						'label'   => __( 'Enable Survey', 'cancellation-surveys-offers-for-woo-subscriptions' ),
					) );
				?>
            </div>

            <div id="cancellation-offers-survey__inner-options">
				
				<?php
					FormTab::renderInputRow( array(
						'id'    => 'survey_title',
						'label' => __( 'Survey Title', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'value' => $offer->getSurvey()->getTitle(),
					) );
					
					FormTab::renderInputRow( array(
						'id'    => 'survey_description',
						'type'  => 'textarea',
						'label' => __( 'Survey Description', 'cancellation-surveys-offers-for-woo-subscriptions' ),
						'value' => $offer->getSurvey()->getDescription(),
					) );
				?>

                <hr class="cancellation-offers-components__separator" data-title="
				<?php
					esc_html_e( 'Cancellation Reasons', 'cancellation-surveys-offers-for-woo-subscriptions' );
				?>
					">

                <div id="cancellation-offers-survey__survey-options" style="margin-top: 20px">
                    <div class="cancellation-offers-components-row">
                        <div class="cancellation-offers-components-row__label">
							<?php esc_html_e( 'Cancellation reasons',
								'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
                        </div>
                        <div class="cancellation-offers-components-row__value">

                            <div class="cancellation-offers-survey__survey-items">
								
								<?php $iterator = 0; ?>
								
								<?php foreach ( $offer->getSurvey()->getItems() as $item ) : ?>
                                    <div class="cancellation-offers-survey__survey-item">
                                        <div class="cancellation-offers-survey__survey-item__main">
                                            <div class="cancellation-offers-survey__survey-item__title">
                                                <input type="text" required
                                                       value="<?php echo esc_attr( $item->getTitle() ); ?>"
                                                       name="survey_options[title][]">
                                                <input type="hidden" value="<?php echo esc_attr( $item->getSlug() ); ?>"
                                                       name="survey_options[slug][]">
                                            </div>
                                            <div class="cancellation-offers-survey__survey-item__actions">
                                                <button type="button"
                                                        title="Remove survey item"
                                                        class="notice-dismiss cancellation-offers-survey__survey-item__actions--remove cancellation-offers-components__dismiss-button">
                                                </button>
                                            </div>
                                        </div>

                                        <div class="cancellation-offers-survey__survey-item__options">
                                            <div class="cancellation-offers-survey__survey-item__options-text-answer">
                                                <label><input
                                                            value="yes"
                                                            type="checkbox" <?php echo checked( $item->isTextAnswerEnabled() ); ?>
                                                            name="survey_options[text_answer_enabled][<?php echo esc_attr( $iterator ); ?>]">
													<?php esc_html_e( 'Ask for details in the text field',
														'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
									<?php $iterator ++; ?>
								<?php endforeach; ?>
                            </div>

                            <button type="button" class="button" id="cancellation-offers-survey__survey-add-new">
								<?php esc_html_e( 'Add cancellation reason',
									'cancellation-surveys-offers-for-woo-subscriptions' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
		<?php
	}
}
