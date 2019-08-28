<?php   # -*- php-mode -*-
    # Include functions and definitions
    require (__DIR__ . "/../../filestore.inc");

    # Include page layout functions
    require (__DIR__ . "/../../layout.inc");

    # Include the configuration definitions
    require (__DIR__ . "/config.inc");

    # Generate the page header, start body
    MakeHeader ("FileStore Upload");
    StartBody ("FileStore Upload");

    # Check if we already had an upload
    if ($Done) {
        printf ("<p><b>%s</b></p>\n", _('A file has already been uploaded.'));
    } else {
        # We need javascript code here
        LoadJS ();
?>

<p><?=_('Select the file and click "Upload".');?></p>
<form action="receive-upload.php" method="post" enctype="multipart/form-data">
    <input name="file" type="file" id="UploadFilename" onchange="FileChangeHandler();"/>
    <input name="upload" value=<?=_('"Upload"');?> type="button" id="UploadButton" disabled onclick="UploadFileHandler('receive-upload.php');" />
    <input name="abort" value=<?=_('"Abort"');?> type="button" id="AbortButton" disabled onclick="AbortUploadHandler();" />
</form>
<p><table>
    <tr><td><?=_('File name:');?></td><td><div id="InfoFileName"></div></td></tr>
    <tr><td><?=_('File size:');?></td><td><div id="InfoFileSize"></div></td></tr>
</table>
<p><?=_('Upload progress:');?> <progress id="Progress" value="0" max="100" style="margin-top:10px"></progress> <span id="Percent"></span></p>
<div id="UploadResult"></div>

<?php
    }

    # End of page
    EndBody ();
?>
