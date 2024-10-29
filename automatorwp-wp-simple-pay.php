<?php
/**
 * Plugin Name:           AutomatorWP - WP Simple Pay integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-wp-simple-pay-integration/
 * Description:           Connect AutomatorWP with WP Simple Pay.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wp-simple-pay-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.9
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WP_Simple_Pay
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_WP_Simple_Pay_Integration {

    /**
     * @var         AutomatorWP_WP_Simple_Pay_Integration $instance The one true AutomatorWP_WP_Simple_Pay_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_WP_Simple_Pay_Integration self::$instance The one true AutomatorWP_WP_Simple_Pay_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_WP_Simple_Pay_Integration();

            if( ! self::$instance->pro_installed() ) {

                self::$instance->constants();
                self::$instance->includes();
                self::$instance->load_textdomain();

            }

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_WP_SIMPLE_PAY_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_WP_SIMPLE_PAY_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WP_SIMPLE_PAY_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WP_SIMPLE_PAY_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() && ! $this->pro_installed()  ) {

            if( SIMPLE_PAY_PLUGIN_NAME === 'WP Simple Pay Pro' ) {

                // Triggers
                require_once AUTOMATORWP_WP_SIMPLE_PAY_DIR . 'includes/triggers/complete-purchase.php';

            } else {

                // Lite Triggers
                require_once AUTOMATORWP_WP_SIMPLE_PAY_DIR . 'includes/triggers/complete-purchase-lite.php';

            }

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'wp_simple_pay', array(
            'label' => 'WP Simple Pay',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wp-simple-pay.svg',
        ) );

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'AutomatorWP - WP Simple Pay requires %s and %s in order to work. Please install and activate them.', 'automatorwp-wp-simple-pay-integration' ),
                        '<a href="https://wordpress.org/plugins/automatorwp/" target="_blank">AutomatorWP</a>',
                        '<a href="https://wordpress.org/plugins/stripe/" target="_blank">WP Simple Pay</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php elseif ( $this->pro_installed() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php echo __( 'You can uninstall AutomatorWP - WP Simple Pay Integration because you already have the pro version installed and includes all the features of the free version.', 'automatorwp-wp-simple-pay-integration' ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! defined( 'SIMPLE_PAY_PLUGIN_NAME' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_WP_Simple_Pay' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = AUTOMATORWP_WP_SIMPLE_PAY_DIR . '/languages/';
        $lang_dir = apply_filters( 'automatorwp_wp_simple_pay_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'automatorwp-wp-simple-pay-integration' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'automatorwp-wp-simple-pay-integration', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/automatorwp-wp-simple-pay-integration/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/automatorwp-wp-simple-pay-integration/ folder
            load_textdomain( 'automatorwp-wp-simple-pay-integration', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/automatorwp-wp-simple-pay-integration/languages/ folder
            load_textdomain( 'automatorwp-wp-simple-pay-integration', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'automatorwp-wp-simple-pay-integration', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_WP_Simple_Pay_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_WP_Simple_Pay_Integration The one true AutomatorWP_WP_Simple_Pay_Integration
 */
function AutomatorWP_WP_Simple_Pay_Integration() {
    return AutomatorWP_WP_Simple_Pay_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_WP_Simple_Pay_Integration' );
