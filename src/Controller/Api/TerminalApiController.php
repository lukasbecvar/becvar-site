<?php

namespace App\Controller\Api;

use App\Util\JsonUtil;
use App\Util\SessionUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    This controller provides API function: execute terminal commands
*/

class TerminalApiController extends AbstractController
{
    private JsonUtil $jsonUtil;
    private LogManager $logManager;
    private SessionUtil $sessionUtil;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;

    public function __construct(
        JsonUtil $jsonUtil,
        LogManager $logManager,
        AuthManager $authManager,
        SessionUtil $sessionUtil,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager
    ) {
        $this->jsonUtil = $jsonUtil;
        $this->logManager = $logManager;
        $this->sessionUtil = $sessionUtil;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
    }

    #[Route('/api/system/terminal', name: 'api_terminal')]
    public function terminalAction(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin() && $this->authManager->isAdmin()) {

            // check if request is post
            if ($request->isMethod('POST')) {

                // get username
                $username = $this->authManager->getUsername();

                // set default working dir
                if ($this->sessionUtil->checkSession('terminal-dir')) {
                    $currentDir = $this->sessionUtil->getSessionValue('terminal-dir');
                    if (!file_exists($currentDir)) {
                        $currentDir = '/';
                    }
                    chdir($currentDir);
                } else {
                    chdir('/');
                }

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
                    if ($command === 'get_current_hostname_1181517815187484') {
                        return new Response(gethostname());
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
                            $this->sessionUtil->setSession('terminal-dir', $finalDir);
                            return new Response('', 200);
                        } else {
                            return new Response('error directory: ' . $finalDir . ' not found');
                        }
                    } else {

                        // execute command
                        exec('sudo '.$command, $output, $return_code);

                        // check if command run valid
                        if ($return_code !== 0) {

                            // check if command not found
                            if ($return_code == 127) {
                                $this->logManager->log('terminal', $username.' executed not found command: '.$command);
                                return new Response('command: ' . $command . ' not found');
                            } else {
                                $this->logManager->log('terminal', $username.' executed command: '.$command.' with error code: '.$return_code);
                                return new Response('error to execute command: ' . $command);
                            }
                        } else {

                            $this->logManager->log('terminal', $username.' executed command: '.$command);

                            // get output
                            $output = implode("\n", $output);

                            // escape output
                            $output = $this->securityUtil->escapeString($output);

                            // return output
                            return new Response($output);
                        }
                    }
                } else {
                    return $this->errorManager->handleError('terminal-api: command data is empty!', 401);
                }
            } else {
                return $this->errorManager->handleError('terminal-api: request is not POST!', 500);
            }
        } else {
            return $this->errorManager->handleError('error to set online status for non authentificated users!', 401);
        }
    }
}
