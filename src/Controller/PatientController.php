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
        $patients = $patientRepository->findBy(['nurse' => $this->getUser()]);

        // Resquest to Symfony to "serialize" entities in form of JSON
        return $this->json($patients, 200, [], ['groups' => 'patients_get']);
    }


    /**
     * Get one patient by id
     * 
     * @Route("/api/patients/{id<\d+>}", name="api_patients_get_item", methods="GET")
     */
    public function read(Patient $patient = null): Response
    {
        if ($patient === null) {
            return new JsonResponse(["message" => "Patient non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // we compare the user and the nurse of the patient
        $user = $this->getUser();
        $nursePatient = $patient->getNurse();

        // Error if this patient is not the patient of this nurse/user
        if ($user != $nursePatient) {
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
        if ($patient === null) {
            return new JsonResponse(["message" => "Patient non trouvé"], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();
        $nursePatient = $patient->getNurse();

        if ($user != $nursePatient) {
            return new JsonResponse(["message" => "Patient non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // else we get the data's request
        $data = $request->getContent();

        // @todo Pour PUT, s'assurer qu'on ait un certain nombre de champs
        // @todo Pour PATCH, s'assurer qu'on au moins un champ
        // sinon => 422 HTTP_UNPROCESSABLE_ENTITY

        // we deserialize the JSON to the existing Patient entity
        $patient = $serializer->deserialize($data, Patient::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $patient]);

        // We can validate the entity with the Validator service
        $errors = $validator->validate($patient);

        // Errors display
        // ($errors is like an array, it contains one élément by error)
        if (count($errors) > 0) {

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

        // Database recording error or succes 
        //! TODO : manage errors with @Assert
        if (($entityManager->flush()) === false) {
            return new JsonResponse(["message" => "Erreur : Patient non modifié"], Response::HTTP_EXPECTATION_FAILED);
        } else {
            return new JsonResponse(["message" => "Patient modifié"], Response::HTTP_OK);
        }
    }


    /**
     * add a new patient
     * 
     * @Route("/api/patients", name="api_patients_post", methods="POST")
     */
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        // Deserialize the JSON to the new entity Patient
        // @see https://symfony.com/doc/current/components/serializer.html#deserializing-an-object
        $patient = $serializer->deserialize($jsonContent, Patient::class, 'json');

        $patient->setNurse($this->getUser());

        //We can validate the entity with the Validator service
        $errors = $validator->validate($patient);

        if (count($errors) > 0) {

            $newErrors = [];

            foreach ($errors as $error) {

                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return new JsonResponse(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($patient);
        $entityManager->flush();

        return $this->json(
            $patient,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_patients_get_item', ['id' => $patient->getId()])],
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
        if (null === $patient) {
            $error = 'Patient non trouvé';
            return $this->json(['error' => $error], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();
        $nursePatient = $patient->getNurse();

        // If the $nursePatientId not refer to a patient of this nurse/user
        if ($user != $nursePatient) {
            return new JsonResponse(["message" => "Patient non trouvé"], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($patient);
        $entityManager->flush();
        
        //! TODO : manage errors with @Assert
        return $this->json(['message' => 'Le patient a bien été supprimé.'], Response::HTTP_OK);
    }
}
