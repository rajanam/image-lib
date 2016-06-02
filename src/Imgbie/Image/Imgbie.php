<?php

namespace Imgbie\Image;

use PHPImageWorkshop\ImageWorkshop;

class Imgbie
{
  /**
   * @var object
   */
  protected $imageWorkshop;

  /**
   * @
   */
  protected $originalImage;
  /**
   * @var int
   */
  protected $origImageWidth;

  /**
   * @var int
   */
  protected $origImageHeight;

  /**
   * @var int maximum size
   */
  protected $maxSize = 120;

  /**
   * @var int
   */
  protected $thumbWidth;

  /**
   * @var int
   */
  protected $thumbHeight;

  /**
   * @var string type of image
   */
  protected $imageType;

  /**
   * @var string
   */
  protected $baseName;

  /**
   * @var string
   */
  protected $destination;

  protected $successMessage = [];

  /**
   * Generates the image workshop object
   *
   * @param string $image
   */
  public function __construct($image)
  {
    if (!is_file($image) && !is_readable($image)) {
      throw new \Exception("Either the file does not exist or the file is not readable.");
    }

     $this->imageWorkshop = ImageWorkshop::initFromPath($image);

     if ($this->imageWorkshop) {
       $this->originalImage = $image;
       $this->origImageWidth = $this->imageWorkshop->getWidth();
       $this->origImageHeight = $this->imageWorkshop->getHeight();
       $this->baseName = pathinfo($image, PATHINFO_FILENAME);
       $this->imageType = pathinfo($image, PATHINFO_EXTENSION);
     }
  }

  /**
   * Sets the maximum size.
   *
   * @param int
   * @return int $maxsize
   */
  public function setMaxSize($size)
  {
    if (!is_numeric($size) && $size <= 0) {
      throw new \Exception("The given size must be greater than 0 and is numeric.");
    }
    $this->maxSize = abs($size);
  }

  /**
   * Set's the destination path of the image
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
      if (!is_dir($destination) && !is_readable($destination)) {
        throw new \Exception("The given %s path is not a directory or not readable.", $destination);
      }
      $last = substr($destination, -1);
      if ($last == '/' || $last == '\\') {
        $this->destination = $destination;
      }
  }

  /**
   * Resize to exact width and height. Aspect ratio will not be maintained
   *
   * @param int $width
   * @param int $height
   */
  public function exact($width, $height)
  {
    $this->imageWorkshop->resizeInPixel($width, $height);
    $this->checkAndSave();
  }

  /**
   * Generates the thumbnail for the given image.
   * just pass 'portrait' or 'landscape'.
   *
   * @param string $type
   */
  public function auto($type = 'portrait')
  {
    if ($type == 'portrait') {
      $ratio = $this->maxSize / $this->origImageHeight;
    } elseif ($type == 'landscape') {
      $ratio = $this->maxSize / $this->origImageWidth;
    }
    $this->thumbWidth = round($this->origImageWidth * $ratio);
    $this->thumbHeight = round($this->origImageHeight * $ratio);

    $resouceType = $this->imageResourceType();
    $thumbNail = imagecreatetruecolor($this->thumbWidth, $this->thumbHeight);

    imagecopyresampled($thumbNail, $resouceType, 0, 0, 0, 0, $this->thumbWidth, $this->thumbHeight, $this->origImageWidth, $this->origImageHeight);

    $newName = $this->baseName;
    if ($this->imageType == 'jpg') {
      $newName .= '.jpg';
      $success = imagejpeg($thumbNail, $this->destination . $newName, 100);
    } elseif ($this->imageType == 'png') {
      $newName .= '.png';
      $success = imagepng($thumbNail, $this->destination . $newName, 0);
    }

    if ($success) {
      $this->successMessage[] = "$newName created successfully!";
    }

    imagedestroy($resouceType);
    imagedestroy($thumbNail);

  }

  /**
   * Returns success message
   *
   * @return string
   */
  public function getSuccessMessage()
  {
    return $this->successMessage;
  }

  /**
   * This option will crop your images to the exact size you specify with no distortion
   *
   * @param int $width
   * @param int $height
   * @param int $positionX
   * @param int $positionY
   */
  public function crop($width, $height, $positionX, $positionY)
  {
    $this->imageWorkshop->cropInPixel($width, $height, $positionX, $positionY);
    $this->checkAndSave();
  }

  /**
   * Resize the image into square.
   *
   * @param int $width
   * @param int $height
   */
  public function square($width = 200, $height = 200)
  {
    $this->imageWorkshop->cropMaximumInPixel(0, 0, "MM");
    $this->imageWorkshop->resizeInPixel($width, $height);

    $this->checkAndSave();
  }

  /**
   * Saves the image to the given directory.
   *
   * @return string $name
   */
  protected function checkType()
  {
    $newName = $this->baseName;
    if ($this->imageType == 'jpg') {
      $newName .= '.jpg';
    } elseif ($this->imageType == 'png') {
      $newName .= '.png';
    }
    return $newName;
  }

  /**
   * Creates the image type
   *
   * @return image resource | boolean false
   */
  protected function imageResourceType()
  {
    if ($this->imageType == 'jpg') {
      return imagecreatefromjpeg($this->originalImage);
    }
    if ($this->imageType == 'png') {
      return imagecreatefrompng($this->originalImage);
    }
  }

  /**
   * Inner method for saving and adding the success message.
   */
  protected function checkAndSave()
  {
    $newName = $this->checkType();

    $success = $this->imageWorkshop->getResult();
    $this->imageWorkshop->save($this->destination, $newName, true);
    if ($success) {
      $this->successMessage[] = "$newName was successfully added!";
    }
  }
}
