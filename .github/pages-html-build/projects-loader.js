document.addEventListener('DOMContentLoaded', () => {
    console.log('Fetching projects.json...');
    fetch('projects.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Projects data loaded:', data);
            renderProjects(data.open, 'open-projects-list');
            renderProjects(data.closed, 'closed-projects-list');
        })
        .catch(err => console.error('Error loading projects:', err));
});

function renderProjects(projects, containerId) {
    const container = document.getElementById(containerId);
    console.log(`Rendering projects into ${containerId}:`, projects);
    if (!container) {
        console.error(`Container not found: ${containerId}`);
        return;
    }
    projects.forEach(project => {
        const item = document.createElement('div');
        item.className = 'projects-item';
        item.innerHTML = `
            <h4>${project.title}</h4>
            <p><em>${project.description}</em></p>
            <ul>
                <li>Jazyk: ${project.language}</li>
                <li>Repozitář: <a href="${project.github_url}" target="_blank">github</a></li>
            </ul>
        `;
        container.appendChild(item);
    });
}
