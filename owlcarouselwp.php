<?php
/**
 * Plugin Name: OWL Carousel WP
 * Plugin URI: http://github.com/jonalvarezz/OWLCarouselWP
 * Description: Touch enabled jQuery plugin that lets you create beautiful responsive carousel slider.
 * Version: 0.3
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

			// Move featured image meta box
			add_action( 'move_image_meta_boxes', array( $this, 'move_image_meta_boxes') );

			// Add new meta boxes
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes') );

			// Save post fields
			add_action( 'save_post', array( $this, 'save' ) );
		}


		/**
		 * Init function
		 * Create a custom type to hold the slides
		 *
		 * @since 0.2
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
		 * Move the featured image meta box
		 *
		 * @since 0.3
		 */
		public function move_image_meta_boxes() {
			remove_meta_box( 'postimagediv', 'slide', 'side' );
			add_meta_box('postimagediv', __('Custom Image'), 'post_thumbnail_meta_box', 'slide', 'normal', 'high');
		}

		/**
		 * Admin Init function
		 * Add meta box to the admin for sliders meta
		 *
		 * @since 0.3
		 */
		public function add_meta_boxes() {
			add_meta_box('link_to_meta', 'Links to', array($this, 'link_to_meta_box_render'), 'slide');
			add_meta_box('image_meta', 'Meta information (SEO)', array($this, 'image_meta_box_render'), 'slide');
		}

		/**
		* Prints the meta box content for the link to meta.
		* 
		* @since 0.3
		*/
		function link_to_meta_box_render() {
			global $post;

			// Add an nonce field to check it later.
			wp_nonce_field( 'link_to_meta_box', 'link_to_meta_box_nonce' );

			/*
			* Use get_post_meta() to retrieve an existing value
			* from the database and use the value for the form.
			*/
			$value_link = get_post_meta( $post->ID, '_owlcwp_link_to_value_key', true );
			$value_new_tab = get_post_meta( $post->ID, '_owlcwp_link_in_new_tab_value_key', true );

			$checked = ($value_new_tab == '') ? '' : 'checked';

			echo '<input type="text" id="owlcwp_link_to" name="owlcwp_link_to" size="60" placeholder="http://" value="' . esc_attr( $value_link ) . '" />';
			echo '<input style="margin-left:50px" type="checkbox" id="owlcwp_link_open_new_tab" name="owlcwp_link_open_new_tab" '. $checked .'></input> <em>Open link in a new tab?</em>';
		}

		/**
		* Prints the meta box content for the image meta information.
		* 
		* @since 0.3
		*/
		function image_meta_box_render() {
			global $post;

			/*
			* Use get_post_meta() to retrieve an existing value
			* from the database and use the value for the form.
			*/
			$value = get_post_meta( $post->ID, '_owlcwp_image_desc_value_key', true );

			echo '<input type="text" placeholder="Image description" id="owlcwp_img_desc" size="90" name="owlcwp_img_desc" value="' . esc_attr( $value ) . '" />';
		}

		/**
		* Save the meta when the post is saved.
		*
		* @param int $post_id The ID of the post being saved. 
		* @since 0.3
		*/
		function save( $post_id ) {
			/*
			 * We need to verify this came from the our screen and with proper authorization,
			 * because save_post can be triggered at other times.
			 */

			// Check if our nonce is set.
			if ( ! isset( $_POST['link_to_meta_box_nonce'] ) )
				return $post_id;

			$nonce = $_POST['link_to_meta_box_nonce'];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'link_to_meta_box' ) )
				return $post_id;

			// If this is an autosave, our form has not been submitted,
	                //     so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				return $post_id;

			// Check the user's permissions.
			if ( 'page' == $_POST['post_type'] ) {

				if ( ! current_user_can( 'edit_page', $post_id ) )
					return $post_id;
		
			} else {

				if ( ! current_user_can( 'edit_post', $post_id ) )
					return $post_id;
			}

			/* OK, its safe for us to save the data now. */

			// Sanitize the user input.
			$link = sanitize_text_field( $_POST['owlcwp_link_to'] );
			$link_new_tab = $_POST['owlcwp_link_open_new_tab'];
			$img_desc = sanitize_text_field( $_POST['owlcwp_img_desc'] );

			// Update the meta field.
			update_post_meta( $post_id, '_owlcwp_link_to_value_key', $link );
			update_post_meta( $post_id, '_owlcwp_link_in_new_tab_value_key', $link_new_tab );
			update_post_meta( $post_id, '_owlcwp_image_desc_value_key', $img_desc );
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