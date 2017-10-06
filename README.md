# Image-lib

A simple PHP library for manipulating images nothing fancy, just some dirty codes

<h2>Usage</h2>
Just instantiate the Imgbie class and call it's methods.

```php
try {
  $image = new ImgBie($_POST['pictures']);
  // set the destination folder
  $image->setDestination('/path/to/your/uploadfolder/');
  $image->auto();
  $messages = $image->getSuccessMessage();
} catch (Exception $e){
  echo $e->getMessage();
}

```

<h2>Enjoy!</h2>
