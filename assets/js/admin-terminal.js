/* becvar-site: admin terminal */ 
document.addEventListener("DOMContentLoaded", function() {
    // get html element list
    const terminal = document.getElementById('output-container');
    const command_container = document.getElementById('command-container');
    const command_input = document.getElementById('command');
    const hostname_element = document.getElementById('user');
    const path_element = document.getElementById('path');

    // main api url
    const api_url = '/api/system/terminal';

    let currentPath = '';
    let currentHostname = '';

    // focus command input
    command_input.focus();

    // update cwd
    function updatePath() {
        path_element.textContent = currentPath;
    }

    // update hostname
    function updateHostname() {
        hostname_element.textContent = 'root@' + currentHostname;
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
    command_input.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            const command = this.value.trim();
            if (command.length > 0) {
                // display command in the terminal
                terminal.innerHTML += '<div class="prompr-reset" id="prompt-line"><span id="user">' + hostname_element.textContent + '</span><span class="color-white">:</span><span id="path">' + path_element.textContent + '</span><span id="prompt" class="color-white">$ ' + command + '</span></div>';
                
                // clear the input
                this.value = '';

                // execute the command
                executeCommand(command);

                // update and display the current hostname and path
                getCurrentHostname();
                getCurrentPath();

                // scroll to the bottom of the terminal
                scrollToBottom();
            }
        }
    });

    // event listener to focus on the command input when clicking outside of it
    document.addEventListener("click", function(e) {
        var isInsideTerminalComponent = e.target.closest('.terminal-component') !== null;
    
        if (isInsideTerminalComponent && e.target !== command_input) {
            command_input.focus();
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
            command_container.style.display = 'none';

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
                        command_container.style.display = '';

                        // scroll to the bottom of the terminal
                        scrollToBottom();

                        // focus on the command input
                        command_input.focus();
                    } else {
                        // log an error and display a warning in the terminal
                        console.log(xhr.responseText);
                        terminal.innerHTML += '<div class="text-warning">Error communicating with the API.</div>';
                        
                        // scroll to the bottom of the terminal
                        scrollToBottom();
                        
                        // focus on the command input
                        command_input.focus();
                    }
                }
            };
            
            // send the command to the server
            xhr.send('command=' + encodeURIComponent(command));
        }
    }
});
