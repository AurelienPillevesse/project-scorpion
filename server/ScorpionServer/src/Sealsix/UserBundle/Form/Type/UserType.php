<?php

namespace Sealsix\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserType
 *
 * @author Aurélien
 */
class UserType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('login', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 4)),
                )))
                ->add('password', 'password', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 4)),
                )))
                ->add('email', 'email', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 4)),
                )))
                ->add('firstName', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 2)),
                )))
                ->add('lastName', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 2)),
                )))
                ->add('city', 'text')
                ->add('country', 'text')
                ->add('biography', 'text');
                
        //$user = new Sealsix\UserBundle\Entity\User($login, $password, $email, $firstName, $lastName)
    }
    
    public function getName() {
        return 'user';
    }
    
}
