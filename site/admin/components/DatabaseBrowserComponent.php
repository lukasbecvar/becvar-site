<div class="adminPanel">
<?php //Main admin database table browser

	//Check if user is owner
	if (!$adminController->isUserOwner()) {
		echo"<h2 class=pageTitle>Sorry you dont have permission to this page</h2>";
	} else {

		////////////////////////////////PAGE-SITES-VALUES////////////////////////////////

		//Check if id seted
		if (isset($_GET["id"])) {

			//Get id from query string
			$idGet = $mysqlUtils->escapeString($_GET["id"], true, true);
		}

		//Check if delete seted
		if (isset($_GET["delete"])) {

			//Get delete from query string
			$deleteGet = $mysqlUtils->escapeString($_GET["delete"], true, true);
		}

		//Check if editor seted
		if (isset($_GET["editor"])) {

			//Get editor from query string
			$editorGet = $mysqlUtils->escapeString($_GET["editor"], true, true);
		}

		//Check if add seted
		if (isset($_GET["add"])) {

			//Get add from query string
			$addGet = $mysqlUtils->escapeString($_GET["add"], true, true);
		}

		//Check if browse table
		if (isset($_GET["name"])) {

			//Get escaped table name
			$tableName = $mysqlUtils->escapeString($_GET["name"], true, true);
		}

		//Check if browse table
		if (isset($_GET["name"])) {

			//Get row count in table by name
			$rowsCount = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM ".$tableName)); 
		} else {
			$rowsCount = 0;
		}
		
		//Default select start id
		$startByRow = 0;
		
		//Page items limit (read from config)
		$limitOnPage = $pageConfig->getValueByName("rowInTableLimit");

		//Pager system calculator
		if (isset($_GET["name"]) && (isset($_GET["limit"]) && isset($_GET["startby"]))) {
 
			//Get show limit form url
			$showLimit = $mysqlUtils->escapeString($_GET["limit"], true, true);
 
			//Get start row form url
			$startByRow = $mysqlUtils->escapeString($_GET["startby"], true, true);
 
			//Calculate next limit
			$nextLimit = (int) $showLimit + $limitOnPage;
 
			//Calculate next start
			$nextStartByRow = (int) $startByRow + $limitOnPage;

			//Calculate back limit
			$nextLimitBack = (int) $showLimit - $limitOnPage;

			//Calculate back start row
			$nextStartByRowBack = (int) $startByRow - $limitOnPage;	
		}
		/////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////SUB-PANEL////////////////////////////////////
		if (!empty($_GET["name"]) or !empty($_GET["editor"]) or !empty($_GET["add"])) { //Check if panel required
			echo '<ul class="breadcrumb bg-dark">'; //Create panel element

				// Add table selector button to panel
				echo '
					<li>
						<a class="selectorButton btn-small" href="?admin=dbBrowser"><strong>TABLES</strong></a>
					</li>';

				//Add delete all button to panel
				if (!empty($_GET["name"])) {
					echo '
						<li> 
							<a class="selectorButton btn-small" href="?admin=dbBrowser&delete='.$tableName.'&id=all"><strong>DELETE ALL '.$tableName.'</strong></a>
						</li>';
				}
 
				//Add new row button to panel
				if (!empty($_GET["name"])) {
					echo '
						<li> 
							<a class="selectorButton btn-small" href="?admin=dbBrowser&add='.$tableName.'"><strong>ADD</strong></a>
						</li>';
				}

				//Add back table button to panel
				if (!empty($_GET["add"])) {
					echo '
						<li> 
							<a class="selectorButton btn-small" href="?admin=dbBrowser&name='.$_GET["add"].'"><strong>BACK</strong></a>
						</li>';
				}

				//Print row count
				if (!empty($_GET["name"])) {
					echo '<li class="countTextInMenuR">'.$_GET["name"].' = '.$rowsCount["count"].' rows</li>';	
				} else {

					//Print editor title
					if (isset($_GET["editor"])) {
						echo '<li class="countTextInMenuR">Row editor</li>';	
					} 
					
					//Print addition title
					elseif (isset($_GET["add"])) {
						echo '<li class="countTextInMenuR">New row</li>';
					} 
					
					//Print default titile
					else {
						echo '<li class="countTextInMenuR">Database browser</li>';
					}
				}
			echo '</ul>';
		}
		/////////////////////////////////////////////////////////////////////////////////

		//Table browser /////////////////////////////////////////////////////////////////
		if (isset($_GET["name"])) {

			//Select table data
			$tableData = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM ".$tableName." LIMIT $startByRow, $limitOnPage");
 
			//Create associative array from table data
			$tableDataAssoc = mysqli_fetch_array($tableData, MYSQLI_ASSOC);

			//Select columns from table
			$tableColumns = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$tableName);
 
			//Check if table empty
			if ($tableData->num_rows == 0) {
				echo"<h2 class=pageTitle>Table is empty</h2>";
			} 
			
			//Print table data
			else {

				//Create table element
				echo '<div class="table-responsive"><table class="table table-dark">';
				echo '<thead><tr>'; 

				//Print mysql fields to table
				while($row = mysqli_fetch_array($tableColumns)) {
					echo "<th scope='col'>".$row['Field']."</th>";
				}

				echo "<th cope='col'>X</th>";
				
				//Add edit col to table
				if ($tableName != "visitors" && $tableName != "pastes" && $tableName != "hash_gen" && $tableName != "users") {
					echo "<th cope='col'>Edit</th>";
				}

				echo '</tr></thead>';

				//Print all rows to site
				foreach ($tableData as $data) {

					//////////////////////////////////CUSTOM-VIEW//////////////////////////////////
					//image uploader custom view
					if ($tableName == "image_uploader") {
						$data = [
							"id" => $data["id"],
							"imgSpec" => '<a href="?process=image&spec='.$data["imgSpec"].'" target="_blank">'.$data["imgSpec"].'</a>',
							"image" => "encrypted",
							"date" => $data["date"]
						];			
					}

					//paste custom view
					if ($tableName == "pastes") {
						$data = [
							"id" => $data["id"],
							"link" => '<a href="?process=paste&method=view&f='.$data["link"].'" target="_blank">'.$data["link"].'</a>',
							"spec" => $data["spec"],
							"content" => "hidden",
							"date" => $data["date"]
						];				
					}

					//users custom view
					if ($tableName == "users") {
						$data = [
							"id" => $data["id"],
							"username" => $data["username"],
							"password" => "encrypted_hash",
							"role" => $data["role"],
							"image_base64" => "hidden",
							"remote_addr" => $data["remote_addr"],
							"token" => $data["token"]
						];			
					}
					///////////////////////////////////////////////////////////////////////////////

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
						echo '<td><a class="deleteLinkTodos" href="?admin=dbBrowser&delete='.$tableName.'&id='.$dataOK[0].'">X</a></td>';
						
						//Add edit link to row
						if ($tableName != "visitors" && $tableName != "pastes" && $tableName != "hash_gen" && $tableName != "users") {
							echo '<td><a class="text-warning deleteLinkTodos" href="?admin=dbBrowser&editor='.$tableName.'&id='.$dataOK[0].'">Edit</a></td>';
						}
					}
					echo '</tr></tbody>';
				}
				echo '</table>';
			}

			//Log action to database 
			$mysqlUtils->logToMysql("Database", "User ".$adminController->getCurrentUsername()." viewed table $tableName");
		} 
		
		//Delete function ///////////////////////////////////////////////////////////////
		elseif (isset($_GET["delete"])) {

			//Check if seted id
			if (isset($idGet)) {

				//Check if user delete all form table
				if ($idGet == "all") {
					//Delete all rows
					$mysqlUtils->insertQuery("DELETE FROM $deleteGet WHERE id=id");

					//Reset auto increment
					$mysqlUtils->insertQuery("ALTER TABLE $deleteGet AUTO_INCREMENT = 1");
				} 
				
				//One row delete
				else {

					//Delete one row
					$mysqlUtils->insertQuery("DELETE FROM $deleteGet WHERE id='$idGet'"); 
				}

				//Log action to database
				$mysqlUtils->logToMysql("Database delete", "User ".$adminController->getCurrentUsername()." deleted item $idGet form table $deleteGet");


				//Check if delete auto close
				if (isset($_GET["close"]) && $_GET["close"] == "y") {
					echo "<script>window.close();</script>";
				} 
				
				//Redirect back
				else {
					//Redirect to log reader
					if (isset($_GET["reader"])) {
						$urlUtils->jsRedirect("?admin=logReader&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
					} 
					
					//Redirect to visitors system
					else if (isset($_GET["visitors"])) {
						$urlUtils->jsRedirect("?admin=visitors&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
					} 
					
					//Redirect to database browser
					else {
						$urlUtils->jsRedirect("?admin=dbBrowser&name=$deleteGet&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
					}
				}

			} else {

				//Check if site dev mode enabled
				if ($siteController->isSiteDevMode()) {

					//Print error
					die("<h2 class=pageTitle>[DEV-MODE]:Error: query string id not found.<h2>");

				} else {

					//Redirect to browser main page
					$urlUtils->jsRedirect("?admin=dbBrowser");
				}
			}
		}
		
		//Editor function ///////////////////////////////////////////////////////////////
		elseif (isset($_GET["editor"])) {

			//Check if user submit edit form
			if (isset($_POST["submitEdit"])) {

				//Select columns from selected table
				$resultEdit = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$editorGet);

				//Update all fileds by id
				while($rowOK = mysqli_fetch_array($resultEdit)) { 

					//Insert query
					$mysqlUtils->insertQuery("UPDATE $editorGet SET ".$rowOK["Field"]."='".$_POST[$rowOK["Field"]]."' WHERE id='$idGet'");
				} 

				//Log action to mysql dsatabase 
				$mysqlUtils->logToMysql("Database edit", "User ".$adminController->getCurrentUsername()." edited item $idGet in table $editorGet");

				//Flash status msg
				$alertController->flashSuccess("Row has saved!");
				
				//Set final action
				if (isset($_GET["postby"]) and $_GET["postby"] == "todomanager") {
					//Close editor after save
					echo "<script>window.close();</script>";
				} else {
					$urlUtils->jsRedirect("?admin=dbBrowser&name=".$editorGet."&limit=".$limitOnPage."&startby=0");
				}
			}

			//Init table name
			$dbName = $mysqlUtils->escapeString($editorGet, true, true);

			//Select columns from selected table
			$result = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$editorGet);

			//Select all from selected table
			$resultAll = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM $editorGet WHERE id = '$idGet'");

			//Migrate object to array
			$rowAll = mysqli_fetch_array($resultAll);

			echo "<br><br>";

			//Create form
			if (isset($_GET["postby"]) and $_GET["postby"] == "todomanager") {
				echo '<form class="dbEditForm" action="?admin=dbBrowser&editor='.$editorGet.'&id='.$idGet.'&postby=todomanager" method="post">';
			} else {
				echo '<form class="dbEditForm" action="?admin=dbBrowser&editor='.$editorGet.'&id='.$idGet.'" method="post">';
			}
			echo '<p style="color: white; font-size: 20px;" class="loginFormTitle">Edit row with '.$idGet.'<p>';

				//Print Fields
				while($row = mysqli_fetch_array($result)) {
					echo '<p class="textInputTitle">'.$row['Field'].'</p>';
					echo '<input class="textInput bg-dark" type="text" name="'.$row['Field'].'" value="'.$rowAll[$row['Field']].'"><br>';
				}

			//End form
			echo '<input class="inputButton bg-dark" type="submit" name="submitEdit" value="Edit"></form>';
		}
		
		//Addition function /////////////////////////////////////////////////////////////
		elseif (isset($_GET["add"])) {
			
			//Select columns add table
			$selectedColumns = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$addGet);

			//Check if save submited
			if (isset($_POST["submitSave"])) {

				////////////////////-COLUMNS-LIST-BUILDER-/////////////////////
				//Create columns list
				$columnsBuilder = "";

				//Build columns list
				while($row = mysqli_fetch_array($selectedColumns)) {

					//prevent id valud build
					if (strtolower($row["Field"]) != "id") {
						$columnsBuilder = $columnsBuilder.", `".$row["Field"]."`";
					}
				}

				//Remove invalid character from columns list
				$columnsBuilder = substr($columnsBuilder, 1);
				///////////////////////////////////////////////////////////////
 
				/////////////////////-VALUES-LIST-BUILDER-/////////////////////
				//Create values list string
				$valuesBuilder = "";

				//Build values list
				foreach ($_POST as $post) {

					//Check if value not SAVE (button post remove)
					if ($post != "SAVE") {
						$valuesBuilder = $valuesBuilder.", '".$post."'";
					}
						
				}

				//Remove invalid character from values
				$valuesBuilder = substr($valuesBuilder, 1);
				///////////////////////////////////////////////////////////////

				//Build query
				$query = "INSERT INTO `".$addGet."`(".$columnsBuilder.") VALUES (".$valuesBuilder.")";

				//Insert query to database
				$mysqlUtils->insertQuery($query);

				//Flash alert
				$alertController->flashSuccess("New item has saved!");

				//Log to database
				$mysqlUtils->logToMysql("Database insert", "User ".$adminController->getCurrentUsername()." add new row to $addGet");

				//Redirect back to table reader
				$urlUtils->jsRedirect("?admin=dbBrowser&name=$addGet&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
			} 
			
			//Print add form
			else {

				//Create add form
				echo '<form class="dbEditForm" action="?admin=dbBrowser&add='.$addGet.'" method="post">';

				//Print from title
				echo '<p class="textInputTitle">New item</p>';

				//Add fields
				while($row = mysqli_fetch_array($selectedColumns)) {
					if (strtolower($row["Field"]) != "id") {
						echo '<input class="textInput bg-dark" type="text" name="'.$row["Field"].'" placeholder="'.$row["Field"].'"><br>';
					}
				}

				//Add submit button to form
				echo '<input class="inputButton bg-dark" type="submit" name="submitSave" value="SAVE">';

				//End of form
				echo '</form>';
			}
		} 
		
		//Table selector ////////////////////////////////////////////////////////////////
		else {

			//Page title
			echo '<h2 class="pageTitle">Select table</h2>';
 
			//Create select box element
			echo '<div><ol><br>';
 
			//Get tables object from database
			$tables = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW TABLES");

			//Print all tables links
			while ($row = mysqli_fetch_assoc($tables)) {
				echo "<a class='dbBrowserSelectLink' href=?admin=dbBrowser&name=".$row["Tables_in_".$pageConfig->getValueByName("basedb")]."&limit=".$limitOnPage."&startby=0>".$row["Tables_in_".$pageConfig->getValueByName("basedb")]."</a><br><br>";
			}

			//End of select box element
			echo '</ol></div>';

			//Log action to database 
			$mysqlUtils->logToMysql("Database list", "User ".$adminController->getCurrentUsername()." viewed database list");
		}

		///////////////////////////////////////PAGER-BUTTONS///////////////////////////////////////
		if (isset($_GET["name"]) && (isset($_GET["limit"]) and isset($_GET["startby"]))) {
 
			//Check if page buttons can show
			if (($showLimit > $limitOnPage) or ($tableData->num_rows == $limitOnPage)) {
				echo '<div class="pageButtonBox">'; //Create buttons element area
			}
		
			//Print back button if user in next page
			if ($showLimit > $limitOnPage) {
				echo '<br><a class="backPageButton" href=?admin=dbBrowser&name='.$_GET["name"].'&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
			}

			//Print next button if user on start page and can see next items
			if ($tableData->num_rows == $limitOnPage) {
				echo '<br><a class="backPageButton" href=?admin=dbBrowser&name='.$_GET["name"].'&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
			}
	
			//Check if page buttons can show
			if (($showLimit > $limitOnPage) or ($tableData->num_rows == $limitOnPage)) {
				echo '</div><br>'; //Close buttons element area
			}
		}
		///////////////////////////////////////////////////////////////////////////////////////////	
	}
?>
