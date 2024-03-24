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

/* platform/home/index.html.twig */
class __TwigTemplate_63220f4ee696fa788ec9aa1c45fe3478 extends Template
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
        return "platform/base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "platform/home/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "platform/home/index.html.twig"));

        $this->parent = $this->loadTemplate("platform/base.html.twig", "platform/home/index.html.twig", 1);
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
        echo "\tAccueil
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
        echo "\t<header class=\"relative isolate px-6 pt-14 lg:px-8\">
\t\t<div class=\"absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80\" aria-hidden=\"true\">
\t\t\t<div class=\"relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-danger to-primary opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
\t\t</div>
\t\t<div class=\"mx-auto max-w-2xl py-32\">
\t\t\t<div class=\"text-center\">
\t\t\t\t<h1 class=\"text-4xl tracking-tight text-gray-900 sm:text-6xl\">Games to enrich your online experience</h1>
\t\t\t\t<p class=\"mt-6 text-lg leading-8 text-gray-600\">Anim aute id magna aliqua ad ad non deserunt sunt. Qui irure qui lorem cupidatat commodo. Elit sunt amet fugiat veniam occaecat fugiat aliqua.</p>
\t\t\t\t";
        // line 16
        if ( !twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 16, $this->source); })()), "user", [], "any", false, false, false, 16)) {
            // line 17
            echo "\t\t\t\t\t<div class=\"mt-10 flex items-center justify-center gap-x-6\">
\t\t\t\t\t\t<a href=\"/register\" class=\"rounded-md bg-primary px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600\">Create an account</a>
\t\t\t\t\t</div>
\t\t\t\t";
        }
        // line 21
        echo "\t\t\t</div>
\t\t</div>
\t\t<div class=\"absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]\" aria-hidden=\"true\">
\t\t\t<div class=\"relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary to-danger opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
\t\t</div>
\t</header>
\t<div class=\"bg-white py-24 sm:py-32\">
\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8\">
\t\t\t<div class=\"mx-auto max-w-2xl text-center relative z-50\">
\t\t\t\t<h2 class=\"text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl\">AGORA Games</h2>
\t\t\t\t<p class=\"mt-2 text-lg leading-8 text-gray-600\">Anim aute id magna aliqua ad ad non deserunt sunt</p>
\t\t\t</div>
\t\t\t<div class=\"mx-auto mt-16 grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3\">
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1585504198199-20277593b94f?q=80&w=3592&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<div class=\"transition duration-300 ease-in-out group-hover:-translate-y-1 group-hover:translate-x-3 group-hover:scale-110\">
\t\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\t\tLorem ipsum dolor sit amettt
\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t</h3>
\t\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?q=80&w=3862&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1597764894768-df73d7fde605?q=80&w=3870&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "platform/home/index.html.twig";
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
        return array (  108 => 21,  102 => 17,  100 => 16,  90 => 8,  80 => 7,  69 => 4,  59 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'platform/base.html.twig' %}

{% block title %}
\tAccueil
{% endblock %}

{% block body %}
\t<header class=\"relative isolate px-6 pt-14 lg:px-8\">
\t\t<div class=\"absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80\" aria-hidden=\"true\">
\t\t\t<div class=\"relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-danger to-primary opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
\t\t</div>
\t\t<div class=\"mx-auto max-w-2xl py-32\">
\t\t\t<div class=\"text-center\">
\t\t\t\t<h1 class=\"text-4xl tracking-tight text-gray-900 sm:text-6xl\">Games to enrich your online experience</h1>
\t\t\t\t<p class=\"mt-6 text-lg leading-8 text-gray-600\">Anim aute id magna aliqua ad ad non deserunt sunt. Qui irure qui lorem cupidatat commodo. Elit sunt amet fugiat veniam occaecat fugiat aliqua.</p>
\t\t\t\t{% if not app.user %}
\t\t\t\t\t<div class=\"mt-10 flex items-center justify-center gap-x-6\">
\t\t\t\t\t\t<a href=\"/register\" class=\"rounded-md bg-primary px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600\">Create an account</a>
\t\t\t\t\t</div>
\t\t\t\t{% endif %}
\t\t\t</div>
\t\t</div>
\t\t<div class=\"absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]\" aria-hidden=\"true\">
\t\t\t<div class=\"relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary to-danger opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
\t\t</div>
\t</header>
\t<div class=\"bg-white py-24 sm:py-32\">
\t\t<div class=\"mx-auto max-w-7xl px-6 lg:px-8\">
\t\t\t<div class=\"mx-auto max-w-2xl text-center relative z-50\">
\t\t\t\t<h2 class=\"text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl\">AGORA Games</h2>
\t\t\t\t<p class=\"mt-2 text-lg leading-8 text-gray-600\">Anim aute id magna aliqua ad ad non deserunt sunt</p>
\t\t\t</div>
\t\t\t<div class=\"mx-auto mt-16 grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3\">
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1585504198199-20277593b94f?q=80&w=3592&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<div class=\"transition duration-300 ease-in-out group-hover:-translate-y-1 group-hover:translate-x-3 group-hover:scale-110\">
\t\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\t\tLorem ipsum dolor sit amettt
\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t</h3>
\t\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?q=80&w=3862&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80 transform transition-transform duration-300 ease-in-out hover:scale-105\">
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1597764894768-df73d7fde605?q=80&w=3870&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"#\">
\t\t\t\t\t\t\t<span class=\"absolute inset-0\"></span>
\t\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>
{% endblock %}
", "platform/home/index.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\platform\\home\\index.html.twig");
    }
}
