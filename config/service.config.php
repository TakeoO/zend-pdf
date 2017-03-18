<?php

return [
  'service_manager' => [
    'factories' => [
      'PdfCreator' => \Takeoo\Pdf\Model\Factory\PdfCreatorFactory::class
    ],
    'shared' => [
      'PdfCreator' => false
    ]
  ]
];