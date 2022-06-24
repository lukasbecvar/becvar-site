<div class="contactPanel">
<?php //Main admin database table browser

	//Check if user is owner
	if (!$adminController->isUserOwner()) {
		echo"<h2 class=pageTitle>Sorry you dont have permission to this page</h2>";
	} else {

		//Page items limit
		$limitOnPage = $pageConfig->getValueByName("rowInTableLimit");

		//If limit get seted make this trash part of code xD
		if (isset($_GET["limit"]) && isset($_GET["startby"])) {

			//Get show limit form url
			$showLimit = $mysqlUtils->escapeString($_GET["limit"], true, true);

			//Get start row form url
			$startByRow = $mysqlUtils->escapeString($_GET["startby"], true, true);

			//Set next limit
			$nextLimit = (int) $showLimit + $limitOnPage;

			//Set next start by for pages
			$nextStartByRow = (int) $startByRow + $limitOnPage;
			$nextLimitBack = (int) $showLimit - $limitOnPage;
			$nextStartByRowBack = (int) $startByRow - $limitOnPage;	
		}



		//If name not empty get count from table
		if (!empty($_GET["name"])) {
			$rowCounterGet = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM ".$_GET["name"])); 
		}


		
		//Print button if name not empty
		if (!empty($_GET["name"]) or !empty($_GET["editor"])) {
			echo '<ul class="breadcrumb bg-dark">';
				echo '<li><a class="selectorButton btn-small" href="index.php?page=admin&process=dbBrowser"><strong>Table selector</strong></a></li>';

				if (!empty($_GET["name"])) {
					echo '<li><a class="selectorButton btn-small" href="index.php?page=admin&process=dbBrowser&delete='.$_GET["name"].'&id=all"><strong>Delete all '.$_GET["name"].'</strong></a></li>';
				}

				if (!empty($_GET["name"])) {
					echo '<li class="countTextInMenuR">'.$_GET["name"].' = '.$rowCounterGet["count"].' rows</li>';	
				} else {
					echo '<li class="countTextInMenuR">Row editor</li>';	
				}
			echo '</ul>';
		}

		//Check if set delete (For delete items by id)
		if (isset($_GET["delete"])) {

			//Check if user selectd db name
			if (isset($_GET["delete"])) {
				if (isset($_GET["id"])) {

					//Get values form url and escape
					$dbName = $_GET["delete"];
					$id = $mysqlUtils->escapeString($_GET["id"]);

					//Log action to mysql dsatabase 
					$mysqlUtils->logToMysql("Database delete", "User ".$adminController->getCurrentUsername()." deleted item $id form table $dbName");

					//If user delete all form table
					if ($id == "all") {
						//Delete all rows
						$mysqlUtils->insertQuery("DELETE FROM $dbName WHERE id=id");

						//Reset auto increment
						$mysqlUtils->insertQuery("ALTER TABLE $dbName AUTO_INCREMENT = 1");
					} else {

						//Delete one row
						$mysqlUtils->insertQuery("DELETE FROM $dbName WHERE id='$id'"); 
					}

					//Redirect to db browser page	
					if (isset($_GET["close"]) && $_GET["close"] == "y") {
						echo "<script>window.close();</script>";
					} else {
						if (isset($_GET["reader"])) {
							$urlUtils->jsRedirect("index.php?page=admin&process=logReader&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
						} else {
							$urlUtils->jsRedirect("index.php?page=admin&process=dbBrowser&name=$dbName&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
						}
					}
					
				} else {

					//If user seted name and not id
					$urlUtils->jsRedirect("index.php?page=admin&process=dbBrowser");
				}

			//If id is not seted redirect to browser				
			} else { 
				$urlUtils->jsRedirect("index.php?page=admin&process=dbBrowser");
			}

			//Check if set editor for edit item by id
			} elseif (isset($_GET["editor"])) {

				//Check if user submit edit form
				if (isset($_POST["submitEdit"])) {

					//Inti query strings
					$dbNameQ = $mysqlUtils->escapeString($_GET["editor"]);
					$idQ = $mysqlUtils->escapeString($_GET["id"]);

					//Select columns from selected table
					$resultEdit = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$dbNameQ);

					//Update all fileds by id
					while($rowOK = mysqli_fetch_array($resultEdit)) { 

						//Insert query
						$mysqlUtils->insertQuery("UPDATE $dbNameQ SET ".$rowOK["Field"]."='".$_POST[$rowOK["Field"]]."' WHERE id='$idQ'");
					} 

					//Log action to mysql dsatabase 
					$mysqlUtils->logToMysql("Database edit", "User ".$adminController->getCurrentUsername()." edited item $idQ in table $dbNameQ");

					$alertController->flashSuccess("Row has saved!");
					

					//Set final action
					if (isset($_GET["postby"]) and $_GET["postby"] == "todomanager") {
						//Close editor after save
						echo "<script>window.close();</script>";
					} else {
						$urlUtils->jsRedirect("index.php?page=admin&process=dbBrowser&name=".$_GET['editor']."&limit=".$limitOnPage."&startby=0");
					}
				}


				//Init table name
				$dbName = $mysqlUtils->escapeString($_GET["editor"], true, true);

				//Init row id
				$id = $mysqlUtils->escapeString($_GET["id"], true, true);

				//Select columns from selected table
				$result = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$dbName);

				//Select all from selected table
				$resultAll = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM $dbName WHERE id = '$id'");

				//Migrate object to array
				$rowAll = mysqli_fetch_array($resultAll);
 
				echo "<br><br>";

				//Create form
				if (isset($_GET["postby"]) and $_GET["postby"] == "todomanager") {
					echo '<form class="dbEditForm" action="index.php?page=admin&process=dbBrowser&editor='.$dbName.'&id='.$id.'&postby=todomanager" method="post">';
				} else {
					echo '<form class="dbEditForm" action="index.php?page=admin&process=dbBrowser&editor='.$dbName.'&id='.$id.'" method="post">';
				}
				echo '<p style="color: white; font-size: 20px;" class="loginFormTitle">Edit row with '.$id.'<p>';

					//Print Fields
					while($row = mysqli_fetch_array($result)) {
						echo '<p class="textInputTitle">'.$row['Field'].'</p>';
						echo '<input class="textInput bg-dark" type="text" name="'.$row['Field'].'" value="'.$rowAll[$row['Field']].'"><br>';
					}

				//End form
				echo '<input class="inputButton bg-dark" type="submit" name="submitEdit" value="Edit"></form>';

			}  else {
				
				//Log action to mysql dsatabase 
				$mysqlUtils->logToMysql("Database list", "User ".$adminController->getCurrentUsername()." viewed database list");

				//Save tables to objects
				$tables = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW TABLES");


				//Check if user selected database
				if (empty($_GET["name"])) { 

					//Print title
					echo '<h2 class="pageTitle">Select table</h2>';

					//Print select box
					echo '<div><ol><br>';

					//Check if name not empty
					if (empty($_GET["name"])) {

						//Print all tables to page
						while ($row = mysqli_fetch_assoc($tables)) {
							echo "<a class='dbBrowserSelectLink' href=index.php?page=admin&process=dbBrowser&name=".$row["Tables_in_".$pageConfig->getValueByName("basedb")]."&limit=".$limitOnPage."&startby=0>".$row["Tables_in_".$pageConfig->getValueByName("basedb")]."</a><br><br>";
						}	
					}
					echo '</ol></div>';		
				}
					
				//Check if set get name
				if (isset($_GET["name"])) {

				//Save name to variable and escape string
				$name = $mysqlUtils->escapeString($_GET["name"], true, true); 

				//Select all data from table by name
				$tableData = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM ".$name." LIMIT $startByRow, $limitOnPage");

				//Create associative array from table data
				$row = mysqli_fetch_array($tableData, MYSQLI_ASSOC);

				//Select columns from selected table
				$result = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$name);

				//Log action to mysql dsatabase 
				$mysqlUtils->logToMysql("Database", "User ".$adminController->getCurrentUsername()." viewed table $name");


				if ($tableData->num_rows == 0) {
					echo"<h2 class=pageTitle>Table is empty</h2>";
				} else {

					//Create table
					echo '<div class="table-responsive"><table class="table table-dark">';
					echo '<thead><tr>'; 

					//Print mysql fields to table
					while($row = mysqli_fetch_array($result)) {
						echo "<th scope='col'>".$row['Field']."</th>";
					}

					echo "<th cope='col'>X</th>";
					
					//Add edit col to table
					if ($_GET["name"] != "visitors" && $_GET["name"] != "pastes" && $_GET["name"] != "crypted" && $_GET["name"] != "hash_gen" && $_GET["name"] != "users") {
						echo "<th cope='col'>Edit</th>";
					}

					echo '</tr></thead>';

					foreach ($tableData as $data) {

						//Edit raw image uploader data
						if ($_GET["name"] == "image_uploader") {
							$data = [
								"id" => $data["id"],
								"imgSpec" => '<a href="index.php?process=image&spec='.$data["imgSpec"].'" target="_blank">'.$data["imgSpec"].'</a>',
								"image" => "encrypted",
								"date" => $data["date"]
							];			
						}


						//Edit raw paste data
						if ($_GET["name"] == "pastes") {
							$data = [
								"id" => $data["id"],
								"link" => '<a href="index.php?process=paste&method=view&f='.$data["link"].'" target="_blank">'.$data["link"].'</a>',
								"spec" => $data["spec"],
								"content" => "hidden",
								"date" => $data["date"]
							];				
						}
						

						//Edit raw visitors data
						if ($_GET["name"] == "visitors") {

							//Check if cookie seted and if key = cookie (for highlite user cookie)
							if (!empty($_COOKIE["identifier"]) && $data["key"] == $_COOKIE["identifier"]) {

								$data = [ 
									"id" => $data["id"],
									"key" => "<span class='text-warning'>".$data["key"]."</span> [<span class='text-success'>You</span>]",
									"visited_sites" => $data["visited_sites"],
									"first_visit" => $data["first_visit"],
									"last_visit" => $data["last_visit"],
									"browser" => $data["browser"],
									"ip_adress" => $data["ip_adress"],
								];

							} elseif ($data["browser"] == "Undefined") {

								$data = [
									"id" => $data["id"],
									"key" => $data["key"],
									"visited_sites" => $data["visited_sites"],
                                                                        "first_visit" => "<span class='text-red'>".$data["first_visit"]."</span>",
                                                                        "last_visit" => "<span class='text-red'>".$data["last_visit"]."</span>",
                                                                        "browser" => "<span class='text-red'>".$data["browser"]."</span>",
									"ip_adress" => $data["ip_adress"],
								];

							} elseif ($data["first_visit"] == $data["last_visit"]) {

								$data = [
									"id" => $data["id"],
									"key" => $data["key"],
									"visited_sites" => $data["visited_sites"],
									"first_visit" => "<span class='text-red'>". $data["first_visit"]."</span>",
									"last_visit" => "<span class='text-red'>". $data["last_visit"]."</span>",
									"browser" => $data["browser"],
									"ip_adress" => $data["ip_adress"],
								];

							} else {

								$data = [
									"id" => $data["id"],
									"key" => $data["key"],
									"visited_sites" => $data["visited_sites"],
									"first_visit" => $data["first_visit"],
									"last_visit" => $data["last_visit"],
									"browser" => $data["browser"],
									"ip_adress" => $data["ip_adress"],
								];

							}			
						}


						//Edit raw user data
						if ($_GET["name"] == "users") {
							$data = [
								"id" => $data["id"],
								"username" => $data["username"],
								"password" => "encrypted_hash",
								"role" => $data["role"],
								"image_base64" => "hidden",
								"token" => $data["token"]
							];			
						}


						//Edit raw crypted data
						if ($_GET["name"] == "crypted") {

							if ($data["method"] == "Image encode") {

								$data = [
									"id" => $data["id"],
									"algorithm" => "<span class='text-warning'>".$data["algorithm"]."</span>",
									"key" => "<span class='text-red'>".$data["key"]."</span>",
									"method" => "<span class='text-light-green'>".$data["method"]."</span>",
									"input" => "<a href='index.php?process=image&spec=".$data["input"]."' target='_blank'>".$data["input"]."</a>",
									"output" => $data["output"]
								];							
							
							} else {

								$data = [
									"id" => $data["id"],
									"algorithm" => "<span class='text-warning'>".$data["algorithm"]."</span>",
									"key" => "<span class='text-red'>".$data["key"]."</span>",
									"method" => "<span class='text-light-green'>".$data["method"]."</span>",
									"input" => $data["input"],
									"output" => $data["output"]
								];	
								
							}	
						}

						//Transfrom associative array to indexed array
						$dataOK = array_values($data);

						echo '<tbody><tr class="lineItem">';
						//Print table data
						for ($id = 0; $id <= 100; $id++) {
							if (!empty($dataOK[$id])) {
								echo "<th scope='row'>".$dataOK[$id]."</th>";	
							}
						}
				
						if(empty($data["base64"])) {
							echo '<td><a class="deleteLinkTodos" href="index.php?page=admin&process=dbBrowser&delete='.$name.'&id='.$dataOK[0].'">X</a></td>';
							
							//Add edit link to row
							if ($_GET["name"] != "visitors" && $_GET["name"] != "pastes" && $_GET["name"] != "crypted" && $_GET["name"] != "hash_gen" && $_GET["name"] != "users") {
								echo '<td><a class="text-warning deleteLinkTodos" href="index.php?page=admin&process=dbBrowser&editor='.$name.'&id='.$dataOK[0].'">Edit</a></td>';
							}
						}
						echo '</tr></tbody>';
					}
				
					echo '</table>';

				}


				if (isset($_GET["limit"]) and isset($_GET["startby"])) {

					if (($showLimit > $limitOnPage) or ($tableData->num_rows == $limitOnPage)) {
						echo '<div class="pageButtonBox">';
					}
				
					//Print back button if user in next page
					if ($showLimit > $limitOnPage) {
						echo '<br><a class="backPageButton" href=index.php?page=admin&process=dbBrowser&name='.$_GET["name"].'&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
					}


					//Print next button if user on start page and can see next items
					if ($tableData->num_rows == $limitOnPage) {
						echo '<br><a class="backPageButton" href=index.php?page=admin&process=dbBrowser&name='.$_GET["name"].'&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
					}
			
					if (($showLimit > $limitOnPage) or ($tableData->num_rows == $limitOnPage)) {
						echo '</div><br>';
					}
				}
			}	
		}
	}
?>
