<?php

class ACF_Fancy_Repeater_Field_V5 {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new ACF_Fancy_Repeater_Field_V5();
		}
	}

	public function __construct() {
		// vars
		$this->name = 'fancyrepeater';
		$this->label = __( "Fancy Repeater", 'acf' );
		$this->category = 'layout';
		$this->defaults = array(
		    'sub_fields' => array(),
		    'min' => 0,
		    'max' => 0,
		    'layout' => 'table',
		    'button_label' => __( "Add Row", 'acf' ),
		);
		$this->l10n = array(
		    'min' => __( "Minimum rows reached ({min} rows)", 'acf' ),
		    'max' => __( "Maximum rows reached ({max} rows)", 'acf' ),
		);


		add_filter( "acf/load_field/type=repeater", array($this, 'load_field'), 10, 1 );
		add_action( "acf/render_field_settings/type=repeater", array($this, 'render_field_settings'), 10, 1 );
		add_action( "acf/render_field_settings/type=fancyrepeater", array($this, 'render_field_settings'), 10, 1 );

		add_filter( "acf/update_field/type=fancyrepeater", array($this, 'update_field'), 5, 1 );
		add_filter( "acf/duplicate_field/type=fancyrepeater", array($this, 'duplicate_field'), 5, 1 );
		add_action( "acf/delete_field/type=fancyrepeater", array($this, 'delete_field'), 5, 1 );
		add_action( "acf/render_field/type=fancyrepeater", array($this, 'render_field'), 5, 1 );


		add_filter( "acf/load_value/type=fancyrepeater", array($this, 'load_value'), 10, 3 );
		add_filter( "acf/update_value/type=fancyrepeater", array($this, 'update_value'), 10, 3 );
		add_filter( "acf/format_value/type=fancyrepeater", array($this, 'format_value'), 10, 3 );
		add_filter( "acf/validate_value/type=fancyrepeater", array($this, 'validate_value'), 10, 4 );
		add_action( "acf/delete_value/type=fancyrepeater", array($this, 'delete_value'), 10, 2 );
	}

	public function load_field( $field ) {
		global $post;

		$field['use_fancy_repeater'] = isset( $field['use_fancy_repeater'] ) ? $field['use_fancy_repeater'] : 'no';
		if ( empty( $post ) || ( $post && $post->post_type != 'acf-field-group' ) ) {
			$field['use_fancy_repeater'] = isset( $field['use_fancy_repeater'] ) ? $field['use_fancy_repeater'] : 'no';
			$field['forced_fancy_repeater'] = false;
			if ( $field['use_fancy_repeater'] == 'yes' ) {
				$field['type'] = 'fancyrepeater';
				$field['forced_fancy_repeater'] = true;
			}
		}

		return $field;
	}

	public function render_field_settings( $field ) {

		// layout
		acf_render_field_setting( $field, array(
		    'label' => __( 'Fancy Repeater', 'acf' ),
		    'instructions' => '',
		    'class' => 'acf-repeater-use-fancy-repeater',
		    'type' => 'radio',
		    'name' => 'use_fancy_repeater',
		    'layout' => 'horizontal',
		    'default_value' => 'no',
		    'value' => isset( $field['use_fancy_repeater'] ) ? $field['use_fancy_repeater'] : 'no',
		    'choices' => array(
			'yes' => __( 'Yes', 'acf' ),
			'no' => __( 'No', 'acf' )
		    )
		) );

		return $field;
	}

	public function load_value( $value, $post_id, $field ) {
		return apply_filters( 'acf/load_value/type=repeater', $value, $post_id, $field );
	}

	public function format_value( $value, $post_id, $field ) {
		return apply_filters( 'acf/format_value/type=repeater', $value, $post_id, $field );
	}

	public function validate_value( $valid, $value, $field, $input ) {
		return apply_filters( 'acf/validate_value/type=repeater', $valid, $value, $field, $input );
	}

	public function update_value( $value, $post_id, $field ) {
		return apply_filters( 'acf/update_value/type=repeater', $value, $post_id, $field );
	}

	public function delete_field( $field ) {
		return do_action( 'acf/delete_field/type=repeater', $field );
	}

	public function update_field( $field ) {
		if ( isset( $field['forced_fancy_repeater'] ) && !empty( $field['forced_fancy_repeater'] ) ) {
			unset( $field['forced_fancy_repeater'] );
		}

		return apply_filters( 'acf/update_field/type=repeater', $field );
	}

	public function duplicate_field( $field ) {
		return apply_filters( 'acf/duplicate_field/type=repeater', $field );
	}

	function render_field( $field ) {

		// ensure value is an array
		if ( empty( $field['value'] ) ) {

			$field['value'] = array();
		}


		// rows
		$field['min'] = empty( $field['min'] ) ? 0 : $field['min'];
		$field['max'] = empty( $field['max'] ) ? 0 : $field['max'];


		// populate the empty row data (used for acfcloneindex and min setting)
		$empty_row = array();
		if ( isset( $field['sub_fields'] ) && !empty( $field['sub_fields'] ) ) {
			foreach ( $field['sub_fields'] as $f ) {
				$empty_row[$f['key']] = isset( $f['default_value'] ) ? $f['default_value'] : false;
			}
		}



		// If there are less values than min, populate the extra values
		if ( $field['min'] ) {

			for ( $i = 0; $i < $field['min']; $i++ ) {

				// continue if already have a value
				if ( array_key_exists( $i, $field['value'] ) ) {

					continue;
				}


				// populate values
				$field['value'][$i] = $empty_row;
			}
		}


		// If there are more values than man, remove some values
		if ( $field['max'] ) {

			for ( $i = 0; $i < count( $field['value'] ); $i++ ) {

				if ( $i >= $field['max'] ) {

					unset( $field['value'][$i] );
				}
			}
		}


		// setup values for row clone
		$field['value']['acfcloneindex'] = $empty_row;


		// show columns
		$show_order = true;
		$show_add = true;
		$show_remove = true;


		if ( $field['max'] ) {

			if ( $field['max'] == 1 ) {

				$show_order = false;
			}

			if ( $field['max'] <= $field['min'] ) {

				$show_remove = false;
				$show_add = false;
			}
		}


		// field wrap
		$el = 'td';
		$before_fields = '';
		$after_fields = '';

		if ( $field['layout'] == 'row' ) {

			$el = 'tr';
			$before_fields = '<td class="acf-table-wrap"><table class="acf-table">';
			$after_fields = '</table></td>';
		} elseif ( $field['layout'] == 'block' ) {

			$el = 'div';
			$before_fields = '<td class="acf-fields">';
			$after_fields = '</td>';
		}


		// hidden input
		acf_hidden_input( array(
		    'type' => 'hidden',
		    'name' => $field['name'],
		) );
		?>

		<div <?php acf_esc_attr_e( array('class' => 'acf-repeater acf-fancyrepeater', 'data-min' => $field['min'], 'data-max' => $field['max'], 'data-titlefieldkey' => $this->get_title_field_key( $field )) ); ?>>


			<div class="acf-fancyrepeater-list-wrap">
				<ul class="acf-hl acf-thead">
					<li class="li-fancyrepeater-order"><?php _e( 'Order', 'acf_child_post_field' ); ?></li>
					<li class="li-fancyrepeater-label"><?php _e( 'Item', 'acf_child_post_field' ); ?></li>
					<li class="li-fancyrepeater-name"></li>
					<li class="li-fancyrepeater-type"></li>
				</ul>

				<div class="acf-fancyrepeater-list">

					<?php foreach ( $field['value'] as $i => $row ): ?>
						<?php $clone_class = ($i === 'acfcloneindex') ? ' acf-clone' : ''; ?>


						<div class="acf-fancyrepeater-object <?php echo $clone_class; ?>">
							<?php $this->render_child_table( $field, $row, $i, $before_fields, $after_fields, $show_order, $el ); ?>
						</div>
					<?php endforeach; ?>
				</div>
				<ul class="acf-hl acf-tfoot">
					<li class="comic-sans"><i class="acf-sprite-arrow"></i><?php _e( 'Drag and drop to reorder', 'acf' ); ?></li>
					<li class="acf-fr">
						<a href="#" class="acf-button blue acf-fancyrepeater-add-row">+ <?php echo $field['button_label']; ?></a>
					</li>
				</ul>
			</div>

		</div>
		<?php
	}

	private function render_child_table( $field, $row, $i, $before_fields, $after_fields, $show_order, $el ) {
		$title_input_key = $this->get_title_field_key( $field );
		include 'views/repeater-table.php';
	}

	private function get_title_field_key( $field ) {
		if ( isset( $field['sub_fields'] ) ) {
			return $field['sub_fields'][0]['key'];
		} else {
			return '';
		}
	}

}
