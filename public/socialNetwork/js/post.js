$(function (){
    $('.create-post textarea').text('');
    $('.summernote').summernote({
        codeviewFilter: false,
        codeviewIframeFilter: true,
        lang: 'fr-FR',
        height: 200,
        disableResizeEditor: true,
        disableResizeImage: true,
        placeholder: 'Alors champion, quoi de neuf ?',
        toolbar: [
            ['style', ['bold', 'italic']],
            ['insert', ['picture']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                sendFile(files[0], $(this));
            }
        },
        popover: {
            image: [],
            link: [],
            air: []
        }
    });
    function sendFile(file, summernote) {
        data = new FormData();
        data.append("file", file);
        $('#loading').css('display','inline-block');
        $.ajax({
            data: data,
            type: "POST",
            url: url_post_img,
            cache: false,
            contentType: false,
            processData: false,
            success: function(url) {
                var radom = Math.floor(Math.random() * 99999999) + 9999;
                var image = $('<img style="cursor: pointer" data-toggle="modal" data-target="#img'+radom+'">');
                image.attr('src', url);
                var modalImage = $('<div class="modal fade" id="img'+radom+'" role="dialog" aria-hidden="true">\n' +
                    '                        <div class="modal-dialog modal-lg">\n' +
                    '                          <div class="modal-content">\n' +
                    '                            <div class="post-content">\n' +
                    '                              <div class="post-container">\n' +
                    '                                <img src="'+url+'" alt="post-image" class="img-responsive post-image" />\n' +
                    '                              </div>\n' +
                    '                            </div>\n' +
                    '                          </div>\n' +
                    '                        </div>\n' +
                    '                      </div>');

                var modalImage = $('<div class="modal fade" id="img'+radom+'" role="dialog" aria-hidden="true">\n' +
                    '                        <div class="modal-dialog modal-lg">\n' +
                    '                          <div class="modal-content">\n' +
                    '                            <div class="modal-header">\n' +
                    '                                <h5 class="modal-title"></h5>\n' +
                    '                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
                    '                                    <span aria-hidden="true">&times;</span>\n' +
                    '                                </button>\n' +
                    '                           </div>\n' +
                    '                            <div class="modal-body">\n' +
                    '                                <img src="'+url+'" alt="post-image" class="img-responsive post-image" />\n' +
                    '                            </div>\n' +
                    '                          </div>\n' +
                    '                        </div>\n' +
                    '                      </div>');
                // summernote.summernote("insertNode", image[0]);
                $('#img-note-editable').html(image);
                $('#img-note-editable').show();
                $('#img-note-editable-input').val(url);
                $('#img-note-editable-random').val(radom);
                summernote.summernote("insertNode", modalImage[0]);
                $('#loading').hide();
            }
        });
    }

    $('.input-post-comment').on('keypress',function(e) {
        var input_post_comment = $(this);
        var comment = $(this).val();
        var post = $(this).attr('data-post');
        if(e.which == 13) {
            if(comment.length > 0){
                $.ajax({
                    url : url_add_comment,
                    type : "POST",
                    data: {
                        "comment":comment,
                        "post_id": post
                    },
                    success : function (serverResponse){
                        var response = JSON.parse(serverResponse);
                        if(response.result == true){
                            $('.all-comment-'+post).prepend('<div class="post-comment">' +
                                '<i class="fa fa-close remove-comment" data-comment="'+response.id+'" style="color:red; cursor: pointer"></i>' +
                                '<img src="'+response.path+'" alt="" class="profile-photo-sm">' +
                                '<a class="profile-link a-none">'+response.name+' <span class="comment-date">'+response.date+'</span></a><p>'+comment+'</p>'+
                                '</div>');
                            input_post_comment.val('');
                            $('.all-comment-'+post).animate({ scrollTop: 0 }, "fast");
                        }
                    },
                    error : function (){
                        alert('Une erreur est survenue')
                    }
                })
            }else{
                alert('Veuillez renseigner le commentaire')
            }

        }
    });

    $('body').on('click', '.remove-comment', function(e) {
        var comment = $(this);
        var comment_id = $(this).attr('data-comment');
        $.ajax({
            url : url_remove_comment,
            type : "POST",
            data: {
                "comment_id":comment_id
            },
            success : function (serverResponse){
                var response = JSON.parse(serverResponse);
                if(response.result == true){
                    comment.parent().remove();
                }
            },
            error : function (){
                alert('Une erreur est survenue')
            }
        })
    });

    $('#publish').click(function (e){
        e.preventDefault();
        if($('textarea[name=post]').length > 0){
            $('textarea[name=post]').html($('textarea[name=post]').text().replace(/\n\r?/g, '<br />'));
            $('#post-form').submit();
        }else{
            alert('Veuillez saisir un contenu avant de publier');
        }

    });

    $('body').on('click','.reaction a',function (){
        var parent = $(this).parent('.reaction');
        var postid = parent.attr('data-post');
        var reactionlink = $(this);
        var like = null;
        if($(this).hasClass('like')){
            like = 1;
        }else if($(this).hasClass('dislike')){
            like = 2;
        }
        $.ajax({
            url : url_post_like,
            type : "POST",
            data: {
                "like":like,
                "post_id": postid
            },
            success : function (serverResponse){
                var response = JSON.parse(serverResponse);
                var class_like = '';
                if(response.liked == 1){
                    class_like = '<a class="btn text-green like" data-nb="'+response.numberlike+'"><i class="icon ion-thumbsup"></i> '+response.numberlike+'</a>'
                        // '<a class="btn dislike" data-nb="'+response.numberDislike+'"><i class="fa fa-thumbs-down"></i> '+response.numberDislike+'</a>'
                    ;
                }else if(response.liked == 2){
                    class_like = '<a class="btn like" data-nb="'+response.numberlike+'"><i class="icon ion-thumbsup"></i> '+response.numberlike+'</a>'+
                    '<a class="btn text-red dislike" data-nb="'+response.numberDislike+'"><i class="fa fa-thumbs-down"></i> '+response.numberDislike+'</a>';
                }else{
                    class_like = '<a class="btn like" data-nb="'+response.numberlike+'"><i class="icon ion-thumbsup"></i> '+response.numberlike+'</a>'
                        // '<a class="btn dislike" data-nb="'+response.numberDislike+'"><i class="fa fa-thumbs-down"></i> '+response.numberDislike+'</a>'
                    ;
                }
                parent.html('' + class_like

                );
            },
            error : function (){
                alert('Une erreur est survenue')
            }
        })
    });

    $('.post-text').each(function (){
        if($(this).find('img').length == 0){
            $(this).css('overflow-y','auto');
        }
    })
})
