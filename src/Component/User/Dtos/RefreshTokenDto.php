<?php

declare(strict_types=1);

namespace App\Component\User\Dtos;

class RefreshTokenDto
{
    private int $id;
    private int $iat;

    public function __construct(int $id, int $iat)
    {
        $this->id = $id;
        $this->iat = $iat;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIat(): int
    {
        return $this->iat;
    }
}
