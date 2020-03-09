(function ($) {
    if (!$) {
        console.error('JQuery and $ objects are missing');
        return;
    }

    var $body = $('body');

    // Save button
    $(function() {
        $body.on('submit', '.wb-web-editor__form', function (e) {
            wpb_save(e, this);
        })
    });
    function wpb_save(e, _form) {
        if ($body.hasClass('in-process')) return;
        e.preventDefault();

        var $form = $(_form),
            $par = $form.closest('.wb-webp'),
            ajaxData = $form.serialize();

        if (wpb_is_empty($par)) return;

        $.ajax({
            type: 'POST',
            url: wedding_budget.url,
            async: true,
            dataType: 'json',
            data: ajaxData,
            beforeSend: function (xhr, ajaxOptions, thrownError) {
                $body.addClass('in-process').addClass('in-process-save');
            },
            success: function (data) {
                console.log(data);
                window.onbeforeunload = {};
                if (data.answer !== void 0 && data.answer) {
                    var $wb_webp__items = $('.wb-webp__items'),
                        $form__success = $('.wb-site-name__form--success'),
                        $page_link = $('.wb-site-name__page_link');

                    $wb_webp__items.addClass('wb-webp__items--page-exist');
                    $par.addClass('wb-webp--saved');
                    $page_link.attr('href', data.url);
                    $form__success.removeClass('wb-site-name__form--success');

                    // if (data.value !== void 0) {
                    // }
                }

                if (data.ErrorMessage) {
                    console.error(data.ErrorMessage);
                }
            },
            complete: function (xhr, ajaxOptions, thrownError) {
                $body.removeClass('in-process').removeClass('in-process-save');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('wpb-save-@11: '+xhr.status);
                console.log('wpb-save-@12: '+thrownError);
            }
        });
        return false;
    }

    // Clear empty errors
    $(function() {
        $body.on('keyup', '.wedp-single-com__field input, .wedp-single-com__field textarea', function (e) {
            var $b = $('.wb-webp--saved');

            if ($b.length) {
                $b.removeClass('wb-webp--saved');
            }
            window.onbeforeunload = function() {
                return true;
            };
        });
    });

    // Clear success message
    $(function() {
        $body.on('keyup', '.wedp-single-com__field--required input, .wedp-single-com__field--required textarea', function (e) {
            var $this = $(this),
                $par = $this.closest('.wedb-tab'),
                $content = $this.closest('.wedb-tab__content'),
                $block = $this.closest('.wedp-single-com__field'),
                $tab = $('.wedb-tab__header--' + $content.data('id'), $par);

            if ($this.val()) {
                $tab.removeClass('wedb-tab__header--empty');
                $block.removeClass('error--empty-field');
            }
        });
    });

    // Check empty fields
    function wpb_is_empty($par) {
        var $el = $('.wedp-single-com__field--required input, .wedp-single-com__field--required textarea', $par),
            is_empty = false;

        for (var i = 0; i < $el.length; ++i) {
            var $this = $($el[i]);

            if ($this.val() === '') {
                var $content = $this.closest('.wedb-tab__content'),
                    $block = $this.closest('.wedp-single-com__field'),
                    $tab = $('.wedb-tab__header--' + $content.data('id'), $par);

                $tab.addClass('wedb-tab__header--empty');
                $block.addClass('error--empty-field');
                is_empty = true;
            }
        }
        return is_empty;
    }

    // Tabs
    $(function() {
        $body.on('click', '.wedb-tab__header', function() {
            var $this = $(this),
                $par = $this.closest('.wedb-tab'),
                this_id = $this.data('id'),
                $content_target = $('.wedb-tab__content--' + this_id, $par),
                $headers = $('.wedb-tab__header', $par),
                $contents = $('.wedb-tab__content', $par);

            $headers.removeClass('wedb-tab__header--current');
            $contents.removeClass('wedb-tab__content--current');
            $content_target.addClass('wedb-tab__content--current');
            $this.addClass('wedb-tab__header--current');
        });
    });

    // Media uploader
    $(function() {
        $body.on('click', '.wedp__upload_image_button', function() {
            var $this = $(this),
                $wedp_single_com__field = $this.closest('.wedp-single-com__field'),
                item_type = $wedp_single_com__field.data('type');

            console.log(item_type);
            if (item_type == 'media upload') {
                wedp_uploader($wedp_single_com__field, false);
            } else if (item_type == 'multiple media upload') {
                wedp_uploader($wedp_single_com__field, true);
            }
            return false;
        });
    });

    function wedp_uploader($wedp_single_com__field, multiple) {
        var element_name = $('.wedp-single-com__field-label', $wedp_single_com__field).html().replace(/<[^>]+>.*<\/[^>]+>/g,''),
            $wedp_single_com__images = $('.wedp-single-com__images', $wedp_single_com__field), // image preview
            $wedp_single_com__media_upload = $('.wedp-single-com__media-upload', $wedp_single_com__field), // input with ID
            wp_media_post_id = wp.media.model.settings.post.id, // Store the old id
            custom_uploader = null,
            set_to_post_id = $wedp_single_com__media_upload.val();

        wp.media.model.settings.post.id = set_to_post_id;

        //TODO select pictures in wp.media if they were before
        // If the media frame already exists, reopen it.
        if ( custom_uploader ) {
            // Set the post ID to what we want
            custom_uploader.uploader.uploader.param( 'post_id', set_to_post_id );
            // Open frame
            custom_uploader.open();
            return;
        } else {
            // Set the wp.media post id so the uploader grabs the ID we want when initialised
            wp.media.model.settings.post.id = set_to_post_id;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image for ' + element_name,
            button: {
                text: 'Choose Image'
            },
            library : {
                type: 'image'
            },
            multiple: multiple
        });

        custom_uploader.on('select', function () {
            var selection = custom_uploader.state().get('selection'),
                ids = '';

            $wedp_single_com__images.html('');

            selection.map(function (att) {
                var attachment = att.toJSON();

                wedp_get_img(attachment.id, $wedp_single_com__images);
                ids += attachment.id + ',';
                console.log(attachment.id, ids);

                $wedp_single_com__images.append('<div class="wedp-single-com__single-image wedp-single-com__single-image--'+attachment.id+'"><i class="wedp-single-com__delete-img" onclick="wedp_delete_img(this)"></i><div class="wedp-single-com__upload-image" data-id="'+attachment.id+'" onclick="wedp_thumbnail_contain(this)"></div></div>');
            });

            // console.log(77, ids);
            wp.media.model.settings.post.id = ids.slice(0, -1);
            $wedp_single_com__media_upload.val(ids.slice(0, -1));
            // console.log(wp.media.model.settings.post.id);

            wp.media.model.settings.post.id = wp_media_post_id;
        });
        custom_uploader.open();
    }

    function wedp_delete_img(el) {
        var $this = $(el),
            $img_container = $this.closest('.wedp-single-com__single-image'),
            $img = $('.wedp-single-com__upload-image', $img_container),
            image_id = $img.data('id'),
            $wedp_single_com__field = $this.closest('.wedp-single-com__field'),
            $wedp_single_com__media_upload = $('.wedp-single-com__media-upload', $wedp_single_com__field), // input with ID
            ids = $wedp_single_com__media_upload.val(),
            $wedp_single_com__images = $('.wedp-single-com__images', $wedp_single_com__field);

        ids = ids.replace(image_id, '').replace(/\,{2,}/g, ',').replace(/^,|,$/g, '');
        $wedp_single_com__media_upload.val(ids);

        $img_container.remove();

        if (!$wedp_single_com__images.children('.wedp-single-com__single-image').length) {
            $wedp_single_com__images.html('');
            $wedp_single_com__images.append('<div class="wedp-single-com__single-image"><div class="wedp-single-com__upload-image" style="background-image: url('+$wedp_single_com__images.data('src')+'"></div></div>');
        }

        return false;
    }

    function wedp_get_img(id, $par) {
        var data_sent = {
            'action': 'wb-creator',
            'query': 'img_url_by_ID',
            'id': id,
            'size': 'large'
        };

        $.ajax({
            url: wedding_budget.url,
            data: data_sent,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            async: true,
            success: function(data){
                if (data.url) {
                    var $img = $('.wedp-single-com__single-image--'+ id +' .wedp-single-com__upload-image', $par);

                    $img.data('img', data.url);
                    $img.css({
                        'background-image': 'url('+ data.url +')'
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) { // в случае неудачного завершения запроса к серверу
                console.error('gag-rating-@11: '+xhr.status); // покажем ответ сервера
                console.error('gag-rating-@12: '+thrownError); // и текст ошибки
            }
        });
    }

    // Open thumb window
    function wedp_thumbnail_contain(el) {
        var $this = $(el),
            $wedp_modal__content = $('.wedp-modal--thumbnail .wedp-modal__content'),
            link = $this.data('img');

        $wedp_modal__content.html('<img src="'+link+'">');
        wedp_thumbnail_adaptive();
        wedp_open_thumb('thumbnail');
    }
    function wedp_thumbnail_adaptive() {
        var $wedp_modal__content = $('.mm-modal--thumbnail .mm-modal__content img'),
            windowHeight = window.innerHeight,
            windowWidth = window.innerWidth;

        $wedp_modal__content.css({
            "max-height": windowHeight*0.96 +"px",
            "max-width": windowWidth*0.94 + "px"
        });
    }
    function wedp_open_thumb(modal_id) {
        $body.addClass('open-thumb').addClass('open-thumb--' + modal_id);
        $body.data('current-thumb', modal_id);
    }
    // Close modal
    $body.on('click', '.close_thumb_modal', function () {
        wedp_close_thumb();
    });
    // Close current thumbnail
    function wedp_close_thumb() {
        $body.removeClass('open-thumb').removeClass('open-thumb--' + $body.data('current-modal'));
    }

    window.wedp_uploader = wedp_uploader;
    window.wedp_delete_img = wedp_delete_img;
    window.wedp_get_img = wedp_get_img;
    window.wedp_thumbnail_contain = wedp_thumbnail_contain;
    window.wedp_thumbnail_adaptive = wedp_thumbnail_adaptive;
    window.wedp_open_thumb = wedp_open_thumb;
    window.wedp_close_thumb = wedp_close_thumb;

}($ || window.jQuery));