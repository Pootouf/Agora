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

/* shared/navbar.html.twig */
class __TwigTemplate_59cb9cb2351dad7e65adb5f1bf3be906 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "shared/navbar.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "shared/navbar.html.twig"));

        // line 1
        echo "<nav class=\"flex items-center justify-between p-6 lg:px-8 relative z-50\" aria-label=\"Global\">
\t<a href=\"/\" class=\"-m-1.5 p-1.5 text-xl font-bold\">
\t\tAGORA
\t</a>
\t<a href=\"#\" class=\"text-sm font-semibold leading-6 text-gray-900\">Log in
\t\t<span aria-hidden=\"true\">&rarr;</span>
\t</a>
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
        return "shared/navbar.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  43 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<nav class=\"flex items-center justify-between p-6 lg:px-8 relative z-50\" aria-label=\"Global\">
\t<a href=\"/\" class=\"-m-1.5 p-1.5 text-xl font-bold\">
\t\tAGORA
\t</a>
\t<a href=\"#\" class=\"text-sm font-semibold leading-6 text-gray-900\">Log in
\t\t<span aria-hidden=\"true\">&rarr;</span>
\t</a>
</nav>
", "shared/navbar.html.twig", "C:\\Users\\Cheetoh\\Desktop\\agora\\templates\\shared\\navbar.html.twig");
    }
}
