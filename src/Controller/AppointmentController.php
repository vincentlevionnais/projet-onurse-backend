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
        $appointments = $appointmentRepository->findby(['nurse' => $this->getUser()]);

        // Resquest to Symfony to "serialize" entities in form of JSON
        return $this->json($appointments, Response::HTTP_OK, [], ['groups' => 'appointment_get']);
    }

    /**
     * Get an appointment of one nurse by id
     * 
     * @Route("/api/appointments/{id<\d+>}", name="api_appointments_get_item", methods="GET")
     */
    public function read(Appointment $appointment = null): Response
    {
        if ($appointment === null) {
            return new JsonResponse(["message" => "Rendez-vous non trouvé"], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();

        $nurseAppointment = $appointment->getNurse();

        if ($user != $nurseAppointment) {
            return new JsonResponse(["message" => "Rendez-vous non trouvé"], Response::HTTP_NOT_FOUND);
        }
        return $this->json($appointment, Response::HTTP_OK, [], ['groups' => 'appointment_get']);
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
        $appointment = $serializer->deserialize($jsonContent, Appointment::class, 'json');

        $appointment->setNurse($this->getUser());

        // Validate with the Validator service
        $errors = $validator->validate($appointment);

        // If validation encounters errors
        // ($errors behaves like an array and contains an element by mistake)
        if (count($errors) > 0) {

            $newErrors = [];

            foreach ($errors as $error) {

                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return new JsonResponse(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Database recording
        $entityManager->persist($appointment);
        $entityManager->flush();

        return $this->json(
            $appointment,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_appointments_get_item', ['id' => $appointment->getId()])],
            ['groups' => 'appointment_get']
        );
    }

    /**
     * Edit an appointment by id
     * 
     * @Route("/api/appointments/{id<\d+>}", name="api_appointments_put_item", methods={"PUT", "PATCH"})
     */
    public function edit(Appointment $appointment = null, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($appointment === null) {
            return new JsonResponse(["message" => "Rendez-vous non trouvé"], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();
        $nurseAppointment = $appointment->getNurse();

        if ($user != $nurseAppointment) {
            return new JsonResponse(["message" => "Rendez-vous non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // Retrieve the request data
        $data = $request->getContent();

        // Deserialize the JSON to the *existing Appointment entity*
        $appointment = $serializer->deserialize($data, Appointment::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $appointment]);

        // Validate entity
        $errors = $validator->validate($appointment);

        // Error display
        if (count($errors) > 0) {

            $newErrors = [];

            foreach ($errors as $error) {

                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return new JsonResponse(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Database recording
        $entityManager->flush();

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
            $error = 'Rendez-vous non trouvé';
            return $this->json(['error' => $error], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();
        $nurseAppointment = $appointment->getNurse();

        if ($user != $nurseAppointment) {
            return new JsonResponse(["message" => "Rendez-vous non trouvé"], Response::HTTP_NOT_FOUND);
        }
        // Database remove
        $em->remove($appointment);
        $em->flush();

        return $this->json(['message' => 'Rendez-vous supprimé.'], Response::HTTP_OK);
    }
}
