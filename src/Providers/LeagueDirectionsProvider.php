<?php


namespace Laasti\Directions\Providers;

class LeagueDirectionsProvider extends \League\Container\ServiceProvider\AbstractServiceProvider implements \League\Container\ServiceProvider\BootableServiceProviderInterface
{

    protected $provides = [
        'Laasti\Directions\Strategies\StrategyInterface',
        'Laasti\Directions\Strategies\HttpMessageStrategy',
        'Laasti\Directions\Strategies\PeelsStrategy',
        'Laasti\Directions\Locator',
        'Laasti\Directions\RouteCollection',
        'Laasti\Directions\UrlBuilder',
        'Laasti\Directions\Router',
        'Laasti\Directions\RouterInterface',
    ];

    protected $defaultConfig = [
        'strategy' => 'Laasti\Directions\Strategies\StrategyInterface',
        'router' => 'Laasti\Directions\Router',
        'builder' => 'Laasti\Directions\UrlBuilder',
        'routes' => []
    ];

    public function register()
    {
        $this->getContainer()->add('Laasti\Directions\Strategies\HttpMessageStrategy', 'Laasti\Directions\Strategies\HttpMessageStrategy');
        $this->getContainer()->add('Laasti\Directions\Strategies\PeelsStrategy', 'Laasti\Directions\Strategies\PeelsStrategy')->withArgument('Laasti\Peels\Http\HttpRunner');

        if ($this->getContainer()->has('Laasti\Peels\Http\HttpRunner')) {
            $this->getContainer()->add('Laasti\Directions\Strategies\StrategyInterface', 'Laasti\Directions\Strategies\PeelsStrategy')->withArgument('Laasti\Peels\Http\HttpRunner');
        } else {
            $this->getContainer()->add('Laasti\Directions\Strategies\StrategyInterface', 'Laasti\Directions\Strategies\HttpMessageStrategy');
        }
        $this->getContainer()->add('Laasti\Directions\UrlBuilder')->withArguments(['Psr\Http\Message\ServerRequestInterface', 'Laasti\Directions\RouteCollection']);

        $this->getContainer()->add('Laasti\Directions\RouterInterface', 'Laasti\Directions\Router')->withArgument('Laasti\Directions\RouteCollection');
        $this->getContainer()->add('Laasti\Directions\RouteCollection', 'Laasti\Directions\RouteCollection')->withArguments([
            'Laasti\Directions\Strategies\StrategyInterface'
        ]);

        $di = $this->getContainer();
        foreach ($this->getConfig() as $name => $config) {
            $config += $this->defaultConfig;
            $this->getContainer()->share('directions.collections.'.$name, 'Laasti\Directions\RouteCollection')->withArguments([
                $config['strategy']
            ]);
            $this->getContainer()->share('directions.routers.'.$name, $config['router'])->withArguments(['directions.collections.'.$name]);
            $this->getContainer()->share('directions.builders.'.$name, $config['builder'])->withArguments(['Psr\Http\Message\ServerRequestInterface', 'directions.collections.'.$name]);
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
                } else if ($alias === 'directions.builders.'.$name) {
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
            $aliases[] = 'directions.builders.'.$name;
            $aliases[] = 'directions.collections.'.$name;
        }

        return array_merge($this->provides, $aliases);
    }

    public function boot()
    {
        $this->getContainer()->inflector('Laasti\Directions\RouterAwareInterface')
             ->invokeMethod('setRouter', ['Laasti\Directions\RouterInterface']);
        $this->getContainer()->inflector('Laasti\Directions\UrlBuilderAwareInterface')
             ->invokeMethod('setUrlBuilder', ['Laasti\Directions\UrlBuilder']);
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
