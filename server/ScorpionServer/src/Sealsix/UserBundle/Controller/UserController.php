<?php

namespace Sealsix\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sealsix\UserBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sealsix\UserBundle\Form\Type\UserType;
use FOS\RestBundle\View\View;
use Sealsix\UserBundle\Entity\APIKey;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Sealsix\UserBundle\Form\Type\UserPatchType;

class UserController extends FOSRestController
{

    public function getUserAction(){
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->getUser();
    }

    public function postUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);
        $form->bind($request);

        $view = View::create();

        if($form->isValid())
        {
            $encoderFactory = $this->get('security.encoder_factory');
            $encoder = $encoderFactory->getEncoder($user);

            $random = new SecureRandom();
            $salt = base64_encode($random->nextBytes(128 / 8));

            $passwordEncode = $encoder->encodePassword($user->getPassword(), $salt);
            $user->setPassword($passwordEncode);
            $user->setSalt($salt);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $user;
        }
        return $form;
    }

    public function patchUserAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $password = $user->getPassword();

        $form = $this->createForm(new UserPatchType(), $user);
        $form->bind($request);

        if($form->isValid())
        {
            if($form->getData()->getPassword() === null) {
                $user->setPassword($password);
            } else {
                $encoderFactory = $this->get('security.encoder_factory');
                $encoder = $encoderFactory->getEncoder($user);

                $random = new SecureRandom();
                $salt = base64_encode($random->nextBytes(128 / 8));

                $passwordEncode = $encoder->encodePassword($user->getPassword(), $salt);
                $user->setPassword($passwordEncode);
                $user->setSalt($salt);
            }
            
            $em->persist($user);
            $em->flush();

            return $user;
        }
        return $form;
    }


    /**
     * @Post("/users/login")
     */
    public function loginAction(Request $request) {
        $userRepository = $this->getDoctrine()->getRepository('SealsixUserBundle:User');
        $data = $request->request->all();

        if(!$data['login'])
            throw new BadRequestHttpException("No login field in request");
        if(!$data['password'])
            throw new BadRequestHttpException("No password field in request");

        $userByLogin = $userRepository->findOneByLogin($data['login']);

        if($userByLogin !== null)
        {
            $encoderFactory = $this->get('security.encoder_factory');
            $encoder = $encoderFactory->getEncoder($userByLogin);
            $passwordEncode = $encoder->encodePassword($data['password'], $userByLogin->getSalt());

            if($passwordEncode === $userByLogin->getPassword())
            {
                //si déjà une ligne dans ApiKey (BDD) avec l'id de l'user, retourner le token
                $APIKeyUser = new APIKey();
                $APIKeyUser->setUser($userByLogin);

                $em = $this->getDoctrine()->getManager();
                $em->persist($APIKeyUser);
                $em->flush();

                return $APIKeyUser;
            }
            else {
              throw new BadCredentialsException();
            }

        }
        throw new BadCredentialsException();
    }

    /**
     * @Post("/users/logout")
     */
    public function logoutAction(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $hash = $request->query->get('key');
        $em = $this->getDoctrine()->getManager();
        $token = $em->getRepository('SealsixUserBundle:APIKey')->findOneByHash($hash);
        if(!$token){
            throw $this->createNotFoundException(sprintf("Hash %s not found", $hash));
        }

        $em->remove($token);
        $em->flush();

        return new Response('', 204);
    }
    
    public function patchAvatarAction($id) {
        $em = $this->getDoctrine()->getManager();
        $avatar = $em->getRepository('SealsixMediaBundle:Image')->find($id);
        
        $user = $this->getUser();
        $user->setAvatar($avatar);
        
        return $user;
    }
}
