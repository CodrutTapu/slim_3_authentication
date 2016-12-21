<?php

    namespace App\Controllers\Auth;

    use App\Controllers\Controller;

    use Respect\Validation\Validator as v;

    class PasswordController extends Controller
    {
        public function getChangePassword($request,$response)
        {
            $this->view->render($response, 'auth/password/change.twig');
        }

        public function postChangePassword($request,$response)
        {
            $validation = $this->validator->validate($request, [
                'password_old' => v::noWhitespace()->notEmpty()->MatchesPassword($this->auth->user()->password),
                'password' => v::noWhitespace()->notEmpty(),
            ]);

            if ($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('auth.password.change'));
            }

            $user = $this->auth->user();

            $this->db->update("users", [
            	"password" => password_hash($request->getParam('password'), PASSWORD_DEFAULT)
            ],[
                "id" => $user->id
            ]);

            $this->flash->addMessage('info', 'Your password was changed.');
            return $response->withRedirect($this->router->pathFor('home'));

        }

    }

 ?>
