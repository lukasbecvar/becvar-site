<?php

namespace App\Manager;

use App\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;

/*
    Todos manager provides methods for admin todo manager
*/

class TodosManager
{
    private $errorManager;
    private $entityManager;

    public function __construct(
        ErrorManager $errorManager, 
        EntityManagerInterface $entityManager
    ) {
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
                return array_reverse($todos);
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to get todos: '.$e->getMessage(), 500);
            }
        } else {
            return [];
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
