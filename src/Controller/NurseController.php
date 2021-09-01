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
     * Get the nurse connected
     * 
     * @Route("/api/index", name="api_nurse_connected_get", methods="GET")
     */
    public function index(): Response
    {
        // Only the user can access to his own informations
        $user = $this->getUser();
        
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'home_get']);
    }


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

        if ($user != $nurse) {
            return new JsonResponse(["message" => "Compte utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($nurse, Response::HTTP_OK, [], ['groups' => 'nurse_get']);
    }


    /**
     * Edit nurse by id (edit my account)
     * 
     * @Route("/api/nurses/{id<\d+>}", name="api_nurse_put_item", methods={"PUT", "PATCH"})
     */
    public function edit(Nurse $nurse = null, UserPasswordHasherInterface $userPasswordHasher, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();

        // if nurse not found or if the user is not this nurse, return error
        if ($nurse === null || $user != $nurse) {
            return new JsonResponse(["message" => "Compte utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // else we get the data's request
        $data = $request->getContent();

        $jsonData = json_decode($data, true);

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

        if (isset($jsonData['password'])) {
        
            $hashedPassword = $userPasswordHasher->hashPassword($nurse, $nurse->getPassword());
            $nurse->setPassword($hashedPassword);
        }

        $entityManager->flush();

        return new JsonResponse(["message" => "Compte modifié"], Response::HTTP_OK);
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

        $hashedPassword = $userPasswordHasher->hashPassword($nurse, $nurse->getPassword());
        $nurse->setPassword($hashedPassword);

        $errors = $validator->validate($nurse);

        if (count($errors) > 0) {

            $newErrors = [];

            foreach ($errors as $error) {
                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

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

        if ($user != $nurse) {
            return new JsonResponse(["message" => "Compte utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($nurse);
        $entityManager->flush();

        //! TODO : manage errors with @Assert
        return $this->json(['message' => 'Votre compte a bien été supprimé.'], Response::HTTP_OK);
    }
}
