<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* Game/Six_qp/index.html.twig */
class __TwigTemplate_74071f7a75a0ce935b3105d174c18b8a extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'body' => [$this, 'block_body'],
            'nav' => [$this, 'block_nav'],
            'endGame' => [$this, 'block_endGame'],
            'ranking' => [$this, 'block_ranking'],
            'personalBoard' => [$this, 'block_personalBoard'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/index.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "Game/Six_qp/index.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        echo "Agora - 6 qui prend";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 5
    public function block_body($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 6
        echo "    <div class=\"h-screen w-screen\" ";
        echo $this->extensions['Symfony\UX\StimulusBundle\Twig\StimulusTwigExtension']->renderStimulusController("sixqp");
        echo ">
        <div class=\"h-full w-full\">
            ";
        // line 8
        $this->displayBlock('nav', $context, $blocks);
        // line 11
        echo "            ";
        $this->displayBlock('endGame', $context, $blocks);
        // line 14
        echo "                <div id=\"chosenCards\" class=\"flow float-left w-1/5 h-3/4\">
                ";
        // line 15
        echo twig_include($this->env, $context, "/Game/Six_qp/chosenCards.html.twig");
        echo "
                </div>
            ";
        // line 17
        $this->displayBlock('ranking', $context, $blocks);
        // line 22
        echo "                <div id=\"mainBoard\" class=\"flow float-start w-[63.3%] h-3/4\">
                    ";
        // line 23
        echo twig_include($this->env, $context, "/Game/Six_qp/mainBoard.html.twig");
        echo "
                </div>
            ";
        // line 25
        $this->displayBlock('personalBoard', $context, $blocks);
        // line 30
        echo "        </div>
    </div>
    <script>
        ";
        // line 33
        $context["path"] = (($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_game_show_sixqp", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["game"]) || array_key_exists("game", $context) ? $context["game"] : (function () { throw new RuntimeError('Variable "game" does not exist.', 33, $this->source); })()), "id", [], "any", false, false, false, 33)]) . "notifyPlayer") . twig_get_attribute($this->env, $this->source, (isset($context["player"]) || array_key_exists("player", $context) ? $context["player"] : (function () { throw new RuntimeError('Variable "player" does not exist.', 33, $this->source); })()), "id", [], "any", false, false, false, 33));
        // line 34
        echo "        const eventSourceNotification = new EventSource(\"";
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, $this->extensions['Symfony\Component\Mercure\Twig\MercureExtension']->mercure((isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 34, $this->source); })())), "js"), "html", null, true);
        echo "\");
        eventSourceNotification.onmessage = event => {
            for(let row of document.getElementsByClassName('rows')) {
                row.disabled = false;
            }
            //Notify player
            alert(\"Please choose a row\")
        }
    </script>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 8
    public function block_nav($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "nav"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "nav"));

        // line 9
        echo "                ";
        echo twig_include($this->env, $context, "/Game/Utils/navigation.html.twig");
        echo "
            ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 11
    public function block_endGame($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "endGame"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "endGame"));

        // line 12
        echo "                ";
        echo twig_include($this->env, $context, "/Game/Six_qp/endGameScreenResult.html.twig");
        echo "
            ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 17
    public function block_ranking($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ranking"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ranking"));

        // line 18
        echo "            <div id=\"ranking\" class=\"absolute bg-violet-100 w-1/6 h-full right-0\">
                ";
        // line 19
        echo twig_include($this->env, $context, "/Game/Six_qp/ranking.html.twig");
        echo "
            </div>
            ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 25
    public function block_personalBoard($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "personalBoard"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "personalBoard"));

        // line 26
        echo "                <div class=\"flex bg-green-500 h-1/4 w-5/6 float-bottom-0 \" id=\"personalBoard\">
                    ";
        // line 27
        echo twig_include($this->env, $context, "/Game/Six_qp/personalBoard.html.twig");
        echo "
                </div>
            ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "Game/Six_qp/index.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  236 => 27,  233 => 26,  223 => 25,  210 => 19,  207 => 18,  197 => 17,  184 => 12,  174 => 11,  161 => 9,  151 => 8,  130 => 34,  128 => 33,  123 => 30,  121 => 25,  116 => 23,  113 => 22,  111 => 17,  106 => 15,  103 => 14,  100 => 11,  98 => 8,  92 => 6,  82 => 5,  63 => 3,  40 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Agora - 6 qui prend{% endblock %}

{% block body %}
    <div class=\"h-screen w-screen\" {{ stimulus_controller('sixqp') }}>
        <div class=\"h-full w-full\">
            {% block nav %}
                {{ include('/Game/Utils/navigation.html.twig') }}
            {% endblock %}
            {% block endGame %}
                {{ include('/Game/Six_qp/endGameScreenResult.html.twig') }}
            {% endblock %}
                <div id=\"chosenCards\" class=\"flow float-left w-1/5 h-3/4\">
                {{ include('/Game/Six_qp/chosenCards.html.twig') }}
                </div>
            {% block ranking %}
            <div id=\"ranking\" class=\"absolute bg-violet-100 w-1/6 h-full right-0\">
                {{ include('/Game/Six_qp/ranking.html.twig') }}
            </div>
            {% endblock %}
                <div id=\"mainBoard\" class=\"flow float-start w-[63.3%] h-3/4\">
                    {{ include('/Game/Six_qp/mainBoard.html.twig') }}
                </div>
            {% block personalBoard %}
                <div class=\"flex bg-green-500 h-1/4 w-5/6 float-bottom-0 \" id=\"personalBoard\">
                    {{ include('/Game/Six_qp/personalBoard.html.twig') }}
                </div>
            {% endblock %}
        </div>
    </div>
    <script>
        {% set path = path('app_game_show_sixqp', {'id': game.id}) ~ 'notifyPlayer' ~ player.id %}
        const eventSourceNotification = new EventSource(\"{{ mercure(path)|escape('js') }}\");
        eventSourceNotification.onmessage = event => {
            for(let row of document.getElementsByClassName('rows')) {
                row.disabled = false;
            }
            //Notify player
            alert(\"Please choose a row\")
        }
    </script>
{% endblock %}
", "Game/Six_qp/index.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\Game\\Six_qp\\index.html.twig");
    }
}
