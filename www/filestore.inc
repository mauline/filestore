<?php   # -*- php-mode -*-

    # Allowed file extensions for the upload
    $AllowedExts = array (
        '7z',
        'dat',
        'jpeg',
        'jpg',
        'pdf',
        'png',
        'rar',
        'zip'
    );

    # Sender used in outgoing e-mails
    $MailSender = "FileStore <filestore@example.com>";

    # Allowed recipient mail domains for notifications. Must include the
    # '@' sign.
    $AllowedMailDomains = array (
        '@example.com'
    );

    # Locales we support
    $SupportedLocales = array (
        'de_DE',
        'de',
        'en_US',
        'en'
    );

    # Define several variables used in the scripts
    $MainDirName = __DIR__;
    $DataDirName = $MainDirName . "/data";
    $Protocol = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $Protocol .= "s";
    }
    $MainDirURL = $Protocol . "://" .
                  $_SERVER["SERVER_NAME"] .
                  preg_replace ('/(\/data\/[0-9a-f]{64}\/?|^\/)$/',
                                "",
                                dirname ($_SERVER['SCRIPT_NAME']));
    $DataDirURL = $MainDirURL . "/data";

    # Initialize the text locale
    $Lang = 'en_US';
    if (isset ($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        # Look for the languages the browser says the user accepts
        $Langs = explode (',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        # Convert hyphen to underline in the whole list
        array_walk($Langs, function (&$Lang) {
            $Lang = strtr(strtok($Lang, ';'), ['-' => '_']);
        });
        # Search for the first matching language
        foreach ($Langs as $L) {
            if (in_array($L, $SupportedLocales)) {
                $Lang = $L;
                break;
            }
        }
    }

    # Define the global system locale given the found language
    putenv ("LANG=$Lang");

    # Also use locale specific formatting for date etc.
    setlocale(LC_ALL, $Lang);

    # Look for locale files in the "po" subdir
    bindtextdomain ('filestore', $MainDirName . '/po');

    # Specify the encoding
    bind_textdomain_codeset ('filestore', 'UTF-8');

    # Use "filestore" as default domain
    textdomain ('filestore');

    # Define random_bytes if it does not exist (comes with PHP 7 and above).
    # Throws an exception in case of errors as the PHP function does.
    if (!function_exists ('random_bytes')) {
        function random_bytes ($Length)
        {
            $H = fopen ("/dev/urandom", 'r');   # Linux only but ok for us
            if ($H) {
                $Data = fread ($H, $Length);
                fclose ($H);
                if (strlen ($Data) == $Length) return $Data;
            }
            # Error
            throw new RuntimeException (_('Error reading entropy data.'));
        }
    }

    # Function that checks for upload errors and throws an exception if we have
    # one.
    function CheckUploadError()
    {
        # Check for Undefined | Multiple Files | $_FILES Corruption Attack
        if (!isset ($_FILES['upfile']['error']) ||
            !is_int ($_FILES['upfile']['error'])) {
            throw new RuntimeException (
                _('Invalid parameter (maybe file is too big).')
            );
        }

        # Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException (_('No file received.'));
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException (_('File is too big.'));
            case UPLOAD_ERR_PARTIAL:
                throw new RuntimeException (_('File was only partially received.'));
            case UPLOAD_ERR_NO_TEMP_DIR:
                throw new RuntimeException (
                    _('Configuration error: Temp directory does not exist.')
                );
            case UPLOAD_ERR_CANT_WRITE:
                throw new RuntimeException (_('Error writing to disk.'));
            default:
                throw new RuntimeException (_('Unknown error.'));
        }

        # Ok
    }

    # Function that checks for enough disk space in the data directory. Does
    # not throw an error in all cases, but if it does, we have a more helpful
    # error message (otherwise the move will fail).
    function CheckDiskSpace ($SpaceNeeded)
    {
        global $DataDirName;
        $Space = disk_free_space ($DataDirName);
        if ($Space !== FALSE && $Space <= $SpaceNeeded) {
            throw new RuntimeException (_('Out of disk space.'));
        }
    }

    # Function that validates an email address for notification. Throws an
    # exception in case of errors.
    function ValidateEmail ($Address)
    {
        global $AllowedMailDomains;
        $Address = strtolower ($Address);
        if (strlen ($Address) < 128 && filter_var ($Address, FILTER_VALIDATE_EMAIL)) {
            foreach ($AllowedMailDomains as $Domain) {
                $Length = strlen ($Domain);
                if ($Length > 0) {
                    if (substr ($Address, -$Length) === $Domain) {
                        return;         # Ok
                    }
                }
            }
        }
        # Error
        throw new RuntimeException (_('The e-mail address is invalid.'));
    }

    # Function that validates the extension of an upload file. Throws an
    # exception in case of errors.
    function ValidateFileExt ($Filename)
    {
        global $AllowedExts;
        # Check the extension of the name. Disallow unknown extensions.
        $PathParts = pathinfo ($Filename);
        $Ext = strtolower ($PathParts['extension']);
        if (array_search ($Ext, $AllowedExts) === FALSE) {
            # Error
            throw new RuntimeException (
                sprintf (
                    _('File extension is invalid. Allowed extensions are: %s'),
                    implode (", ", $AllowedExts)
                )
            );
        }
    }

    # Function that generates the config file. Throws an exception on errors.
    function GenerateConfig ($Dir, $Description, $Done, $Email, $Permanent)
    {
        $H = fopen ($Dir . "/config.inc", "w");
        if ($H) {
            fprintf ($H, '<?php   # -*- php-mode -*-' . "\n" .
                         '    $Description = "%s";' . "\n"   .
                         '    $Done = %s;' . "\n"            .
                         '    $Email = "%s";' . "\n"         .
                         '    $Permanent = %s;' . "\n"       .
                         '?>' . "\n",
                         $Description,
                         $Done? "TRUE" : "FALSE",
                         $Email,
                         $Permanent? "TRUE" : "FALSE");
            if (fclose ($H)) {
                return;
            }
        }
        # Error
        throw new RuntimeException (_('Error generating configuration file.'));
    }

    # Function that sends an e-mail. Throws an exception on errors.
    # Note: Not throwing an error here does not mean that the mail has been
    # delivered to the recipient! It just means that the mail has been passed
    # to the MTA successfully. Delivery may still fail!
    function SendMail ($Email, $Subject, $Message)
    {
        global $MailSender;

        # Prepare the additional header
        $Headers = "Mime-Version: 1.0\r\n" .
                   "Content-type: text/plain; charset=utf-8\r\n" .
                   "From: " . $MailSender;

        # Encode the subject (needs mb_string extensions)
        # $Subject = mb_encode_mimeheader ($Subject, "UTF-8", "Q");

        # Try to send mail
        if (mail ($Email, $Subject, $Message, $Headers) !== TRUE) {
            throw new RuntimeException(
                _('Error sending the notification e-mail.')
            );
        }
    }

?>

