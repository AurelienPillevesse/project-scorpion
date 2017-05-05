<?php

use Sealsix\UserBundle\Entity\User;

/**
 * Description of UserTest
 *
 * @author Aurélien
 */
class UserTest extends \PHPUnit_Framework_TestCase {
    
    public function testGettersSetters() {
        $user = new User();
        $date = new DateTime();
        
        $user->setLogin("Aurelio");
        $user->setPassword("yoloswag58");
        $user->setFirstName("Aurelien");
        $user->setLastName("Cafiere");
        $user->setEmail("aurelien@test.fr");
        $user->setDate($date);
        
        $this->assertEquals("Aurelio", $user->getLogin());
        $this->assertEquals("yoloswag58", $user->getPassword());
        $this->assertEquals("Aurelien", $user->getFirstName());
        $this->assertEquals("Cafiere", $user->getLastName());
        $this->assertEquals("aurelien@test.fr", $user->getEmail());
        $this->assertEquals($date, $user->getDate());
    }
    
}
