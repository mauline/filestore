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

function ValidExt(name)
{
    name = String(name).toLowerCase();
    return AllowedExts.find(function(E) { return name.endsWith(E); });
}

function SizeAsString(size)
{
    var Res;
    if (size < 1000) {
        Res = String(size) + ' B';
    } else if (size < 1000 * 10) {
        Res = String((size / 1000.0).toFixed(2)) + ' KB';
    } else if (size < 1000 * 100) {
        Res = String((size / 1000.0).toFixed(1)) + ' KB';
    } else if (size < 1000 * 1000) {
        Res = String((size / 1000.0).toFixed(0)) + ' KB';
    } else if (size < 1000 * 1000 * 10) {
        Res = String((size / (1000.0 * 1000.0)).toFixed(2)) + ' MB';
    } else if (size < 1000 * 1000 * 100) {
        Res = String((size / (1000.0 * 1000.0)).toFixed(1)) + ' MB';
    } else {
        Res = String((size / (1000.0 * 1000.0)).toFixed(0)) + ' MB';
    }
    if (size > MaxFileSize) {
        Res += ' (' + FileIsTooBig + ')';
    }
    return Res;
}

function ResetUI()
{
    // Reset the display
    var prog = document.getElementById("Progress");
    prog.value = 0;
    prog.max = 100;
    document.getElementById("Percent").innerHTML = "0%";
    document.getElementById("UploadResult").innerHTML = "";
    document.getElementById("UploadButton").disabled = false;
    document.getElementById("AbortButton").disabled = true;
}

function FileChangeHandler()
{
    // Get file name from input element "UploadFilename"
    var file = document.getElementById("UploadFilename").files[0];

    // If file is empty, nothing was selected
    if (!file) {
        document.getElementById("UploadButton").disabled = true;
        return;
    }

    // Get the file name and check for allowed extensions
    var fileName = document.getElementById("InfoFileName");
    if (ValidExt(file.name)) {
        fileName.innerHTML = file.name;
    } else {
        fileName.innerHTML = file.name + ' (' + InvalidExt + ')';
    }

    // Show file properties and reset progress bar and result
    document.getElementById("InfoFileSize").innerHTML = SizeAsString(file.size);

    // Reset the user interface
    ResetUI();
}

// This is the HttpRequest
var client = null;

function UploadFileHandler(action)
{
    // Get file name from input element "UploadFilename"
    var file = document.getElementById("UploadFilename").files[0];

    // If file is empty, nothing was selected
    if (!file) {
        return;
    }

    // Create a new FormData object
    var formData = new FormData();

    // Create a new XMLHttpRequest object. We store this in a global variable
    // since we need to access it in other event handlers
    client = new XMLHttpRequest();

    // Get the progress bar element and initialize it
    var prog = document.getElementById("Progress");
    prog.value = 0;
    prog.max = 100;

    // Get the element that contains the upload result and clear it
    var resultText = document.getElementById("UploadResult");
    resultText.innerHTML = "";

    // Get the upload and abort buttons
    var uploadButton = document.getElementById("UploadButton");
    var abortButton = document.getElementById("AbortButton");
    uploadButton.disabled = true;
    abortButton.disabled = false;

    // Place the upload file into the FormaData object
    formData.append("upfile", file);

    // Specify the function called on errors
    client.onerror = function(e) {
        alert(ErrorInUpload);
        ResetUI();
    };

    // Specify the function called when the upload is complete
    client.onload = function(e) {
        document.getElementById("Percent").innerHTML = "100%";
        prog.value = prog.max;
    };

    // Specify the function called on progress changes
    client.upload.onprogress = function(e) {
        var p = Math.round(100 / e.total * e.loaded);
        document.getElementById("Progress").value = p;
        document.getElementById("Percent").innerHTML =
                SizeAsString (e.loaded) + " / " + p + "%";
    };

    // Specify the function when upload is aborted
    client.onabort = function(e) {
        alert(UploadAborted);
        ResetUI();
    };

    // Specify the function called on status changes
    client.onreadystatechange = function() {
        if (this.readyState == this.DONE) {
            resultText.innerHTML = this.responseText;
            uploadButton.disabled = false;
            abortButton.disabled = true;
        }
    };

    // Open the connection and send the file data
    client.open("POST", action);
    client.send(formData);
}

function AbortUploadHandler() {
    if (client instanceof XMLHttpRequest) {
        client.abort();
    }
}
