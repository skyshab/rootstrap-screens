<?php
/**
 * Rootstrap class.
 *
 * This class handles the Rootstrap config data and sets up
 * the individual modules that make up Rootstrap.
 *
 * @package   Rootstrap
 * @author    Sky Shabatura
 * @copyright Copyright (c) 2019, Sky Shabatura
 * @link      https://github.com/skyshab/rootstrap
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Rootstrap\Screens;

use Hybrid\Contracts\Bootable;

/**
 * Creates a new Rootstrap object.
 *
 * @since  1.0.0
 * @access public
 */
class Manager implements Bootable {

    /**
     * Stores Screens object.
     *
     * @since 1.0.0
     * @var array
     */
    private $screens;

    /**
     * Store default vendor path on instantiation.
     *
     * @since 1.0.0
     * @param object $vendor_path
     * @return void
     */
    public function __construct($devices = []) {

        $screensArray = $this->generateScreens($devices);

        $screens = new Screens;

        // Create screens from devices
        foreach( $screensArray as $screen => $args ) {
            $screens->add( $screen, $args );
        }

        $this->screens = $screens->all();
    }


    /**
     * Load resources.
     *
     * @since 1.0.0
     * @return object
     */
    public function boot() {
        // Add js data to customizer page
        add_filter( 'rootstrap/customize-controls/data', [ $this, 'getScreensData' ] );
    }

    /**
     * Generate screens from devices.
     *
     * @since  1.0.0
     * @access public
     * @return array  returns array of screens
     */
    private function generateScreens($devices) {

        $screensArray = [ 'default' => [] ];

        // 'and up' screens loop
        foreach ( $devices as $name => $device ) {

            $min = $device->min();
            $max = $device->max();

            if( $min && $max ) $id  = sprintf( '%s-and-up', $name );
            elseif( $min ) $id  = $name;
            else continue;

            $screensArray[$id]['min'] = $min;
        }

        // 'and under' screens loop
        foreach ( $devices as $name => $device ) {

            $min = $device->min();
            $max = $device->max();

            if( $min && $max ) $id  = sprintf( '%s-and-under', $name );
            elseif( $max ) $id  = $name;
            else continue;

            $screensArray[$id]['max'] = $max;
        }

        // generate all possible screen combinations that have both a min and max
        foreach ( $devices as $outer_name => $outer_device ) {

            $outer_min = ( $outer_device->min() && '' !== $outer_device->min() ) ? $outer_device->min() : false;

            if( $outer_min ) {

                foreach ( $devices as $inner_name => $inner_device ) {

                    $inner_min = $inner_device->min();
                    $inner_max = $inner_device->max();

                    if( !$inner_max ) continue;

                    $outer_min_value = filter_var( $outer_min, FILTER_SANITIZE_NUMBER_INT );
                    $inner_min_value = filter_var( $inner_min, FILTER_SANITIZE_NUMBER_INT );
                    $inner_max_value = filter_var( $inner_max, FILTER_SANITIZE_NUMBER_INT );

                    if( $outer_min_value <= $inner_min_value && $outer_min_value < $inner_max_value ) {

                        $id = ( $outer_name === $inner_name ) ? $outer_name : sprintf( '%s-%s', $outer_name, $inner_name );
                        $screensArray[$id]['min'] = $outer_min;
                        $screensArray[$id]['max'] = $inner_max;

                    } // end if max

                } // end inner loop

            } // end if min

        } // end outer loop

        return $screensArray;
    }

    /**
     * Get Screens Object
     *
     * @since 1.0.0
     * @return void
     */
    public function getScreens() {
        return $this->screens;
    }

    /**
     * Get Screens Data
     *
     * @since  1.0.0
     * @access public
     * @return array
     */
    function getScreensData() {
        $array = [];
        foreach( $this->getScreens() as $name => $device ) {
            $array[$name]['min'] = $device->min();
            $array[$name]['max'] = $device->max();
        }
        return $array;
    }
}
