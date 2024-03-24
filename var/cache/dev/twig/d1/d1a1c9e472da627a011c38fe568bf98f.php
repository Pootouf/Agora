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

/* platform/game/description.html.twig */
class __TwigTemplate_7a021ccb0254af56b61a9a8b500b98d9 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "platform/game/description.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "platform/game/description.html.twig"));

        $this->parent = $this->loadTemplate("platform/base.html.twig", "platform/game/description.html.twig", 1);
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
        echo "\tDescription : Jeux 1
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
\t\t<div class=\"flex justify-center\">
\t\t\t<div class=\"w-full max-w-screen-xl\">
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80\">
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1597764894768-df73d7fde605?q=80&w=3870&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"/game\">
\t\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"mx-auto mt-16\">
\t\t\t\t\t<div class=\"w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700\">
\t\t\t\t\t\t<h5 class=\"mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white\">Noteworthy technology acquisitions 2021</h5>
\t\t\t\t\t</br>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t</br>
\t\t\t\t<a href=\"/game\" class=\"text-white bg-primary hover:bg-blue-400 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-semibold rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">
\t\t\t\t\tJOUER
\t\t\t\t</a>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
<div class=\"absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]\" aria-hidden=\"true\">
\t<div class=\"relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary to-danger opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
</div></header>";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "platform/game/description.html.twig";
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
        return new Source("{% extends 'platform/base.html.twig' %}

{% block title %}
\tDescription : Jeux 1
{% endblock %}

{% block body %}
\t<header class=\"relative isolate px-6 pt-14 lg:px-8\">
\t\t<div class=\"absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80\" aria-hidden=\"true\">
\t\t\t<div class=\"relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-danger to-primary opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
\t\t</div>
\t\t<div class=\"flex justify-center\">
\t\t\t<div class=\"w-full max-w-screen-xl\">
\t\t\t\t<div class=\"relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80\">
\t\t\t\t\t<img src=\"https://images.unsplash.com/photo-1597764894768-df73d7fde605?q=80&w=3870&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D\" alt=\"\" class=\"absolute inset-0 -z-10 h-full w-full object-cover\">
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40\"></div>
\t\t\t\t\t<div class=\"absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10\"></div>
\t\t\t\t\t<h3 class=\"mt-3 text-lg font-semibold leading-6 text-white\">
\t\t\t\t\t\t<a href=\"/game\">
\t\t\t\t\t\t\tLorem ipsum dolor sit amet
\t\t\t\t\t\t</a>
\t\t\t\t\t</h3>
\t\t\t\t\t<p class=\"text-sm leading-6 text-gray-300\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
\t\t\t\t</div>
\t\t\t\t<div class=\"mx-auto mt-16\">
\t\t\t\t\t<div class=\"w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700\">
\t\t\t\t\t\t<h5 class=\"mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white\">Noteworthy technology acquisitions 2021</h5>
\t\t\t\t\t</br>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t\t<p class=\"mb-3 font-normal text-gray-700 dark:text-gray-400\">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
\t\t\t\t</br>
\t\t\t\t<a href=\"/game\" class=\"text-white bg-primary hover:bg-blue-400 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-semibold rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2\">
\t\t\t\t\tJOUER
\t\t\t\t</a>
\t\t\t</div>
\t\t</div>
\t</div>
</div>
<div class=\"absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]\" aria-hidden=\"true\">
\t<div class=\"relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary to-danger opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]\" style=\"clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)\"></div>
</div></header>{% endblock %}
", "platform/game/description.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\platform\\game\\description.html.twig");
    }
}
