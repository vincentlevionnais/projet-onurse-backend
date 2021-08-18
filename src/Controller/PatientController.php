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
    public function Browse(PatientRepository $patientRepository): Response
    {
        $patients = $patientRepository->findAll();

        // On demande à Symfony de "sérialiser" nos entités sous forme de JSON
        return $this->json($patients, 200, [], ['groups' => 'patients_get']);
    }

     /**
     * Get one patient by id
     * 
     * @Route("/api/patients/{id<\d+>}", name="api_patients_get_item", methods="GET")
     */
    public function read(Patient $patient): Response
    {       
        return $this->json($patient, Response::HTTP_OK, [], ['groups' => 'patients_get']);
    }


    /**
     * Edit patient by id
     * 
     * @Route("/api/patients/{id<\d+>}", name="api_patients_put_item", methods={"PUT", "PATCH"})
     */
    public function Edit(Patient $patient = null, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Si patient non trouvé
        if ($patient === null) {
            return new JsonResponse(["message" => "Patient non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // Si oui on récupère les données de la requête
        $data = $request->getContent();

        // @todo Pour PUT, s'assurer qu'on ait un certain nombre de champs
        // @todo Pour PATCH, s'assurer qu'on au moins un champ
        // sinon => 422 HTTP_UNPROCESSABLE_ENTITY

        // On désérialise le JSON vers l'entité Patient existante
        $patient = $serializer->deserialize($data, Patient::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $patient]);

        // On valide l'entité avec le service Validator
        $errors = $validator->validate($patient);

        // Gestion de l'affichage des erreurs
        if (count($errors) > 0) {

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

            // On crée untableau d'erreurs
            $newErrors = [];

            foreach ($errors as $error) {
                // Astuce ici ! on poush dans un tabbleau
                // = similaire à la structure des Flash Messages
                // On push le message, à la clé qui contient la propriété                
                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return new JsonResponse(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Enregistrement en BDD
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
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        // On désérialise le JSON vers une nouvelle entité Patient
        // @see https://symfony.com/doc/current/components/serializer.html#deserializing-an-object
        $patient = $serializer->deserialize($jsonContent, Patient::class, 'json');

        // On valide l'entité avec le service Validator
        $errors = $validator->validate($patient);

        // Gestion de l'affichage des erreurs
        // ($errors se comporte comme un tableau et contient un élément par erreur)
        if (count($errors) > 0) {
            return $this->json(["errors" => $errors],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //dd($movie);

        // On prépare à faire persister en BDD, on flush
        $entityManager->persist($patient);
        $entityManager->flush();

        // REST nous demande un statut 201 et un header Location: url
        //! Si on le fait "à la mano" voir autre manière de faire ?
        return $this->json(
            // Le film que l'on retourne en JSON directement au front
            $patient,
            // Le status code
            // C'est cool d'utiliser les constantes de classe !
            // => ça aide à la lecture du code et au fait de penser objet
            Response::HTTP_CREATED,
            // Un header Location + l'URL de la ressource créée
            ['Location' => $this->generateUrl('api_patients_get_item', ['id' => $patient->getId()])],
            //!todo à vérifier après avoir mis les relations sur les entités
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

        //Gestion des erreurs
        if (null === $patient) {

            $error = 'Ce patient n\'existe pas';

            return $this->json(['error' => $error], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($patient);
        $entityManager->flush();

        return $this->json(['message' => 'Le patient a bien été supprimé.'], Response::HTTP_OK);
    }

}
