<?php   # -*- php-mode -*-
    # Include functions and definitions
    require (__DIR__ . "/filestore.inc");

    # Include page layout functions
    require (__DIR__ . "/layout.inc");

    # Generate the page header, start body
    MakeHeader ("FileStore Upload");
    StartBody ("FileStore Upload");
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

function GenerateHandler()                                               
{
    // Make sure we have an e-mail address given
    var AllowedMailDomains = <?= sprintf ("[ '%s' ];\n", implode ("','", $AllowedMailDomains)); ?>
    var Email = document.getElementById("EmailInput").value.toLowerCase();
    if (!AllowedMailDomains.find(function(E) { return Email.endsWith(E); })) {
        alert('<?=_('The e-mail address is invalid.');?>');
        return;
    };

    // Create a new FormData object
    var formData = new FormData(document.querySelector("form"));

    // Create a new XMLHttpRequest object. We store this in a global variable
    // since we need to access it in other event handlers
    var client = new XMLHttpRequest();

    // Get the element that contains the generation result and clear it
    var resultText = document.getElementById("GenerateResult");
    resultText.innerHTML = "";

    // Get the generate button
    var generateButton = document.getElementById("GenerateButton");
    generateButton.disabled = true;

    // Specify the function called on errors
    client.onerror = function(e) {
        alert('<?=_('Error generating upload page');?>');
        generateButton.disabled = false;
    };

    // Specify the function called on status changes
    client.onreadystatechange = function() {
        if (this.readyState == this.DONE) {
            if (this.status == 200) {
                resultText.innerHTML = this.responseText;
            } else {
                resultText.InnerHTML = this.statusText;
            }
            generateButton.disabled = false;
        }
    };

    // Open the connection and send the form data
    client.open("POST", "receive-generate.php");
    client.send(formData);
}
</script>

<p><?=_('Please configure the upload and click "Generate". The upload URL will be sent to the given e-mail address so make sure it is valid!');?></p>
<h2><?=_('Configuration');?></h2>
<form action="receive-generate.php" method="post" id="ConfigurationForm" enctype="application/x-www-form-urlencoded">
    <table>
    <tr><td><label for="PermanentCheckbox"><?=_('Permanent');?></label></td><td><input name="permanent" value="false" type="checkbox" id="PermanentCheckbox"></td></tr>
    <tr><td><label for="EmailInput"><?=_('Your e-mail address');?></label></td><td><input name="email" value="" type="email" maxlength="80" size="80" id="EmailInput" required></td></tr>
    <tr><td><label for="DescInput"><?=_('Description (optional)');?></label></td><td><input name="description" value="" type="text" maxlength="80" size="80" id="DescInput"></td></tr>
    </table><p>
    <input name="generate" value=<?=_('"Generate"');?> type="button" id="GenerateButton" onclick="GenerateHandler();" />
</form><p>
<div id="GenerateResult"></div>

<?php
    # End of page
    EndBody ();
?>

