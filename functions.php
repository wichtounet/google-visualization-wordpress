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
            'title' => "Graph",
            'id' => "graph_id" + count($graph_ids), //By default give iterated id to the graph
        ), $atts );

    //Register the graph ID
    $graph_ids[] = $options['id'];

    $graph_content = "";

    //Generate the div
    $graph_content .= new_div($options['id'], $options['width'], $options['height']);

    //Generate the Javascript for the graph
    $graph_draw_js = "";

    $graph_draw_js .= '<script type="text/javascript">';
    $graph_draw_js .= 'function draw_' . $options['id'] . '(){';

    $graph_draw_js .= 'var data = google.visualization.arrayToDataTable([';
    $graph_draw_js .= str_replace(array('<br/>', '<br />'), '', $content);
    $graph_draw_js .= ']);';

    $graph_draw_js .= 'new google.visualization.ColumnChart(document.getElementById(\'' . $options['id'] . '\')).';
    $graph_draw_js .= 'draw(data, ';

    $graph_draw_js .= '{title:"' . $options['title'] . '",';
    $graph_draw_js .= 'width:\'' . $options['width'] . '\', height:\'' . $options['height'] . '\',';
    $graph_draw_js .= 'hAxis: {title: "Options"},';
    $graph_draw_js .= 'vAxis: {title: "Seconds", minValue: 0}}';

    $graph_draw_js .= ');';

    $graph_draw_js .= '}';
    $graph_draw_js .= '</script>';

    $graph_content .= $graph_draw_js;

    return $graph_content;
}

function line_chart_shortcode( $atts, $content = null ) {
    return "Unimplemented";
}

function load_graphs_js($content) {
    //use global variables
    global $graph_ids;

    if(is_single()) {
        $graph_draw_js = "";
        
        $graph_draw_js .= '<script type="text/javascript">';
        $graph_draw_js .= 'function draw_visualization(){';

        foreach($graph_ids as $graph){
            $graph_draw_js .= 'draw_' . $graph . '();';
        }

        $graph_draw_js .= '}';
        $graph_draw_js .= 'google.setOnLoadCallback(draw_visualization);';
        $graph_draw_js .= '</script>';

        //Add the graph drawing JS to the content of the post
        $content .= $graph_draw_js;
    }

    return $content;
}

//Add the short codes for the charts
add_shortcode( 'line_chart', 'line_chart_shortcode' );
add_shortcode( 'bar_chart', 'bar_chart_shortcode' );
 
//Add filter to edit the contents of the post
add_filter('the_content', 'load_graphs_js', 1000);

?>
