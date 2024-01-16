<?php

namespace App\Manager;

use App\Entity\Todo;
use App\Util\SecurityUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Todos manager provides methods for admin todo manager
*/
class TodosManager
{
    /** @var LogManager */
    private LogManager $logManager;

    /** @var AuthManager */
    private AuthManager $authManager;
    
    /** @var SecurityUtil */
    private SecurityUtil $securityUtil;
    
    /** @var ErrorManager */
    private ErrorManager $errorManager;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /**
     * TodosManager constructor.
     *
     * @param LogManager             $logManager
     * @param AuthManager            $authManager
     * @param SecurityUtil           $securityUtil
     * @param ErrorManager           $errorManager
     * @param EntityManagerInterface $entityManager
     */
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
     * @param array $search_array
     *
     * @return array|null
     */
    public function getTodos(array $search_array): ?array
    {
        $repository = $this->entityManager->getRepository(Todo::class);

        // check if repository found
        if ($repository !== null) {
            try {
                $todo_data = [];
                $todos = $repository->findBy($search_array);

                foreach ($todos as $todo) {
                    $todo_item = [
                        'id' => $todo->getId(),
                        'text' => $this->securityUtil->decryptAes($todo->getText())
                    ];
                    array_push($todo_data, $todo_item);
                }
                
                return array_reverse($todo_data);
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to get todos: '.$e->getMessage(), 500);
                return null;
            }
        }
    }

    /**
     * Adds a new todo item.
     *
     * @param string $text  The text for the new todo item.
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
            $this->logManager->log('todo-manager', 'user: '.$username.' add new todo');

        } catch (\Exception $e) {
            $this->errorManager->handleError('error to add todo: '.$e->getMessage(), 500);
        }
    }

    /**
     * Edit a todo item.
     *
     * @param string $id             The ID of the todo item to be edited.
     * @param string $new_todo_text  The new text for the todo item.
     *
     * @return void
     */
    public function editTodo(string $id, string $new_todo_text): void
    {
        // get todo repository
        $todo = $this->entityManager->getRepository(Todo::class)->find($id);

        // encrypt todo text
        $new_todo_text = $this->securityUtil->encryptAes($new_todo_text);

        // set new todo text
        $todo->setText($new_todo_text);

        // update todo
        try {
            $this->entityManager->flush();

            // log event
            $this->logManager->log('todo-manager', 'user: '.$this->authManager->getUsername().' edit todo: '.$id);

        } catch (\Exception $e) {
            $this->errorManager->handleError('error to flush update todo: '.$id.', error: '.$e->getMessage(), 500);
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
                $this->logManager->log('todo-manager', 'user: '.$this->authManager->getUsername().' close todo: '.$id);
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to close todo: '.$id.', '.$e->getMessage(), 500);
            }
        }  
    }
}
