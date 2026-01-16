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
                <div class="h1 p-3" id="label">Messages</div>
                <div class="row">
                    <div class="col" id="composeSmsContainer">
                        <table class="table table-striped table-hover">
                            <thead id="table">
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
                        <div class="row">
                            
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
    <?php include '../frontend/components/messageModal.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        var messageRow = document.getElementById('messageContainer');
        var expandMessageRow = document.getElementById('expandMessageRow');
        var composeSmsContainer = document.getElementById('composeSmsContainer');
        var table = document.getElementById('table');
        var label = document.getElementById('label');

        function toggleComposeSMS(){
            composeSmsContainer.innerHTML = `
                <div class="col col-lg-6 p-lg-5 py-3">
                    <div class="h1 mb-5">Compose Message</div>
                    <label for="toNumber" class="form-label">To</label>
                    <input type="text" class="form-control mb-3" name="toNumber" placeholder="Enter Number" id="toNumber">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control rounded-3 mb-3" id="message" name="message" rows="5" placeholder="Type your message here..."></textarea>
                    <button onclick="sendSms(); return false;" class="btn btn-success px-4 py-2 mb-5">Send</button>
                </div>
            `;
        }

        async function sendSms() {
            var toNumber = document.getElementById('toNumber');
            var message = document.getElementById('message');

            const response = await fetch('../api/sendAdminSMS.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({
                    phone_number: toNumber.value,
                    message: message.value
                })
            });

            const result = await response.json();

            if (result.status === 200) {
                showMessage('Success', '', 'Message sent successfully!', 'OK');
                toNumber.value = '';
                message.value = '';
            } else {
                showMessage('Error', '', 'Failed to send message: ' + (result.error || 'Unknown error'), 'OK');
            }
        }

        async function fetchMessages(){

            const response = await fetch('../api/sms forwarder/messages.json', {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin'
            });

            const messages = await response.json();
            window.messages = messages

            const arrayMessages = [];
            arrayMessages.push(...messages);

            arrayMessages.forEach(message => {
                shortMessage = message.text.length > 10 ? message.text.substring(0, 10) + '...' : message.text;

                messageRow.innerHTML += `
                    <tr class="text-center" id="messageRow" onclick="expandMessage('`+ message.id +`')" style="cursor: pointer;">
                        <td>
                            `+ message.from +`
                        </td>
                        <td>
                            `+ shortMessage +`   
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

            table.style.display = 'none';
            label.style.display = 'none';
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