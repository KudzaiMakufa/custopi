<?php
class Controller_Prochem extends Controller_Template
{
	public function action_apiretrieve()
	{
		$res = [ 'name' => 'kudzie', 'email' => 'name' ];
		//header('Content-type: application/json');
		echo json_encode($res);

		$this->template->title = "Leaves";
		$this->template->content = View::forge('leave/test');
	}
		public function action_checkUserCreate()
	{


			$result = DB::select()->from('users')->where('email', Input::post('name'))->as_object()->execute();
			//Debug::dump($result);die;

			if($result[0]  == null){

				$res = ['check'=> ''];
				echo json_encode($res);
			}
			else{

				foreach($result as $item)
				{

					$res = ['check'=> 'Exists'];
					echo json_encode($res);
						
				}
			}
			


		$this->template->title = "Leaves";
		$this->template->content = View::forge('leave/test');


	}
	public function action_upload()
	{
		$config = array(
		'path' => DOCROOT.'files',
		'randomize' => true,
		'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png','txt'),
		);

				// process the uploaded files in $_FILES
		Upload::process($config);

		// if there are any valid files
		if (Upload::is_valid())
		{
		    // save them according to the config
		    Upload::save();

		    // call a model method to update the database
		    Model_Uploads::add(Upload::get_files());
		}

		// and process any errors
		foreach (Upload::get_errors() as $file)
		{
		    // $file is an array with all file information,
		    // $file['errors'] contains an array of all error occurred
		    // each array element is an an array containing 'error' and 'message'
		}
	}
		public function action_mailtoreg()
	{
		\Package::load('email');
		// Create an instance
		$email = Email::forge();

		// Set the from address
		$email->from('kidapptestingemail@gmail.com', 'kidapptesting');

		// Set the to address
		$email->to('kidkudzy@gmail.com', 'Johny Squid');

		// Set a subject
		$email->subject('This is the subject');

		// And set the body.
		$email->body('This is my message');

		try
			{
			    $email->send();
			}
			catch(\EmailValidationFailedException $e)
			{
			    // The validation failed
			}
			catch(\EmailSendingFailedException $e)
			{
			    // The driver could not send the email
			}
	}
		public function action_createuser()
	{

			Auth::create_user(
			Input::post('phone'),
			Input::post('password'),
			Input::post('email'),
			0,
			0,
			Input::post('fullname'),
			Input::post('gender'),
			0,
			1,
			array(
			'fullname' => Input::post('fullname'),
			)
			);

			/*$res = ['create'=> 'created'];
			echo json_encode($res);*/


		$this->template->title = "Leaves";
		$this->template->content = View::forge('leave/test');
	}
	public function action_delete($id = null)
	{
		if ($leave = Model_Leave::find_one_by_id($id))
		{
			$leave->delete();

			Session::set_flash('success', 'Deleted leave #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete leave #'.$id);
		}

		Response::redirect('leave');

	}

	
	public function action_index()
	{
			
		list(, $userid) = Auth::get_user_id();
		if($userid == 0){
			Session::set_flash('error'  , 'You must login first');
			Response::redirect('login/create');
		}
		else{
		    $data['leaves'] = Model_Leave::find_all();
		}

		
		$this->template->title = "Leaves";
		$this->template->content = View::forge('leave/index', $data);

	}
	public function action_leaveInsert()
	{
		
			
				$leave = Model_Leave::forge(array(
					'fullname'  => Input::post('fullname'),
					'leavetype' => Input::post('leavetype'),
					'startdate' => Input::post('startdate'),
					'enddate' => Input::post('enddate'),
					'typeofday' => Input::post('typeofday'),
					'notes' => Input::post('notes'),
				));

				$leave->save();
	}
	public function action_login()
	{
		
			$name = Input::post('name');
			$pass = Input::post('pass');
		/*	$name = "admin@admin.com";
			$pass = "12345";*/

			if (Auth::login($name, $pass))
			{		
					$userid = "";
					list(, $userid) = Auth::get_user_id();

						/*$result = DB::select()->from('users')->where_open()->where('id', $userid)->and_where('verified', '0')->where_close()->as_object()->execute();*/
						$result = DB::select()->from('users')->where('id', $userid)->as_object()->execute();
					foreach($result as $item)
					{
						


						$login = "";

						if($item->email == "admin@admin.com" && $item->id == 1){
							$login = "failed";

						}
						else if($item->isactivated == "0" && $item->verified == "1"){
							$login = "notactivated";
						}
						else if($item->isactivated == "1" && $item->verified == "1"){
							$login = "success";
						}
						else{

							$login = "failed";


						}
				
						

						$res = [
							
							'login'=> $login,
							//'gender'=> $item->gender,
							'userid'=> $item->fullname
					];
						echo json_encode($res);

					}

				
			}
			else{
					$res = ['login'=> 'failed'];
					echo json_encode($res);
				}
		$this->template->title = "Leave";
		$this->template->content = View::forge('prochem/test', null);
	}
	
	public function action_verifyUserAccount()
	{


			$result = DB::update('users')
			->value("verified", "1")
			->where('email', '=', Input::post('email'))
			->execute();

			if($result = 1){

				$res = ['verify'=> 'true'];
				echo json_encode($res);
			}
			else{
			
			}

		

				
			

		$this->template->title = "Leaves";
		$this->template->content = View::forge('leave/test');


	}



	public function action_view($id = null)
	{
		is_null($id) and Response::redirect('leave');

		$data['leave'] = Model_Leave::find_by_pk($id);

		$this->template->title = "Leave";
		$this->template->content = View::forge('leave/view', $data);

	}

	public function action_testconnection()
	{


		$res = ['test'=> 'success'];
		echo json_encode($res);

		$this->template->title = "Leaves";
		$this->template->content = View::forge('leave/test');


	}

}
