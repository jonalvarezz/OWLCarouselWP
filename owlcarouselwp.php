<?php
/**
 * Plugin Name: OWL Carousel WP
 * Plugin URI: http://github.com/jonalvarezz/OWLCarouselWP
 * Description: Touch enabled jQuery plugin that lets you create beautiful responsive carousel slider.
 * Version: 0.1
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
