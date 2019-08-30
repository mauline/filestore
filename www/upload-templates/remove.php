<?php   # -*- php-mode -*-
    # Include functions and definitions
    require (__DIR__ . "/../../filestore.inc");

    # Include page layout functions
    require (__DIR__ . "/../../layout.inc");

    # Include the configuration definitions
    require (__DIR__ . "/config.inc");

    # Generate the page header, start body
    MakeHeader ("FileStore Remove");
    StartBody ("FileStore Remove");
?>

<script>
if (!String.prototype.endsWith) {
    // Internet exploder doesn't support endsWith
    String.prototype.endsWith = function(searchString, position)
    {
        var subjectString = this.toString();
        if (typeof position !== 'number' || !isFinite(position) ||
            Math.floor(position) !== position || position > subjectString.length) {
            position = subjectString.length;
        }
        position -= searchString.length;
        var lastIndex = subjectString.indexOf(searchString, position);
        return lastIndex !== -1 && lastIndex === position;
    };
}

function RemoveHandler()
{
    // Create a new FormData object
    var formData = new FormData(document.querySelector("form"));

    // Create a new XMLHttpRequest object. We store this in a global variable
    // since we need to access it in other event handlers
    var client = new XMLHttpRequest();

    // Get the element that contains the remove result and clear it
    var resultText = document.getElementById("RemoveResult");
    resultText.innerHTML = "";

    // Get the remove button
    var removeButton = document.getElementById("RemoveButton");
    removeButton.disabled = true;

    // Specify the function called on errors
    client.onerror = function(e) {
        alert('<?=_('Error removing upload page');?>');
        removeButton.disabled = false;
    };

    // Specify the function called on status changes
    client.onreadystatechange = function() {
        if (this.readyState == this.DONE) {
            resultText.innerHTML = this.responseText;
            removeButton.disabled = false;
        }
    };

    // Open the connection and send the form data
    client.open("POST", "remove-action.php");
    client.send(formData);
}
</script>

<p><?=_('Please enter the remove code and click "Remove".');?></p>
<form action="remove-action.php" method="post" id="RemoveForm" enctype="application/x-www-form-urlencoded">
    <table>
    <tr><td><label for="CodeInput"><?=_('Remove code');?></label></td><td><input name="code" value="" type="text" maxlength="80" size="80" id="CodeInput" required></td></tr>
    </table><p>
    <input name="remove" value=<?=_('"Remove"');?> type="button" id="RemoveButton" onclick="RemoveHandler();" />
</form><p>
<div id="RemoveResult"></div>

<?php
    # End of page
    EndBody ();
?>

