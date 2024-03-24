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

/* platform/security/registration.html.twig */
class __TwigTemplate_ae16ff4bd187b37026ada27c25e16561 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "platform/security/registration.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "platform/security/registration.html.twig"));

        $this->parent = $this->loadTemplate("platform/base.html.twig", "platform/security/registration.html.twig", 1);
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
        echo "\tInscription
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
        echo "\t<div class=\"w-full max-w-xs mx-auto\">
\t\t<form class=\"space-y-4 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-16\">
\t\t\t<div>
\t\t\t\t<label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"username\">
\t\t\t\t\tNom de l'utilisateur
\t\t\t\t</label>
\t\t\t\t<input class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline\" id=\"username\" type=\"text\" placeholder=\"Nom d'utilisateur\">
\t\t\t</div>
\t\t\t<div>
\t\t\t\t<label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"mail\">
\t\t\t\t\temail
\t\t\t\t</label>
\t\t\t\t<input class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline\" id=\"mail\" type=\"email\" placeholder=\"exemple@exemple.com\">
\t\t\t</div>
\t\t\t<div>
\t\t\t\t<label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"password\">
\t\t\t\t\tMots de passe
\t\t\t\t</label>
\t\t\t\t<input class=\"shadow appearance-none border rounded w-full py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline\" id=\"password\" type=\"password\" placeholder=\"******************\">
\t\t\t</div>
\t\t\t<div>
\t\t\t\t<label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"confirmPassword\">
\t\t\t\t\tConfirmer Mots de passe
\t\t\t\t</label>
\t\t\t\t<input class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline\" id=\"confirmPassword\" type=\"password\" placeholder=\"******************\">
\t\t\t\t<p class=\"text-sm\">Vous avez déjà un compte ?<br/>
\t\t\t\t\t<a class=\"underline text-primary\" href=\"#\">Connectez-vous</a>
\t\t\t\t</p>
\t\t\t</div>
\t\t\t<button class=\"bg-primary hover:bg-blue-400 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline\" type=\"button\">
\t\t\t\tInscription
\t\t\t</button>
\t\t</form>
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
        return "platform/security/registration.html.twig";
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
\tInscription
{% endblock %}

{% block body %}
\t<div class=\"w-full max-w-xs mx-auto\">
\t\t<form class=\"space-y-4 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-16\">
\t\t\t<div>
\t\t\t\t<label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"username\">
\t\t\t\t\tNom de l'utilisateur
\t\t\t\t</label>
\t\t\t\t<input class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline\" id=\"username\" type=\"text\" placeholder=\"Nom d'utilisateur\">
\t\t\t</div>
\t\t\t<div>
\t\t\t\t<label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"mail\">
\t\t\t\t\temail
\t\t\t\t</label>
\t\t\t\t<input class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline\" id=\"mail\" type=\"email\" placeholder=\"exemple@exemple.com\">
\t\t\t</div>
\t\t\t<div>
\t\t\t\t<label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"password\">
\t\t\t\t\tMots de passe
\t\t\t\t</label>
\t\t\t\t<input class=\"shadow appearance-none border rounded w-full py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline\" id=\"password\" type=\"password\" placeholder=\"******************\">
\t\t\t</div>
\t\t\t<div>
\t\t\t\t<label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"confirmPassword\">
\t\t\t\t\tConfirmer Mots de passe
\t\t\t\t</label>
\t\t\t\t<input class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline\" id=\"confirmPassword\" type=\"password\" placeholder=\"******************\">
\t\t\t\t<p class=\"text-sm\">Vous avez déjà un compte ?<br/>
\t\t\t\t\t<a class=\"underline text-primary\" href=\"#\">Connectez-vous</a>
\t\t\t\t</p>
\t\t\t</div>
\t\t\t<button class=\"bg-primary hover:bg-blue-400 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline\" type=\"button\">
\t\t\t\tInscription
\t\t\t</button>
\t\t</form>
\t</div>
{% endblock %}
", "platform/security/registration.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\platform\\security\\registration.html.twig");
    }
}
