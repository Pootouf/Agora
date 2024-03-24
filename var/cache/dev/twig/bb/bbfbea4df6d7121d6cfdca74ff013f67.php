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

/* Game/Six_qp/personalBoard.html.twig */
class __TwigTemplate_7519fa824956d23dff7f521ac199e35f extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'personalBoard' => [$this, 'block_personalBoard'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/personalBoard.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/personalBoard.html.twig"));

        // line 1
        $this->displayBlock('personalBoard', $context, $blocks);
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    public function block_personalBoard($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "personalBoard"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "personalBoard"));

        // line 2
        echo "    <div class=\"grid grid-rows-2 grid-cols-5 w-full mx-2 mt-5
                landscape:md:flex landscape:md:mt-2 landscape:md-justify-center landscape:md:align-center
                lg:flex lg:mt-2 lg:justify-center lg:align-center\">
        ";
        // line 5
        $context["gameId"] = twig_get_attribute($this->env, $this->source, (isset($context["game"]) || array_key_exists("game", $context) ? $context["game"] : (function () { throw new RuntimeError('Variable "game" does not exist.', 5, $this->source); })()), "id", [], "any", false, false, false, 5);
        // line 6
        echo "        ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["playerCards"]) || array_key_exists("playerCards", $context) ? $context["playerCards"] : (function () { throw new RuntimeError('Variable "playerCards" does not exist.', 6, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["card"]) {
            // line 7
            echo "            <div class=\"hover:z-10 hover:overflow-visible hover:-translate-y-14 w-fit overflow-hidden\">
                ";
            // line 8
            $context["cardNumber"] = twig_get_attribute($this->env, $this->source, $context["card"], "value", [], "any", false, false, false, 8);
            // line 9
            echo "                ";
            $context["selectCardRoute"] = ((("/game/" . (isset($context["gameId"]) || array_key_exists("gameId", $context) ? $context["gameId"] : (function () { throw new RuntimeError('Variable "gameId" does not exist.', 9, $this->source); })())) . "/sixqp/select/") . (isset($context["cardNumber"]) || array_key_exists("cardNumber", $context) ? $context["cardNumber"] : (function () { throw new RuntimeError('Variable "cardNumber" does not exist.', 9, $this->source); })()));
            // line 10
            echo "                <button type=\"button\" ";
            echo $this->extensions['Symfony\UX\StimulusBundle\Twig\StimulusTwigExtension']->renderStimulusAction("sixqp", "selectCard", "click", ["url" => $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_game_sixqp_select", ["idCard" => twig_get_attribute($this->env, $this->source,             // line 11
$context["card"], "id", [], "any", false, false, false, 11), "idGame" => twig_get_attribute($this->env, $this->source, (isset($context["game"]) || array_key_exists("game", $context) ? $context["game"] : (function () { throw new RuntimeError('Variable "game" does not exist.', 11, $this->source); })()), "id", [], "any", false, false, false, 11)])]);
            echo ">
                    <img class=\"z-0\"
                         src=\"";
            // line 13
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl((("resourcesGames/6QP/cardsImages/" . twig_get_attribute($this->env, $this->source, $context["card"], "value", [], "any", false, false, false, 13)) . ".png")), "html", null, true);
            echo "\"
                         alt=\"";
            // line 14
            echo twig_escape_filter($this->env, (isset($context["cardNumber"]) || array_key_exists("cardNumber", $context) ? $context["cardNumber"] : (function () { throw new RuntimeError('Variable "cardNumber" does not exist.', 14, $this->source); })()), "html", null, true);
            echo "\"
                    >
                </button>
            </div>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['card'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 19
        echo "    </div>
    <script>
        ";
        // line 21
        $context["path"] = (($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_game_show_sixqp", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["game"]) || array_key_exists("game", $context) ? $context["game"] : (function () { throw new RuntimeError('Variable "game" does not exist.', 21, $this->source); })()), "id", [], "any", false, false, false, 21)]) . "personalBoard") . twig_get_attribute($this->env, $this->source, (isset($context["player"]) || array_key_exists("player", $context) ? $context["player"] : (function () { throw new RuntimeError('Variable "player" does not exist.', 21, $this->source); })()), "id", [], "any", false, false, false, 21));
        // line 22
        echo "        const eventSourcePersonalBoard = new EventSource(\"";
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, $this->extensions['Symfony\Component\Mercure\Twig\MercureExtension']->mercure((isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 22, $this->source); })())), "js"), "html", null, true);
        echo "\");
        eventSourcePersonalBoard.onmessage = event => {
            let personalBoard = document.getElementById('personalBoard');
            personalBoard.innerHTML = event.data;
        }
    </script>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "Game/Six_qp/personalBoard.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  111 => 22,  109 => 21,  105 => 19,  94 => 14,  90 => 13,  85 => 11,  83 => 10,  80 => 9,  78 => 8,  75 => 7,  70 => 6,  68 => 5,  63 => 2,  44 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% block personalBoard %}
    <div class=\"grid grid-rows-2 grid-cols-5 w-full mx-2 mt-5
                landscape:md:flex landscape:md:mt-2 landscape:md-justify-center landscape:md:align-center
                lg:flex lg:mt-2 lg:justify-center lg:align-center\">
        {% set gameId = game.id %}
        {% for card in playerCards %}
            <div class=\"hover:z-10 hover:overflow-visible hover:-translate-y-14 w-fit overflow-hidden\">
                {% set cardNumber = card.value %}
                {% set selectCardRoute = \"/game/\" ~ gameId ~ \"/sixqp/select/\" ~ cardNumber %}
                <button type=\"button\" {{ stimulus_action('sixqp', 'selectCard', 'click',
                    {url: path('app_game_sixqp_select', {idCard: card.id, idGame: game.id})}) }}>
                    <img class=\"z-0\"
                         src=\"{{ asset('resourcesGames/6QP/cardsImages/' ~ card.value ~ \".png\") }}\"
                         alt=\"{{ cardNumber }}\"
                    >
                </button>
            </div>
        {% endfor %}
    </div>
    <script>
        {% set path = path('app_game_show_sixqp', {'id': game.id}) ~ 'personalBoard' ~ player.id %}
        const eventSourcePersonalBoard = new EventSource(\"{{ mercure(path)|escape('js') }}\");
        eventSourcePersonalBoard.onmessage = event => {
            let personalBoard = document.getElementById('personalBoard');
            personalBoard.innerHTML = event.data;
        }
    </script>
{% endblock %}", "Game/Six_qp/personalBoard.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\Game\\Six_qp\\personalBoard.html.twig");
    }
}
