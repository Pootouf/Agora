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

/* Game/Six_qp/mainBoard.html.twig */
class __TwigTemplate_894b3f7b8664812d74ca7406435f3051 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/mainBoard.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/mainBoard.html.twig"));

        // line 6
        echo "    <div class=\"p-2 bg-red-500 flow float-start w-full h-full overflow-hidden
                shadow-black shadow-[10px_10px_15px_-3px_rgba(0,0,0,0.1)] -z-1\">
        <div class=\"flex flex-col h-full w-full ml-4\">
            ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["rows"]) || array_key_exists("rows", $context) ? $context["rows"] : (function () { throw new RuntimeError('Variable "rows" does not exist.', 9, $this->source); })()), "values", [], "any", false, false, false, 9));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 10
            echo "                <div class=\"h-1/4\">
                    <button disabled class=\"rows button h-full\"
                    ";
            // line 12
            echo $this->extensions['Symfony\UX\StimulusBundle\Twig\StimulusTwigExtension']->renderStimulusAction("sixqp", "selectRow", "click", ["url" => $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_game_sixqp_placecardonrow", ["idRow" => twig_get_attribute($this->env, $this->source,             // line 13
$context["row"], "id", [], "any", false, false, false, 13), "idGame" => twig_get_attribute($this->env, $this->source, (isset($context["game"]) || array_key_exists("game", $context) ? $context["game"] : (function () { throw new RuntimeError('Variable "game" does not exist.', 13, $this->source); })()), "id", [], "any", false, false, false, 13)])]);
            echo ">
                    ";
            // line 14
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["row"], "cards", [], "any", false, false, false, 14));
            foreach ($context['_seq'] as $context["_key"] => $context["card"]) {
                // line 15
                echo "                        <img class=\"h-full float float-left\"
                             src=\"";
                // line 16
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl((("resourcesGames/6QP/cardsImages/" . twig_get_attribute($this->env, $this->source, $context["card"], "value", [], "any", false, false, false, 16)) . ".png")), "html", null, true);
                echo "\"
                             alt=\"card\">
                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['card'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 19
            echo "                    </button>
                </div>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 22
        echo "        </div>
    </div>
    <script>
        ";
        // line 25
        $context["path"] = ($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_game_show_sixqp", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["game"]) || array_key_exists("game", $context) ? $context["game"] : (function () { throw new RuntimeError('Variable "game" does not exist.', 25, $this->source); })()), "id", [], "any", false, false, false, 25)]) . "mainBoard");
        // line 26
        echo "        const eventSourceMainBoard = new EventSource(\"";
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, $this->extensions['Symfony\Component\Mercure\Twig\MercureExtension']->mercure((isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 26, $this->source); })())), "js"), "html", null, true);
        echo "\");
        eventSourceMainBoard.onmessage = event => {
            let mainBoard = document.getElementById('mainBoard');
            mainBoard.innerHTML = event.data;
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
        return "Game/Six_qp/mainBoard.html.twig";
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
        return array (  92 => 26,  90 => 25,  85 => 22,  77 => 19,  68 => 16,  65 => 15,  61 => 14,  57 => 13,  56 => 12,  52 => 10,  48 => 9,  43 => 6,);
    }

    public function getSourceContext()
    {
        return new Source("{#
This section displays the main board of the game
@param : rows -> A collection of the existing rows
@return : display a grid of the cards on the main board
#}
    <div class=\"p-2 bg-red-500 flow float-start w-full h-full overflow-hidden
                shadow-black shadow-[10px_10px_15px_-3px_rgba(0,0,0,0.1)] -z-1\">
        <div class=\"flex flex-col h-full w-full ml-4\">
            {% for row in rows.values %}
                <div class=\"h-1/4\">
                    <button disabled class=\"rows button h-full\"
                    {{ stimulus_action('sixqp', 'selectRow', 'click',
                        {url: path('app_game_sixqp_placecardonrow', {idRow: row.id, idGame: game.id})}) }}>
                    {% for card in row.cards %}
                        <img class=\"h-full float float-left\"
                             src=\"{{ asset('resourcesGames/6QP/cardsImages/' ~ card.value ~ \".png\")}}\"
                             alt=\"card\">
                    {% endfor %}
                    </button>
                </div>
            {% endfor %}
        </div>
    </div>
    <script>
        {% set path = path('app_game_show_sixqp', {'id': game.id}) ~ 'mainBoard' %}
        const eventSourceMainBoard = new EventSource(\"{{ mercure(path)|escape('js') }}\");
        eventSourceMainBoard.onmessage = event => {
            let mainBoard = document.getElementById('mainBoard');
            mainBoard.innerHTML = event.data;
        }
    </script>", "Game/Six_qp/mainBoard.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\Game\\Six_qp\\mainBoard.html.twig");
    }
}
