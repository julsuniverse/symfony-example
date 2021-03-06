<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Signup\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\SignUpConfirmTokenizer;
use App\Model\User\Service\SignUpConfirmTokenSender;
use App\Model\User\Service\PasswordsHasher;

class Handler
{
    /**
     * @var UserRepository
     */
    private UserRepository $users;
    /**
     * @var PasswordsHasher
     */
    private PasswordsHasher $hasher;
    /**
     * @var Flusher
     */
    private Flusher $flusher;
    /**
     * @var SignUpConfirmTokenizer
     */
    private SignUpConfirmTokenizer $tokenizer;
    /**
     * @var SignUpConfirmTokenSender
     */
    private SignUpConfirmTokenSender $sender;

    public function __construct(
        UserRepository $users,
        PasswordsHasher $hasher,
        SignUpConfirmTokenizer $tokenizer,
        SignUpConfirmTokenSender $sender,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);
        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Name(
                $command->firstName,
                $command->lastName
            ),
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate()
        );

        $this->users->add($user);
        $this->sender->send($email, $token);
        $this->flusher->flush();
    }
}