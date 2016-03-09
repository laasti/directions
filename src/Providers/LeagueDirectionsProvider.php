<?php


namespace Laasti\Directions\Providers;

class LeagueDirectionsProvider extends \League\Container\ServiceProvider\AbstractServiceProvider
{

    protected $provides = [
        'Laasti\Directions\Resolvers\ResolverInterface',
        'Laasti\Directions\Resolvers\CallableResolver',
        'Laasti\Directions\Resolvers\ContainerResolver',
        'Laasti\Directions\Strategies\StrategyInterface',
        'Laasti\Directions\Strategies\HttpMessageStrategy',
        'Laasti\Directions\Strategies\PeelsStrategy',
        'Laasti\Directions\Locator',
        'Laasti\Directions\RouteCollection',
        'Laasti\Directions\Router',
        'Laasti\Directions\RouterInterface',
    ];

    protected $defaultConfig = [
        'resolver' => 'Laasti\Directions\Resolvers\ResolverInterface',
        'strategy' => 'Laasti\Directions\Strategies\StrategyInterface',
        'router' => 'Laasti\Directions\Router',
        'routes' => []
    ];

    public function register()
    {
        $this->getContainer()->add('Laasti\Directions\Resolvers\CallableResolver', 'Laasti\Directions\Resolvers\CallableResolver');
        $this->getContainer()->add('Laasti\Directions\Resolvers\CallableResolver', 'Laasti\Directions\Resolvers\ContainerResolver');

        if ($this->getContainer()->has('Interop\Container\ContainerInterface')) {
            $this->getContainer()->get('Interop\Container\ContainerInterface');
            $this->getContainer()->add('Laasti\Directions\Resolvers\ResolverInterface', 'Laasti\Directions\Resolvers\ContainerResolver')->withArgument('Interop\Container\ContainerInterface');
        } else {
            $this->getContainer()->add('Laasti\Directions\Resolvers\ResolverInterface', 'Laasti\Directions\Resolvers\CallableResolver');
        }

        $this->getContainer()->add('Laasti\Directions\Strategies\HttpMessageStrategy', 'Laasti\Directions\Strategies\HttpMessageStrategy');
        $this->getContainer()->add('Laasti\Directions\Strategies\PeelsStrategy', 'Laasti\Directions\Strategies\PeelsStrategy')->withArgument('Laasti\Peels\StackBuilder');

        if ($this->getContainer()->has('Laasti\Peels\StackBuilderInterface')) {
            $this->getContainer()->add('Laasti\Directions\Strategies\StrategyInterface', 'Laasti\Directions\Strategies\PeelsStrategy')->withArgument('Laasti\Peels\StackBuilder');
        } else {
            $this->getContainer()->add('Laasti\Directions\Strategies\StrategyInterface', 'Laasti\Directions\Strategies\HttpMessageStrategy');
        }

        $this->getContainer()->add('Laasti\Directions\RouterInterface', 'Laasti\Directions\Router')->withArgument('Laasti\Directions\RouteCollection');
        $this->getContainer()->add('Laasti\Directions\RouteCollection', 'Laasti\Directions\RouteCollection')->withArguments([
            'Laasti\Directions\Resolvers\ResolverInterface', 'Laasti\Directions\Strategies\StrategyInterface'
        ]);

        $di = $this->getContainer();
        foreach ($this->getConfig() as $name => $config) {
            $config += $this->defaultConfig;
            $this->getContainer()->share('directions.collections.'.$name, 'Laasti\Directions\RouteCollection')->withArguments([
                $config['resolver'], $config['strategy']
            ]);
            $this->getContainer()->share('directions.routers.'.$name, $config['router'])->withArguments(['directions.collections.'.$name]);
            $this->getContainer()->share('directions.'.$name, function($router) use ($di, $config) {
                foreach ($config['routes'] as $routeArgs) {
                    call_user_func_array([$router, 'add'], $routeArgs);
                }
                return $router;
            })->withArguments(['directions.routers.'.$name]);
        }
    }
    
    public function provides($alias = null)
    {
        $names = array_keys($this->getConfig());
        if (!is_null($alias)) {
            if (in_array($alias, $this->provides)) {
                return true;
            }
            foreach ($names as $name) {
                if ($alias === 'directions.'.$name) {
                    return true;
                } else if ($alias === 'directions.routers.'.$name) {
                    return true;
                } else if ($alias === 'directions.collections.'.$name) {
                    return true;
                }
            }
        }

        $aliases = [];
        foreach ($names as $name) {
            $aliases[] = 'directions.'.$name;
            $aliases[] = 'directions.routers.'.$name;
            $aliases[] = 'directions.collections.'.$name;
        }

        return array_merge($this->provides, $aliases);
    }

    protected function getConfig()
    {
        $config = $this->getContainer()->get('config');
        if (isset($config['directions'])) {
            return $config['directions'];
        }

        return [];
    }
    
}
