<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WEDB class
 *
 * @class       WEDB
 * @version     0.0.1
 * @category    Admin
 * @author      Dmitry
 */
class WEDB {

	/** @var WEDB_Components $components */
	private $components;

	/** @var array $data */
	private $data;

	/**
	 * Setup class.
	 */
	public function __construct() {
		$this->components = new WEDB_Components();
		$this->data = get_user_meta( wp_get_current_user()->ID, 'wpb_save', 1 );
	}

	/**
	 * Return HTML of Wedding page builder.
	 *
	 * @return string
	 */
	public function get_builder() {
		$v = new WEDB_Default_Values();
		ob_start();
		echo $this->components->init();
			echo $this->components->site_name();
			echo $this->components->init_tabs();
				echo $this->components->tabs_header( 1, 'Main slider', true );
				echo $this->components->tabs_header( 2, 'Our story' );
				echo $this->components->tabs_header( 3, 'About us' );
				echo $this->components->tabs_header( 4, 'The ceremony' );
				echo $this->components->tabs_header( 5, 'Restaurant' );
				echo $this->components->tabs_header( 6, 'How it will be' );
				echo $this->components->tabs_header( 7, 'Contacts' );
				echo $this->components->tabs_header( 8, 'Contact form' );
				echo $this->components->tabs_header( 9, 'Map' );
				echo $this->components->tabs_header( 10, '1' );
				echo $this->components->tabs_header( 11, '2' );
				echo $this->components->tabs_header( 12, '3' );
				echo $this->components->tabs_header( 13, '4' );
				echo $this->components->tabs_header( 14, '5' );
			echo $this->components->tabs_content_begin();
				echo $this->components->tabs_content( 1, true, true );
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'text',
							'name' => 'Names',
							'value' => $v->get_value( 0, $this->data( $this->components->tab(), 'Names' ) ), // first param 'get_value' look WEDB_Default_Values
							'options' => [
								'required' => 1,
								'limited' => 120,
							]
						],[
							'type' => 'text',
							'name' => 'Sub title',
							'value' => $v->get_value( 1, $this->data( $this->components->tab(), 'Sub title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 100,
							]
						],[
							'type' => 'text',
							'name' => 'Date line',
							'value' => $v->get_value( 2, $this->data( $this->components->tab(), 'Date line' ) ),
							'options' => [
								'required' => 1,
								'limited' => 100,
							]
						],[
							'type' => 'text',
							'name' => 'Button text to message form',
							'value' => $v->get_value( 25, $this->data( $this->components->tab(), 'Button text to message form' ) ),
							'options' => [
								'required' => 1,
								'limited' => 100,
							]
						],[
							'type' => 'media upload',
							'name' => 'Background',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 2 );
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'text',
							'name' => 'Title',
							'value' => $v->get_value( 3, $this->data( $this->components->tab(), 'Title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 100,
							]
						],[
							'type' => 'textarea',
							'name' => 'Description',
							'value' => $v->get_value( 21, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
								'limited' => 2000,
								'help' => 'Not used for template 3.',
							]
						],[
							'type' => 'media upload',
							'name' => 'Image',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
							'options' => [
								'required' => 1,
							]
						],[
							'type' => 'media upload',
							'name' => 'Background',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 3 ); // About us
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'media upload',
							'name' => 'Background',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],[
							'type' => 'notes',
							'name' => 'notes 1',
							'options' => [
								'text' => '<hr><h4>THE GROOM</h4>',
							]
						],[
							'type' => 'text',
							'name' => 'Name of the groom',
							'value' => $v->get_value( 4, $this->data( $this->components->tab(), 'Name of the groom' ) ),
							'options' => [
								'required' => 1,
								'limited' => 100,
							]
						],[
							'type' => 'wysiwyg',
							'name' => 'Description of the groom',
							'value' => $v->get_value( 5, $this->data( $this->components->tab(), 'Description of the groom' ) ),
							'options' => [
								'required' => 1,
								'limited' => 2000,
							]
						],[
							'type' => 'media upload',
							'name' => 'Photo of the groom',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Photo of the groom' ) ),
							'options' => [
								'required' => 1,
							]
						],[
							'type' => 'notes',
							'name' => 'notes 2',
							'options' => [
								'text' => '<hr><h4>THE BRIDE</h4>',
							]
						],[
							'type' => 'text',
							'name' => 'Name of the bride',
							'value' => $v->get_value( 6, $this->data( $this->components->tab(), 'Name of the bride' ) ),
							'options' => [
								'required' => 1,
								'limited' => 100,
							]
						],[
							'type' => 'wysiwyg',
							'name' => 'Description of the bride',
							'value' => $v->get_value( 7, $this->data( $this->components->tab(), 'Description of the bride' ) ),
							'options' => [
								'required' => 1,
								'limited' => 2000,
							]
						],[
							'type' => 'media upload',
							'name' => 'Photo of the bride',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Photo of the bride' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 4 ); // The ceremony
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'notes',
							'name' => 'notes 3',
							'options' => [
								'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>(THE CHURCH)</h4></div>',
							]
						],[
							'type' => 'text',
							'name' => 'Title',
							'value' => $v->get_value( 22, $this->data( $this->components->tab(), 'Title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 200,
							]
						],[
							'type' => 'text',
							'name' => 'Sub Title',
							'value' => $v->get_value( 8, $this->data( $this->components->tab(), 'Sub Title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 200,
							]
						],[
							'type' => 'textarea',
							'name' => 'Description',
							'value' => $v->get_value( 9, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
								'limited' => 2000,
							]
						],[
							'type' => 'media upload',
							'name' => 'Image',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 5 );
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'notes',
							'name' => 'notes 3',
							'options' => [
								'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>(THE PARTY)</h4></div>',
							]
						],[
							'type' => 'text',
							'name' => 'Title',
							'value' => $v->get_value( 10, $this->data( $this->components->tab(), 'Title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 200,
							]
						],[
							'type' => 'text',
							'name' => 'Sub Title',
							'value' => $v->get_value( 11, $this->data( $this->components->tab(), 'Sub Title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 200,
							]
						],[
							'type' => 'textarea',
							'name' => 'Description',
							'value' => $v->get_value( 12, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
								'limited' => 2000,
							]
						],[
							'type' => 'media upload',
							'name' => 'Image',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 6 ); // How it will be
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'text',
							'name' => 'Title',
							'value' => $v->get_value( 13, $this->data( $this->components->tab(), 'Title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 200,
							]
						],[
							'type' => 'wysiwyg',
							'name' => 'Description',
							'value' => $v->get_value( 14, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
							]
						],[
							'type' => 'multiple media upload',
							'name' => 'Images',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Images' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 7 ); // Contacts
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							 'type' => 'notes',
							 'name' => 'notes 6',
							 'options' => [
								 'text' => '<h5>This is relevant for template 1.</h5><hr>',
							 ]
						 ],[
							'type' => 'text',
							'name' => 'Title',
							'value' => $v->get_value( 15, $this->data( $this->components->tab(), 'Title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 200,
							]
						],[
							'type' => 'wysiwyg',
							'name' => 'Description',
							'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 8 ); // Contact form
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'text',
							'name' => 'Title',
							'value' => $v->get_value( 17, $this->data( $this->components->tab(), 'Title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 200,
							]
						],[
							'type' => 'text',
							'name' => 'Button text',
							'value' => $v->get_value( 19, $this->data( $this->components->tab(), 'Button text' ) ),
							'options' => [
								'required' => 1,
								'limited' => 50,
							]
						],[
							'type' => 'text',
							'name' => 'Success message',
							'value' => $v->get_value( 24, $this->data( $this->components->tab(), 'Success message' ) ),
							'options' => [
								'required' => 1,
								'limited' => 50,
							]
						],[
							'type' => 'media upload',
							'name' => 'Background',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 9 ); // Map
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'text',
							'name' => 'Title',
							'value' => $v->get_value( 22, $this->data( $this->components->tab(), 'Title' ) ),
							'options' => [
								'required' => 1,
								'limited' => 200,
							]
						],[
							'type' => 'textarea',
							'name' => 'Map embed iframe',
							'value' => $v->get_value( 23, $this->data( $this->components->tab(), 'Map embed iframe' ) ),
							'options' => [
								'required' => 1,
								'filter' => 'map',
							]
						],[
							'type' => 'notes',
							'name' => 'notes 4',
							'options' => [
								'text' => '<span class="wedp-description">Visit <a href="https://www.google.com/maps" target="_blank">Google maps</a> to create your map (Step by step: 1) Find location 2) Click the cog symbol in the lower right corner and select "Share or embed map" 3) On modal window select "Embed map" 4) Copy iframe code and paste it).</span>',
							]
						],[
							'type' => 'media upload',
							'name' => 'Background',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 10 ); // 1
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'notes',
							'name' => 'notes 3',
							'options' => [
								'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Additional tab</h4></div>',
							]
						],[
							'type' => 'wysiwyg',
							'name' => 'Description',
							'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
							]
						],[
							'type' => 'media upload',
							'name' => 'Image',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 11 ); // 2
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'notes',
							'name' => 'notes 11',
							'options' => [
								'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Additional tab</h4></div>',
							]
						],[
							'type' => 'wysiwyg',
							'name' => 'Description',
							'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
							]
						],[
							'type' => 'media upload',
							'name' => 'Image',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 12 ); // 3
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'notes',
							'name' => 'notes 12',
							'options' => [
								'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Additional tab</h4></div>',
							]
						],[
							'type' => 'wysiwyg',
							'name' => 'Description',
							'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
							]
						],[
							'type' => 'media upload',
							'name' => 'Image',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 13 ); // 4
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'notes',
							'name' => 'notes 13',
							'options' => [
								'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Additional tab</h4></div>',
							]
						],[
							'type' => 'wysiwyg',
							'name' => 'Description',
							'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
							]
						],[
							'type' => 'media upload',
							'name' => 'Image',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
				echo $this->components->tabs_content( 14 ); // 5
					echo $this->components->get_fields( [
						[
							'type' => 'enabled',
							'name' => 'Enabled Tab',
							'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
						],[
							'type' => 'notes',
							'name' => 'notes 14',
							'options' => [
								'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Additional tab</h4></div>',
							]
						],[
							'type' => 'wysiwyg',
							'name' => 'Description',
							'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
							'options' => [
								'required' => 1,
							]
						],[
							'type' => 'media upload',
							'name' => 'Image',
							'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
							'options' => [
								'required' => 1,
							]
						],
					] );
		echo $this->components->end();
		return ob_get_clean();
	}

	/**
	 * Return user data with tab id.
	 *
	 * @param  int|string $tab_id
	 * @param  string $name
	 * @return string
	 */
	public function data( $tab_id, $name ) {
		$name = WEDB_Components::clear_name( $name );
		return ( isset( $this->data["{$name}-$tab_id"] ) ) ? $this->data["{$name}-$tab_id"] : '';
	}
}

