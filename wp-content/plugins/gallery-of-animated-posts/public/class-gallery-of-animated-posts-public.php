<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, hooks for enqueue styles and scripts, and registers the shortcode.
 *
 * @package    Gallery_Of_Animated_Posts
 * @subpackage Gallery_Of_Animated_Posts/public
 * @author     Marcus Hogh <hogh@lenscapades.com>
 */
class Gallery_Of_Animated_Posts_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, get_template_directory_uri().'/css/grid.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * The $handle value needs to be unique. Otherwise only the first script will be loaded.
		 * So $this->plugin_name can only be uses once.
		 */

		wp_enqueue_script( 'gridsby-masonry', 
			get_template_directory_uri().'/js/masonry.pkgd.js', 
			array( 'jquery' ), 
			$this->version, 
			true );
		wp_enqueue_script( 'gridsby-grid3d', 
			get_template_directory_uri().'/js/grid3d.js', 
			array( 'jquery' ), 
			$this->version, 
			true );
		wp_enqueue_script( 'gridsby-gallery', 
			get_template_directory_uri().'/js/gridsby-gallery.js', 
			array( 'jquery' ), 
			$this->version, 
			true );

	}

	/**
	 * Register the shortcode for displaying image galleries.
	 *
	 * @since    1.0.0
	 */
	public function register_shortcode() {

		add_shortcode( 'gallery_of_animated_posts', array( __CLASS__, 'handle_shortcode' ) );

	}

	/**
	 * Handle the shortcode.
	 *
	 * @since    1.0.0
	 * @param    array                $atts            The collection of attributes that is being passed with the shortcode.
	 */
	public static function handle_shortcode( $atts ) {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-gallery-of-animated-posts-public-factory.php';

		$public_factory_obj = new Gallery_Of_Animated_Posts_Public_Factory();

		return $public_factory_obj->get( $atts );

	}

}
