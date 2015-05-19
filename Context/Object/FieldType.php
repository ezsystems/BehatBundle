<?php
/**
 * File containing the Content Type context
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
     * @Given a Content Type with an :fieldType Field exists
     *
     * Creates a ContentType with only the desired FieldType
     */
    public function createContentTypeWithFieldType( $fieldType )
    {
        return $this->getFieldTypeManager()->createField( $fieldType );
    }

    /**
     * @Given I create a content of this Content Type
     *
     * Creates a Content with the previously defined ContentType
     */
    public function createContentOfThisType()
    {
        return $this->getFieldTypeManager()->executeDelayedOperations();
    }
    /**
     * @Then I should have an "integer" field
     *
     * Creates a Content with the previously defined ContentType
     */
    public function verifyContentOfType()
    {
//        return $this->getFieldTypeManager()->createContent( $fieldType );
    }
}
