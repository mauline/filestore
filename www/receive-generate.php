<?php   # -*- php-mode -*-

    # Include functions and definitions
    require (__DIR__ . "/filestore.inc");

    # Copy a file from SrcDir to TgtDir. Throws an exception in case of errors.
    function CopyFile ($File, $SrcDir, $TgtDir)
    {
        if (!copy ($SrcDir . "/" . $File, $TgtDir . "/" . $File)) {
            throw new RuntimeException (_('Error copying files.'));
        }
    }

    try {

        # Get stuff from the post script and check it
        $Permanent = isset ($_POST['permanent']);
        $Email = isset ($_POST['email']) ? trim ($_POST['email']) : null;
        ValidateEmail ($Email);
        $Description = "";
        if (isset ($_POST['description'])) {
            $Description = htmlspecialchars (trim ($_POST['description']), ENT_NOQUOTES);
            if (strlen ($Description) > 128) {
                throw new RuntimeException (_('The description is invalid.'));
            }
        }

        # Create the subdirectory name
        $SubDirName = bin2hex (random_bytes (32));

        # Determine where to place the upload script. This will fail if the
        # directory does already exist, so in fact covers a (very unlikely)
        # collision when generating a unique directory name.
        $DirName = $DataDirName . "/" . $SubDirName;
        if (!mkdir ($DirName, 0770, TRUE)) {
             throw new RuntimeException (
                sprintf (_('Error creating directory "%s".'), $SubDirName)
             );
        }

        # Copy the upload template files to the target directory.
        $SrcDirName = __DIR__ . "/upload-templates";
        CopyFile ("index.php", $SrcDirName, $DirName);
        CopyFile ("receive-upload.php", $SrcDirName, $DirName);

        # Generate an include file containing configuration variables
        GenerateConfig ($DirName, $Description, FALSE, $Email, $Permanent);

        # Send an email with the upload URL
        $UploadURL = $DataDirURL . "/" . $SubDirName . "/";
        $Message = _('The requested upload facility has been generated.') . "\r\n" .
                   _('The upload URL is:') . "\r\n" .
                   "\r\n" .
                   $UploadURL . "\r\n";
        SendMail ($Email, _('Upload facility has been configured'), $Message);

        print ("<p><b>" . _('The upload has been configured successfully.') . "</b></p>\n");
        print ("<p>" . sprintf (_('The upload URL has been sent to <pre>%s</pre> via e-mail.'), $Email) . "</p>\n");

    } catch (Exception $E) {

        # Some sort of error occured
        printf ("<p><b>%s</b></p>\n", _('An error has occurred. The error message is:'));
        printf ("<p>%s</p>\n", $E->getMessage());

    }
?>
