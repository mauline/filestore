<?php   # -*- php-mode -*-

    # Include functions and definitions
    require (__DIR__ . "/../../filestore.inc");

    # Include the configuration definitions
    require (__DIR__ . "/config.inc");

    try {

        # Check for upload errors
        CheckUploadError ();

        # Check if we already had an upload
        if ($Done) {
            throw new RuntimeException (
                _('A file has already been uploaded.')
            );
        }

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
        $SubDirName = bin2hex (random_bytes (32));

        # Determine where to place the uploaded file. This will fail if the
        # directory does already exist, so in fact covers a (very unlikely)
        # collision when generating a unique directory name.
        $DirName = $DataDirName . "/" . $SubDirName;
        if (!mkdir($DirName, 0770, TRUE)) {
             throw new RuntimeException (
                _('Error creating the download directory.')
             );
        }

        # Move the uploaded file to this directory
        $DestName = $DirName . "/" . $CleanName;
        if (!move_uploaded_file($_FILES['upfile']['tmp_name'], $DestName)) {
            throw new RuntimeException (
                _('Error moving received file to destination.')
            );
        }

        # If this is not a permanent upload facility, generate a new config
        # setting $Done to true.
        if (!$Permanent) {
            GenerateConfig (__DIR__, $Description, TRUE, $Email, $Permanent);
        }

        # Success! Send an email
        $DownloadURL = $DataDirURL . "/" . $SubDirName . "/" . $CleanName;
        $Message = _('A file has been uploaded for you:') . "\r\n" .
                   "\r\n" .
                   $DownloadURL . "\r\n" .
                   "\r\n";
        if (!empty ($Description)) {
            $Message .= _('Description: ') . $Description . "\r\n\r\n";
        }
        SendMail ($Email, _('Upload available'), $Message);

        # Tell the user about the successful upload
        printf ("<p><b>%s</b></p>\n", _('The file has been successfully uploaded and a notification e-mail has been sent!'));

    } catch (Exception $E) {

        # Some sort of error occured
        printf ("<p><b>%s</b></p>\n", _('An error has occurred. The error message is:'));
        printf ("<p>%s</p>\n", $E->getMessage());

    }
?>
