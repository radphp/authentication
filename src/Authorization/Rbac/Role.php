<?php

namespace Rad\Authorization\Rbac;

use InvalidArgumentException;
use RuntimeException;

/**
 * Role
 *
 * @package Rad\Authorization\Rbac
 */
class Role implements RoleInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var ResourceCollection
     */
    protected $resources;

    /**
     * Rad\Authorization\Rbac\Role constructor
     *
     * @param string                               $name      Role name
     * @param ResourceCollection|array|string|null $resources Resources
     */
    public function __construct($name, $resources = null)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Role name must be string.');
        }

        $this->name = $name;
        $this->setResources($resources);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setResources($resources)
    {
        $collection = new ResourceCollection();

        if (is_array($resources)) {
            foreach ($resources as $resource) {
                if ($resource instanceof ResourceInterface) {
                    $collection->attach($resource);
                } elseif (is_string($resource)) {
                    $collection->attach(new Resource($resource));
                } else {
                    throw new InvalidArgumentException(
                        'Resource must be string or an object implemented "Rad\Authorization\Rbac\ResourceInterface".'
                    );
                }
            }

            $this->resources = $collection;
        } elseif ($resources instanceof ResourceCollection) {
            $this->resources = $resources;
        } elseif (is_string($resources)) {
            $collection->attach(new Resource($resources));
            $this->resources = $collection;
        } else {
            $this->resources = $collection;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * {@inheritdoc}
     */
    public function addResource($resource)
    {
        if (!$resource instanceof ResourceInterface) {
            $resource = new Resource($resource);
        }

        $this->resources->attach($resource);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasResource($resource)
    {
        if (!$resource instanceof ResourceInterface) {
            $resource = new Resource($resource);
        }

        return $this->resources->contains($resource);
    }
}