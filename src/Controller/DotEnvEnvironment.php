<?php
    namespace App\Controller;

    use Dotenv\Dotenv;

    class DotEnvEnvironment
    {
        public function load(string $path): void
        {
            Dotenv::createImmutable($path)->load();
        }
    }
?>