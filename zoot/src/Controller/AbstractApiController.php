<?php


namespace App\Controller;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;


abstract class AbstractApiController extends AbstractFOSRestController
{
    /**
     * @var AutoMapperInterface
     */
    protected AutoMapperInterface $mapper;

    public function __construct(AutoMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param array|object $source
     * @param string $targetClass
     * @return mixed|null
     */
    public function map($source, string $targetClass)
    {
        try {
            return is_array($source)
                ? $this->mapper->mapMultiple($source, $targetClass)
                : $this->mapper->map($source, $targetClass);
        } catch (UnregisteredMappingException $e) {
            return null;
        }
    }

    /**
     * @param object $entity
     */
    public function saveEntity(object $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param mixed $data
     * @param int $statusCode
     * @return Response
     */
    public function createView($data, int $statusCode = 200): Response
    {
        $view = $this->view($data, $statusCode);

        return $this->handleView($view);
    }
}