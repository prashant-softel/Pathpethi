(function () {
    if (typeof faq_plugin_url == "undefined" || faq_plugin_url == null) {
        return;
    }
    tinymce.PluginManager.add('faq_wd', function (editor, url) {
        var category_options = new Array();
        var faq_options = new Array();
        var sh_tag = 'faq_wd';
        var name;
        var cat_ids = "";

        function getAttr(s, n) {
            n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
            return n ? window.decodeURIComponent(n[1]) : '';
        }
        ;

        function html(cls, data) {
            var placeholder = faq_plugin_url + '/images/new_post_icon.png';
            data = window.encodeURIComponent(data);
            return '<img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-sh-attr="' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
        }

        function replaceShortcodes(content) {
            return content.replace(/\[faq_wd([^\]]*)\]/g, function (all, attr) {
                return html('wp-faq_wd', attr);
            });
        }

        function restoreShortcodes(content) {
            //match any image tag with our class and replace it with the shortcode's content and attributes
            return content.replace(/(?:<p(?: [^>]+)?>)*(<img class="mceItem wp-faq_wd" [^>]+>)(?:<\/p>)*/g, function (match, image) {
                var data = getAttr(image, 'data-sh-attr');
                var con = getAttr(image, 'data-sh-content');

                if (data) {
                    return '<p>[' + sh_tag + data + ']</p>';
                }
                return match;
            });
        }


//add popup
        editor.addCommand('faq_wd_popup', function (ui, v, e) {
            var cats = new Array();          
            if (v.cats) {
                cats = v.cats.split(',');
            }
            var faq_expand_answers_value = false;
            if (v.faq_expand_answers_value) {
                faq_expand_answers_value = v.faq_expand_answers_value;
            }
            var faq_search = false;
            if (v.faq_search) {
                faq_search = v.faq_search;
            }
            var faq_category_numbering_value = false;
            if (v.faq_category_numbering_value) {
                faq_category_numbering_value = v.faq_category_numbering_value;
            }

            var faq_like_value = false;
            if (v.faq_like_value) {
                faq_like_value = v.faq_like_value;
            }
            var faq_hits_value = false;
            if (v.faq_hits_value) {
                faq_hits_value = v.faq_hits_value;
            }
            var faq_user_value = false;
            if (v.faq_user_value) {
                faq_user_value = v.faq_user_value;
            }
            var faq_date_value = false;
            if (v.faq_date_value) {
                faq_date_value = v.faq_date_value;
            }
            var category_show_description_value = false;
            if (v.category_show_description_value) {
                category_show_description_value = v.category_show_description_value;
            }
            var category_show_title_value = false;
            if (v.category_show_title_value) {
                category_show_title_value = v.category_show_title_value;
            }
            //open the popup
            var cat_body = new Array();
            var category_options = new Array();
            var faq_options = new Array();
            cat_body[0] = {type: 'label',
                name: 'select_category',
                text: 'Select Categories'
                    };
            var i;            
            for (i=1; i < categories.length + 1; i++) {                
                var checked = false;
                if (inArray(categories[i-1].id, cats)) {
                    checked = true;
                }
                cat_body[i] = {
                    type: 'checkbox',
                    name: categories[i-1].id,
                    label: categories[i-1].name,
                    value: categories[i-1].id,
                    checked: checked
                };
            }
            //category

            category_options[0] =
            {
                type: 'checkbox',
                name: 'category_show_title',
                        label: 'Show Categories:',
                checked: category_show_title_value
            };
            category_options[1] =
            {
                type: 'checkbox',
                name: 'category_show_description',
                label: 'Show Description:',
                checked: category_show_description_value
            };
            category_options[2] =
            {
                type: 'checkbox',
                name: 'faq_category_numbering',
                label: 'Category Numbering:',
                checked: faq_category_numbering_value
            };

            //faq
            faq_options[0] =
            {
                type: 'checkbox',
                name: 'faq_like',
                        label: 'Show Like:',
                checked: faq_like_value
            };
            faq_options[1] =
            {
                type: 'checkbox',
                name: 'faq_hits',
                        label: 'Show Hits:',
                checked: faq_hits_value
            };
            faq_options[2] =
            {
                type: 'checkbox',
                name: 'faq_date',
                        label: 'Show Date:',
                checked: faq_date_value
            };
            faq_options[3] =
            {
                type: 'checkbox',
                name: 'faq_user',
                        label: 'Show User:',
                checked: faq_user_value
            };
            faq_options[4] =
            {
                type: 'checkbox',
                name: 'faq_expand_answers',
                label: 'Expand All Answers After The Page Is Loaded:',
                checked: faq_expand_answers_value
            };
            faq_options[5] =
            {
                type: 'checkbox',
                name: 'faq_search',
                label: 'Search:',
                checked: faq_search
            };


            var win = editor.windowManager.open(
                {
                    title: 'FAQ WD',
                    bodyType: 'tabpanel',
                    body: [
                        {
                            title: 'Categories',
                            type: "form",
                            layout: 'flex',
                            direction: 'column',
                            align: 'stretch',
                            items: cat_body
                        },
                        {
                            title: 'Category options',
                            type: "form",
                            layout: 'flex',
                            direction: 'column',
                            align: 'stretch',
                            items: category_options
                        },
                        {
                            title: 'FAQ options',
                            type: "form",
                            layout: 'flex',
                            direction: 'column',
                            align: 'stretch',
                            items: faq_options
                        }
                    ],
                    onsubmit: function (e) { //when the ok button is clicked
                        var data = win.toJSON();
                        var tt = false;
                            for (var i = 1; i <= categories.length; i++) {
                            name = cat_body[i].name;
                            if (data[name]) {
                                cat_ids += cat_body[i].value + ",";
                            }
                        }
                        cat_ids = cat_ids.substring(0, cat_ids.length - 1);
                            if (!cat_ids) {
                            alert("Please select category");                            
                            return false;
                        }
                        var shortcode_str = '[' + sh_tag + ' cat_ids="' + cat_ids + '"';
                        if (data["faq_expand_answers"]) {
                            shortcode_str += ' faq_expand_answers="true"';
                        }


                        if (data["faq_category_numbering"]) {
                            shortcode_str += ' faq_category_numbering="true"';
                        }
                        if (data["category_show_description"]) {
                            shortcode_str += ' category_show_description="true"';
                        }
                        if (data["category_show_title"]) {
                            shortcode_str += ' category_show_title="true"';
                        }

                        if (data["faq_like"]) {
                            shortcode_str += ' faq_like="true"';
                        }
                        if (data["faq_hits"]) {
                            shortcode_str += ' faq_hits="true"';
                        }
                        if (data["faq_user"]) {
                            shortcode_str += ' faq_user="true"';
                        }
                        if (data["faq_date"]) {
                            shortcode_str += ' faq_date="true"';
                        }
                        if (data["faq_search"]) {
                            shortcode_str += ' faq_search="true"';
                        }

                        shortcode_str += ']';                        
                        cat_ids = "";
                        editor.insertContent(shortcode_str);
                    }
                });
        });

//add button
        editor.addButton('faq_wd', {
            icon: 'faq_wd',
            tooltip: 'Select Category',
            image: faq_plugin_url + '/images/insert-icon.png',
            onclick: function () {
                editor.execCommand('faq_wd_popup', '', {
                    cats: '',
                    faq_expand_answers_value: '',
                    faq_search: 'true',
                    faq_category_numbering_value: 'true',
                    faq_like_value: 'true',
                    faq_hits_value: 'true',
                    faq_user_value: 'true',
                    faq_date_value: 'true',
                    category_show_description_value: 'true',
                    category_show_title_value: 'true'
                });
            }
        });

        //replace from shortcode to an image placeholder
        editor.on('BeforeSetcontent', function (event) {
            event.content = replaceShortcodes(event.content);
        });

        //replace from image placeholder to shortcode
        editor.on('GetContent', function (event) {
            event.content = restoreShortcodes(event.content);
        });
        //open popup on placeholder double click
        editor.on('DblClick', function (e) {
            var cls = e.target.className.indexOf('wp-faq_wd');
            if (e.target.nodeName == 'IMG' && e.target.className.indexOf('wp-faq_wd') > -1) {
                var title = e.target.attributes['data-sh-attr'].value;
                title = window.decodeURIComponent(title);
                editor.execCommand('faq_wd_popup', '', {
                    cats: getAttr(title, 'cat_ids'),
                    faq_expand_answers_value: getAttr(title, 'faq_expand_answers'),
                    faq_category_numbering_value: getAttr(title, 'faq_category_numbering'),
                    faq_like_value: getAttr(title, 'faq_like'),
                    faq_hits_value: getAttr(title, 'faq_hits'),
                    faq_user_value: getAttr(title, 'faq_user'),
                    faq_date_value: getAttr(title, 'faq_date'),
                    faq_search: getAttr(title, 'faq_search'),
                    category_show_description_value: getAttr(title, 'category_show_description'),
                    category_show_title_value: getAttr(title, 'category_show_title')
                });
            }
        });
    });
})();
function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle)
            return true;
    }
    return false;
}