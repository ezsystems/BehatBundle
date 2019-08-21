<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Context\Object;

use Behat\Behat\Context\Context;
use EzSystems\BehatBundle\Context\RepositoryContext;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

/**
 * Sentences for ContentTypeGroups.
 */
class ContentTypeGroup implements Context
{
    use RepositoryContext;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /**
     * @injectService $repository @ezpublish.api.repository
     * @injectService $contentTypeService @ezpublish.api.service.content_type
     */
    public function __construct(Repository $repository, ContentTypeService $contentTypeService)
    {
        $this->setRepository($repository);
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @Given there is a Content Type Group with identifier :identifier
     *
     * Ensures a content type group exists, creating a new one if it doesn't.
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup
     */
    public function ensureContentTypeGroupExists($identifier)
    {
        /** @var \eZ\Publish\API\Repository\ContentTypeService */
        $contentTypeService = $this->contentTypeService;

        $found = false;
        // verify if the content type group exists
        try {
            $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier($identifier);
            $found = true;
        } catch (ApiExceptions\NotFoundException $e) {
            // other wise create it
            $ContentTypeGroupCreateStruct = $contentTypeService->newContentTypeGroupCreateStruct($identifier);
            $contentTypeGroup = $contentTypeService->createContentTypeGroup($ContentTypeGroupCreateStruct);
        }

        return [
            'found' => $found,
            'contentTypeGroup' => $contentTypeGroup,
        ];
    }

    /**
     * @Given there isn't a Content Type Group with identifier :identifier
     *
     * Ensures a content type group does not exist, removing it if necessary.
     */
    public function ensureContentTypeGroupDoesntExist($identifier)
    {
        /** @var \eZ\Publish\API\Repository\ContentTypeService */
        $contentTypeService = $this->contentTypeService;

        // attempt to delete the content type group with the identifier
        try {
            $contentTypeService->deleteContentTypeGroup(
                $contentTypeService->loadContentTypeGroupByIdentifier($identifier)
            );
        } catch (ApiExceptions\NotFoundException $e) {
            // nothing to do
        }
    }

    /**
     * @Given there are the following Content Type Groups:
     *
     * Make sure that content type groups in the provided table exist, by identifier. Example:
     *      | group                 |
     *      | testContentTypeGroup1 |
     *      | testContentTypeGroup2 |
     *      | testContentTypeGroup3 |
     */
    public function ensureContentTypeGroupsExists(TableNode $table)
    {
        $contentTypeGroups = $table->getTable();

        array_shift($contentTypeGroups);
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $this->ensureContentTypeGroupExists($contentTypeGroup[0]);
        }
    }

    /**
     * @Then Content Type Group with identifier :identifier exists
     * @Then Content Type Group with identifier :identifier was created
     * @Then Content Type Group with identifier :identifier wasn't deleted
     *
     * Checks that content type group with identifier $identifier exists
     */
    public function assertContentTypeGroupWithIdentifierExists($identifier)
    {
        Assert::assertTrue(
            $this->checkContentTypeGroupExistenceByIdentifier($identifier),
            "Couldn't find ContentTypeGroup with identifier '$identifier'"
        );
    }

    /**
     * @Then Content Type Group with identifier :identifier doesn't exist (anymore)
     * @Then Content Type Group with identifier :identifier wasn't created
     * @Then Content Type Group with identifier :identifier was deleted
     *
     * Checks that content type group with identifier $identifier does not exist
     */
    public function assertContentTypeGroupWithIdentifierDoesntExist($identifier)
    {
        Assert::assertFalse(
            $this->checkContentTypeGroupExistenceByIdentifier($identifier),
            "Unexpected ContentTypeGroup with identifer '$identifier' found"
        );
    }

    /**
     * @Then (only) :total Content Type Group(s) with identifier :identifier exists
     * @Then (only) :total Content Type Group(s) exists with identifier :identifier
     *
     * Checks that there are exactly ':total' content type groups with identifier $identifier
     */
    public function assertTotalContentTypeGroups($total, $identifier)
    {
        Assert::assertEquals(
            $this->countContentTypeGroup($identifier),
            $total
        );
    }

    /**
     * Checks if the ContentTypeGroup with $identifier exists.
     *
     * @param string $identifier Identifier of the possible content
     *
     * @return bool True if it exists
     */
    public function checkContentTypeGroupExistenceByIdentifier($identifier)
    {
        /** @var \eZ\Publish\API\Repository\ContentTypeService */
        $contentTypeService = $this->contentTypeService;

        // attempt to load the content type group with the identifier
        try {
            $contentTypeService->loadContentTypeGroupByIdentifier($identifier);

            return true;
        } catch (ApiExceptions\NotFoundException $e) {
            return false;
        }
    }

    /**
     * Find a non existing ContentTypeGroup identifier.
     *
     * @return string A not used identifier
     *
     * @throws \Exception Possible endless loop
     */
    private function findNonExistingContentTypeGroupIdentifier()
    {
        $i = 0;
        while ($i++ < 20) {
            $identifier = 'ctg' . rand(10000, 99999);
            if (!$this->checkContentTypeGroupExistenceByIdentifier($identifier)) {
                return $identifier;
            }
        }

        throw new \Exception('Possible endless loop when attempting to find a new identifier to ContentTypeGroups');
    }
}
