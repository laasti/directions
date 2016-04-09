<?php

namespace Laasti\Directions;

interface UrlBuilderAwareInterface
{
    public function setUrlBuilder(UrlBuilder $builder);

    public function getUrlBuilder();

    public function createUrl($format, $params = [], $host = false);
}
