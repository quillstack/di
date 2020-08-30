<?php

declare(strict_types=1);

namespace QuillDIMocks\Database;

final class MockDatabase
{
    public string $hostname;
    public string $user;
    public string $password;
    public string $database;

    public function __construct(string $hostname, string $user, string $password, string $database)
    {
        $this->hostname = $hostname;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
    }
}
