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
use function Rootstrap\Devices\get_devices;

/**
 * Creates a new Rootstrap object.
 *
 * @since  1.0.0
 * @access public
 */
class RootstrapScreens extends Bootable {

    /**
     * Stores Screens object.
     *
     * @since 1.0.0
     * @var array
     */
    private $screens;

    /**
     * Load resources.
     *
     * @since 1.0.0
     * @return object
     */
    public function boot() {
        // Initiate Core Module
        add_action( 'rootstrap/loaded', [ $this, 'init' ] );
        // Add js data to customizer page
        add_filter( 'rootstrap/customize-controls/data', [ $this, 'js_data' ] );
    }

    /**
     * Load Rootstrap Modules when required
     *
     * @since 1.0.0
     * @return void
     */
    private function init() {

        $screensArray = [ 'default' => [] ];
        $devices = get_devices();

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

        $screens = new Screens;

        // Create screens from devices
        foreach( $screensArray as $screen => $args ) {
            $screens->add( $screen, $args );
        }

        // action hook for plugins and child themes to add or remove screens
        do_action( 'rootstrap/register/screens', $screens );

        $this->screens = $screens;
    }

    /**
     * Get data to make available to JS.
     *
     * @since  1.0.0
     * @access public
     * @return array  returns array of js data
     */
    public function js_data($data) {
        $data['screens'] = get_screens_data();
        return $data;
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
}
