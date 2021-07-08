<?php declare(strict_types=1);

namespace MaxServ\RestClient\Validator;

interface ValidatorInterface
{
    public function validate(string $json): bool;
}
