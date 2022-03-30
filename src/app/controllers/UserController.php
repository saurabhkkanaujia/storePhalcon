<?php

use Phalcon\Mvc\Controller;

class UserController extends Controller
{
    public function indexAction()
    {
    }

    public function signupAction()
    {
        if ($this->request->isPost()) {

            $user = new Users();
            $obj = new App\Components\Myescaper();

            $inputData = array(
                'name' => $obj->sanitize($this->request->getPost('name')),
                'username' => $obj->sanitize($this->request->getPost('username')),
                'email' => $obj->sanitize($this->request->getPost('email')),
                'password' => $obj->sanitize($this->request->getPost('password'))
            );

            $user->assign(
                $inputData,
                [
                    'name',
                    'username',
                    'email',
                    'password'
                ]
            );

            $success = $user->save();

            $this->view->success = $success;

            if ($success) {
                $this->response->redirect('user/signin');
                // $this->dispatcher->forward(
                //     [
                //         'controller' => 'signin',
                //         'action' => 'index',
                //     ]
                // );
                $this->view->message = "Register succesfully";
            } else {
                $this->view->message = "Not Register succesfully due to following reason: <br>" . implode("<br>", $user->getMessages());
            }
        }
    }

    public function signinAction()
    {
        if ($this->request->isPost()) {
            $postData = $this->request->getPost();
            
            $user = Users::find([
                'conditions' => 'email= :email: AND password = :password:',
                'bind' => [
                    'email' => $postData['email'],
                    'password' => $postData['password'],
                ]
            ]);
            if (count($user) == 0) {
                $this->response->redirect('user/signin?err="Invalid Credentials');
                    
            } else {
                $user = json_decode(json_encode($user[0]));
                $this->session->loginUser = json_decode(json_encode($user));
                
                if ($user->status == 'Approved') {
                    $this->response->redirect('panel/dashboard');
                    // $this->dispatcher->forward(
                    //     [
                    //         'controller' => 'panel',
                    //         'action' => 'dashboard',
                    //     ]
                    // );
                } else {
                    $this->response->redirect('user/signin?err="Oops!!, You haven\'t been approved');

                }
            }
        }
    }
}
