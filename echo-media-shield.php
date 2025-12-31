<?php
/**
 * Plugin Name: Echo Media Shield
 * Plugin URI: https://github.com/ogichanchan/echo-media-shield
 * Description: A unique PHP-only WordPress utility. A echo style media plugin acting as a shield. Focused on simplicity and efficiency.
 * Version: 1.0.0
 * Author: ogichanchan
 * Author URI: https://github.com/ogichanchan
 * License: GPLv2 or later
 * Text Domain: echo-media-shield
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class for Echo Media Shield.
 *
 * This class encapsulates all plugin functionality, including admin settings,
 * shortcode handling, and inline styling, adhering to the single-file,
 * PHP-only, and no-external-files requirements.
 */
class Echo_Media_Shield {

    /**
     * Option group name for WordPress Settings API.
     *
     * @var string
     */
    const OPTION_GROUP = 'echo_media_shield_options';

    /**
     * Stores the plugin's settings.
     *
     * @var array
     */
    private $options;

    /**
     * Constructor for the Echo_Media_Shield class.
     *
     * Initializes hooks for admin menu, settings, shortcode, and frontend styles.
     */
    public function __construct() {
        // Load stored options, or an empty array if none exist.
        $this->options = get_option( self::OPTION_GROUP, array() );

        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_shortcode( 'echo_media', array( $this, 'echo_media_shortcode' ) );
        add_action( 'wp_head', array( $this, 'add_inline_styles' ) );
    }

    /**
     * Adds the plugin's settings page to the WordPress admin menu under 'Settings'.
     */
    public function add_admin_menu() {
        add_options_page(
            esc_html__( 'Echo Media Shield Settings', 'echo-media-shield' ), // Page title
            esc_html__( 'Echo Media Shield', 'echo-media-shield' ),          // Menu title
            'manage_options',                                               // Capability required to access
            'echo-media-shield',                                            // Menu slug
            array( $this, 'settings_page_html' )                            // Callback function to render page
        );
    }

    /**
     * Registers plugin settings with the WordPress Settings API.
     */
    public function register_settings() {
        register_setting(
            self::OPTION_GROUP,                                  // Option group name
            self::OPTION_GROUP,                                  // Option name (same as group for single option)
            array( $this, 'sanitize_options' )                   // Sanitization callback
        );

        add_settings_section(
            'echo_media_shield_main_section',                   // Section ID
            esc_html__( 'General Shield Settings', 'echo-media-shield' ), // Section title
            array( $this, 'main_settings_section_callback' ),   // Callback to render section content
            'echo-media-shield'                                 // Page slug
        );

        // Add settings fields.
        add_settings_field(
            'shield_enabled',
            esc_html__( 'Enable Shield', 'echo-media-shield' ),
            array( $this, 'shield_enabled_callback' ),
            'echo-media-shield',
            'echo_media_shield_main_section'
        );

        add_settings_field(
            'shield_border_color',
            esc_html__( 'Border Color', 'echo-media-shield' ),
            array( $this, 'shield_border_color_callback' ),
            'echo-media-shield',
            'echo_media_shield_main_section'
        );

        add_settings_field(
            'shield_overlay_text',
            esc_html__( 'Overlay Text (on hover)', 'echo-media-shield' ),
            array( $this, 'shield_overlay_text_callback' ),
            'echo-media-shield',
            'echo_media_shield_main_section'
        );

        add_settings_field(
            'shield_overlay_bg',
            esc_html__( 'Overlay Background Color', 'echo-media-shield' ),
            array( $this, 'shield_overlay_bg_callback' ),
            'echo-media-shield',
            'echo_media_shield_main_section'
        );

        add_settings_field(
            'shield_overlay_text_color',
            esc_html__( 'Overlay Text Color', 'echo-media-shield' ),
            array( $this, 'shield_overlay_text_color_callback' ),
            'echo-media-shield',
            'echo_media_shield_main_section'
        );
    }

    /**
     * Sanitizes plugin options submitted from the settings page.
     *
     * @param array $input The raw input array from the settings form.
     * @return array The sanitized options array.
     */
    public function sanitize_options( $input ) {
        $sanitized_input = array();

        $sanitized_input['shield_enabled']      = isset( $input['shield_enabled'] ) ? (bool) $input['shield_enabled'] : false;
        $sanitized_input['shield_border_color'] = sanitize_hex_color( $input['shield_border_color'] );
        $sanitized_input['shield_overlay_text'] = sanitize_text_field( $input['shield_overlay_text'] );
        // Allow RGBA for overlay background, sanitize as text. Browser handles invalid CSS.
        $sanitized_input['shield_overlay_bg']   = sanitize_text_field( $input['shield_overlay_bg'] );
        $sanitized_input['shield_overlay_text_color'] = sanitize_hex_color( $input['shield_overlay_text_color'] );

        return $sanitized_input;
    }

