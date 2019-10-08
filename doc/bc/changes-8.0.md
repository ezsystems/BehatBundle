# Backwards compatibility changes

Changes affecting version compatibility with former or future versions.

## Changes

Autoloading:
- Bundle now uses PSR-4 autoloading

Removed Behat sentences:
- "there is a User Group with name :name"
- "there isn't a User Group with name :name"
- "there is a User Group with name :childGroupName in :parentGroup group"
- "there isn't a User Group with name :childGroupName in :parentGroup group"
- "there is a User Group with id :id"
- "there is a User Group with name :name with id :id in :parentGroup group"
- "there are the following User Groups::
- "a User Group with name :name already exists"
- "User Group with name :name exists"
- "User Group with name :name doesn't exist"
- "User Group with name :name exists in group :parentGroup"
- "User Group with name :name exists in :parentGroup group"
- "User Group with name :name doesn't exist in group :parentGroup"
- "User Group with name :name doesn't exist in :parentGroup group"
- "there is a User with name :username
- "there is a User with name :username, email :email and password :password
- "there is a User with name :username in :parentGroup group
- "there is a User with name :username, email :email and password :password in :parentGroup group
- "there is a User with name :username with the following fields:
- "there isn't a User with name :username
- "there isn't a User with name :username in :parentGroup group
- "there is a User with id :id
- "there is a User with name :username with id :id in :parentGroup group
- "there are the following Users:
- "a User with name :username already exists
- "User with name :username exists
- "User with name :username doesn't exist
- "User with name :username exists in group :parentGroup
- "User with name :username exists in :parentGroup group
- "User with name :username doesn't exist in group :parentGroup
- "User with name :username doesn't exist in :parentGroup group
- "User with name :username doesn't exist in the following groups:
- "User with name :username has the following fields:
- "User with name :username exists with the following fields:
- "a/an :name role exists
- "I see that a/an :name role exists
- ":name do not have any assigned policies
- ":name do not have any assigned Users and groups
- "I see that a/an :name role does not exists
- "a Content Type with an/a :fieldType field exists
- "a Content Type with an/a :fieldType with field definition name :name exists
- "a Content Type with a required :fieldType field exists
- "a Content Type with a required :fieldType with field definition name :name exists
- "a Content of this type exists
- "a Content of this type exists with :field Field Value set to :value
- "a Content Type with an/a :fieldType field exists with Properties:
- "a Content Type with an/a :fieldType field with name :name exists with Properties:
- "a/an :path folder exists
- "a/an :path article exists
- "a/an :path article draft exists
- "there is a Content Type Group with identifier :identifier"
- "there isn't a Content Type Group with identifier :identifier"
- "there are the following Content Type Groups:"
- "Content Type Group with identifier :identifier exists"
- "Content Type Group with identifier :identifier was created"
- "Content Type Group with identifier :identifier wasn't deleted"
- "Content Type Group with identifier :identifier doesn't exist (anymore)"
- "Content Type Group with identifier :identifier wasn't created"
- "Content Type Group with identifier :identifier was deleted"
- "(only) :total Content Type Group(s) with identifier :identifier exists"
- "(only) :total Content Type Group(s) exists with identifier :identifier"
- "a Content Type exists with identifier :identifier with fields:"
- "Content Type (with identifier) :identifier exists"
- "a Content Type exists with identifier :identifier in Group with identifier :groupIdentifier with fields:"
- "a Content Type does not exist with identifier :identifier"
- "Content Type (with identifier) :identifier does not exist"
- "Content Type (with identifier) :identifier exists in Group with identifier :groupIdentifier"

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

