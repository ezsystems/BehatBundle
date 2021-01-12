<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Command;

use eZ\Bundle\EzPublishCoreBundle\Command\BackwardCompatibleCommand;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateLanguageCommand extends Command implements BackwardCompatibleCommand
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        LanguageService $languageService,
        UserService $userService,
        PermissionResolver $permissionResolver
    ) {
        $this->languageService = $languageService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ibexa:behat:create-language')
            ->setAliases(['ez:behat:create-language'])
            ->setDescription('Create a Language')
            ->addArgument('language-code', InputArgument::REQUIRED)
            ->addArgument('language-name', InputArgument::OPTIONAL, 'Language name', '')
            ->addArgument(
                'user',
                InputArgument::OPTIONAL,
                'eZ Platform User with access to content / translations',
                'admin'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // set user with proper permissions to create language (content / translations)
        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin(
                $input->getArgument('user')
            )
        );

        $languageCreateStruct = $this->languageService->newLanguageCreateStruct();
        $languageCreateStruct->languageCode = $input->getArgument('language-code');
        $languageCreateStruct->name = $input->getArgument('language-name');

        $this->languageService->createLanguage($languageCreateStruct);

        return 0;
    }

    /**
     * @return string[]
     */
    public function getDeprecatedAliases(): array
    {
        return ['ez:behat:create-language'];
    }
}
