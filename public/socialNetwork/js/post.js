$(function (){
    $('.summernote').summernote({
        codeviewFilter: false,
        codeviewIframeFilter: true,
        lang: 'fr-FR',
        disableResizeImage: true,
        placeholder: 'Alors champion, quoi de neuf ?',
        toolbar: [
            ['style', ['bold', 'italic']],
            ['insert', ['picture']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                sendFile(files[0]);
            }
        },
        popover: {
            image: [],
            link: [],
            air: []
        }
    });
    function sendFile(file) {
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
                var image = $('<img>').attr('src', url);
                $('.summernote').summernote("insertNode", image[0]);
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
                                '<p><a class="profile-link a-none">'+response.name+' </a>'+comment+'</p>'+
                                '<span class="comment-date">'+response.date+'</span>'+
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
        if($('input[name=post]').length > 0){
            $('input[name=post]').html($('input[name=post]').text().replace(/\n\r?/g, '<br />'));
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
                    class_like = '<a class="btn text-green like" data-nb="'+response.numberlike+'"><i class="icon ion-thumbsup"></i> '+response.numberlike+'</a>'+
                        '<a class="btn dislike" data-nb="'+response.numberDislike+'"><i class="fa fa-thumbs-down"></i> '+response.numberDislike+'</a>';
                }else if(response.liked == 2){
                    class_like = '<a class="btn like" data-nb="'+response.numberlike+'"><i class="icon ion-thumbsup"></i> '+response.numberlike+'</a>'+
                    '<a class="btn text-red dislike" data-nb="'+response.numberDislike+'"><i class="fa fa-thumbs-down"></i> '+response.numberDislike+'</a>';
                }else{
                    class_like = '<a class="btn like" data-nb="'+response.numberlike+'"><i class="icon ion-thumbsup"></i> '+response.numberlike+'</a>'+
                        '<a class="btn dislike" data-nb="'+response.numberDislike+'"><i class="fa fa-thumbs-down"></i> '+response.numberDislike+'</a>';
                }
                parent.html('' + class_like

                );
            },
            error : function (){
                alert('Une erreur est survenue')
            }
        })
    });
})
