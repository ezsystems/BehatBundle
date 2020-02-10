<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Command;

use Buzz\Client\Curl;
use Nyholm\Psr7\Factory\Psr17Factory as HttpFactory;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetPullRequestDataCommand extends Command
{
    /** @var string */
    private $token;

    /** @var \Http\Client\HttpClient */
    private $httpClient;

    public function __construct()
    {
        parent::__construct();
        $this->httpClient = new Curl(
            [
                'verify' => false,
                'timeout' => 90,
                'allow_redirects' => true,
            ],
            new HttpFactory()
        );
    }

    protected function configure()
    {
        $this
            ->setName('ezsystems:github:get-pull-request-data')
            ->setDescription('Get data about a GitHub Pull Request')
            ->addArgument(
                'pull-request-url',
                InputArgument::REQUIRED,
                'GitHub Pull Request URL')
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'GitHub OAuth token')
            ->setHelp('Help text here');
    }

    //# - get target page-builder branch (dictionary, 2.5 -> 1.3, master -> master)

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pullRequestUrl = $input->getArgument('pull-request-url');
        $this->token = $input->getArgument('token');

        $prUrlData = $this->getPullRequestDataFromURL($pullRequestUrl);

        $responseBody = $this->getPullRequestData($prUrlData['owner'], $prUrlData['repository'], $prUrlData['prNumber'])->getBody();

        $responseData = json_decode($responseBody->getContents(), true);
        $targetBranch = $responseData['base']['ref'];
        $branchName = $responseData['head']['ref'];
        $repositoryURL = $responseData['head']['repo']['html_url'];

        $composerExtraData = $this->getComposerExtraData($prUrlData['owner'], $prUrlData['repository'], $targetBranch);
        $metarepositoryBranch = $composerExtraData['metarepositoryTargetBranch'];
        $branchAlias = $composerExtraData['branchAlias'];
        $pageBuilderBranch = $this->getPageBuilderBranchName($metarepositoryBranch);

        $outputString = sprintf('%s %s %s %s %s %s',
            $repositoryURL,
            $branchName,
            $branchAlias,
            $prUrlData['repository'],
            $metarepositoryBranch,
            $pageBuilderBranch);

        $output->write($outputString);
    }

    private function getPullRequestData(string $owner, string $repository, string $prNumber): Response
    {
        $requestUrl = sprintf('https://api.github.com/repos/%s/%s/pulls/%s', $owner, $repository, $prNumber);

        $request = new Request('GET', $requestUrl);
        $request = $request
                        ->withHeader('User-Agent', 'ezrobot')
                        ->withHeader('Authorization', sprintf('token %s', $this->token))
                        ->withHeader('Accept', 'application/vnd.github.v3+json');

        return $this->httpClient->sendRequest($request);
    }

    private function getComposerExtraData(string $owner, string $repository, string $branchName): array
    {
        $composerJsonFileURL = sprintf('https://raw.githubusercontent.com/%s/%s/%s/composer.json', $owner, $repository, $branchName);
        $data = json_decode(file_get_contents($composerJsonFileURL), true);

        $branchAlias = $data['extra']['branch-alias']['dev-master'];
        $metarepositoryTargetBranch = array_key_exists('_ezplatform_branch_for_behat_tests', $data['extra'])
            ? $data['extra']['_ezplatform_branch_for_behat_tests']
            : '';

        return [
            'branchAlias' => $branchAlias,
            'metarepositoryTargetBranch' => $metarepositoryTargetBranch,
            ];
    }

    private function getPullRequestDataFromURL(string $pullRequestLink): array
    {
        $matches = [];
        preg_match('/.*github.com\/(.*)\/(.*)\/pull\/(.*)/', $pullRequestLink, $matches);
        list(, $owner, $repository, $prNumber) = $matches;

        return [
            'owner' => $owner,
            'repository' => $repository,
            'prNumber' => $prNumber,
        ];
    }

    private function getPageBuilderBranchName(string $metarepositoryTargetBranch)
    {
        $pageBuilderTargetBranches = [
            '2.5' => '1.3',
            'master' => 'master',
        ];

        if (!array_key_exists($metarepositoryTargetBranch, $pageBuilderTargetBranches)) {
            throw new \Exception(sprintf('Cannot find suitable PageBuilder branch for %s metareposiotry branch', $metarepositoryTargetBranch));
        }

        return $pageBuilderTargetBranches[$metarepositoryTargetBranch];
    }
}
