<?php
/**
  *This file is part of the BehatBundle package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Context\Object;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert as Assertion;

/**
 * Sentences for Fields
 */
trait FieldType
{
    /**
     * @Given a Content Type with an :fieldType field exists
     * @Given a Content Type with an :fieldType with field definition name :name exists
     *
     * Creates a ContentType with only the desired FieldType
     */
    public function createContentTypeWithFieldType( $fieldType, $name = null )
    {
        return $this->getFieldTypeManager()->createField( $fieldType, $name );
    }

    /**
     * @Given a Content Type with a required :fieldType field exists
     * @Given a Content Type with a required :fieldType with field definition name :name exists
     *
     * Creates a ContentType with only the desired FieldType
     */
    public function createContentTypeWithRequiredFieldType( $fieldType, $name = null )
    {
        return $this->getFieldTypeManager()->createField( $fieldType, $name, true );
    }

    /**
     * @Given a Content of this type exists
     * @Given a Content of this type exists with :field Field Value set to :value
     * @And a Content of this type exists
     * @And a Content of this type exists with :field Field Value set to :value
     *
     * Creates a Content with the previously defined ContentType
     */
    public function createContentOfThisType( $field = null, $value = null )
    {
        return $this->getFieldTypeManager()->createContent( $field, $value );
    }

    /**
     * @Given a Content Type with an :fieldType field exists with Properties:
     * @Given a Content Type with an :fieldType field with name :name exists with Properties:
     */
    public function createContentOfThisTypeWithProperties( $fieldType, TableNode $properties, $name = null )
    {
        $this->getFieldTypeManager()->createField( $fieldType, $name );
        foreach ( $properties as $property )
        {
            switch( $property[ 'Validator' ] )
            {
                case 'maximum value validator':
                case 'maximum length validator':
                    $this->getFieldTypeManager()->addValueConstraint( $fieldType, $property[ 'Value' ], "max" );
                    break;
                case 'minimum value validator':
                case 'minimum length validator':
                    $this->getFieldTypeManager()->addValueConstraint( $fieldType, $property[ 'Value' ], "min" );
                    break;
            }
        }
    }
}
