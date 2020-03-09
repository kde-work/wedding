<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WEDB_Components class
 *
 * @class       WEDB_Components
 * @version     0.0.1
 * @category    Admin
 * @author      Dmitry
 */
class WEDB_Components {

	/** @var integer $html_id */
	private $html_id = 0;

	/** @var string */
	private $class__field = 'wedp-single-com__field';

	/** @var array Array of fields of Component */
	private $array_of_fields = array();

	/** @var string of Path to the image folder */
	private $path_img = array();

	/** @var int of Current tab */
	private $current_tab = 0;

	/**
	 * Setup class
	 */
	public function __construct() {
	    global $wb_file;

	    $this->path_img = plugins_url( '/html/img', $wb_file );
	}

	/**
	 * Printed field by component
	 *
	 * @param object|array $component
	 * @param boolean $is_array
	 * @return string
	 */
	public function get_fields( $component, $is_array = true ) {
		ob_start();
		if ( !$is_array ) {
		    $contents = unserialize( $component->post_content );
		} else {
		    $contents = $component;
		}
		foreach ( $contents as $content ) {
			if ( strtolower( $content['type'] ) == 'text' ) {
				echo $this->text_field( $content );
			} elseif ( strtolower( $content['type'] ) == 'textarea' ) {
				echo $this->textarea_field( $content );
			} elseif ( strtolower( $content['type'] ) == 'notes' ) {
				echo $this->notes_field( $content );
			} elseif ( strtolower( $content['type'] ) == 'wysiwyg' ) {
				echo $this->wysiwyg_field( $content );
			} elseif ( strtolower( $content['type'] ) == 'radio' ) {
				echo $this->radio_field( $content );
			} elseif ( strtolower( $content['type'] ) == 'checkbox' ) {
				echo $this->checkbox_field( $content );
			} elseif ( strtolower( $content['type'] ) == 'select' ) {
				echo $this->select_field( $content );
			} elseif ( strtolower( $content['type'] ) == 'multiple select' ) {
				echo $this->select_field( $content, '', true );
			} elseif ( strtolower( $content['type'] ) == 'media upload' ) {
				echo $this->media_field( $content, '', false );
			} elseif ( strtolower( $content['type'] ) == 'multiple media upload' ) {
				echo $this->media_field( $content, '', true );
			} elseif ( strtolower( $content['type'] ) == 'file upload' ) {
				echo $this->file_field( $content, '' );
			} elseif ( strtolower( $content['type'] ) == 'enabled' ) {
				echo $this->enabled( $content );
			} elseif ( strtolower( $content['type'] ) == 'ooto begin' ) {
                 echo '';
			}
		}
		return ob_get_clean();
	}

