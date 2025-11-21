<?php
declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return static function (ContainerInterface $container): EntityManagerInterface {
    /** @var EntityManagerInterface $em */
    $em = $container->get('doctrine.orm.entity_manager');
    return $em;
};
