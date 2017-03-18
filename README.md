# HTML to PDF for zend framework

Create pdf from view in ZendFramework 2


## Requirements

 - wkhtmltopdf installed on your server
    - Debian, Ubuntu:
     
     ```bash
     sudo apt-get install wkhtmltopdf
     ```
    - *Debian servers have known errors with wkhtmltopdf lib, so if you get error like:
     ```bash
     wkhtmltopdf: cannot connect to X server
     ```
     install xvfb:
     
     ```bash
     sudo apt-get install xvfb
     ```
 - zend-view 2.4* is installed as dependency
 - zend-service-manager is already installed with 2.4 version of zf2.
 
 
## Install
 - Composer install
     ```bash
      composer require takeoo/zend-pdf
      ```
## Usage
 - Takeoo\Zend-pdf will create service throughout service manager, so it is available in serviceLocator as 'PdfCreator';

 - Instantiate:
 
  ```php
  $pdfCreator = $serviceManager->get('PdfCreator');
  ```
  
   **NOTE:** PdfCreator is not shared service, so every call to serviceManager will create new instance.
   
   PdfCreator uses ViewResolver which is auto-magically injected to PhpRenderer if have MVC application, so you can use any view or layout you have in your app!
   - Set layout
   
       ```php
        $pdfCreator->setLayoutTemplate('layout/pdf-template');
       ```
       - Add view as you would in your response, just pass name and variables to *createHtml()* function
       
       ```php
       $pdfCreator->createHtml('path/to/view', ['variableName' => $variableValue]);
       ```
           
       OR you can directly pass already created view model
       
       ```php
       $pdfCreator->createHtmlFromViewModel($viewModel);
       ```
   
   - Output
        - All modern browser support pdf output directly to browser:
        
            ```php
            $pdfCreator->output();
            ```
        -  get file handle
        
           ```php
           $pdfCreator->writePdf();
           ```
           
     **NOTE:** Both above functions will save file to your disk
         
   - Get file path
       ```php
       $pdfCreator->getFilePath();
       ```
       
   - By default all files are generated to your project root into "{Ymd_His}.pdf"
   
   - Change file destination
        ```php
        $pdfCreator->setPdfFileName('path/to/desired/folder/nameofyourfile.pdf');
        ```
        
        
   **NOTE:** All functions with public scope (with exception of output() and writePdf()) are fluent, so you can chain all functions:
   
   ```php
   $pdfCreator->setLayoutTemplate('layout/pdf-layout')
     ->createHtml('view', ['variable1' => $variable1Value])
     ->setPdfFileName('./../file.pdf')
     ->setHasXvfb(false)
     ->output();
   ```
   
   
   
   **NOTE:** By default all wkhtmltopdf conversions are created with  *setHasXvfb(true)*, so you have to have installed  **Xvfb** on your server!
   If you want to turn it of just do:
   
```php
$pdfCreator->setHasXvfb(false);
```
        
  
     
     
     
     
     
 