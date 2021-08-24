<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class PatientController extends AbstractController
{
    
    /**
     * Get patients 
     * 
     * @Route("/api/patients", name="api_patients_get", methods="GET")
     */
    public function browse(PatientRepository $patientRepository): Response
    {
        $patients = $patientRepository->findBy(['nurse'=>$this->getUser()]);

        // Resquest to Symfony to "serialize" entities in form of JSON
        return $this->json($patients, 200, [], ['groups' => 'patients_get']);
    }



     /**
     * Get one patient by id
     * 
     * @Route("/api/patients/{id<\d+>}", name="api_patients_get_item", methods="GET")
     */
    public function read(Patient $patient): Response
    {       
        $user = $this->getUser();
        $userId = $user->getId();

        $nursePatient = $patient->getNurse();
        $nursePatientId = $nursePatient->getId();

        // If this patient is not the patient of this nurse/user
        if($userId != $nursePatientId)
        {
            return new JsonResponse(["message" => "Patient non trouvé"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($patient, Response::HTTP_OK, [], ['groups' => 'patients_get']);
    }


    /**
     * Edit patient by id
     * 
     * @Route("/api/patients/{id<\d+>}", name="api_patients_put_item", methods={"PUT", "PATCH"})
     */
    public function edit(Patient $patient = null, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $nursePatient = $patient->getNurse();
        $nursePatientId = $nursePatient->getId();

        // If this patient is not the patient of this nurse/user
        if($userId != $nursePatientId)
        {
            return new JsonResponse(["message" => "Patient non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // If patient not found
        if ($patient === null) {
            return new JsonResponse(["message" => "Patient non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // else we get the data's request
        $data = $request->getContent();

        // @todo Pour PUT, s'assurer qu'on ait un certain nombre de champs
        // @todo Pour PATCH, s'assurer qu'on au moins un champ
        // sinon => 422 HTTP_UNPROCESSABLE_ENTITY

        // we désérialise the JSON to the existing Patient entity
        $patient = $serializer->deserialize($data, Patient::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $patient]);

        // We can validate the entity with the Validator service
        $errors = $validator->validate($patient);

        // Errors display
        if (count($errors) > 0) {

            //!todo mettre en anglais
            // Objectif : créer ce format de sortie
            // {
            //     "errors": {
            //         "title": [
            //             "Cette valeur ne doit pas être vide."
            //         ],
            //             "releaseDate": [
            //             "Cette valeur doit être de type string."
            //         ],
            //         "rating": [
            //             "Cette chaîne est trop longue. Elle doit avoir au maximum 1 caractère.",
            //             "Cette valeur doit être l'un des choix proposés."
            //         ]
            //     }
            // }

            // creating an errors array
            $newErrors = [];

            foreach ($errors as $error) {
                // We push in an arrays
                // = similar tu the structure of Flash Messages
                // We pus the message, ti the key that contains the property         
                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return new JsonResponse(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Database recording
         $entityManager->flush();

        //!todo Conditionner le message de retour au cas où
        // l'entité ne serait pas modifiée
        return new JsonResponse(["message" => "Patient modifié"], Response::HTTP_OK);
    }


    /**
     * add a new patient
     * 
     * @Route("/api/patients", name="api_patients_post", methods="POST")
     */
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        // Désérialise the JSON to the new entity Patient
        // @see https://symfony.com/doc/current/components/serializer.html#deserializing-an-object
        $patient = $serializer->deserialize($jsonContent, Patient::class, 'json');

        //We can validate the entity with the Validator service
        $errors = $validator->validate($patient);

        // Errors display
        // ($errors is like an array, he contains one élément by error)
        if (count($errors) > 0) {
            return $this->json(["errors" => $errors],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $patient->setNurse($this->getUser());

        // We are preparing to persist in Database, and flush
        $entityManager->persist($patient);
        $entityManager->flush();

        // REST ask us a 201 status and a header Location: url
        //! Si on le fait "à la mano" voir autre manière de faire ?
        return $this->json(
            // the Patient we return in JSON at the front
            $patient,
            // The status code
            Response::HTTP_CREATED,
            // The header Location + l'URL of the created ressource
            ['Location' => $this->generateUrl('api_patients_get_item', ['id' => $patient->getId()])],
            //!TODO à vérifier après avoir mis les relations sur les entités
            // Le groupe de sérialisation pour que $patient soit sérialisé sans erreur de référence circulaire
            ['groups' => 'patients_get']
        );
    }

     /**
     * Delete a patient
     * 
     * @Route("/api/patients/{id<\d+>}", name="api_patients_delete", methods="DELETE")
     */
    public function delete(Patient $patient = null, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $nursePatient = $patient->getNurse();
        $nursePatientId = $nursePatient->getId();

        // If this patient is not the patient of this nurse/user
        if($userId != $nursePatientId)
        {
            return new JsonResponse(["message" => "Patient non trouvé"], Response::HTTP_NOT_FOUND);
        }

        //Errors display
        if (null === $patient) {

            $error = 'Patient non trouvé';

            return $this->json(['error' => $error], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($patient);
        $entityManager->flush();

        return $this->json(['message' => 'Le patient a bien été supprimé.'], Response::HTTP_OK);
    }

}
