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
    print ("<table style=\"padding-left:32px\">\n");
    print ("<tr>\n");
    print ("    <td style=\"padding-right:8px\"><img src=\"$MainDirURL/images/arrow-right-green-32x16.png\"></td>\n");
    print ("    <td style=\"padding:8px 0px\"><a href=\"send.php\">" . _('Upload a file that can be downloaded by someone else.') . "</a></td>\n");
    print ("</tr><tr>\n");
    print ("    <td style=\"padding-right:8px\"><img src=\"$MainDirURL/images/arrow-left-red-32x16.png\"></td>\n");
    print ("    <td style=\"padding:8px 0px\"><a href=\"receive.php\">" . _('Allow someone else to upload a file for me.') . "</a></td>\n");
    print ("</tr>\n");
    print ("</table>\n");

    # End of page
    EndBody ();
?>
