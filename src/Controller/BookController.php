<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Services\HttpClientService\ClientBookMapperInterface;
use App\Services\HttpClientService\HttpClientServiceTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    use HttpClientServiceTrait;

    private string $apiUrl = '/api/v1/';
    private string $apiTokenUrl = '/api/login_check';

    #[Route('/', name: 'book_index', methods: ['GET'])]
    public function index(): Response
    {
        $books = $this->apiRequest(
            'GET',
            $this->getParameter('api.host') . $this->apiUrl . 'books',
            $this->getUserToken()
        );

        if(!isset($books['data'])) {
            $books['data'] = [];
        }
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/new', name: 'book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ClientBookMapperInterface $clientBookMapper): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $book instanceof Book) {

            $bookParams = $clientBookMapper->getBookParams($book);
            $book = $this->apiRequest(
                'POST',
                $this->getParameter('api.host') . $this->apiUrl . 'book',
                $this->getUserToken(),
                $bookParams
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'book_show', methods: ['GET'])]
    public function show(Request $request): Response
    {
        $book = $this->apiRequest(
            'GET',
            $this->getParameter('api.host') . $this->apiUrl . 'book/' . $request->get('id'),
            $this->getUserToken()
        );
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ClientBookMapperInterface $clientBookMapper): Response
    {
        $bookParams = $this->apiRequest(
            'GET',
            $this->getParameter('api.host') . $this->apiUrl . 'book/' . $request->get('id'),
            $this->getUserToken()
        );

        $book = $clientBookMapper->createBookEntity($bookParams);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $bookParams = $clientBookMapper->getBookParams($book);
            $book = $this->apiRequest(
                'PATCH',
                $this->getParameter('api.host') . $this->apiUrl . 'book/' . $request->get('id') . '/edit',
                $this->getUserToken(),
                $bookParams
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $bookParams,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'book_delete', methods: ['DELETE'])]
    public function delete(Request $request): Response
    {

        $res = $this->apiRequest(
            'DELETE',
            $this->getParameter('api.host') . $this->apiUrl . 'book/' . $request->get('id') . '/remove',
            $this->getUserToken()
        );

        return $this->redirectToRoute('book_index');
    }


    private function getUserToken(): string
    {
        return $this->getToken(
            $this->getParameter('api.host') . $this->apiTokenUrl,
            $this->getParameter('api.username'),
            $this->getParameter('api.password')
        );
    }

}
