<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WEDB_Default_Values class
 *
 * @class       WEDB_Default_Values
 * @version     0.0.1
 * @category    Admin
 * @author      Dmitry
 */
class WEDB_Default_Values {

	/**
	 * Return value by id.
	 *
	 * @param  int|string $id
	 * @return string
	 */
	private function value( $id ) {
		$v = [
			'default' => 'default',
			0 => WeddingBudgetClass::get_option( 'bride', 'Anna' ) . ' & ' . WeddingBudgetClass::get_option( 'groom', 'Michael' ),
			1 => 'We are getting merried!',
			2 => date( 'F j, Y', strtotime( WeddingBudgetClass::get_option( 'date', '+2 year' ) ) ),
			3 => 'WE ARE GETTING MERRIED!',
			4 => WeddingBudgetClass::get_option( 'groom', 'Michael' ),
			5 => 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
			6 => WeddingBudgetClass::get_option( 'bride', 'Anna' ),
			7 => 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
			8 => '11:30 am In The Square',
			9 => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor vitae in faucibus cura. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor.',
			10 => 'RESTAURANT "PLAZA"',
			11 => '16:00 banquet in the restaurant',
			12 => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor vitae in faucibus cura. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor.',
			13 => 'ORGANIZATION',
			14 => '<h3 style="font-size: 18px;color: #727475;text-align: left;font-family:Merriweather;font-weight:400;font-style:normal" class="vc_custom_heading" id="">Wedding Ceremony</h3>
		<div class="kleo_text_column wpb_text_column wpb_content_element " style=" font-size:14px; line-height:2em;">
			<div class="wpb_wrapper">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor vitae in.</p>
	
			</div>
		</div><h3 style="font-size: 18px;color: #727475;text-align: left;font-family:Merriweather;font-weight:400;font-style:normal" class="vc_custom_heading" id="">Lunch Time</h3>
		<div class="kleo_text_column wpb_text_column wpb_content_element " style=" font-size:14px; line-height:2em;">
			<div class="wpb_wrapper">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor vitae in.</p>
	
			</div>
		</div><h3 style="font-size: 18px;color: #727475;text-align: left;font-family:Merriweather;font-weight:400;font-style:normal" class="vc_custom_heading" id="">Party with Music</h3>
		<div class="kleo_text_column wpb_text_column wpb_content_element " style=" font-size:14px; line-height:2em;">
			<div class="wpb_wrapper">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor vitae in.</p>
	
			</div>
		</div><h3 style="font-size: 18px;color: #727475;text-align: left;font-family:Merriweather;font-weight:400;font-style:normal" class="vc_custom_heading" id="">Cake Cutting</h3>
		<div class="kleo_text_column wpb_text_column wpb_content_element " style=" font-size:14px; line-height:2em;">
			<div class="wpb_wrapper">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor vitae in.</p>
	
			</div>
		</div>',
			15 => 'CONTACT PHONES',
			16 => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor vitae in faucibus cura. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat purus sed tempus ornare. Sed convallis eu orci ut sodales. Nam rhoncus laoreet elit, a condimentum augue tempor.',
			17 => 'Send us a message',
			18 => WeddingBudgetClass::get_option( 'email', 'yourmail@gmail.com' ),
			19 => 'Send',
			20 => 'Where it will be',
			21 => 'Vi er glade for å informere deg om at våre bryllup vil skje veldig snart. Denne gledelige begivenheten er planlagt for September 20, 2020. Vi vil prøve å gjøre denne gledens dag, for alle våre gjester var komfortable og morsomme. Mer om arrangementet vil komme på denne nettsiden, og du kan også sende oss en melding her.',
			22 => 'CHURCH OF SAN PAUL',
			23 => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13453.23469090343!2d10.75898543844026!3d59.91828067548125!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46416dd779ba357b%3A0x677038c9acc2591c!2z0JrQvtGA0L7Qu9C10LLRgdC60LjQuSDQtNCy0L7RgNC10YY!5e0!3m2!1sru!2sru!4v1581938634061!5m2!1sru!2sru" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen=""></iframe>',
		];
		return $v[$id];
	}

	/**
	 * Return HTML of Wedding page builder
	 *
	 * @param  int|string $id
	 * @param  string $value
	 * @return string
	 */
	public function get_value( $id, $value = '' ) {
		return ( $value ) ? $value : $this->value( $id );
	}
}