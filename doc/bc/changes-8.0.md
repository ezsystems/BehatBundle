# Backwards compatibility changes

Changes affecting version compatibility with former or future versions.

## Changes

Autoloading:
- Bundle now uses PSR-4 autoloading

Removed Behat sentences:
- 

Removed classes:
- EzSystems\BehatBundle\Context\Api\Context
- EzSystems\BehatBundle\Context\Browser\Context
- EzSystems\BehatBundle\Context\Object\ContentType
- EzSystems\BehatBundle\Context\Object\ContentTypeGroup
- EzSystems\BehatBundle\Context\Object\FieldType
- EzSystems\BehatBundle\Context\EzContext

- EzSystems\BehatBundle\Helper\EzAssertion
- EzSystems\BehatBundle\Helper\Gherkin
- EzSystems\BehatBundle\Helper\ValueObject
- EzSystems\BehatBundle\Helper\Xpath
- EzSystems\BehatBundle\ObjectManager\Base
- EzSystems\BehatBundle\ObjectManager\BasicContent
- EzSystems\BehatBundle\ObjectManager\FieldType
- EzSystems\BehatBundle\ObjectManager\Role
- EzSystems\BehatBundle\ObjectManager\User
- EzSystems\BehatBundle\ObjectManager\UserGroup

Removed traits:
- EzSystems\BehatBundle\Context\Browser\MinkTrait
- EzSystems\BehatBundle\Context\Browser\SubContext\Authentication
- EzSystems\BehatBundle\Context\Browser\SubContext\CommonActions
- EzSystems\BehatBundle\Context\Object\BasicContent
- EzSystems\BehatBundle\Context\Object\FieldType
- EzSystems\BehatBundle\Context\Object\Role
- EzSystems\BehatBundle\Context\Object\User
- EzSystems\BehatBundle\Context\Object\UserGroup

Removed interfaces:
- EzSystems\BehatBundle\ObjectManager\ObjectManagerInterface

Moved classes:
- EzSystems\BehatBundle\Context\Object\ContentContext -> ?
- Kernel -> Extensiom
- KernelBundleExtension -> BundleExtension
- AdminUI stuff -> 

