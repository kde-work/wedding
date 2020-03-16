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
		echo $this->components->password();
		echo $this->components->init_tabs();
		echo $this->components->tabs_header( 1, 'Header', true );
		echo $this->components->tabs_header( 2, 'Vår historie' );
		echo $this->components->tabs_header( 3, 'Om oss' );
		echo $this->components->tabs_header( 4, 'Seremonien' );
		echo $this->components->tabs_header( 5, 'Bryllupsfesten' );
		echo $this->components->tabs_header( 6, 'Bryllupsprogram' );
		echo $this->components->tabs_header( 7, 'Kontakt' );
		echo $this->components->tabs_header( 10, 'Egen 1' );
		echo $this->components->tabs_header( 11, 'Egen 2' );
		echo $this->components->tabs_header( 12, 'Egen 3' );
		echo $this->components->tabs_header( 13, 'Egen 4' );
		echo $this->components->tabs_header( 14, 'Egen 5' );
		echo $this->components->tabs_header( 8, 'Kontaktskjema' );
		echo $this->components->tabs_header( 9, 'Kart' );
		echo $this->components->tabs_content_begin();
		echo $this->components->tabs_content( 1, true, true );
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'text',
				'name' => 'Names',
				'label' => 'Navn',
				'value' => $v->get_value( 0, $this->data( $this->components->tab(), 'Names' ) ), // first param 'get_value' look WEDB_Default_Values
				'options' => [
					'required' => 1,
					'limited' => 120,
				]
			],[
				'type' => 'text',
				'name' => 'Sub title',
				'label' => 'Undertittel',
				'value' => $v->get_value( 1, $this->data( $this->components->tab(), 'Sub title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 100,
				]
			],[
				'type' => 'text',
				'name' => 'Date line',
				'label' => 'Datolinje',
				'value' => $v->get_value( 2, $this->data( $this->components->tab(), 'Date line' ) ),
				'options' => [
					'required' => 1,
					'limited' => 100,
				]
			],[
				'type' => 'text',
				'name' => 'Button text to message form',
				'label' => 'Tekst på knapp til hilsenskjema',
				'value' => $v->get_value( 25, $this->data( $this->components->tab(), 'Button text to message form' ) ),
				'options' => [
					'required' => 1,
					'limited' => 100,
				]
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Bakgrunn',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 2 );
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'text',
				'name' => 'Title',
				'label' => 'Tittel',
				'value' => $v->get_value( 3, $this->data( $this->components->tab(), 'Title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 100,
				]
			],[
				'type' => 'textarea',
				'name' => 'Description',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 21, $this->data( $this->components->tab(), 'Description' ) ),
				'options' => [
					'required' => 1,
					'limited' => 2000,
					'help' => 'Brukes ikke i tema nr. 3',
				]
			],[
				'type' => 'media upload',
				'name' => 'Image',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Bakgrunn',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 3 ); // About us
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Bakgrunn',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'notes',
				'name' => 'notes 1',
				'options' => [
					'text' => '<hr><h4>PERSON 1</h4>',
				]
			],[
				'type' => 'text',
				'name' => 'Name of the groom',
				'label' => 'Navn på brud/brudgom',
				'value' => $v->get_value( 4, $this->data( $this->components->tab(), 'Name of the groom' ) ),
				'options' => [
					'required' => 1,
					'limited' => 100,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description of the groom',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 5, $this->data( $this->components->tab(), 'Description of the groom' ) ),
				'options' => [
					'required' => 1,
					'limited' => 2000,
				]
			],[
				'type' => 'media upload',
				'name' => 'Photo of the groom',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Photo of the groom' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'notes',
				'name' => 'notes 2',
				'options' => [
					'text' => '<hr><h4>PERSON 2</h4>',
				]
			],[
				'type' => 'text',
				'name' => 'Name of the bride',
				'label' => 'Navn på brud/brudgom',
				'value' => $v->get_value( 6, $this->data( $this->components->tab(), 'Name of the bride' ) ),
				'options' => [
					'required' => 1,
					'limited' => 100,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description of the bride',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 7, $this->data( $this->components->tab(), 'Description of the bride' ) ),
				'options' => [
					'required' => 1,
					'limited' => 2000,
				]
			],[
				'type' => 'media upload',
				'name' => 'Photo of the bride',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Photo of the bride' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 4 ); // The ceremony
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'notes',
				'name' => 'notes 3',
				'options' => [
					'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>(KIRKEN / VIGSELSSTED)</h4></div>',
				]
			],[
				'type' => 'text',
				'name' => 'Title',
				'label' => 'Tittel',
				'value' => $v->get_value( 22, $this->data( $this->components->tab(), 'Title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 200,
				]
			],[
				'type' => 'text',
				'name' => 'Sub Title',
				'label' => 'Undertittel',
				'value' => $v->get_value( 8, $this->data( $this->components->tab(), 'Sub Title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 200,
				]
			],[
				'type' => 'textarea',
				'name' => 'Description',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 9, $this->data( $this->components->tab(), 'Description' ) ),
				'options' => [
					'required' => 1,
					'limited' => 2000,
				]
			],[
				'type' => 'media upload',
				'name' => 'Image',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 5 );
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'notes',
				'name' => 'notes 3',
				'options' => [
					'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>BRYLLUPSFESTEN / LOKALET</h4></div>',
				]
			],[
				'type' => 'text',
				'name' => 'Title',
				'label' => 'Tittel',
				'value' => $v->get_value( 10, $this->data( $this->components->tab(), 'Title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 200,
				]
			],[
				'type' => 'text',
				'name' => 'Sub Title',
				'label' => 'Undertittel',
				'value' => $v->get_value( 11, $this->data( $this->components->tab(), 'Sub Title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 200,
				]
			],[
				'type' => 'textarea',
				'name' => 'Description',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 12, $this->data( $this->components->tab(), 'Description' ) ),
				'options' => [
					'required' => 1,
					'limited' => 2000,
				]
			],[
				'type' => 'media upload',
				'name' => 'Image',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 6 ); // How it will be
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'text',
				'name' => 'Title',
				'label' => 'Title',
				'value' => $v->get_value( 13, $this->data( $this->components->tab(), 'Title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 200,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description',
				'label' => 'Description',
				'value' => $v->get_value( 14, $this->data( $this->components->tab(), 'Description' ) ),
				'options' => [
					'required' => 1,
				]
			],[
				'type' => 'multiple media upload',
				'name' => 'Images',
				'label' => 'Images',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Images' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 7 ); // Contacts
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'notes',
				'name' => 'notes 6',
				'options' => [
					'text' => '<h5>Kun relevant for hjemmeside-tema nr. 1</h5><hr>',
				]
			],[
				'type' => 'text',
				'name' => 'Title',
				'label' => 'Tittel',
				'value' => $v->get_value( 15, $this->data( $this->components->tab(), 'Title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 200,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description',
				'label' => 'Beskrivelse',
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
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'text',
				'name' => 'Title',
				'label' => 'Tittel',
				'value' => $v->get_value( 17, $this->data( $this->components->tab(), 'Title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 200,
				]
			],[
				'type' => 'text',
				'name' => 'Button text',
				'label' => 'Tekst på knapp',
				'value' => $v->get_value( 19, $this->data( $this->components->tab(), 'Button text' ) ),
				'options' => [
					'required' => 1,
					'limited' => 50,
				]
			],[
				'type' => 'text',
				'name' => 'Success message',
				'label' => 'Suksess beskjed',
				'value' => $v->get_value( 24, $this->data( $this->components->tab(), 'Success message' ) ),
				'options' => [
					'required' => 1,
					'limited' => 50,
				]
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Bakgrunn',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 9 ); // Map
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 26, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'text',
				'name' => 'Title',
				'label' => 'Tittel',
				'value' => $v->get_value( 22, $this->data( $this->components->tab(), 'Title' ) ),
				'options' => [
					'required' => 1,
					'limited' => 200,
				]
			],[
				'type' => 'textarea',
				'name' => 'Map embed iframe',
				'label' => 'Kart bygg-inn Iframe',
				'value' => $v->get_value( 23, $this->data( $this->components->tab(), 'Map embed iframe' ) ),
				'options' => [
					'required' => 1,
					'filter' => 'map',
				]
			],[
				'type' => 'notes',
				'name' => 'notes 4',
				'options' => [
					'text' => '<span class="wedp-description">Besøk <a href="https://www.google.com/maps" target="_blank">Google maps</a> for å lage kartet deres (Steg for steg: 1) Finn lokasjonen 2) Klikk på DEL i stedsinfovinduet som du ser til venstre. 3) I popup-vinduet velg "Bygg inn et kart" 4) Trykk på Kopiér HTML og lim koden inn her).</span>',
				]
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Bakgrunn',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 10 ); // 1
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'notes',
				'name' => 'notes 3',
				'options' => [
					'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Her kan dere legge til en ekstradel som er helt unik for dere. Denne delen vil vises helt nederst på den ferdige bryllupssiden deres.</h4></div>',
				]
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Background',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'media upload',
				'name' => 'Image',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description under image',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description under image' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 11 ); // 2
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'notes',
				'name' => 'notes 3',
				'options' => [
					'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Her kan dere legge til en ekstradel som er helt unik for dere. Denne delen vil vises helt nederst på den ferdige bryllupssiden deres.</h4></div>',
				]
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Background',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'media upload',
				'name' => 'Image',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description under image',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description under image' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 12 ); // 3
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'notes',
				'name' => 'notes 3',
				'options' => [
					'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Her kan dere legge til en ekstradel som er helt unik for dere. Denne delen vil vises helt nederst på den ferdige bryllupssiden deres.</h4></div>',
				]
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Background',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'media upload',
				'name' => 'Image',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description under image',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description under image' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 13 ); // 4
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'notes',
				'name' => 'notes 3',
				'options' => [
					'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Her kan dere legge til en ekstradel som er helt unik for dere. Denne delen vil vises helt nederst på den ferdige bryllupssiden deres.</h4></div>',
				]
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Background',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'media upload',
				'name' => 'Image',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description under image',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description under image' ) ),
				'options' => [
					'required' => 0,
				]
			],
		] );
		echo $this->components->tabs_content( 14 ); // 5
		echo $this->components->get_fields( [
			[
				'type' => 'enabled',
				'name' => 'Enabled Tab',
				'label' => 'Aktiv på side',
				'value' => $v->get_value( 27, $this->data( $this->components->tab(), 'Enabled Tab' ) ),
			],[
				'type' => 'notes',
				'name' => 'notes 3',
				'options' => [
					'text' => '<div style="margin-bottom: 1.6em;text-align: center"><h4>Her kan dere legge til en ekstradel som er helt unik for dere. Denne delen vil vises helt nederst på den ferdige bryllupssiden deres.</h4></div>',
				]
			],[
				'type' => 'media upload',
				'name' => 'Background',
				'label' => 'Background',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Background' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'media upload',
				'name' => 'Image',
				'label' => 'Bilde',
				'value' => $v->get_value( 'default', $this->data( $this->components->tab(), 'Image' ) ),
				'options' => [
					'required' => 0,
				]
			],[
				'type' => 'wysiwyg',
				'name' => 'Description under image',
				'label' => 'Beskrivelse',
				'value' => $v->get_value( 16, $this->data( $this->components->tab(), 'Description under image' ) ),
				'options' => [
					'required' => 0,
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