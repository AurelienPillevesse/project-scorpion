<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sealsix\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
/**
 * Description of CommentType
 *
 * @author Aurélien
 */
class CommentType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('content', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 1)),
                )));
    }
    
    public function getName() {
        return 'comment';
    }
    
}
