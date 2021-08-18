<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Nurse;
use DateTimeImmutable;
use App\Entity\Patient;
use App\Entity\Appointment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // NURSES
        
        print('NURSE01' . PHP_EOL);
        $nurse = new Nurse();
        $nurse->setFirstname('Alain');
        $nurse->setLastname('Firmier');
        $nurse->setPhone('0770502030');
        $nurse->setEmail('alainfirmier@liberal.com');
        $nurse->setPassword('$2y$13$j.gXly4IYfmvqYnL/rEDWuIta7DuUmfQcj9RiYiLIeGWvRIv045Y2');
        //password de test hashé: "liberal"
        $nurse->setCreatedAt(new DateTimeImmutable('2021-08-10 10:15:00'));
        //$nurse->setUpdatedAt('');

        $manager->persist($nurse);


        print('NURSE02' . PHP_EOL);
        $nurse = new Nurse();
        $nurse->setFirstname('Morgane');
        $nurse->setLastname('Hize');
        $nurse->setPhone('0655443210');
        $nurse->setEmail('morganehize@soins.fr');
        $nurse->setPassword('$2y$13$1WR9MM2276uGyIzD7RWviuDKi.s/PUju9K37GU0VC2Yh1fazVf6re');
        //password de test hashé: "soins"
        $nurse->setCreatedAt(new DateTimeImmutable('2021-08-15 12:13:37'));
        //$nurse->setUpdatedAt('');

        $manager->persist($nurse);


        // APPOINTMENTS

        print('APPOINTMENT01' . PHP_EOL);
        $appointment = new Appointment();
        $appointment->setDatetimeStart(new DateTimeImmutable("2021-08-19 12:00:00"));
        $appointment->setDatetimeEnd(new DateTimeImmutable("2021-08-19 14:30:00"));
        $appointment->setReason("Un premier rendez-vous de test");
        
        $manager->persist($appointment);


        print('APPOINTMENT02' . PHP_EOL);
        $appointment = new Appointment();
        $appointment->setDatetimeStart(new DateTimeImmutable("2021-08-20 9:30:00"));
        $appointment->setDatetimeEnd(new DateTimeImmutable("2021-08-20 10:00:00"));
        $appointment->setReason("Un deuxième rendez-vous de test...");
        
        $manager->persist($appointment);


        print('APPOINTMENT03' . PHP_EOL);
        $appointment = new Appointment();
        $appointment->setDatetimeStart(new DateTimeImmutable("2021-08-20 10:30:00"));
        $appointment->setDatetimeEnd(new DateTimeImmutable("2021-08-20 11:30:00"));
        $appointment->setReason("Mon 3e rendez-vous, c'est super!!");
        
        $manager->persist($appointment);


        // 10 PATIENTS "hard-written" to have consistent chosen data (nir/birthdate...)

        
        print('PATIENT01' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Katell");
        $patient->setLastname("Mensouaf");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","10/04/1952"));
        $patient->setPhone("0607080910");
        $patient->setCompleteAdress("10 Rue de la source 35400 Saint Malo");
        //$patient->setInformationAdress("");
        $patient->setNir("252043505500513");
        $patient->setDoctorName("Meredith Grey");
        $patient->setMutualName("France Mutuelle");
        $patient->setMutualNumberAmc("44223544");
        //$patient->setMutualCardImage("");
        $patient->setPathology("diabète type 2");
        //$patient->setNote("");
        $patient->setTrustedPerson("MARI Gérard Mensouaf 0680089090");
        $patient->setCreatedAt(new DateTimeImmutable("2021-08-15 12:00:00"));
        //$patient->setUpdatedAt("");

        $manager->persist($patient);


        
        print('PATIENT02' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Theophil");
        $patient->setLastname("Olit");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","24/12/1965"));
        $patient->setPhone("0706050403");
        $patient->setCompleteAdress("2 Impasse de la Belle Etoile, 35800 Saint-Lunaire");
        //$patient->setInformationAdress("");
        $patient->setNir("165127218502198");
        $patient->setDoctorName("Martin Whitly");
        $patient->setMutualName("Harmony Mutuelle");
        $patient->setMutualNumberAmc("35266531");
        //$patient->setMutualCardImage("");
        $patient->setPathology("apnées du sommeil");
        //$patient->setNote("");
        $patient->setTrustedPerson("SOEUR Claire Delune 0660500500  25 Rue de l'Horizon 35800 Saint-Lunaire");
        $patient->setCreatedAt(new DateTimeImmutable("2021-08-15 12:00:00"));
        $patient->setUpdatedAt(new DateTimeImmutable("2021-08-15 14:30:00"));

        $manager->persist($patient);


        
        print('PATIENT03' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Fabien");
        $patient->setLastname("Veunu");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","24/06/1987"));
        $patient->setPhone("0654321001");
        $patient->setCompleteAdress("2 Rue de Bellevue 35430 Saint-Père");
        $patient->setInformationAdress("Attention au chien, passer par derrière");
        $patient->setNir("187062265412575");
        $patient->setDoctorName("Michaela Quinn");
        $patient->setMutualName("La Mutuelle Verte");
        $patient->setMutualNumberAmc("455332652");
        //$patient->setMutualCardImage("");
        //$patient->setPathology("");
        //$patient->setNote("");
        $patient->setTrustedPerson("SOEUR Sarah Crauche 0606060606 5 Le Calvaire 35270 Bonnemain");
        $patient->setCreatedAt(new DateTimeImmutable("2021-08-15 12:00:00"));
        //$patient->setUpdatedAt("");

        $manager->persist($patient);


        
        print('PATIENT04' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Rémi");
        $patient->setLastname("Fasol");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","18/10/2004"));
        $patient->setPhone("0678963210");
        $patient->setCompleteAdress("La Musiquerie 35350 Saint-Méloir-des-Ondes");
        //$patient->setInformationAdress("");
        $patient->setNir('104103562542563');
        $patient->setDoctorName("Louis Pasteur");
        $patient->setMutualName("La Mutuelle Des Etudiants");
        $patient->setMutualNumberAmc('423588654');
        //$patient->setMutualCardImage("");
        //$patient->setPathology("");
        //$patient->setNote("");
        $patient->setTrustedPerson("MERE Sidonie Fasol 0696789475");
        $patient->setCreatedAt(new DateTimeImmutable("2021-08-15 12:00:00"));
        //$patient->setUpdatedAt('');

        $manager->persist($patient);


        
        print('PATIENT05' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Lara");
        $patient->setLastname("Clette");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","05/11/1990"));
        $patient->setPhone("0760618914");
        $patient->setCompleteAdress("5 Rue du Pourquoi Pas 35430 Saint-Jouan-des-Guérets");
        $patient->setInformationAdress("ne pas sonner (bébé dort)");
        $patient->setNir("290117423315613");
        $patient->setDoctorName("René Laennec");
        $patient->setMutualName("Harmonie Mutuelle");
        $patient->setMutualNumberAmc("65542358");
        //$patient->setMutualCardImage("");
        $patient->setPathology("obésité morbide");
        $patient->setNote("suivi cicatrisation/pansement");
        $patient->setTrustedPerson("MERE Katell Mensouaf 0680089090");
        $patient->setCreatedAt(new DateTimeImmutable("2021-08-15 12:00:00"));
        $patient->setUpdatedAt(new DateTimeImmutable("2021-08-15 15:00:00"));

        $manager->persist($patient);


        
        print('PATIENT06' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Henry");
        $patient->setLastname("Zotto");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","10/11/1941"));
        $patient->setPhone("0711010111");
        $patient->setCompleteAdress("25 Rue de Riancourt 35400 Saint Malo");
        //$patient->setInformationAdress("");
        $patient->setNir("141110114101410");
        $patient->setDoctorName("Michaela Quinn");
        $patient->setMutualName("Malakoff Humanis Nationale");
        $patient->setMutualNumberAmc("14255654");
        //$patient->setMutualCardImage("");
        //$patient->setPathology("");
        //$patient->setNote("");
        $patient->setTrustedPerson("MARI Gérard Mensouaf 0680089090");
        $patient->setCreatedAt(new DateTimeImmutable('2021-08-15 12:00:00'));
        //$patient->setUpdatedAt("");

        $manager->persist($patient);


        
        print('PATIENT07' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Inès");
        $patient->setLastname("Cargot");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","12/09/1928"));
        $patient->setPhone("0650272843");
        $patient->setCompleteAdress("1 Impasse du grand jardin 35400 Saint Malo");
        //$patient->setInformationAdress("");
        $patient->setNir("228099265425855");
        $patient->setDoctorName("Michel Cymes");
        $patient->setMutualName("Mutuelle 403");
        $patient->setMutualNumberAmc("88985689");
        //$patient->setMutualCardImage("");
        $patient->setPathology("troubles de la déglutition");
        $patient->setNote("point adaptation eau gélifiée");
        $patient->setTrustedPerson("FILS Younès Cargot 0732101234 LE HAVRE");
        $patient->setCreatedAt(new DateTimeImmutable("2021-08-15 12:00:00"));
        //$patient->setUpdatedAt("");

        $manager->persist($patient);


        
        print('PATIENT08' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Paul");
        $patient->setLastname("Ochon");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","15/07/1973"));
        $patient->setPhone("0685564321");
        $patient->setCompleteAdress("3 Rue de Saturne 35400 Saint Malo");
        //$patient->setInformationAdress("");
        $patient->setNir("173072732189846");
        $patient->setDoctorName("Louis Pasteur");
        $patient->setMutualName("Mutuelle d'Ouest-France");
        $patient->setMutualNumberAmc("775648588");
        //$patient->setMutualCardImage("");
        $patient->setPathology("narcolepsie-cataplexie");
        $patient->setNote("suivi traitements - prise de sang");
        $patient->setTrustedPerson("COMPAGNE Sidonie Fasol 0696789475");
        $patient->setCreatedAt(new DateTimeImmutable("2021-08-16 08:00:00"));
        //$patient->setUpdatedAt("");

        $manager->persist($patient);


        
        print('PATIENT09' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Ernest");
        $patient->setLastname("Ragon");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","14/07/1950"));
        $patient->setPhone("0622293556");
        $patient->setCompleteAdress("2 PLace aux Herbes 35400 Saint Malo");
        //$patient->setInformationAdress("");
        $patient->setNir("150071426456425");
        $patient->setDoctorName("Michaela Quinn");
        $patient->setMutualName("Matmut");
        $patient->setMutualNumberAmc("565655895");
        //$patient->setMutualCardImage("");
        //$patient->setPathology("");
        //$patient->setNote("");
        //$patient->setTrustedPerson("");
        $patient->setCreatedAt(new DateTimeImmutable("2021-08-16 08:00:00"));
        //$patient->setUpdatedAt("");

        $manager->persist($patient);


        
        print('PATIENT10' . PHP_EOL);
        $patient = new Patient();
        $patient->setFirstname("Alphonse");
        $patient->setLastname("Danlta");
        $patient->setBirthdate(DateTime::createFromFormat("d/m/Y","02/12/1941"));
        $patient->setPhone("0299605035");
        $patient->setCompleteAdress("7 La Folie 35350 Saint-Coulomb");
        //$patient->setInformationAdress("");
        $patient->setNir("1411253695478");
        $patient->setDoctorName("Martin Whitly");
        $patient->setMutualName("AXA");
        $patient->setMutualNumberAmc("727548559");
        //$patient->setMutualCardImage("");
        //$patient->setPathology("");
        $patient->setNote("patient violent - accident vélo/pansements");
        $patient->setTrustedPerson("FILLE Emma Toudi 0633864275 CAEN");
        $patient->setCreatedAt(new DateTimeImmutable("2021-08-16 08:00:00"));
        //$patient->setUpdatedAt("");

        $manager->persist($patient);


        
        $manager->flush();
    }
}
