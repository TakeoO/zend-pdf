<?php

namespace Takeoo\Pdf\Model;

use Takeoo\Pdf\Exception\FileException;
use Takeoo\Pdf\Exception\HtmlException;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

/**
 * Class PdfCreator
 * @package Takeoo\Pdf\Model
 */
class PdfCreator
{
  /** @var string */
  protected $commandArgs = 'wkhtmltopdf [SOURCE] [TARGET]';

  protected $xvfb = 'xvfb-run --server-args="-screen 0, 1240x860x24"';

  protected $marginCommand = ' --margin-%s %s';

  /** @var string $tempFileName */
  protected $tempFileName;

  /** @var  string */
  protected $command;

  /** @var  ViewModel */
  private $layout;

  /** @var PhpRenderer */
  private $viewRenderer = null;

  private $margins = [];

  private $allowedMargins = ['top', 'bottom', 'left', 'right'];

  /** @var string $layoutTemplate */
  private $layoutTemplate = 'layout/layout';

  /** @var null $html */
  private $html = null;

  /** @var string $pdfFileName */
  private $pdfFileName = null;

  /** @var string $tempPath */
  private $tempPath = __DIR__ . '/../../tmp/html';

  /** @var bool */
  private $hasXvfb = true;

  /**
   * PdfCreator constructor.
   */
  public function __construct ()
  {
    $this->generateTempHtmlFileName();
    $this->generatePdfFileName();
  }

  /**
   * @return PhpRenderer
   */
  public function getViewRenderer ()
  {
    return $this->viewRenderer;
  }

  /**
   * @param PhpRenderer $viewRenderer
   * @return $this
   */
  public function setViewRenderer (PhpRenderer $viewRenderer)
  {
    $this->viewRenderer = $viewRenderer;

    return $this;
  }

  /**
   * @return null
   */
  public function getLayoutTemplate ()
  {
    return $this->layoutTemplate;
  }

  /**
   * @param null $layout
   * @return $this
   */
  public function setLayoutTemplate ($layout)
  {
    $this->layoutTemplate = $layout;

    return $this;
  }

  /**
   * @param string $viewName
   * @param array $variables
   * @return PdfCreator
   */
  public function createHtml (string $viewName, array $variables = [])
  {
    $viewModel = $this->createViewModel($viewName, $variables);
    return $this->createHtmlFromViewModel($viewModel);
  }


  /**
   * @param ViewModel $viewModel
   * @return $this
   */
  public function createHtmlFromViewModel (ViewModel $viewModel)
  {
    $view = $this->createLayout();

    $childHtml = $this->getViewRenderer()
      ->render($viewModel);

    $view->setVariable('content', $childHtml);

    $html = $this->getViewRenderer()
      ->render($view);

    $this->html = $html;

    return $this;
  }

  /**
   * Generates actual pdf
   * @return string
   */
  public function writePdf ()
  {
    $this->generatePdfFile();

    return file_get_contents($this->pdfFileName);
  }


  /**
   * Writes to to windows
   */
  public function output ()
  {
    $this->generatePdfFile();

    header('Content-Type: application/pdf');
    header('Content-disposition: inline; filename="' . $this->pdfFileName . '"');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

    echo file_get_contents($this->pdfFileName);
  }

  /**
   * Creates temporary html file
   * @return int
   * @throws FileException
   * @throws HtmlException
   */
  private function createTemporaryHtml ()
  {
    if (!$this->html)
      throw new HtmlException('No html provided!');

    $saved = file_put_contents($this->tempFileName, $this->html);

    if (!$saved)
      throw new FileException('Temporary file cannot be saved, check permissions');

    return $saved;
  }

  /**
   * Creates wrapper view
   * @return ViewModel
   */
  private function createLayout ()
  {
    if ($this->layout instanceof ViewModel)
      return $this->layout;

    $this->layout = $this->createViewModel($this->layoutTemplate);
    $this->layout->setTerminal(true);
    return $this->layout;
  }

  /**
   * @param string $viewName
   * @param array $variables
   * @return ViewModel
   */
  private function createViewModel (string $viewName, array $variables = [])
  {
    $viewModel = new ViewModel($variables);
    $viewModel->setTemplate($viewName);
    return $viewModel;
  }


  /**
   * Returns string for a file
   * @return string
   */
  private function generateTempHtmlFileName ()
  {
    $this->tempFileName = $this->tempPath . '/' . sha1(rand(0, 5) . time()) . '.html';

    return $this->tempFileName;
  }

  /**
   * Build command for excecution
   * @return $this
   */
  private function buildCommand ()
  {
    $command = '';

    if ($this->isHasXvfb())
      $command = $this->xvfb;

    $commandArgs = str_replace('[SOURCE]', escapeshellarg($this->tempFileName), $this->commandArgs);
    $commandArgs = str_replace('[TARGET]', escapeshellarg($this->pdfFileName), $commandArgs);

    if (count($this->margins))
      foreach ($this->margins as $position => $value)
        $commandArgs .= sprintf($this->marginCommand, $position, $value);


    $this->command = $command;

    return $this;
  }

  /**
   * Execute command
   * @return $this
   * @throws FileException
   */
  private function executeCommand ()
  {
    $response = exec($this->command);

    $isSuccessfullyDone = strpos($response, 'Done') > - 1;

    if (!$isSuccessfullyDone)
      throw new FileException(sprintf('Cannot create pdf file [%s]', $response));

    return $this;
  }

  /**
   * @return bool
   */
  public function isHasXvfb (): bool
  {
    return $this->hasXvfb;
  }

  /**
   * @param bool $hasXvfb
   */
  public function setHasXvfb (bool $hasXvfb)
  {
    $this->hasXvfb = $hasXvfb;
  }

  /**
   * Generate new pdf
   */
  private function generatePdfFileName ()
  {
    $this->pdfFileName = date('Ymd_His') . '.pdf';
  }

  /**
   * Deletes temporary file
   */
  private function cleanUp ()
  {
    unlink($this->tempFileName);
  }

  /**
   * Generates pdf file
   */
  private function generatePdfFile ()
  {
    $this->createTemporaryHtml();

    $this->buildCommand();

    $this->executeCommand();

    $this->cleanUp();
  }

  /**
   * @param string $pdfFileName
   * @return PdfCreator
   */
  public function setPdfFileName (string $pdfFileName): PdfCreator
  {
    $this->pdfFileName = $pdfFileName;
    return $this;
  }

  /**
   * @param $position
   * @param $margin
   * @return PdfCreator
   * @internal param array $margins
   */
  public function setMargins ($position, $margin)
  {
    if (in_array($position, $this->allowedMargins) && is_numeric($margin))
      $this->margins[$position] = $margin;

    return $this;
  }

}