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

/* Game/Six_qp/ranking.html.twig */
class __TwigTemplate_0fdd349231a27003832bb917d06a049a extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'ranking' => [$this, 'block_ranking'],
            'javascripts' => [$this, 'block_javascripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/ranking.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "Game/Six_qp/ranking.html.twig"));

        // line 10
        echo "
";
        // line 11
        $this->displayBlock('ranking', $context, $blocks);
        // line 80
        echo "
";
        // line 81
        $this->displayBlock('javascripts', $context, $blocks);
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 11
    public function block_ranking($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ranking"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ranking"));

        // line 12
        echo "\t<div>
\t\t<img src=\"";
        // line 13
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("resourcesGames/6QP/logo.png"), "html", null, true);
        echo "\" alt=\"logo\" class=\"rounded-full mr-2\">
\t\t<p id=\"elapsedTime\" class=\"mr-1 text-center md:text-base portrait:text-xl md:text-xl\">
\t\t\t00:00:00
\t\t</p>
\t\t";
        // line 22
        echo "    </div>
\t<p class=\"text-center py-2 font-bold dark:bg-gray-700 text-white text-xl border-y-4 border-violet-600\">
\t\tClassement
\t</p>
\t<div class=\"dark:bg-slate-400 overflow-y-auto\">
\t\t<div id=\"leaderboard\" class=\"text-sm font-medium space-y-1 text-white\">
\t\t\t";
        // line 28
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["ranking"]) || array_key_exists("ranking", $context) ? $context["ranking"] : (function () { throw new RuntimeError('Variable "ranking" does not exist.', 28, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 29
            echo "\t\t\t\t<div id=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 29), "html", null, true);
            echo "\" class=\"score-white\">
\t\t\t\t\t<div id=\"l_";
            // line 30
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 30), "html", null, true);
            echo "_points\" class=\"portrait:hidden flex w-full items-center overflow-hidden\"
\t\t\t\t\t     data-score=\"";
            // line 31
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["user"], "discardSIxQP", [], "any", false, false, false, 31), "totalPoints", [], "any", false, false, false, 31), "html", null, true);
            echo "\">
\t\t\t\t\t\t<img src=\"";
            // line 32
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("resourcesGames/6QP/teteDeBoeuf.svg"), "html", null, true);
            echo "\" alt=\"logo\"
\t\t\t\t\t\t     class=\"lg:size-8 md:size-4 mr-1\">
\t\t\t\t\t\t<p class=\"mr-1 lg:text-xl font-bold\">
\t\t\t\t\t\t\t";
            // line 35
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["user"], "discardSIxQP", [], "any", false, false, false, 35), "totalPoints", [], "any", false, false, false, 35), "html", null, true);
            echo "
\t\t\t\t\t\t</p>
\t\t\t\t\t\t<p class=\"mr-2 text-sm font-thin\">
\t\t\t\t\t\t\t";
            // line 38
            echo (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["user"], "discardSIxQP", [], "any", false, false, false, 38), "totalPoints", [], "any", false, false, false, 38) > 1)) ? ("points") : ("point"));
            echo "
\t\t\t\t\t\t</p>
\t\t\t\t\t\t";
            // line 40
            if (((isset($context["player"]) || array_key_exists("player", $context) ? $context["player"] : (function () { throw new RuntimeError('Variable "player" does not exist.', 40, $this->source); })()) == twig_get_attribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 40))) {
                // line 41
                echo "\t\t\t\t\t\t\t<p class=\"flex-grow text-clip text-right sm:text-xs text-violet-300\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 41), "html", null, true);
                echo "</p>
\t\t\t\t\t\t";
            } else {
                // line 43
                echo "\t\t\t\t\t\t\t<p class=\"flex-grow text-clip text-right sm:text-xs\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 43), "html", null, true);
                echo "</p>
\t\t\t\t\t\t";
            }
            // line 45
            echo "\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"landscape:hidden w-full items-center overflow-hidden\">
\t\t\t\t\t\t";
            // line 48
            if (((isset($context["player"]) || array_key_exists("player", $context) ? $context["player"] : (function () { throw new RuntimeError('Variable "player" does not exist.', 48, $this->source); })()) == twig_get_attribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 48))) {
                // line 49
                echo "\t\t\t\t\t\t\t<p class=\" flex items-center text-clip text-right mb-2 text-xl text-violet-300\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 49), "html", null, true);
                echo "</p>
\t\t\t\t\t\t";
            } else {
                // line 51
                echo "\t\t\t\t\t\t\t<p class=\"flex items-center text-clip text-right mb-2 text-xl\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 51), "html", null, true);
                echo "</p>
\t\t\t\t\t\t";
            }
            // line 53
            echo "
\t\t\t\t\t\t<div id=\"p_";
            // line 54
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "username", [], "any", false, false, false, 54), "html", null, true);
            echo "_points\" class=\"flex\">
