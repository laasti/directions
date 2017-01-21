<?php

namespace Laasti\Directions;

trait UrlBuilderAwareTrait
{
    protected $urlBuilder;

    public function getUrlBuilder()
    {
        return $this->urlBuilder;
    }

    public function setUrlBuilder(UrlBuilder $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
        return $this;
    }

    public function createUrl($format, $params = [], $host = false)
    {
        return $this->urlBuilder->create($format, $params, $host);
    }
}
