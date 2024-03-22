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

/* Game/Six_qp/endGameScreenResult.html.twig */
class __TwigTemplate_a0f4001c4b759c83aefc31a5bc509522 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/endGameScreenResult.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/endGameScreenResult.html.twig"));

        // line 10
        echo "
<div id=\"endGameScreen\"
     class=\"absolute top-0 left-0 w-full h-full bg-black bg-opacity-70 flex z-50 hidden text-white items-center justify-center\">
\t<div class=\"bg-slate-900 p-5 rounded-lg flex flex-col justify-center items-center\">
\t\t<p class=\"text-2xl\">PARTIE TERMINEE</p>
\t\t<p id=\"winner\" class=\"text-4xl font-bold mb-4\"></p>
\t\t<div class=\"font-bold\">
\t\t\t<a href=\"";
        // line 17
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_home");
        echo "\" class=\"rounded-l-lg bg-amber-300 text-black py-2 px-3 inline-flex portrait:text-2xl items-center\">
\t\t\t\t<svg class=\"me-2 portrait:size-10 landscape:size-5\" xmlns=\"http://www.w3.org/2000/svg\"
\t\t\t\t     viewBox=\"0 0 24 24\">
\t\t\t\t\t<path d=\"M11 21h8v-2l1-1v4h-9v2l-10-3v-18l10-3v2h9v5l-1-1v-3h-8v18zm10.053-9l-3.293-3.293.707-.707
                    4.5 4.5-4.5 4.5-.707-.707 3.293-3.293h-9.053v-1h9.053z\"/>
\t\t\t\t</svg>
\t\t\t\tRetour à l'accueil
\t\t\t</a>
\t\t\t<a href=\"";
        // line 25
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_game_sixqp_list");
        echo "\" class=\"flex rounded-r-lg bg-violet-600 py-2 px-3 inline-flex portrait:text-2xl items-center\">
\t\t\t\t<svg class=\"me-2 portrait:size-10 landscape:size-5\" xmlns=\"http://www.w3.org/2000/svg\"
\t\t\t\t     viewBox=\"0 0 24 24\">
\t\t\t\t\t<path fill=\"currentColor\" d=\"M13.5 2c-5.621 0-10.211 4.443-10.475 10h-3.025l5 6.625
                    5-6.625h-2.975c.257-3.351 3.06-6 6.475-6 3.584 0 6.5 2.916 6.5 6.5s-2.916 6.5-6.5 6.5c-1.863
                    0-3.542-.793-4.728-2.053l-2.427 3.216c1.877 1.754 4.389 2.837 7.155 2.837 5.79 0 10.5-4.71
                    10.5-10.5s-4.71-10.5-10.5-10.5z\"/>
\t\t\t\t</svg>
\t\t\t\tRelancer une partie
\t\t\t</a>
\t\t</div>
\t</div>
</div>

<script>
    function gameFinished(winner) {
        clearInterval(clockID);
        document.getElementById('endGameScreen').classList.remove('hidden');
        if (!winner) {
            document.getElementById('winner').innerHTML = 'Match nul ! 🤝';
        }
        else if (winner.username === '";
        // line 46
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["player"]) || array_key_exists("player", $context) ? $context["player"] : (function () { throw new RuntimeError('Variable "player" does not exist.', 46, $this->source); })()), "username", [], "any", false, false, false, 46), "html", null, true);
        echo "') {
            document.getElementById('winner').innerHTML = 'Bravo ! 👏 Vous avez gagné la partie';
        } else {
            document.getElementById('winner').innerHTML = 'Dommage ! 😖 ' + winner + ' a gagné la partie';
        }
        document.body.style.overflow = 'hidden';
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
        return "Game/Six_qp/endGameScreenResult.html.twig";
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
        return array (  87 => 46,  63 => 25,  52 => 17,  43 => 10,);
    }

    public function getSourceContext()
    {
        return new Source("{#
This section displays if the current user is the winner and if not, the name of the winner is displayed.
Comes with a button to the homepage and another one to the game's homepage

@param :    winner      -> the Player entity who winned the game
\t\t    currentUser -> the Player entity of the session's user

@return :   display the winner and a button to the game and platform's homepage
#}

<div id=\"endGameScreen\"
     class=\"absolute top-0 left-0 w-full h-full bg-black bg-opacity-70 flex z-50 hidden text-white items-center justify-center\">
\t<div class=\"bg-slate-900 p-5 rounded-lg flex flex-col justify-center items-center\">
\t\t<p class=\"text-2xl\">PARTIE TERMINEE</p>
\t\t<p id=\"winner\" class=\"text-4xl font-bold mb-4\"></p>
\t\t<div class=\"font-bold\">
\t\t\t<a href=\"{{ path('app_home') }}\" class=\"rounded-l-lg bg-amber-300 text-black py-2 px-3 inline-flex portrait:text-2xl items-center\">
\t\t\t\t<svg class=\"me-2 portrait:size-10 landscape:size-5\" xmlns=\"http://www.w3.org/2000/svg\"
\t\t\t\t     viewBox=\"0 0 24 24\">
\t\t\t\t\t<path d=\"M11 21h8v-2l1-1v4h-9v2l-10-3v-18l10-3v2h9v5l-1-1v-3h-8v18zm10.053-9l-3.293-3.293.707-.707
                    4.5 4.5-4.5 4.5-.707-.707 3.293-3.293h-9.053v-1h9.053z\"/>
\t\t\t\t</svg>
\t\t\t\tRetour à l'accueil
\t\t\t</a>
\t\t\t<a href=\"{{ path('app_game_sixqp_list')  }}\" class=\"flex rounded-r-lg bg-violet-600 py-2 px-3 inline-flex portrait:text-2xl items-center\">
\t\t\t\t<svg class=\"me-2 portrait:size-10 landscape:size-5\" xmlns=\"http://www.w3.org/2000/svg\"
\t\t\t\t     viewBox=\"0 0 24 24\">
\t\t\t\t\t<path fill=\"currentColor\" d=\"M13.5 2c-5.621 0-10.211 4.443-10.475 10h-3.025l5 6.625
                    5-6.625h-2.975c.257-3.351 3.06-6 6.475-6 3.584 0 6.5 2.916 6.5 6.5s-2.916 6.5-6.5 6.5c-1.863
                    0-3.542-.793-4.728-2.053l-2.427 3.216c1.877 1.754 4.389 2.837 7.155 2.837 5.79 0 10.5-4.71
                    10.5-10.5s-4.71-10.5-10.5-10.5z\"/>
\t\t\t\t</svg>
\t\t\t\tRelancer une partie
\t\t\t</a>
\t\t</div>
\t</div>
</div>

<script>
    function gameFinished(winner) {
        clearInterval(clockID);
        document.getElementById('endGameScreen').classList.remove('hidden');
        if (!winner) {
            document.getElementById('winner').innerHTML = 'Match nul ! 🤝';
        }
        else if (winner.username === '{{ player.username }}') {
            document.getElementById('winner').innerHTML = 'Bravo ! 👏 Vous avez gagné la partie';
        } else {
            document.getElementById('winner').innerHTML = 'Dommage ! 😖 ' + winner + ' a gagné la partie';
        }
        document.body.style.overflow = 'hidden';
    }
</script>", "Game/Six_qp/endGameScreenResult.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\Game\\Six_qp\\endGameScreenResult.html.twig");
    }
}
