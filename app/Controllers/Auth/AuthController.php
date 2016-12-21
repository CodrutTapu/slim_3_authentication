<?php

    namespace App\Controllers\Auth;

    use App\Controllers\Controller;

    use Respect\Validation\Validator as v;

    class AuthController extends Controller
    {

        public function getSignOut($request,$response) {

            $this->auth->logout();

            return $response->withRedirect($this->router->pathFor('home'));

        }

        public function getSignIn($request,$response)
        {
            return $this->view->render($response,'auth/signin.twig');
        }

        public function postSignIn($request,$response)
        {
            $auth = $this->auth->attempt(
                $request->getParam('email'),
                $request->getParam('password')
            );

            if (!$auth) {
                $this->flash->addMessage('danger', 'Could not sign you with those details !');
                return $response->withRedirect($this->router->pathFor('auth.signin'));
            }

            return $response->withRedirect($this->router->pathFor('home'));
        }

        public function getSignUp($request,$response)
        {
            return $this->view->render($response,'auth/signup.twig');
        }

        public function postSignUp($request,$response)
        {

            $validation = $this->validator->validate($request, [
                'full_name' => v::notEmpty()->alpha(),
                'email' => v::noWhitespace()->notEmpty()->email()->EmailAvailable(),
                'password' => v::noWhitespace()->notEmpty(),
            ]);

            if($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('auth.signup'));
            }

            $new_user = $this->db->insert("users", [
            	"full_name" => $request->getParam('full_name'),
            	"email" => $request->getParam('email'),
            	"password" => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
            ]);

            $this->flash->addMessage('info', 'You have been signed up!');

            $auth = $this->auth->attempt(
                $request->getParam('email'),
                $request->getParam('password')
            );

            return $response->withRedirect($this->router->pathFor('home'));
        }
    }


 ?>
