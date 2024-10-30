<?php namespace MeowCrew\CancellationOffers\Offer\CPT\Form;

use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use MeowCrew\CancellationOffers\Offer\Entity\Offer;

abstract class FormTab {
	
	use ServiceContainerTrait;
	
	/**
	 * Form
	 *
	 * @var Form
	 */
	protected $form;
	
	public function __construct( Form $form ) {
		$this->form = $form;
	}
	
	abstract public function getId();
	
	abstract public function getTitle();
	
	abstract public function getDescription();
	
	abstract public function render( Offer $offer );
	
	public static function renderInputRow( $args = array() ) {
		
  
		$args = wp_parse_args( $args, array(
			'id'                => false,
			'label'             => '',
			'type'              => 'text',
			'custom_attributes' => array(),
			'is_required'       => false,
			'description'       => false,
			'value'             => null,
			'placeholder'       => '',
			'help_tip'          => false,
		) );
		
		// Consider throwing an  error
		if ( ! $args['id'] ) {
			return;
		}
		
		?>
		<div class="cancellation-offers-components-row">
			<div class="cancellation-offers-components-row__label">
				<label for="<?php echo esc_attr( $args['id'] ); ?>">
					<?php echo esc_html( $args['label'] ); ?>
				</label>
				
				<?php if ( $args['help_tip'] ) : ?>
					<?php echo wp_kses_post( wc_help_tip( $args['help_tip'] ) ); ?>
				<?php endif; ?>
			</div>
			<div class="cancellation-offers-components-row__value">
				
				<?php if ( in_array( $args['type'], array( 'text', 'number', 'email' ) ) ) : ?>

					<input <?php echo esc_attr( $args['is_required'] ? 'required' : '' ); ?>
							name="<?php echo esc_attr( $args['id'] ); ?>"
							type="<?php echo esc_attr( $args['type'] ); ?>"
							placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
							id="<?php echo esc_attr( $args['id'] ); ?>"
							type="<?php echo esc_attr( $args['type'] ); ?>"
							value="<?php echo esc_attr( $args['value'] ); ?>">
				
				<?php elseif ( 'textarea' === $args['type'] ) : ?>
					<textarea <?php echo esc_attr( $args['is_required'] ? 'required' : '' ); ?>
							id="<?php echo esc_attr( $args['id'] ); ?>"
							rows="4"
							name="<?php echo esc_attr( $args['id'] ); ?>"
							placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					><?php echo esc_attr( $args['value'] ); ?></textarea>
				<?php elseif ( 'checkbox' === $args['type'] ) : ?>
					<div class="cancellation-offers-components__checkbox">
						<?php
							$args = wp_parse_args( $args, array(
								'checked' => false,
							) );
						?>

						<input type="checkbox"
							   value="yes"
							   name="<?php echo esc_attr( $args['id'] ); ?>"
							   id="<?php echo esc_attr( $args['id'] ); ?>"
							<?php checked( $args['checked'] ); ?>/>

						<label for="<?php echo esc_attr( $args['id'] ); ?>"></label>
					</div>
				
				<?php elseif ( 'multiple-select' === $args['type'] ) : ?>
					<?php
					$args = wp_parse_args( $args, array(
						'action'    => '',
						'options'   => array(),
						'css_class' => 'wc-product-search',
					) );
					?>
					<div>
						<select class="<?php echo esc_attr( $args['css_class'] ); ?>" multiple="multiple"
								id="<?php echo esc_attr( $args['id'] ); ?>"
								name="<?php echo esc_attr( $args['id'] ); ?>[]"
								data-placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
							<?php if ( $args['action'] ) : ?>
								data-action="<?php echo esc_attr( $args['action'] ); ?>"
							<?php endif; ?>>
							
							<?php foreach ( $args['options'] as $key => $optionData ) : ?>
								<option <?php selected( $optionData['is_selected'] ); ?>
										value="<?php echo esc_attr( $key ); ?>">
									<?php echo esc_attr( $optionData['label'] ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>
				
				
				<?php if ( $args['description'] ) : ?>
					<p class="description">
						<?php echo wp_kses_post( $args['description'] ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
