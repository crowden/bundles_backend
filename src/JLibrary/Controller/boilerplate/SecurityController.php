<?php

namespace JLibrary\Controller\boilerplate;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="j29.admin.login")
     */
    public function loginAction(Request $request){
        $auth_utils = $this->get('security.authentication_utils');
        $error = $auth_utils->getLastAuthenticationError();
        $last_username = $auth_utils->getLastUsername();

        return $this->render('BUNDLE:DIR:login.html.twig', array(
            'last_username' => $last_username,
            'error' => $error,
            'page_title' => 'Login',
            'page_description' => 'Login',
        ));
    }
}