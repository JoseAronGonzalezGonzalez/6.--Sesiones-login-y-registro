<?php 


if (!isset($_SESSION)) {
	session_start();
}
include_once "connectionController.php";


if (isset($_POST['action'])) {
	$authController = new AuthController();

	switch ($_POST['action']) {
		case 'register':
			
			$name = strip_tags($_POST['name']);
			$email = strip_tags($_POST['email']);
			$password = strip_tags($_POST['password']);

			$authController->register($name,$email,$password);

		break;

		case 'login':

			$email = strip_tags($_POST['email']);
			$password = strip_tags($_POST['password']);

			$authController->access($email,$password);

		break;
		
	}
}

class AuthController
{

	public function register($name,$email,$password){

	$conn = connect();
	if (!$conn->connect_error) {
		
		if ($name!="" && $email!="" && $password!="") {
			

			$originalPassword =$password;
			$password = sha1($password.'tacos_dorados_123');

			$query = "insert into users (name, email, password) value (?,?,?)";

			$prepared_query = $conn->prepare($query);
			$prepared_query->bind_param('sss',$name,$email,$password);
			if ($prepared_query->execute()) {

				$this->access($email,$originalPassword);

			}else{
				$_SESSION['error'] = 'verifique los datos enviados';

				header("location:". $_SERVER['HTTP_REFERER']);
			}

		}else{

			$_SESSION['error'] = 'verifique la informacion';

			header("location:". $_SERVER['HTTP_REFERER']);

		}
	}else{
		$_SESSION['error'] = 'verifique la informacion';

		header("location:". $_SERVER['HTTP_REFERER']);

	}

	}

	public function access($email,$password)
	{


		$conn = connect();
		if (!$conn->connect_error) {
			
			if ($email!="" && $password!="") {
				$password = sha1($password. 'tacos_dorados_123');

				$query = "select * from users where email = ? and password = ?";

				$prepared_query = $conn->prepare($query);
				$prepared_query->bind_param('ss',$email,$password);

				if ($prepared_query->execute()) {
					

					$result = $prepared_query->get_result();
					$user = $result->fetch_all(MYSQLI_ASSOC);

					if (count($user)>0) {
						$user = array_pop($user);
						$_SESSION['id'] = $user['id'];
						$_SESSION['name'] = $user['name'];
						$_SESSION['email'] = $user['email'];


						header("Location:../category");
					}

				}else{
					$_SESSION['error'] = 'verifique la informacion';

					header("location:". $_SERVER['HTTP_REFERER']);

				}
			}else{
				$_SESSION['error'] = 'verifique la informacion del formulario';

				header("location:". $_SERVER['HTTP_REFERER']);
			}

		}


		// "select * from users where email = ? and password = ?";

		// if($user){

		// 	$_SESSION['id'] = $user['id'];
		// 	$_SESSION['name'] = $user['name'];
		// 	$_SESSION['email'] = $user['email'];
		// 	$_SESSION['role'] = $user['role'];

		// 	if ($_SESSION['role']=="admin") {
		// 		//header(string)
		// 	}else{
				
		// 	}

		// }

	}

	public function logout()
	{

	}

}



?>