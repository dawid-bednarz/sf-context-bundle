<?php

/**
 *  * Dawid Bednarz( dawid@bednarz.pro )
 * Read README.md file for more information and licence uses
 */

namespace DawBed\ContextBundle;

use DawBed\ContextBundle\Entity\Context;
use DawBed\ContextBundle\Service\CreateServiceInterface;
use DawBed\PHPClassProvider\ClassProvider;
use DawBed\PHPContext\ContextInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use DawBed\ContextBundle\Model\CreateModel;

class Provider
{
    protected $types = [];
    protected $entityManager;

    public function __construct(array $types, EntityManagerInterface $entityManager)
    {
        $this->types = $types;
        $this->entityManager = $entityManager;
    }

    public function build(string $type): Context
    {
        if (!array_key_exists($type, $this->types)) {
            throw new ContextBundleException(sprintf('"%s" is not supported. Before use declare it in configuration file', $type));
        }
        /**
         * @var ServiceEntityRepository $repository
         */
        $repository = $this->entityManager->getRepository($this->getDiscriminatorName());

        $entity = $repository->findOneBy(['type' => $type]);

        if (is_null($entity)) {
            throw new ContextBundleException(sprintf('"%s" is not found in database update it by command', $type));
        }

        return $entity;
    }

    public function getData(): array
    {
        return $this->types;
    }

    public function getDiscriminatorName(): string
    {
        return ClassProvider::get(Context::class);
    }
}