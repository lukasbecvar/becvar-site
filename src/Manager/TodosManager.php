<?php

namespace App\Manager;

use App\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;

/*
    Todos manager provides methods for admin todo manager
*/

class TodosManager
{
    private AuthManager $authManager;
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AuthManager $authManager,
        ErrorManager $errorManager, 
        EntityManagerInterface $entityManager
    ) {
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    public function getTodos(string $status, string $category = '_all_todos_1189848'): ?array
    {
        $repository = $this->entityManager->getRepository(Todo::class);

        // check if repository found
        if ($repository !== null) {
            try {
                if ($category == '_all_todos_1189848') {
                    $todos = $repository->findBy(['status' => $status]);
                } else {
                    $todos = $repository->findBy(['status' => $status, 'category' => $category]);
                }
                
                $todo_data = [];

                foreach ($todos as $todo) {
                    $todo_item = [
                        'id' => $todo->getId(),
                        'text' => $todo->getText(),
                        'category' => $todo->getCategory()
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
