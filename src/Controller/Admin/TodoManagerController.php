<?php

namespace App\Controller\Admin;

use App\Entity\Todo;
use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use App\Manager\AuthManager;
use App\Form\NewTodoFormType;
use App\Manager\ErrorManager;
use App\Manager\TodosManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Todo manager controller provides view/add/delete todos.
 */
class TodoManagerController extends AbstractController
{
    /** * @var SiteUtil */
    private SiteUtil $siteUtil;

    /** * @var AuthManager */
    private AuthManager $authManager;

    /** * @var TodosManager */
    private TodosManager $todosManager;

    /** * @var SecurityUtil */
    private SecurityUtil $securityUtil;

    /** * @var ErrorManager */
    private ErrorManager $errorManager;

    /**
     * TodoManagerController constructor.
     *
     * @param SiteUtil      $siteUtil
     * @param AuthManager   $authManager
     * @param TodosManager  $todosManager
     * @param SecurityUtil  $securityUtil
     * @param ErrorManager  $errorManager
     */
    public function __construct(
        SiteUtil $siteUtil,
        AuthManager $authManager,
        TodosManager $todosManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
    ) {
        $this->siteUtil = $siteUtil;
        $this->authManager = $authManager;
        $this->todosManager = $todosManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
    }

    /**
     * Display the table of non-completed todos and handle new todo creation.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/todos', methods: ['GET', 'POST'], name: 'admin_todos')]
    public function todosTable(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get todos data
            $todos = $this->todosManager->getTodos(['status' => 'non-completed']);

            // create todo entity
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

                    // escape text
                    $text = $this->securityUtil->escapeString($text);

                    // save new todo
                    $this->todosManager->addTodo($text);

                    return $this->redirectToRoute('admin_todos');
                }
            }

            return $this->render('admin/todo-manager.html.twig', [
                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                'todo_editor' => false,
                'completed_list' => false,
                'todos_data' => $todos,
                'todos_count' => count($todos),
                'new_todo_form' => $form
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    /**
     * Display the table of completed todos.
     *
     * @return Response
     */
    #[Route('/admin/todos/completed', methods: ['GET'], name: 'admin_todos_completed')]
    public function completedTodosTable(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get todos data
            $todos = $this->todosManager->getTodos(['status' => 'completed']);

            return $this->render('admin/todo-manager.html.twig', [
                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                'todo_editor' => false,
                'completed_list' => true,
                'todos_count' => count($todos),
                'todos_data' => $todos,
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
    
    /**
     * Edit a todo.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/todos/edit', methods: ['GET', 'POST'], name: 'admin_todo_edit')]
    public function editTodo(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
        
            // default error message
            $error_msg = null;

            // get query parameter
            $id = $this->siteUtil->getQueryString('id', $request);

            // get todo data
            $todo_data = $this->todosManager->getTodos(['id' => $id]);

            // check if request is post
            if ($request->isMethod('POST')) {
                // get submit button value
                $submited = $request->get('submitTodoEdit');
                
                // check if edit form submited
                if (isset($submited)) {

                    // get new todo text
                    $new_todo_text = $request->get('new-todo-text');

                    // check if new todo text is not empty
                    if (empty($new_todo_text)) {
                        $error_msg = 'Please add todo text!';
                    } else {
                        
                        // escape todo text
                        $new_todo_text = $this->securityUtil->escapeString($new_todo_text);

                        // save change
                        $this->todosManager->editTodo($id, $new_todo_text);

                        // return back to todo table
                        return $this->redirectToRoute('admin_todos');
                    }
                }
            }

            // check if todo not found
            if (empty($todo_data)) {
                return $this->errorManager->handleError('error todo: '.$id.' not found', 404);
            } else {
                return $this->render('admin/todo-manager.html.twig', [
                    // user data
                    'user_name' => $this->authManager->getUsername(),
                    'user_role' => $this->authManager->getUserRole(),
                    'user_pic' => $this->authManager->getUserProfilePic(),
    
                    'todo_editor' => true,
                    'todo_edited_data' => $todo_data,
                    'error_msg' => $error_msg
                ]);
            }
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    /**
     * Close a todo.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/todos/close', methods: ['GET'], name: 'admin_todo_close')]
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
