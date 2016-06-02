<?php

require __DIR__ . '/vendor/autoload.php';

use Imgbie\Image\Imgbie;

$folder = 'C:/xampp/htdocs/ImageLibrary/images/';

if (isset($_POST['submit'])) {
  try {
      $image = new ImgBie($_POST['pictures']);
      // just add a path to your upload folder
      $image->setDestination('C:/upload_test/thumbs/');
      // $image->exact(100, 100);
      // $image->crop(80, 70, 5, 3);
      //$image->square();
      $image->auto();
      $messages = $image->getSuccessMessage();
  } catch (Exception $e) {
      echo $e->getMessage();
  }
}

?>
<html>
<head>
    <title>Image Library</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" media="screen" title="no title" charset="utf-8">
</head>
<body>
<div class="form-group">
  <?php if (isset($messages) && !empty($messages)) :?>
      <ul>
        <?php foreach ($messages as $message): ?>
          <li><?= $message ?></li>
        <?php endforeach; ?>
      </ul>
<?php endif; ?>
  <form method="post" action="" class="navbar-form navbar-left">
        <h1 class=" ">Select Images</h1>
        <select name="pictures" id="pictures" class="form-control">
            <option value="">Select an image</option>
            <?php

                $files = new FilesystemIterator('./images');
                $images = new RegexIterator($files, '/\.(?:jpg|png|gif)$/i');
                foreach ($images as $image):
                    $filename = $image->getFilename();
            ?>
            <option value="<?= $folder . $filename; ?>"><?= $filename; ?></option>
            <?php endforeach; ?>
        </select>
        <p style="margin-top: 20px;">
            <input type="submit" name="submit" class="btn btn-primary" value="Create Thumbnail">
        </p>
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" />
</body>
</html>
