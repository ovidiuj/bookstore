<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class ApiControllerTest extends WebTestCase
{

    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private string $apiHost;
    private string $username;
    private string $password;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client =  static::createClient();
        $this->apiHost = self::$kernel->getContainer()->getParameter('api.host');
        $this->username = self::$kernel->getContainer()->getParameter('api.username');
        $this->password = self::$kernel->getContainer()->getParameter('api.password');

    }

    public function testAddBookAsAnonymous(): void
    {
        $this->makePostRequestWithBodyAndToken('book', [], '');
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider bookDataProvider
     */
    public function testAddBook(array $book): void
    {
        $token = $this->getToken($this->username, $this->password);
        $this->assertNotEmpty($token);

        $this->makePostRequestWithBodyAndToken('book', $book, $token);
        $response = $this->getResponseArray();
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($response['data'][0]);
        $this->assertNotEmpty($response['data'][0]['attributes']);
        $this->assertEquals('book', $response['data'][0]['type']);
    }

    public function testAddBookWithInvalidData(): void
    {
        $book = [
            'cover' => 'Winston Smith',
            'author' => 'George Orwell',
            'description' => 'an omniscient government with an agenda.',
            'status' => Book::STATUS_PUBLIC
        ];

        $token = $this->getToken($this->username, $this->password);
        $this->assertNotEmpty($token);

        $this->makePostRequestWithBodyAndToken('book', $book, $token);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testGetBooks()
    {
        $token = $this->getToken($this->username, $this->password);
        $this->assertNotEmpty($token);

        $this->makeGetRequestWithBodyAndToken('books', $token, []);
        $response = $this->getResponseArray();
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($response['data'][0]);
        $this->assertNotEmpty($response['data'][0]['attributes']);
        $this->assertEquals('book', $response['data'][0]['type']);
        $this->assertNotEmpty($response['pagination']);

        return $response['data'][0]['id'];
    }

    /**
     * @depends testGetBooks
     */
    public function testGetPublicBook(string $bookId): void
    {
        $token = $this->getToken($this->username, $this->password);
        $this->assertNotEmpty($token);

        $this->makeGetRequestWithBodyAndToken("book/$bookId", $token, []);
        $response = $this->getResponseArray();

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($response['data'][0]);
        $this->assertNotEmpty($response['data'][0]['attributes']);
        $this->assertEquals('book', $response['data'][0]['type']);
        $this->assertEquals(Book::STATUS_PUBLIC, $response['data'][0]['attributes']['status']);
    }

    /**
     * @depends testGetBooks
     */
    public function testEditBook(string $bookId): void
    {
        $token = $this->getToken($this->username, $this->password);
        $this->assertNotEmpty($token);
        $book = [
            'status' => Book::STATUS_NOT_PUBLIC
        ];

        $this->makePatchRequestWithBodyAndToken("book/$bookId/edit", $token, $book);
        $response = $this->getResponseArray();
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($response['data'][0]);
        $this->assertNotEmpty($response['data'][0]['attributes']);
        $this->assertEquals('book', $response['data'][0]['type']);
        $this->assertEquals(Book::STATUS_NOT_PUBLIC, $response['data'][0]['attributes']['status']);
    }

    /**
     * @depends testGetBooks
     */
    public function testGetNotPublicBook(string $bookId): void
    {
        $token = $this->getToken($this->username, $this->password);
        $this->assertNotEmpty($token);

        $this->makeGetRequestWithBodyAndToken("book/$bookId", $token, []);
        $response = $this->getResponseArray();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($response['errors']);
        $this->assertEquals('A not-public book can\'t be fetched.', $response['errors'][0]);
    }

    public function bookDataProvider(): iterable
    {
        yield [
                [
                    'title' => '1984',
                    'cover' => 'Winston Smith',
                    'author' => 'George Orwell',
                    'description' => 'an omniscient government with an agenda.',
                    'status' => Book::STATUS_PUBLIC
                ],
                [
                    'title' => 'Adventures of Huckleberry Finn',
                    'cover' => 'Louisiana',
                    'author' => 'Mark Twain',
                    'description' => 'A young boy and a slave in 19th-century Louisiana must find their way home — with only the Mississippi River for a guide. ',
                    'status' => Book::STATUS_PUBLIC
                ],
                [
                    'title' => 'The Alchemist',
                    'cover' => 'Egypt',
                    'author' => 'Paulo Coelho',
                    'description' => 'and the magical story of Santiago’s journey to the pyramids of Egypt',
                    'status' => Book::STATUS_NOT_PUBLIC
                ],
        ];
    }

    protected function makePostRequestWithBodyAndToken(string $url, array $body, string $token): void
    {
        $this->client->request(
            'POST',
            $this->apiHost . '/api/v1/' . $url,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token],
            \json_encode($body)
        );
    }

    private function getToken($username, $password): string
    {
        $postRequest = array(
            'username' => $username,
            'password' => $password,
        );

        $this->client->request('POST',
            $this->apiHost . '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($postRequest)
        );

        $response = $this->getResponseArray();

        return $response['token'] ?? '';
    }

    private function getResponseArray(): array
    {
        return \json_decode($this->client->getResponse()->getContent(), true);
    }

    private function makeGetRequestWithBodyAndToken(string $url, string $token, array $parameters = []): void
    {
        $this->client->request(
            'GET',
            $this->apiHost . '/api/v1/' . $url,
            $parameters,
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]
        );
    }

    private function makePatchRequestWithBodyAndToken(string $url, string $token, array $parameters = []): void
    {
        $this->client->request(
            'PATCH',
            $this->apiHost . '/api/v1/' . $url,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode($parameters)
        );
    }
}