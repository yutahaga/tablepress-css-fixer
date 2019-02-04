<?php
/**
 * Plugin Name:     Tablepress CSS Fixer
 * Description:     Tablepress の CSS の扱いの問題点を修正します。
 * Author:          Yuta Haga
 * Author URI:      https://github.com/yutahaga
 * Text Domain:     tablepress-fixer
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Tablepress_CSS_Fixer
 */

function tablepress_css_fixer_replace_script( $name, $dependencies = [] ) {
	$suffix = SCRIPT_DEBUG ? '' : '.min';
	$js_file = "admin/js/{$name}{$suffix}.js";
	$js_url = plugins_url( $js_file, __FILE__ );
	wp_dequeue_script( "tablepress-{$name}" );
	wp_enqueue_script( "tablepress-{$name}", $js_url, $dependencies, TablePress::version, true );
}

add_action(
	'admin_init', function () {
		if ( ! class_exists( 'TablePress' ) || ! isset( $_GET['page'] ) || $_GET['page'] !== 'tablepress' || ! isset( $_GET['action'] ) || $_GET['action'] !== 'edit' ) {
			return;
		}

		tablepress_css_fixer_replace_script( 'edit', [ 'jquery-core', 'jquery-ui-sortable', 'json2' ] );
	}, 10
);

add_filter(
	'sanitize_html_class', function ( $sanitized, $class, $fallback = '' ) {
		$sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $class );

		//Limit to A-Z,a-z,0-9,_,:,-
		$sanitized = preg_replace( '/[^A-Za-z0-9_:-]/', '', $sanitized );

		if ( '' == $sanitized && $fallback ) {
			return $fallback;
		}

		return $sanitized;
	}, 10, 3
);
