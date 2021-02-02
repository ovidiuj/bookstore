<?php


namespace App\Controller;


use App\Api\Response\ApiResponseInterface;
use App\Config\BookConfig;
use App\Services\BookServiceInterface;
use App\TransferObjects\Request\Book\BookRequestTransfer;
use App\TransferObjects\Request\Book\EditBookRequestTransfer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    private BookServiceInterface $bookService;
    private ApiResponseInterface $apiResponse;

    public function __construct(
        BookServiceInterface $bookService,
        ApiResponseInterface $apiResponse
    )
    {
        $this->bookService = $bookService;
        $this->apiResponse = $apiResponse;
    }

    #[Route('/books', name: 'api_books_list', methods: ['GET'])]
    public function getBooks(Request $request): Response
    {
        try {
            $books = $this->bookService->findAllAvailableBooks($request);

            return $this->apiResponse->buildJsonResponseFromArray($books, BookConfig::RESOURCE_NAME, $request);
        } catch (ConflictHttpException $exception) {
            $this->apiResponse->setStatusCode(Response::HTTP_BAD_REQUEST);

            return $this->apiResponse->buildErrorResponse($exception->getMessage());
        } catch (\Exception | \Throwable $exception) {
            $this->apiResponse->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            return $this->apiResponse->buildErrorResponse($exception->getMessage());
        }
    }

    #[Route('/book', name: 'api_add_book', methods: ['POST'])]
    public function addBook(BookRequestTransfer $bookRequestTransfer): Response
    {
        try {
            $bookTransfer = $this->bookService->addBook($bookRequestTransfer);

            return $this->apiResponse->buildJsonResponse($bookTransfer, BookConfig::RESOURCE_NAME);
        } catch (ConflictHttpException $exception) {
            $this->apiResponse->setStatusCode(Response::HTTP_BAD_REQUEST);

            return $this->apiResponse->buildErrorResponse($exception->getMessage());
        } catch (\Exception | \Throwable $exception) {
            $this->apiResponse->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            return $this->apiResponse->buildErrorResponse($exception->getMessage());
        }
    }

    #[Route('/book/{uuid}', name: 'api_edit_book', methods: ['PATCH'])]
    public function editBook(Request $request, EditBookRequestTransfer $editBookRequestTransfer): Response
    {
        try {
            $bookTransfer = $this->bookService->editBook($request->get('uuid'), $editBookRequestTransfer);

            return $this->apiResponse->buildJsonResponse($bookTransfer, BookConfig::RESOURCE_NAME);
        } catch (ConflictHttpException $exception) {
            $this->apiResponse->setStatusCode(Response::HTTP_BAD_REQUEST);

            return $this->apiResponse->buildErrorResponse($exception->getMessage());
        } catch (\Exception | \Throwable $exception) {
            $this->apiResponse->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            return $this->apiResponse->buildErrorResponse($exception->getMessage());
        }
    }

    #[Route('/book/{uuid}', name: 'api_edit_book', methods: ['DELETE'])]
    public function deleteBook(Request $request): Response
    {
        try {
            $this->bookService->deleteBook($request->get('uuid'));

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (ConflictHttpException $exception) {
            $this->apiResponse->setStatusCode(Response::HTTP_BAD_REQUEST);

            return $this->apiResponse->buildErrorResponse($exception->getMessage());
        } catch (\Exception | \Throwable $exception) {
            $this->apiResponse->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            return $this->apiResponse->buildErrorResponse($exception->getMessage());
        }
    }

}