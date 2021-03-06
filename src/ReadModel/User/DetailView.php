<?php

declare(strict_types=1);

namespace App\ReadModel\User;

class DetailView
{
    public string $id;
    public string $date;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $role;
    public string $status;

    /**
     * @var NetworkView[]
     */
    public array $networks;
}