    /**
     * Renders introductory text for the main settings section.
     */
    public function main_settings_section_callback() {
        echo '<p>' . esc_html__( 'Configure the visual shield for images embedded with the [echo_media] shortcode.', 'echo-media-shield' ) . '</p>';
    }

    /**
     * Renders the checkbox field for enabling/disabling the shield.
     */
    public function shield_enabled_callback() {
        $enabled = isset( $this->options['shield_enabled'] ) ? (bool) $this->options['shield_enabled'] : false;
        ?>
        <label for="shield_enabled">
            <input type="checkbox" id="shield_enabled" name="<?php echo esc_attr( self::OPTION_GROUP ); ?>[shield_enabled]" value="1" <?php checked( $enabled, true ); ?>>
            <?php esc_html_e( 'Activate the custom shield styling for [echo_media] shortcode.', 'echo-media-shield' ); ?>
        </label>
        <?php
    }

    /**
     * Renders the text input field for the shield border color.
     */
    public function shield_border_color_callback() {
        $color = isset( $this->options['shield_border_color'] ) ? $this->options['shield_border_color'] : '#0073AA';
        echo '<input type="text" id="shield_border_color" name="' . esc_attr( self::OPTION_GROUP ) . '[shield_border_color]" value="' . esc_attr( $color ) . '" class="regular-text" placeholder="#RRGGBB" />';
        echo '<p class="description">' . esc_html__( 'Enter a hex color code (e.g., #FF0000).', 'echo-media-shield' ) . '</p>';
    }

    /**
     * Renders the text input field for the overlay text.
     */
    public function shield_overlay_text_callback() {
        $text = isset( $this->options['shield_overlay_text'] ) ? $this->options['shield_overlay_text'] : esc_html__( 'Shielded Media', 'echo-media-shield' );
        echo '<input type="text" id="shield_overlay_text" name="' . esc_attr( self::OPTION_GROUP ) . '[shield_overlay_text]" value="' . esc_attr( $text ) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__( 'Text that appears on hover over the shielded media.', 'echo-media-shield' ) . '</p>';
    }

    /**
     * Renders the text input field for the overlay background color.
     */
    public function shield_overlay_bg_callback() {
        $color = isset( $this->options['shield_overlay_bg'] ) ? $this->options['shield_overlay_bg'] : 'rgba(0, 0, 0, 0.7)';
        echo '<input type="text" id="shield_overlay_bg" name="' . esc_attr( self::OPTION_GROUP ) . '[shield_overlay_bg]" value="' . esc_attr( $color ) . '" class="regular-text" placeholder="rgba(0,0,0,0.7) or #RRGGBB" />';
        echo '<p class="description">' . esc_html__( 'Enter a CSS color (hex, rgb, rgba). For transparency, use rgba().', 'echo-media-shield' ) . '</p>';
    }

    /**
     * Renders the text input field for the overlay text color.
     */
    public function shield_overlay_text_color_callback() {
        $color = isset( $this->options['shield_overlay_text_color'] ) ? $this->options['shield_overlay_text_color'] : '#FFFFFF';
        echo '<input type="text" id="shield_overlay_text_color" name="' . esc_attr( self::OPTION_GROUP ) . '[shield_overlay_text_color]" value="' . esc_attr( $color ) . '" class="regular-text" placeholder="#RRGGBB" />';
        echo '<p class="description">' . esc_html__( 'Enter a hex color code (e.g., #FFFFFF).', 'echo-media-shield' ) . '</p>';
    }

