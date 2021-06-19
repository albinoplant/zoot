<?php


namespace App\Controller;


use App\Entity\Animal;
use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use App\ViewModel\PostAppointmentViewModel;

/**
 * @Rest\Route("/v1/appointments")
 * @OA\Tag(name="Appointments")
 */
class AppointmentController extends AbstractApiController
{

    /**
     * @Rest\Get("/{date}", name="appointments_by_day")
     * @OA\Parameter(name="date", in="path", @OA\Schema(type="string", example="19-06-2021"))
     * @OA\Response(response=200, description="Success")
     */
    public function getAppointments(string $date, EntityManagerInterface $manager): Response
    {
        $appointments = $manager->getRepository(Appointment::class)->getByDate(new \DateTime($date));

        return $this->createView($appointments, 200);
    }

    /**
     * @Rest\Post("", name="appointments_post")
     * @OA\RequestBody(@Model(type=PostAppointmentViewModel::class))
     * @OA\Response(response=200, description="Success")
     */
    public function postAppointment(Request $request, EntityManagerInterface $manager): Response
    {
        $date = $request->get('date');
        $dateObj = new \DateTime($date);
        $from = new \DateTime($dateObj->format("Y-m-d")." 08:00:00");
        $to = new \DateTime($dateObj->format("Y-m-d")." 16:00:00");

        if ($dateObj > $from && $dateObj < $to) {

                $description = $request->get('description');
                $createdAt = new \DateTime();
                $animalId = $request->get('animal_id');
                $animal = $manager->getRepository(Animal::class)->findOneBy(['id' => $animalId]);
                $appointment = new Appointment();
                $appointment->setAnimal($animal)->setCreatedAt($createdAt)->setDescription($description)->setDate(
                    new \DateTime($date)
                );
                $manager->persist($appointment);
                $manager->flush();
            return $this->createView($appointment->getId(), 200);
        } else {
            return $this->createView('This hour is not in our working hours 08:00 - 16:00', Response::HTTP_BAD_REQUEST);
        }
    }
}