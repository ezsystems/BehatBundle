<?php
/**
 * This file is part of the BehatBundle package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\ObjectManager;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions as ApiExceptions;

class Languages extends Base
{
    /**
     * Make sure a Language with name $name exists
     *
     * @param string $name Language name
     * @param string $code Language code
     *
     * @return \eZ\Publish\API\Repository\Values\Language
     */
    public function ensureLanguageExists( $name, $code )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $that = $this;
        $language = $repository->sudo(
            function() use( $repository, $name, $code, $that )
            {
                $language = null;
                $languageService = $repository->getContentLanguageService();
                try
                {
                    $language = $languageService->loadLanguage( $code );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    $languageCreateStruct = $languageService->newLanguageCreateStruct();
                    $languageCreateStruct->name = $name;
                    $languageCreateStruct->languageCode = $code;
                    $language = $languageService->createLanguage( $languageCreateStruct );
                    $that->addObjectToList( $language );
                }

                return $language;
            }
        );

        return $language;
    }

    /**
     * [destroy description]
     * @param  ValueObject $object [description]
     * @return [type]              [description]
     */
    protected function destroy( ValueObject $object )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getRepository();
        $repository->sudo(
            function() use( $repository, $object )
            {
                $languageService = $repository->getContentLanguageService();
                try
                {
                    $languageService->deleteLanguage( $object );
                }
                catch ( ApiExceptions\NotFoundException $e )
                {
                    // nothing to do
                }
            }
       );
    }

}
