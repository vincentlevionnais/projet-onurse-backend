<?php

// src\Normalizer\EntityNormalizer.php

namespace App\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Entity normalizer
 */
class EntityNormalizer implements DenormalizerInterface
{
    /** @var EntityManagerInterface **/
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Ce dénormalizer doit-il s'appliquer sur la donnée courante ?
     * Si oui, on appelle $this->denormalize() méthode en dessous
     * 
     * $data => l'id du genre
     * $type => le type de la classe vers laquelle on souhaite dénormalizer
     * 
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        //est ce que la classe est de type entité Doctrine ?
        //est-ce que la donnée fournie est numérique?
        return strpos($type, 'App\\Entity\\') === 0 && (is_numeric($data));
    }

    /**
     * Cette méthode est appellée si la condition du dessus est valide
     * 
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        //raccourci depuis l'EM pour aller chercher l'entité
        return $this->em->find($class, $data);
    }
}
