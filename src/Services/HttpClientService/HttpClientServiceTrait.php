<?php


namespace App\Services\HttpClientService;


use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait HttpClientServiceTrait
{
    private HttpClientInterface $client;
    private string $token;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getToken(string $url, string $username, string $password): string
    {
        try {
            $response = $this->client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    "username" => $username,
                    "password" => $password,
                ])
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === Response::HTTP_OK) {
                $content = $response->toArray();
                return $this->token = $content['token'];
            }

            throw new HttpClientServiceException('Invalid credentials', Response::HTTP_UNAUTHORIZED);

        } catch (TransportException $exception) {
            throw new HttpClientServiceException($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }


    }

    public function apiRequest(string $method, string $url, ?string $token = null, ?string $body = null): array
    {

        $headers['Content-Type'] = 'application/json';

        if ($token !== null) {
            $headers['Authorization'] = "Bearer " . $token;
        }

        $response = $this->client->request($method, $url, [
            'headers' => $headers,
            'body' => $body
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode === Response::HTTP_OK || $statusCode === Response::HTTP_NO_CONTENT) {
            return $response->toArray();
        }

        throw new HttpClientServiceException($response->getContent(), $statusCode);

    }

}