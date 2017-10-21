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

function bn_create_table_shortcode($stdin, $content="") {
	$trimmed = trim($content);
	$splitted = explode("\n", $trimmed);

	$data = array(); 

	foreach($splitted as $looped) {
		$data[] = explode(",", $looped);
	}

	$table = table($data);

  $content = $table;

  return $content;
}

function table($data) {
  /*
   * Must write on new line as there is an empty array element we need to delete somehow
   * TODO: Escape commas properly, can't just split on comma willy nilly
   */
  $first = (strpos($data[0][0], '<') == 0 ? $data[1] : $data[0]);
	$thead = thead($first);
	$tbody = tbody(array_slice($data, 2));
	$table = "<div class=\"responsive-table\">\n<table class=\"responsive-table__table\">" . $thead . $tbody . "</table>\n</div>\n";
		
	return $table;
}

function row($row , $wrap) {
	$columns = "";
	foreach($row as $column) {
		$columns .= "<" . $wrap . ">" . $column . "</" . $wrap . ">\n";
	}
	
	return $columns; 
}

function thead($row) {
    $ths = row($row, "th");
	$output = "\n<thead>\n<tr>" . $ths . "</tr>\n</thead>\n";
	
	return $output;
}

function rows($rows) {
	$trs = "";
	foreach($rows as $row) {
		$trs .= "<tr>\n" . row($row, "td") . "</tr>\n";
	} 
	
	return $trs;
}

function tbody($rows) {
	$tbs = "<tbody>\n" . rows($rows, "tb") . "</tbody>\n";
	
	return $tbs;
}

