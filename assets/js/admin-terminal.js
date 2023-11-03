document.addEventListener("DOMContentLoaded", function() {
    
    // elements list
    const terminal = document.getElementById('output-container');
    const command_input = document.getElementById('command');
    const user_element = document.getElementById('user');
    const path_element = document.getElementById('path');

    const api_url = '/api/system/terminal';

    let currentPath = '';
    let currentUser = '';

    // select command input
    command_input.focus();

    function updatePath() {
        path_element.textContent = currentPath;
    }

    function updateUser() {
        user_element.textContent = currentUser;
    }

    function scrollToBottom() {
        terminal.scrollTop = terminal.scrollHeight;
    }

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

    function getCurrentUser() {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', api_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                currentUser = xhr.responseText;
                updateUser();
            }
        };
        xhr.send('command=get_current_user_1181517815187484');
    }

    getCurrentUser();
    getCurrentPath();

    command_input.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            const command = this.value.trim();
            if (command.length > 0) {
                terminal.innerHTML += '<div class="prompr-reset" id="prompt-line"><span id="user">' + user_element.textContent + '</span><span class="color-white">:</span><span id="path">' + path_element.textContent + '</span><span id="prompt" class="color-white">$ ' + command + '</span></div>';
                this.value = '';
                executeCommand(command);
                getCurrentUser();
                getCurrentPath();
                scrollToBottom();
            }
        }
    });

    document.addEventListener("click", function(e) {
        if (e.target !== command_input) {
            command_input.focus();
        }
    });

    function executeCommand(command) {
        if (command.toLowerCase() === 'clear') {
            terminal.innerHTML = '';
        } else {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', api_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        terminal.innerHTML += '<div>' + xhr.responseText + '</div>';
                        scrollToBottom();
                    } else {
                        console.log(xhr.responseText);
                        terminal.innerHTML += '<div class="text-warning">Error communicating with the API.</div>';
                        scrollToBottom();
                    }
                }
            };
            xhr.send('command=' + encodeURIComponent(command));
        }
    }
});
