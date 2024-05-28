<?php

namespace App\Controller\Admin;

use App\Entity\Todo;
use App\Util\SiteUtil;
use App\Manager\AuthManager;
use App\Form\NewTodoFormType;
use App\Manager\ErrorManager;
use App\Manager\TodosManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class TodoManagerController
 *
 * Todo manager controller provides view/add/delete todos.
 *
 * @package App\Controller\Admin
 */
class TodoManagerController extends AbstractController
{
    private SiteUtil $siteUtil;
    private AuthManager $authManager;
    private TodosManager $todosManager;
    private ErrorManager $errorManager;

    public function __construct(
        SiteUtil $siteUtil,
        AuthManager $authManager,
        TodosManager $todosManager,
        ErrorManager $errorManager,
    ) {
        $this->siteUtil = $siteUtil;
        $this->authManager = $authManager;
        $this->todosManager = $todosManager;
        $this->errorManager = $errorManager;
    }

    /**
     * Display the table of non-completed todos and handle new todo creation.
     *
     * @param Request $request object representing the HTTP request.
     *
     * @return Response object representing the HTTP response.
     */
    #[Route('/admin/todos', methods: ['GET', 'POST'], name: 'admin_todos')]
    public function todosTable(Request $request): Response
    {
        // init todo entity
        $todo = new Todo();

        // get todos data
        $todos = $this->todosManager->getTodos(['status' => 'non-completed']);

        // create register form
        $form = $this->createForm(NewTodoFormType::class, $todo);
        $form->handleRequest($request);

        // check form if submited
        if ($form->isSubmitted() && $form->isValid()) {
            // get text
            $text = $form->get('text')->getData();

            // check if text is empty
            if (!empty($text)) {
                // save new todo
                $this->todosManager->addTodo($text);
                return $this->redirectToRoute('admin_todos');
            }
        }

        // render todo page view
        return $this->render('admin/todo-manager.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // editor data
            'todo_editor' => false,
            'completed_list' => false,
            'todos_data' => $todos,
            'todos_count' => count($todos),
            'new_todo_form' => $form
        ]);
    }

    /**
     * Display the table of completed todos.
     *
     * @return Response object representing the HTTP response.
     */
    #[Route('/admin/todos/completed', methods: ['GET'], name: 'admin_todos_completed')]
    public function completedTodosTable(): Response
    {
        // get todos data
        $todos = $this->todosManager->getTodos(['status' => 'completed']);

        // render completed todo page view
        return $this->render('admin/todo-manager.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // editor data
            'todo_editor' => false,
            'completed_list' => true,
            'todos_count' => count($todos),
            'todos_data' => $todos,
        ]);
    }

    /**
     * Edit a todo.
     *
     * @param Request $request object representing the HTTP request.
     *
     * @return Response object representing the HTTP response.
     */
    #[Route('/admin/todos/edit', methods: ['GET', 'POST'], name: 'admin_todo_edit')]
    public function editTodo(Request $request): Response
    {
        // init default data
        $errorMsg = null;

        // get query parameter
        $id = $this->siteUtil->getQueryString('id', $request);

        // get todo data
        $todoData = $this->todosManager->getTodos(['id' => $id]);

        // check if request is post
        if ($request->isMethod('POST')) {
            // get submit button value
            $submited = $request->get('submitTodoEdit');

            // check if edit form submited
            if (isset($submited)) {
                // get new todo text
                $newTodoText = $request->get('new-todo-text');

                // check if new todo text is not empty
                if (empty($newTodoText)) {
                    $errorMsg = 'Please add todo text!';
                } else {
                    // save change
                    $this->todosManager->editTodo($id, $newTodoText);

                    // return back to todo table
                    return $this->redirectToRoute('admin_todos');
                }
            }
        }

        // check if todo not found
        if (empty($todoData)) {
            return $this->errorManager->handleError('error todo: ' . $id . ' not found', 404);
        }

        // return todo editor view
        return $this->render('admin/todo-manager.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // editor data
            'todo_editor' => true,
            'todo_edited_data' => $todoData,
            'error_msg' => $errorMsg
        ]);
    }

    /**
     * Close a todo.
     *
     * @param Request $request object representing the HTTP request.
     *
     * @return Response object representing the HTTP response.
     */
    #[Route('/admin/todos/close', methods: ['GET'], name: 'admin_todo_close')]
    public function closeTodo(Request $request): Response
    {
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
    }
}
