<?php   # -*- php-mode -*-

    # Include functions and definitions
    require (__DIR__ . "/../../filestore.inc");

    # Include the configuration definitions
    require (__DIR__ . "/config.inc");

    # Remove a file. Throw an exception in case of errors
    function RemoveFile ($Name)
    {
        if (!unlink ($Name)) {
            throw new RuntimeException (_('Error deleting files.'));
        }
    }

    try {

        # Make sure nothing we create is readable by others
        umask (umask() | 0007);

        # Get stuff from the post script and check it
        $UserCode = isset ($_POST['code']) ? trim ($_POST['code']) : "";
        if ($Code !== $UserCode) {
            throw new RuntimeException (_('Invalid remove code.'));
        }

        # Remove all files in the directory, then the directory itself.
        # Remove the config file first, since without it, the others won't
        # run and the cleanup function will take care.
        # Remove known files one by one so we force a check if something
        # changes.
        RemoveFile (__DIR__ . "/config.inc");
        RemoveFile (__DIR__ . "/index.php");
        RemoveFile (__DIR__ . "/receive-upload.php");
        RemoveFile (__DIR__ . "/remove.php");
        RemoveFile (__DIR__ . "/remove-action.php");
        if (!rmdir (__DIR__)) {
            throw new RuntimeException (_('Error deleting the directory.'));
        }

        # Tell the user about the successful remove
        printf ("<p><b>%s</b></p>\n", _('The upload page has been removed!'));

    } catch (Exception $E) {

        # Some sort of error occured
        printf ("<p><b>%s</b></p>\n", _('An error has occurred. The error message is:'));
        printf ("<p>%s</p>\n", $E->getMessage());

    }
?>
