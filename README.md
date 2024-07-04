# Symfony Snake Command

This PHP project is a Symfony console command that interacts with a remote snake game API. The command collects user inputs, fetches a CSRF token, submits user details, and records the game score.

## Features

- Prompts user for name, email, desired score, and solving time.
- Fetches a CSRF token for secure communication.
- Submits user details to the snake game API.
- Records the game score and solving time.

## Prerequisites

- PHP 8.3
- Symfony 7.1
- Composer
- Internet connection (for API requests)

## Installation

1. Clone the repository:
   \```sh
   git clone https://github.com/your-username/symfony-snake-command.git
   cd symfony-snake-command
   \```

2. Install the dependencies:
   \```sh
   composer install
   \```

3. Configure your environment variables, particularly for the HTTP client if needed.

## Usage

Run the Symfony console command:
\```sh
php bin/console app:snake
\```

You will be prompted to enter the following details:
- **Name**: Your full name (at least 2 characters).
- **Email**: A valid email address.
- **Score**: The desired score as an integer.
- **Solving Time**: The solving time as a float value.

The command will then interact with the snake game API to submit your details and record the score.

## Code Overview

The command is implemented in the `App\Command\SnakeCommand` class. Here's a brief overview of its key parts:

### Dependencies

- `HttpClientInterface`: Used to make HTTP requests to the snake game API.
- `SymfonyStyle`: Provides styled console output.

### Command Configuration

The command is configured with the `#[AsCommand]` attribute:
\```php
#[AsCommand(
name: 'app:snake',
description: 'Add a short description for your command',
)]
\```

### User Input

Prompts the user for their name, email, score, and solving time:
\```php
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
\```

### API Requests

1. Fetch CSRF token:
   \```php
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
   \```

2. Submit user details:
   \```php
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
   \```

3. Record score:
   \```php
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
   \```

## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss any changes.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.