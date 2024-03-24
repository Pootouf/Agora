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

/* platform/shared/navbar.html.twig */
class __TwigTemplate_52ce9cd7d0086d6111e2ab79c58952e7 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "platform/shared/navbar.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "platform/shared/navbar.html.twig"));

        // line 1
        echo "<nav class=\"flex items-center justify-between p-6 lg:px-8 relative z-50\" aria-label=\"Global\">
\t<a href=\"/\" class=\"-m-1.5 p-1.5 text-xl font-bold\">
\t\tAGORA
\t</a>
\t";
        // line 5
        if (twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 5, $this->source); })()), "user", [], "any", false, false, false, 5)) {
            // line 6
            echo "\t\t<a href=\"/logout\" class=\"text-sm font-semibold leading-6 text-gray-900\">Log out
\t\t</a>
\t";
        } else {
            // line 9
            echo "\t\t<a href=\"/login\" class=\"text-sm font-semibold leading-6 text-gray-900\">Log in
\t\t\t<span aria-hidden=\"true\">&rarr;</span>
\t\t</a>
\t";
        }
        // line 13
        echo "
</nav>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "platform/shared/navbar.html.twig";
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
        return array (  62 => 13,  56 => 9,  51 => 6,  49 => 5,  43 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<nav class=\"flex items-center justify-between p-6 lg:px-8 relative z-50\" aria-label=\"Global\">
\t<a href=\"/\" class=\"-m-1.5 p-1.5 text-xl font-bold\">
\t\tAGORA
\t</a>
\t{% if app.user %}
\t\t<a href=\"/logout\" class=\"text-sm font-semibold leading-6 text-gray-900\">Log out
\t\t</a>
\t{% else %}
\t\t<a href=\"/login\" class=\"text-sm font-semibold leading-6 text-gray-900\">Log in
\t\t\t<span aria-hidden=\"true\">&rarr;</span>
\t\t</a>
\t{% endif %}

</nav>
", "platform/shared/navbar.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\platform\\shared\\navbar.html.twig");
    }
}
