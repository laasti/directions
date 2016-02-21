<?php


namespace Laasti\Directions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

interface RouterInterface
{

    public function add($httpMethod, $route, $handler);

    /**
     * @return Route
     */
    public function find(ServerRequestInterface $request, ResponseInterface $response);
    
    /**
     * @return Response
     */
    public function findAndDispatch(ServerRequestInterface $request, ResponseInterface $response);
    
    /**
     * @return Response
     */
    public function dispatch(Route $route, ServerRequestInterface $request = null, ResponseInterface $response = null);
}
