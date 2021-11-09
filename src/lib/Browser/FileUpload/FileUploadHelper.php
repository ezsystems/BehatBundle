<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Behat\Browser\FileUpload;

use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;

class FileUploadHelper
{
    /** @var \Behat\Mink\Session */
    private $session;
    /** @var \FriendsOfBehat\SymfonyExtension\Mink\MinkParameters */
    private $minkParameters;

    public function __construct(Session $session, MinkParameters $minkParameters)
    {
        $this->session = $session;
        $this->minkParameters = $minkParameters;
    }

    public function getRemoteFileUploadPath($filename)
    {
        if (!preg_match('#[\w\\\/\.]*\.zip$#', $filename)) {
            throw new \InvalidArgumentException('Zip archive required to upload to remote browser machine.');
        }

        $localFile = sprintf('%s%s', $this->minkParameters['files_path'], $filename);

        return $this->session->getDriver()->getWebDriverSession()->file([
            'file' => base64_encode(file_get_contents($localFile)),
        ]);
    }
}
