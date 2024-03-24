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

/* game/game.html.twig */
class __TwigTemplate_2d493810588c33be76626d655d8ad69a extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "game/game.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "game/game.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "game/game.html.twig", 1);
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

        // line 4
        echo "Jeux
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    // line 7
    public function block_body($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 8
        echo "<style>
\t.grid {
\tmargin-top: -50px;
\t}
\t.table-container {
\tborder-collapse: separate;
\tborder-spacing: 0 8px;
\t}
</style>
<header class=\"relative isolate px-6 pt-14 lg:px-8\">
\t<div class=\"absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80\" aria-hidden=\"true\">
\t\t<div class=\"relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-danger to-primary opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
\t</div>
\t<div class=\"absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]\" aria-hidden=\"true\">
\t\t<div class=\"relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary to-danger opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
\t</div>
</header>
<div class=\"bg-white py-24 sm:py-32\">
<div class=\"max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 mb-20\">
<div class=\"mb-20\">
\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 mt-16\">
\t\t<h2 class=\"text-4xl font-bold text-gray-800 mb-8\">Mes tables en cours :</h2>
\t\t<div class=\"relative overflow-x-auto shadow-md sm:rounded-lg\">
\t\t\t<table class=\"w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300\">
\t\t\t\t<thead class=\"text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400\">
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th scope=\"col\" class=\"px-3 py-3 text-center\">
\t\t\t\t\t\t\tAperçu
\t\t\t\t\t\t</th>
\t\t\t\t\t\t<th scope=\"col\" class=\"px-3 py-3\">
\t\t\t\t\t\t\tInformations
\t\t\t\t\t\t</th>
\t\t\t\t\t\t<th scope=\"col\" class=\"px-20 py-3\">
\t\t\t\t\t\t\tJoueurs
\t\t\t\t\t\t</th>
\t\t\t\t\t\t<th scope=\"col\" class=\"px-3 py-3\">
\t\t\t\t\t\t\tAction
\t\t\t\t\t\t</th>
\t\t\t\t\t</tr>
\t\t\t\t</thead>
\t\t\t\t<tbody>
\t\t\t\t\t<tr class=\"bg-gradient-to-r from-transparent to-green-200 border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600\">
\t\t\t\t\t\t<td class=\"p-2 text-center\">
\t\t\t\t\t\t\t<img src=\"https://images.pexels.com/photos/8111252/pexels-photo-8111252.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1\" class=\"w-32 h-16 md:w-24 md:h-24 rounded-md mx-auto\" alt=\"Nom du Jeu 1\">
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-3 py-4 font-semibold text-gray-900 dark:text-white\">
\t\t\t\t\t\t\t<div>Nom du jeu</div>
\t\t\t\t\t\t\t<div>Nom de la table</div>
\t\t\t\t\t\t\t<div>Places disponibles : 0</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-20 py-4\">
\t\t\t\t\t\t\t<table class=\"w-full text-xs text-gray-500 dark:text-gray-400 table-container\">
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 1</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 2</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 3</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 4</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 5</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 6</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 7</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 8</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-3 py-4\">
\t\t\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre</button>
\t\t\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Quitter</button>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr class=\"bg-gradient-to-r from-transparent to-red-200 border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600\">
\t\t\t\t\t\t<td class=\"p-2 text-center\">
\t\t\t\t\t\t\t<img src=\"https://images.pexels.com/photos/541538/pexels-photo-541538.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1\" class=\"w-32 h-16 md:w-24 md:h-24 rounded-md mx-auto\" alt=\"Nom du Jeu 1\">
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-3 py-4 font-semibold text-gray-900 dark:text-white\">
\t\t\t\t\t\t\t<div>Nom du jeu</div>
\t\t\t\t\t\t\t<div>Nom de la table</div>
\t\t\t\t\t\t\t<div>Places disponibles : 0</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-20 py-4\">
\t\t\t\t\t\t\t<table class=\"w-full text-xs text-gray-500 dark:text-gray-400 table-container\">
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 1</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 2</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 3</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 4</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 5</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 6</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 7</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 8</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-3 py-4\">
\t\t\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre</button>
\t\t\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Quitter</button>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t</tbody>
\t\t\t</table>
\t\t</div>
\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 flex justify-center items-center mt-8\">
\t\t\t<button type=\"button\" class=\"text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L2 10l8 6.5V3.5z\"/>
\t\t\t\t</svg>
\t\t\t\t<span class=\"sr-only\">Page précédente</span>
\t\t\t</button>
\t\t\t<div class=\"text-gray-600 flex items-center mx-2\">1/2</div>
\t\t\t<button type=\"button\" class=\"text-white mx-1 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L18 10l-8 6.5V3.5z\"/>
\t\t\t\t</svg>
\t\t\t\t<span class=\"sr-only\">Page suivante</span>
\t\t\t</button>
\t\t</div>
\t</div>
\t<div class=\"mb-20\">
\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 mt-16\">
\t\t\t<h2 class=\"text-4xl font-bold text-gray-800 mb-20 select-none\">Mes favoris :</h2>
\t\t\t<div class=\"mx-auto mt-16 grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3\">
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
\t\t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
\t\t\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t\t\t<path fill=\"#FFD700\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
\t\t\t\t\t\t\t</svg>
\t\t\t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
\t\t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1585504198199-20277593b94f?q=80&w=3592&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<div class=\"transition duration-300 ease-in-out group-hover:-translate-y-1 group-hover:translate-x-3 group-hover:scale-110\">
\t\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\tLorem ipsum dolor sit amettt
\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t</h3>
\t\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
\t\t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
\t\t\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t\t\t<path fill=\"#FFD700\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
\t\t\t\t\t\t\t</svg>
\t\t\t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
\t\t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?q=80&w=3862&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
\t\t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
\t\t\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t\t\t<path fill=\"#FFD700\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
\t\t\t\t\t\t\t</svg>
\t\t\t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
\t\t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1597764894768-df73d7fde605?q=80&w=3870&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 flex justify-center items-center mt-8\">
\t\t\t\t<button type=\"button\" class=\"text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L2 10l8 6.5V3.5z\"/>
\t\t\t\t\t</svg>
\t\t\t\t\t<span class=\"sr-only\">Page précédente</span>
\t\t\t\t</button>
\t\t\t\t<div class=\"text-gray-600 flex items-center mx-2\">1/2</div>
\t\t\t\t<button type=\"button\" class=\"text-white mx-1 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L18 10l-8 6.5V3.5z\"/>
\t\t\t\t\t</svg>
\t\t\t\t\t<span class=\"sr-only\">Page suivante</span>
\t\t\t\t</button>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"mb-20\">
\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 mt-16\">
\t\t\t<h2 class=\"text-4xl font-bold text-gray-800 mb-20 select-none\">Récemment joués :</h2>
\t\t\t<div class=\"mx-auto mt-16 grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3\">
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
    \t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
        \t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
            \t\t\t\t\t<path fill=\"#CCCCCC\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
       \t\t\t\t\t\t</svg>
        \t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
    \t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1585504198199-20277593b94f?q=80&w=3592&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<div class=\"transition duration-300 ease-in-out group-hover:-translate-y-1 group-hover:translate-x-3 group-hover:scale-110\">
\t\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\tLorem ipsum dolor sit amettt
\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t</h3>
\t\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
    \t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
        \t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
            \t\t\t\t\t<path fill=\"#CCCCCC\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
       \t\t\t\t\t\t</svg>
        \t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
    \t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?q=80&w=3862&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
    \t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
        \t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
            \t\t\t\t\t<path fill=\"#CCCCCC\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
       \t\t\t\t\t\t</svg>
        \t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
    \t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1597764894768-df73d7fde605?q=80&w=3870&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 flex justify-center items-center mt-8\">
\t\t\t\t<button type=\"button\" class=\"text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L2 10l8 6.5V3.5z\"/>
\t\t\t\t\t</svg>
\t\t\t\t\t<span class=\"sr-only\">Page précédente</span>
\t\t\t\t</button>
\t\t\t\t<div class=\"text-gray-600 flex items-center mx-2\">1/2</div>
\t\t\t\t<button type=\"button\" class=\"text-white mx-1 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L18 10l-8 6.5V3.5z\"/>
\t\t\t\t\t</svg>
\t\t\t\t\t<span class=\"sr-only\">Page suivante</span>
\t\t\t\t</button>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
<div class=\"bg-white py-24 sm:py-32\"></div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "game/game.html.twig";
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
        return array (  90 => 8,  80 => 7,  69 => 4,  59 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}
Jeux
{% endblock %}

{% block body %}
<style>
\t.grid {
\tmargin-top: -50px;
\t}
\t.table-container {
\tborder-collapse: separate;
\tborder-spacing: 0 8px;
\t}
</style>
<header class=\"relative isolate px-6 pt-14 lg:px-8\">
\t<div class=\"absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80\" aria-hidden=\"true\">
\t\t<div class=\"relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-danger to-primary opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
\t</div>
\t<div class=\"absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]\" aria-hidden=\"true\">
\t\t<div class=\"relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary to-danger opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
\t</div>
</header>
<div class=\"bg-white py-24 sm:py-32\">
<div class=\"max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 mb-20\">
<div class=\"mb-20\">
\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 mt-16\">
\t\t<h2 class=\"text-4xl font-bold text-gray-800 mb-8\">Mes tables en cours :</h2>
\t\t<div class=\"relative overflow-x-auto shadow-md sm:rounded-lg\">
\t\t\t<table class=\"w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300\">
\t\t\t\t<thead class=\"text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400\">
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th scope=\"col\" class=\"px-3 py-3 text-center\">
\t\t\t\t\t\t\tAperçu
\t\t\t\t\t\t</th>
\t\t\t\t\t\t<th scope=\"col\" class=\"px-3 py-3\">
\t\t\t\t\t\t\tInformations
\t\t\t\t\t\t</th>
\t\t\t\t\t\t<th scope=\"col\" class=\"px-20 py-3\">
\t\t\t\t\t\t\tJoueurs
\t\t\t\t\t\t</th>
\t\t\t\t\t\t<th scope=\"col\" class=\"px-3 py-3\">
\t\t\t\t\t\t\tAction
\t\t\t\t\t\t</th>
\t\t\t\t\t</tr>
\t\t\t\t</thead>
\t\t\t\t<tbody>
\t\t\t\t\t<tr class=\"bg-gradient-to-r from-transparent to-green-200 border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600\">
\t\t\t\t\t\t<td class=\"p-2 text-center\">
\t\t\t\t\t\t\t<img src=\"https://images.pexels.com/photos/8111252/pexels-photo-8111252.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1\" class=\"w-32 h-16 md:w-24 md:h-24 rounded-md mx-auto\" alt=\"Nom du Jeu 1\">
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-3 py-4 font-semibold text-gray-900 dark:text-white\">
\t\t\t\t\t\t\t<div>Nom du jeu</div>
\t\t\t\t\t\t\t<div>Nom de la table</div>
\t\t\t\t\t\t\t<div>Places disponibles : 0</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-20 py-4\">
\t\t\t\t\t\t\t<table class=\"w-full text-xs text-gray-500 dark:text-gray-400 table-container\">
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 1</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 2</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 3</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 4</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 5</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 6</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 7</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 8</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-3 py-4\">
\t\t\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre</button>
\t\t\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Quitter</button>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr class=\"bg-gradient-to-r from-transparent to-red-200 border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600\">
\t\t\t\t\t\t<td class=\"p-2 text-center\">
\t\t\t\t\t\t\t<img src=\"https://images.pexels.com/photos/541538/pexels-photo-541538.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1\" class=\"w-32 h-16 md:w-24 md:h-24 rounded-md mx-auto\" alt=\"Nom du Jeu 1\">
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-3 py-4 font-semibold text-gray-900 dark:text-white\">
\t\t\t\t\t\t\t<div>Nom du jeu</div>
\t\t\t\t\t\t\t<div>Nom de la table</div>
\t\t\t\t\t\t\t<div>Places disponibles : 0</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-20 py-4\">
\t\t\t\t\t\t\t<table class=\"w-full text-xs text-gray-500 dark:text-gray-400 table-container\">
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 1</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 2</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 3</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 4</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 5</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 6</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 7</td>
\t\t\t\t\t\t\t\t\t<td class=\"h-8 w-8 rounded-full bg-gray-300 mb-1\"></td>
\t\t\t\t\t\t\t\t\t<td class=\"text-xs pl-2\">Joueur 8</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-3 py-4\">
\t\t\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre</button>
\t\t\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Quitter</button>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t</tbody>
\t\t\t</table>
\t\t</div>
\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 flex justify-center items-center mt-8\">
\t\t\t<button type=\"button\" class=\"text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L2 10l8 6.5V3.5z\"/>
\t\t\t\t</svg>
\t\t\t\t<span class=\"sr-only\">Page précédente</span>
\t\t\t</button>
\t\t\t<div class=\"text-gray-600 flex items-center mx-2\">1/2</div>
\t\t\t<button type=\"button\" class=\"text-white mx-1 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L18 10l-8 6.5V3.5z\"/>
\t\t\t\t</svg>
\t\t\t\t<span class=\"sr-only\">Page suivante</span>
\t\t\t</button>
\t\t</div>
\t</div>
\t<div class=\"mb-20\">
\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 mt-16\">
\t\t\t<h2 class=\"text-4xl font-bold text-gray-800 mb-20 select-none\">Mes favoris :</h2>
\t\t\t<div class=\"mx-auto mt-16 grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3\">
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
\t\t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
\t\t\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t\t\t<path fill=\"#FFD700\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
\t\t\t\t\t\t\t</svg>
\t\t\t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
\t\t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1585504198199-20277593b94f?q=80&w=3592&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<div class=\"transition duration-300 ease-in-out group-hover:-translate-y-1 group-hover:translate-x-3 group-hover:scale-110\">
\t\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\tLorem ipsum dolor sit amettt
\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t</h3>
\t\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
\t\t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
\t\t\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t\t\t<path fill=\"#FFD700\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
\t\t\t\t\t\t\t</svg>
\t\t\t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
\t\t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?q=80&w=3862&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
\t\t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
\t\t\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t\t\t<path fill=\"#FFD700\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
\t\t\t\t\t\t\t</svg>
\t\t\t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
\t\t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1597764894768-df73d7fde605?q=80&w=3870&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 flex justify-center items-center mt-8\">
\t\t\t\t<button type=\"button\" class=\"text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L2 10l8 6.5V3.5z\"/>
\t\t\t\t\t</svg>
\t\t\t\t\t<span class=\"sr-only\">Page précédente</span>
\t\t\t\t</button>
\t\t\t\t<div class=\"text-gray-600 flex items-center mx-2\">1/2</div>
\t\t\t\t<button type=\"button\" class=\"text-white mx-1 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L18 10l-8 6.5V3.5z\"/>
\t\t\t\t\t</svg>
\t\t\t\t\t<span class=\"sr-only\">Page suivante</span>
\t\t\t\t</button>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"mb-20\">
\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 mt-16\">
\t\t\t<h2 class=\"text-4xl font-bold text-gray-800 mb-20 select-none\">Récemment joués :</h2>
\t\t\t<div class=\"mx-auto mt-16 grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3\">
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
    \t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
        \t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
            \t\t\t\t\t<path fill=\"#CCCCCC\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
       \t\t\t\t\t\t</svg>
        \t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
    \t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1585504198199-20277593b94f?q=80&w=3592&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<div class=\"transition duration-300 ease-in-out group-hover:-translate-y-1 group-hover:translate-x-3 group-hover:scale-110\">
\t\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\tLorem ipsum dolor sit amettt
\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t</h3>
\t\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
    \t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
        \t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
            \t\t\t\t\t<path fill=\"#CCCCCC\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
       \t\t\t\t\t\t</svg>
        \t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
    \t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?q=80&w=3862&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<div class=\"absolute top-4 right-4\">
    \t\t\t\t\t<button type=\"button\" class=\"bg-white text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500\">
        \t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
            \t\t\t\t\t<path fill=\"#CCCCCC\" d=\"M10 0l2.42 6.16 6.34.05c1.48.01 2.16 2.02.92 3.11l-5.11 4.67 1.52 6.35c.34 1.44-1.48 2.56-2.86 1.82L10 16.38l-6.3 3.54c-1.38.74-3.2-.38-2.85-1.82l1.52-6.36L.32 9.22C-1.02 8.12.16 6 1.64 6h6.34L10 0z\"/>
       \t\t\t\t\t\t</svg>
        \t\t\t\t\t<span class=\"sr-only\">Jeux favoris</span>
    \t\t\t\t\t</button>
\t\t\t\t\t</div>
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1597764894768-df73d7fde605?q=80&w=3870&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Créer une table</button>
\t\t\t\t\t<button type=\"button\" class=\"text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">Rejoindre une table</button>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8 flex justify-center items-center mt-8\">
\t\t\t\t<button type=\"button\" class=\"text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L2 10l8 6.5V3.5z\"/>
\t\t\t\t\t</svg>
\t\t\t\t\t<span class=\"sr-only\">Page précédente</span>
\t\t\t\t</button>
\t\t\t\t<div class=\"text-gray-600 flex items-center mx-2\">1/2</div>
\t\t\t\t<button type=\"button\" class=\"text-white mx-1 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-1 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800\">
\t\t\t\t\t<svg class=\"w-4 h-4\" aria-hidden=\"true\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 20 20\">
\t\t\t\t\t\t<path fill=\"currentColor\" d=\"M10 3.5L18 10l-8 6.5V3.5z\"/>
\t\t\t\t\t</svg>
\t\t\t\t\t<span class=\"sr-only\">Page suivante</span>
\t\t\t\t</button>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
<div class=\"bg-white py-24 sm:py-32\"></div>
{% endblock %}", "game/game.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\game\\game.html.twig");
    }
}
