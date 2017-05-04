<?php

namespace Takeoo\Pdf\Model\Factory;


use Takeoo\Pdf\Model\PdfCreator;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class PdfCreatorFactory implements FactoryInterface
{

  /**
   * Create an object
   *
   * @param  ContainerInterface $container
   * @param  string $requestedName
   * @param  null|array $options
   * @return object
   * @throws ServiceNotFoundException if unable to resolve the service.
   * @throws ServiceNotCreatedException if an exception is raised when
   *     creating a service.
   * @throws ContainerException if any other error occurs
   */
  public function __invoke (ContainerInterface $container, $requestedName, array $options = null)
  {
    $pdfCreator = new PdfCreator();

    $pdfCreator->setViewRenderer($container->get('ViewRenderer'));

    return $pdfCreator;
  }
}