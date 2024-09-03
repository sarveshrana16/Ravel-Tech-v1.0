<?php

if (extension_loaded('imagick')) {
    echo 'ImageMagick is installed and enabled.';
} else {
    echo 'ImageMagick is not installed or not enabled.';
}


if (isset($_POST['submit'])) {
    $uploadedFile = $_FILES['file']['tmp_name'];
    $bgColor = isset($_POST['bg_color']) ? $_POST['bg_color'] : '#FFFFFF';
    $outputDir = 'output/';

    if (extension_loaded('imagick')) {
        $imagick = new Imagick($uploadedFile);

        // Step 1: Convert the image to grayscale and create a mask
        $imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_DEACTIVATE);
        $imagick->setImageColorspace(Imagick::COLORSPACE_GRAY);
        $imagick->thresholdImage(0.8 * Imagick::getQuantum());

        // Step 2: Use the mask to identify the background
        $mask = clone $imagick;
        $mask->negateImage(false);

        // Step 3: Set the new background color
        $background = new Imagick();
        $background->newImage($imagick->getImageWidth(), $imagick->getImageHeight(), $bgColor);

        // Step 4: Composite the new background with the original image
        $background->compositeImage($imagick, Imagick::COMPOSITE_DSTOVER, 0, 0);
        $background->compositeImage($mask, Imagick::COMPOSITE_COPYOPACITY, 0, 0);

        // Step 5: Save the image
        $outputFile = $outputDir . uniqid() . '.png';
        $background->writeImage($outputFile);

        // Clean up
        $imagick->clear();
        $background->clear();
        $mask->clear();

        echo "<p>Image processed successfully. <a href='$outputFile'>Download it here</a></p>";
    } else {
        echo "Imagick extension is not installed.";
    }
} else {
    echo "No file uploaded.";
}
