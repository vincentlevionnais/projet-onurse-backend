<?php

namespace App\Tests\Controller;

use App\Repository\NurseRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Teste les accès pour le ROLE_USER
 */
class RoleUserTest extends WebTestCase
{
    /**
     * Access OK (200)
     * 
     * @dataProvider hasAccessUrls
     */
    public function testHasAccess($method, $url): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(NurseRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('alainfirmier@liberal.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);
        
        $crawler = $client->request($method, $url);

        $this->assertResponseIsSuccessful();
    }

    
    public function hasAccessUrls()
    {
        yield ['GET', 'api/patients'];
        yield ['GET', 'api/patients/1'];
        yield ['GET', 'api/appointments'];
        yield ['GET', 'api/nurses/1'];
        yield ['GET', 'api/appointments/3'];
        // yield ['DELETE', 'api/appointments/4']; OK FONCTIONNEL
        
    }


    /** 
     * not found 404
     * 
    * @dataProvider dataNotFound
     */
    public function testNoAccess($method, $url): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(NurseRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('alainfirmier@liberal.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);
        
        $crawler = $client->request($method, $url);

        $this->assertResponseStatusCodeSame(404);
    }

    public function dataNotFound()
    {
        yield ['GET', 'api/patients/2'];
        yield ['GET', 'api/appointments/2'];
        yield ['DELETE', 'api/appointments/5'];
        yield ['GET', 'api/nurses/2'];
        
    }

}
