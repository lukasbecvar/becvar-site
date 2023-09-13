<?php // open project site component 

    // get projects list object from manager
    $openProjects = $projects_manager->get_projects_list("open"); 

    // print all open project to element
    foreach ($openProjects as $data) {

        // get information form object
        $name = $data["name"];
        $description = $data["description"];
        $technology = $data["technology"];
        $github_link = $data["github_link"];

        // build item elemen
        if ($github_link == "none" or empty($github_link) or $github_link == null) {

            // element with github link
            $element = '
                <div class="projects-item">
                    <h4>'.$name.'</h4>
                    <p><em>'.$description.'</em></p>
                    <p><ul>
                        <li>Technology: '.$technology.'</li>
                    </ul></p>
                </div>
            ';
        } else {

            // element withou github link
            $element = '
                <div class="projects-item">
                    <h4>'.$name.'</h4>
                    <p><em>'.$description.'</em></p>
                    <p><ul>
                        <li>Technology: '.$technology.'</li>
                        <li>Source: <a href="'.$github_link.'" target="_blank">source </a>on github</li>
                    </ul></p>
                </div>
            ';
        }

        // print element
        echo $element;
    }
?>