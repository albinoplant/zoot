<?php


namespace App\Controller;


use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use App\ViewModel\PostUserViewModel;

/**
 * @Rest\Route("/v1/available-appointments")
 * @OA\Tag(name="Available Appointments")
 */
class AvailableAppointmentController extends AbstractApiController
{

    /**
     * @Rest\Get("/{date}", name="available")
     * @OA\Parameter(name="date", in="path", @OA\Schema(type="string", example="19-06-2021"))
     * @OA\Response(response=200, description="Success")
     */
    public function getAppointments(string $date,EntityManagerInterface $manager): Response
    {
        $appointments = $manager->getRepository(Appointment::class)->getByDate(new \DateTime($date));
        return $this->createView($appointments,200);
    }

//    /**
//     * @Rest\Post("", name="users_post")
//     * @OA\RequestBody(@Model(type=PostUserViewModel::class))
//     * @OA\Response(response=200, description="Success")
//     */
//    public function postUser(Request $request, EntityManagerInterface $manager): Response
//    {
//        $username = $request->get('username');
//        $user = new User();
//        $user->setUsername($username);
//        $user->setRoles(['ROLE_USER']);
//        $manager->getRepository(User::class);
//        $manager->persist($user);
//        $manager->flush();
//        return $this->createView($user->getId(),200);
//    }
}