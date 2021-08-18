<?php

namespace App\DataFixtures;

use App\Entity\Nurse;
use App\Entity\Patient;
use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

         // 1er infirmier utilisateur en test
         print('NURSE01' . PHP_EOL);
         $nurse = new Nurse();
         $nurse->setFirstname('Alain');
         $nurse->setLastname('Firmier');
         $nurse->setPhone('0770502030');
         $nurse->setEmail('alainfirmier@liberal.com');
         $nurse->setPassword('$2y$13$j.gXly4IYfmvqYnL/rEDWuIta7DuUmfQcj9RiYiLIeGWvRIv045Y2');
         //password de test hashé: "liberal"
         $nurse->setCreatedAt(new \DateTimeImmutable('2021-08-10 10:15:00'));
         //$nurse->setUpdatedAt('');

         $manager->persist($nurse);
         
         // 10 patients "en dur" pour avoir des données choisies cohérentes (nir/birthdate...)
 
         // 1er patient en test :
         print('PATIENT01' . PHP_EOL);
         $patient = new Patient();
         $patient->setFirstname('Katell');
         $patient->setLastname('Mensouaf');
         $patient->setBirthdate(new DateTime("10/04/1952"));
         $patient->setPhone('0607080910');
         $patient->setCompleteAdress('10 Rue de la source 35400 Saint Malo');
         $patient->setInformationAdress('Katell');
         $patient->setNir('252043505500513');
         $patient->setDoctorName('Meredith Grey');
         $patient->setMutualName('France Mutuelle');
         $patient->setMutualNumberAmc('44223544');
         //$patient->setMutualCardImage('');
         $patient->setPathology('diabète type 2');
         //$patient->setNote('');
         $patient->setTrustedPerson('MARI Gérard Mensouaf 0680089090');
         $patient->setCreatedAt(new \DateTimeImmutable('2021-08-15 12:00:00'));
         //$patient->setUpdatedAt('');

        $manager->persist($patient);

        $manager->flush();
    }
}
