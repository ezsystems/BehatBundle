<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Behat;

use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\API\Facade\RoleFacade;
use Ibexa\Behat\Browser\Environment\ParameterProviderInterface;

class ArgumentParser
{
    private const ROOT_KEYWORD = 'root';

    /** @var \Ibexa\Behat\Browser\Environment\ParameterProviderInterface */
    private $parameterProvider;

    public function __construct(RoleFacade $roleFacade, ParameterProviderInterface $parameterProvider)
    {
        $this->roleFacade = $roleFacade;
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * Generates URLAlias value based on given Content Path.
     *
     * Example: Home/New Folder => /Home/New-Folder
     * Example: root => /
     *
     * @return string
     */
    public function parseUrl(string $url)
    {
        $url = str_replace([' ', '@', ',', ':', $this->parameterProvider->getParameter('root_content_name'), 'root'], ['-', '-', '', '-', '', ''], $url);

        return 0 === strpos($url, '/') ? $url : sprintf('/%s', $url);
    }

    public function parseLimitations(TableNode $limitations)
    {
        $parsedLimitations = [];
        $limitationParsers = $this->roleFacade->getLimitationParsers();

        foreach ($limitations->getHash() as $rawLimitation) {
            foreach ($limitationParsers as $parser) {
                if ($parser->supports($rawLimitation['limitationType'])) {
                    $parsedLimitations[] = $parser->parse($rawLimitation['limitationValue']);

                    break;
                }
            }
        }

        return $parsedLimitations;
    }

    /**
     * Replaces the 'root' keyword with the name of the real root Content Item.
     *
     * Example: root/MyItem1 => eZ Platform/MyItem1 on ezplatform and Home/MyItem1 on ezplatform-ee
     */
    public function replaceRootKeyword(string $path): string
    {
        return str_replace(self::ROOT_KEYWORD, $this->parameterProvider->getParameter('root_content_name'), $path);
    }
}
