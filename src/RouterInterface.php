<?php


namespace Laasti\Directions;

interface RouterInterface
{
    
    public function add($httpMethod, $route, $handler);
    
    /**
     * @return Route
     */
    public function find();
    
    /**
     * @return Response
     */
    public function findAndDispatch();
    
    /**
     * @return Response
     */
    public function dispatch(Route $route);
}