\t\t\t\t\t\t\t<img src=\"";
            // line 55
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("resourcesGames/6QP/teteDeBoeuf.svg"), "html", null, true);
            echo "\" alt=\"logo\"
\t\t\t\t\t\t\t     class=\"size-8  mr-1\">
\t\t\t\t\t\t\t<p class=\"mr-1 text-xl font-bold\">
\t\t\t\t\t\t\t\t";
            // line 58
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["user"], "discardSIxQP", [], "any", false, false, false, 58), "totalPoints", [], "any", false, false, false, 58), "html", null, true);
            echo "
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t<p class=\"mr-2 font-thin text-sm\">
\t\t\t\t\t\t\t\t";
            // line 61
            echo (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["user"], "discardSIxQP", [], "any", false, false, false, 61), "totalPoints", [], "any", false, false, false, 61) > 1)) ? ("points") : ("point"));
            echo "
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['user'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 67
        echo "\t\t</div>
\t</div>

\t<script>
\t\t";
        // line 71
        $context["path"] = ($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_game_show_sixqp", ["id" => twig_get_attribute($this->env, $this->source, (isset($context["game"]) || array_key_exists("game", $context) ? $context["game"] : (function () { throw new RuntimeError('Variable "game" does not exist.', 71, $this->source); })()), "id", [], "any", false, false, false, 71)]) . "ranking");
        // line 72
        echo "        const eventSourceRanking = new EventSource(\"";
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, $this->extensions['Symfony\Component\Mercure\Twig\MercureExtension']->mercure((isset($context["path"]) || array_key_exists("path", $context) ? $context["path"] : (function () { throw new RuntimeError('Variable "path" does not exist.', 72, $this->source); })())), "js"), "html", null, true);
        echo "\");
        eventSourceRanking.onmessage = event => {
            let ranking = document.getElementById('ranking');
            ranking.innerHTML = event.data;
        }
\t</script>

