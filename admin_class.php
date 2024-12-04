<?php
session_start();
ini_set('display_errors', 1);
class Action
{
	private $db;

	public function __construct()
	{
		ob_start();
		include 'db_connect.php';

		$this->db = $conn;
	}
	function __destruct()
	{
		$this->db->close();
		ob_end_flush();
	}

	function login()
{
    extract($_POST);
    // Define role mappings
    $type = array("", "users", "users", "student_list", "faculty_list");
    $type2 = array("", "admin", "dean", "student", "faculty");

    if ($login == 1 || $login == 2) {
        // For admin or dean, check 'users' table with 'type'
        $user_type = ($login == 1) ? 1 : 2; // 1 for admin, 2 for dean
        $qry = $this->db->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE email = '$email' AND password = MD5('$password') AND type = $user_type");
    } elseif ($login == 3 || $login == 4) {
        // For student or faculty
        $qry = $this->db->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM {$type[$login]} WHERE email = '$email' AND password = MD5('$password')");
    } else {
        return 2; // Invalid login type
    }

    if ($qry->num_rows > 0) {
        $user = $qry->fetch_assoc(); // Fetch the user data once

        // Store user data in session
        foreach ($user as $key => $value) {
            if ($key != 'password' && !is_numeric($key))
                $_SESSION['login_' . $key] = $value;
        }
        $_SESSION['login_type'] = $login;
        $_SESSION['login_view_folder'] = $type2[$login] . '/';

        // Store department ID if the user is a Dean
        if ($login == 2) {
            $_SESSION['login_department_id'] = $user['department_id'];
        }

        // Load academic settings
        $academic = $this->db->query("SELECT * FROM academic_list WHERE is_default = 1");
        if ($academic->num_rows > 0) {
            $academic_data = $academic->fetch_assoc();
            foreach ($academic_data as $k => $v) {
                if (!is_numeric($k))
                    $_SESSION['academic'][$k] = $v;
            }
        }
        return 1;
    } else {
        return 2;
    }
}

	

	function checkLogin($email, $password, $role, $conn)
	{
		// Hash the password with MD5 as it matches the stored format
		$hashed_password = md5($password);

		// Prepare SQL query based on the role
		$sql = "";
		if ($role == "admin") {
			$sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$hashed_password' AND role = '$role'";
		} else if ($role == "student") {
			$sql = "SELECT * FROM student WHERE email = '$email' AND password = '$hashed_password' AND role = '$role'";
		} else if ($role == "faculty") {
			$sql = "SELECT * FROM faculty WHERE email = '$email' AND password = '$hashed_password' AND role = '$role'";
		} else {
			return false; // Invalid role
		}

		// Execute the query
		$result = mysqli_query($conn, $sql);

		// Check if a row was returned
		if (mysqli_num_rows($result) == 1) {
			return true;
		} else {
			return false;
		}
	}



