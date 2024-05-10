/* becvar-site: admin terminal */ 
document.addEventListener("DOMContentLoaded", function() {
    // get html element list
    const terminal = document.getElementById('output-container');
    const commandContainer = document.getElementById('command-container');
    const commandInput = document.getElementById('command');
    const hostnameElement = document.getElementById('user');
    const pathElement = document.getElementById('path');

    // main api url
    const api_url = '/api/system/terminal';

    let currentPath = '';
    let currentHostname = '';

    // focus command input
    commandInput.focus();

    // update cwd
    function updatePath() {
        pathElement.textContent = currentPath;
    }

    // update hostname
    function updateHostname() {
        hostnameElement.textContent = 'root@' + currentHostname;
    }

    // scroll the bottom
    function scrollToBottom() { 
        terminal.scrollTop = terminal.scrollHeight;
    }

    // fetch the current cwd from the server
    function getCurrentPath() {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', api_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                currentPath = xhr.responseText;
                updatePath();
            }
        };
        xhr.send('command=get_current_path_1181517815187484');
    }

    // fetch the current hostname from the server
    function getCurrentHostname() {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', api_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                currentHostname = xhr.responseText;
                updateHostname();
            }
        };
        xhr.send('command=get_current_hostname_1181517815187484');
    }

    // fetch and display hostname and cwd
    getCurrentHostname();
    getCurrentPath();

    // event listener for keypress in the command input
    commandInput.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            const command = this.value.trim();
            if (command.length > 0) {
                // display command in the terminal
                terminal.innerHTML += '<div class="prompr-reset" id="prompt-line"><span id="user">' + hostnameElement.textContent + '</span><span class="color-white">:</span><span id="path">' + pathElement.textContent + '</span><span id="prompt" class="color-white">$ ' + command + '</span></div>';
                
                // clear the input
                this.value = '';

                // execute the command
                executeCommand(command);

                // scroll to the bottom of the terminal
                scrollToBottom();
            }
        }
        // update and display the current hostname and path
        getCurrentHostname();
        getCurrentPath();
    });

    // event listener to focus on the command input when clicking outside of it
    document.addEventListener("click", function(e) {
        var isInsideTerminalComponent = e.target.closest('.terminal-component') !== null;
    
        if (isInsideTerminalComponent && e.target !== commandInput) {
            commandInput.focus();
        }
    });
    

    // execute the entered command
    function executeCommand(command) {
        // set command to lower case
        command = command.toLowerCase();

        // clear terminal history
        if (command === 'clear') {
            // clear the terminal
            terminal.innerHTML = '';
        } else {
            // hide the command container during command execution
            commandContainer.style.display = 'none';

            // send the command to the server
            const xhr = new XMLHttpRequest();
            xhr.open('POST', api_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        // display the server response in the terminal
                        terminal.innerHTML += '<div>' + xhr.responseText + '</div>';

                        // show the command container again
                        commandContainer.style.display = '';

                        // scroll to the bottom of the terminal
                        scrollToBottom();

                        // focus on the command input
                        commandInput.focus();
                    } else {
                        // log an error and display a warning in the terminal
                        console.log(xhr.responseText);
                        terminal.innerHTML += '<div class="text-warning">Error communicating with the API.</div>';
                        
                        // scroll to the bottom of the terminal
                        scrollToBottom();
                        
                        // focus on the command input
                        commandInput.focus();
                    }
                }
            };
            
            // send the command to the server
            xhr.send('command=' + encodeURIComponent(command));
        }
    }
});
