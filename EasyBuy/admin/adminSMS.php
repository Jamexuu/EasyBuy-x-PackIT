<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<body>
    <?php include '../frontend/components/adminNavBar.php' ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-2 p-3 d-none d-lg-block d-flex flex-column" style="background-color: #F5F5F5; height: 100vh;">
                <div class="mt-auto">
                    <div class="btn rounded-pill text-center py-2 d-flex align-items-center justify-content-center w-100"
                        style="background-color: #D9D9D9;" onclick="toggleComposeSMS();">
                        <span class="material-symbols-outlined">
                            edit
                        </span>
                        Compose Message
                    </div>
                </div>
            </div>
            <div class="col col-lg-10 p-lg-3">
                <div class="h1 p-3">Messages</div>
                <div class="row">
                    <div class="col">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 30%;">From</th>
                                    <th style="width: 45%;">Message</th>
                                    <th style="width: 25%;">Time</th>
                                </tr>
                            </thead>
                            <tbody id="messageContainer">
                                
                            </tbody>
                        </table>
                        <div class="row p-3" id="expandMessageRow">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-lg-none position-fixed bottom-0 start-0 end-0 p-3 z-1">
        <div class="btn rounded-pill text-center py-2 d-flex align-items-center justify-content-center w-50"
            style="background-color: #D9D9D9;" onclick="toggleComposeSMS();">
            <span class="material-symbols-outlined">
                edit
            </span>
            Compose Message
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        var messageRow = document.getElementById('messageContainer');
        var expandMessageRow = document.getElementById('expandMessageRow');

        function toggleComposeSMS() {
            alert('Compose SMS clicked');
        }

        async function fetchMessages(){

            const response = await fetch('../api/sms forwarder/messages.json', {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin'
            });

            const messages = await response.json();
            messages.reverse();

            window.messages = messages;

            messages.forEach(message => {
                message.text = message.text.length > 10 ? message.text.substring(0, 10) + '...' : message.text;

                messageRow.innerHTML += `
                    <tr class="text-center" id="messageRow" onclick="expandMessage('`+ message.id +`')" style="cursor: pointer;">
                        <td>
                            `+ message.from +`
                        </td>
                        <td>
                            `+ message.text +`   
                        </td>
                        <td>
                            `+ message.receivedStamp +`
                        </td>
                    </tr>
                    
                `;
            });
        }

        fetchMessages();

        async function expandMessage(messageId){
            const message = window.messages.find(msg => msg.id === messageId);
            if (!message) return;

            messageRow.innerHTML = `
                <div class="col d-flex flex-column gap-3">
                    <div class="h3">From: `+ message.from +`</div>
                    <div class="card border-0 shadow-sm mx-5 rounded-3">
                        <div class="card-body">
                            <p class="fw-normal">` + message.text + `</p>
                        </div>
                    </div>
                </div>
            `;
        }

    </script>
</body>

</html>