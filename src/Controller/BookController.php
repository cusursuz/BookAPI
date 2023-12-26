<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Dto\CreateBookDto;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class BookController extends AbstractFOSRestController
{
    public function __construct(private BookRepository $bookRepository)
    {
    }

    #[Rest\Get('/api/books/', name: 'get_books')]
    #[OA\Parameter(in: "query", name: "page", required: false)]
    #[OA\Parameter(in: "query", name: "format", required: false)]
    #[OA\Get(description: "The list of books")]
    #[OA\Response(response: 200, description: 'Get list of books')]
    public function getBooks(Request $request, SerializerInterface $serializer)
    {
        $page = 1;
        if ($request->get("page")) {
            $page = $request->get("page");
        }
        $books = $this->bookRepository->getPaginated($page);

        $format = $request->get('format', 'json');
        if ($format === 'json') {
            return $this->json($books, Response::HTTP_OK);
        } else if ($format === 'xml') {
            $xmlContent = $serializer->serialize($books, 'xml');
            return new Response($xmlContent, Response::HTTP_OK);
        } else {
            return new JsonResponse([
                'message' => 'Unknown data format',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Rest\Get('/api/books/{id}', name: 'get_book')]
    #[OA\Parameter(in: "path", name: "id", required: true)]
    #[OA\Schema(type: "integer")]
    #[OA\Get(description: "Get book by id")]
    #[OA\Examples(example: "int", value: "100000", summary: "An int value.")]
    #[OA\Response(response: 200, description: "OK")]
    public function getBook(Request $request, SerializerInterface $serializer, int $id)
    {
        $book = $this->bookRepository->find($id);

        $format = $request->get('format', 'json');
        if ($format === 'json') {
            return $this->json($book, Response::HTTP_OK);
        } else if ($format === 'xml') {
            $xmlContent = $serializer->serialize($book, 'xml');
            return new Response($xmlContent, Response::HTTP_OK);
        } else {
            return new JsonResponse([
                'message' => 'Unknown data format',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Rest\Post('/api/books', name: 'create_book')]
    #[OA\Get(description: "Create new book")]
    #[OA\Parameter(name: "title", schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: "author", required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: "description", required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: "price", required: false, schema: new OA\Schema(type: 'float'))]
    #[OA\Response(response: 200, description: "OK")]
    public function createBook(#[MapRequestPayload()] CreateBookDto $createBook, EntityManagerInterface $entityManager): JsonResponse
    {
        $book = new Book;
        $book->setTitle($createBook->title);
        $book->setAuthor($createBook->author);
        $book->setDescription($createBook->description);
        $book->setPrice($createBook->price);

        try {
            $entityManager->persist($book);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Book created successfully!',
                'data' => [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                    'author' => $book->getAuthor(),
                    'description' => $book->getDescription(),
                    'price' => $book->getPrice(),
                ],
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Error creating book: ' . $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Rest\Put('/api/books/{id}', name: 'update_book')]
    #[OA\Get(description: "Update book by id")]
    #[OA\Parameter(in: "path", name: "id", schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: "title", schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: "author", required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: "description", required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: "price", required: false, schema: new OA\Schema(type: 'float'))]
    #[OA\Response(response: 200, description: "OK")]
    public function updateBook(#[MapRequestPayload()] CreateBookDto $createBook, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            return new JsonResponse([
                'message' => 'Book not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $book->setTitle($createBook->title);
        $book->setAuthor($createBook->author);
        $book->setDescription($createBook->description);
        $book->setPrice($createBook->price);
        try {
            $entityManager->persist($book);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Book updated successfully',
                'data' => [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                    'author' => $book->getAuthor(),
                    'description' => $book->getDescription(),
                    'price' => $book->getPrice(),
                ]
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Error updating book: ' . $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Rest\Delete('/api/books/{id}', name: 'delete_book')]
    #[OA\Get(description: "Delete book by id")]
    #[OA\Parameter(in: "path", name: "id", required: true)]
    #[OA\Response(response: 200, description: "OK")]
    public function deleteBook(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            return new JsonResponse([
                'message' => 'Book not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $entityManager->remove($book);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Book deleted successfully',
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Error deleting book: ' . $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Rest\Get('/api/books/get/by-author', name: 'get_books_by_author')]
    #[OA\Parameter(in: "query", name: "author", required: false)]
    #[OA\Get(description: "The list of books by author")]
    #[OA\Response(response: 200, description: "OK")]
    public function getByAuthor(Request $request, SerializerInterface $serializer)
    {
        $query = [];
        $author = $request->get("author");
        if ($author) {
            $query['author'] = $author;
        }

        $format = $request->get('format', 'json');
        $books = $this->bookRepository->findBy($query, [], 1000);

        if ($format === 'json') {
            return $this->json($books, Response::HTTP_OK);
        } else if ($format === 'xml') {
            $xmlContent = $serializer->serialize($books, 'xml');
            return new Response($xmlContent, Response::HTTP_OK);
        } else {
            return new JsonResponse([
                'message' => 'Unknown data format',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Rest\Get('/api/books/get/catalog', name: 'get_books_catalog')]
    #[OA\Get(description: "Get the books catalog")]
    #[OA\Response(response: 200, description: "OK")]
    public function getCatalog()
    {
        $books = $this->bookRepository->createQueryBuilder('b')
            ->select('b.id', 'b.title', 'b.price')
            ->getQuery()
            ->toIterable();

        $tempFilePath = tempnam(sys_get_temp_dir(), 'catalog');
        $tempFile = fopen($tempFilePath, 'w');

        foreach ($books as $book) {
            fwrite($tempFile, sprintf("%s, %s\n", $book['title'], $book['price']));
        }

        fclose($tempFile);
        return $this->file($tempFilePath, 'catalog.csv');
    }
}
