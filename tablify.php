<?php
/**
 * Plugin Name: Blog Network Responsive Tables
 * Plugin URI: https://onemallgroup.com
 * Description: The real effin way to do responsive tables
 * Author: Mariam Fayad and Ryan Morton
 * Author URI: https://www.thenerdscribe.com
 * Version: 0.1
 * Text Domain: bn-responsive-tables
 *
 * Copyright 2017
 *
 * @package editorial-ids
 * @author Ryan Morton
 * @version 0.1
 */

// Prevent direct file access
if( ! defined( 'ABSPATH' ) ) {
  die();
}

function bebug($var, $dump=True) {
  echo "<pre style=\"background: #444;\"><code>";
  if ($dump) {
    var_dump($var);
  } else {
    echo $var;
  }
  echo "</code></pre>";
}

// Register style sheet.
add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );

/**
 * Register style sheet.
 */
function register_plugin_styles() {
	wp_register_style( 'responsive-tables', plugins_url( 'bn_responsive_tables/css/responsive-tables.css' ) );
	wp_enqueue_style( 'responsive-tables' );
}

add_shortcode('responsive_table', bn_create_table_shortcode);

function bn_create_table_shortcode($atts, $content="") {
  $atts = shortcode_atts( array(
    'centered' => '',
  ), $atts, 'bn_create_table_shortcode' );

  $centered_cols = str_split($atts['centered']);
  $trimmed = trim($content);
  $splitted = explode("\n", $trimmed);
  $escaped_commas = str_replace('\,', '\$', $splitted);
  $data = array(); 

	foreach($escaped_commas as $looped) {
		$data[] = explode(",", $looped);
	}

  $table = table($data, $centered_cols);
  $content = $table;

  return $content;
}

function table($data, $centered) {
  /*
   * Must write on new line as there is an empty array element we need to delete somehow
   * TODO: Escape commas properly, can't just split on comma willy nilly
   */
  $first = (strpos($data[0][0], '<') == 0 ? $data[1] : $data[0]);
  $thead = thead($first, $centered);
  $tbody = tbody(array_slice($data, 2), $centered);
  $table = "<div class=\"responsive-table\">\n<table class=\"responsive-table__table\" role=\"grid\">" . $thead . $tbody . "</table>\n</div>\n";

  return $table;
}

function row($row , $wrap, $centered) {
  $columns = "";
  $rowCount = 1;
  foreach($row as $column) {
    $center_class = ($centered && in_array($rowCount, $centered) ? ' class="col-centered"' : '');
    $replaced_column = str_replace('\$', ',', $column);
    $columns .= "<" . $wrap . $center_class . ">" . $replaced_column . "</" . $wrap . ">\n";
    $rowCount++;
  }

  return $columns; 
}

function thead($row, $centered) {
  $ths = row($row, "th", $centered);
  $output = "\n<thead role=\"grid\">\n<tr role=\"row\">" . $ths . "</tr>\n</thead>\n";

  return $output;
}

function rows($rows, $centered) {
  $trs = "";
  foreach($rows as $row) {
    $trs .= "<tr role=\"row\">\n" . row($row, "td", $centered) . "</tr>\n";
  } 

  return $trs;
}

function tbody($rows, $centered) {
  $tbs = "<tbody role=\"grid\">\n" . rows($rows, $centered) . "</tbody>\n";

  return $tbs;
}

