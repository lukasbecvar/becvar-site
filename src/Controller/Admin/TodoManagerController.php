<?php

namespace App\Controller\Admin;

use App\Entity\Todo;
use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use App\Manager\AuthManager;
use App\Form\NewTodoFormType;
use App\Manager\ErrorManager;
use App\Manager\TodosManager;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Todo manager controller provides view/add/delete todos
*/

class TodoManagerController extends AbstractController
{
    private SiteUtil $siteUtil;
    private AuthManager $authManager;
    private TodosManager $todosManager;
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(
        SiteUtil $siteUtil,
        AuthManager $authManager,
        TodosManager $todosManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->authManager = $authManager;
        $this->todosManager = $todosManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/admin/todos', name: 'admin_todos')]
    public function todosTable(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get todo category
            $category = $this->siteUtil->getQueryString('category', $request);

            // check if category is missing
            if ($category == '1') {
                return $this->errorManager->handleError('todo category error: error query missing', 500);
            }

            // get todos data
            $todos = $this->todosManager->getTodos('non-completed',  $category);

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

                    // get username
                    $username = $this->authManager->getUsername();

                    // escape text
                    $text = $this->securityUtil->escapeString($text);
                    
                    // encrypt todo text
                    $text = $this->securityUtil->encrypt_aes($text);

                    // set todo data
                    $todo->setText($text);
                    $todo->setCategory($category);
                    $todo->setStatus('non-completed');
                    $todo->setAddedTime($date);
                    $todo->setCompletedTime('non-completed');
                    $todo->setAddedBy($username);
                    $todo->setEditedBy('non-edited');
                    $todo->setClosedBy('non-closed');

                    // save new todo
                    try {
                        $this->entityManager->persist($todo);
                        $this->entityManager->flush();
                    } catch (\Exception $e) {
                        return $this->errorManager->handleError('error to add todo: '.$e->getMessage(), 500);
                    }

                    return $this->redirectToRoute('admin_todos', [
                        'category' => $category
                    ]);
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

                'completed_list' => false,
                'todos_data' => $todos,
                'todos_count' => count($todos),
                'new_todo_form' => $form
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }


    #[Route('/admin/todos/completed', name: 'admin_todos_completed')]
    public function completedTodosTable(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get todos data
            $todos = $this->todosManager->getTodos('completed');

            return $this->render('admin/todo-manager.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                'completed_list' => true,
                'todos_count' => count($todos),
                'todos_data' => $todos,
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/todos/close', name: 'admin_todo_close')]
    public function closeTodo(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get todo category
            $category = $this->siteUtil->getQueryString('category', $request);

            // get todo id
            $id = intval($this->siteUtil->getQueryString('id', $request));

            // close todo
            $this->todosManager->closeTodo($id);

            // redirect to todo page
            return $this->redirectToRoute('admin_todos', [
                'category' => $category
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
