<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class BaseController.
 * 
 * @author Mickael Nambinintsoa <mickael.nambinitsoa07081999@gmail.com>
 */
class BaseController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * BaseController constructeur.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param object $object
     * 
     * @return bool
     */
    public function save(object $object)
    {
        try {
            if (!$object->getId()) {
                $this->entityManager->persist($object);
            }
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param object $object
     * 
     * @return bool
     */
    public function remove(object $object)
    {
        try {
            if ($object) {
                $this->entityManager->remove($object);
            }
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param array $files
     * @param object $object
     * 
     * @return object
     */
    public function uploadFile(array $files, object $object)
    {
        foreach ($files as $file) {
            $filename = bin2hex(random_bytes(6)) . '.' . $file->guessExtension();
            $file->move($this->getParameter('image_directory'), $filename);

            $image = new Image();
            $image->setName($filename);
            $object->addImage($image);
        }
        return $object;
    }
}
