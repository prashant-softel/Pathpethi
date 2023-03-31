(function ($) {
    setTimeout(function () {
        jQuery('.faqwd_conteiner').each(function (k, v) {
            jQuery(this).find('.faqwd_cat_desc').hide();
            if (jQuery(this).find('.faqwd_categories').attr("class") == "faqwd_categories hidden") {
                jQuery(this).find(".faqwd_cat").show();
            } else {
                jQuery(this).find('.faqwd_categories_li:first').addClass("faqwd_cat_current");
                var current_id =jQuery(this).find('.faqwd_categories_li:first').data('catid');
                jQuery(this).find('.faqwd_cat_desc_' + current_id).show();
                jQuery(this).find('.faqwd_questions .faqwd_cat_' + current_id).show();
            }
            if (jQuery(this).find(".faqwd_question_li").hasClass('expanded')) {
                jQuery(this).find(".faqwd_question_title_container").each(function () {
                   $(this).addClass('opened');
                });
            } else {
                jQuery(this).find(".faqwd_question_li .faqwd_question_content").hide();
            }
            expand_collapse('text', $(this));

        });

    }, 500);


    jQuery(".faqwd_categories_li").live("click", function () {
        $(this).closest('.faqwd_conteiner').find('.faqwd_categories_li').removeClass("faqwd_cat_current");
        $(this).addClass("faqwd_cat_current");
        $(this).closest('.faqwd_conteiner').find('.faqwd_cat').hide();
        $(this).closest('.faqwd_conteiner').find('.faqwd_cat_desc').hide();
        var cat_id = $(this).attr('class');
        cat_id = cat_id.split(" ")[1];
        var id = cat_id.split("_")[3];
        var quest_class = ".faqwd_cat_" + id;
        var cat_desc_class = ".faqwd_cat_desc_" + id;
        $(this).closest('.faqwd_conteiner').find(quest_class).show();
        $(this).closest('.faqwd_conteiner').find(cat_desc_class).show();
        expand_collapse('text', $(this));
    });


    jQuery('.faqwd_question_li .faqwd_question_title_container').live("click", function () {
        var content_class = ".faqwd_question_" + $(this).data('faqid');
        if ($(this).closest('.faqwd_questions_ul').find(content_class).is(':visible')) {
            $(this).closest('.faqwd_questions_ul').find(content_class).slideUp("slow");
            jQuery(this).removeClass('opened');
        }
        else {
            $(this).closest('.faqwd_questions_ul').find(content_class).slideDown("slow");
            jQuery(this).addClass('opened');
        }
        expand_collapse('text', $(this));
    });

    jQuery('.faqwd_expand').live("click", function () {
        $(this).closest('.faqwd_conteiner ').find('.faqwd_question_content').slideDown("slow");
        $(this).closest('.faqwd_conteiner ').find(".faqwd_question_title_container").each(function () {
            jQuery(this).addClass('opened');
        });
        expand_collapse('faqwd_expand', $(this));
    });
    jQuery('.faqwd_collapse').live("click", function () {
        $(this).closest('.faqwd_conteiner ').find('.faqwd_question_content').slideUp("slow");
        $(this).closest('.faqwd_conteiner ').find(".faqwd_question_title_container").each(function () {
            jQuery(this).removeClass('opened');
        });
        expand_collapse('faqwd_collapse', $(this));
    });


    function expand_collapse(text, el) {
        $(el).closest('.faqwd_conteiner ').find('.faqwd_expand').next('span').hide();
        if (text == 'faqwd_collapse') {
            $(el).closest('.faqwd_conteiner ').find('.faqwd_expand').show();
            $(el).closest('.faqwd_conteiner ').find('.faqwd_collapse').hide();            
            return 0;
        }
        if (text == 'faqwd_expand') {
            $(el).closest('.faqwd_conteiner ').find('.faqwd_collapse').show();            
            $(el).closest('.faqwd_conteiner ').find('.faqwd_expand').hide();     
            
            return 0;
        }

        var collapse = "no";
        var expand = "no";

        var current = el.closest('.faqwd_conteiner').find('.faqwd_cat_current').attr('class');
        var cat_id =jQuery("."+current+"").data('catid');
        el.closest('.faqwd_conteiner').find(".faqwd_questions " + cat_id + " .faqwd_question_title_container").each(function () {
            if ($(this).attr('class') == 'faqwd_question_title_container opened') {

            }
        });

        $(el).closest('.faqwd_conteiner').find(".faqwd_question_title_container ").each(function () {
            if (jQuery(this).attr('class') == "faqwd_question_title_container opened" && collapse == "no") {
                collapse = "yes";
            }
            if (jQuery(this).attr('class') == "faqwd_question_title_container" && expand == "no") {
                expand = "yes";
            }
        });
        if (collapse == "yes") {
            $(el).closest('.faqwd_conteiner ').find('.faqwd_collapse').show();
        }
        else {
            $(el).closest('.faqwd_conteiner ').find('.faqwd_collapse').hide();
        }
        if (expand == "yes") {
            $(el).closest('.faqwd_conteiner ').find('.faqwd_expand').show();
        }
        else {
            $(el).closest('.faqwd_conteiner ').find('.faqwd_expand').hide();
        }        
        if (collapse == "yes" && expand == "yes") {                        
            $(el).closest('.faqwd_conteiner ').find('.faqwd_expand').next('span').show();
        }
    }


    jQuery(document).on('click', '.faqwd_search_button', function (e) {
        faqwd_search($(this));
    });
    jQuery(document).on('keyup', '.faqwd_search_input', function (e) {
        if (e.keyCode == 13) {
            faqwd_search($(this));
        }
    });

    function faqwd_search(el) {

        var search, t1, t2, text, quest_id;
        var show_question = [];
        search = el.closest('.faqwd_conteiner').find('.faqwd_search_input').val();
        var search_text = new RegExp(search, "gi");
        var Exp = /^[a-z\d\-_\s]+$/gi;
        el.closest('.faqwd_conteiner').find('.faqwd_search_resulte,.faqwd_no_result, .faqwd_search_resulte').hide();
        if (search.length == 0 && !el.closest('.faqwd_conteiner').find('.faqwd_categories_ul').is(':visible')) {

            el.closest('.faqwd_conteiner').find('.faqwd_categories').show();
            el.closest('.faqwd_conteiner').find('.faqwd_cat,  .faqwd_cat_desc').hide();
            var cat_desc_class = el.closest('.faqwd_conteiner').find('.faqwd_cat:first').attr('class');
            cat_desc_class = ".faqwd_cat_desc_" + cat_desc_class.split("faqwd_cat_")[1].split(" ")[0] + ":first";
            el.closest('.faqwd_conteiner').find('.faqwd_cat:first').show();
            el.closest('.faqwd_conteiner').find(cat_desc_class).show();
            el.closest('.faqwd_conteiner').find('.faqwd_categories_li').each(function () {
                $(this).removeClass('faqwd_cat_current');
            });
            el.closest('.faqwd_conteiner').find('.faqwd_categories_li:first').addClass('faqwd_cat_current');
            el.closest('.faqwd_conteiner').find(".faqwd_question_li").each(function (k, v) {
                jQuery($(this)).show();
            });
            return;
        }


        if (search.length > 0 && search.match(Exp)) {

            el.closest('.faqwd_conteiner').find('.faqwd_quest_numbering').html("");
            el.closest('.faqwd_conteiner').find('.faqwd_categories, .faqwd_cat_desc').hide();
            el.closest('.faqwd_conteiner').find('.faqwd_questions .faqwd_cat').show();
            el.closest('.faqwd_conteiner').find('.faqwd_question_li').show();

            el.closest('.faqwd_conteiner').find('.faqwd_question_title_container').each(function () {
                t1 = ($(this).text()).search(search_text);
                text = el.closest('.faqwd_conteiner').find(".faqwd_question_" + $(this).attr("data-faqid") + " .faqwd_answer_container .faqwd_answer").text();
                t2 = text.search(search_text);
                if (t1 < 0 && t2 < 0) {
                    el.closest('.faqwd_conteiner').find(".faqwd_qustion_li_" + ($(this).attr("data-faqid"))).hide();
                }
                else {
                    quest_id = $(this).attr('data-faqid').split('_')[1];
                    if (show_question[quest_id] == 1) {
                        el.closest('.faqwd_conteiner').find(".faqwd_qustion_li_" + ($(this).attr("data-faqid"))).hide();
                    }
                    else {
                        show_question[quest_id] = [1];
                    }
                }
            })
            if (el.closest('.faqwd_conteiner').find(".faqwd_question_li:visible").length == 0) {
                $('.faqwd_expand_collapse').hide();
                var msg = '<span class="faqwd_no_result">Nothing has been found</span>';
                el.closest('.faqwd_conteiner').find('.faqwd_questions').append(msg);
            } else {
                var msg = '<div class="faqwd_search_resulte">Search result</div>';
                el.closest('.faqwd_conteiner').find('.faqwd_questions').prepend(msg);
            }
        }
        if (search.length > 0 && !search.match(Exp)) {
            $('.faqwd_expand_collapse, .faqwd_categories, .faqwd_cat ').hide();
            var msg = '<div class="faqwd_search_resulte">Search key must contain only letters and numbers</div>';
            el.closest('.faqwd_conteiner').find('.faqwd_questions').prepend(msg);
        }
    }


}(jQuery));



