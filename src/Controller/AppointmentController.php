<?php

namespace App\Controller;

use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AppointmentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class AppointmentController extends AbstractController
{
    /**
     * Get appointments collection
     * 
     * @Route("/api/appointments", name="api_appointments_get", methods="GET")
     */
    public function browse(AppointmentRepository $appointmentRepository): Response
    {
        $appointments = $appointmentRepository->findAll();

        // Resquest to Symfony to "serialize" entities in form of JSON
        return $this->json($appointments, 200, [], []);
    }

    /**
     * Get an appointment by id
     * 
     * @Route("/api/appointments/{id<\d+>}", name="api_appointments_get_item", methods="GET")
     */
    public function read(Appointment $appointment): Response
    {       
        return $this->json($appointment, Response::HTTP_OK, [], []);
    }

    /**
     * Create a new appointment item
     * 
     * @Route("/api/appointments", name="api_appointments_post", methods="POST")
     */
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        // Deserialization of JSON to an entity Appointment
        // @see https://symfony.com/doc/current/components/serializer.html#deserializing-an-object
        $appointment = $serializer->deserialize($jsonContent, Appointment::class, 'json');

        // Validate with the Validator service
        $errors = $validator->validate($appointment);

        // If validation encounters errors
        // ($errors behaves like an array and contains an element by mistake)
        if (count($errors) > 0) {
            return $this->json(["errors" => $errors],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //dd($appointment);

        // persist, and flush
        $entityManager->persist($appointment);
        $entityManager->flush();

        // REST ask us a 201 status and a header Location: url
        // if we do it by ourselves
        return $this->json(
            // The appointment that we return in JSON directly to the front
            $appointment,
            // the status code
            Response::HTTP_CREATED,
            // A header Location + URL of the created resource
            ['Location' => $this->generateUrl('api_appointments_get_item', ['id' => $appointment->getId()])],
            // The serialization group so that $appointment is serialized without circular reference errors
            []
        );

    }

    /**
     * Edit an appointment by id
     * 
     * @Route("/api/appointments/{id<\d+>}", name="api_appointments_put_item", methods={"PUT", "PATCH"})
     */
    public function edit(Appointment $appointment = null, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Appointment not found
        if ($appointment === null) {
            return new JsonResponse(["message" => "Rendez-vous non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // Retrieve the request data
        $data = $request->getContent();

        //TODO Pour PUT, s'assurer qu'on ait un certain nombre de champs
        //TODO Pour PATCH, s'assurer qu'on au moins un champ
        //TODO sinon => 422 HTTP_UNPROCESSABLE_ENTITY

        // Deserialize the JSON to the *existing Appointment entity*
        // @see https://symfony.com/doc/current/components/serializer.html#deserializing-in-an-existing-object
        $appointment = $serializer->deserialize($data, Appointment::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $appointment]);

        // Validate entity
        $errors = $validator->validate($appointment);

        // Error display
        if (count($errors) > 0) {

            // Goal: create this output format
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
                // We push in an array
                // = similar to structure of Flash Messages
                // We push the message, to the key that contains the property               
                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return new JsonResponse(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Database recording
        $entityManager->flush();

        //TODO Conditionner le message de retour au cas où l'entité ne serait pas modifiée
        return new JsonResponse(["message" => "Rendez-vous modifié"], Response::HTTP_OK);
    }

    /**
     * Delete an appointment by id
     * 
     * @Route("/api/appointments/{id<\d+>}", name="api_appointments_delete", methods="DELETE")
     */
    public function delete(Appointment $appointment = null, EntityManagerInterface $em)
    {
        if (null === $appointment) {

            $error = 'Ce film n\'existe pas';

            return $this->json(['error' => $error], Response::HTTP_NOT_FOUND);
        }

        $em->remove($appointment);
        $em->flush();

        return $this->json(['message' => 'Rendez-vous supprimé.'], Response::HTTP_OK);
    }

}
