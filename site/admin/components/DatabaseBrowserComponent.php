<div class="adminPanel">
<?php // admin database table browser

	// check if user is owner
	if (!$adminController->isUserOwner()) {
		echo"<h2 class=pageTitle>Sorry you dont have permission to this page</h2>";
	} else {

		////////////////////////////////PAGE-SITES-VALUES////////////////////////////////

		// check if id seted
		if (isset($_GET["id"])) {

			// get id from query string
			$idGet = $mysqlUtils->escapeString($_GET["id"], true, true);
		}

		// check if delete seted
		if (isset($_GET["delete"])) {

			// get delete from query string
			$deleteGet = $mysqlUtils->escapeString($_GET["delete"], true, true);
		}

		// check if editor seted
		if (isset($_GET["editor"])) {

			// get editor from query string
			$editorGet = $mysqlUtils->escapeString($_GET["editor"], true, true);
		}

		// check if add seted
		if (isset($_GET["add"])) {

			// get add from query string
			$addGet = $mysqlUtils->escapeString($_GET["add"], true, true);
		}

		// check if browse table
		if (isset($_GET["name"])) {

			// get escaped table name
			$tableName = $mysqlUtils->escapeString($_GET["name"], true, true);
		}

		// check if browse table
		if (isset($_GET["name"])) {

			// get row count in table by name
			$rowsCount = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM ".$tableName)); 
		} else {
			$rowsCount = 0;
		}
		
		// default select start id
		$startByRow = 0;
		
		// page items limit (read from config)
		$limitOnPage = $pageConfig->getValueByName("rowInTableLimit");

		// pager system calculator
		if (isset($_GET["name"]) && (isset($_GET["limit"]) && isset($_GET["startby"]))) {
 
			// get show limit form url
			$showLimit = $mysqlUtils->escapeString($_GET["limit"], true, true);
 
			// get start row form url
			$startByRow = $mysqlUtils->escapeString($_GET["startby"], true, true);
 
			// calculate next limit
			$nextLimit = (int) $showLimit + $limitOnPage;
 
			// calculate next start
			$nextStartByRow = (int) $startByRow + $limitOnPage;

			// calculate back limit
			$nextLimitBack = (int) $showLimit - $limitOnPage;

			// calculate back start row
			$nextStartByRowBack = (int) $startByRow - $limitOnPage;	
		}
		/////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////SUB-PANEL////////////////////////////////////
		if (!empty($_GET["name"]) or !empty($_GET["editor"]) or !empty($_GET["add"])) { // check if panel required
			echo '<ul class="breadcrumb bg-dark">'; // panel element

				// table selector button to panel
				echo '
					<li>
						<a class="selectorButton btn-small" href="?admin=dbBrowser"><strong>TABLES</strong></a>
					</li>';

				// delete all button to panel
				if (!empty($_GET["name"])) {
					echo '
						<li> 
							<a class="selectorButton btn-small" href="?admin=dbBrowser&delete='.$tableName.'&id=all"><strong>DELETE ALL '.$tableName.'</strong></a>
						</li>';
				}
 
				// new row button to panel
				if (!empty($_GET["name"])) {
					echo '
						<li> 
							<a class="selectorButton btn-small" href="?admin=dbBrowser&add='.$tableName.'"><strong>ADD</strong></a>
						</li>';
				}

				// back table button to panel
				if (!empty($_GET["add"])) {
					echo '
						<li> 
							<a class="selectorButton btn-small" href="?admin=dbBrowser&name='.$_GET["add"].'"><strong>BACK</strong></a>
						</li>';
				}

				// row count
				if (!empty($_GET["name"])) {
					echo '<li class="countTextInMenuR">'.$_GET["name"].' = '.$rowsCount["count"].' rows</li>';	
				} else {

					// editor title
					if (isset($_GET["editor"])) {
						echo '<li class="countTextInMenuR">Row editor</li>';	
					} 
					
					// addition title
					elseif (isset($_GET["add"])) {
						echo '<li class="countTextInMenuR">New row</li>';
					} 
					
					// default titile
					else {
						echo '<li class="countTextInMenuR">Database browser</li>';
					}
				}
			echo '</ul>';
		}
		/////////////////////////////////////////////////////////////////////////////////

		// table browser ////////////////////////////////////////////////////////////////
		if (isset($_GET["name"])) {

			// select table data
			$tableData = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM ".$tableName." LIMIT $startByRow, $limitOnPage");
 
			// create associative array from table data
			$tableDataAssoc = mysqli_fetch_array($tableData, MYSQLI_ASSOC);

			// select columns from table
			$tableColumns = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$tableName);
 
			// check if table empty
			if ($tableData->num_rows == 0) {
				echo"<h2 class=pageTitle>Table is empty</h2>";
			} 
			
			// table data
			else {

				// create table element
				echo '<div class="table-responsive"><table class="table table-dark">';
				echo '<thead><tr>'; 

				// mysql fields to table
				while($row = mysqli_fetch_array($tableColumns)) {
					echo "<th scope='col'>".$row['Field']."</th>";
				}

				echo "<th cope='col'>X</th>";
				
				// edit col to table
				if ($tableName != "visitors" && $tableName != "pastes" && $tableName != "hash_gen" && $tableName != "users") {
					echo "<th cope='col'>Edit</th>";
				}

				echo '</tr></thead>';

				// all rows to site
				foreach ($tableData as $data) {

					//////////////////////////////////CUSTOM-VIEW//////////////////////////////////
					// image uploader custom view
					if ($tableName == "image_uploader") {
						$data = [
							"id" => $data["id"],
							"imgSpec" => '<a href="?process=image&spec='.$data["imgSpec"].'" target="_blank">'.$data["imgSpec"].'</a>',
							"image" => "encrypted",
							"date" => $data["date"]
						];			
					}

					// paste custom view
					if ($tableName == "pastes") {
						$data = [
							"id" => $data["id"],
							"link" => '<a href="?process=paste&method=view&f='.$data["spec"].'" target="_blank">'.$data["spec"].'</a>',
							"content" => "hidden",
							"date" => $data["date"]
						];				
					}

					// users custom view
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

					// transfrom associative array to indexed array
					$dataOK = array_values($data);

					echo '<tbody><tr class="lineItem">';
					// table data
					for ($id = 0; $id <= 100; $id++) {
						if (!empty($dataOK[$id])) {
							echo "<th scope='row'>".$dataOK[$id]."</th>";	
						}
					}
			
					if(empty($data["base64"])) {
						echo '<td><a class="deleteLinkTodos" href="?admin=dbBrowser&delete='.$tableName.'&id='.$dataOK[0].'">X</a></td>';
						
						// edit link to row
						if ($tableName != "visitors" && $tableName != "pastes" && $tableName != "hash_gen" && $tableName != "users") {
							echo '<td><a class="text-warning deleteLinkTodos" href="?admin=dbBrowser&editor='.$tableName.'&id='.$dataOK[0].'">Edit</a></td>';
						}
					}
					echo '</tr></tbody>';
				}
				echo '</table>';
			}

			// log action to database 
			$mysqlUtils->logToMysql("Database", "User ".$adminController->getCurrentUsername()." viewed table $tableName");
		} 
		
		// delete function //////////////////////////////////////////////////////////////
		elseif (isset($_GET["delete"])) {

			// check if seted id
			if (isset($idGet)) {

				// check if user delete all form table
				if ($idGet == "all") {
					// delete all rows
					$mysqlUtils->insertQuery("DELETE FROM $deleteGet WHERE id=id");

					// reset auto increment
					$mysqlUtils->insertQuery("ALTER TABLE $deleteGet AUTO_INCREMENT = 1");
				} 
				
				// one row delete
				else {

					// delete one row
					$mysqlUtils->insertQuery("DELETE FROM $deleteGet WHERE id='$idGet'"); 
				}

				// log action to database
				$mysqlUtils->logToMysql("Database delete", "User ".$adminController->getCurrentUsername()." deleted item $idGet form table $deleteGet");


				// check if delete auto close
				if (isset($_GET["close"]) && $_GET["close"] == "y") {
					echo "<script>window.close();</script>";
				} 
				
				// redirect back
				else {
					// redirect to log reader
					if (isset($_GET["reader"])) {
						$urlUtils->jsRedirect("?admin=logReader&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
					} 
					
					// redirect to visitors system
					else if (isset($_GET["visitors"])) {
						$urlUtils->jsRedirect("?admin=visitors&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
					} 
					
					// redirect to database browser
					else {
						$urlUtils->jsRedirect("?admin=dbBrowser&name=$deleteGet&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
					}
				}

			} else {

				// check if site dev mode enabled
				if ($siteController->isSiteDevMode()) {

					// print error
					die("<h2 class=pageTitle>[DEV-MODE]:Error: query string id not found.<h2>");

				} else {

					// redirect to browser main page
					$urlUtils->jsRedirect("?admin=dbBrowser");
				}
			}
		}
		
		// editor function //////////////////////////////////////////////////////////////
		elseif (isset($_GET["editor"])) {

			// check if user submit edit form
			if (isset($_POST["submitEdit"])) {

				// select columns from selected table
				$resultEdit = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$editorGet);

				// update all fileds by id
				while($rowOK = mysqli_fetch_array($resultEdit)) { 

					// insert query
					$mysqlUtils->insertQuery("UPDATE $editorGet SET ".$rowOK["Field"]."='".$_POST[$rowOK["Field"]]."' WHERE id='$idGet'");
				} 

				// log action to mysql dsatabase 
				$mysqlUtils->logToMysql("Database edit", "User ".$adminController->getCurrentUsername()." edited item $idGet in table $editorGet");

				// flash status msg
				$alertController->flashSuccess("Row has saved!");
				
				// set final action
				if (isset($_GET["postby"]) and $_GET["postby"] == "todomanager") {
					// close editor after save
					echo "<script>window.close();</script>";
				} else {
					$urlUtils->jsRedirect("?admin=dbBrowser&name=".$editorGet."&limit=".$limitOnPage."&startby=0");
				}
			}

			// init table name
			$dbName = $mysqlUtils->escapeString($editorGet, true, true);

			// select columns from selected table
			$result = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$editorGet);

			// select all from selected table
			$resultAll = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM $editorGet WHERE id = '$idGet'");

			// migrate object to array
			$rowAll = mysqli_fetch_array($resultAll);

			echo "<br><br>";

			// create form
			if (isset($_GET["postby"]) and $_GET["postby"] == "todomanager") {
				echo '<form class="dbEditForm" action="?admin=dbBrowser&editor='.$editorGet.'&id='.$idGet.'&postby=todomanager" method="post">';
			} else {
				echo '<form class="dbEditForm" action="?admin=dbBrowser&editor='.$editorGet.'&id='.$idGet.'" method="post">';
			}
			echo '<p style="color: white; font-size: 20px;" class="loginFormTitle">Edit row with '.$idGet.'<p>';

				// print Fields
				while($row = mysqli_fetch_array($result)) {
					echo '<p class="textInputTitle">'.$row['Field'].'</p>';
					echo '<input class="textInput bg-dark" type="text" name="'.$row['Field'].'" value="'.$rowAll[$row['Field']].'"><br>';
				}

			// end form
			echo '<input class="inputButton bg-dark" type="submit" name="submitEdit" value="Edit"></form>';
		}
		
		// addition function /////////////////////////////////////////////////////////////
		elseif (isset($_GET["add"])) {
			
			// select columns add table
			$selectedColumns = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW COLUMNS FROM ".$addGet);

			// check if save submited
			if (isset($_POST["submitSave"])) {

				////////////////////-COLUMNS-LIST-BUILDER-/////////////////////
				// create columns list
				$columnsBuilder = "";

				// build columns list
				while($row = mysqli_fetch_array($selectedColumns)) {

					// prevent id valud build
					if (strtolower($row["Field"]) != "id") {
						$columnsBuilder = $columnsBuilder.", `".$row["Field"]."`";
					}
				}

				// remove invalid character from columns list
				$columnsBuilder = substr($columnsBuilder, 1);
				///////////////////////////////////////////////////////////////
 
				/////////////////////-VALUES-LIST-BUILDER-/////////////////////
				// create values list string
				$valuesBuilder = "";

				// build values list
				foreach ($_POST as $post) {

					// check if value not SAVE (button post remove)
					if ($post != "SAVE") {
						$valuesBuilder = $valuesBuilder.", '".$post."'";
					}
						
				}

				// remove invalid character from values
				$valuesBuilder = substr($valuesBuilder, 1);
				///////////////////////////////////////////////////////////////

				// build query
				$query = "INSERT INTO `".$addGet."`(".$columnsBuilder.") VALUES (".$valuesBuilder.")";

				// insert query to database
				$mysqlUtils->insertQuery($query);

				// flash alert
				$alertController->flashSuccess("New item has saved!");

				// log to database
				$mysqlUtils->logToMysql("Database insert", "User ".$adminController->getCurrentUsername()." add new row to $addGet");

				// redirect back to table reader
				$urlUtils->jsRedirect("?admin=dbBrowser&name=$addGet&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
			} 
			
			// print add form
			else {

				// create add form
				echo '<form class="dbEditForm" action="?admin=dbBrowser&add='.$addGet.'" method="post">';

				// print from title
				echo '<p class="textInputTitle">New item</p>';

				// fields
				while($row = mysqli_fetch_array($selectedColumns)) {
					if (strtolower($row["Field"]) != "id") {
						echo '<input class="textInput bg-dark" type="text" name="'.$row["Field"].'" placeholder="'.$row["Field"].'"><br>';
					}
				}

				// form submit button
				echo '<input class="inputButton bg-dark" type="submit" name="submitSave" value="SAVE">';

				// form end
				echo '</form>';
			}
		} 
		
		// table selector ///////////////////////////////////////////////////////////////
		else {

			// page title
			echo '<h2 class="pageTitle">Select table</h2>';
 
			// select box element
			echo '<div><ol><br>';
 
			// get tables object from database
			$tables = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SHOW TABLES");

			// print all tables links
			while ($row = mysqli_fetch_assoc($tables)) {
				echo "<a class='dbBrowserSelectLink' href=?admin=dbBrowser&name=".$row["Tables_in_".$pageConfig->getValueByName("basedb")]."&limit=".$limitOnPage."&startby=0>".$row["Tables_in_".$pageConfig->getValueByName("basedb")]."</a><br><br>";
			}

			// end of select box element
			echo '</ol></div>';

			// log action to database 
			$mysqlUtils->logToMysql("Database list", "User ".$adminController->getCurrentUsername()." viewed database list");
		}

		///////////////////////////////////////PAGER-BUTTONS///////////////////////////////////////
		if (isset($_GET["name"]) && (isset($_GET["limit"]) and isset($_GET["startby"]))) {
 
			// check if page buttons can show
			if (($showLimit > $limitOnPage) or ($tableData->num_rows == $limitOnPage)) {
				echo '<div class="pageButtonBox">'; //Create buttons element area
			}
		
			// print back button if user in next page
			if ($showLimit > $limitOnPage) {
				echo '<br><a class="backPageButton" href=?admin=dbBrowser&name='.$_GET["name"].'&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
			}

			// print next button if user on start page and can see next items
			if ($tableData->num_rows == $limitOnPage) {
				echo '<br><a class="backPageButton" href=?admin=dbBrowser&name='.$_GET["name"].'&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
			}
	
			// check if page buttons can show
			if (($showLimit > $limitOnPage) or ($tableData->num_rows == $limitOnPage)) {
				echo '</div><br>'; // close buttons element area
			}
		}
		///////////////////////////////////////////////////////////////////////////////////////////	
	}
?>
