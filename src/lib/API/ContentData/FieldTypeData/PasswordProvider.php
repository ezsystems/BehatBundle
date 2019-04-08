<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\API\ContentData\FieldTypeData;


class PasswordProvider implements FieldTypeDataProviderInterface
{
    public function canWork(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'password';
    }

    public function generateData(string $language)
    {
        return 'Passw0rd-42';
    }
}