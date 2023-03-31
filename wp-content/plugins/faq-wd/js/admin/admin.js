(function ($) {
    jQuery(".faqwd_sotable_ul").sortable({
        update: function () {
            var order = [];
            $('#sortable').children().each(function (i) {
                order.push($(this).attr('id'));
            });
            jQuery.post(
                    ajaxurl, {
                        'action': 'save_categories_order',
                        'cats': order.join(',')
                    },
            function (response) {
                var result = jQuery.parseJSON(response);
                if (jQuery('#faqwd_msg').length > 0) {
                    jQuery('#faqwd_msg').remove();
                }
                if (result.status == 'ok') {
                    $('#sortable').before($('<div id="faqwd_msg" class="faqwd-success">' + result.msg + '</div>')).delay(2000).queue(function (n) {
                        $('#faqwd_msg').fadeOut('slow').remove();
                        n();
                    });
                } else {
                    $('#sortable').before($('<div id="faqwd_msg" class="faqwd-error">' + result.msg + '</div>'));
                }


            }
            );
        }
    });
    $(document).ready(function () {
        var search = window.location.search;
        var arr = search.split('post_type=');
        arr = arr[1];
        if (typeof arr != 'undefined' && arr.split('&')[0] == "faq_wd") {
            $('table.posts #the-list').sortable({
                'items': 'tr',
                'axis': 'y',
                'update': function (e, ui) {
                    var order = [];
                    $('#the-list').children().each(function (i) {
                        var id = $(this).attr('id');
                        id = id.split('-');
                        order.push(id[1]);
                    });
                    $.post(ajaxurl, {
                        action: 'faqwd_sotable',
                        order: order.join(',')
                    });
                }
            });

            $('table.tags #the-list').sortable({
                'items': 'tr',
                'axis': 'y',
                'update': function (e, ui) {
                    var order = [];
                    $('#the-list').children().each(function (i) {
                        var id = $(this).attr('id');
                        id = id.split('-');
                        order.push(id[1]);
                    });
                    $.post(ajaxurl, {
                        action: 'faqwd_category_sotable',
                        order: order.join(',')
                    });
                }
            });
        }

    });

    $('.uninstall_button').click(function () {
        if (confirm("You Are About To Uninstall Spider FAQ From WordPress.\nThis Action Is Not Reversible.\n\n Choose [Cancel] To Stop, [OK] To Uninstall.")) {
            $("#uninstall_form").submit();
        } else {
            
        }
    });
    
    var taxonomy = 'faq_category';
    $('#' + taxonomy + 'checklist li :checkbox, #' + taxonomy + 'checklist-pop :checkbox').live( 'click', function(){
        var t = $(this), c = t.is(':checked'), id = t.val();
        $('#in-' + taxonomy + '-' + id + ', #in-popular-' + taxonomy + '-' + id).prop( 'checked', c );
    });
    

}(jQuery));

 