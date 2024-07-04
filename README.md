# Web Summer Camp 2024 Snake game result generator

This is a Symfony command that submits Snake game results to the API without the need to play the game.

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
   ```sh
   git clone https://github.com/MarioBlazek/web-summer-camp-2024-snake-solver
   cd web-summer-camp-2024-snake-solver
   ```

2. Install the dependencies:
   ```sh
   composer install
   ```

3. Run and profit:

## Usage

Run the Symfony console command:
```sh
php bin/console app:snake
```

You will be prompted to enter the following details:
- **Name**: Your full name (at least 2 characters).
- **Email**: A valid email address.
- **Score**: The desired score as an integer.
- **Solving Time**: The solving time as a float value.

The command will then interact with the snake game API to submit your details and record the score.

## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss any changes.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.