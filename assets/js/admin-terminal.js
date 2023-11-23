document.addEventListener("DOMContentLoaded", function() {
    
    // elements list
    const terminal = document.getElementById('output-container');
    const command_container = document.getElementById('command-container');
    const command_input = document.getElementById('command');
    const hostname_element = document.getElementById('user');
    const path_element = document.getElementById('path');

    const api_url = '/api/system/terminal';

    let currentPath = '';
    let currentHostname = '';

    // select command input
    command_input.focus();

    function updatePath() {
        path_element.textContent = currentPath;
    }

    function updateHostname() {
        hostname_element.textContent = 'root@' + currentHostname;
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

    getCurrentHostname();
    getCurrentPath();

    command_input.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            const command = this.value.trim();
            if (command.length > 0) {
                terminal.innerHTML += '<div class="prompr-reset" id="prompt-line"><span id="user">' + hostname_element.textContent + '</span><span class="color-white">:</span><span id="path">' + path_element.textContent + '</span><span id="prompt" class="color-white">$ ' + command + '</span></div>';
                this.value = '';
                executeCommand(command);
                getCurrentHostname();
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
            command_container.style.display = 'none';
            const xhr = new XMLHttpRequest();
            xhr.open('POST', api_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        terminal.innerHTML += '<div>' + xhr.responseText + '</div>';
                        command_container.style.display = '';
                        scrollToBottom();
                        command_input.focus();
                    } else {
                        console.log(xhr.responseText);
                        terminal.innerHTML += '<div class="text-warning">Error communicating with the API.</div>';
                        scrollToBottom();
                        command_input.focus();
                    }
                }
            };
            xhr.send('command=' + encodeURIComponent(command));
        }
    }
});
