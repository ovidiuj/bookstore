<?php


namespace App\TransferObjects\Request\Book;


use App\Entity\Book;
use App\TransferObjects\Request\RequestTransferInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BookRequestTransfer implements RequestTransferInterface
{
    /**
     * @Assert\Sequentially({
     * @Assert\NotBlank(),
     * @Assert\NotNull(),
     * @Assert\Type(type="string")
     * })
     */
    private $title;

    /**
     * @Assert\Sequentially({
     * @Assert\NotBlank(),
     * @Assert\NotNull(),
     * @Assert\Type(type="string")
     * })
     */
    private $cover;

    /**
     * @Assert\Sequentially({
     * @Assert\NotBlank(),
     * @Assert\NotNull(),
     * @Assert\Type(type="string")
     * })
     */
    private $description;

    /**
     * @Assert\Sequentially({
     * @Assert\NotBlank(),
     * @Assert\NotNull(),
     * @Assert\Type(type="string")
     * })
     */
    private $author;

    /**
     * @Assert\Sequentially({
     * @Assert\Choice({"public", "not-public"})
     * })
     */
    private $status = Book::STATUS_PUBLIC;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCover(): string
    {
        return $this->cover;
    }

    public function setCover(string $cover): void
    {
        $this->cover = $cover;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}