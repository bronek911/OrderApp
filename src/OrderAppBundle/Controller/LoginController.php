<?php

namespace OrderAppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{

    /**
     * Lists all client entities.
     * @Route("/", name="after_login_action")
     * @Method("GET")
     */
    public function loginAction()
    {
        $user = $this->getUser();
        if(!$user || !$user->getId()){
            return $this->redirect("/login");
        }

        return $this->redirect("/orders");

    }

}
