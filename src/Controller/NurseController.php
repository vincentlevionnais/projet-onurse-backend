<?php

namespace App\Controller;

use App\Entity\Nurse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class NurseController extends AbstractController
{

    /**
     * Get one nurse by id (see my account)
     * 
     * @Route("/api/nurses/{id<\d+>}", name="api_nurse_get_item", methods="GET")
     */
    public function read(Nurse $nurse = null): Response
    {
        if ($nurse === null) {
            return new JsonResponse(["message" => "Compte utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // Only the user can access to his own informations
        $user = $this->getUser();
        $userId = $user->getId();

        $nurseId = $nurse->getId();

        if ($userId != $nurseId) {
            return new JsonResponse(["message" => "Compte utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($nurse, Response::HTTP_OK, [], ['groups' => 'nurse_get']);
    }


    /**
     * Edit nurse by id (edit my account)
     * 
     * @Route("/api/nurses/{id<\d+>}", name="api_nurse_put_item", methods={"PUT", "PATCH"})
     */
    public function edit(Nurse $nurse = null, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, Request $request): Response
    {
        // if nurse not found
        if ($nurse === null) {
            return new JsonResponse(["message" => "Compte utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // if the user is not this nurse, return error
        $user = $this->getUser();
        $userId = $user->getId();

        $nurseId = $nurse->getId();

        if ($userId != $nurseId) {
            return new JsonResponse(["message" => "Compte utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // else we get the data's request
        $data = $request->getContent();

        // @todo Pour PUT, s'assurer qu'on ait un certain nombre de champs
        // @todo Pour PATCH, s'assurer qu'on au moins un champ
        // sinon => 422 HTTP_UNPROCESSABLE_ENTITY

        // we désérialise the JSON to the existing Nurse entity
        $nurse = $serializer->deserialize($data, Nurse::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $nurse]);

        // We can validate the entity with the Validator service
        $errors = $validator->validate($nurse);

        // Errors display
        if (count($errors) > 0) {

            // creating an errors array
            $newErrors = [];

            foreach ($errors as $error) {
                // We push in an arrays
                // = similar tu the structure of Flash Messages
                // We push the message, to the key that contains the property               
                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return new JsonResponse(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Database recording error or succes
        if (($entityManager->flush()) === false) {
            return new JsonResponse(["message" => "Erreur : Compte non modifié"], Response::HTTP_EXPECTATION_FAILED);
        } else {
            return new JsonResponse(["message" => "Compte modifié"], Response::HTTP_OK);
        }
    }


    /**
     * add a new Nurse (create account)
     * 
     * @Route("/api/login", name="api_nurse_post", methods="POST")
     */
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        $nurse = $serializer->deserialize($jsonContent, Nurse::class, 'json');

        $errors = $validator->validate($nurse);

        if (count($errors) > 0) {
            return $this->json(["errors" => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $hashedPassword = $userPasswordHasher->hashPassword($nurse, $nurse->getPassword());

        $nurse->setPassword($hashedPassword);

        $entityManager->persist($nurse);
        $entityManager->flush();


        return $this->json(
            $nurse,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_nurse_get_item', ['id' => $nurse->getId()])],
            ['groups' => 'nurse_get']
        );
    }

    /**
     * Delete a nurse ( delete my account)
     * 
     * @Route("/api/nurses/{id<\d+>}", name="api_nurse_delete", methods="DELETE")
     */
    public function delete(Nurse $nurse = null, EntityManagerInterface $entityManager)
    {
        if ($nurse === null) {
            return new JsonResponse(["message" => "Compte utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();
        $userId = $user->getId();

        $nurseId = $nurse->getId();

        if ($userId != $nurseId) {
            return new JsonResponse(["message" => "Compte utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($nurse);
        $entityManager->flush();

        return $this->json(['message' => 'Votre compte a bien été supprimé.'], Response::HTTP_OK);
    }
}
