<?php
/**
 * Screens functions.
 *
 * Helper functions related to screens.
 *
 * @package   rootstrap-screens
 * @author    Sky Shabatura
 * @copyright Copyright (c) 2019, Sky Shabatura
 * @link      https://github.com/skyshab/rootstrap
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Rootstrap\Screens;

/**
 * Returns the Rootstrap object instance.
 *
 * @since  1.0.0
 * @access public
 * @return Rootstrap
 */
function rootstrapScreens() {
    return RootstrapScreens::instance();
}

/**
 * Get main Screens instance.
 *
 * @since  1.0.0
 * @access public
 * @return Screens
 */
function screens() {
    return rootstrapScreens()->getScreens();
}

/**
 * Add a screen
 *
 * @since  1.0.0
 * @access public
 * @param  string            $name
 * @param  array             $args
 * @return void
 */
function add_screen( $name, $args ) {
    screens()->add( $name, $args );
}


/**
 * Get a Screen
 *
 * @since  1.0.0
 * @access public
 * @param  string            $name
 * @return void
 */
function get_screen( $name ) {
    return screens()->get( $name );
}


/**
 * Get all Screens
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function get_screens() {
    return screens()->all();
}


/**
 * Get Screens Data
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function get_screens_data() {
    $array = [];
    foreach( get_screens() as $name => $device ) {
        $array[$name]['min'] = $device->min();
        $array[$name]['max'] = $device->max();
    }
    return $array;
}
