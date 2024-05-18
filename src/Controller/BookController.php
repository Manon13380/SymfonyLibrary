<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\GoogleBookApi;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class BookController extends AbstractController
{
    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, GoogleBookApi $googleBookApi): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $googleBookApi->getBooksByAuthor($book);
            $bookApi = $data['items'][0]['volumeInfo'];
            if (isset($bookApi['title'])) {
                $book->setTitle($bookApi['title']);
            }
            if (isset($bookApi['authors'][0])) {
                $book->setAuthor($bookApi['authors'][0]);
            }
            if (isset($bookApi['publishedDate'])) {
                $book->setPublication($bookApi['publishedDate']);
            }
            if (isset($bookApi['industryIdentifiers'][0]['identifier'])) {
                $book->setISBN($bookApi['industryIdentifiers'][0]['identifier']);
            }
            if (isset($bookApi['imageLinks']['smallThumbnail'])) {
                $book->setImage($bookApi['imageLinks']['smallThumbnail']);
            }
            if (isset($bookApi['description'])) {
                $book->setDescription($bookApi['description']);
            }
            if (isset($bookApi['categories'])) {
                $book->setGenre($bookApi['categories']);
            }
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/book/{id}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/book/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/book/{id}', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
