# cli_chatserver

This chatserver was made to tinker around with PHP on the commandline and REST APIs.
The chatserver can serve multiple clients which post direct messages through the API.
-----------
## Server usage
* Start a php webserver on <host>:<port> with the following command:
```
> php -S <host>:<port> -t <path_to>/cli_chatserver/src <path_naar>/cli_chatserver/index.php
```
The server is now active and the clients can connect to the <host>:<port> combination.

## Client usage.
* Start a client with the following command:
```
> php <path_to>/cli_chatserver/src/Client/CLIChatClientApp.php <host> <port>
```

The client is now active; to get a summary of the commands supported by the client you can type help.

Have fun :).
