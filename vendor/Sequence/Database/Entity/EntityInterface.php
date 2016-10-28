<?php


namespace Sequence\Database\Entity;


interface EntityInterface
{
    /**
     * @param array $data
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data);

    /**
     * @return array
     *
     * @see get_object_vars
     */
    public function toArray();
} 