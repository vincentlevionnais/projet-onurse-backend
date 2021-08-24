<?php

namespace App\Controller;

use App\Entity\Nurse;
use App\Repository\NurseRepository;
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
     * Get Nurses 
     * 
     * @Route("/api/nurses", name="api_nurses_get", methods="GET")
     */
    public function browse(NurseRepository $nursesRepository): Response
    {
        $nurses = $nursesRepository->findAll();

        // Resquest to Symfony to "serialize" entities in form of JSON
        return $this->json($nurses, 200, [], ['groups' => 'nurse_get']);
    }

    
     /**
     * Get one nurse by id (see my account)
     * 
     * @Route("/api/nurses/{id<\d+>}", name="api_nurse_get_item", methods="GET")
     */
    public function read(Nurse $nurse): Response
    {       
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
            return new JsonResponse(["message" => "Compte non trouvé"], Response::HTTP_NOT_FOUND);
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

            //!todo mettre en anglais
            // Objectif : create this out format 
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
        return new JsonResponse(["message" => "Compte modifié"], Response::HTTP_OK);
    }


    /**
     * add a new Nurse (create account)
     * 
     * @Route("/api/nurses", name="api_nurse_post", methods="POST")
     */
    public function add(Request $request,UserPasswordHasherInterface $userPasswordHasher, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        // Désérialise the JSON to the new entity Nurse
        // @see https://symfony.com/doc/current/components/serializer.html#deserializing-an-object
        $nurse = $serializer->deserialize($jsonContent, Nurse::class, 'json');

        // We can validate the entity with the Validator service
        $errors = $validator->validate($nurse);

        // Errors display
        // ($errors is like an array, he contains one élément by error)
        if (count($errors) > 0) {
            return $this->json(["errors" => $errors],Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        // On hash le mot de passe
        $hashedPassword = $userPasswordHasher->hashPassword($nurse, $nurse->getPassword());
        // On le remet dans $user->password
        $nurse->setPassword($hashedPassword);
        //dd($nurse);

        // We are preparing to persist in Database, and flush
        $entityManager->persist($nurse);
        $entityManager->flush();

        // REST ask us a 201 status and a header Location: url
        //! Si on le fait "à la mano" voir autre manière de faire ?
        return $this->json(
            // the Nurse we return in JSON at the front
            $nurse,
            // The status code
            Response::HTTP_CREATED,
            // The header Location + the URL of the created ressource
            ['Location' => $this->generateUrl('api_nurse_get_item', ['id' => $nurse->getId()])],
            //!todo à vérifier après avoir mis les relations sur les entités
            //! Le groupe de sérialisation pour que $patient soit sérialisé sans erreur de référence circulaire
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

        //Errors display
        if (null === $nurse) {

            $error = 'Ce compte n\'existe pas';

            return $this->json(['error' => $error], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($nurse);
        $entityManager->flush();

        return $this->json(['message' => 'Votre compte a bien été supprimé.'], Response::HTTP_OK);
    }

}
