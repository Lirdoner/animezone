<?php

namespace Sequence;


/**
 * Interface ProviderInterface
 * @package Sequence\Container
 */
interface ProviderInterface
{
    /**
     * @param Container $container
     * @param mixed $options
     */
    public function register(Container $container, $options);
} 