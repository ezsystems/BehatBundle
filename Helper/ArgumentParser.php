<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\BehatBundle\Helper;

use Behat\Gherkin\Node\TableNode;
use EzSystems\BehatBundle\API\Facade\RoleFacade;
use EzSystems\EzPlatformAdminUi\Behat\Helper\EzEnvironmentConstants;

class ArgumentParser
{
    private const ROOT_KEYWORD = 'root';

    public function __construct(RoleFacade $roleFacade)
    {
        $this->roleFacade = $roleFacade;
    }

    /**
     * Generates URLAlias value based on given Content Path.
     *
     * Example: Home/New Folder => /Home/New-Folder
     * Example: root => /
     *
     * @param string $url
     *
     * @return mixed|string
     */
    public function parseUrl(string $url)
    {
        if ($url === 'root') {
            return '/';
        }

        $url = str_replace(' ', '-', $url);

        return strpos($url, '/') === 0 ? $url : sprintf('/%s', $url);
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
     *
     * @param string $path
     *
     * @return string
     */
    public function replaceRootKeyword(string $path): string
    {
        return str_replace(self::ROOT_KEYWORD, EzEnvironmentConstants::get('ROOT_CONTENT_NAME'), $path);
    }
}
