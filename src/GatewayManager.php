<?php

namespace Phpfox\Model;


/**
 * Class GatewayManager
 *
 * @package Phpfox\Model
 */
class GatewayManager implements GatewayManagerInterface
{
    /**
     * @var GatewayInterface[]
     */
    protected $gateways = [];

    /**
     * @var string[]
     */
    protected $map = [];

    public function __construct()
    {
        $this->map = config('models');
    }

    public function set($id, $gateway)
    {
        $this->gateways[$id] = $gateway;
        return $this;
    }

    public function findById($id, $value)
    {
        return $this->get($id)->findById($value);
    }

    /**
     * @param string $id
     *
     * @return GatewayInterface
     */
    public function get($id)
    {
        return isset($this->gateways[$id]) ? $this->gateways[$id]
            : $this->gateways[$id] = $this->build($id);
    }

    public function build($id)
    {
        if (!isset($this->map[$id])
            || !class_exists($this->map[$id][0])
        ) {
            throw new GatewayException("gateway `$id` does not exists");
        }

        list($gateway, $modelClass, $adapter, $table) = $this->map[$id];

        if (!class_exists($gateway) || !class_exists($modelClass)) {
            throw new GatewayException("gateway `$id` does not exists");
        }

        return new $gateway($table, $modelClass, $adapter, $id);
    }
}