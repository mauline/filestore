<?php	# -*- php-mode -*-

    # This PHP include file defines functions that determine the layout of
    # the user visible pages.

    # Function to create the page header
    function MakeHeader ($Title, $Script = null)
    {
        global $Lang;
        $L = substr ($Lang, 0, 2);
    	$Desc = htmlspecialchars ($Title);
       	print ("<!DOCTYPE HTML>\n");
       	print ("<html lang=\"$L\">\n");
    	print ("<head>\n");
       	print ("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n");
       	print ("<meta name=\"description\" content=\"$Desc\">\n");
    	print ("<link rel=\"shortcut icon\" href=\"/images/filestore.ico\">\n");
       	print ("<title>$Title</title>\n");
        if (is_string ($Script)) {
            print ("<script src=\"$Script\"></script>\n");
        }
    	print ("</head>\n");
    	print ("\n");
    }

    # Function that starts a page body
    function StartBody ($Title)
    {
       	print ("<body>\n");
        print ("<table style=\"width:100%\"><tr>\n");
       	print ("    <td style=\"text-align:left\"><img width=\"96\" height=\"96\" alt=\"FileStore Logo\" src=\"/images/filestore-96x96.png\"></td>\n");
       	print ("    <td style=\"text-align:center\"><h1>$Title</h1></td>\n");
       	print ("    <td style=\"text-align:right\"><img width=\"96\" height=\"96\" alt=\"FileStore Logo\" src=\"/images/filestore-96x96.png\"></td>\n");
       	print ("</tr></table>\n");
       	print ("<hr style=\"border-style:solid\"><p>\n");
    }

    # Function that ends a page body
    function EndBody ()
    {
       	print ("<hr style=\"border-style:solid\"><p>\n");
        print ("<address><p>\n");
        print ("<a href=\"https://github.com/mauline/filestore\">FileStore!</a><br>\n");
        print ("</p></address>\n");
        print ("</body>\n");
        print ("</html>\n");
    }

?>
