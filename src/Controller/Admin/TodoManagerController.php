<?php

namespace App\Controller\Admin;

use App\Entity\Todo;
use App\Form\NewTodoFormType;
use App\Manager\AuthManager;
use App\Manager\DatabaseManager;
use App\Manager\ErrorManager;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/*
    Todo manager controller provides view/add/delete todos
*/

class TodoManagerController extends AbstractController
{
    private $authManager;
    private $errorManager;
    private $securityUtil;
    private $entityManager;
    private $databaseManager;
    private $visitorInfoUtil;

    public function __construct(
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        DatabaseManager $databaseManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->databaseManager = $databaseManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/admin/todos', name: 'admin_todos')]
    public function todos(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get todos data
            $todos = $this->databaseManager->getTodos('non-completed');

            // create user entity
            $todo = new Todo();

            // create register form
            $form = $this->createForm(NewTodoFormType::class, $todo);

            // processing an HTTP request
            $form->handleRequest($request);

            // check form if submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get text
                $text = $form->get('text')->getData();

                // check if text is empty
                if (!empty($text)) {
                        
                    // get current date
                    $date = date('d.m.Y H:i:s');

                    // escape text
                    $text = $this->securityUtil->escapeString($text);
                    
                    // set todo data
                    $todo->setText($text);
                    $todo->setStatus('non-completed');
                    $todo->setAddedTime($date);
                    $todo->setCompletedTime('non-completed');

                    // save new todo
                    try {
                        $this->entityManager->persist($todo);
                        $this->entityManager->flush();
                    } catch (\Exception $e) {
                        $this->errorManager->handleError('error to add todo: '.$e->getMessage(), 500);
                    }

                    return $this->redirectToRoute('admin_todos');
                }
            }

            return $this->render('admin/todo-manager.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                'todos_data' => $todos,
                'new_todo_form' => $form
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/todos/close/{id}', name: 'admin_todo_close')]
    public function close(int $id): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // close todo
            $this->databaseManager->closeTodo($id);

            // redirect to todo page
            return $this->redirectToRoute('admin_todos');
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
