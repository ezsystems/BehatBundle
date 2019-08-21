<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Helper;

use eZ\Publish\API\Repository\Values\ValueObject as ValueObjectInterface;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

/**
 * Helper class for value objects handling.
 * Provides methods to:
 *  - set/get properties
 *  - serialize to array.
 */
class ValueObject
{
    /**
     * Gets $property from $object.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object The object to be updated
     * @param string $property Name of property or field
     *
     * @return mixed
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException If the property/field is not found
     */
    public static function getProperty(ValueObjectInterface $object, $property)
    {
        if (property_exists($object, $property)) {
            return $object->$property;
        } elseif (method_exists($object, 'setField')) {
            return $object->getField($property);
        } else {
            throw new InvalidArgumentException($property, "wasn't found in the '" . \get_class($object) . "' object");
        }
    }

    /**
     * Sets $property in $object to $value.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object The object to be updated
     * @param string $property Name of property or field
     * @param mixed  $value The value to set the property/field to
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException If the property/field is not found
     */
    public static function setProperty(ValueObjectInterface $object, $property, $value)
    {
        if (property_exists($object, $property)) {
            $object->$property = $value;
        } elseif (method_exists($object, 'setField')) {
            $object->setField($property, $value);
        } else {
            throw new InvalidArgumentException($property, "wasn't found in the '" . \get_class($object) . "' object");
        }
    }

    /**
     * Sets an objects properties.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object Object to be updated
     * @param array $values Associative array with properties => values
     */
    public static function setProperties(ValueObjectInterface $object, array $values)
    {
        foreach ($values as $property => $value) {
            $this->setProperty($object, $property, $value);
        }
    }

    /**
     * Convert/serialize ValueObject to array.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $object Object to get all properties/fields
     *
     * @return array
     *
     * @todo For ContentType the object will have several fields/properties with same name (for example 'names' that will exist in every FieldDefinition)
     */
    public static function tooArray(ValueObjectInterface $object)
    {
        // clone object to ReflectionClass
        $reflectionClass = new \ReflectionClass($object);

        // get each property/field
        $properties = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = $this->getProperty($object, $reflectionProperty->getName());
        }

        return $properties;
    }
}
