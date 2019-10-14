# Extending BehatBundle

There are two extension points:
- ## Support for custom Field Types
If you want BehatBundle to support your custom Field Type when generating Content items you need to implement `EzSystems\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface` and tag the service with the `ezplatform.behat.fieldtype_data_provider` tag.

Example service definition:
```
    App\CustomFieldTypeProvider:
        tags: ['ezplatform.behat.fieldtype_data_provider']
```
Example class implementing the interface:
```
<?php

namespace App;

use EzSystems\Behat\API\ContentData\FieldTypeData\AbstractFieldTypeDataProvider;

class CustomFieldTypeProvider extends AbstractFieldTypeDataProvider
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'customfieldtypeidentifier';
    }

    public function generateData(string $language = 'eng-GB')
    {
        $this->setLanguage($language);

        return $this->getFaker()->paragraphs(5, true);
    }
}
```

- ## Support for custom Limitations
If you want to create Roles with permissions containing custom Limitations, you need to implement `EzSystems\Behat\API\Context\LimitationParser\LimitationParserInterface` and tag your service with the `ezplatform.behat.limitation_parser` tag. This will allow you to parse values passed in Gherkin tables into Limitation objects.

Example service definition:
```
    App\CustomLimitationParser:
        tags: ['ezplatform.behat.limitation_parser']
```
Example class implementing the interface:
```
<?php

namespace App;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\Behat\API\Context\LimitationParser\LimitationParserInterface
use CustomLimitation;

class CustomLimitationParser implements LimitationParserInterface
{
    public function supports(string $limitationType): bool
    {
        return $limitationType === 'customlimitation';
    }

    public function parse(string $limitationValues): Limitation
    {
        $limitations = explode(',', $limitationValues);

        return new CustomLimitation(
            ['limitationValues' => $limitations]
        );
    }
}
```
