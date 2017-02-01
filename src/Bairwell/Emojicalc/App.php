<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc;

use Bairwell\Emojicalc\Controllers\Index;
use Bairwell\Emojicalc\Entities\Operator;
use Bairwell\Emojicalc\Entities\Operators;
use Bairwell\Emojicalc\Entities\Symbol;

/**
 * Class App
 * @package Bairwell\Emojicalc
 */
class App
{
    /**
     * The router
     *
     * @var Router
     */
    protected $router;

    /**
     * Configuration data.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Basic App constructor.
     *
     * @param array $configuration Configuration details (to be used instead of default).
     * @param array $environment Environment details (overrides _SERVER) for testing.
     */
    public function __construct(array $configuration = [], array $environment = [])
    {
        $this->configuration = $this->buildConfiguration($configuration);
        $this->router = new Router($environment);
    }

    /**
     * Run the application (after registering routes)
     */
    public function run() {
        // should really use dependency injection/containers here.
        $indexController=new Index($this->configuration['operators']);
        $this->router->registerRoute('GET','/^\/?$/', [$indexController, 'startAction']);
        $this->router->registerRoute('POST','/^\/?$/', [$indexController, 'calculateAction']);
        $this->router->run();
    }

    /**
     * Build the basic configuration taking into account any default settings.
     *
     * @param array $configuration Configuration to use as user-defined.
     * @return array
     * @throws \Exception If the list of operators is not an Operator instance.
     */
    protected function buildConfiguration(array $configuration): array
    {
        // any default configurations
        $defaultConfiguration = [];
        // only build the operators if they aren't already set. why call the classes
        // unnecessarily?
        if (false === isset($configuration['operators'])) {
            $configuration['operators'] = $this->buildDefaultOperators();
        }
        if (false===($configuration['operators'] instanceof Operators)) {
            throw new \Exception('Invalid operators');
        }
        $configuration = array_merge($configuration, $defaultConfiguration);
        return $configuration;
    }

    /**
     * Build the list of default operators.
     * @return Operators
     */
    protected function buildDefaultOperators(): Operators
    {
        $operators = (new Operators())
            ->addOperator(new Operator('+', new Symbol("\u{1f47d}", 'Alien')))
            ->addOperator(new Operator('-', new Symbol("\u{1f480}", 'Skull')))
            ->addOperator(new Operator('*', new Symbol("\u{1f47b}", 'Ghost')))
            ->addOperator(new Operator('/', new Symbol("\u{1f631}", 'Scream'))
        );
        return $operators;
    }

}