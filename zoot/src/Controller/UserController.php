<?php


namespace App\Controller;

use App\Entity\Animal;
use App\Entity\User;
use App\Entity\Vet;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use App\ViewModel\PostUserViewModel;
use App\ViewModel\PostAnimalViewModel;
/**
 * @Rest\Route("/v1/users")
 * @OA\Tag(name="Users")
 */
class UserController extends AbstractApiController
{
    /**
     * @Rest\Get("", name="users_get")
     * @OA\Response(response=200, description="Success")
     */
    public function getUsers(EntityManagerInterface $manager): Response
    {
        $users = $manager->getRepository(User::class)->findAll();
        return $this->createView($users,200);
    }

    /**
     * @Rest\Post("", name="users_post")
     * @OA\RequestBody(@Model(type=PostUserViewModel::class))
     * @OA\Response(response=200, description="Success")
     */
    public function postUser(Request $request, EntityManagerInterface $manager): Response
    {
        $username = $request->get('username');
        $isVet = $request->get('is_vet');
        $user = new User();
        $user->setUsername($username);
        $user->setRoles(['ROLE_USER']);
        $manager->getRepository(User::class);
        $manager->persist($user);
        if($isVet){
            $vet = new Vet();
            $vet->setUser($user);
            $manager->persist($vet);
        }
        $manager->flush();
        return $this->createView($user->getId(),200);
    }

    /**
     * @Rest\Get("/{id}/animals", name="users_animals_get")
     * @OA\Parameter(name="id", in="path", @OA\Schema(type="string", example="1"))
     * @OA\Response(response=200, description="Success")
     */
    public function getUsersAnimals(string $id, EntityManagerInterface $manager): Response
    {
        $animals = $manager->getRepository(Animal::class)->findBy(['owner'=>$id]);
        return $this->createView($animals,200);
    }

    /**
     * @Rest\Post("/{id}/animals", name="users_animals_post")
     * @OA\Parameter(name="id", in="path", @OA\Schema(type="string", example="1"))
     * @OA\RequestBody(@Model(type=PostAnimalViewModel::class))
     * @OA\Response(response=200, description="Success")
     */
    public function postUsersAnimals(Request $request, string $id, EntityManagerInterface $manager): Response
    {
        $user = $manager->getRepository(User::class)->findOneBy(['id'=>$id]);
        $animal = new Animal();
        $name = $request->get('name');
        $weight = $request->get('weight');
        $animal->setName($name)->setWeight($weight);
        $user->addAnimal($animal);
        $manager->persist($user);
        $manager->persist($animal);
        $manager->flush();
        return $this->createView($animal->getId(),200);
    }
}

