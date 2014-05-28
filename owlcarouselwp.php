<?php
/**
 * Plugin Name: OWL Carousel WP
 * Plugin URI: http://github.com/jonalvarezz/OWLCarouselWP
 * Description: Touch enabled jQuery plugin that lets you create beautiful responsive carousel slider.
 * Version: 0.2
 * Author: Jonathan Álvarez González
 * Author URI: http://jonalvarezz.com
 * Requires at least: 3.8
 * Tested up to: 3.9
 * Licence: GPL2
 *
 * @package OWLCarouselWP
 * @category Core
 * @author jonalvarezz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'OWLCarouselWP' ) ) :



	/**
	 * Main OWLCarouselWP Class
	 *
	 * @class OWLCarouselWP
	 */
	final class OWLCarouselWP {

		/**
		 * @var WooCommerce The single instance of the class
		 * @since 0.2
		 */
		protected static $_instance = null;

		/**
		 * Singleton
		 *
		 * Ensures only one instance of OWLCarouselWP is loaded or can be loaded.
		 *
		 * @since 0.2
		 * @static
		 * @see OWLCWP()
		 * @return OWLCarouselWP - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}


		/**
		 * OWLCarouselWP Constructor.
		 * @access public
		 * @return OWLCarouselWP
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'slide' ), 0 );
		}


		/**
		 * Init function
		 * Create a custom type to hold the slides
		 */
		public function slide() {

			$labels = array(
				'name'                => 'Slides',
				'singular_name'       => 'Slide',
				'menu_name'           => 'Slides',
				'parent_item_colon'   => 'Parent Item:',
				'all_items'           => 'All Slides',
				'view_item'           => 'View Slide',
				'add_new_item'        => 'Add New sllide',
				'add_new'             => 'Add New',
				'edit_item'           => 'Edit Slide',
				'update_item'         => 'Update Slide',
				'search_items'        => 'Search Slide',
				'not_found'           => 'Not found',
				'not_found_in_trash'  => 'Not found in Trash',
			);
			$args = array(
				'label'               => 'owlcarouselwp',
				'description'         => 'OWL Carousel WP',
				'labels'              => $labels,
				'supports'            => array( 'title', 'thumbnail', ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'menu_position'       => 20,
				'menu_icon'           => $this->plugin_url() . '/img/owl-logo-16.png',
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'query_var'           => 'owlcarouselwp',
				'rewrite'             => false,
				'capability_type'     => 'post',
			);
			register_post_type( 'slide', $args );
		}

		/** 
		* ==================================
		* Helper functions
		* ==================================
		*/

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}


	}

endif ;


/**
 * Returns the main instance of OWLCarouselWP to prevent the need to use globals.
 *
 * @since  0.1
 * @return OWLCarouselWP
 */
function OWLCplz() {
	return OWLCarouselWP::instance();
}

OWLCplz();