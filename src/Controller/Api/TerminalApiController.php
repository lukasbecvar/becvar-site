<?php

namespace App\Controller\Api;

use App\Util\JsonUtil;
use App\Util\SecurityUtil;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use App\Manager\SessionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    This controller provides API function: execute terminal commands
*/

class TerminalApiController extends AbstractController
{
    private JsonUtil $jsonUtil;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private SessionManager $sessionManager;

    public function __construct(
        JsonUtil $jsonUtil,
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        SessionManager $sessionManager
    ) {
        $this->jsonUtil = $jsonUtil;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->sessionManager = $sessionManager;
    }

    #[Route('/api/system/terminal', name: 'api_terminal')]
    public function terminalAction(Request $request)
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            // start session (for saving working path)
            $this->sessionManager->startSession();

            // set default working dir
            if ($this->sessionManager->checkSession('terminal-dir')) {
                $currentDir = $this->sessionManager->getSessionValue('terminal-dir');
                if (!file_exists($currentDir)) {
                    $currentDir = '/';
                }
                chdir($currentDir);
            } else {
                chdir('/');
            }

            // check if request is post
            if ($request->isMethod('POST')) {
                
                // get command
                $command = $request->request->get('command');

                // check if command empty
                if (!empty($command)) {

                    // escape command
                    $command = $this->securityUtil->escapeString($command);

                    // check if blocked commands config found
                    if (file_exists((__DIR__ . '/../../../terminal-blocked-commands.json'))) {
                        // get blocked command list
                        try {
                            $blockedCommands = $this->jsonUtil->getJson(__DIR__ . '/../../../terminal-blocked-commands.json');
                        } catch (\Exception $e) {
                            return new Response($e->getMessage());
                        }

                        // check if command is blocked
                        foreach ($blockedCommands as $blockedCommand) {
                            if (str_starts_with($command, $blockedCommand)) {
                                return new Response('command: ' . $command . ' is not allowed!');
                            }
                        }
                    }

                    // check if aliases config found
                    if (file_exists(__DIR__ . '/../../../terminal-aliases.json')) {
                        // get aliases list
                        try {
                            $aliases = $this->jsonUtil->getJson(__DIR__ . '/../../../terminal-aliases.json');
                        } catch (\Exception $e) {
                            return new Response($e->getMessage());
                        }

                        // replace aliases with runnable command
                        foreach ($aliases as $index => $value) {
                            if ($command == $index) {
                                $command = $value;
                            }
                        }
                    }

                    // get cwd (system get)
                    if ($command === 'get_current_path_1181517815187484') {
                        return new Response(getcwd());
                    }

                    // get user (system get)
                    if ($command === 'get_current_user_1181517815187484') {
                        return new Response(get_current_user());
                    }

                    // update cwd (system get)
                    if (str_starts_with($command, 'cd ')) {
                        $newDir = str_replace('cd ', '', $command);

                        // check if dir is / root dir
                        if (!str_starts_with($newDir, '/')) {
                            $finalDir = getcwd() . '/' . $newDir;
                        } else {
                            $finalDir = $newDir;
                        }

                        // check if directory exists
                        if (file_exists($finalDir)) {
                            $this->sessionManager->setSession('terminal-dir', $finalDir);
                            return new Response('', 200);
                        } else {
                            return new Response('error directory: ' . $finalDir . ' not found');
                        }
                    } else {

                        // execute command
                        exec($command, $output, $returnCode);

                        // check if command run valid
                        if ($returnCode !== 0) {

                            // check if command not found
                            if ($returnCode == 127) {
                                return new Response('command: ' . $command . ' not found');
                            } else {
                                return new Response('error to execute command: ' . $command);
                            }
                        } else {

                            // get output
                            $output = implode("\n", $output);

                            // escape output
                            $output = $this->securityUtil->escapeString($output);

                            // return output
                            return new Response($output);
                        }
                    }
                }
            } else {

                return new Response('terminal-api: request is not POST');
            }
        } else {
            $this->errorManager->handleError('error to set online status for non authentificated users!', 401);
            return new RedirectResponse('/');
        }
    }
}
