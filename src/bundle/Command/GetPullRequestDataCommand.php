<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\BehatBundle\Command;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Psr7\Request;
use Http\Discovery\HttpClientDiscovery;
use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\MessageFactoryDiscovery;

class GetPullRequestDataCommand extends Command
{
    /** @var string */
    private $token;

    /** @var \Http\Client\HttpClient */
    private $httpClient;

    public function __construct()
    {
        parent::__construct();
        $this->httpClient = new HttpMethodsClient(
            HttpClientDiscovery::find(),
            MessageFactoryDiscovery::find()
        );
    }

    protected function configure()
    {
        $this
            ->setName('ezplatform:tools:get-pull-request-data')
            ->setDescription('Get data about a GitHub Pull Request')
            ->addArgument(
                'pull-request-url',
                InputArgument::REQUIRED,
                'GitHub Pull Request URL')
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'GitHub OAuth token')
            ->setHelp("This command outputs data in given order:
            - repository URL
            - name of the branch used in PR
            - branch alias for the PR
            - repository name
            - corresponding metarepository branch to run tests on
            - corresponding Page Builder branch to run tests on
Command accepts two parameters: GitHub Pull request link and GitHub OATH Token.
If you have configured Composer with your token it can be obtained by running 'composer config github-oauth.github.com --global'")
            ->addUsage('https://github.com/ezsystems/ezplatform-admin-ui/pull/1250 ff34885a8624460a855540c6592698d2f1812843');
    }

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

    private function getPullRequestData(string $owner, string $repository, string $prNumber): ResponseInterface
    {
        $requestUrl = sprintf('https://api.github.com/repos/%s/%s/pulls/%s', $owner, $repository, $prNumber);

        return $this->sendRequest($requestUrl, 'application/vnd.github.v3+json');
    }

    private function getComposerExtraData(string $owner, string $repository, string $branchName): array
    {
        $requestUrl = sprintf(
            'https://api.github.com/repos/%s/%s/contents/composer.json?ref=%s',
            $owner,
            $repository,
            $branchName
        );

        $composerJsonFile = $this->sendRequest($requestUrl, 'application/vnd.github.v3.raw');
        $data = json_decode($composerJsonFile->getBody()->getContents(), true);

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

    private function getPageBuilderBranchName(string $metarepositoryTargetBranch): string
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

    private function sendRequest(string $requestUrl, string $acceptFormat): ResponseInterface
    {
        $headers = [
            'User-Agent' => 'ezrobot',
            'Authorization' => sprintf('token %s', $this->token),
            'Accept' => $acceptFormat,
        ];

        $request = new Request('GET', $requestUrl, $headers);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception(
                sprintf('GitHub API returned code %d, expected 200. Reason: %s',
                    $response->getStatusCode(),
                    $response->getReasonPhrase())
            );
        }

        return $response;
    }
}
