<?php
/**
 * Plugin Name: CA Youtube Playlist
 * Description: Adds the <code>ca_youtube_playlist</code> shortcode. Specify the <code>playlist</code> attribute (required). Optionally set the height of the display via the <code>height</code> attribute.
 * Plugin URI:  https://github.com/ajatamayo/Youtube-TV
 * Version:     1.0
 * Author:      AJ Tamayo
 * Author URI:  https://github.com/ajatamayo
 * License:     GPL
 * Text Domain: ca-youtube-playlist
 * Domain Path: /languages
 *
 */

add_action( 'plugins_loaded', array( CA_Youtube_Playlist::get_instance(), 'plugin_setup' ) );

class CA_Youtube_Playlist {
    protected static $instance = NULL;
    public $plugin_url = '';
    public $plugin_path = '';
    public $option_name = 'ca_youtube_playlist';
    public $default_options = array(
        'api_key' => '',
    );

    /**
     *
     * @since 1.0
     */
    public function __construct() {}

    /**
     *
     * @since 1.0
     */
    public function load_language( $domain ) {
        load_plugin_textdomain(
            $domain,
            FALSE,
            $this->plugin_path . '/languages'
        );
    }

    /**
     *
     * @since 1.0
     */
    public static function get_instance() {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }

    /**
     *
     * @since 1.0
     */
    public function plugin_setup() {
        $this->plugin_url    = plugins_url( '/', __FILE__ );
        $this->plugin_path   = plugin_dir_path( __FILE__ );
        $this->load_language( 'ca-youtube-playlist' );

        // Settings
        add_action( 'admin_menu', array( &$this, 'add_admin_menu_page' ), 10 );
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'add_action_links' ) );

        // Register shortcode
        add_shortcode( 'ca_youtube_playlist', array( &$this, 'shortcode' ) );
    }

    /**
     *
     * @since 1.0
     */
    function add_admin_menu_page() {
        add_menu_page(
            __( 'Youtube Playlist', 'ca-youtube-playlist' ),
            __( 'Youtube Playlist', 'ca-youtube-playlist' ),
            'administrator',
            'ca-youtube-playlist',
            array( &$this, 'settings_page' ),
            'dashicons-admin-generic'
        );
    }

    /**
     *
     * @since 1.0
     */
    function settings_page() {
        $settings = get_option( $this->option_name, $this->default_options );

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $settings['api_key'] = stripcslashes( $_POST['ca_youtube_playlist_api_key'] );
            update_option( $this->option_name, $settings );
        }

        ?>
        <div class="wrap">

            <h1><?php _e( 'Youtube Playlist', 'ca-youtube-playlist' ); ?></h1>
            <p><?php _e( 'Adds the <code>ca_youtube_playlist</code> shortcode. Specify the <code>playlist</code> attribute (required). Optionally set the height of the display via the <code>height</code> attribute.', 'ca-youtube-playlist' ); ?></p>

            <form method="POST">
                <table class="widefat striped" style="max-width: 500px;">
                    <tbody>
                        <tr>
                            <td><label for="ca_youtube_playlist_api_key"><?php _e( 'Youtube API Key:', 'ca-countdown-lightbox' ); ?></label></td>
                            <td>
                                <input type="text" name="ca_youtube_playlist_api_key" value="<?php echo $settings['api_key']; ?>" style="width: 100%;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" value="<?php _e( 'Save', 'ca-youtube-playlist' ); ?>" class="button button-primary"></td>
                        </tr>
                    </tbody>
                </table>
            </form>

        </div>
        <?php
    }

    /**
     *
     * @since 1.0
     */
    function add_action_links( $links ) {
        $mylinks = array(
            '<a href="' . admin_url( 'admin.php?page=ca-youtube-playlist' ) . '">' . __( 'Settings', 'ca-youtube-playlist' ) . '</a>'
        );
        return array_merge( $links, $mylinks );
    }

    /**
     *
     * @since 1.0
     */
    function shortcode( $atts ) {
        wp_enqueue_style( 'ca-youtube-playlist', $this->plugin_url . '/src/ytv.css', array(), '1.0' );
        wp_enqueue_script( 'ca-youtube-playlist', $this->plugin_url . '/src/ytv.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'ca-youtube-playlist-frontend', $this->plugin_url . '/frontend.js', array( 'jquery', 'ca-youtube-playlist' ), '1.0', true );
        $id = 'ytpl-' . uniqid();
        $settings = get_option( $this->option_name, $this->default_options );

        $atts = shortcode_atts( array(
            'height' => 400,
            'playlist' => '',
        ), $atts );

        ob_start();
        ?>

        <div id="<?php echo $id; ?>" class="ca-youtube-playlist" data-api_key="<?php echo $settings['api_key']; ?>" data-playlist="<?php echo $atts['playlist']; ?>" style="height: <?php echo $height; ?>px;"></div>

        <?php return ob_get_clean();
    }
}

?>