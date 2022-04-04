<?php

namespace FSProVendor\WPDesk\View\Renderer;

use FSProVendor\WPDesk\View\Resolver\Resolver;
/**
 * Can render templates
 */
class SimplePhpRenderer implements \FSProVendor\WPDesk\View\Renderer\Renderer
{
    /** @var Resolver */
    private $resolver;
    public function __construct(\FSProVendor\WPDesk\View\Resolver\Resolver $resolver)
    {
        $this->set_resolver($resolver);
    }
    /**
     * @param Resolver $resolver
     *
     * @return void|Resolver
     */
    public function set_resolver(\FSProVendor\WPDesk\View\Resolver\Resolver $resolver)
    {
        $this->resolver = $resolver;
    }
    /**
     * @param string $template
     * @param array|null $params
     *
     * @return string
     */
    public function render($template, array $params = null)
    {
        if ($params !== null) {
            \extract($params, \EXTR_SKIP);
        }
        \ob_start();
        include $this->resolver->resolve($template . '.php');
        return \ob_get_clean();
    }
}
