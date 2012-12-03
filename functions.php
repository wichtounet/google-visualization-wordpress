<?php
/*
 * Plugin Name: Google Visualization Charts
 * Plugin URI: https://github.com/wichtounet/google-visualization-wordpress 
 * Description: Easy Generation of Google Visualization Charts in Wordpress.
 * Version: 0.1 
 * Author: Baptiste Wicht
 * Author URI: http://www.baptiste-wicht.com
 * License: GPL2
*/

/*  Copyright 2012 Baptiste Wicht (email: baptiste.wicht@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function add_visualization_js() {
    echo '<script type="text/javascript" src="http://www.google.com/jsapi"></script>';
    echo '<script type="text/javascript">';
    echo 'google.load(\'visualization\', \'1\', {packages: [\'corechart\']});';
    echo '</script>';
}

// Add hook for front-end <head></head>
add_action('wp_head', 'add_visualization_js');

// Store the IDs of the generated graphs
$graph_ids = array();

// Create a DIV placeholder for the Visualization API
function new_div( $id, $width, $height) {
    return "<div id=\"" . $id . "\" style=\"width: " . $width . "; height: " . $height . ";\"></div>";
}

// Generate a bar chart
function bar_chart_shortcode( $atts, $content = null ) {
    //use global variables
    global $graph_ids;

    $options = shortcode_atts( array(
            'width' => "400px",
            'height' => "300px",
            'id' => "graph_id" + count($graph_ids), //By default give iterated id to the graph
        ), $atts );

    //Register the graph ID
    $graph_ids[] = $options['id'];

    $graph_content = "";

    //Generate the div
    $graph_content = $graph_content . new_div($options['id'], $options['width'], $options['height']);

    return $graph_content;
}

function line_chart_shortcode( $atts, $content = null ) {
    return "Unimplemented";
}

//Add the short codes for the charts
add_shortcode( 'line_chart', 'line_chart_shortcode' );
add_shortcode( 'bar_chart', 'bar_chart_shortcode' );

?>
