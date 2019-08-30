<?php   # -*- php-mode -*-

    # Include functions and definitions
    require (__DIR__ . "/filestore.inc");

    try {

        # Check for upload errors
        CheckUploadError ();

        # Check the extension of the name. Disallow unknown extensions.
        ValidateFileExt ($_FILES['upfile']['name']);

        # Sanitize the file name. We will first remove a path, then
        # replace any unsafe characters by an underline and finally
        # check the length.
        $OrigName = $_FILES['upfile']['name'];
        $CleanName = preg_replace ("/[^-0-9A-Z_\.]/i", "_", basename ($OrigName));
        if (strlen ($CleanName) <= 0 || strlen ($CleanName) > 64) {
             throw new RuntimeException (_('Invalid filename.'));
        }

        # Check for free space in the target directory. This isn't strictly
        # necessary, since moving the uploaded file otherwise, but checking
        # it here enables us to give better error messages.
        CheckDiskSpace ($_FILES['upfile']['size']);

        # Create the subdirectory name
        $SubDirName = bin2hex (random_bytes (16));

        # Determine where to place the uploaded file. This will fail if the
        # directory does already exist, so in fact covers a (very unlikely)
        # collision when generating a unique directory name.
        $DirName = $DataDirName . "/" . $SubDirName;
        if (!mkdir ($DirName, 0770, TRUE)) {
             throw new RuntimeException (
                sprintf (_('Error creating directory "%s".'), $SubDirName)
             );
        }

        # Move the uploaded file to this directory
        $DestName = $DirName . "/" . $CleanName;
        if (!move_uploaded_file($_FILES['upfile']['tmp_name'], $DestName)) {
            throw new RuntimeException (
                _('Error moving received file to destination.')
            );
        }

        # Success! Tell the user and show the download URL.
        $DownloadURL = $DataDirURL . "/" . $SubDirName . "/" . $CleanName;
        printf ("<p><b>%s</b></p>\n", _('The file has been successfully uploaded!'));
        printf ("<p>%s</p>\n", _('The download URL is:'));
        printf ("<pre><p>%s</p></pre>\n", $DownloadURL);

    } catch (Exception $E) {

        # Some sort of error occured
        printf ("<p><b>%s</b></p>\n", _('An error has occurred. The error message is:'));
        printf ("<p>%s</p>\n", $E->getMessage());

    }
?>
