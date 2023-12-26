<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateBookDto
{
    public function __construct(
        #[Assert\NotBlank(message:'cant be blank')]
        public readonly string $title,
        
        #[Assert\NotBlank]
        public readonly float $price,

        public ?string $author = null,

        public ?string $description = null,
    )
    {
    }
}