<?php

declare(strict_types=1);

namespace App\Command;

use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:snake',
    description: 'Play a snake game 😉',
)]
class SnakeCommand extends Command
{
    private const string URL_REFERER = 'https://snake-game.factory.dev/snake';
    private const string URL_SUBMIT_FORM = 'https://snake-game.factory.dev/snake-game/submit-form';
    private const string URL_ORIGIN = 'https://snake-game.factory.dev';
    private const string URL_GET_CSRF = 'https://snake-game.factory.dev/get-csrf-token';
    private const string URL_RECORD_RESULT = 'https://snake-game.factory.dev/snake-game/record-result';
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $io->ask('Please enter your name', null, function ($name) {
            if (empty($name) || strlen($name) < 2) {
                throw new \InvalidArgumentException('The name must be at least 2 characters long.');
            }
            return $name;
        });

        $email = $io->ask('Please enter your email', null, function ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('The email is not valid.');
            }
            return $email;
        });

        $score = $io->ask('Please enter wanted score', null, function ($value) {
            if (!is_numeric($value) || intval($value) != $value) {
                throw new \InvalidArgumentException('The value is not a valid integer.');
            }
            return (int)$value;
        });

        $solvingTime = $io->ask('Please enter solving time', null, function ($value) {
            if (!is_numeric($value) || floatval($value) != $value) {
                throw new \InvalidArgumentException('The value is not a valid float.');
            }
            return (float)$value;
        });

        $io->info("Thank you for your input, issuing request to snake...");

        $faker = Factory::create();
        $userAgent = $faker->userAgent;
        $crfUrl = sprintf('%s?%s', self::URL_GET_CSRF, md5($userAgent));

        $response = $this->client->request('GET', $crfUrl, [
            'headers' => [
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Connection' => 'keep-alive',
                'Referer' => self::URL_REFERER,
                'Sec-Fetch-Dest' => 'empty',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Site' => 'same-origin',
                'User-Agent' => $userAgent,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            $io->warning("Problem occurred when fetching captcha.");
            return Command::FAILURE;
        }

        $csrfToken = $response->toArray()['csrfToken'];

        $response = $this->client->request('POST', self::URL_SUBMIT_FORM, [
            'headers' => [
                'User-Agent' => $userAgent,
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate, br, zstd',
                'X-CSRF-Token' => $csrfToken,
                'Content-Type' => 'multipart/form-data',
                'Origin' => self::URL_ORIGIN,
                'Connection' => 'keep-alive',
                'Referer' => self::URL_ORIGIN,
            ],
            'body' => [
                'snake_game[FullName]' => $name,
                'snake_game[Email]' => $email,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            $io->warning("Problem occurred when submitting form.");
            return Command::FAILURE;
        }


        $response = $this->client->request('POST', self::URL_RECORD_RESULT, [
            'headers' => [
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Connection' => 'keep-alive',
                'Content-Type' => 'multipart/form-data',
                'Origin' => self::URL_ORIGIN,
                'Referer' => self::URL_REFERER,
                'Sec-Fetch-Dest' => 'empty',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Site' => 'same-origin',
                'User-Agent' => $userAgent,
                'X-CSRF-Token' => $csrfToken,
            ],
            'body' => [
                'email' => $email,
                'score' => (string)$score,
                'solving_time' => (string)$solvingTime,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            $io->warning("Problem occurred when submitting score.");
            return Command::FAILURE;
        }

        $io->success('Your score was successfully submitted 😋');

        return Command::SUCCESS;
    }
}
