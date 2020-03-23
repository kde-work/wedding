<?php
function wedbuilder_shortcode( $atts, $content = null) {
	$atts = shortcode_atts( array(
		'default_id' => 1,
		'default' => '',
		'tab_id' => 1,
		'type' => '',
		'class' => '',
		'name' => '',
	), $atts );

	$user_id = get_user_id_by_current_url();

	if ( $user_id > 0 ) {
		$data = get_user_meta( $user_id, 'wpb_save', 1 );
		$atts['name'] = WEDB_Components::clear_name( $atts['name'] );
		$item = $data["{$atts['name']}-{$atts['tab_id']}"];

		// if user date exist
		if ( isset( $item ) AND $item AND $item != 'default' ) {
			return apply_filters( 'wedbuilder_content', $item, $atts['name'], $atts, true );
		} else {

			// default data from template
			if ( $atts['default'] ) {
				return apply_filters( 'wedbuilder_content', $atts['default'], $atts['name'], $atts, false );
			}

			// default data from WEDB_Default_Values()
			return (new WEDB_Default_Values(true))->get_value( $atts['default_id'] );
		}
	}
	return '';
}
add_shortcode( 'wedbuilder', 'wedbuilder_shortcode' );

function wedbuilder_tabs_shortcode( $atts ) {
	$atts = shortcode_atts( array(
	), $atts );

	$user_id = get_user_id_by_current_url();

	if ( $user_id > 0 ) {
		$data = get_user_meta( $user_id, 'wpb_save', 1 );

		$class = '';
		foreach ( $data as $key => $datum ) {
			if ( strpos( $key, 'enabled_tab' ) !== false AND $datum != 'enable' ) {
				$class .= ".$key,";
			}
		}
		$class = substr( $class, 0, -1 );
		return "<style>$class{display: none !important;}</style>";
	}
	return '';
}
add_shortcode( 'wedbuilder-tabs', 'wedbuilder_tabs_shortcode' );

add_filter( 'the_content', 'filter_content_wedbuilder_early', 1 );
function filter_content_wedbuilder_early( $content ) {
	if ( strpos( $content, '%wedbuilder' ) !== false ) {
		$content = preg_replace_callback(
			'/\%(wedbuilder .*)\%/mU',
			function ($matches) {
				return do_shortcode( "[$matches[1]]" );
			},
			$content
		);
	}
	return $content;
}

function get_user_id_by_current_url() {
	global $wpdb;

	$post_title = wp_parse_url( ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], PHP_URL_PATH );
	$post_title = trim( $post_title, "/" );
//	$post_title = array_pop( explode( "/", trim( $post_title, "/" ) ) );
	if ( strpos( $post_title, '/' ) !== false ) {
		$post_title = explode( "/", $post_title );
		$post_title = array_pop( $post_title );
	}
	return $wpdb->get_var( $wpdb->prepare(
			"SELECT `post_author` FROM `$wpdb->posts`
				 WHERE `post_name`='%s'
		        ",
		$post_title
		)
	);
}

add_filter( 'the_content', 'filter_content_wedbuilder', 100 );
function filter_content_wedbuilder( $content ) {
	return ( strpos( $content, 'wedbuilder' ) !== false ) ? do_shortcode( $content ) : $content;
}

// Fix map width
add_filter( 'wedbuilder_content', 'filter_wedbuilder_map', 10, 2 );
function filter_wedbuilder_map( $content, $name ) {
	if ( $name == 'map_embed_iframe' ) {
		return str_replace( '"600"', '"100%"', $content );
	}
	return $content;
}

// Return image url if type 'url'
add_filter( 'wedbuilder_content', 'wedbuilder_type_filter', 10, 3 );
function wedbuilder_type_filter( $content, $name, $atts ) {
	if ( $atts['type'] == 'url' ) {
		return wp_get_attachment_image_src( $content, ( $atts['size'] ) ? $atts['type'] : 'full', true )[0];
	}
	if ( $atts['type'] == 'css-image' ) {
		return "<style>{$atts['class']}{ background-image: url(" . wp_get_attachment_image_src( $content, ( $atts['size'] ) ? $atts['type'] : 'full', true )[0] . ");}</style>";
	}
	return $content;
}