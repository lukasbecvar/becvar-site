<?php

namespace App\Manager;

use App\Entity\Todo;
use App\Util\SecurityUtil;
use Doctrine\ORM\EntityManagerInterface;

/*
    Todos manager provides methods for admin todo manager
*/

class TodosManager
{
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SecurityUtil $securityUtil,
        ErrorManager $errorManager, 
        EntityManagerInterface $entityManager
    ) {
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

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
                        'text' => $this->securityUtil->decrypt_aes($todo->getText()),
                        'added_time' => $todo->getAddedTime(),
                        'completed_time' => $todo->getCompletedTime(),
                        'status' => $todo->getStatus()
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

        // check if todo found
        if ($todo !== null) {
            // close todo
            $todo->setStatus('completed');

            // update closed time
            $todo->setCompletedTime($date);

            try {
                // update todo
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to close todo: '.$id.', '.$e->getMessage(), 500);
            }
        }  
    }
}
