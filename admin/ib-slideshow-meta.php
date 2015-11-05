<?php

class IB_Slideshow_Field {
	/**
	 * @access public
	 * @var string
	 */
	public $type;

	/**
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * @access public
	 * @var string
	 */
	public $label;

	/**
	 * @access public
	 * @var string
	 */
	public $description;

	/**
	 * @access public
	 * @var array
	 */
	public $choices;

	/**
	 * @access public
	 * @var array
	 */
	public $slideshow_type;

	/**
	 * @access public
	 * @var string
	 */
	public $class;

	/**
	 * Constructor.
	 *
	 * @param array $options
	 */
	public function __construct( $options ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $options[ $key ] ) ) {
				$this->$key = $options[ $key ];
			}
		}

		if ( $this->slideshow_type && ! is_array( $this->slideshow_type ) ) {
			$this->slideshow_type = array( $this->slideshow_type );
		}
	}

	/**
	 * Check if this field belongs to a given slideshow type.
	 *
	 * @param string $type
	 * @return boolean
	 */
	public function validSlideshowType( $type ) {
		if ( ! $this->slideshow_type || ! $type ) return true;
		return in_array( $type, $this->slideshow_type );
	}

	/**
	 * Output the field.
	 *
	 * @param mixed $value
	 */
	public function output( $value, $name_prefix = '', $name_suffix = '' ) {
		// Name.
		$name = $this->name;

		if ( ! empty( $name_prefix ) ) {
			$name = $name_prefix . $name;
		}

		if ( ! empty( $name_suffix ) ) {
			$name .= $name_suffix;
		}

		// Slideshow type.
		$slideshow_type = '';

		if ( ! empty( $this->slideshow_type ) ) {
			$slideshow_type = ' data-slideshow_type="' . esc_attr( implode( ',', $this->slideshow_type ) ) . '"';
		}

		// Id.
		$id = empty( $this->id ) ? '' : ' id="' . esc_attr( $this->id ) . '"';

		// Class.
		$class = empty( $this->class ) ? '' : ' class="' . esc_attr( $this->class ) . '"';

		switch ( $this->type ) {
			case 'checkbox':
				?>
				<div class="ib-slideshow-field checkbox"<?php echo $slideshow_type; ?>>
					<div class="control">
						<label>
							<input type="checkbox" name="<?php echo esc_attr( $name ); ?>"<?php echo $id; echo $class; ?> value="1"<?php checked( $value, 1 ); ?>>
							<?php echo $this->label; ?>
						</label>

						<?php
							if ( $this->description ) {
								echo '<div class="description">' . $this->description . '</div>';
							}
						?>
					</div>
				</div>
				<?php
				break;

			case 'textarea':
				?>
				<div class="ib-slideshow-field"<?php echo $slideshow_type; ?>>
					<div class="label"><label><?php echo $this->label; ?></label></div>
					<div class="control">
						<textarea name="<?php echo esc_attr( $name ); ?>"<?php echo $id; echo $class; ?>><?php echo esc_textarea( $value ); ?></textarea>
						<?php
							if ( $this->description ) {
								echo '<div class="description">' . $this->description . '</div>';
							}
						?>
					</div>
				</div>
				<?php
				break;

			case 'select':
				if ( empty( $this->choices ) ) return;

				?>
				<div class="ib-slideshow-field"<?php echo $slideshow_type; ?>>
					<div class="label"><label><?php echo $this->label; ?></label></div>
					<div class="control">
						<select name="<?php echo esc_attr( $name ); ?>"<?php echo $id; echo $class; ?>>
							<?php
								foreach ( $this->choices as $choice_value => $label ) {
									echo '<option value="' . esc_attr( $choice_value ) . '"' . selected( $value, $choice_value, false ) . '>' . esc_html( $label ) . '</option>';
								}
							?>
						</select>
						<?php
							if ( $this->description ) {
								echo '<div class="description">' . $this->description . '</div>';
							}
						?>
					</div>
				</div>
				<?php
				break;

			default:
				?>
				<div class="ib-slideshow-field"<?php echo $slideshow_type; ?>>
					<div class="label"><label><?php echo $this->label; ?></label></div>
					<div class="control">
						<input type="text" name="<?php echo esc_attr( $name ); ?>"<?php echo $id; echo $class; ?> value="<?php echo esc_attr( $value ); ?>">
						<?php
							if ( $this->description ) {
								echo '<div class="description">' . $this->description . '</div>';
							}
						?>
					</div>
				</div>
				<?php
		}
	}

	/**
	 * Sanitize the field value.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function sanitize( $value ) {
		switch ( $this->type ) {
			case 'checkbox':
				if ( 1 != $value ) $value = 0;
				break;

			case 'select':
				if ( empty( $this->choices ) || ! array_key_exists( $value, $this->choices ) ) {
					$value = '';
				}
				break;

			default:
				if ( ! current_user_can( 'unfiltered_html' ) ) {
					$value = wp_kses_data( $value );
				}
		}

		return $value;
	}
}

class IB_Slideshow_Meta {
	/**
	 * @access public
	 * @var array
	 */
	public $fields = array();

	/**
	 * @access public
	 * @var array
	 */
	public $values = array();

	/**
	 * @access public
	 * @var string
	 */
	public $slideshow_type;

	/**
	 * Constructor.
	 *
	 * @param array $fields
	 * @param string $slideshow_type
	 */
	public function __construct( $fields = null, $slideshow_type = null ) {
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				$this->add_field( $field );
			}
		}

		$this->slideshow_type = $slideshow_type;
	}

	/**
	 * Add field.
	 *
	 * @param mixed $field
	 */
	public function add_field( $field ) {
		if ( ! is_object( $field ) ) {
			$this->fields[] = new IB_Slideshow_Field( $field );
		} else {
			$this->fields[] = $field;
		}
	}

	/**
	 * Set values for the fields.
	 *
	 * @param array $values
	 */
	public function set_values( $values ) {
		$this->values = $values;
	}

	/**
	 * Output this field.
	 *
	 * @param string $name_prefix
	 * @param string $name_suffix
	 */
	public function output( $name_prefix = '', $name_suffix = '' ) {
		foreach ( $this->fields as $field ) {
			if ( ! $field->validSlideshowType( $this->slideshow_type ) ) {
				continue;
			}

			$value = ! isset( $this->values[ $field->name ] ) ? '' : $this->values[ $field->name ];
			$field->output( $value, $name_prefix, $name_suffix );
		}
	}
}
