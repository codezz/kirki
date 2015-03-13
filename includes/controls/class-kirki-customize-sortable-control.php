<?php

class Kirki_Customize_Sortable_Control extends WP_Customize_Control {

	public $type = 'sortable';

	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}


	public function render_content() {
		global $kirki;

		if ( ! is_array( $this->choices ) ) {
			return;
		}
		if ( ! count( $this->choices ) ) {
			return;
		}

		?>
		<label class='kirki-sortable'>
			<span class="customize-control-title">
				<?php echo esc_html( $this->label ); ?>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
			</span>

			<?php
				$values = $this->value();
				$values = $values == '' ? array_keys( $this->choices ) : $values;
				$values = is_serialized( $values ) ? unserialize( $values ) : $values;
				$this->visible_button = count( $values ) != count( $this->choices ) ? true : '';
				$visibleButton = '<i class="dashicons dashicons-visibility visibility"></i>';
			?>
			<ul>
				<?php foreach ( $values as $dummy => $value ) : ?>
					<?php printf( "<li class='kirki-sortable-item' data-value='%s'><i class='dashicons dashicons-menu'></i>%s%s</li>", esc_attr( $value ), $visibleButton, $this->choices[$value] ); ?>
				<?php endforeach; ?>
				<?php $invisibleKeys = array_diff( array_keys( $this->choices ), $values ); ?>
				<?php foreach ( $invisibleKeys as $dummy => $value ) : ?>
					<?php printf( "<li class='kirki-sortable-item' data-value='%s'><i class='dashicons dashicons-menu'></i>%s%s</li>", esc_attr( $value ), $visibleButton, $this->choices[$value] ); ?>
				<?php endforeach; ?>
			</ul>
			<div style='clear: both'></div>
			<?php $values = ! is_serialized( $values ) ? serialize( $values ) : $values; ?>
			<input type='hidden' <?php $this->link(); ?> value='<?php echo esc_attr( $values )  ?>'/>
		</label>
		<?php
	}
}
