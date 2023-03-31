<?php

/* forumlist_body.html */
class __TwigTemplate_4c6f28cfd99596d33fc5eaf45c21ddfbb44d83a9b5f3d8d17ef41961a0adf968 extends Twig_Template
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
        echo "
";
        // line 2
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["loops"]) ? $context["loops"] : null), "forumrow", array()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["forumrow"]) {
            // line 3
            echo "\t";
            if ((($this->getAttribute($context["forumrow"], "S_IS_CAT", array()) &&  !$this->getAttribute($context["forumrow"], "S_FIRST_ROW", array())) || $this->getAttribute($context["forumrow"], "S_NO_CAT", array()))) {
                // line 4
                echo "\t\t\t</ul>

\t\t\t</div>
\t\t</div>
\t";
            }
            // line 9
            echo "
\t";
            // line 10
            // line 11
            echo "\t";
            if ((($this->getAttribute($context["forumrow"], "S_IS_CAT", array()) || $this->getAttribute($context["forumrow"], "S_FIRST_ROW", array())) || $this->getAttribute($context["forumrow"], "S_NO_CAT", array()))) {
                // line 12
                echo "\t\t<div class=\"forabg category-";
                echo $this->getAttribute($context["forumrow"], "FORUM_ID", array());
                if (($this->getAttribute((isset($context["definition"]) ? $context["definition"] : null), "STANDARD_FORUMS_LAYOUT", array()) == 0)) {
                    echo " elegant";
                }
                echo "\" data-hide-description=\"";
                echo $this->getAttribute((isset($context["definition"]) ? $context["definition"] : null), "HIDE_FORUM_DESCRIPTION", array());
                echo "\">
\t\t\t<div class=\"inner\">
\t\t\t<ul class=\"topiclist\">
\t\t\t\t<li class=\"header\">
\t\t\t\t\t";
                // line 16
                // line 17
                echo "\t\t\t\t\t<dl class=\"icon\">
\t\t\t\t\t\t<dt><div class=\"list-inner\">Your Discussions</div></dt>
                        
\t\t\t\t\t\t<dd class=\"topics\">";
                // line 20
                echo $this->env->getExtension('phpbb')->lang("TOPICS");
                echo "</dd>
\t\t\t\t\t\t<dd class=\"posts\">";
                // line 21
                echo $this->env->getExtension('phpbb')->lang("POSTS");
                echo "</dd>
\t\t\t\t\t\t<dd class=\"lastpost\"><span>";
                // line 22
                echo $this->env->getExtension('phpbb')->lang("LAST_POST");
                echo "</span></dd>
\t\t\t\t\t</dl>
\t\t\t\t\t";
                // line 24
                // line 25
                echo "\t\t\t\t</li>
\t\t\t</ul>
\t\t\t<ul class=\"topiclist forums\">
\t";
            }
            // line 29
            echo "\t";
            // line 30
            echo "
\t";
            // line 31
            if ( !$this->getAttribute($context["forumrow"], "S_IS_CAT", array())) {
                // line 32
                echo "\t\t";
                // line 33
                echo "\t\t<li class=\"row forum-";
                echo $this->getAttribute($context["forumrow"], "FORUM_ID", array());
                echo "\">
\t\t\t";
                // line 34
                // line 35
                echo "\t\t\t";
                ob_start();
                // line 36
                echo "\t\t\t";
                if (((($this->getAttribute((isset($context["definition"]) ? $context["definition"] : null), "STANDARD_FORUMS_LAYOUT", array()) == 0) &&  !$this->getAttribute($context["forumrow"], "CLICKS", array())) &&  !$this->getAttribute($context["forumrow"], "S_IS_LINK", array()))) {
                    // line 37
                    echo "\t\t\t\t<div class=\"forum-statistics\">
\t\t\t\t\t<span class=\"dfn\">";
                    // line 38
                    echo $this->env->getExtension('phpbb')->lang("TOPICS");
                    echo "</span>";
                    echo $this->env->getExtension('phpbb')->lang("COLON");
                    echo " <span class=\"value\">";
                    echo $this->getAttribute($context["forumrow"], "TOPICS", array());
                    echo "</span><span class=\"comma\">";
                    echo $this->env->getExtension('phpbb')->lang("COMMA_SEPARATOR");
                    echo "</span>
\t\t\t\t\t<span class=\"dfn\">";
                    // line 39
                    echo $this->env->getExtension('phpbb')->lang("POSTS");
                    echo "</span>";
                    echo $this->env->getExtension('phpbb')->lang("COLON");
                    echo " <span class=\"value\">";
                    echo $this->getAttribute($context["forumrow"], "POSTS", array());
                    echo "</span>
\t\t\t\t</div>
\t\t\t";
                }
                // line 42
                echo "\t\t\t";
                $value = ('' === $value = ob_get_clean()) ? '' : new \Twig_Markup($value, $this->env->getCharset());
                $context['definition']->set('EXTRA_DESC', $value);
                // line 43
                echo "\t\t\t<dl class=\"icon ";
                echo $this->getAttribute($context["forumrow"], "FORUM_IMG_STYLE", array());
                if (trim($this->getAttribute((isset($context["definition"]) ? $context["definition"] : null), "EXTRA_DESC", array()))) {
                    echo " elegant-row";
                }
                echo "\">
\t\t\t\t<dt title=\"";
                // line 44
                echo $this->getAttribute($context["forumrow"], "FORUM_FOLDER_IMG_ALT", array());
                echo "\">
\t\t\t\t\t";
                // line 45
                if ($this->getAttribute($context["forumrow"], "S_UNREAD_FORUM", array())) {
                    echo "<a href=\"";
                    echo $this->getAttribute($context["forumrow"], "U_VIEWFORUM", array());
                    echo "\" class=\"icon-link\"></a>";
                }
                // line 46
                echo "\t\t\t\t\t<div class=\"list-inner\">
\t\t\t\t\t\t";
                // line 47
                if (((isset($context["S_ENABLE_FEEDS"]) ? $context["S_ENABLE_FEEDS"] : null) && $this->getAttribute($context["forumrow"], "S_FEED_ENABLED", array()))) {
                    echo "<!-- <a class=\"feed-icon-forum\" title=\"";
                    echo $this->env->getExtension('phpbb')->lang("FEED");
                    echo " - ";
                    echo $this->getAttribute($context["forumrow"], "FORUM_NAME", array());
                    echo "\" href=\"";
                    echo (isset($context["U_FEED"]) ? $context["U_FEED"] : null);
                    echo "?f=";
                    echo $this->getAttribute($context["forumrow"], "FORUM_ID", array());
                    echo "\"></a> -->";
                }
                // line 48
                echo "
\t\t\t\t\t\t";
                // line 49
                if ($this->getAttribute($context["forumrow"], "FORUM_IMAGE", array())) {
                    echo "<span class=\"forum-image\">";
                    echo $this->getAttribute($context["forumrow"], "FORUM_IMAGE", array());
                    echo "</span>";
                }
                // line 50
                echo "\t\t\t\t\t\t<a href=\"";
                echo $this->getAttribute($context["forumrow"], "U_VIEWFORUM", array());
                echo "\" class=\"forumtitle\" data-id=\"";
                echo $this->getAttribute($context["forumrow"], "FORUM_ID", array());
                echo "\">";
                echo $this->getAttribute($context["forumrow"], "FORUM_NAME", array());
                echo "</a>
\t\t\t\t\t\t";
                // line 51
                if ($this->getAttribute($context["forumrow"], "FORUM_DESC", array())) {
                    echo "<div class=\"forum-description\">";
                    echo $this->getAttribute($context["forumrow"], "FORUM_DESC", array());
                    echo "</div>";
                }
                // line 52
                echo "\t\t\t\t\t\t";
                echo $this->getAttribute((isset($context["definition"]) ? $context["definition"] : null), "EXTRA_DESC", array());
                echo "
\t\t\t\t\t\t";
                // line 53
                if ($this->getAttribute($context["forumrow"], "MODERATORS", array())) {
                    // line 54
                    echo "\t\t\t\t\t\t\t<div class=\"forum-moderators\"><strong>";
                    echo $this->getAttribute($context["forumrow"], "L_MODERATOR_STR", array());
                    echo $this->env->getExtension('phpbb')->lang("COLON");
                    echo "</strong> ";
                    echo $this->getAttribute($context["forumrow"], "MODERATORS", array());
                    echo "</div>
\t\t\t\t\t\t";
                }
                // line 56
                echo "\t\t\t\t\t\t";
                if ((twig_length_filter($this->env, $this->getAttribute($context["forumrow"], "subforum", array())) && $this->getAttribute($context["forumrow"], "S_LIST_SUBFORUMS", array()))) {
                    // line 57
                    echo "\t\t\t\t\t\t\t<div class=\"subforums-list\">
\t\t\t\t\t\t\t";
                    // line 58
                    // line 59
                    echo "\t\t\t\t\t\t\t<strong>";
                    echo $this->getAttribute($context["forumrow"], "L_SUBFORUM_STR", array());
                    echo $this->env->getExtension('phpbb')->lang("COLON");
                    echo "</strong>
\t\t\t\t\t\t\t<ul>
\t\t\t\t\t\t\t";
                    // line 61
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["forumrow"], "subforum", array()));
                    foreach ($context['_seq'] as $context["_key"] => $context["subforum"]) {
                        // line 62
                        echo "\t\t\t\t\t\t\t\t<li><a href=\"";
                        echo $this->getAttribute($context["subforum"], "U_SUBFORUM", array());
                        echo "\" class=\"subforum";
                        if ($this->getAttribute($context["subforum"], "S_UNREAD", array())) {
                            echo " unread";
                        } else {
                            echo " read";
                        }
                        echo "\" title=\"";
                        if ($this->getAttribute($context["subforum"], "S_UNREAD", array())) {
                            echo $this->env->getExtension('phpbb')->lang("UNREAD_POSTS");
                        } else {
                            echo $this->env->getExtension('phpbb')->lang("NO_UNREAD_POSTS");
                        }
                        echo "\">";
                        echo $this->getAttribute($context["subforum"], "SUBFORUM_NAME", array());
                        echo "</a>";
                        if ( !$this->getAttribute($context["subforum"], "S_LAST_ROW", array())) {
                            echo "<span>";
                            echo $this->env->getExtension('phpbb')->lang("COMMA_SEPARATOR");
                            echo "</span>";
                        }
                        echo "</li>
\t\t\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subforum'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 64
                    echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t";
                    // line 65
                    // line 66
                    echo "\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
                }
                // line 68
                echo "
\t\t\t\t\t\t";
                // line 69
                if ( !(isset($context["S_IS_BOT"]) ? $context["S_IS_BOT"] : null)) {
                    // line 70
                    echo "\t\t\t\t\t\t";
                    if ((trim($this->getAttribute((isset($context["definition"]) ? $context["definition"] : null), "EXTRA_DESC", array())) == "")) {
                        // line 71
                        echo "\t\t\t\t\t\t<div class=\"responsive-show\" style=\"display: none;\">
\t\t\t\t\t\t\t";
                        // line 72
                        if ($this->getAttribute($context["forumrow"], "CLICKS", array())) {
                            // line 73
                            echo "\t\t\t\t\t\t\t\t";
                            echo $this->env->getExtension('phpbb')->lang("REDIRECTS");
                            echo $this->env->getExtension('phpbb')->lang("COLON");
                            echo " <strong>";
                            echo $this->getAttribute($context["forumrow"], "CLICKS", array());
                            echo "</strong>
\t\t\t\t\t\t\t";
                        } elseif (( !$this->getAttribute(                        // line 74
$context["forumrow"], "S_IS_LINK", array()) && $this->getAttribute($context["forumrow"], "TOPICS", array()))) {
                            // line 75
                            echo "\t\t\t\t\t\t\t\t";
                            echo $this->env->getExtension('phpbb')->lang("TOPICS");
                            echo $this->env->getExtension('phpbb')->lang("COLON");
                            echo " <strong>";
                            echo $this->getAttribute($context["forumrow"], "TOPICS", array());
                            echo "</strong>
\t\t\t\t\t\t\t";
                        }
                        // line 77
                        echo "\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
                    }
                    // line 79
                    echo "\t\t\t\t\t\t\t";
                    if (( !$this->getAttribute($context["forumrow"], "S_IS_LINK", array()) && $this->getAttribute($context["forumrow"], "LAST_POST_TIME", array()))) {
                        // line 80
                        echo "\t\t\t\t\t\t\t<div class=\"forum-lastpost\" style=\"display: none;\">
\t\t\t\t\t\t\t\t<span><strong>";
                        // line 81
                        echo $this->env->getExtension('phpbb')->lang("LAST_POST");
                        echo $this->env->getExtension('phpbb')->lang("COLON");
                        echo "</strong> <a href=\"";
                        echo $this->getAttribute($context["forumrow"], "U_LAST_POST", array());
                        echo "\" title=\"";
                        echo $this->getAttribute($context["forumrow"], "LAST_POST_SUBJECT", array());
                        echo "\" class=\"lastsubject\">";
                        echo $this->getAttribute($context["forumrow"], "LAST_POST_SUBJECT_TRUNCATED", array());
                        echo "</a></span>
\t\t\t\t\t\t\t\t<span>";
                        // line 82
                        echo $this->env->getExtension('phpbb')->lang("POST_BY_AUTHOR");
                        echo " ";
                        echo $this->getAttribute($context["forumrow"], "LAST_POSTER_FULL", array());
                        echo ", ";
                        echo $this->getAttribute($context["forumrow"], "LAST_POST_TIME", array());
                        echo "</span>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t";
                    }
                    // line 85
                    echo "\t\t\t\t\t\t";
                }
                // line 86
                echo "
\t\t\t\t\t\t";
                // line 87
                if ($this->getAttribute($context["forumrow"], "U_UNAPPROVED_TOPICS", array())) {
                    // line 88
                    echo "\t\t\t\t\t\t\t<a href=\"";
                    echo $this->getAttribute($context["forumrow"], "U_UNAPPROVED_TOPICS", array());
                    echo "\" class=\"forum-mcplink\">";
                    echo (isset($context["UNAPPROVED_IMG"]) ? $context["UNAPPROVED_IMG"] : null);
                    echo "</a>
\t\t\t\t\t\t";
                } elseif ($this->getAttribute(                // line 89
$context["forumrow"], "U_UNAPPROVED_POSTS", array())) {
                    // line 90
                    echo "\t\t\t\t\t\t\t<a href=\"";
                    echo $this->getAttribute($context["forumrow"], "U_UNAPPROVED_POSTS", array());
                    echo "\" class=\"forum-mcplink\">";
                    echo (isset($context["UNAPPROVED_POST_IMG"]) ? $context["UNAPPROVED_POST_IMG"] : null);
                    echo "</a>
\t\t\t\t\t\t";
                }
                // line 92
                echo "\t\t\t\t\t</div>
\t\t\t\t</dt>
\t\t\t\t";
                // line 94
                if ($this->getAttribute($context["forumrow"], "CLICKS", array())) {
                    // line 95
                    echo "\t\t\t\t\t<dd class=\"redirect\"><span>";
                    echo $this->env->getExtension('phpbb')->lang("REDIRECTS");
                    echo $this->env->getExtension('phpbb')->lang("COLON");
                    echo " ";
                    echo $this->getAttribute($context["forumrow"], "CLICKS", array());
                    echo "</span></dd>
\t\t\t\t";
                } elseif ( !$this->getAttribute(                // line 96
$context["forumrow"], "S_IS_LINK", array())) {
                    // line 97
                    echo "\t\t\t\t\t";
                    if (($this->getAttribute((isset($context["definition"]) ? $context["definition"] : null), "STANDARD_FORUMS_LAYOUT", array()) != 0)) {
                        // line 98
                        echo "\t\t\t\t\t<dd class=\"topics\">";
                        echo $this->getAttribute($context["forumrow"], "TOPICS", array());
                        echo " <dfn>";
                        echo $this->env->getExtension('phpbb')->lang("TOPICS");
                        echo "</dfn></dd>
\t\t\t\t\t<dd class=\"posts\">";
                        // line 99
                        echo $this->getAttribute($context["forumrow"], "POSTS", array());
                        echo " <dfn>";
                        echo $this->env->getExtension('phpbb')->lang("POSTS");
                        echo "</dfn></dd>
\t\t\t\t\t";
                    }
                    // line 101
                    echo "\t\t\t\t\t<dd class=\"lastpost\"><span>
\t\t\t\t\t\t";
                    // line 102
                    if ($this->getAttribute($context["forumrow"], "LAST_POST_TIME", array())) {
                        echo "<dfn>";
                        echo $this->env->getExtension('phpbb')->lang("LAST_POST");
                        echo "</dfn>
\t\t\t\t\t\t";
                        // line 103
                        if ($this->getAttribute($context["forumrow"], "S_DISPLAY_SUBJECT", array())) {
                            // line 104
                            echo "\t\t\t\t\t\t\t";
                            // line 105
                            echo "\t\t\t\t\t\t\t<a href=\"";
                            echo $this->getAttribute($context["forumrow"], "U_LAST_POST", array());
                            echo "\" title=\"";
                            echo $this->getAttribute($context["forumrow"], "LAST_POST_SUBJECT", array());
                            echo "\" class=\"lastsubject\">";
                            echo $this->getAttribute($context["forumrow"], "LAST_POST_SUBJECT_TRUNCATED", array());
                            echo "</a> <br />
\t\t\t\t\t\t";
                        }
                        // line 106
                        echo " 
\t\t\t\t\t\t";
                        // line 107
                        echo $this->env->getExtension('phpbb')->lang("POST_BY_AUTHOR");
                        echo " ";
                        echo $this->getAttribute($context["forumrow"], "LAST_POSTER_FULL", array());
                        echo "
\t\t\t\t\t\t";
                        // line 108
                        if ( !(isset($context["S_IS_BOT"]) ? $context["S_IS_BOT"] : null)) {
                            echo "<a href=\"";
                            echo $this->getAttribute($context["forumrow"], "U_LAST_POST", array());
                            echo "\">";
                            echo (isset($context["LAST_POST_IMG"]) ? $context["LAST_POST_IMG"] : null);
                            echo "</a> ";
                        }
                        echo "<br />";
                        echo $this->getAttribute($context["forumrow"], "LAST_POST_TIME", array());
                    } else {
                        echo $this->env->getExtension('phpbb')->lang("NO_POSTS");
                        echo "<br />&nbsp;";
                    }
                    echo "</span>
\t\t\t\t\t</dd>
\t\t\t\t";
                } else {
                    // line 111
                    echo "\t\t\t\t\t<dd>&nbsp;</dd>
\t\t\t\t";
                }
                // line 113
                echo "\t\t\t</dl>
\t\t\t";
                // line 114
                // line 115
                echo "\t\t</li>
\t\t";
                // line 116
                // line 117
                echo "\t";
            }
            // line 118
            echo "
