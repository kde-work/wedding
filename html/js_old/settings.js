(function ($) {
    if (!$) {
        console.error('jQuery and $ not exist');
        return;
    }

    // Delete image Btn
    $(function () {
        var $box__delete = $('.box__delete');

        $box__delete.on('click', function () {
            var $this = $(this),
                this_id = $this.data('id'),
                $box__icons = $this.closest('.box__icons'),
                $box__has_image__1 = $('.box__has-image--1', $box__icons),
                $box__has_image__2 = $('.box__has-image--0', $box__icons);

            if (!confirm('Are you sure you want to delete this image? This will be saved.')) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: wedding_budget.url,
                async: false,
                dataType: 'TEXT',
                data: {
                    'action': 'wb_settings_delete_img',
                    'id': this_id
                },
                success: function (data) {
                    console.log(data);
                    $box__has_image__1.hide();
                    $box__has_image__2.show();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('wb_settings_delete_img-@21: '+xhr.status);
                    console.log('wb_settings_delete_img-@22: '+thrownError);
                }
            });
        });
    });

    // Word with Drag and Drop and Ajax upload
    $(function () {
        // feature detection for drag&drop upload

        var isAdvancedUpload = function()
        {
            var div = document.createElement( 'div' );
            return ( ( 'draggable' in div ) || ( 'ondragstart' in div && 'ondrop' in div ) ) && 'FormData' in window && 'FileReader' in window;
        }();


        // applying the effect for the form
        $('.wb-settings__form').each( function()
        {
            var $this        = $(this),
                url		     = wedding_budget.url,
                $form		 = $this,
                $wb__dad1     = $('.wb-settings__dad--1', $form),
                $wb__dad2     = $('.wb-settings__dad--2', $form),
                $box1		 = $('.box', $wb__dad1),
                $box2		 = $('.box', $wb__dad2),
                $body		 = $('body'),
                $input1		 = $('.box__input input[type="file"]', $wb__dad1),
                $input2		 = $('.box__input input[type="file"]', $wb__dad2),
                $errorMsg	 = $('.box__error span', $this),
                droppedFiles1 = false,
                droppedFiles2 = false,
                showFiles	 = function(files, $this)
                {
                    var $input = $('.box__input input[type="file"]', $this),
                        $label = $('.box__input label', $this);
                    $label.text( files.length > 1 ? ( $input.attr( 'data-multiple-caption' ) || '' ).replace('{count}', files.length ) : files[ 0 ].name );
                };

            // letting the server side to know we are going to make an Ajax request
            $form.append( '<input type="hidden" name="ajax" value="1" />' );

            // automatically submit the form on file select
            $input1.on( 'change', function( e )
            {
                showFiles( e.target.files, $wb__dad1 );
            });
            $input2.on( 'change', function( e )
            {
                showFiles( e.target.files, $wb__dad2 );
            });


            // drag&drop files if the feature is available
            if( isAdvancedUpload )
            {
                $wb__dad1
                    .addClass( 'has-advanced-upload' ) // letting the CSS part to know drag&drop is supported by the browser
                    .on( 'drag dragstart dragend dragover dragenter dragleave drop', function( e )
                    {
                        // preventing the unwanted behaviours
                        e.preventDefault();
                        e.stopPropagation();
                    })
                    .on( 'dragover dragenter', function() //
                    {
                        $wb__dad1.addClass( 'is-dragover' );
                    })
                    .on( 'dragleave dragend drop', function()
                    {
                        $wb__dad1.removeClass( 'is-dragover' );
                    })
                    .on( 'drop', function( e )
                    {
                        droppedFiles1 = e.originalEvent.dataTransfer.files; // the files that were dropped
                        showFiles( droppedFiles1, $wb__dad1 );
                    });

                $wb__dad2
                    .addClass( 'has-advanced-upload' ) // letting the CSS part to know drag&drop is supported by the browser
                    .on( 'drag dragstart dragend dragover dragenter dragleave drop', function( e )
                    {
                        // preventing the unwanted behaviours
                        e.preventDefault();
                        e.stopPropagation();
                    })
                    .on( 'dragover dragenter', function() //
                    {
                        $wb__dad2.addClass( 'is-dragover' );
                    })
                    .on( 'dragleave dragend drop', function()
                    {
                        $wb__dad2.removeClass( 'is-dragover' );
                    })
                    .on( 'drop', function( e )
                    {
                        droppedFiles2 = e.originalEvent.dataTransfer.files; // the files that were dropped
                        showFiles( droppedFiles2, $wb__dad2 );
                    });
            }

            // if the form was submitted
            $form.on( 'submit', function( e )
            {
                $body.removeClass('is-error');

                // preventing the duplicate submissions if the current one is in progress
                if($body.hasClass('is-uploading')) return false;

                if(isAdvancedUpload) // ajax file upload for modern browsers
                {
                    e.preventDefault();

                    // gathering the form data
                    var ajaxData = new FormData($form.get(0));
                    if(droppedFiles1)
                    {
                        $.each(droppedFiles1, function(i, file)
                        {
                            ajaxData.append($input1.attr('name'), file);
                        });
                    } else {
                        // if (
                        //     $('#file_1', $wb__dad1).val().length == 0
                        //     || $('#file_2', $wb__dad2).val().length == 0
                        // ) {
                        //     if ($('.wb__img-upload--1').length == 0 || $('.wb__img-upload--2').length == 0) {
                        //         $body.addClass('is-error');
                        //         return false;
                        //     }
                        // }
                    }
                    if(droppedFiles2)
                    {
                        $.each(droppedFiles2, function(i, file)
                        {
                            ajaxData.append($input2.attr('name'), file);
                        });
                    }

                    $body.addClass('is-uploading').removeClass('is-error');

                    // ajax request
                    $.ajax(
                        {
                            url: 			url,
                            type:           'POST',
                            data: 			ajaxData,
                            dataType:		'json',
                            // dataType:		'text',
                            cache:			false,
                            contentType:	false,
                            processData:	false,
                            complete: function()
                            {
                                $body.removeClass( 'is-uploading' );
                            },
                            success: function( data )
                            {
                                console.log(data);
                                var id = parseInt(data.status, 10);
                                if (isNaN(id)) {
                                    $body.addClass('is-error');
                                } else {
                                    $body.addClass('is-success');
                                    var $box__icons1 = $('.box__image--1'),
                                        $box__icons2 = $('.box__image--2');

                                    if (data.img1) {
                                        var $box__has_image__1 = $('.box__has-image--1', $wb__dad1),
                                            $box__has_image__2 = $('.box__has-image--0', $wb__dad1);

                                        $box__has_image__1.show();
                                        $box__has_image__2.hide();
                                        $box__icons1.html('<img src="'+data.img1+'" class="wb__img-upload wb__img-upload--1">');
                                    }
                                    if (data.img2) {
                                        var $box__has_image__1 = $('.box__has-image--1', $wb__dad2),
                                            $box__has_image__2 = $('.box__has-image--0', $wb__dad2);

                                        $box__has_image__1.show();
                                        $box__has_image__2.hide();
                                        $box__icons2.html('<img src="' + data.img2 + '" class="wb__img-upload wb__img-upload--2">');
                                    }
                                    // location.href = window.location.protocol + '//'+document.domain+'/?p=' + id;
                                }
                                $body.addClass( data.status == true ? 'is-success' : 'is-error' );
                                if( !data.success ) $errorMsg.text( data.error );
                            },
                            error: function (xhr, ajaxOptions, thrownError) { // в случае неудачного завершения запроса к серверу
                                console.error('tvgag-post-submit-error-@11: '+xhr.status); // покажем ответ сервера
                                console.error('tvgag-post-submit-error-@12: '+thrownError); // и текст ошибки
                            }
                        });
                }
                else // fallback Ajax solution upload for older browsers
                {
                    var iframeName	= 'uploadiframe' + new Date().getTime(),
                        $iframe		= $( '<iframe name="' + iframeName + '" style="display: none;"></iframe>' );

                    $( 'body' ).append( $iframe );
                    $form.attr( 'target', iframeName );

                    $iframe.one( 'load', function()
                    {
                        var data = $.parseJSON( $iframe.contents().find( 'body' ).text() );
                        $form.removeClass( 'is-uploading' ).addClass( data.success == true ? 'is-success' : 'is-error' ).removeAttr( 'target' );
                        if( !data.success ) $errorMsg.text( data.error );
                        $iframe.remove();
                    });
                }
            });
        });
    });

}($ || window.jQuery));
// end of file