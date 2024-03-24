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

/* Game/Six_qp/chosenCards.html.twig */
class __TwigTemplate_5dc4e4d597cc600106864d367ba24626 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/chosenCards.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/chosenCards.html.twig"));

        // line 9
        echo "        <div class=\"bg-gray-500 rounded flow float-left w-full h-full overflow-hidden shadow-black shadow-lg\">
            <div class=\"grid grid-cols-2 grid-rows-5 w-full h-full pt-10 p-3\">
                ";
        // line 11
        $context["ind"] = 0;
        // line 12
        echo "                ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["chosenCards"]) || array_key_exists("chosenCards", $context) ? $context["chosenCards"] : (function () { throw new RuntimeError('Variable "chosenCards" does not exist.', 12, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["chosenCard"]) {
            // line 13
            echo "                        <div class=\" hover:z-10 hover:overflow-visible z-0 p-2\">
                        ";
            // line 14
            if ( !(null === $context["chosenCard"])) {
                // line 15
                echo "                            ";
                $context["hovering"] = "";
                // line 16
                echo "                            ";
                if ((((isset($context["ind"]) || array_key_exists("ind", $context) ? $context["ind"] : (function () { throw new RuntimeError('Variable "ind" does not exist.', 16, $this->source); })()) != 0) && ((isset($context["ind"]) || array_key_exists("ind", $context) ? $context["ind"] : (function () { throw new RuntimeError('Variable "ind" does not exist.', 16, $this->source); })()) != 1))) {
                    // line 17
                    echo "                                ";
                    $context["hovering"] = "transform hover:-translate-y-10";
                    // line 18
                    echo "                            ";
                } else {
                    // line 19
                    echo "                                ";
                    $context["hovering"] = "";
                    // line 20
                    echo "                            ";
                }
                // line 21
                echo "                            ";
                if ((twig_length_filter($this->env, (isset($context["chosenCards"]) || array_key_exists("chosenCards", $context) ? $context["chosenCards"] : (function () { throw new RuntimeError('Variable "chosenCards" does not exist.', 21, $this->source); })())) == twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["game"]) || array_key_exists("game", $context) ? $context["game"] : (function () { throw new RuntimeError('Variable "game" does not exist.', 21, $this->source); })()), "playerSixQPs", [], "any", false, false, false, 21), "count", [], "any", false, false, false, 21))) {
                    // line 22
                    echo "                                <img class=\"";
                    echo twig_escape_filter($this->env, ("rounded float float-left shadow-lg shadow-black aspect-auto
                                                hover:md:p-0" .                     // line 24
(isset($context["hovering"]) || array_key_exists("hovering", $context) ? $context["hovering"] : (function () { throw new RuntimeError('Variable "hovering" does not exist.', 24, $this->source); })())), "html", null, true);
                    // line 25
                    echo "\"
                                     src=\"";
                    // line 26
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl((("resourcesGames/6QP/cardsImages/" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,                     // line 27
$context["chosenCard"], "card", [], "any", false, false, false, 27), "value", [], "any", false, false, false, 27)) . ".png")), "html", null, true);
                    // line 28
                    echo "\"
                                     alt=\"";
                    // line 29
                    echo twig_escape_filter($this->env, (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["chosenCard"], "card", [], "any", false, false, false, 29), "value", [], "any", false, false, false, 29) . "card"), "html", null, true);
                    echo "\">
                            ";
                } else {
                    // line 31
                    echo "                                <img class=\"";
                    echo twig_escape_filter($this->env, ("rounded shadow-lg shadow-black aspect-auto hover:md:p-0" . (isset($context["hovering"]) || array_key_exists("hovering", $context) ? $context["hovering"] : (function () { throw new RuntimeError('Variable "hovering" does not exist.', 31, $this->source); })())), "html", null, true);
                    echo "\"
                                     src=\"";
                    // line 32
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl(("resourcesGames/6QP/cardsImages" . "/back.png")), "html", null, true);
                    echo "\"
                                     alt=\"back\">
                            ";
                }
                // line 35
                echo "                            ";
                $context["ind"] = ((isset($context["ind"]) || array_key_exists("ind", $context) ? $context["ind"] : (function () { throw new RuntimeError('Variable "ind" does not exist.', 35, $this->source); })()) + 1);
                // line 36
                echo "                        ";
            }
            // line 37
            echo "                        </div>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['chosenCard'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 39
        echo "            </div>
        </div>
        <script>
            ";
        // line 42
        $context["path"] = ($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_game_show_sixqp", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["game"]) || array_key_exists("game", $context) ? $context["game"] : (function () { throw new RuntimeError('Variable "game" does not exist.', 42, $this->source); })()), "id", [], "any", false, false, false, 42)]) . "chosenCards");
        // line 43
        echo "            const eventSourceChosenCards = new EventSource(\"";
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, $this->extensions['Symfony\Component\Mercure\Twig\MercureExtension']->mercure((isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 43, $this->source); })())), "js"), "html", null, true);
        echo "\");
            eventSourceChosenCards.onmessage = event => {
                let chosenCards = document.getElementById('chosenCards');
                chosenCards.innerHTML = event.data;
            }
        </script>";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "Game/Six_qp/chosenCards.html.twig";
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
        return array (  130 => 43,  128 => 42,  123 => 39,  116 => 37,  113 => 36,  110 => 35,  104 => 32,  99 => 31,  94 => 29,  91 => 28,  89 => 27,  88 => 26,  85 => 25,  83 => 24,  80 => 22,  77 => 21,  74 => 20,  71 => 19,  68 => 18,  65 => 17,  62 => 16,  59 => 15,  57 => 14,  54 => 13,  49 => 12,  47 => 11,  43 => 9,);
    }

    public function getSourceContext()
    {
        return new Source("{#
    This section displays the cards who have been chosen for the round by players
    @param : chosenCards -> A list of pictures chosen by players
             playersNumer -> An integer of the number of current players in this game
    @return : chosenCards.length == playersNumber => display a grid of the visible chosen cards
              chosenCards.length != playersNumber => display a grid of hidden face of the chosen cards
                                                     (the back)
#}
        <div class=\"bg-gray-500 rounded flow float-left w-full h-full overflow-hidden shadow-black shadow-lg\">
            <div class=\"grid grid-cols-2 grid-rows-5 w-full h-full pt-10 p-3\">
                {% set ind = 0 %}
                {% for chosenCard in chosenCards%}
                        <div class=\" hover:z-10 hover:overflow-visible z-0 p-2\">
                        {% if chosenCard is not null %}
                            {% set hovering = \"\" %}
                            {% if ind != 0 and ind != 1 %}
                                {% set hovering = \"transform hover:-translate-y-10\" %}
                            {% else %}
                                {% set hovering = \"\" %}
                            {% endif %}
                            {% if chosenCards|length == game.playerSixQPs.count %}
                                <img class=\"{{
                                                'rounded float float-left shadow-lg shadow-black aspect-auto
                                                hover:md:p-0' ~ hovering
                                        }}\"
                                     src=\"{{
                                            asset('resourcesGames/6QP/cardsImages/' ~ chosenCard.card.value ~ '.png')
                                        }}\"
                                     alt=\"{{ chosenCard.card.value ~ 'card' }}\">
                            {% else %}
                                <img class=\"{{'rounded shadow-lg shadow-black aspect-auto hover:md:p-0' ~ hovering}}\"
                                     src=\"{{ asset('resourcesGames/6QP/cardsImages' ~ \"/back.png\") }}\"
                                     alt=\"back\">
                            {% endif %}
                            {% set ind = ind + 1 %}
                        {% endif %}
                        </div>
                {% endfor %}
            </div>
        </div>
        <script>
            {% set path = path('app_game_show_sixqp', {'id': game.id}) ~ 'chosenCards' %}
            const eventSourceChosenCards = new EventSource(\"{{ mercure(path)|escape('js') }}\");
            eventSourceChosenCards.onmessage = event => {
                let chosenCards = document.getElementById('chosenCards');
                chosenCards.innerHTML = event.data;
            }
        </script>", "Game/Six_qp/chosenCards.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\Game\\Six_qp\\chosenCards.html.twig");
    }
}
