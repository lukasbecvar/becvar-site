<?php

namespace App\Manager;

use App\Entity\Todo;
use App\Util\SecurityUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AuthManager
 *
 * Todos manager provides methods for admin todo manager
 *
 * @package App\Manager
*/
class TodosManager
{
    private LogManager $logManager;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        LogManager $logManager,
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    /**
     * Gets todos based on the specified status.
     *
     * @param array<string, mixed> $searchArray
     *
     * @return array<int, array{id: int, text: string}>|null
     */
    public function getTodos(array $searchArray): ?array
    {
        $repository = $this->entityManager->getRepository(Todo::class);

        // check if repository found
        if ($repository !== null) {
            try {
                $todoData = [];

                // get todos list
                $todos = $repository->findBy($searchArray);

                foreach ($todos as $todo) {
                    // decrypt todo text
                    $todoText = $this->securityUtil->decryptAes($todo->getText());

                    // check if message data is decrypted
                    if ($todoText == null) {
                        $this->errorManager->handleError('Error to decrypt aes todo data', 500);
                    }

                    // build todo item
                    $todoItem = [
                        'id' => $todo->getId(),
                        'text' => $todoText
                    ];

                    array_push($todoData, $todoItem);
                }

                return array_reverse($todoData);
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to get todos: ' . $e->getMessage(), 500);
                return null;
            }
        }
    }

    /**
     * Adds a new todo item.
     *
     * @param string $text The text for the new todo item.
     *
     * @return void
     */
    public function addTodo(string $text): void
    {
        // create todo entity
        $todo = new Todo();

        // get current date & time
        $date = date('d.m.Y H:i:s');

        // get username
        $username = $this->authManager->getUsername();

        // encrypt todo
        $text = $this->securityUtil->encryptAes($text);

        // set todo data
        $todo->setText($text);
        $todo->setStatus('non-completed');
        $todo->setAddedTime($date);
        $todo->setCompletedTime('non-completed');
        $todo->setAddedBy($username);
        $todo->setClosedBy('non-closed');

        // save new todo
        try {
            $this->entityManager->persist($todo);
            $this->entityManager->flush();

            // log event
            $this->logManager->log('todo-manager', 'user: ' . $username . ' add new todo');
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to add todo: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Edit a todo item.
     *
     * @param string $id The ID of the todo item to be edited.
     * @param string $newTodoText The new text for the todo item.
     *
     * @return void
     */
    public function editTodo(string $id, string $newTodoText): void
    {
        // get todo repository
        $todo = $this->entityManager->getRepository(Todo::class)->find($id);

        // encrypt todo text
        $newTodoText = $this->securityUtil->encryptAes($newTodoText);

        // set new todo text
        $todo->setText($newTodoText);

        // update todo
        try {
            $this->entityManager->flush();

            // log event
            $this->logManager->log('todo-manager', 'user: ' . $this->authManager->getUsername() . ' edit todo: ' . $id);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to flush update todo: ' . $id . ', error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Closes a todo by updating its status, completion time, and the user who closed it.
     *
     * @param int $id
     *
     * @return void
     */
    public function closeTodo(int $id): void
    {
        // get current date
        $date = date('d.m.Y H:i:s');

        // get todo repository
        $todo = $this->entityManager->getRepository(Todo::class)->find($id);

        // get username
        $username = $this->authManager->getUsername();

        // check if todo found
        if ($todo !== null) {
            // close todo
            $todo->setStatus('completed');

            // update closed time
            $todo->setCompletedTime($date);
            $todo->setClosedBy($username);

            try {
                // update todo
                $this->entityManager->flush();

                // log event
                $this->logManager->log('todo-manager', 'user: ' . $this->authManager->getUsername() . ' close todo: ' . $id);
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to close todo: ' . $id . ', ' . $e->getMessage(), 500);
            }
        }
    }
}
