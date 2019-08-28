<?php   # -*- php-mode -*-
    # Include functions and definitions
    require (__DIR__ . "/filestore.inc");

    # Include page layout functions
    require (__DIR__ . "/layout.inc");

    # Generate the page header, start body
    MakeHeader ("FileStore Upload");
    StartBody ("FileStore Upload");

    # We need javascript code here
    LoadJS ();
?>

<p><?=_('Select the file and click "Upload".');?></p>
<form action="send-upload.php" method="post" enctype="multipart/form-data">
    <input name="file" type="file" id="UploadFilename" onchange="FileChangeHandler();"/>
    <input name="upload" value=<?=_('"Upload"');?> type="button" id="UploadButton" disabled onclick="UploadFileHandler('send-upload.php');" />
    <input name="abort" value=<?=_('"Abort"');?> type="button" id="AbortButton" disabled onclick="AbortUploadHandler();" />
</form>
<p><table>
    <tr><td><?=_('File name:');?></td><td><div id="InfoFileName"></div></td></tr>
    <tr><td><?=_('File size:');?></td><td><div id="InfoFileSize"></div></td></tr>
</table>
<p><?=_('Upload progress:');?> <progress id="Progress" value="0" max="100" style="margin-top:10px"></progress> <span id="Percent"></span></p>
<div id="UploadResult"></div>

<?php
    # End of page
    EndBody ();
?>


