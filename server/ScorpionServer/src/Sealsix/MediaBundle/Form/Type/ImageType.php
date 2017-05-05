<?php

namespace Sealsix\MediaBundle\Form\Type;

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
 * Description of MediaType
 *
 * @author Aurélien
 */
class ImageType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('visibility', 'choice', array(
                    'mapped' => false,
                    'choices'  => array(
                        'public' => true, 'private' => false
                    ),
                    'choices_as_values' => false,
                ))
                ->add('nom', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 1)),
                )))
                ->add('auteur', 'text')
                ->add('description', 'text')
                ->add('style', 'text', array(
                    'constraints' => array(
                        new NotBlank()
                )));
    }
    
    public function getName() {
        return 'image';
    }
    
}
