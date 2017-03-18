<?php

namespace Takeoo\Pdf\Model\Factory;


use Takeoo\Pdf\Model\PdfCreator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PdfCreatorFactory implements FactoryInterface
{

  /**
   * Create service
   *
   * @param ServiceLocatorInterface $serviceLocator
   * @return mixed
   */
  public function createService (ServiceLocatorInterface $serviceLocator)
  {
    $pdfCreator = new PdfCreator();

    $pdfCreator->setViewRenderer($serviceLocator->get('ViewRenderer'));

    return $pdfCreator;
  }
}