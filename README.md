# FileStore

## What is it?

FileStore is a web based facility to exchange files using a web
server.


## What does that mean?

It allows you to upload files and generates a download link for you.
You can send this download link to others. It does also allow you to
configure upload links for others. If they upload a file, you will
receive the download link by e-mail.


## Why do I need it?

I don't know why you might need it, but I can tell you why I did.

I'm working as a freelancer for (usually) small companies. A common
problem is file exchange between employees and customers. Some files
are just too large for e-mail, others won't go through mail filters on
the local or remote sides. Think about executables, .doc files, large
mpegs or whatever.

While stuff like this is blocked when it comes via e-mail, most people
can freely download it using a web browser.

So this is what FileStore does: It allows you to upload your files on
your web server, so your customers can download it. Or let your
customers upload files for you or anybody else in your company.


## So yes, I need it!

Ok. But please note that FileStore is not for everyone. It is simple,
easy to use and does not need much attention once installed correctly.
But is does also have a few requirements.


## Ok, so what do I need?

First of all, installation needs a proficient admin. Next an SSL
enabled web server on a linux box is needed. The web server must be
controlled by you. It needs internet connectivity and PHP must be
installed. And an MTA must be in place so PHP web pages can send
e-mail using mail().

With all this in place, you should be able to get FileStore running in
less than 10 minutes. Just read the install-instructions.txt document
in the docs subfolder.

