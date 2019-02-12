<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\Facade;

class ContentTypeFacade
{
    public function createX()
    {
        $contentTypeCreateStruct = $this->contentTypeContext->newContentTypeCreateStruct($contentTypeIdentifier);
        $contentTypeCreateStruct->names = ['eng-GB' => $contentTypeName];
    }
}