    /**
     * Renders the HTML for the plugin's settings page.
     */
    public function settings_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( self::OPTION_GROUP );      // Output security fields for the registered settings group
                do_settings_sections( 'echo-media-shield' ); // Output settings sections and fields for the page
                submit_button( esc_html__( 'Save Settings', 'echo-media-shield' ) ); // Output the save button
                ?>
            </form>
        </div>
        <?php
        // No inline scripts or styles specific to admin page beyond basic HTML inputs,
        // as per "PHP ONLY" and "NO EXTERNAL FILES" rules.
    }

    /**
     * Handles the `[echo_media]` shortcode to display shielded media.
     *
     * Supports `id` (media attachment ID) or `url` (direct image URL).
     *
     * @param array $atts Shortcode attributes.
     * @return string The HTML output for the shielded media.
     */
    public function echo_media_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'id'  => 0,
                'url' => '',
            ),
            $atts,
            'echo_media'
        );

        $image_url = '';
        if ( ! empty( $atts['id'] ) ) {
            $image_url = wp_get_attachment_url( (int) $atts['id'] );
        } elseif ( ! empty( $atts['url'] ) ) {
            $image_url = esc_url_raw( $atts['url'] );
        }

        if ( empty( $image_url ) ) {
            return '<p class="echo-media-shield-error">' . esc_html__( 'Error: Media ID or URL not provided for [echo_media] shortcode.', 'echo-media-shield' ) . '</p>';
        }

        // Retrieve plugin options with fallback defaults.
        $enabled         = isset( $this->options['shield_enabled'] ) ? (bool) $this->options['shield_enabled'] : false;
        $border_color    = isset( $this->options['shield_border_color'] ) ? $this->options['shield_border_color'] : '#0073AA';
        $overlay_text    = isset( $this->options['shield_overlay_text'] ) ? $this->options['shield_overlay_text'] : esc_html__( 'Shielded Media', 'echo-media-shield' );
        $overlay_bg      = isset( $this->options['shield_overlay_bg'] ) ? $this->options['shield_overlay_bg'] : 'rgba(0, 0, 0, 0.7)';
        $overlay_text_color = isset( $this->options['shield_overlay_text_color'] ) ? $this->options['shield_overlay_text_color'] : '#FFFFFF';

        $output = '';

        if ( $enabled ) {
            // Apply shield styling if enabled.
            $output .= '<div class="echo-media-shield-container" style="border: 2px solid ' . esc_attr( $border_color ) . '; position: relative; display: inline-block; overflow: hidden; max-width: 100%;">';
            $output .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $overlay_text ) . '" class="echo-media-shield-image" style="display: block; max-width: 100%; height: auto; transition: transform 0.3s ease-in-out;" />';
            $output .= '<div class="echo-media-shield-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: ' . esc_attr( $overlay_bg ) . '; color: ' . esc_attr( $overlay_text_color ) . '; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease-in-out; pointer-events: none;">';
            $output .= '<span class="echo-media-shield-overlay-text" style="text-align: center; padding: 10px; font-size: 1.2em; font-weight: bold;">' . esc_html( $overlay_text ) . '</span>';
            $output .= '</div>';
            $output .= '</div>';
        } else {
            // If shield is disabled, just output the raw image.
            $output .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $overlay_text ) . '" class="echo-media-shield-image" style="max-width: 100%; height: auto;" />';
        }

        return $output;
    }

    /**
     * Adds inline CSS styles to the site's <head> section for the shielded media.
     * These styles enable the hover effects.
     */
    public function add_inline_styles() {
        // Only output styles if the shield feature is enabled in settings.
        $enabled = isset( $this->options['shield_enabled'] ) ? (bool) $this->options['shield_enabled'] : false;
        if ( ! $enabled ) {
            return;
        }
        ?>
        <style type="text/css">
            .echo-media-shield-container:hover .echo-media-shield-overlay {
                opacity: 1 !important; /* Ensure overlay becomes visible on hover */
                pointer-events: auto !important; /* Allow interaction with overlay content if any */
            }
            .echo-media-shield-container:hover .echo-media-shield-image {
                transform: scale(1.05); /* Slight zoom effect on image on hover */
            }
            .echo-media-shield-error {
                color: red;
                font-weight: bold;
            }
        </style>
        <?php
    }

    /**
     * Static method to handle plugin activation.
     *
     * Sets default options if they don't already exist.
     */
    public static function activate() {
        $default_options = array(
            'shield_enabled'      => true,
            'shield_border_color' => '#0073AA',
            'shield_overlay_text' => esc_html__( 'Shielded Media', 'echo-media-shield' ),
            'shield_overlay_bg'   => 'rgba(0, 0, 0, 0.7)',
            'shield_overlay_text_color' => '#FFFFFF',
        );
        // Only add options if they don't exist, preserving user settings on re-activation.
        add_option( self::OPTION_GROUP, $default_options );
    }

    /**
     * Static method to handle plugin deactivation.
     *
     * Currently, it does not delete options, allowing settings to persist
     * if the plugin is reactivated. Uncomment `delete_option` if full cleanup
     * on deactivation is desired.
     */
    public static function deactivate() {
        // delete_option( self::OPTION_GROUP ); // Uncomment to remove settings on deactivation.
    }
}

// Register activation and deactivation hooks for the plugin.
register_activation_hook( __FILE__, array( 'Echo_Media_Shield', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Echo_Media_Shield', 'deactivate' ) );

// Initialize the plugin class to start its functionality.
new Echo_Media_Shield();