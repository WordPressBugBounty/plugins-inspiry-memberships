<?php
/**
 * IMS Settings Class
 *
 * Settings class form IMS.
 *
 * @since    1.0.0
 * @package  IMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_OSA.
 *
 * WP Settings API Class.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'WP_OSA' ) ) :

	class WP_OSA {

		/**
		 * Sections array.
		 *
		 * @since    1.0.0
		 * @var    array
		 */
		private $sections_array = array();

		/**
		 * Fields array.
		 *
		 * @since    1.0.0
		 * @var    array
		 */
		private $fields_array = array();

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			// Enqueue the admin scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			// Hook it up.
			add_action( 'admin_init', array( $this, 'admin_init' ) );

			// Menu.
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		}

		/**
		 * Admin Scripts.
		 *
		 * @since 1.0.0
		 */
		public function admin_scripts() {
			// jQuery is needed.
			wp_enqueue_script( 'jquery' );

			// Color Picker
			wp_enqueue_style( 'wp-color-picker' );

			// Media Uploader.
			wp_enqueue_media();
		}


		/**
		 * Set Sections.
		 *
		 * @since 1.0.0
		 *
		 * @param array $sections
		 */
		public function set_sections( $sections ) {
			// Bail if not array.
			if ( ! is_array( $sections ) ) {
				return false;
			}

			// Assign to the sections array.
			$this->sections_array = $sections;

			return $this;
		}


		/**
		 * Add a single section.
		 *
		 * @since 1.0.0
		 *
		 * @param array $section
		 */
		public function add_section( $section ) {
			// Bail if not array.
			if ( ! is_array( $section ) ) {
				return false;
			}

			// Assign the section to sections array.
			$this->sections_array[] = $section;

			return $this;
		}


		/**
		 * Set Fields.
		 *
		 * @since 1.0.0
		 */
		public function set_fields( $fields ) {
			// Bail if not array.
			if ( ! is_array( $fields ) ) {
				return false;
			}

			// Assign the fields.
			$this->fields_array = $fields;

			return $this;
		}


		/**
		 * Add a single field.
		 *
		 * @since 1.0.0
		 */
		public function add_field( $section, $field_array ) {
			// Set the defaults
			$defaults = array(
				'id'   => '',
				'name' => '',
				'desc' => '',
				'type' => 'text'
			);

			// Combine the defaults with user's arguements.
			$arg = wp_parse_args( $field_array, $defaults );

			// Each field is an array named against its section.
			$this->fields_array[ $section ][] = $arg;

			return $this;
		}


		/**
		 * Initialize API.
		 *
		 * Initializes and registers the settings sections and fields.
		 * Usually this should be called at `admin_init` hook.
		 *
		 * @since  1.0.0
		 */
		function admin_init() {
			/**
			 * Register the sections.
			 *
			 * Sections array is like this:
			 *
			 *        $sections_array = array (
			 *            $section_array,
			 *            $section_array,
			 *            $section_array,
			 *        );
			 *
			 * Section array is like this:
			 *
			 *        $section_array = array (
			 *            'id'    => 'section_id',
			 *            'title' => 'Section Title'
			 *        );
			 *
			 *
			 * @since 1.0.0
			 */
			foreach ( $this->sections_array as $section ) {
				if ( false == get_option( $section['id'] ) ) {
					// Add a new field as section ID.
					add_option( $section['id'] );
				}

				// Deals with sections description.
				if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
					// Build HTML.
					$section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';

					// Set the callback for description.
					$callback = array( $this, 'callback' );

				} else if ( isset( $section['callback'] ) ) {
					$callback = $section['callback'];
				} else {
					$callback = null;
				}


				/**
				 * Add a new section to a settings page.
				 *
				 * @since 1.0.0
				 *
				 * @param string   $title
				 * @param callable $callback
				 * @param string   $page | Page is same as sectipn ID.
				 * @param string   $id
				 */
				add_settings_section( $section['id'], $section['title'], $callback, $section['id'] );
			} // foreach ended.


			/**
			 * Register settings fields.
			 *
			 * Fields array is like this:
			 *
			 *        $fields_array = array (
			 *            $section => $field_array,
			 *            $section => $field_array,
			 *            $section => $field_array,
			 *        );
			 *
			 *
			 * Field array is like this:
			 *
			 *        $field_array = array (
			 *            'id'    => 'id',
			 *            'name'    => 'Name',
			 *            'type'    => 'text'
			 *        );
			 *
			 * @since 1.0.0
			 */
			foreach ( $this->fields_array as $section => $field_array ) {
				foreach ( $field_array as $field ) {
					// ID.
					$id = isset( $field['id'] ) ? $field['id'] : false;

					// Type.
					$type = isset( $field['type'] ) ? $field['type'] : 'text';

					// Name.
					$name = isset( $field['name'] ) ? $field['name'] : 'No Name Added';

					// Label for.
					$label_for = "{$section}[{$field['id']}]";

					// Description.
					$description = isset( $field['desc'] ) ? $field['desc'] : '';

					// Size.
					$size = isset( $field['size'] ) ? $field['size'] : null;

					// Options.
					$options = isset( $field['options'] ) ? $field['options'] : '';

					// Standard default value.
					$default = isset( $field['default'] ) ? $field['default'] : '';

					// Sanitize Callback.
					$sanitize_callback = isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : '';

					$args = array(
						'id'                => $id,
						'type'              => $type,
						'name'              => $name,
						'label_for'         => $label_for,
						'desc'              => $description,
						'section'           => $section,
						'size'              => $size,
						'options'           => $options,
						'std'               => $default,
						'sanitize_callback' => $sanitize_callback
					);

					/**
					 * Add a new field to a section of a settings page.
					 *
					 * @since 1.0.0
					 *
					 * @param string   $title
					 * @param callable $callback
					 * @param string   $page
					 * @param string   $section = 'default'
					 * @param array    $args    = array()
					 * @param string   $id
					 */

					// @param string 	$id
					$field_id = $section . '[' . $field['id'] . ']';

					add_settings_field(
						$field_id,
						$name,
						array( $this, 'callback_' . $type ),
						$section,
						$section,
						$args
					);
				} // foreach ended.
			} // foreach ended.


			// Creates our settings in the fields table.
			foreach ( $this->sections_array as $section ) {
				/**
				 * Registers a setting and its sanitization callback.
				 *
				 * @since 1.0.0
				 *
				 * @param string   $field_name        | The name of an option to sanitize and save.
				 * @param callable $sanitize_callback = ''
				 * @param string   $field_group       | A settings group name.
				 */
				register_setting( $section['id'], $section['id'], array( $this, 'sanitize_fields' ) );
			} // foreach ended.

		} // admin_init() ended.

		/**
		 * A callback function to display filtered text.
		 *
		 * @param string $text Text to display.
		 */
		public function callback( $text ) {
			echo esc_html( str_replace( '"', '\"', $text ) );
		}

		/**
		 * Sanitize callback for Settings API fields.
		 *
		 * @since 1.0.0
		 */
		public function sanitize_fields( $fields ) {
			foreach ( $fields as $field_slug => $field_value ) {
				$sanitize_callback = $this->get_sanitize_callback( $field_slug );

				// If callback is set, call it
				if ( $sanitize_callback ) {
					$fields[ $field_slug ] = call_user_func( $sanitize_callback, $field_value );
					continue;
				}
			}

			return $fields;
		}


		/**
		 * Get sanitization callback for given option slug
		 *
		 * @since  1.0.0
		 *
		 * @param string $slug option slug
		 *
		 * @return mixed    string | bool    false
		 */
		function get_sanitize_callback( $slug = '' ) {
			if ( empty( $slug ) ) {
				return false;
			}

			// Iterate over registered fields and see if we can find proper callback.
			foreach ( $this->fields_array as $section => $field_array ) {
				foreach ( $field_array as $field ) {
					if ( $field['name'] != $slug ) {
						continue;
					}

					// Return the callback name
					return isset( $field['sanitize_callback'] ) && is_callable( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : false;
				}
			}

			return false;
		}


		/**
		 * Get field description for display
		 *
		 * @param array $args settings field args
		 */
		public function get_field_description( $args ) {
			if ( ! empty( $args['desc'] ) ) {
				$desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
			} else {
				$desc = '';
			}

			return $desc;
		}


		/**
		 * Displays a text field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_text( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type  = isset( $args['type'] ) ? $args['type'] : 'text';

			$html = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"/>', $type, $size, $args['section'], $args['id'], $value );
			$html .= $this->get_field_description( $args );

			echo $html;
		}


		/**
		 * Displays a url field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_url( $args ) {
			$this->callback_text( $args );
		}

		/**
		 * Displays a number field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_number( $args ) {
			$this->callback_text( $args );
		}

		/**
		 * Displays a checkbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_checkbox( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

			$html = '<fieldset>';
			$html .= sprintf( '<label for="wpuf-%1$s[%2$s]">', $args['section'], $args['id'] );
			$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
			$html .= sprintf( '<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked( $value, 'on', false ) );
			$html .= sprintf( '%1$s</label>', $args['desc'] );
			$html .= '</fieldset>';

			echo $html;
		}

		/**
		 * Displays a multicheckbox a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_multicheck( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );

			$html = '<fieldset>';
			foreach ( $args['options'] as $key => $label ) {
				$checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
				$html    .= sprintf( '<label for="wpuf-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
				$html    .= sprintf( '<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
				$html    .= sprintf( '%1$s</label><br>', $label );
			}
			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';

			echo $html;
		}

		/**
		 * Displays a multicheckbox a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_radio( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );

			$html = '<fieldset>';
			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<label for="wpuf-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
				$html .= sprintf( '<input type="radio" class="radio" id="wpuf-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
				$html .= sprintf( '%1$s</label><br>', $label );
			}
			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';

			echo $html;
		}

		/**
		 * Displays a selectbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_select( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
			}
			$html .= sprintf( '</select>' );
			$html .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays a textarea for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_textarea( $args ) {

			$value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], $value );
			$html .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays a textarea for a settings field
		 *
		 * @param array $args settings field args
		 *
		 * @return string
		 */
		function callback_html( $args ) {
			echo $this->get_field_description( $args );
		}

		/**
		 * Displays a rich text textarea for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_wysiwyg( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';

			echo '<div style="max-width: ' . $size . ';">';

			$editor_settings = array(
				'teeny'         => true,
				'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
				'textarea_rows' => 10
			);
			if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
				$editor_settings = array_merge( $editor_settings, $args['options'] );
			}

			wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );

			echo '</div>';

			echo $this->get_field_description( $args );
		}

		/**
		 * Displays a file upload field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_file( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$id    = $args['section'] . '[' . $args['id'] . ']';
			$label = isset( $args['options']['button_label'] ) ?
				$args['options']['button_label'] :
				esc_html__( 'Choose File' );

			$html = sprintf( '<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			$html .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
			$html .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays an image upload field with a preview
		 *
		 * @param array $args settings field args
		 */
		function callback_image( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$id    = $args['section'] . '[' . $args['id'] . ']';
			$label = isset( $args['options']['button_label'] ) ?
				$args['options']['button_label'] :
				esc_html__( 'Choose Image' );

			$html = sprintf( '<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			$html .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
			$html .= $this->get_field_description( $args );
			$html .= '<p class="wpsa-image-preview"><img src=""/></p>';

			echo $html;
		}

		/**
		 * Displays a password field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_password( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			$html .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays a color picker field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_color( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, $args['std'] );
			$html .= $this->get_field_description( $args );

			echo $html;
		}


		/**
		 * Get the value of a settings field
		 *
		 * @param string $option  settings field name
		 * @param string $section the section name this field belongs to
		 * @param string $default default text if it's not found
		 *
		 * @return string
		 */
		function get_option( $option, $section, $default = '' ) {

			$options = get_option( $section );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return $default;
		}

		/**
		 * Add submenu page to the Settings main menu.
		 *
		 * @param string   $page_title
		 * @param string   $menu_title
		 * @param string   $capability
		 * @param string   $menu_slug
		 * @param callable $function = ''
		 */
		public function admin_menu() {
			add_submenu_page(
				'inspiry_memberships',
				esc_html__( 'Settings', IMS_TEXT_DOMAIN ),
				esc_html__( 'Settings', IMS_TEXT_DOMAIN ),
				'manage_options',
				'ims_settings',
				array( $this, 'plugin_page' )
			);
		}

		public function plugin_page() {
			?>
            <div id="realhomes-settings-wrap">
                <header class="settings-header">
                    <h1><?php esc_html_e( 'RealHomes Memberships Settings', IMS_TEXT_DOMAIN ); ?><span class="current-version-tag"><?php echo IMS_VERSION; ?></span></h1>
                    <p class="credit">
                        <a class="logo-wrap" href="https://themeforest.net/item/real-homes-wordpress-real-estate-theme/5373914?aid=inspirythemes" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" height="29" width="29" viewBox="0 0 36 41">
                                <style>
                                    .a{
                                        fill:#4E637B;
                                    }
                                    .b{
                                        fill:white;
                                    }
                                    .c{
                                        fill:#27313D !important;
                                    }
                                </style><g>
                                    <path d="M25.5 14.6C28.9 16.6 30.6 17.5 34 19.5L34 11.1C34 10.2 33.5 9.4 32.8 9 30.1 7.5 28.4 6.5 25.5 4.8L25.5 14.6Z" class="a"></path>
                                    <path d="M15.8 38.4C16.5 38.8 17.4 38.8 18.2 38.4 20.8 36.9 22.5 35.9 25.5 34.2 22.1 32.2 20.4 31.3 17 29.3 13.6 31.3 11.9 32.2 8.5 34.2 11.5 35.9 13.1 36.9 15.8 38.4" mask="url(#mask-2)" class="a"></path>
                                    <path d="M24.3 25.1C25 24.7 25.5 23.9 25.5 23L25.5 14.6 17 19.5 17 29.3 24.3 25.1Z" fill="#C8ED1E"></path>
                                    <path d="M18.2 10.4C17.4 10 16.5 10 15.8 10.4L8.5 14.6 17 19.5 25.5 14.6 18.2 10.4Z" fill="#F9FAF8"></path>
                                    <path d="M8.5 23C8.5 23.9 8.9 24.7 9.7 25.1L17 29.3 17 19.5 8.5 14.6 8.5 23Z" fill="#88B2D7"></path>
                                    <path d="M8.5 14.6C5.1 16.6 3.4 17.5 0 19.5L0 11.1C0 10.2 0.5 9.4 1.2 9 3.8 7.5 5.5 6.5 8.5 4.8L8.5 14.6Z" mask="url(#mask-4)" class="a"></path>
                                    <path d="M34 27.9L34 19.5 25.5 14.6 25.5 23C25.5 23.4 25.4 23.8 25.1 24.2L33.6 29.1C33.8 28.7 34 28.3 34 27.9" fill="#5E9E2D"></path>
                                    <path d="M25.1 24.2C24.9 24.6 24.6 24.9 24.3 25.1L17 29.3 25.5 34.2 32.8 30C33.1 29.8 33.4 29.5 33.6 29.1L25.1 24.2Z" fill="#6FBF2C"></path>
                                    <path d="M17 10.1C17.4 10.1 17.8 10.2 18.2 10.4L25.5 14.6 25.5 4.8 18.2 0.6C17.8 0.4 17.4 0.3 17 0.3L17 10.1Z" fill="#BDD2E1"></path>
                                    <path d="M1.2 30L8.5 34.2 17 29.3 9.7 25.1C9.3 24.9 9 24.6 8.8 24.2L0.3 29.1C0.5 29.5 0.8 29.8 1.2 30" fill="#418EDA"></path>
                                    <path d="M8.8 24.2C8.6 23.8 8.5 23.4 8.5 23L8.5 14.6 0 19.5 0 27.9C0 28.3 0.1 28.7 0.3 29.1L8.8 24.2Z" fill="#3570AA"></path>
                                    <path d="M15.8 0.6L8.5 4.8 8.5 14.6 15.8 10.4C16.2 10.2 16.6 10.1 17 10.1L17 0.3C16.6 0.3 16.2 0.4 15.8 0.6" fill="#A7BAC8"></path>
                                </g>
                            </svg>InspiryThemes
                        </a>
                    </p>
                </header>
                <div class="settings-content">
					<?php
                    settings_errors();
					$this->show_navigation();
					$this->show_forms();
					?>
                </div>
                <footer class="settings-footer">
                    <p>
                        <span class="dashicons dashicons-editor-help"></span>
						<?php printf( esc_html__( 'For help, please consult the %1$s documentation %2$s of the plugin.', IMS_TEXT_DOMAIN ), '<a href="' . esc_url( IMS_DOCS_URL ) . '" target="_blank">', '</a>' ); ?>
                    </p>
                    <p>
                        <span class="dashicons dashicons-feedback"></span>
	                    <?php printf( esc_html__( 'For feedback, please provide your %1$s feedback here! %2$s', IMS_TEXT_DOMAIN ), '<a href="' . esc_url( IMS_ISSUE_URL ) . '" target="_blank">', '</a>' ); ?>
                    </p>
                </footer>
            </div>
			<?php
		}

		/**
		 * Show navigations as tab
		 *
		 * Shows all the settings section labels as tab
		 */
		function show_navigation() {
			$html = '<div class="nav-tab-wrapper">';

			foreach ( $this->sections_array as $tab ) {
				$html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title'] );
			}

			$html .= '</div>';

			echo $html;
		}

		/**
		 * Show the section settings forms
		 *
		 * This function displays every sections in a different form
		 */
		function show_forms() {
			?>
            <div class="form-wrapper">
				<?php foreach ( $this->sections_array as $form ) { ?>
                    <!-- style="display: none;" -->
                    <div id="<?php echo $form['id']; ?>" class="group">
                        <form method="post" action="options.php">
							<?php
							do_action( 'wsa_form_top_' . $form['id'], $form );
							settings_fields( $form['id'] );
							do_settings_sections( $form['id'] );
							do_action( 'wsa_form_bottom_' . $form['id'], $form );
							?>
                            <div style="padding-left: 10px">
								<?php submit_button(); ?>
                            </div>
                        </form>
                    </div>
				<?php } ?>
            </div>
			<?php
			$this->script();
		}

		/**
		 * Tabbable JavaScript codes & Initiate Color Picker
		 *
		 * This code uses localstorage for displaying active tabs
		 */
		function script() {
			?>
            <script>
                jQuery( document ).ready( function ( $ ) {
                    //Initiate Color Picker
                    // $('.wp-color-picker-field').wpColorPicker();

                    // Switches option sections
                    $( '.group' ).hide();
                    var activetab = '';
                    if ( typeof ( localStorage ) != 'undefined' ) {
                        activetab = localStorage.getItem( "activetab" );
                    }
                    if ( activetab != '' && $( activetab ).length ) {
                        $( activetab ).fadeIn();
                    } else {
                        $( '.group:first' ).fadeIn();
                    }
                    $( '.group .collapsed' ).each( function () {
                        $( this ).find( 'input:checked' ).parent().parent().parent().nextAll().each(
                            function () {
                                if ( $( this ).hasClass( 'last' ) ) {
                                    $( this ).removeClass( 'hidden' );
                                    return false;
                                }
                                $( this ).filter( '.hidden' ).removeClass( 'hidden' );
                            } );
                    } );

                    if ( activetab != '' && $( activetab + '-tab' ).length ) {
                        $( activetab + '-tab' ).addClass( 'nav-tab-active' );
                    } else {
                        $( '.nav-tab-wrapper a:first' ).addClass( 'nav-tab-active' );
                    }
                    $( '.nav-tab-wrapper a' ).click( function ( evt ) {
                        $( '.nav-tab-wrapper a' ).removeClass( 'nav-tab-active' );
                        $( this ).addClass( 'nav-tab-active' ).blur();
                        var clicked_group = $( this ).attr( 'href' );
                        if ( typeof ( localStorage ) != 'undefined' ) {
                            localStorage.setItem( "activetab", $( this ).attr( 'href' ) );
                        }
                        $( '.group' ).hide();
                        $( clicked_group ).fadeIn();
                        evt.preventDefault();
                    } );

                    $( '.wpsa-browse' ).on( 'click', function ( event ) {
                        event.preventDefault();

                        var self = $( this );

                        // Create the media frame.
                        var file_frame = wp.media.frames.file_frame = wp.media( {
                            title    : self.data( 'uploader_title' ),
                            button   : {
                                text : self.data( 'uploader_button_text' )
                            },
                            multiple : false
                        } );

                        file_frame.on( 'select', function () {
                            attachment = file_frame.state().get( 'selection' ).first().toJSON();

                            self.prev( '.wpsa-url' ).val( attachment.url ).change();
                        } );

                        // Finally, open the modal
                        file_frame.open();
                    } );

                    $( 'input.wpsa-url' ).on( 'change keyup paste input', ( function () {
                        var self = $( this );
                        self.next().parent().children( '.wpsa-image-preview' ).children( 'img' ).attr( 'src', self.val() );
                    } ) ).change();
                } );
            </script>

            <style type="text/css">
                /** WordPress 3.8 Fix **/
                .form-table th {
                    padding: 20px 10px;
                }

                #wpbody-content .metabox-holder {
                    padding-top: 5px;
                }

                .wpsa-image-preview img {
                    height: auto;
                    max-width: 70px;
                }
            </style>
			<?php
		}


	} // WP_OSA ended.

endif;
