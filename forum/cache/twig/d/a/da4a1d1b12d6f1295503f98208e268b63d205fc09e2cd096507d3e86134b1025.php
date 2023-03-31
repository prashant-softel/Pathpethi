<?php

/* acp_forums.html */
class __TwigTemplate_da4a1d1b12d6f1295503f98208e268b63d205fc09e2cd096507d3e86134b1025 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $location = "overall_header.html";
        $namespace = false;
        if (strpos($location, '@') === 0) {
            $namespace = substr($location, 1, strpos($location, '/') - 1);
            $previous_look_up_order = $this->env->getNamespaceLookUpOrder();
            $this->env->setNamespaceLookUpOrder(array($namespace, '__main__'));
        }
        $this->loadTemplate("overall_header.html", "acp_forums.html", 1)->display($context);
        if ($namespace) {
            $this->env->setNamespaceLookUpOrder($previous_look_up_order);
        }
        // line 2
        echo "
<a id=\"maincontent\"></a>

";
        // line 5
        if ((isset($context["S_EDIT_FORUM"]) ? $context["S_EDIT_FORUM"] : null)) {
            // line 6
            echo "
\t<script type=\"text/javascript\">
\t// <![CDATA[
\t\t/**
\t\t* Handle displaying/hiding several options based on the forum type
\t\t*/
\t\tfunction display_options(value)
\t\t{
\t\t\t";
            // line 14
            if (( !(isset($context["S_ADD_ACTION"]) ? $context["S_ADD_ACTION"] : null) && (isset($context["S_FORUM_ORIG_POST"]) ? $context["S_FORUM_ORIG_POST"] : null))) {
                // line 15
                echo "\t\t\t\tif (value == ";
                echo (isset($context["FORUM_POST"]) ? $context["FORUM_POST"] : null);
                echo ")
\t\t\t\t{
\t\t\t\t\tphpbb.toggleDisplay('type_actions', -1);
\t\t\t\t}
\t\t\t\telse
\t\t\t\t{
\t\t\t\t\tphpbb.toggleDisplay('type_actions', 1);
\t\t\t\t}
\t\t\t";
            }
            // line 24
            echo "
\t\t\t";
            // line 25
            if ((( !(isset($context["S_ADD_ACTION"]) ? $context["S_ADD_ACTION"] : null) && (isset($context["S_FORUM_ORIG_CAT"]) ? $context["S_FORUM_ORIG_CAT"] : null)) && (isset($context["S_HAS_SUBFORUMS"]) ? $context["S_HAS_SUBFORUMS"] : null))) {
                // line 26
                echo "\t\t\t\tif (value == ";
                echo (isset($context["FORUM_LINK"]) ? $context["FORUM_LINK"] : null);
                echo ")
\t\t\t\t{
\t\t\t\t\tphpbb.toggleDisplay('cat_to_link_actions', 1);
\t\t\t\t}
\t\t\t\telse
\t\t\t\t{
\t\t\t\t\tphpbb.toggleDisplay('cat_to_link_actions', -1);
\t\t\t\t}
\t\t\t";
            }
            // line 35
            echo "
\t\t\tif (value == ";
            // line 36
            echo (isset($context["FORUM_POST"]) ? $context["FORUM_POST"] : null);
            echo ")
\t\t\t{
\t\t\t\tphpbb.toggleDisplay('forum_post_options', 1);
\t\t\t\tphpbb.toggleDisplay('forum_link_options', -1);
\t\t\t\tphpbb.toggleDisplay('forum_rules_options', 1);
\t\t\t\tphpbb.toggleDisplay('forum_cat_options', -1);
\t\t\t}
\t\t\telse if (value == ";
            // line 43
            echo (isset($context["FORUM_LINK"]) ? $context["FORUM_LINK"] : null);
            echo ")
\t\t\t{
\t\t\t\tphpbb.toggleDisplay('forum_post_options', -1);
\t\t\t\tphpbb.toggleDisplay('forum_link_options', 1);
\t\t\t\tphpbb.toggleDisplay('forum_rules_options', -1);
\t\t\t\tphpbb.toggleDisplay('forum_cat_options', -1);
\t\t\t}
\t\t\telse if (value == ";
            // line 50
            echo (isset($context["FORUM_CAT"]) ? $context["FORUM_CAT"] : null);
            echo ")
\t\t\t{
\t\t\t\tphpbb.toggleDisplay('forum_post_options', -1);
\t\t\t\tphpbb.toggleDisplay('forum_link_options', -1);
\t\t\t\tphpbb.toggleDisplay('forum_rules_options', 1);
\t\t\t\tphpbb.toggleDisplay('forum_cat_options', 1);
\t\t\t}
\t\t}

\t\t/**
\t\t* Init the wanted display functionality if javascript is enabled.
\t\t* If javascript is not available, the user is still able to properly administer.
\t\t*/
\t\tonload = function()
\t\t{
\t\t\t";
            // line 65
            if (( !(isset($context["S_ADD_ACTION"]) ? $context["S_ADD_ACTION"] : null) && (isset($context["S_FORUM_ORIG_POST"]) ? $context["S_FORUM_ORIG_POST"] : null))) {
                // line 66
                echo "\t\t\t\t";
                if ((isset($context["S_FORUM_POST"]) ? $context["S_FORUM_POST"] : null)) {
                    // line 67
                    echo "\t\t\t\t\tphpbb.toggleDisplay('type_actions', -1);
\t\t\t\t";
                }
                // line 69
                echo "\t\t\t";
            }
            // line 70
            echo "
\t\t\t";
            // line 71
            if ((( !(isset($context["S_ADD_ACTION"]) ? $context["S_ADD_ACTION"] : null) && (isset($context["S_FORUM_ORIG_CAT"]) ? $context["S_FORUM_ORIG_CAT"] : null)) && (isset($context["S_HAS_SUBFORUMS"]) ? $context["S_HAS_SUBFORUMS"] : null))) {
                // line 72
                echo "\t\t\t\t";
                if ((isset($context["S_FORUM_CAT"]) ? $context["S_FORUM_CAT"] : null)) {
                    // line 73
                    echo "\t\t\t\t\tphpbb.toggleDisplay('cat_to_link_actions', -1);
\t\t\t\t";
                }
                // line 75
                echo "\t\t\t";
            }
            // line 76
            echo "
\t\t\t";
            // line 77
            if ( !(isset($context["S_FORUM_POST"]) ? $context["S_FORUM_POST"] : null)) {
                // line 78
                echo "\t\t\t\tphpbb.toggleDisplay('forum_post_options', -1);
\t\t\t";
            }
            // line 80
            echo "
\t\t\t";
            // line 81
            if ( !(isset($context["S_FORUM_CAT"]) ? $context["S_FORUM_CAT"] : null)) {
                // line 82
                echo "\t\t\t\tphpbb.toggleDisplay('forum_cat_options', -1);
\t\t\t";
            }
            // line 84
            echo "
\t\t\t";
            // line 85
            if ( !(isset($context["S_FORUM_LINK"]) ? $context["S_FORUM_LINK"] : null)) {
                // line 86
                echo "\t\t\t\tphpbb.toggleDisplay('forum_link_options', -1);
\t\t\t";
            }
            // line 88
            echo "
\t\t\t";
            // line 89
            if ((isset($context["S_FORUM_LINK"]) ? $context["S_FORUM_LINK"] : null)) {
                // line 90
                echo "\t\t\tphpbb.toggleDisplay('forum_rules_options', -1);
\t\t\t";
            }
            // line 92
            echo "\t\t}

\t// ]]>
\t</script>

\t<a href=\"";
            // line 97
            echo (isset($context["U_BACK"]) ? $context["U_BACK"] : null);
            echo "\" style=\"float: ";
            echo (isset($context["S_CONTENT_FLOW_END"]) ? $context["S_CONTENT_FLOW_END"] : null);
            echo ";\">&laquo; ";
            echo $this->env->getExtension('phpbb')->lang("BACK");
            echo "</a>

\t<h1>";
            // line 99
            echo $this->env->getExtension('phpbb')->lang("TITLE");
            echo " ";
            if ((isset($context["FORUM_NAME"]) ? $context["FORUM_NAME"] : null)) {
                echo ":: ";
                echo (isset($context["FORUM_NAME"]) ? $context["FORUM_NAME"] : null);
            }
            echo "</h1>

\t<p>";
            // line 101
            echo $this->env->getExtension('phpbb')->lang("FORUM_EDIT_EXPLAIN");
            echo "</p>

\t";
            // line 103
            if ((isset($context["S_ERROR"]) ? $context["S_ERROR"] : null)) {
                // line 104
                echo "\t\t<div class=\"errorbox\">
\t\t\t<h3>";
                // line 105
                echo $this->env->getExtension('phpbb')->lang("WARNING");
                echo "</h3>
\t\t\t<p>";
                // line 106
                echo (isset($context["ERROR_MSG"]) ? $context["ERROR_MSG"] : null);
                echo "</p>
\t\t</div>
\t";
            }
            // line 109
            echo "
\t<form id=\"forumedit\" method=\"post\" action=\"";
            // line 110
            echo (isset($context["U_EDIT_ACTION"]) ? $context["U_EDIT_ACTION"] : null);
            echo "\">

\t<fieldset>
\t\t<legend>";
            // line 113
            echo $this->env->getExtension('phpbb')->lang("FORUM_SETTINGS");
            echo "</legend>
\t";
            // line 114
            // line 115
            echo "\t<dl>
\t\t<dt><label for=\"forum_type\">";
            // line 116
            echo $this->env->getExtension('phpbb')->lang("FORUM_TYPE");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label></dt>
\t\t<dd><select id=\"forum_type\" name=\"forum_type\" onchange=\"display_options(this.options[this.selectedIndex].value);\">";
            // line 117
            echo (isset($context["S_FORUM_TYPE_OPTIONS"]) ? $context["S_FORUM_TYPE_OPTIONS"] : null);
            echo "</select></dd>
\t</dl>
\t";
            // line 119
            if (( !(isset($context["S_ADD_ACTION"]) ? $context["S_ADD_ACTION"] : null) && (isset($context["S_FORUM_ORIG_POST"]) ? $context["S_FORUM_ORIG_POST"] : null))) {
                // line 120
                echo "\t<div id=\"type_actions\">
\t\t<dl>
\t\t\t<dt><label for=\"type_action\">";
                // line 122
                echo $this->env->getExtension('phpbb')->lang("DECIDE_MOVE_DELETE_CONTENT");
                echo $this->env->getExtension('phpbb')->lang("COLON");
                echo "</label></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"type_action\" value=\"delete\"";
                // line 123
                if ( !(isset($context["S_MOVE_FORUM_OPTIONS"]) ? $context["S_MOVE_FORUM_OPTIONS"] : null)) {
                    echo " checked=\"checked\" id=\"type_action\"";
                }
                echo " /> ";
                echo $this->env->getExtension('phpbb')->lang("DELETE_ALL_POSTS");
                echo "</label></dd>
\t\t\t";
                // line 124
                if ((isset($context["S_MOVE_FORUM_OPTIONS"]) ? $context["S_MOVE_FORUM_OPTIONS"] : null)) {
                    echo "<dd><label><input type=\"radio\" class=\"radio\" name=\"type_action\" id=\"type_action\" value=\"move\" checked=\"checked\" /> ";
                    echo $this->env->getExtension('phpbb')->lang("MOVE_POSTS_TO");
                    echo "</label> <select name=\"to_forum_id\">";
                    echo (isset($context["S_MOVE_FORUM_OPTIONS"]) ? $context["S_MOVE_FORUM_OPTIONS"] : null);
                    echo "</select></dd>";
                }
                // line 125
                echo "\t\t</dl>
\t</div>
\t";
            }
            // line 128
            echo "\t";
            if ((( !(isset($context["S_ADD_ACTION"]) ? $context["S_ADD_ACTION"] : null) && (isset($context["S_FORUM_ORIG_CAT"]) ? $context["S_FORUM_ORIG_CAT"] : null)) && (isset($context["S_HAS_SUBFORUMS"]) ? $context["S_HAS_SUBFORUMS"] : null))) {
                // line 129
                echo "\t<div id=\"cat_to_link_actions\">
\t\t<dl>
\t\t\t<dt><label for=\"action_subforums\">";
                // line 131
                echo $this->env->getExtension('phpbb')->lang("DECIDE_MOVE_DELETE_SUBFORUMS");
                echo $this->env->getExtension('phpbb')->lang("COLON");
                echo "</label></dt>
\t\t\t";
                // line 132
                if ((isset($context["S_FORUMS_LIST"]) ? $context["S_FORUMS_LIST"] : null)) {
                    // line 133
                    echo "\t\t\t\t<dd><label><input type=\"radio\" class=\"radio\" id=\"action_subforums\" name=\"action_subforums\" value=\"move\" checked=\"checked\" /> ";
                    echo $this->env->getExtension('phpbb')->lang("MOVE_SUBFORUMS_TO");
                    echo "</label> <select name=\"subforums_to_id\">";
                    echo (isset($context["S_FORUMS_LIST"]) ? $context["S_FORUMS_LIST"] : null);
                    echo "</select></dd>
\t\t\t";
                } else {
                    // line 135
                    echo "\t\t\t\t<dd><label><input type=\"radio\" class=\"radio\" id=\"action_subforums\" name=\"action_subforums\" value=\"delete\" checked=\"checked\" /> ";
                    echo $this->env->getExtension('phpbb')->lang("DELETE_SUBFORUMS");
                    echo "</label></dd>
\t\t\t";
                }
                // line 137
                echo "\t\t</dl>
\t</div>
\t";
            }
            // line 140
            echo "\t<dl>
\t\t<dt><label for=\"parent\">";
            // line 141
            echo $this->env->getExtension('phpbb')->lang("FORUM_PARENT");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label></dt>
\t\t<dd><select id=\"parent\" name=\"forum_parent_id\"><option value=\"0\"";
            // line 142
            if ( !(isset($context["S_FORUM_PARENT_ID"]) ? $context["S_FORUM_PARENT_ID"] : null)) {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo $this->env->getExtension('phpbb')->lang("NO_PARENT");
            echo "</option>";
            echo (isset($context["S_PARENT_OPTIONS"]) ? $context["S_PARENT_OPTIONS"] : null);
            echo "</select></dd>
\t</dl>
\t";
            // line 144
            if ((isset($context["S_CAN_COPY_PERMISSIONS"]) ? $context["S_CAN_COPY_PERMISSIONS"] : null)) {
                // line 145
                echo "\t\t<dl>
\t\t\t<dt><label for=\"forum_perm_from\">";
                // line 146
                echo $this->env->getExtension('phpbb')->lang("COPY_PERMISSIONS");
                echo $this->env->getExtension('phpbb')->lang("COLON");
                echo "</label><br /><span>";
                echo $this->env->getExtension('phpbb')->lang("COPY_PERMISSIONS_EXPLAIN");
                echo "</span></dt>
\t\t\t<dd><select id=\"forum_perm_from\" name=\"forum_perm_from\"><option value=\"0\">";
                // line 147
                echo $this->env->getExtension('phpbb')->lang("NO_PERMISSIONS");
                echo "</option>";
                echo (isset($context["S_FORUM_OPTIONS"]) ? $context["S_FORUM_OPTIONS"] : null);
                echo "</select></dd>
\t\t</dl>
\t";
            }
            // line 150
            echo "\t<dl>
\t\t<dt><label for=\"forum_name\">";
            // line 151
            echo $this->env->getExtension('phpbb')->lang("FORUM_NAME");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label></dt>
\t\t<dd><input class=\"text medium\" type=\"text\" id=\"forum_name\" name=\"forum_name\" value=\"";
            // line 152
            echo (isset($context["FORUM_NAME"]) ? $context["FORUM_NAME"] : null);
            echo "\" maxlength=\"255\" /></dd>
\t</dl>
\t<dl>
\t\t<dt><label for=\"forum_desc\">";
            // line 155
            echo $this->env->getExtension('phpbb')->lang("FORUM_DESC");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_DESC_EXPLAIN");
            echo "</span></dt>
\t\t<dd><textarea id=\"forum_desc\" name=\"forum_desc\" rows=\"5\" cols=\"45\" data-bbcode=\"true\">";
            // line 156
            echo (isset($context["FORUM_DESC"]) ? $context["FORUM_DESC"] : null);
            echo "</textarea></dd>
\t\t<dd><label><input type=\"checkbox\" class=\"radio\" name=\"desc_parse_bbcode\"";
            // line 157
            if ((isset($context["S_DESC_BBCODE_CHECKED"]) ? $context["S_DESC_BBCODE_CHECKED"] : null)) {
                echo " checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("PARSE_BBCODE");
            echo "</label>
\t\t\t<label><input type=\"checkbox\" class=\"radio\" name=\"desc_parse_smilies\"";
            // line 158
            if ((isset($context["S_DESC_SMILIES_CHECKED"]) ? $context["S_DESC_SMILIES_CHECKED"] : null)) {
                echo " checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("PARSE_SMILIES");
            echo "</label>
\t\t\t<label><input type=\"checkbox\" class=\"radio\" name=\"desc_parse_urls\"";
            // line 159
            if ((isset($context["S_DESC_URLS_CHECKED"]) ? $context["S_DESC_URLS_CHECKED"] : null)) {
                echo " checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("PARSE_URLS");
            echo "</label></dd>
\t</dl>
\t<dl>
\t\t<dt><label for=\"forum_image\">";
            // line 162
            echo $this->env->getExtension('phpbb')->lang("FORUM_IMAGE");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_IMAGE_EXPLAIN");
            echo "</span></dt>
\t\t<dd><input class=\"text medium\" type=\"text\" id=\"forum_image\" name=\"forum_image\" value=\"";
            // line 163
            echo (isset($context["FORUM_IMAGE"]) ? $context["FORUM_IMAGE"] : null);
            echo "\" maxlength=\"255\" /></dd>
\t\t";
            // line 164
            if ((isset($context["FORUM_IMAGE_SRC"]) ? $context["FORUM_IMAGE_SRC"] : null)) {
                // line 165
                echo "\t\t\t<dd><img src=\"";
                echo (isset($context["FORUM_IMAGE_SRC"]) ? $context["FORUM_IMAGE_SRC"] : null);
                echo "\" alt=\"";
                echo $this->env->getExtension('phpbb')->lang("FORUM_IMAGE");
                echo "\" /></dd>
\t\t";
            }
            // line 167
            echo "\t</dl>
\t<dl>
\t\t<dt><label for=\"forum_password\">";
            // line 169
            echo $this->env->getExtension('phpbb')->lang("FORUM_PASSWORD");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_PASSWORD_EXPLAIN");
            echo "</span></dt>
\t\t<dd><input type=\"password\" id=\"forum_password\" name=\"forum_password\" value=\"";
            // line 170
            if ((isset($context["S_FORUM_PASSWORD_SET"]) ? $context["S_FORUM_PASSWORD_SET"] : null)) {
                echo "&#x20;&#x20;&#x20;&#x20;&#x20;&#x20;";
            }
            echo "\" autocomplete=\"off\" /></dd>
\t</dl>
\t<dl>
\t\t<dt><label for=\"forum_password_confirm\">";
            // line 173
            echo $this->env->getExtension('phpbb')->lang("FORUM_PASSWORD_CONFIRM");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_PASSWORD_CONFIRM_EXPLAIN");
            echo "</span></dt>
\t\t<dd><input type=\"password\" id=\"forum_password_confirm\" name=\"forum_password_confirm\" value=\"";
            // line 174
            if ((isset($context["S_FORUM_PASSWORD_SET"]) ? $context["S_FORUM_PASSWORD_SET"] : null)) {
                echo "&#x20;&#x20;&#x20;&#x20;&#x20;&#x20;";
            }
            echo "\" autocomplete=\"off\" /></dd>
\t</dl>
\t";
            // line 176
            if ((isset($context["S_FORUM_PASSWORD_SET"]) ? $context["S_FORUM_PASSWORD_SET"] : null)) {
                // line 177
                echo "\t<dl>
\t\t<dt><label for=\"forum_password_unset\">";
                // line 178
                echo $this->env->getExtension('phpbb')->lang("FORUM_PASSWORD_UNSET");
                echo $this->env->getExtension('phpbb')->lang("COLON");
                echo "</label><br /><span>";
                echo $this->env->getExtension('phpbb')->lang("FORUM_PASSWORD_UNSET_EXPLAIN");
                echo "</span></dt>
\t\t<dd><input id=\"forum_password_unset\" name=\"forum_password_unset\" type=\"checkbox\" /></dd>
\t</dl>
\t";
            }
            // line 182
            echo "\t<dl>
\t\t<dt><label for=\"forum_style\">";
            // line 183
            echo $this->env->getExtension('phpbb')->lang("FORUM_STYLE");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label></dt>
\t\t<dd><select id=\"forum_style\" name=\"forum_style\"><option value=\"0\">";
            // line 184
            echo $this->env->getExtension('phpbb')->lang("DEFAULT_STYLE");
            echo "</option>";
            echo (isset($context["S_STYLES_OPTIONS"]) ? $context["S_STYLES_OPTIONS"] : null);
            echo "</select></dd>
\t</dl>
\t";
            // line 186
            // line 187
            echo "\t</fieldset>

\t<div id=\"forum_cat_options\">
\t\t<fieldset>
\t\t\t<legend>";
            // line 191
            echo $this->env->getExtension('phpbb')->lang("GENERAL_FORUM_SETTINGS");
            echo "</legend>
\t\t<dl>
\t\t\t<dt><label for=\"display_active\">";
            // line 193
            echo $this->env->getExtension('phpbb')->lang("DISPLAY_ACTIVE_TOPICS");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("DISPLAY_ACTIVE_TOPICS_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"display_active\" value=\"1\"";
            // line 194
            if ((isset($context["S_ENABLE_ACTIVE_TOPICS"]) ? $context["S_ENABLE_ACTIVE_TOPICS"] : null)) {
                echo " id=\"display_active\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"display_active\" value=\"0\"";
            // line 195
            if ( !(isset($context["S_ENABLE_ACTIVE_TOPICS"]) ? $context["S_ENABLE_ACTIVE_TOPICS"] : null)) {
                echo " id=\"display_active\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t</fieldset>
\t</div>

\t<div id=\"forum_post_options\">
\t\t<fieldset>
\t\t\t<legend>";
            // line 202
            echo $this->env->getExtension('phpbb')->lang("GENERAL_FORUM_SETTINGS");
            echo "</legend>
\t\t";
            // line 203
            // line 204
            echo "\t\t<dl>
\t\t\t<dt><label for=\"forum_status\">";
            // line 205
            echo $this->env->getExtension('phpbb')->lang("FORUM_STATUS");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label></dt>
\t\t\t<dd><select id=\"forum_status\" name=\"forum_status\">";
            // line 206
            echo (isset($context["S_STATUS_OPTIONS"]) ? $context["S_STATUS_OPTIONS"] : null);
            echo "</select></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"display_subforum_list\">";
            // line 209
            echo $this->env->getExtension('phpbb')->lang("LIST_SUBFORUMS");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("LIST_SUBFORUMS_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"display_subforum_list\" value=\"1\"";
            // line 210
            if ((isset($context["S_DISPLAY_SUBFORUM_LIST"]) ? $context["S_DISPLAY_SUBFORUM_LIST"] : null)) {
                echo " id=\"display_subforum_list\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"display_subforum_list\" value=\"0\"";
            // line 211
            if ( !(isset($context["S_DISPLAY_SUBFORUM_LIST"]) ? $context["S_DISPLAY_SUBFORUM_LIST"] : null)) {
                echo " id=\"display_subforum_list\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"display_on_index\">";
            // line 214
            echo $this->env->getExtension('phpbb')->lang("LIST_INDEX");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("LIST_INDEX_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"display_on_index\" value=\"1\"";
            // line 215
            if ((isset($context["S_DISPLAY_ON_INDEX"]) ? $context["S_DISPLAY_ON_INDEX"] : null)) {
                echo " id=\"display_on_index\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"display_on_index\" value=\"0\"";
            // line 216
            if ( !(isset($context["S_DISPLAY_ON_INDEX"]) ? $context["S_DISPLAY_ON_INDEX"] : null)) {
                echo " id=\"display_on_index\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"enable_post_review\">";
            // line 219
            echo $this->env->getExtension('phpbb')->lang("ENABLE_POST_REVIEW");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("ENABLE_POST_REVIEW_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"enable_post_review\" value=\"1\"";
            // line 220
            if ((isset($context["S_ENABLE_POST_REVIEW"]) ? $context["S_ENABLE_POST_REVIEW"] : null)) {
                echo " id=\"enable_post_review\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"enable_post_review\" value=\"0\"";
            // line 221
            if ( !(isset($context["S_ENABLE_POST_REVIEW"]) ? $context["S_ENABLE_POST_REVIEW"] : null)) {
                echo " id=\"enable_post_review\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"enable_quick_reply\">";
            // line 224
            echo $this->env->getExtension('phpbb')->lang("ENABLE_QUICK_REPLY");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("ENABLE_QUICK_REPLY_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"enable_quick_reply\" value=\"1\"";
            // line 225
            if ((isset($context["S_ENABLE_QUICK_REPLY"]) ? $context["S_ENABLE_QUICK_REPLY"] : null)) {
                echo " id=\"enable_quick_reply\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"enable_quick_reply\" value=\"0\"";
            // line 226
            if ( !(isset($context["S_ENABLE_QUICK_REPLY"]) ? $context["S_ENABLE_QUICK_REPLY"] : null)) {
                echo " id=\"enable_quick_reply\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"enable_indexing\">";
            // line 229
            echo $this->env->getExtension('phpbb')->lang("ENABLE_INDEXING");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("ENABLE_INDEXING_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"enable_indexing\" value=\"1\"";
            // line 230
            if ((isset($context["S_ENABLE_INDEXING"]) ? $context["S_ENABLE_INDEXING"] : null)) {
                echo " id=\"enable_indexing\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"enable_indexing\" value=\"0\"";
            // line 231
            if ( !(isset($context["S_ENABLE_INDEXING"]) ? $context["S_ENABLE_INDEXING"] : null)) {
                echo " id=\"enable_indexing\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"enable_icons\">";
            // line 234
            echo $this->env->getExtension('phpbb')->lang("ENABLE_TOPIC_ICONS");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"enable_icons\" value=\"1\"";
            // line 235
            if ((isset($context["S_TOPIC_ICONS"]) ? $context["S_TOPIC_ICONS"] : null)) {
                echo " id=\"enable_icons\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"enable_icons\" value=\"0\"";
            // line 236
            if ( !(isset($context["S_TOPIC_ICONS"]) ? $context["S_TOPIC_ICONS"] : null)) {
                echo " id=\"enable_icons\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"display_recent\">";
            // line 239
            echo $this->env->getExtension('phpbb')->lang("ENABLE_RECENT");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("ENABLE_RECENT_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"display_recent\" value=\"1\"";
            // line 240
            if ((isset($context["S_DISPLAY_ACTIVE_TOPICS"]) ? $context["S_DISPLAY_ACTIVE_TOPICS"] : null)) {
                echo " id=\"display_recent\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"display_recent\" value=\"0\"";
            // line 241
            if ( !(isset($context["S_DISPLAY_ACTIVE_TOPICS"]) ? $context["S_DISPLAY_ACTIVE_TOPICS"] : null)) {
                echo " id=\"display_recent\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"topics_per_page\">";
            // line 244
            echo $this->env->getExtension('phpbb')->lang("FORUM_TOPICS_PAGE");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_TOPICS_PAGE_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><input type=\"number\" id=\"topics_per_page\" name=\"topics_per_page\" value=\"";
            // line 245
            echo (isset($context["TOPICS_PER_PAGE"]) ? $context["TOPICS_PER_PAGE"] : null);
            echo "\" size=\"4\" maxlength=\"4\" min=\"0\" max=\"9999\" /></dd>
\t\t</dl>
        
       <!-- <dl>
      \t\t<dt><label for=\"forum_society_access\">";
            // line 249
            echo $this->env->getExtension('phpbb')->lang("FORUM_SOCIETY_ACCESS");
            echo ":</label></dt>
      \t\t<dd><input class=\"text medium\" type=\"text\" id=\"forum_society_access\" name=\"forum_society_access\" value=\"";
            // line 250
            echo (isset($context["FORUM_SOCIETY_ACCESS"]) ? $context["FORUM_SOCIETY_ACCESS"] : null);
            echo "\" maxlength=\"255\" /> </dd>
\t\t</dl> -->
        
           
        <dl>
\t\t\t<dt><label for=\"forum_society_access\">";
            // line 255
            echo $this->env->getExtension('phpbb')->lang("FORUM_SOCIETY_ACCESS");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_SOCIETY_ACCESS_NOTE");
            echo "</span></dt>
\t\t\t<dd><select name=\"forum_society_access[]\" id=\"forum_society_access\" multiple=\"multiple\" style=\" height:150px;overflow-y:auto;overflow-x:auto;\" >";
            // line 256
            echo (isset($context["S_FORUM_SOCIETY_ACCESS_OPTIONS"]) ? $context["S_FORUM_SOCIETY_ACCESS_OPTIONS"] : null);
            echo "></select></dd>
\t\t</dl>
        
        
\t\t";
            // line 260
            // line 261
            echo "\t\t</fieldset>

\t\t<fieldset>
\t\t\t<legend>";
            // line 264
            echo $this->env->getExtension('phpbb')->lang("FORUM_PRUNE_SETTINGS");
            echo "</legend>
\t\t";
            // line 265
            // line 266
            echo "\t\t<dl>
\t\t\t<dt><label for=\"enable_prune\">";
            // line 267
            echo $this->env->getExtension('phpbb')->lang("FORUM_AUTO_PRUNE");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_AUTO_PRUNE_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"enable_prune\" value=\"1\"";
            // line 268
            if ((isset($context["S_PRUNE_ENABLE"]) ? $context["S_PRUNE_ENABLE"] : null)) {
                echo " id=\"enable_prune\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"enable_prune\" value=\"0\"";
            // line 269
            if ( !(isset($context["S_PRUNE_ENABLE"]) ? $context["S_PRUNE_ENABLE"] : null)) {
                echo " id=\"enable_prune\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"prune_freq\">";
            // line 272
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_FREQ");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_FREQ_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><input type=\"number\" id=\"prune_freq\" name=\"prune_freq\" value=\"";
            // line 273
            echo (isset($context["PRUNE_FREQ"]) ? $context["PRUNE_FREQ"] : null);
            echo "\" maxlength=\"4\" size=\"4\" min=\"0\" max=\"9999\" /> ";
            echo $this->env->getExtension('phpbb')->lang("DAYS");
            echo "</dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"prune_days\">";
            // line 276
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_DAYS");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_DAYS_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><input type=\"number\" id=\"prune_days\" name=\"prune_days\" value=\"";
            // line 277
            echo (isset($context["PRUNE_DAYS"]) ? $context["PRUNE_DAYS"] : null);
            echo "\" maxlength=\"4\" size=\"4\" min=\"0\" max=\"9999\" /> ";
            echo $this->env->getExtension('phpbb')->lang("DAYS");
            echo "</dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"prune_viewed\">";
            // line 280
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_VIEWED");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_VIEWED_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><input type=\"number\" id=\"prune_viewed\" name=\"prune_viewed\" value=\"";
            // line 281
            echo (isset($context["PRUNE_VIEWED"]) ? $context["PRUNE_VIEWED"] : null);
            echo "\" maxlength=\"4\" size=\"4\" min=\"0\" max=\"9999\" /> ";
            echo $this->env->getExtension('phpbb')->lang("DAYS");
            echo "</dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"prune_old_polls\">";
            // line 284
            echo $this->env->getExtension('phpbb')->lang("PRUNE_OLD_POLLS");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("PRUNE_OLD_POLLS_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"prune_old_polls\" value=\"1\"";
            // line 285
            if ((isset($context["S_PRUNE_OLD_POLLS"]) ? $context["S_PRUNE_OLD_POLLS"] : null)) {
                echo " id=\"prune_old_polls\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"prune_old_polls\" value=\"0\"";
            // line 286
            if ( !(isset($context["S_PRUNE_OLD_POLLS"]) ? $context["S_PRUNE_OLD_POLLS"] : null)) {
                echo " id=\"prune_old_polls\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"prune_announce\">";
            // line 289
            echo $this->env->getExtension('phpbb')->lang("PRUNE_ANNOUNCEMENTS");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"prune_announce\" value=\"1\"";
            // line 290
            if ((isset($context["S_PRUNE_ANNOUNCE"]) ? $context["S_PRUNE_ANNOUNCE"] : null)) {
                echo " id=\"prune_announce\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"prune_announce\" value=\"0\"";
            // line 291
            if ( !(isset($context["S_PRUNE_ANNOUNCE"]) ? $context["S_PRUNE_ANNOUNCE"] : null)) {
                echo " id=\"prune_announce\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"prune_sticky\">";
            // line 294
            echo $this->env->getExtension('phpbb')->lang("PRUNE_STICKY");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"prune_sticky\" value=\"1\"";
            // line 295
            if ((isset($context["S_PRUNE_STICKY"]) ? $context["S_PRUNE_STICKY"] : null)) {
                echo " id=\"prune_sticky\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"prune_sticky\" value=\"0\"";
            // line 296
            if ( !(isset($context["S_PRUNE_STICKY"]) ? $context["S_PRUNE_STICKY"] : null)) {
                echo " id=\"prune_sticky\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"enable_shadow_prune\">";
            // line 299
            echo $this->env->getExtension('phpbb')->lang("FORUM_PRUNE_SHADOW");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_PRUNE_SHADOW_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"enable_shadow_prune\" value=\"1\"";
            // line 300
            if ((isset($context["S_PRUNE_SHADOW_ENABLE"]) ? $context["S_PRUNE_SHADOW_ENABLE"] : null)) {
                echo " id=\"enable_shadow_prune\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"enable_shadow_prune\" value=\"0\"";
            // line 301
            if ( !(isset($context["S_PRUNE_SHADOW_ENABLE"]) ? $context["S_PRUNE_SHADOW_ENABLE"] : null)) {
                echo " id=\"enable_shadow_prune\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"prune_shadow_freq\">";
            // line 304
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_SHADOW_FREQ");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_SHADOW_FREQ_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><input type=\"number\" id=\"prune_shadow_freq\" name=\"prune_shadow_freq\" value=\"";
            // line 305
            echo (isset($context["PRUNE_SHADOW_FREQ"]) ? $context["PRUNE_SHADOW_FREQ"] : null);
            echo "\" maxlength=\"4\" size=\"4\" min=\"0\" max=\"9999\" /> ";
            echo $this->env->getExtension('phpbb')->lang("DAYS");
            echo "</dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"prune_shadow_days\">";
            // line 308
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_SHADOW_DAYS");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("AUTO_PRUNE_SHADOW_DAYS_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><input type=\"number\" id=\"prune_shadow_days\" name=\"prune_shadow_days\" value=\"";
            // line 309
            echo (isset($context["PRUNE_SHADOW_DAYS"]) ? $context["PRUNE_SHADOW_DAYS"] : null);
            echo "\" maxlength=\"4\" size=\"4\" min=\"0\" max=\"9999\" /> ";
            echo $this->env->getExtension('phpbb')->lang("DAYS");
            echo "</dd>
\t\t</dl>
\t\t";
            // line 311
            // line 312
            echo "\t\t</fieldset>
\t</div>

\t<div id=\"forum_link_options\">
\t\t<fieldset>
\t\t\t<legend>";
            // line 317
            echo $this->env->getExtension('phpbb')->lang("GENERAL_FORUM_SETTINGS");
            echo "</legend>
\t\t<dl>
\t\t\t<dt><label for=\"link_display_on_index\">";
            // line 319
            echo $this->env->getExtension('phpbb')->lang("LIST_INDEX");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("LIST_INDEX_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"link_display_on_index\" value=\"1\"";
            // line 320
            if ((isset($context["S_DISPLAY_ON_INDEX"]) ? $context["S_DISPLAY_ON_INDEX"] : null)) {
                echo " id=\"link_display_on_index\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"link_display_on_index\" value=\"0\"";
            // line 321
            if ( !(isset($context["S_DISPLAY_ON_INDEX"]) ? $context["S_DISPLAY_ON_INDEX"] : null)) {
                echo " id=\"link_display_on_index\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"forum_link\">";
            // line 324
            echo $this->env->getExtension('phpbb')->lang("FORUM_LINK");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_LINK_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><input class=\"text medium\" type=\"text\" id=\"forum_link\" name=\"forum_link\" value=\"";
            // line 325
            echo (isset($context["FORUM_DATA_LINK"]) ? $context["FORUM_DATA_LINK"] : null);
            echo "\" maxlength=\"255\" /></dd>
\t\t</dl>
\t\t<dl>
\t\t\t<dt><label for=\"forum_link_track\">";
            // line 328
            echo $this->env->getExtension('phpbb')->lang("FORUM_LINK_TRACK");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_LINK_TRACK_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"forum_link_track\" value=\"1\"";
            // line 329
            if ((isset($context["S_FORUM_LINK_TRACK"]) ? $context["S_FORUM_LINK_TRACK"] : null)) {
                echo " id=\"forum_link_track\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("YES");
            echo "</label>
\t\t\t\t<label><input type=\"radio\" class=\"radio\" name=\"forum_link_track\" value=\"0\"";
            // line 330
            if ( !(isset($context["S_FORUM_LINK_TRACK"]) ? $context["S_FORUM_LINK_TRACK"] : null)) {
                echo " id=\"forum_link_track\" checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("NO");
            echo "</label></dd>
\t\t</dl>
\t\t</fieldset>
\t</div>

\t<div id=\"forum_rules_options\">
\t\t<fieldset>
\t\t\t<legend>";
            // line 337
            echo $this->env->getExtension('phpbb')->lang("FORUM_RULES");
            echo "</legend>
\t\t";
            // line 338
            // line 339
            echo "\t\t<dl>
\t\t\t<dt><label for=\"forum_rules_link\">";
            // line 340
            echo $this->env->getExtension('phpbb')->lang("FORUM_RULES_LINK");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_RULES_LINK_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><input class=\"text medium\" type=\"text\" id=\"forum_rules_link\" name=\"forum_rules_link\" value=\"";
            // line 341
            echo (isset($context["FORUM_RULES_LINK"]) ? $context["FORUM_RULES_LINK"] : null);
            echo "\" maxlength=\"255\" /></dd>
\t\t</dl>
\t";
            // line 343
            if ((isset($context["FORUM_RULES_PREVIEW"]) ? $context["FORUM_RULES_PREVIEW"] : null)) {
                // line 344
                echo "\t\t<dl>
\t\t\t<dt><label>";
                // line 345
                echo $this->env->getExtension('phpbb')->lang("FORUM_RULES_PREVIEW");
                echo $this->env->getExtension('phpbb')->lang("COLON");
                echo "</label></dt>
\t\t\t<dd>";
                // line 346
                echo (isset($context["FORUM_RULES_PREVIEW"]) ? $context["FORUM_RULES_PREVIEW"] : null);
                echo "</dd>
\t\t</dl>
\t";
            }
            // line 349
            echo "\t\t<dl>
\t\t\t<dt><label for=\"forum_rules\">";
            // line 350
            echo $this->env->getExtension('phpbb')->lang("FORUM_RULES");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label><br /><span>";
            echo $this->env->getExtension('phpbb')->lang("FORUM_RULES_EXPLAIN");
            echo "</span></dt>
\t\t\t<dd><textarea id=\"forum_rules\" name=\"forum_rules\" rows=\"4\" cols=\"70\" data-bbcode=\"true\">";
            // line 351
            echo (isset($context["FORUM_RULES_PLAIN"]) ? $context["FORUM_RULES_PLAIN"] : null);
            echo "</textarea></dd>
\t\t\t<dd><label><input type=\"checkbox\" class=\"radio\" name=\"rules_parse_bbcode\"";
            // line 352
            if ((isset($context["S_BBCODE_CHECKED"]) ? $context["S_BBCODE_CHECKED"] : null)) {
                echo " checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("PARSE_BBCODE");
            echo "</label>
\t\t\t\t<label><input type=\"checkbox\" class=\"radio\" name=\"rules_parse_smilies\"";
            // line 353
            if ((isset($context["S_SMILIES_CHECKED"]) ? $context["S_SMILIES_CHECKED"] : null)) {
                echo " checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("PARSE_SMILIES");
            echo "</label>
\t\t\t\t<label><input type=\"checkbox\" class=\"radio\" name=\"rules_parse_urls\"";
            // line 354
            if ((isset($context["S_URLS_CHECKED"]) ? $context["S_URLS_CHECKED"] : null)) {
                echo " checked=\"checked\"";
            }
            echo " /> ";
            echo $this->env->getExtension('phpbb')->lang("PARSE_URLS");
            echo "</label></dd>
\t\t</dl>
\t\t";
            // line 356
            // line 357
            echo "\t\t</fieldset>
\t</div>
\t
\t";
            // line 360
            // line 361
            echo "
\t<fieldset class=\"submit-buttons\">
\t\t<legend>";
            // line 363
            echo $this->env->getExtension('phpbb')->lang("SUBMIT");
            echo "</legend>
\t\t<input class=\"button1\" type=\"submit\" id=\"submit\" name=\"update\" value=\"";
            // line 364
            echo $this->env->getExtension('phpbb')->lang("SUBMIT");
            echo "\" />&nbsp;
\t\t<input class=\"button2\" type=\"reset\" id=\"reset\" name=\"reset\" value=\"";
            // line 365
            echo $this->env->getExtension('phpbb')->lang("RESET");
            echo "\" />
\t\t";
            // line 366
            echo (isset($context["S_FORM_TOKEN"]) ? $context["S_FORM_TOKEN"] : null);
            echo "
\t</fieldset>
\t</form>

";
        } elseif (        // line 370
(isset($context["S_DELETE_FORUM"]) ? $context["S_DELETE_FORUM"] : null)) {
            // line 371
            echo "
\t<a href=\"";
            // line 372
            echo (isset($context["U_BACK"]) ? $context["U_BACK"] : null);
            echo "\" style=\"float: ";
            echo (isset($context["S_CONTENT_FLOW_END"]) ? $context["S_CONTENT_FLOW_END"] : null);
            echo ";\">&laquo; ";
            echo $this->env->getExtension('phpbb')->lang("BACK");
            echo "</a>

\t<h1>";
            // line 374
            echo $this->env->getExtension('phpbb')->lang("FORUM_DELETE");
            echo "</h1>

\t<p>";
            // line 376
            echo $this->env->getExtension('phpbb')->lang("FORUM_DELETE_EXPLAIN");
            echo "</p>

\t";
            // line 378
            if ((isset($context["S_ERROR"]) ? $context["S_ERROR"] : null)) {
                // line 379
                echo "\t\t<div class=\"errorbox\">
\t\t\t<h3>";
                // line 380
                echo $this->env->getExtension('phpbb')->lang("WARNING");
                echo "</h3>
\t\t\t<p>";
                // line 381
                echo (isset($context["ERROR_MSG"]) ? $context["ERROR_MSG"] : null);
                echo "</p>
\t\t</div>
\t";
            }
            // line 384
            echo "
\t<form id=\"acp_forum\" method=\"post\" action=\"";
            // line 385
            echo (isset($context["U_ACTION"]) ? $context["U_ACTION"] : null);
            echo "\">

\t<fieldset>
\t\t<legend>";
            // line 388
            echo $this->env->getExtension('phpbb')->lang("FORUM_DELETE");
            echo "</legend>
\t<dl>
\t\t<dt><label>";
            // line 390
            echo $this->env->getExtension('phpbb')->lang("FORUM_NAME");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo "</label></dt>
\t\t<dd><strong>";
            // line 391
            echo (isset($context["FORUM_NAME"]) ? $context["FORUM_NAME"] : null);
            echo "</strong></dd>
\t</dl>
\t";
            // line 393
            if ((isset($context["S_FORUM_POST"]) ? $context["S_FORUM_POST"] : null)) {
                // line 394
                echo "\t\t<dl>
\t\t\t<dt><label for=\"delete_action\">";
                // line 395
                echo $this->env->getExtension('phpbb')->lang("ACTION");
                echo $this->env->getExtension('phpbb')->lang("COLON");
                echo "</label></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" id=\"delete_action\" name=\"action_posts\" value=\"delete\" checked=\"checked\" /> ";
                // line 396
                echo $this->env->getExtension('phpbb')->lang("DELETE_ALL_POSTS");
                echo "</label></dd>
\t\t\t";
                // line 397
                if ((isset($context["S_MOVE_FORUM_OPTIONS"]) ? $context["S_MOVE_FORUM_OPTIONS"] : null)) {
                    // line 398
                    echo "\t\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"action_posts\" value=\"move\" /> ";
                    echo $this->env->getExtension('phpbb')->lang("MOVE_POSTS_TO");
                    echo "</label> <select name=\"posts_to_id\">";
                    echo (isset($context["S_MOVE_FORUM_OPTIONS"]) ? $context["S_MOVE_FORUM_OPTIONS"] : null);
                    echo "</select></dd>
\t\t\t";
                }
                // line 400
                echo "\t\t</dl>
\t";
            }
            // line 402
            echo "\t";
            if ((isset($context["S_HAS_SUBFORUMS"]) ? $context["S_HAS_SUBFORUMS"] : null)) {
                // line 403
                echo "\t\t<dl>
\t\t\t<dt><label for=\"sub_delete_action\">";
                // line 404
                echo $this->env->getExtension('phpbb')->lang("ACTION");
                echo $this->env->getExtension('phpbb')->lang("COLON");
                echo "</label></dt>
\t\t\t<dd><label><input type=\"radio\" class=\"radio\" id=\"sub_delete_action\" name=\"action_subforums\" value=\"delete\" checked=\"checked\" /> ";
                // line 405
                echo $this->env->getExtension('phpbb')->lang("DELETE_SUBFORUMS");
                echo "</label></dd>
\t\t\t";
                // line 406
                if ((isset($context["S_FORUMS_LIST"]) ? $context["S_FORUMS_LIST"] : null)) {
                    // line 407
                    echo "\t\t\t\t<dd><label><input type=\"radio\" class=\"radio\" name=\"action_subforums\" value=\"move\" /> ";
                    echo $this->env->getExtension('phpbb')->lang("MOVE_SUBFORUMS_TO");
                    echo "</label> <select name=\"subforums_to_id\">";
                    echo (isset($context["S_FORUMS_LIST"]) ? $context["S_FORUMS_LIST"] : null);
                    echo "</select></dd>
\t\t\t";
                }
                // line 409
                echo "\t\t</dl>
\t";
            }
            // line 411
            echo "
\t<p class=\"quick\">
\t\t<input class=\"button1\" type=\"submit\" name=\"update\" value=\"";
            // line 413
            echo $this->env->getExtension('phpbb')->lang("SUBMIT");
            echo "\" />
\t</p>
\t";
            // line 415
            echo (isset($context["S_FORM_TOKEN"]) ? $context["S_FORM_TOKEN"] : null);
            echo "
\t</fieldset>
\t</form>

";
        } elseif (        // line 419
(isset($context["S_CONTINUE_SYNC"]) ? $context["S_CONTINUE_SYNC"] : null)) {
            // line 420
            echo "
\t<script type=\"text/javascript\">
\t// <![CDATA[
\t\tvar close_waitscreen = 0;
\t\t// no scrollbars...
\t\tpopup('";
            // line 425
            echo (isset($context["UA_PROGRESS_BAR"]) ? $context["UA_PROGRESS_BAR"] : null);
            echo "', 400, 240, '_sync');
\t// ]]>
\t</script>

\t<h1>";
            // line 429
            echo $this->env->getExtension('phpbb')->lang("FORUM_ADMIN");
            echo "</h1>

\t<p>";
            // line 431
            echo $this->env->getExtension('phpbb')->lang("FORUM_ADMIN_EXPLAIN");
            echo "</p>

\t<p>";
            // line 433
            echo $this->env->getExtension('phpbb')->lang("PROGRESS_EXPLAIN");
            echo "</p>

";
        } else {
            // line 436
            echo "
\t<script type=\"text/javascript\">
\t// <![CDATA[
\t\t/**
\t\t* Popup search progress bar
\t\t*/
\t\tfunction popup_progress_bar()
\t\t{
\t\t\tvar close_waitscreen = 0;
\t\t\t// no scrollbars...
\t\t\tpopup('";
            // line 446
            echo (isset($context["UA_PROGRESS_BAR"]) ? $context["UA_PROGRESS_BAR"] : null);
            echo "', 400, 240, '_sync');
\t\t}
\t// ]]>
\t</script>

\t<h1>";
            // line 451
            echo $this->env->getExtension('phpbb')->lang("FORUM_ADMIN");
            echo "</h1>

\t<p>";
            // line 453
            echo $this->env->getExtension('phpbb')->lang("FORUM_ADMIN_EXPLAIN");
            echo "</p>

\t";
            // line 455
            if ((isset($context["ERROR_MSG"]) ? $context["ERROR_MSG"] : null)) {
                // line 456
                echo "\t\t<div class=\"errorbox\">
\t\t\t<h3>";
                // line 457
                echo $this->env->getExtension('phpbb')->lang("WARNING");
                echo "</h3>
\t\t\t<p>";
                // line 458
                echo (isset($context["ERROR_MSG"]) ? $context["ERROR_MSG"] : null);
                echo "</p>
\t\t</div>
\t";
            }
            // line 461
            echo "
\t";
            // line 462
            if ((isset($context["S_RESYNCED"]) ? $context["S_RESYNCED"] : null)) {
                // line 463
                echo "\t\t<script type=\"text/javascript\">
\t\t// <![CDATA[
\t\t\tvar close_waitscreen = 1;
\t\t// ]]>
\t\t</script>

\t\t<div class=\"successbox\">
\t\t\t<h3>";
                // line 470
                echo $this->env->getExtension('phpbb')->lang("NOTIFY");
                echo "</h3>
\t\t\t<p>";
                // line 471
                echo $this->env->getExtension('phpbb')->lang("FORUM_RESYNCED");
                echo "</p>
\t\t</div>
\t";
            }
            // line 474
            echo "
\t<p><strong>";
            // line 475
            echo (isset($context["NAVIGATION"]) ? $context["NAVIGATION"] : null);
            if ((isset($context["S_NO_FORUMS"]) ? $context["S_NO_FORUMS"] : null)) {
                echo " [<a href=\"";
                echo (isset($context["U_EDIT"]) ? $context["U_EDIT"] : null);
                echo "\">";
                echo $this->env->getExtension('phpbb')->lang("EDIT");
                echo "</a> | <a href=\"";
                echo (isset($context["U_DELETE"]) ? $context["U_DELETE"] : null);
                echo "\">";
                echo $this->env->getExtension('phpbb')->lang("DELETE");
                echo "</a>";
                if ( !(isset($context["S_LINK"]) ? $context["S_LINK"] : null)) {
                    echo " | <a href=\"";
                    echo (isset($context["U_SYNC"]) ? $context["U_SYNC"] : null);
                    echo "\">";
                    echo $this->env->getExtension('phpbb')->lang("RESYNC");
                    echo "</a>";
                }
                echo "]";
            }
            echo "</strong></p>

\t";
            // line 477
            if (twig_length_filter($this->env, $this->getAttribute((isset($context["loops"]) ? $context["loops"] : null), "forums", array()))) {
                // line 478
                echo "\t\t<table class=\"table1 forums\">
\t\t\t<col class=\"row1\" /><col class=\"row1\" /><col class=\"row2\" />
\t\t<tbody>
\t\t";
                // line 481
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["loops"]) ? $context["loops"] : null), "forums", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["forums"]) {
                    // line 482
                    echo "\t\t\t<tr>
\t\t\t\t<td class=\"folder\">";
                    // line 483
                    echo $this->getAttribute($context["forums"], "FOLDER_IMAGE", array());
                    echo "</td>
\t\t\t\t<td class=\"forum-desc\">
\t\t\t\t\t";
                    // line 485
                    if ($this->getAttribute($context["forums"], "FORUM_IMAGE", array())) {
                        echo "<div style=\"float: ";
                        echo (isset($context["S_CONTENT_FLOW_BEGIN"]) ? $context["S_CONTENT_FLOW_BEGIN"] : null);
                        echo "; margin-right: 5px;\">";
                        echo $this->getAttribute($context["forums"], "FORUM_IMAGE", array());
                        echo "</div>";
                    }
                    // line 486
                    echo "\t\t\t\t\t<strong>";
                    if ($this->getAttribute($context["forums"], "S_FORUM_LINK", array())) {
                        echo $this->getAttribute($context["forums"], "FORUM_NAME", array());
                    } else {
                        echo "<a href=\"";
                        echo $this->getAttribute($context["forums"], "U_FORUM", array());
                        echo "\">";
                        echo $this->getAttribute($context["forums"], "FORUM_NAME", array());
                        echo "</a>";
                    }
                    echo "</strong>
\t\t\t\t\t";
                    // line 487
                    if ($this->getAttribute($context["forums"], "FORUM_DESCRIPTION", array())) {
                        echo "<br /><span>";
                        echo $this->getAttribute($context["forums"], "FORUM_DESCRIPTION", array());
                        echo "</span>";
                    }
                    // line 488
                    echo "\t\t\t\t\t";
                    if ($this->getAttribute($context["forums"], "S_FORUM_POST", array())) {
                        echo "<br /><br /><span>";
                        echo $this->env->getExtension('phpbb')->lang("TOPICS");
                        echo $this->env->getExtension('phpbb')->lang("COLON");
                        echo " <strong>";
                        echo $this->getAttribute($context["forums"], "FORUM_TOPICS", array());
                        echo "</strong> / ";
                        echo $this->env->getExtension('phpbb')->lang("POSTS");
                        echo $this->env->getExtension('phpbb')->lang("COLON");
                        echo " <strong>";
                        echo $this->getAttribute($context["forums"], "FORUM_POSTS", array());
                        echo "</strong></span>";
                    }
                    // line 489
                    echo "\t\t\t\t</td>
\t\t\t\t<td class=\"actions\">
\t\t\t\t\t<span class=\"up-disabled\" style=\"display:none;\">";
                    // line 491
                    echo (isset($context["ICON_MOVE_UP_DISABLED"]) ? $context["ICON_MOVE_UP_DISABLED"] : null);
                    echo "</span>
\t\t\t\t\t<span class=\"up\"><a href=\"";
                    // line 492
                    echo $this->getAttribute($context["forums"], "U_MOVE_UP", array());
                    echo "\" data-ajax=\"row_up\">";
                    echo (isset($context["ICON_MOVE_UP"]) ? $context["ICON_MOVE_UP"] : null);
                    echo "</a></span>
\t\t\t\t\t<span class=\"down-disabled\" style=\"display:none;\">";
                    // line 493
                    echo (isset($context["ICON_MOVE_DOWN_DISABLED"]) ? $context["ICON_MOVE_DOWN_DISABLED"] : null);
                    echo "</span>
\t\t\t\t\t<span class=\"down\"><a href=\"";
                    // line 494
                    echo $this->getAttribute($context["forums"], "U_MOVE_DOWN", array());
                    echo "\" data-ajax=\"row_down\">";
                    echo (isset($context["ICON_MOVE_DOWN"]) ? $context["ICON_MOVE_DOWN"] : null);
                    echo "</a></span>
\t\t\t\t\t<a href=\"";
                    // line 495
                    echo $this->getAttribute($context["forums"], "U_EDIT", array());
                    echo "\">";
                    echo (isset($context["ICON_EDIT"]) ? $context["ICON_EDIT"] : null);
                    echo "</a>
\t\t\t\t\t";
                    // line 496
                    if ( !$this->getAttribute($context["forums"], "S_FORUM_LINK", array())) {
                        // line 497
                        echo "\t\t\t\t\t\t<a href=\"";
                        echo $this->getAttribute($context["forums"], "U_SYNC", array());
                        echo "\" onclick=\"popup_progress_bar();\">";
                        echo (isset($context["ICON_SYNC"]) ? $context["ICON_SYNC"] : null);
                        echo "</a>
\t\t\t\t\t";
                    } else {
                        // line 499
                        echo "\t\t\t\t\t\t";
                        echo (isset($context["ICON_SYNC_DISABLED"]) ? $context["ICON_SYNC_DISABLED"] : null);
                        echo "
\t\t\t\t\t";
                    }
                    // line 501
                    echo "\t\t\t\t\t<a href=\"";
                    echo $this->getAttribute($context["forums"], "U_DELETE", array());
                    echo "\">";
                    echo (isset($context["ICON_DELETE"]) ? $context["ICON_DELETE"] : null);
                    echo "</a>
\t\t\t\t</td>
\t\t\t</tr>
\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['forums'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 505
                echo "\t\t</tbody>
\t\t</table>
\t";
            }
            // line 508
            echo "
\t<form id=\"fselect\" method=\"post\" action=\"";
            // line 509
            echo (isset($context["U_SEL_ACTION"]) ? $context["U_SEL_ACTION"] : null);
            echo "\">

\t<fieldset class=\"quick\">
\t\t";
            // line 512
            echo $this->env->getExtension('phpbb')->lang("SELECT_FORUM");
            echo $this->env->getExtension('phpbb')->lang("COLON");
            echo " <select name=\"parent_id\" onchange=\"if(this.options[this.selectedIndex].value != -1){ this.form.submit(); }\">";
            echo (isset($context["FORUM_BOX"]) ? $context["FORUM_BOX"] : null);
            echo "</select>

\t\t";
            // line 514
            echo "<input class=\"button2\" type=\"submit\" value=\"";
            echo $this->env->getExtension('phpbb')->lang("GO");
            echo "\" />";
            // line 515
            echo "\t\t";
            echo (isset($context["S_FORM_TOKEN"]) ? $context["S_FORM_TOKEN"] : null);
            echo "
\t</fieldset>
\t</form>

\t<form id=\"forums\" method=\"post\" action=\"";
            // line 519
            echo (isset($context["U_ACTION"]) ? $context["U_ACTION"] : null);
            echo "\">

\t<fieldset class=\"quick\">
\t\t<input type=\"hidden\" name=\"action\" value=\"add\" />

\t\t<input type=\"text\" name=\"forum_name\" value=\"\" maxlength=\"255\" />
\t\t<input class=\"button2\" name=\"addforum\" type=\"submit\" value=\"";
            // line 525
            echo $this->env->getExtension('phpbb')->lang("CREATE_FORUM");
            echo "\" />
\t\t";
            // line 526
            echo (isset($context["S_FORM_TOKEN"]) ? $context["S_FORM_TOKEN"] : null);
            echo "
\t</fieldset>
\t</form>

";
        }
        // line 531
        echo "
";
        // line 532
        $location = "overall_footer.html";
        $namespace = false;
        if (strpos($location, '@') === 0) {
            $namespace = substr($location, 1, strpos($location, '/') - 1);
            $previous_look_up_order = $this->env->getNamespaceLookUpOrder();
            $this->env->setNamespaceLookUpOrder(array($namespace, '__main__'));
        }
        $this->loadTemplate("overall_footer.html", "acp_forums.html", 532)->display($context);
        if ($namespace) {
            $this->env->setNamespaceLookUpOrder($previous_look_up_order);
        }
    }

    public function getTemplateName()
    {
        return "acp_forums.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1606 => 532,  1603 => 531,  1595 => 526,  1591 => 525,  1582 => 519,  1574 => 515,  1570 => 514,  1562 => 512,  1556 => 509,  1553 => 508,  1548 => 505,  1535 => 501,  1529 => 499,  1521 => 497,  1519 => 496,  1513 => 495,  1507 => 494,  1503 => 493,  1497 => 492,  1493 => 491,  1489 => 489,  1474 => 488,  1468 => 487,  1455 => 486,  1447 => 485,  1442 => 483,  1439 => 482,  1435 => 481,  1430 => 478,  1428 => 477,  1404 => 475,  1401 => 474,  1395 => 471,  1391 => 470,  1382 => 463,  1380 => 462,  1377 => 461,  1371 => 458,  1367 => 457,  1364 => 456,  1362 => 455,  1357 => 453,  1352 => 451,  1344 => 446,  1332 => 436,  1326 => 433,  1321 => 431,  1316 => 429,  1309 => 425,  1302 => 420,  1300 => 419,  1293 => 415,  1288 => 413,  1284 => 411,  1280 => 409,  1272 => 407,  1270 => 406,  1266 => 405,  1261 => 404,  1258 => 403,  1255 => 402,  1251 => 400,  1243 => 398,  1241 => 397,  1237 => 396,  1232 => 395,  1229 => 394,  1227 => 393,  1222 => 391,  1217 => 390,  1212 => 388,  1206 => 385,  1203 => 384,  1197 => 381,  1193 => 380,  1190 => 379,  1188 => 378,  1183 => 376,  1178 => 374,  1169 => 372,  1166 => 371,  1164 => 370,  1157 => 366,  1153 => 365,  1149 => 364,  1145 => 363,  1141 => 361,  1140 => 360,  1135 => 357,  1134 => 356,  1125 => 354,  1117 => 353,  1109 => 352,  1105 => 351,  1098 => 350,  1095 => 349,  1089 => 346,  1084 => 345,  1081 => 344,  1079 => 343,  1074 => 341,  1067 => 340,  1064 => 339,  1063 => 338,  1059 => 337,  1045 => 330,  1037 => 329,  1030 => 328,  1024 => 325,  1017 => 324,  1007 => 321,  999 => 320,  992 => 319,  987 => 317,  980 => 312,  979 => 311,  972 => 309,  965 => 308,  957 => 305,  950 => 304,  940 => 301,  932 => 300,  925 => 299,  915 => 296,  907 => 295,  902 => 294,  892 => 291,  884 => 290,  879 => 289,  869 => 286,  861 => 285,  854 => 284,  846 => 281,  839 => 280,  831 => 277,  824 => 276,  816 => 273,  809 => 272,  799 => 269,  791 => 268,  784 => 267,  781 => 266,  780 => 265,  776 => 264,  771 => 261,  770 => 260,  763 => 256,  756 => 255,  748 => 250,  744 => 249,  737 => 245,  730 => 244,  720 => 241,  712 => 240,  705 => 239,  695 => 236,  687 => 235,  682 => 234,  672 => 231,  664 => 230,  657 => 229,  647 => 226,  639 => 225,  632 => 224,  622 => 221,  614 => 220,  607 => 219,  597 => 216,  589 => 215,  582 => 214,  572 => 211,  564 => 210,  557 => 209,  551 => 206,  546 => 205,  543 => 204,  542 => 203,  538 => 202,  524 => 195,  516 => 194,  509 => 193,  504 => 191,  498 => 187,  497 => 186,  490 => 184,  485 => 183,  482 => 182,  472 => 178,  469 => 177,  467 => 176,  460 => 174,  453 => 173,  445 => 170,  438 => 169,  434 => 167,  426 => 165,  424 => 164,  420 => 163,  413 => 162,  403 => 159,  395 => 158,  387 => 157,  383 => 156,  376 => 155,  370 => 152,  365 => 151,  362 => 150,  354 => 147,  347 => 146,  344 => 145,  342 => 144,  331 => 142,  326 => 141,  323 => 140,  318 => 137,  312 => 135,  304 => 133,  302 => 132,  297 => 131,  293 => 129,  290 => 128,  285 => 125,  277 => 124,  269 => 123,  264 => 122,  260 => 120,  258 => 119,  253 => 117,  248 => 116,  245 => 115,  244 => 114,  240 => 113,  234 => 110,  231 => 109,  225 => 106,  221 => 105,  218 => 104,  216 => 103,  211 => 101,  201 => 99,  192 => 97,  185 => 92,  181 => 90,  179 => 89,  176 => 88,  172 => 86,  170 => 85,  167 => 84,  163 => 82,  161 => 81,  158 => 80,  154 => 78,  152 => 77,  149 => 76,  146 => 75,  142 => 73,  139 => 72,  137 => 71,  134 => 70,  131 => 69,  127 => 67,  124 => 66,  122 => 65,  104 => 50,  94 => 43,  84 => 36,  81 => 35,  68 => 26,  66 => 25,  63 => 24,  50 => 15,  48 => 14,  38 => 6,  36 => 5,  31 => 2,  19 => 1,);
    }
}
