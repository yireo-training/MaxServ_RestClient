<?php declare(strict_types=1);

namespace MaxServ\RestClient\Validator;

use JsonSchema\Validator;
use MaxServ\RestClient\Exception\JsonSchemaValidationException;

class JsonSchemaValidator implements ValidatorInterface
{
    public function validate(string $json): bool
    {
        $validator = new Validator;
        $object = json_decode($json, false);
        $validator->validate($object,
            (object)[
                '$ref' => 'file://' . realpath(__DIR__ . '/../schema/magento2-product-review-schema.json')
            ]);

        if (!$validator->isValid()) {
            $errors = [];
            foreach ($validator->getErrors() as $error) {
                $errors[] = var_export($error, true);
            }

            throw new JsonSchemaValidationException('JSON schema validation failed: ' . implode('; ', $errors));
        }

        return true;
    }
}
