<?php   # -*- php-mode -*-
    # Include functions and definitions
    require (__DIR__ . "/filestore.inc");

    # Include page layout functions
    require (__DIR__ . "/layout.inc");

    # Generate the page header, start body
    MakeHeader ("FileStore");
    StartBody ("FileStore");

    # Menue
    print ("<p>" . _('I want to:') . "</p>\n");
    print ("<ul style=\"list-style-type: none\">\n");
    print ("<li><a href=\"send.php\">" . _('Upload a file that can be downloaded by someone else.') . "</a></li>\n");
    print ("<li><a href=\"receive.php\">" . _('Allow someone else to upload a file for me.') . "</a></li>\n");
    print ("</ul>\n");

    # End of page
    EndBody ();
?>
