<?php declare(strict_types=1);

namespace MaxServ\RestClient;

use GuzzleHttp\Client;
use JsonSchema\Validator;
use MaxServ\RestClient\Exception\JsonSchemaValidationException;
use MaxServ\RestClient\Validator\JsonSchemaValidator;
use MaxServ\RestClient\Validator\ValidatorInterface;
use Psr\Http\Message\ResponseInterface;

class RestClient
{
    private string $accessToken;
    private Client $client;

    /**
     * @var ValidatorInterface[]
     */
    private array $validators;

    public function __construct(Client $client, string $accessToken, array $validators = [])
    {
        $this->accessToken = $accessToken;
        $this->client = $client;
        $this->validators = $validators;
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws JsonSchemaValidationException
     */
    public function post(string $endpoint, array $data): ResponseInterface
    {
        $json = json_encode($data);
        if (isset($data['review']) && is_array($data['review'])) {
            $this->validators[] = new JsonSchemaValidator();
        }

        $this->validateInputJson($json);

        $options = [];
        $options['headers'] = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->accessToken
        ];

        $options['body'] = $json;

        return $this->client->post($endpoint, $options);
    }

    /**
     * @param string $json
     */
    private function validateInputJson(string $json)
    {
        foreach ($this->validators as $validator) {
            $validator->validate($json);
        }
    }
}
