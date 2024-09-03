<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Background Removal App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Product Image Background Removal</h1>
        <form action="process_image.php" method="POST" enctype="multipart/form-data">
            <label for="file">Upload your product image:</label>
            <input type="file" name="file" id="file" required>
            <label for="bg_color">Choose background color:</label>
            <input type="color" name="bg_color" id="bg_color" value="#ffffff">
            <input type="submit" name="submit" value="Process Image">
        </form>
    </div>
</body>
</html>
