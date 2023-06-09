(function ($) {
	jQuery('.faqwd_useful').live("click", function(){
		var id = $(this).closest('.faqwd_vote_option').data('faqid');
		if(id){
			faq_wd_vote(id, 'useful');
		}
	});
	
	jQuery('.faqwd_non_useful').live("click", function(){
		var id = $(this).closest('.faqwd_vote_option').data('faqid');
		if(id){
			faq_wd_vote(id, 'non_useful');
		}
	});
	jQuery('.faqwd_question_title_container').live("click", function(){
		var faqid = $(this).data('faqid');
        var ques_class = '.faqwd_question_'+faqid;
        if( ! $(this).closest('.faqwd_question_li').find(ques_class).is(':visible') ) {
                faq_wd_vote(ques_class.split("_")[3], 'hits');
        }
	});
function faq_wd_vote(id,type){
		jQuery.post(
			faqwd.ajaxurl,
				{
				action: 'faq_wd_vote',
				post_id :id,
				type: type
				},
				function(response){
					var useful_id = "span.faqwd_count_useful_" + id;
					var non_useful_id = "span.faqwd_count_non_useful_" + id;
					var hits_id = 'span.faqwd_count_hits_' + id;
					var result = jQuery.parseJSON(response);
					if(result.hits){
						$(hits_id).html(result.hits);
					}
					else{
						$(useful_id).html(result.useful);
						$(non_useful_id).html(result.non_useful);
                        $('.faqwd_vote_option').each(function(){
                            if($(this).attr('data-faqid') == id){
                                var attr = $(this).find('.faqwd_useful').attr('title');
                                if (typeof attr == typeof undefined || attr == false) {
                                    $(this).find('.faqwd_useful').attr('title', 'You have already voted.');
                                    $(this).find('.faqwd_non_useful').attr('title', 'You have already voted.');
                                }
                            }
                        });
					}
				}
		);
}
}(jQuery));