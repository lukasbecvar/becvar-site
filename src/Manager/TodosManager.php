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
     * @param AuthManager            $authManager
     * @param SecurityUtil           $securityUtil
     * @param ErrorManager           $errorManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager, 
        EntityManagerInterface $entityManager
    ) {
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    /**
     * Gets todos based on the specified status.
     *
     * @param string $status
     *
     * @return array|null
     */
    public function getTodos(string $status): ?array
    {
        $repository = $this->entityManager->getRepository(Todo::class);

        // check if repository found
        if ($repository !== null) {
            try {
                $todos = $repository->findBy(['status' => $status]);
                
                $todo_data = [];

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
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to close todo: '.$id.', '.$e->getMessage(), 500);
            }
        }  
    }
}
