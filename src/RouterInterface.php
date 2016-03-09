<?php


namespace Laasti\Directions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

interface RouterInterface
{

    public function add($httpMethod, $route, $handler);

    /**
     * @return ServerRequestInterface
     */
    public function find(ServerRequestInterface $request, ResponseInterface $response);

    public function findRoute($method, $route);
    
    /**
     * @return Response
     */
    public function findAndDispatch(ServerRequestInterface $request, ResponseInterface $response);
    
    /**
     * @return Response
     */
    public function dispatch(ServerRequestInterface $request = null, ResponseInterface $response = null);

    /**
     * @return mixed
     */
    public function dispatchRoute(Route $route);
}