	/**
	 * Returned Enabled component.
	 *
	 * @param array $component
	 * @return string
	 */
	public function enabled( $component ) {
		$id = ++$this->html_id;
		$clear_name = $this->name_filter( self::clear_name( $component['name'] ) );
		$html_id = $this->html_id_filter( "{$clear_name}-{$id}" );
		$this->register_field( $component, "#{$html_id}" );
		ob_start();
		?>
		<div class="<?php echo "{$this->class__field} wedp-single-com__field--enabled" . self::required( $component ); ?>" data-type="enabled">
			<label class="wedp-single-com__field-label inline"><?php echo $component['name']; ?><?php echo $this->help_text( $component ); ?></label>
            <div class="wedp-enabled">
                <input id="<?php echo $html_id; ?>-on" class="wedp-toggle wedp-toggle-left" name="<?php echo $clear_name; ?>" value="enable" autocomplete="off" type="radio" <?php echo ( $component['value'] == 'enable' ) ? 'checked' : ''; ?>>
<label for="<?php echo $html_id; ?>-on" class="wedp-btn">Enable</label>
<input id="<?php echo $html_id; ?>-off" class="wedp-toggle wedp-toggle-right" name="<?php echo $clear_name; ?>" value="disable" type="radio" autocomplete="off" <?php echo ( $component['value'] == 'disable' ) ? 'checked' : ''; ?>>
<label for="<?php echo $html_id; ?>-off" class="wedp-btn">Disable</label>

                <div class="wedp-enabled__bg"></div>
            </div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Returned Textarea
	 *
	 * @param array $component
	 * @param string $extra_class
	 * @return string
	 */
	public function textarea_field( $component, $extra_class = '' ) {
		$id = ++$this->html_id;
		$clear_name = $this->name_filter( self::clear_name( $component['name'] ) );
		$html_id = $this->html_id_filter( "{$clear_name}-{$id}" );
		$this->register_field( $component, "#{$html_id}" );
		$additional_tag = '';
		if ( isset( $component['options'] ) AND isset( $component['options']['limited'] ) ) {
			$additional_tag .= "maxlength=\"{$component['options']['limited']}\"";
        }
		ob_start();
		?>
		<div class="<?php echo "{$this->class__field} wedp-single-com__field--textarea {$extra_class} " . self::required( $component ); ?>" data-type="textarea">
			<label for="<?php echo $html_id; ?>" class="wedp-single-com__field-label inline"><?php echo $component['name']; ?><?php echo $this->help_text( $component ); ?></label>
            <textarea class="wedp-single-com__text-field w-input wedp__name" <?php echo $additional_tag; ?> name="<?php echo $clear_name; ?>" autocomplete="off" id="<?php echo $html_id; ?>"><?php echo $component['value']; ?></textarea>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Returned wysiwyg
	 *
	 * @param array $component
	 * @return string
	 */
	public function wysiwyg_field( $component ) {
		$id = ++$this->html_id;
        $clear_name = $this->name_filter( self::clear_name( $component['name'] ) );
		$html_id = $this->html_id_filter( str_replace( array('-', '_'), '', $clear_name ) . "{$id}" );
		$this->register_field( $component, "#{$html_id}" );
		ob_start();
		?>
		<div class="<?php echo "{$this->class__field} wedp-single-com__field--wysiwyg " . self::required( $component ); ?>" data-type="wysiwyg">
			<label for="<?php echo $html_id; ?>" class="wedp-single-com__field-label inline"><?php echo $component['name']; ?><?php echo $this->help_text( $component ); ?></label>
            <?php
            wp_editor( $component['value'], $html_id , array(
	            'wpautop'       => 1,
	            'media_buttons' => 0,
	            'textarea_name' => $clear_name,
	            'editor_class'  => 'wedp-single-com__wysiwyg wedp__name',
	            'textarea_rows' => 16,
	            'teeny'         => 0,
	            'dfw'           => 0,
	            'tinymce'       => 1,
	            'quicktags'     => 1,
	            'drag_drop_upload' => true
            ) );
            ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Returned text field section
	 *
	 * @param array $component
	 * @param string $extra_class
	 * @param string $extra_class_input
	 * @return string
	 */
	public function text_field( $component, $extra_class = '', $extra_class_input = '' ) {
		$id = ++$this->html_id;
		$clear_name = $this->name_filter( self::clear_name( $component['name'] ) );
		$html_id = $this->html_id_filter( "{$clear_name}-{$id}" );
		$this->register_field( $component, "#{$html_id}" );
		$additional_tag = '';
		if ( isset( $component['options'] ) AND isset( $component['options']['limited'] ) ) {
			$additional_tag .= "maxlength=\"{$component['options']['limited']}\"";
		}
		ob_start();
		?>
		<div class="<?php echo "{$this->class__field} {$extra_class} wedp-single-com__field--text " . self::required( $component ); ?>" data-type="text">
			<label for="<?php echo $html_id; ?>" class="wedp-single-com__field-label inline"><?php echo $component['name']; ?><?php echo $this->help_text( $component ); ?></label>
			<input class="wedp-single-com__text-field w-input <?php echo $extra_class_input; ?> wedp__name" <?php echo $additional_tag; ?> name="<?php echo $clear_name; ?>" autocomplete="off" id="<?php echo $html_id; ?>" value="<?php echo $component['value']; ?>" type="text">
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Returned File upload section
	 *
	 * @param array $component
	 * @param string $extra_class
	 * @return string
	 */
	public function file_field( $component, $extra_class = '' ) {
		$id = ++$this->html_id;
		$clear_name = $this->name_filter( self::clear_name( $component['name'] ) );
		$html_id = $this->html_id_filter( $clear_name . "-{$id}" );
		$type_class = 'file_upload';
		$type = 'file upload';
		$this->register_field( $component, "#{$html_id}" );
        $mime = '';
        if ( isset( $component['options'] ) AND isset( $component['options']['mime'] ) AND $component['options']['mime'] ) {
            $mime = $component['options']['mime'];
        }

		ob_start();
		?>
        <div class="<?php echo "{$this->class__field} {$extra_class} wedp-single-com__field--$type_class " . self::required( $component ); ?>" data-type="<?php echo $type; ?>">
            <label class="wedp-single-com__field-label inline" for="<?php echo $html_id; ?>"><?php echo $component['name']; ?><?php echo $this->help_text( $component ); ?></label>
            <div class="wedp-single-com__upload-section">
                <input type="text" class="wedp-single-com__file-upload w-input wedp__name" name="<?php echo $clear_name; ?>" id="<?php echo $html_id; ?>" autocomplete="off" data-mime="<?php echo $mime; ?>" placeholder="Insert URL or Upload File">
                <div class="wedp__upload_file_button wpb-button">Upload File</div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Returned Media upload section
	 *
	 * @param array $component
	 * @param string $extra_class
	 * @param boolean $multiple
	 * @return string
	 */
	public function media_field( $component, $extra_class = '', $multiple = false ) {
		$id = ++$this->html_id;
		$clear_name = $this->name_filter( self::clear_name( $component['name'] ) );
		$html_id = $this->html_id_filter( $clear_name . "-{$id}" );
		$type_class = 'media_upload';
		$type = 'media upload';
		if ( $multiple ) {
			$type_class = 'multiple_media_upload';
			$type = 'multiple media upload';
		}
		$this->register_field( $component, "#{$html_id}" );
		ob_start();
		?>
        <div class="<?php echo "{$this->class__field} {$extra_class} wedp-single-com__field--$type_class " . self::required( $component ); ?>" data-type="<?php echo $type; ?>">
            <label class="wedp-single-com__field-label inline" for="<?php echo $html_id; ?>"><?php echo $component['name']; ?><?php echo $this->help_text( $component ); ?></label>
            <div class="wedp-single-com__upload-box">
                <?php echo $this->get_image( $component['value'] ); ?>
                <div class="wedp-single-com__upload-buttons">
                    <input type="hidden" class="wedp-single-com__media-upload w-input wedp__name" name="<?php echo $clear_name; ?>" autocomplete="off" id="<?php echo $html_id; ?>" value="<?php echo $component['value']; ?>">
                    <div class="wedp__upload_image_button wpb-button">Upload image</div>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Return html structure for user image.
	 *
	 * @param  string|int $data
	 * @return string
	 */
	public function get_image( $data ) {
        ob_start();
        $def_src = $this->path_img . '/media-upload--thumb.png';
        $ids = explode( ',', $data );
        ?>
        <div class="wedp-single-com__images" data-src="<?php echo $def_src; ?>">
        <?php
        foreach ( $ids as $id ) {
            if ( $id AND $id != 'default' ) {
                $image = wp_get_attachment_image_src( $id, 'large', true )[0];
                ?>
                <div class="wedp-single-com__single-image wedp-single-com__single-image--<?php echo $id; ?>"><i class="wedp-single-com__delete-img" onclick="wedp_delete_img(this)"></i><div class="wedp-single-com__upload-image" data-id="<?php echo $id; ?>" onclick="wedp_thumbnail_contain(this)" data-img="<?php echo $image; ?>" style="background-image: url('<?php echo $image; ?>');"></div></div>
                <?php
            } else {
                ?>
                <div class="wedp-single-com__single-image">
                    <div class="wedp-single-com__upload-image" style="background-image: url('<?php echo $def_src; ?>')"></div>
                </div>
                <?php
            }
        }
        echo "</div>";
        return ob_get_clean();
	}

	/**
	 * Returned radio field section
	 *
	 * @param array $component
	 * @param string $extra_class
	 * @return string
	 */
	public function radio_field( $component, $extra_class = '' ) {
		if ( !isset( $component['options'] ) ) {
			return '';
		}
		$id = ++$this->html_id;
		$clear_name = $this->name_filter( self::clear_name( $component['name'] ) );
		$this->register_field( $component, "[name=\"{$clear_name}\"]:checked" );
		ob_start();
		?>
		<div class="<?php echo "{$this->class__field} {$extra_class} wedp-single-com__field--radio " . self::required( $component ); ?>" data-type="radio">
			<label class="wedp-single-com__field-label inline"><?php echo $component['name']; ?><?php echo $this->help_text( $component ); ?></label>
			<?php
			foreach ( $component['options'] as $input_name => $item ) {
				if ( !is_numeric ( $input_name ) ) {
					continue;
				}
		        $html_id = $this->html_id_filter( $this->name_filter( self::clear_name( $item['type'] ) ) . "-{$id}" );
				?>
                <div class="inline w-radio">
                    <input id="<?php echo $html_id; ?>" name="<?php echo $clear_name; ?>" class="w-radio-input wedp__name" autocomplete="off" type="radio" value="<?php echo $item['type']; ?>">
                    <label for="<?php echo $html_id; ?>" class="w-form-label"><?php echo $item['type']; ?></label>
                </div>
				<?php
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Returned Select field section
	 *
	 * @param array $component
	 * @param string $extra_class
	 * @param boolean $multiple
	 * @return string
	 */
	public function select_field( $component, $extra_class = '', $multiple = false ) {
		if ( !isset( $component['options'] ) ) {
			return '';
		}
		$id = ++$this->html_id;
        $clear_name = $this->name_filter( self::clear_name( $component['name'] ) );
		$html_id = $this->html_id_filter( $clear_name . "-{$id}" );
		$additional = '';
		$type_class = 'select';
		$type = 'select';
		if ( $multiple ) {
			$additional = 'multiple';
			$type_class = 'multiple_select';
			$type = 'multiple select';
        }
		$this->register_field( $component, "#{$html_id}" );
		ob_start();
		?>
		<div class="<?php echo "{$this->class__field} {$extra_class} wedp-single-com__field--$type_class " . self::required( $component ); ?>" data-type="<?php echo $type; ?>">
			<label class="wedp-single-com__field-label inline" for="<?php echo $html_id; ?>"><?php echo $component['name']; ?><?php echo $this->help_text( $component ); ?></label>
            <select name="<?php echo $clear_name; ?>" id="<?php echo $html_id; ?>" class="wedp__name" <?php echo $additional; ?>>
			<?php
		    if ( !( isset( $component['options'] ) AND isset( $component['options']['required'] ) AND $component['options']['required'] === '0' ) AND !$multiple ) {
		        echo "<option disabled selected value=\"default\">Make a choice</option>";
		    }
			foreach ( $component['options'] as $input_name => $item ) {
				if ( !is_numeric ( $input_name ) ) {
					continue;
				}
				?>
                <option value="<?php echo $item['type']; ?>"><?php echo $item['type']; ?></option>
				<?php
			}
			?>
            </select>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Returned checkbox field section
	 *
	 * @param array $component
	 * @param string $extra_class
	 * @return string
	 */
	public function checkbox_field( $component, $extra_class = '' ) {
		if ( !isset( $component['options'] ) ) {
			return '';
		}
		ob_start();
		?>
		<div class="<?php echo "{$this->class__field} {$extra_class} wedp-single-com__field--checkbox "; ?>" data-type="checkbox">
			<label class="wedp-single-com__field-label inline"><?php echo $component['name']; ?><?php echo $this->help_text( $component ); ?></label>
			<?php
			foreach ( $component['options'] as $input_name => $item ) {
				if ( !is_numeric ( $input_name ) ) {
					continue;
				}
				$id = ++$this->html_id;
                $clear_name = $this->name_filter( self::clear_name( $item['type'] ) );
                $html_id = $this->html_id_filter( $clear_name . "-{$id}" );
				$this->register_field( array( 'name' => $item['type'] ), "[name=\"{$clear_name}\"]:checked" );
				?>
                    <div class="inline w-radio <?php echo self::required( $item ); ?>" data-type="checkbox">
                        <input id="<?php echo $html_id; ?>" autocomplete="off" name="<?php echo $clear_name; ?>" class="w-checkbox-input wedp__name" type="checkbox" value="1">
                        <label for="<?php echo $html_id; ?>" class="w-form-label"><?php echo $item['type']; ?></label>
                    </div>
				<?php
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Init.
     *
	 * @return string
	 */
	public function init() {
		ob_start();
		?>
        <div class="wb-web-editor">
            <form class="wb-web-editor__form">
		<?php
		return ob_get_clean();
	}

	/**
	 * Init Tabs.
     *
	 * @return string
	 */
	public function init_tabs() {
		ob_start();
		?>
                <h3 class="wb-change-template__title">Your text and images</h3>
                <div class="wedb-tab">
                    <div class="wedb-tab__headers">
		<?php
		return ob_get_clean();
	}

	/**
	 * Tabs content.
	 *
	 * @return string
	 */
    public function tabs_content_begin() {
        ob_start();
        ?>
                    </div>
                    <div class="wedb-tab__contents">
        <?php
        return ob_get_clean();
    }

	/**
	 * Editor end.
     *
	 * @return string
	 */
	public function end() {
		ob_start();
		?>
                        </div>
                    </div>
                </div>
                <?php echo $this->templates(); ?>
                <input type="hidden" name="action" value="wpb-save">
                <?php wp_nonce_field( 'wpb_action','wpb' ); ?>
                <div class="wb-web-editor__action">
                    <input type="submit" class="wb-web-editor__save wb-button" value="Save">
                </div>
            </form>
        </div>
        <div class="wedp__message wedp__message--success">
            <div class="wedp__message-text">Saved!</div>
            <a class="wb-site-name__page_link" href="<?php echo get_the_permalink( WeddingBudgetClass::get_option( 'wedding-page-id' ) ); ?>" target="_blank" title="" data-title="">
                View Page <div class="wb-icon wb-icon--new-window"></div>
            </a>
        </div>
        <div class="wedp__loader"></div>
        <div class="wedp-modal wedp-modal--thumbnail">
            <div class="wedp-modal__content"></div>
            <div class="wedp-modal__thumb-close close_thumb_modal"></div>
            <div class="wedp-modal__back close_thumb_modal"></div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Change Template.
     *
	 * @return string
	 */
	public function templates() {
		ob_start();
		?>
        <div class="wb-web-editor__templates">
            <h3 class="wb-change-template__title">Select your theme</h3>
            <?php echo wb_templates(); ?>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Tab Header.
	 *
	 * @param int $id
	 * @param string $title
	 * @param bool $is_current
	 * @return string
	 */
	public function tabs_header( $id, $title, $is_current = false ) {
		ob_start();
		?>
		<div class="wedb-tab__header wedb-tab__header--<?php echo $id; ?> <?php echo ( $is_current ) ? 'wedb-tab__header--current' : ''; ?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Tab Content.
	 *
	 * @param int $id
	 * @param bool $is_begin
	 * @param bool $is_current
	 * @return string
	 */
	public function tabs_content( $id, $is_begin = false, $is_current = false ) {
        $this->current_tab = $id;
		ob_start();
		echo ( !$is_begin ) ? '</div>' : '';
		?>
        <div class="wedb-tab__content wedb-tab__content--<?php echo $id; ?> <?php echo ( $is_current ) ? 'wedb-tab__content--current' : ''; ?>" data-id="<?php echo $id; ?>">
		<?php
		return ob_get_clean();
	}

	/**
	 * Return current Tab id.
	 *
	 * @return int|string
	 */
	public function tab() {
        return $this->current_tab;
	}

	/**
	 * Site name.
	 *
	 * @return string
	 */
	public function site_name() {
        $wedding_page = new WeddingPage();
	    $page = $wedding_page->get_pages();
	    $post = $page->posts[0];
		ob_start();
		?>
        <div class="wb-site-name">
            <h3 class="wb-change-template__title">Your site name</h3>
            <div class="wb-site-name__form">
                <label for="wb-site-name__name" class="wb-site-name__site">bryllupshjemmeside.no/</label>
                <input type="text" id="wb-site-name__name" class="wb-site-name__input" autocomplete="off" placeholder="your-page" name="page-url" value="<?php echo WeddingPage::get_page_name(); ?>">
                <div class="wb-site-name__answer wb-site-name__answer--ok"><div class="wb-icon wb-icon--ok" title="Page name is free"></div> <a class="wb-site-name__page_link" href="<?php echo get_the_permalink( WeddingBudgetClass::get_option( 'wedding-page-id' ) ); ?>" target="_blank">
                    View Page <div class="wb-icon wb-icon--new-window"></div>
                </a></div>
                <div class="wb-site-name__answer wb-site-name__answer--success">free</div>
                <div class="wb-site-name__answer wb-site-name__answer--error">is busy</div>
                <div class="wb-site-name__save wpb-button" title="" data-title="">Save page name</div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Returned Notes
	 *
	 * @param array $component
	 * @return string
	 */
	public function notes_field( $component ) {
		if ( isset( $component['options'] ) AND isset( $component['options']['text'] ) ) {
			return $component['options']['text'];
        }
		return '';
	}

	/**
	 * Changed component name for Repeating Group
	 *
	 * @param string $name
	 * @return string
	 */
	protected function name_filter( $name ) {
//		for ($i = 0; $i < count( $this->repeating_group ); ++$i ) {
//            $j = $i + 1;
//			if ( $this->repeating_group[$i]['flag'] == true AND $j AND $this->repeating_group[$i]['tab'] ) {
//				$name .= "__$j-{$this->repeating_group[$i]['tab']}";
//			}
//		}
		return "$name-{$this->current_tab}";
	}

	/**
	 * Changed component html id for Ajax Tabs
	 *
	 * @param string $name
	 * @return string
	 */
	protected function html_id_filter( $name ) {
//        if ( $this->ajax_tab !== false ) {
//            $name = "{$this->ajax_tab}--{$name}";
//        }
		return $name;
	}

	/**
	 * Returned class for required (or none) fields
	 *
	 * @param array $component
	 * @return string
	 */
	public static function required( $component ) {
	    if ( isset( $component['options'] ) AND isset( $component['options']['required'] ) AND $component['options']['required'] == '1' ) {
	        return 'wedp-single-com__field--required';
        } else {
		    return 'wedp-single-com__field--no-required';
        }
	}

	/**
	 * Returned class for required (or none) fields (for checkbox)
	 *
	 * @param array $component
	 * @return string
	 */
	public static function required_level2( $component ) {
	    if ( isset( $component['options'] ) AND  isset( $component['options']['required'] ) AND $component['options']['required'] === '0' ) {
	        return 'wedp-single-com__field--no-required';
        } else {
		    return 'wedp-single-com__field--required';
        }
	}

	/**
	 * Returned clear string without bad charsets
	 *
	 * @param string $string
	 * @return string
	 */
	public static function clear_name( $string ) {
		$iso9_table = array(
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G`',
			'Ґ' => 'G`', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Є' => 'YE',
			'Ж' => 'ZH', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'Y',
			'Ј' => 'J', 'І' => 'I', 'Ї' => 'YI', 'К' => 'K', 'Ќ' => 'K',
			'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N',
			'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
			'У' => 'U', 'Ў' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'TS',
			'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SH', 'Ъ' => '',
			'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g',
			'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye',
			'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'y',
			'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k',
			'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n',
			'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
			'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
			'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'sh', 'ь' => '',
			'ы' => 'y', 'ъ' => "", 'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
		);
		$replace = array( ' ', '.', ',', ';', '%', '&', '<', '>', '*', '(', ')', '$', '^', '@', '!', '+', '-' , '|', '\\', '/' );
		$string = strtr( $string, $iso9_table );
		$string = strtolower( $string );
		$string = str_replace( $replace, '_', $string );
		return $string;
	}

	/**
	 * Register component in array $this->array_of_fields
	 *
	 * @param array $component
	 * @param string $selector
	 * @return void
	 */
	public function register_field( $component, $selector ) {
		array_push(
			$this->array_of_fields,
			array(
				'name' => $component['name'],
				'slug' => $this->name_filter( self::clear_name( $component['name'] ) ),
				'selector' => $selector
			)
		);
	}

	/**
	 * Returned help text for label
	 *
	 * @param array $component
	 * @return string
	 */
	public static function help_text( $component ) {
		if ( isset( $component['options'] ) AND isset( $component['options']['help'] ) AND $component['options']['help'] ) {
            return "<span class=\"help-text\">{$component['options']['help']}</span>";
		}
        return '';
	}
}