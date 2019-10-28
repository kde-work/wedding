<?php
function wedding_webp_shortcode( $atts, $content = null) {
	$atts = shortcode_atts( array(
        'is_short' => false,
	), $atts );

	$wedding_page = new WeddingPage();
	$page = $wedding_page->get_pages();
    wb_scripts__webp();
	ob_start();
    if ( !$atts['is_short'] ) echo "<div class=\"wb-webp\">";
	?>
        <div class="wb-webp__items">
            <div class="wb__loading"></div>
            <?php
            if ( isset( $page->posts ) AND !empty( $page->posts ) AND $atts['is_short'] ) :
                $post = $page->posts[0];
                ?>
                <a class="wb-webp__item wb-webp__item--edit wb-webp__item--<?php echo $post->ID; ?>" href="/wp-admin/post.php?vc_action=vc_inline&post_id=<?php echo $post->ID; ?>&post_type=weddingpage" target="_blank">
                    <span class="wb-webp__icon wb-webp__icon--dove"></span>
                    Edit you Wedding Page <div class="wb-icon wb-icon--edit"></div>
                </a>
                <a class="wb-webp__item wb-webp__item--view wb-webp__item--<?php echo $post->ID; ?>" href="<?php echo get_the_permalink( $post->ID ); ?>" target="_blank">
                    View Page <div class="wb-icon wb-icon--new-window"></div>
                </a>
            <?php else : ?>
                <div class="wb-new-page">
                    <div class="wb-new-page__title"></div>
                    <div class="wb-new-page__templates">
                        <div class="wb-new-page__template wb-new-page__template--1">
                            <?php //$img1 = plugins_url( '/weddingbudget/html/img/maket-1.jpg' ); ?>
<!--                            <a href="--><?php //echo $img1; ?><!--" title="" data-title="" class="wb-new-page__prev"><img src="--><?php //echo $img1; ?><!--" alt="" class="alignnone size-medium wp-image-444200"></a>-->
                            <input type="radio" class="wb-new-page__radio wb-new-page__radio--1" name="template" id="wb-new-page__template--1" checked value="1">
                            <label class="wb-new-page__label wb-new-page__label--1" for="wb-new-page__template--1">Template #1</label>
                        </div>
                        <div class="wb-new-page__template wb-new-page__template--2">
	                        <?php //$img2 = plugins_url( '/weddingbudget/html/img/maket-2.jpg' ); ?>
<!--                            <a href="--><?php //echo $img2; ?><!--" title="" data-title="" class="wb-new-page__prev"><img src="--><?php //echo $img2; ?><!--" alt="" class="alignnone size-medium wp-image-444200"></a>-->
                            <input type="radio" class="wb-new-page__radio wb-new-page__radio--2" name="template" id="wb-new-page__template--2" checked value="2">
                            <label class="wb-new-page__label wb-new-page__label--2" for="wb-new-page__template--2">Template #2</label>
                        </div>
                    </div>
                    <div class="wb-new-page__button wb-webp__item wb-webp__item--edit wb-webp__item--new">
                        Create Wedding Page
                    </div>
                </div>
            <?php endif; ?>
        </div>
	<?php
	if ( !$atts['is_short'] ) echo "</div>";
	return ob_get_clean();
}
add_shortcode( 'wedding_webp', 'wedding_webp_shortcode' );

function wb_show_current_user_attachments( $query ) {
	$user_id = get_current_user_id();
	if ( $user_id && !current_user_can('administrator') && !current_user_can('editor') ) {
		$query['author'] = "$user_id, 8";
	}
	return $query;
}
add_filter( 'ajax_query_attachments_args', 'wb_show_current_user_attachments' );

