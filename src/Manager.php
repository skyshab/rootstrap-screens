<?php
/**
 * Rootstrap Screens.
 *
 * This class handles responsive breakpoints used in theme styles.
 *
 * @package   Rootstrap/Screens
 * @author    Sky Shabatura
 * @copyright Copyright (c) 2019, Sky Shabatura
 * @link      https://github.com/skyshab/rootstrap
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Rootstrap\Screens;

use Rootstrap\Devices\Devices;

/**
 * Creates a new Rootstrap object.
 *
 * @since  1.0.0
 * @access public
 */
class Manager {

    /**
     * Stores Screens Collection.
     *
     * @since 1.0.0
     * @var object
     */
    private $screens;

    /**
     * Generate Screens from Devices on instanstiation.
     *
     * @since 1.0.0
     * @param array $screens - Rootstrap Screens Collection
     * @param array $devices - Devices Collection to generate screens from.
     * @return void
     */
    public function __construct( Devices $devices) {

        // If devices were not passed in, bail.
        if( ! $devices ) return;

        // Create new Screens instance
        $screens = new Screens();

        // Generate array of screens from devices
        $screensArray = $this->generateScreens($devices);

        // Add screens to Collection
        foreach( $screensArray as $screen => $args ) {
            $screens->add( $screen, $args );
        }

        // Store the Screens Collection
        $this->screens = $screens;
    }

    /**
     * Generate screens from devices.
     *
     * @since  1.0.0
     * @access private
     * @param object $devices - a Collection of Devices
     * @return array  returns array of screens
     */
    private function generateScreens( Devices $devices ) {

        // Initiate screens array
        $screens = [ 'default' => [] ];

        // 'and up' screens loop
        foreach ( $devices->all() as $name => $device ) {

            $min = $device->min();
            $max = $device->max();

            if( $min && $max ) $id  = sprintf( '%s-and-up', $name );
            elseif( $min ) $id  = $name;
            else continue;

            $screens[$id]['min'] = $min;
        }

        // 'and under' screens loop
        foreach ( $devices as $name => $device ) {

            $min = $device->min();
            $max = $device->max();

            if( $min && $max ) $id  = sprintf( '%s-and-under', $name );
            elseif( $max ) $id  = $name;
            else continue;

            $screens[$id]['max'] = $max;
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
                        $screens[$id]['min'] = $outer_min;
                        $screens[$id]['max'] = $inner_max;

                    } // end if max

                } // end inner loop

            } // end if min

        } // end outer loop

        return $screens;
    }

    /**
     * Get Screens Collection
     *
     * @since 1.0.0
     * @return void
     */
    public function collection() {
        return $this->screens;
    }
}