\t";
            // line 119
            if ($this->getAttribute($context["forumrow"], "S_LAST_ROW", array())) {
                // line 120
                echo "\t\t\t</ul>

\t\t\t</div>
\t\t</div>
\t";
                // line 124
                // line 125
                echo "\t";
            }
            // line 126
            echo "
";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 128
            echo "\t<div class=\"panel\">
\t\t<div class=\"inner\">
\t\t<strong>";
            // line 130
            echo $this->env->getExtension('phpbb')->lang("NO_FORUMS");
            echo "</strong>
\t\t</div>
\t</div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['forumrow'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "forumlist_body.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  470 => 130,  466 => 128,  460 => 126,  457 => 125,  456 => 124,  450 => 120,  448 => 119,  445 => 118,  442 => 117,  441 => 116,  438 => 115,  437 => 114,  434 => 113,  430 => 111,  412 => 108,  406 => 107,  403 => 106,  393 => 105,  391 => 104,  389 => 103,  383 => 102,  380 => 101,  373 => 99,  366 => 98,  363 => 97,  361 => 96,  353 => 95,  351 => 94,  347 => 92,  339 => 90,  337 => 89,  330 => 88,  328 => 87,  325 => 86,  322 => 85,  312 => 82,  301 => 81,  298 => 80,  295 => 79,  291 => 77,  282 => 75,  280 => 74,  272 => 73,  270 => 72,  267 => 71,  264 => 70,  262 => 69,  259 => 68,  255 => 66,  254 => 65,  251 => 64,  222 => 62,  218 => 61,  211 => 59,  210 => 58,  207 => 57,  204 => 56,  195 => 54,  193 => 53,  188 => 52,  182 => 51,  173 => 50,  167 => 49,  164 => 48,  152 => 47,  149 => 46,  143 => 45,  139 => 44,  131 => 43,  127 => 42,  117 => 39,  107 => 38,  104 => 37,  101 => 36,  98 => 35,  97 => 34,  92 => 33,  90 => 32,  88 => 31,  85 => 30,  83 => 29,  77 => 25,  76 => 24,  71 => 22,  67 => 21,  63 => 20,  58 => 17,  57 => 16,  44 => 12,  41 => 11,  40 => 10,  37 => 9,  30 => 4,  27 => 3,  22 => 2,  19 => 1,);
    }
}
