<?php

namespace Sealsix\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerControllerTest extends WebTestCase
{
    public function testPostUser()
    {
        $client = static::createClient();

        $client->request('POST', '/api/users', array(), array(), array(
        	'CONTENT_TYPE' => 'application/json'),
        	'{"user":{"firstName": "Jackie","lastName": "Michel","login": "licorne21","email": "jackie.michel@rocco.com","password": "mybigd$$"}}');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('POST', '/api/users', array(), array(), array(
        	'CONTENT_TYPE' => 'application/json'),
        	'{"user":{"lastName": "Michel","login": "licorne21","email": "jackie.michel@rocco.com","password": "mybigd$$"}}');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

}
