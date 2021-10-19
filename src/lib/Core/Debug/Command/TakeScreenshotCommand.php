<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Behat\Core\Debug\Command;

use Behat\Mink\Session;
use Cloudinary;
use Cloudinary\Uploader;
use Exception;
use Psy\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TakeScreenshotCommand extends Command
{
    private const CLOUD_NAME_KEY = 'cloud_name';
    private const PRESET_KEY = 'preset';
    private const CLOUD_NAME = 'ezplatformtravis';
    private const PRESET = 'ezplatform';

    /** @var \Behat\Mink\Session */
    private $session;

    public function __construct(Session $session)
    {
        parent::__construct();
        $this->session = $session;
    }

    protected function configure()
    {
        $this
            ->setName('screenshot')
            ->setDefinition([])
            ->setDescription('Takes a screenshot of the currently opened website')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $image = $this->session->getScreenshot();
        $filePath = sys_get_temp_dir() . \DIRECTORY_SEPARATOR . uniqid('debug') . '.png';

        file_put_contents($filePath, $image);

        Cloudinary::config([self::CLOUD_NAME_KEY => self::CLOUD_NAME, self::PRESET_KEY => self::PRESET]);

        try {
            $response = Uploader::unsigned_upload($filePath, self::PRESET);
            $output->writeln(sprintf('Open image at %s', $response['secure_url']));

            return 0;
        } catch (Exception $e) {
            $output->writeln(sprintf('Error while uploading image. %s', $e->getMessage()));

            return $e->getCode();
        }
    }
}
