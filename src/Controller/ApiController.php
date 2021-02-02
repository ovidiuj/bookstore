<?php


namespace App\Controller;


use App\Api\Response\ApiResponseInterface;
use App\Config\BookConfig;
use App\Services\BookServiceInterface;
use App\TransferObjects\Request\Book\BookRequestTransfer;
use App\TransferObjects\Request\Book\EditBookRequestTransfer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
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
    /**
     * @OA\Get(
     *     path="/books",
     *     summary="Search for books",
     *     tags={"Book"},
     *     @OA\Parameter(
     *          name="q",
     *          in="path",
     *          required=false,
     *          example="the",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="page[offset]",
     *          in="path",
     *          required=false,
     *          example="0",
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Parameter(name="page[limit]",
     *          in="path",
     *          required=false,
     *          example="10",
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Parameter(name="sort",
     *          in="path",
     *          required=false,
     *          example="title-asc",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              type="array",
     *               @OA\Items(ref="#/components/schemas/BooksList")
     *           ),
     *      ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     ),
     * )
     */
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
    /**
     * @OA\Post(
     *     path="/book",
     *     summary="Add a new book",
     *     tags={"Book"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/BookRequestTransfer"),
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/MandantResponseData"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/ErrorTransfer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/ValidationErrorTransfer"),
     *         )
     *     ),
     * )
     */
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
    /**
     * @OA\Patch(
     *     path="/book/{uuid}",
     *     summary="Update an existing book",
     *     tags={"Book"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/BookRequestTransfer"),
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/BookResponseTransfer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/ErrorTransfer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/ValidationErrorTransfer"),
     *         )
     *     )
     * )
     */
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

    #[Route('/book/{uuid}', name: 'api_delete_book', methods: ['DELETE'])]
    /**
     * @OA\Delete(
     *     path="/book/{uuid}",
     *     summary="Delete book",
     *     tags={"Book"},
     *
     *     @OA\Response(
     *         response=204,
     *         description="No Content",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/ErrorTransfer"),
     *         )
     *     ),
     * )
     */
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