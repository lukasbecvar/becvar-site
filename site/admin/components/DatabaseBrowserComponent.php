<div class="admin-panel">
<?php // admin database table browser (NEEDS A REFACTOR!!!)

	// check if user is owner
	if (!$user_manager->is_user_Owner()) {
		echo"<h2 class=page-title>Sorry you dont have permission to this page</h2>";
	} else {

		////////////////////////////////PAGE-SITES-VALUES////////////////////////////////

		// check if id seted
		if (isset($_GET["id"])) {

			// get id from query string
			$id_get = $escape_utils->special_chars_strip($_GET["id"]);
		}

		// check if delete seted
		if (isset($_GET["delete"])) {

			// get delete from query string
			$delete_get = $escape_utils->special_chars_strip($_GET["delete"]);
		}

		// check if editor seted
		if (isset($_GET["editor"])) {

			// get editor from query string
			$editor_get = $escape_utils->special_chars_strip($_GET["editor"]);
		}

		// check if add seted
		if (isset($_GET["add"])) {

			// get add from query string
			$add_get = $escape_utils->special_chars_strip($_GET["add"]);
		}

		// check if browse table
		if (isset($_GET["name"])) {

			// get escaped table name
			$table_name = $escape_utils->special_chars_strip($_GET["name"]);
		}

		// check if browse table
		if (isset($_GET["name"])) {

			// get row count in table by name
			$rows_count = $mysql->fetch("SELECT id FROM ".$table_name);
		} else {
			$rows_count = 0;
		}
		
		// default select start id
		$start_by_row = 0;
		
		// page items limit (read from config)
		$limit_on_page = $config->get_value("row-in-table-limit");

		// pager system calculator
		if (isset($_GET["name"]) && (isset($_GET["limit"]) && isset($_GET["startby"]))) {
 
			// get show limit form url
			$show_limit = $escape_utils->special_chars_strip($_GET["limit"]);
 
			// get start row form url
			$start_by_row = $escape_utils->special_chars_strip($_GET["startby"]);
 
			// calculate next limit
			$next_limit = (int) $show_limit + $limit_on_page;
 
			// calculate next start
			$next_start_by_row = (int) $start_by_row + $limit_on_page;

			// calculate back limit
			$next_limit_Back = (int) $show_limit - $limit_on_page;

			// calculate back start row
			$next_start_by_rowBack = (int) $start_by_row - $limit_on_page;	
		}
		/////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////SUB-PANEL////////////////////////////////////
		if (!empty($_GET["name"]) or !empty($_GET["editor"]) or !empty($_GET["add"])) { // check if panel required
			echo '<ul class="breadcrumb">'; // panel element

				// table selector button to panel
				echo '
					<li>
						<a class="selector-button btn-small" href="?admin=dbBrowser"><strong><i class="fa fa-arrow-left" aria-hidden="true"></i></strong></a>
					</li>';

				// delete all button to panel
				if (!empty($_GET["name"])) {
					echo '
						<li> 
							<a class="selector-button btn-small" href="?admin=dbBrowser&delete='.$table_name.'&id=all"><strong><i class="fa fa-trash" aria-hidden="true"></i> '.$table_name.'</strong></a>
						</li>';
				}
 
				// new row button to panel
				if (!empty($_GET["name"])) {
					echo '
						<li> 
							<a class="selector-button btn-small" href="?admin=dbBrowser&add='.$table_name.'"><strong>NEW</strong></a>
						</li>';
				}

				// new row button to panel
				if (!empty($_GET["name"])) {
					if ($_GET["name"] == "projects") {
						echo '
							<li>
								<a class="selector-button btn-small" href="?admin=projectsReload"><strong><i class="fas fa-sync"></i></strong></a>
							</li>';
					}
				}

				// back table button to panel
				if (!empty($_GET["add"])) {
					echo '
						<li> 
							<a class="selector-button btn-small" href="?admin=dbBrowser&name='.$_GET["add"].'"><strong>BACK</strong></a>
						</li>';
				}

				// row count
				if (!empty($_GET["name"])) {
					echo '<li class="count-text-in-menuR">'.$_GET["name"].' = '.count($rows_count).' rows</li>';	
				} else {

					// editor title
					if (isset($_GET["editor"])) {
						echo '<li class="count-text-in-menuR">Row editor</li>';	
					} 
					
					// addition title
					elseif (isset($_GET["add"])) {
						echo '<li class="count-text-in-menuR">New row</li>';
					} 
					
					// default titile
					else {
						echo '<li class="count-text-in-menuR">Database browser</li>';
					}
				}
			echo '</ul>';
		}
		/////////////////////////////////////////////////////////////////////////////////

		// table browser ////////////////////////////////////////////////////////////////
		if (isset($_GET["name"])) {

			// select table data
			$table_data = $mysql->connect()->query("SELECT * FROM ".$table_name." LIMIT $start_by_row, $limit_on_page")->fetchAll(\PDO::FETCH_ASSOC);

			// select columns from table
			$table_columns = $mysql->connect()->query("SHOW COLUMNS FROM ".$table_name)->fetchAll(\PDO::FETCH_ASSOC);
 
			// check if table empty
			if (count($table_data) == 0) {
				echo"<h2 class=page-title>Table is empty</h2>";
			} 
			
			// table data
			else {

				// create table element
				echo '<div class="table-responsive"><table class="table table-dark">';
				echo '<thead><tr class="line-item">'; 

				// mysql fields to table
				foreach($table_columns as $row) {
					echo "<th scope='col'>".$row['Field']."</th>";
				}

				echo "<th cope='col'>X</th>";
				
				// edit col to table
				if ($table_name != "visitors" && $table_name != "pastes" && $table_name != "hash_gen" && $table_name != "users") {
					echo "<th cope='col'>Edit</th>";
				}

				echo '</tr></thead>';

				// all rows to site
				foreach ($table_data as $data) {

					//////////////////////////////////CUSTOM-VIEW//////////////////////////////////
					// image uploader custom view
					if ($table_name == "image_uploader") {
						$data = [
							"id" => $data["id"],
							"img_spec" => '<a href="?process=image&spec='.$data["img_spec"].'" target="_blank">'.$data["img_spec"].'</a>',
							"image" => "encrypted",
							"date" => $data["date"]
						];			
					}

					// paste custom view
					if ($table_name == "pastes") {
						$data = [
							"id" => $data["id"],
							"link" => '<a href="?process=paste&method=view&f='.$data["spec"].'" target="_blank">'.$data["spec"].'</a>',
							"content" => "hidden",
							"date" => $data["date"]
						];				
					}

					// users custom view
					if ($table_name == "users") {
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
					$data_ok = array_values($data);

					echo '<tbody><tr class="line-item">';
					
					// table data
					for ($id = 0; $id <= 50; $id++) {
						if (!empty($data_ok[$id])) {
							echo "<th scope='row'>".$data_ok[$id]."</th>";	
						}
					}
			
					if(empty($data["base64"])) {
						echo '<td><a class="delete-link-todos" href="?admin=dbBrowser&delete='.$table_name.'&id='.$data_ok[0].'">X</a></td>';
						
						// edit link to row
						if ($table_name != "visitors" && $table_name != "pastes" && $table_name != "hash_gen" && $table_name != "users") {
							echo '<td><a class="text-warning delete-link-todos" href="?admin=dbBrowser&editor='.$table_name.'&id='.$data_ok[0].'">Edit</a></td>';
						}
					}
					echo '</tr></tbody>';
				}
				echo '</table>';
			}

			// log action to database 
			$mysql->log("database", "user ".$user_manager->get_username()." viewed table $table_name");
		} 
		
		// delete function //////////////////////////////////////////////////////////////
		elseif (isset($_GET["delete"])) {

			// check if seted id
			if (isset($id_get)) {

				// check if user delete all form table
				if ($id_get == "all") {

					// include delete all confirmation
					if ($site_manager->get_query_string("confirm") != "yes") {
						include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/DatabaseDeleteConfirmationBox.php');
					} 
					
					// delete all
					else {
						// check if confirm selected (only for delete all)
						if ($site_manager->get_query_string("confirm") == "yes") {
							// delete all rows
							$mysql->insert("DELETE FROM $delete_get WHERE id=id");

							// reset auto increment
							$mysql->insert("ALTER TABLE $delete_get AUTO_INCREMENT = 1");
						}
					}
				} 
				
				// one row delete
				else {

					// delete one row
					$mysql->insert("DELETE FROM $delete_get WHERE id='$id_get'"); 
				}

				// log action to database
				$mysql->log("database", "user ".$user_manager->get_username()." deleted item $id_get form table $delete_get");


				// check if delete auto close
				if (isset($_GET["close"]) && $_GET["close"] == "y") {
					echo "<script>window.close();</script>";
				} 
				
				// redirect back
				else {
					// redirect to log reader
					if (isset($_GET["reader"])) {
						$url_utils->js_redirect("?admin=logReader&limit=".$config->get_value("row-in-table-limit")."&startby=0");
					} 
					
					// redirect to visitors system
					else if (isset($_GET["visitors"])) {
						$url_utils->js_redirect("?admin=visitors&limit=".$config->get_value("row-in-table-limit")."&startby=0");
					} 
					
					// redirect to database browser
					else {

						// check if delete all redirect
						if ($id_get == "all") {

							// check if confirmation is used
							if ($site_manager->get_query_string("confirm") == "yes") {
								$url_utils->js_redirect("?admin=dbBrowser&name=$delete_get&limit=".$config->get_value("row-in-table-limit")."&startby=0");
							}

						} else {
							$url_utils->js_redirect("?admin=dbBrowser&name=$delete_get&limit=".$config->get_value("row-in-table-limit")."&startby=0");
						}
					}
				}

			} else {

				// check if site dev mode enabled
				if ($site_manager->is_dev_mode()) {

					// print error
					die("<h2 class=page-title>[DEV-MODE]:Error: query string id not found.<h2>");

				} else {

					// redirect to browser main page
					$url_utils->js_redirect("?admin=dbBrowser");
				}
			}
		}
		
		// editor function //////////////////////////////////////////////////////////////
		elseif (isset($_GET["editor"])) {

			// check if user submit edit form
			if (isset($_POST["submitEdit"])) {

				// select columns from selected table
				$result_edit = $mysql->fetch("SHOW COLUMNS FROM ".$editor_get);

				// update all fileds by id
				foreach($result_edit as $rowOK) { 

					// insert query
					$mysql->insert("UPDATE $editor_get SET ".$rowOK["Field"]."='".$_POST[$rowOK["Field"]]."' WHERE id='$id_get'");
				} 

				// log action to mysql dsatabase 
				$mysql->log("database", "user ".$user_manager->get_username()." edited item $id_get in table $editor_get");

				// flash status msg
				$alert_manager->flash_success("Row has saved!");
				
				// set final action
				if (isset($_GET["postby"]) and $_GET["postby"] == "todomanager") {
					// close editor after save
					echo "<script>window.close();</script>";
				} else {
					$url_utils->js_redirect("?admin=dbBrowser&name=".$editor_get."&limit=".$limit_on_page."&startby=0");
				}
			}

			// select columns from selected table
			$result = $mysql->fetch("SHOW COLUMNS FROM ".$editor_get);

			// select all from selected table
			$result_all = $mysql->connect()->query("SELECT * FROM $editor_get WHERE id = '$id_get'");

			// migrate object to array
			$row_all = $result_all->fetchAll(\PDO::FETCH_ASSOC);

			echo "<br><br>";

			// create form
			if (isset($_GET["postby"]) and $_GET["postby"] == "todomanager") {
				echo '<form class="db-edit-form dark-table" action="?admin=dbBrowser&editor='.$editor_get.'&id='.$id_get.'&postby=todomanager" method="post">';
			} else {
				echo '<form class="db-edit-form dark-table" action="?admin=dbBrowser&editor='.$editor_get.'&id='.$id_get.'" method="post">';
			}
			echo '<p style="color: white; font-size: 20px;" class="login-form-title">Edit row with '.$id_get.'<p>';


				// print Fields
				foreach($result as $row) {
					echo '<p class="text-input-title">'.$row['Field'].'</p>';
					echo '<input class="text-input" type="text" name="'.$row['Field'].'" value="'.$row_all[0][$row['Field']].'"><br>';
				}

			// end form
			echo '<input class="input-button" type="submit" name="submitEdit" value="Edit"></form>';
		}
		
		// addition function /////////////////////////////////////////////////////////////
		elseif (isset($_GET["add"])) {
			
			// select columns add table
			$selected_columns = $mysql->fetch("SHOW COLUMNS FROM ".$add_get);

			// check if save submited
			if (isset($_POST["submitSave"])) {

				////////////////////-COLUMNS-LIST-BUILDER-/////////////////////
				// create columns list
				$columns_builder = "";

				// build columns list
				foreach($selected_columns as $row) {

					// prevent id valud build
					if (strtolower($row["Field"]) != "id") {
						$columns_builder = $columns_builder.", `".$row["Field"]."`";
					}
				}

				// remove invalid character from columns list
				$columns_builder = substr($columns_builder, 1);
				///////////////////////////////////////////////////////////////
 
				/////////////////////-VALUES-LIST-BUILDER-/////////////////////
				// create values list string
				$values_builder = "";

				// build values list
				foreach ($_POST as $post) {

					// check if value not SAVE (button post remove)
					if ($post != "SAVE") {
						$values_builder = $values_builder.", '".$post."'";
					}
						
				}

				// remove invalid character from values
				$values_builder = substr($values_builder, 1);
				///////////////////////////////////////////////////////////////

				// build query
				$query = "INSERT INTO `".$add_get."`(".$columns_builder.") VALUES (".$values_builder.")";

				// insert query to database
				$mysql->insert($query);

				// flash alert
				$alert_manager->flash_success("New item has saved!");

				// log to database
				$mysql->log("database", "user ".$user_manager->get_username()." add new row to $add_get");

				// redirect back to table reader
				$url_utils->js_redirect("?admin=dbBrowser&name=$add_get&limit=".$config->get_value("row-in-table-limit")."&startby=0");
			} 
			
			// print add form
			else {

				// create add form
				echo '<form class="db-edit-form dark-table" action="?admin=dbBrowser&add='.$add_get.'" method="post">';

				// print from title
				echo '<p class="text-input-title">New item</p><br>';

				// fields
				foreach($selected_columns as $row) {
					if (strtolower($row["Field"]) != "id") {
						echo '<input class="text-input" type="text" name="'.$row["Field"].'" placeholder="'.$row["Field"].'"><br>';
					}
				}

				// form submit button
				echo '<input class="input-button" type="submit" name="submitSave" value="SAVE">';

				// form end
				echo '</form>';
			}
		} 
		
		// table selector ///////////////////////////////////////////////////////////////
		else {

			// page title
			echo '<h2 class="page-title">Select table</h2>';
 
			// select box element
			echo '<div><ol><br>';
 
			// get tables object from database
			$tables = $mysql->fetch("SHOW TABLES");

			// print all tables links
			foreach ($tables as $row) {

				echo "<a class='db-browser-select-link' href=?admin=dbBrowser&name=".$row["Tables_in_".$config->get_value("database-name")]."&limit=".$limit_on_page."&startby=0>".$row["Tables_in_".$config->get_value("database-name")]."</a><br><br>";
			}

			// end of select box element
			echo '</ol></div>';

			// log action to database 
			$mysql->log("database", "User ".$user_manager->get_username()." viewed database list");
		}

		///////////////////////////////////////PAGER-BUTTONS///////////////////////////////////////
		if (isset($_GET["name"]) && (isset($_GET["limit"]) and isset($_GET["startby"]))) {
 
			// check if page buttons can show
			if (($show_limit > $limit_on_page) or (count($table_data) == $limit_on_page)) {
				echo '<div class="page-button-box">'; //Create buttons element area
			}
		
			// print back button if user in next page
			if ($show_limit > $limit_on_page) {
				echo '<br><a class="back-page-button" href=?admin=dbBrowser&name='.$_GET["name"].'&limit='.$next_limit_Back.'&startby='.$next_start_by_rowBack.'>Back</a><br>';
			}

			// print next button if user on start page and can see next items
			if (count($table_data) == $limit_on_page) {
				echo '<br><a class="back-page-button" href=?admin=dbBrowser&name='.$_GET["name"].'&limit='.$next_limit.'&startby='.$next_start_by_row.'>Next</a><br>';	
			}
	
			// check if page buttons can show
			if (($show_limit > $limit_on_page) or (count($table_data) == $limit_on_page)) {
				echo '</div><br>'; // close buttons element area
			}
		}
		///////////////////////////////////////////////////////////////////////////////////////////	
	}
?>