";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 81
    public function block_javascripts($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        // line 82
        echo "    <script>
        function updateElapsedTime() {
            const currentTime = Math.floor(Date.now() / 1000);
            const elapsedTime = currentTime - ";
        // line 85
        echo twig_escape_filter($this->env, (isset($context["createdAt"]) || array_key_exists("createdAt", $context) ? $context["createdAt"] : (function () { throw new RuntimeError('Variable "createdAt" does not exist.', 85, $this->source); })()), "html", null, true);
        echo ";

            const hours = Math.floor(elapsedTime / 3600);
            const minutes = Math.floor((elapsedTime % 3600) / 60);
            const seconds = elapsedTime % 60;

\t        document.getElementById('elapsedTime').innerText = hours.toString().padStart(2, '0') + ':' +
\t            minutes.toString().padStart(2, '0') + ':' +
\t            seconds.toString().padStart(2, '0');
\t    }

\t    const clockID = setInterval(updateElapsedTime, 1000);

\t    function updateUserScore(player) {
\t        let scoreElement = document.getElementById(player.username);
\t        if (scoreElement) {
\t            let landscapeScore = document.getElementById('l_' + player.username + '_points');
\t            landscapeScore.dataset.score = player.discardSIxQP.totalPoints;
\t            if (landscapeScore) {
\t                landscapeScore.getElementsByTagName('p').item(0).innerText = player.discardSIxQP.totalPoints;
\t                if (player.discardSIxQP.totalPoints > 1) {
\t                    landscapeScore.getElementsByTagName('p').item(1).innerText = \"points\";
\t                }
\t            }


\t            let portraitScore = document.getElementById('p_' + player.username + '_points');
\t            if (portraitScore) {
\t                portraitScore.getElementsByTagName('p').item(0).innerText = player.discardSIxQP.totalPoints;
\t                if (player.discardSIxQP.totalPoints > 1) {
\t                    portraitScore.getElementsByTagName('p').item(1).innerText = 'points';
\t                }
\t            }
\t        }
\t        updateLeaderboard();
\t    }

\t    function updateLeaderboard() {
\t        let leaderboardContainer = document.getElementById('leaderboard');
\t        if (leaderboardContainer) {
\t            let leaderboardItems = Array.from(leaderboardContainer.children);

\t            leaderboardItems.sort(function (a, b) {
\t                let scoreA = parseInt(document.getElementById('l_' + a.id + '_points').dataset.score);
\t                let scoreB = parseInt(document.getElementById('l_' + b.id + '_points').dataset.score);
\t                return scoreA - scoreB;
\t            });

\t            let first = document.getElementById(
\t                'l_' + leaderboardItems[0].id + '_points').dataset.score;
\t            let last = document.getElementById('l_' +
\t                leaderboardItems[leaderboardItems.length - 1].id + '_points').dataset.score;

\t            leaderboardItems.forEach((item) => {
\t                if (document.getElementById('l_' + item.id + '_points').dataset.score === last) {
\t                    item.classList.remove('score-white', 'score-gold');
\t                    item.classList.add('score-red');
\t                } else if (document.getElementById('l_' + item.id + '_points').dataset.score === first) {
\t                    item.classList.remove('score-red', 'score-white');
\t                    item.classList.add('score-gold');
\t                } else {
\t                    item.classList.remove('score-red', 'score-gold');
\t                    item.classList.add('score-white');
\t                }
\t            });

\t            leaderboardItems.forEach(item => leaderboardContainer.removeChild(item));
\t            leaderboardItems.forEach(item => leaderboardContainer.appendChild(item));
\t        }
\t    }

\t    updateLeaderboard();
\t</script>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "Game/Six_qp/ranking.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  231 => 85,  226 => 82,  216 => 81,  197 => 72,  195 => 71,  189 => 67,  177 => 61,  171 => 58,  165 => 55,  161 => 54,  158 => 53,  152 => 51,  146 => 49,  144 => 48,  139 => 45,  133 => 43,  127 => 41,  125 => 40,  120 => 38,  114 => 35,  108 => 32,  104 => 31,  100 => 30,  95 => 29,  91 => 28,  83 => 22,  76 => 13,  73 => 12,  63 => 11,  53 => 81,  50 => 80,  48 => 11,  45 => 10,);
    }

    public function getSourceContext()
    {
        return new Source("{#
This section displays the game's name, and the current game's timer and leaderboard

@param :    ranking       -> Player entities who play in this game
\t\t    player -> the Player entity of the session's user
\t\t    createdAt   -> The number of seconds since the Unix Epoch when the game started

@return :   display game's informations and leaderboard
#}

{% block ranking %}
\t<div>
\t\t<img src=\"{{ asset('resourcesGames/6QP/logo.png') }}\" alt=\"logo\" class=\"rounded-full mr-2\">
\t\t<p id=\"elapsedTime\" class=\"mr-1 text-center md:text-base portrait:text-xl md:text-xl\">
\t\t\t00:00:00
\t\t</p>
\t\t{#
\t    <p class=\"mr-1 text-center \">{{ playersNumber }} joueurs</p>
\t    <p class=\"mr-1 text-center \">Créée le {{ createdAt|date('d/m/Y H:i') }}</p>
\t    <p id=\"elapsedTime\" class=\"mr-1 text-center \">Temps écoulé : 00:00:00</p>
\t    #}
    </div>
\t<p class=\"text-center py-2 font-bold dark:bg-gray-700 text-white text-xl border-y-4 border-violet-600\">
\t\tClassement
\t</p>
\t<div class=\"dark:bg-slate-400 overflow-y-auto\">
\t\t<div id=\"leaderboard\" class=\"text-sm font-medium space-y-1 text-white\">
\t\t\t{% for user in ranking %}
\t\t\t\t<div id=\"{{ user.username }}\" class=\"score-white\">
\t\t\t\t\t<div id=\"l_{{ user.username }}_points\" class=\"portrait:hidden flex w-full items-center overflow-hidden\"
\t\t\t\t\t     data-score=\"{{ user.discardSIxQP.totalPoints }}\">
\t\t\t\t\t\t<img src=\"{{ asset('resourcesGames/6QP/teteDeBoeuf.svg') }}\" alt=\"logo\"
\t\t\t\t\t\t     class=\"lg:size-8 md:size-4 mr-1\">
\t\t\t\t\t\t<p class=\"mr-1 lg:text-xl font-bold\">
\t\t\t\t\t\t\t{{ user.discardSIxQP.totalPoints }}
\t\t\t\t\t\t</p>
\t\t\t\t\t\t<p class=\"mr-2 text-sm font-thin\">
\t\t\t\t\t\t\t{{ user.discardSIxQP.totalPoints > 1 ? 'points' : 'point' }}
\t\t\t\t\t\t</p>
\t\t\t\t\t\t{% if player == user.username %}
\t\t\t\t\t\t\t<p class=\"flex-grow text-clip text-right sm:text-xs text-violet-300\">{{ user.username }}</p>
\t\t\t\t\t\t{% else %}
\t\t\t\t\t\t\t<p class=\"flex-grow text-clip text-right sm:text-xs\">{{ user.username }}</p>
\t\t\t\t\t\t{% endif %}
\t\t\t\t\t</div>

\t\t\t\t\t<div class=\"landscape:hidden w-full items-center overflow-hidden\">
\t\t\t\t\t\t{% if player == user.username %}
\t\t\t\t\t\t\t<p class=\" flex items-center text-clip text-right mb-2 text-xl text-violet-300\">{{ user.username }}</p>
\t\t\t\t\t\t{% else %}
\t\t\t\t\t\t\t<p class=\"flex items-center text-clip text-right mb-2 text-xl\">{{ user.username }}</p>
\t\t\t\t\t\t{% endif %}

\t\t\t\t\t\t<div id=\"p_{{ user.username }}_points\" class=\"flex\">
\t\t\t\t\t\t\t<img src=\"{{ asset('resourcesGames/6QP/teteDeBoeuf.svg') }}\" alt=\"logo\"
\t\t\t\t\t\t\t     class=\"size-8  mr-1\">
\t\t\t\t\t\t\t<p class=\"mr-1 text-xl font-bold\">
\t\t\t\t\t\t\t\t{{ user.discardSIxQP.totalPoints }}
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t\t<p class=\"mr-2 font-thin text-sm\">
\t\t\t\t\t\t\t\t{{ user.discardSIxQP.totalPoints > 1 ? 'points' : 'point' }}
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t{% endfor %}
\t\t</div>
\t</div>

\t<script>
\t\t{% set path = path('app_game_show_sixqp', {'id': game.id}) ~ 'ranking' %}
        const eventSourceRanking = new EventSource(\"{{ mercure(path)|escape('js') }}\");
        eventSourceRanking.onmessage = event => {
            let ranking = document.getElementById('ranking');
            ranking.innerHTML = event.data;
        }
\t</script>

{% endblock %}

{% block javascripts %}
    <script>
        function updateElapsedTime() {
            const currentTime = Math.floor(Date.now() / 1000);
            const elapsedTime = currentTime - {{ createdAt }};

            const hours = Math.floor(elapsedTime / 3600);
            const minutes = Math.floor((elapsedTime % 3600) / 60);
            const seconds = elapsedTime % 60;

\t        document.getElementById('elapsedTime').innerText = hours.toString().padStart(2, '0') + ':' +
\t            minutes.toString().padStart(2, '0') + ':' +
\t            seconds.toString().padStart(2, '0');
\t    }

\t    const clockID = setInterval(updateElapsedTime, 1000);

\t    function updateUserScore(player) {
\t        let scoreElement = document.getElementById(player.username);
\t        if (scoreElement) {
\t            let landscapeScore = document.getElementById('l_' + player.username + '_points');
\t            landscapeScore.dataset.score = player.discardSIxQP.totalPoints;
\t            if (landscapeScore) {
\t                landscapeScore.getElementsByTagName('p').item(0).innerText = player.discardSIxQP.totalPoints;
\t                if (player.discardSIxQP.totalPoints > 1) {
\t                    landscapeScore.getElementsByTagName('p').item(1).innerText = \"points\";
\t                }
\t            }


\t            let portraitScore = document.getElementById('p_' + player.username + '_points');
\t            if (portraitScore) {
\t                portraitScore.getElementsByTagName('p').item(0).innerText = player.discardSIxQP.totalPoints;
\t                if (player.discardSIxQP.totalPoints > 1) {
\t                    portraitScore.getElementsByTagName('p').item(1).innerText = 'points';
\t                }
\t            }
\t        }
\t        updateLeaderboard();
\t    }

\t    function updateLeaderboard() {
\t        let leaderboardContainer = document.getElementById('leaderboard');
\t        if (leaderboardContainer) {
\t            let leaderboardItems = Array.from(leaderboardContainer.children);

\t            leaderboardItems.sort(function (a, b) {
\t                let scoreA = parseInt(document.getElementById('l_' + a.id + '_points').dataset.score);
\t                let scoreB = parseInt(document.getElementById('l_' + b.id + '_points').dataset.score);
\t                return scoreA - scoreB;
\t            });

\t            let first = document.getElementById(
\t                'l_' + leaderboardItems[0].id + '_points').dataset.score;
\t            let last = document.getElementById('l_' +
\t                leaderboardItems[leaderboardItems.length - 1].id + '_points').dataset.score;

\t            leaderboardItems.forEach((item) => {
\t                if (document.getElementById('l_' + item.id + '_points').dataset.score === last) {
\t                    item.classList.remove('score-white', 'score-gold');
\t                    item.classList.add('score-red');
\t                } else if (document.getElementById('l_' + item.id + '_points').dataset.score === first) {
\t                    item.classList.remove('score-red', 'score-white');
\t                    item.classList.add('score-gold');
\t                } else {
\t                    item.classList.remove('score-red', 'score-gold');
\t                    item.classList.add('score-white');
\t                }
\t            });

\t            leaderboardItems.forEach(item => leaderboardContainer.removeChild(item));
\t            leaderboardItems.forEach(item => leaderboardContainer.appendChild(item));
\t        }
\t    }

\t    updateLeaderboard();
\t</script>
{% endblock %}
", "Game/Six_qp/ranking.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\Game\\Six_qp\\ranking.html.twig");
    }
}