	function logout()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function login2()
	{
		extract($_POST);
		$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM students where student_code = '" . $student_code . "' ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['rs_' . $key] = $value;
			}
			return 1;
		} else {
			return 3;
		}
	}
	function save_user()
	{
		extract($_POST);
		$data = "";
		// Add department_id if user is a dean
		if (isset($_POST['department_id']) && $_POST['type'] == 4) {
			$department_id = $_POST['department_id'];
			$data .= ", department_id = '$department_id'";
		}
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (!empty($password)) {
			$data .= ", password=md5('$password') ";
		}
		$check = $this->db->query("SELECT * FROM users WHERE email ='$email' " . (!empty($id) ? " AND id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users SET $data");
		} else {
			$save = $this->db->query("UPDATE users SET $data WHERE id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function update_user_password(){
		extract($_POST);
		$type = array("","users","faculty_list","student_list",);
		
		// Check if password is not empty
		if(!empty($password) && !empty($id)){
			// Update only the password for the specific user
			$save = $this->db->query("UPDATE {$type[$_SESSION['login_type']]} set password=md5('$password') where id = $id");
			
			if($save){
				return 1; // Success
			}
		}
		
		return 0; // Failed to update
	}
	function signup()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass')) && !is_numeric($k)) {
				if ($k == 'password') {
					if (empty($v))
						continue;
					$v = md5($v);
				}
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM users where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users set $data");
		} else {
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if ($save) {
			if (empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if (!in_array($key, array('id', 'cpass', 'password')) && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			$_SESSION['login_id'] = $id;
			if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
				$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function update_user()
	{
		extract($_POST);
		$data = "";
		$type = array("", "users", "faculty_list", "student_list");
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'table', 'password')) && !is_numeric($k)) {

				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM {$type[$_SESSION['login_type']]} where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (!empty($password))
			$data .= " ,password=md5('$password') ";
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO {$type[$_SESSION['login_type']]} set $data");
		} else {
			echo "UPDATE {$type[$_SESSION['login_type']]} set $data where id = $id";
			$save = $this->db->query("UPDATE {$type[$_SESSION['login_type']]} set $data where id = $id");
		}

		if ($save) {
			foreach ($_POST as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
				$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}
	function delete_user()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = " . $id);
		if ($delete)
			return 1;
	}
	function save_system_settings()
	{
		extract($_POST);
		$data = '';
		foreach ($_POST as $k => $v) {
			if (!is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if ($_FILES['cover']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'], '../assets/uploads/' . $fname);
			$data .= ", cover_img = '$fname' ";
		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if ($chk->num_rows > 0) {
			$save = $this->db->query("UPDATE system_settings set $data where id =" . $chk->fetch_array()['id']);
		} else {
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if ($save) {
			foreach ($_POST as $k => $v) {
				if (!is_numeric($k)) {
					$_SESSION['system'][$k] = $v;
				}
			}
			if ($_FILES['cover']['tmp_name'] != '') {
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image()
	{
		extract($_FILES['file']);
		if (!empty($tmp_name)) {
			$fname = strtotime(date("Y-m-d H:i")) . "_" . (str_replace(" ", "-", $name));
			$move = move_uploaded_file($tmp_name, 'assets/uploads/' . $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path = explode('/', $_SERVER['PHP_SELF']);
			$currentPath = '/' . $path[1];
			if ($move) {
				return $protocol . '://' . $hostName . $currentPath . '/assets/uploads/' . $fname;
			}
		}
	}
	function save_subject()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM subject_list where code = '$code' and id != '{$id}' ")->num_rows;
		if ($chk > 0) {
			return 2;
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO subject_list set $data");
		} else {
			$save = $this->db->query("UPDATE subject_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_subject()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM subject_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_class()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM class_list where (" . str_replace(",", 'and', $data) . ") and id != '{$id}' ")->num_rows;
		if ($chk > 0) {
			return 2;
		}
		if (isset($user_ids)) {
			$data .= ", user_ids='" . implode(',', $user_ids) . "' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO class_list set $data");
		} else {
			$save = $this->db->query("UPDATE class_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_class()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM class_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_academic()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM academic_list where (" . str_replace(",", 'and', $data) . ") and id != '{$id}' ")->num_rows;
		if ($chk > 0) {
			return 2;
		}
		$hasDefault = $this->db->query("SELECT * FROM academic_list where is_default = 1")->num_rows;
		if ($hasDefault == 0) {
			$data .= " , is_default = 1 ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO academic_list set $data");
		} else {
			$save = $this->db->query("UPDATE academic_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_academic()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM academic_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function make_default()
	{
		extract($_POST);
		$update = $this->db->query("UPDATE academic_list set is_default = 0");
		$update1 = $this->db->query("UPDATE academic_list set is_default = 1 where id = $id");
		$qry = $this->db->query("SELECT * FROM academic_list where id = $id")->fetch_array();
		if ($update && $update1) {
			foreach ($qry as $k => $v) {
				if (!is_numeric($k))
					$_SESSION['academic'][$k] = $v;
			}

			return 1;
		}
	}
	function save_criteria()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$chk = $this->db->query("SELECT * FROM criteria_list where (" . str_replace(",", 'and', $data) . ") and id != '{$id}' ")->num_rows;
		if ($chk > 0) {
			return 2;
		}

		if (empty($id)) {
			$lastOrder = $this->db->query("SELECT * FROM criteria_list order by abs(order_by) desc limit 1");
			$lastOrder = $lastOrder->num_rows > 0 ? $lastOrder->fetch_array()['order_by'] + 1 : 0;
			$data .= ", order_by='$lastOrder' ";
			$save = $this->db->query("INSERT INTO criteria_list set $data");
		} else {
			$save = $this->db->query("UPDATE criteria_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_criteria()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM criteria_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_criteria_order()
	{
		extract($_POST);
		$data = "";
		foreach ($criteria_id as $k => $v) {
			$update[] = $this->db->query("UPDATE criteria_list set order_by = $k where id = $v");
		}
		if (isset($update) && count($update)) {
			return 1;
		}
	}

	function save_question()
{
    // Access $_POST variables directly
    $question = isset($_POST['question']) ? $this->db->real_escape_string($_POST['question']) : '';
    $criteria_id = isset($_POST['criteria_id']) ? (int)$_POST['criteria_id'] : 0;
    $academic_id = isset($_POST['academic_id']) ? (int)$_POST['academic_id'] : 0;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    // Prepare data
    $data = "question='$question', criteria_id='$criteria_id', academic_id='$academic_id'";

    if (empty($id)) {
        // Get the last order
        $lastOrderQry = $this->db->query("SELECT * FROM question_list WHERE academic_id = $academic_id ORDER BY ABS(order_by) DESC LIMIT 1");
        $lastOrder = $lastOrderQry->num_rows > 0 ? (int)$lastOrderQry->fetch_array()['order_by'] + 1 : 1;
        $data .= ", order_by='$lastOrder'";
        $save = $this->db->query("INSERT INTO question_list SET $data");
    } else {
        $save = $this->db->query("UPDATE question_list SET $data WHERE id = $id");
    }

    if ($save) {
        return 1;
    } else {
        // Log the error for debugging
        error_log("MySQL Error: " . $this->db->error);
        return 0;
    }
}

	function delete_question()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM question_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_question_order()
	{
		extract($_POST);
		$data = "";
		foreach ($qid as $k => $v) {
			$update[] = $this->db->query("UPDATE question_list set order_by = $k where id = $v");
		}
		if (isset($update) && count($update)) {
			return 1;
		}
	}
	function save_faculty()
	{
		extract($_POST);
		$data = "";
	
		// Get the current user's role and department
		$user_type = $_SESSION['login_type'];
		$user_department_id = isset($_SESSION['login_department_id']) ? $_SESSION['login_department_id'] : null;
	
		// If the user is a Dean, set the department_id to their own and prevent changing it
		if ($user_type == 2) {
			$department_id = $user_department_id;
		}
	
		// Build the data string
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'password', 'department_id')) && !is_numeric($k)) {
				// Sanitize the input to prevent SQL injection
				$v = $this->db->real_escape_string($v);
	
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
	
		// Add department_id to data
		if (isset($department_id)) {
			$data .= ", department_id='$department_id' ";
		} elseif (isset($_POST['department_id'])) {
			// For admin or other roles, use the provided department_id
			$data .= ", department_id='{$_POST['department_id']}' ";
		}
	
		if (!empty($password)) {
			$password = $this->db->real_escape_string($password);
			$data .= ", password=md5('$password') ";
		}
	
		// Check for unique email
		$email = $this->db->real_escape_string($email);
		$check = $this->db->query("SELECT * FROM faculty_list WHERE email ='$email' " . (!empty($id) ? "AND id != {$id}" : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
	
		// Check for unique school ID
		$school_id = $this->db->real_escape_string($school_id);
		$check = $this->db->query("SELECT * FROM faculty_list WHERE school_id ='$school_id' " . (!empty($id) ? "AND id != {$id}" : ''))->num_rows;
		if ($check > 0) {
			return 3;
			exit;
		}
	
		// Handle file upload
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			if ($move) {
				$data .= ", avatar = '$fname' ";
			}
		}
	
		if (empty($id)) {
			// Insert new faculty
			$save = $this->db->query("INSERT INTO faculty_list SET $data");
		} else {
			// Update existing faculty
			// If the user is a Dean, ensure they can only update faculties in their department
			if ($user_type == 2) {
				$save = $this->db->query("UPDATE faculty_list SET $data WHERE id = $id AND department_id = '$user_department_id'");
			} else {
				// Admin or other roles can update any faculty
				$save = $this->db->query("UPDATE faculty_list SET $data WHERE id = $id");
			}
		}
	
		if ($save) {
			return 1;
		} else {
			// Log the error or return an error message
			return 0;
		}
	}
	

	function delete_faculty()
	{
		extract($_POST);
		$user_type = $_SESSION['login_type'];
		$user_department_id = isset($_SESSION['login_department_id']) ? $_SESSION['login_department_id'] : null;
	
		if ($user_type == 2) {
			// Dean can only delete faculties in their department
			$delete = $this->db->query("DELETE FROM faculty_list WHERE id = $id AND department_id = '$user_department_id'");
		} else {
			// Admin or other roles can delete any faculty
			$delete = $this->db->query("DELETE FROM faculty_list WHERE id = $id");
		}
	
		if ($delete)
			return 1;
	}
	

	function save_department(){
        extract($_POST);
        $data = " name = '$name' ";
        if (!empty($description)) {
            $data .= ", description = '$description' ";
        }

        // Check if department already exists
        $check = $this->db->query("SELECT * FROM department_list WHERE name = '$name' ".(!empty($id) ? " AND id != $id" : ""));
        if($check->num_rows > 0){
            return 2; // Department already exists
        }

        if (empty($id)) {
            $save = $this->db->query("INSERT INTO department_list SET $data");
        } else {
            $save = $this->db->query("UPDATE department_list SET $data WHERE id = $id");
        }

        if ($save)
            return 1;
    }

    function delete_department(){
        extract($_POST);
        $delete = $this->db->query("DELETE FROM department_list WHERE id = $id");
        if ($delete)
            return 1;
    }

	function save_student()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (!empty($password)) {
			$data .= ", password=md5('$password') ";
		}
		$check = $this->db->query("SELECT * FROM student_list where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO student_list set $data");
		} else {
			$save = $this->db->query("UPDATE student_list set $data where id = $id");
		}

		if ($save) {
			return 1;
		}
	}
	function delete_student()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM student_list where id = " . $id);
		if ($delete)
			return 1;
	}
	function save_task()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id')) && !is_numeric($k)) {
				if ($k == 'description')
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO task_list set $data");
		} else {
			$save = $this->db->query("UPDATE task_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_task()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_progress()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id')) && !is_numeric($k)) {
				if ($k == 'progress')
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (!isset($is_complete))
			$data .= ", is_complete=0 ";
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO task_progress set $data");
		} else {
			$save = $this->db->query("UPDATE task_progress set $data where id = $id");
		}
		if ($save) {
			if (!isset($is_complete))
				$this->db->query("UPDATE task_list set status = 1 where id = $task_id ");
			else
				$this->db->query("UPDATE task_list set status = 2 where id = $task_id ");
			return 1;
		}
	}
	function delete_progress()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_progress where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_restriction()
	{
		extract($_POST);
		$filtered = implode(",", array_filter($rid));
		if (!empty($filtered))
			$this->db->query("DELETE FROM restriction_list where id not in ($filtered) and academic_id = $academic_id");
		else
			$this->db->query("DELETE FROM restriction_list where  academic_id = $academic_id");
		foreach ($rid as $k => $v) {
			$data = " academic_id = $academic_id ";
			$data .= ", faculty_id = {$faculty_id[$k]} ";
			$data .= ", class_id = {$class_id[$k]} ";
			$data .= ", subject_id = {$subject_id[$k]} ";
			if (empty($v)) {
				$save[] = $this->db->query("INSERT INTO restriction_list set $data ");
			} else {
				$save[] = $this->db->query("UPDATE restriction_list set $data where id = $v ");
			}
		}
		return 1;
	}
	function save_evaluation()
	{
		extract($_POST);
		$data = " student_id = {$_SESSION['login_id']} ";
		$data .= ", academic_id = $academic_id ";
		$data .= ", subject_id = $subject_id ";
		$data .= ", class_id = $class_id ";
		$data .= ", restriction_id = $restriction_id ";
		$data .= ", faculty_id = $faculty_id ";
		$save = $this->db->query("INSERT INTO evaluation_list set $data");
		if ($save) {
			$eid = $this->db->insert_id;
			foreach ($qid as $k => $v) {
				$data = " evaluation_id = $eid ";
				$data .= ", question_id = $v ";
				$data .= ", rate = {$rate[$v]} ";
				$ins[] = $this->db->query("INSERT INTO evaluation_answers set $data ");
			}
			if (isset($ins))
				return 1;
		}
	}
	function get_class()
{
    extract($_POST);
    $department_id = $_SESSION['login_department_id']; // Dean's department ID
    $data = array();
    $get = $this->db->query("SELECT c.id, CONCAT(c.curriculum,' ',c.level,' - ',c.section) as class, s.id as sid, CONCAT(s.code,' - ',s.subject) as subj 
    FROM restriction_list r 
    INNER JOIN class_list c ON c.id = r.class_id 
    INNER JOIN subject_list s ON s.id = r.subject_id 
    INNER JOIN faculty_list f ON f.id = r.faculty_id 
    WHERE r.faculty_id = {$fid} AND academic_id = {$_SESSION['academic']['id']} AND f.department_id = '$department_id'");
    while ($row = $get->fetch_assoc()) {
        $data[] = $row;
    }
    return json_encode($data);
}

	function get_report()
	{
		extract($_POST);
		$department_id = $_SESSION['login_department_id']; // Dean's department ID
		$data = array();
	
		// Verify that the faculty belongs to the Dean's department
		$faculty_check = $this->db->query("SELECT id FROM faculty_list WHERE id = '$faculty_id' AND department_id = '$department_id'");
		if ($faculty_check->num_rows == 0) {
			// Faculty not in Dean's department
			return json_encode($data);
		}

		$get = $this->db->query("SELECT * FROM evaluation_answers where evaluation_id in (SELECT evaluation_id FROM evaluation_list where academic_id = {$_SESSION['academic']['id']} and faculty_id = $faculty_id and subject_id = $subject_id and class_id = $class_id ) ");
		$answered = $this->db->query("SELECT * FROM evaluation_list where academic_id = {$_SESSION['academic']['id']} and faculty_id = $faculty_id and subject_id = $subject_id and class_id = $class_id");
		$rate = array();
		while ($row = $get->fetch_assoc()) {
			if (!isset($rate[$row['question_id']][$row['rate']]))
				$rate[$row['question_id']][$row['rate']] = 0;
			$rate[$row['question_id']][$row['rate']] += 1;
		}
		// $data[]= $row;
		$ta = $answered->num_rows;
		$r = array();
		foreach ($rate as $qk => $qv) {
			foreach ($qv as $rk => $rv) {
				$r[$qk][$rk] = ($rate[$qk][$rk] / $ta) * 100;
			}
		}
		$data['tse'] = $ta;
		$data['data'] = $r;

		return json_encode($data);
	}
}
