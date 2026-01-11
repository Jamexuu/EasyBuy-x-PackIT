<?php
require_once '../api/classes/Auth.php';
Auth::requireAdmin();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyBuy - Admin Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include '../frontend/components/adminNavbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-2 d-none d-md-block py-5" style="background-color: #F5F5F5; height: 200vh;">
                <div class="btn rounded-pill text-center py-2 d-inline-flex align-items-center" style="background-color: #D9D9D9;"
                    onclick="toggleComposeEmail()">
                    <span class="material-symbols-outlined">
                        edit
                    </span>
                    Compose Email
                </div>
                <ul class="list-unstyled mt-4 mx-2" id="sideMenu">
                    <div class="text-center">
                        <div class="spinner-border" role="status" style="color: #398250;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-secondary">Loading users...</p>
                    </div>
                </ul>
            </div>
            <div class="col-12 col-md-10" id="mainContent">
                <div class="row">
                    <div class="col-6 p-0">
                        <div class="btn w-100 h-100 p-4 rounded-0" id="inboxBtn" onclick="inboxClick();"
                            style="background-color: #D9D9D9;">
                            <span class="material-symbols-outlined">
                                inbox
                            </span>
                            Email Inbox
                        </div>
                    </div>
                    <div class="col-6 p-0">
                        <div class="btn w-100 h-100 p-4 rounded-0" id="unreadMailBtn" onclick="unreadMailClick();">
                            <span class="material-symbols-outlined">
                                mark_email_unread
                            </span>
                            Unread Emails
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody id="emails">
                                    <tr id="loading">
                                        <td colspan="3" class="text-center py-5">
                                            <div class="spinner-border" role="status" style="color: #398250;">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 text-secondary">Loading emails...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../frontend/components/messageModal.php'; ?>

    <!-- modal for composing a new email -->
    <div class="modal fade" id="composeEmailModal" tabindex="-1" aria-labelledby="composeEmailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #349C55; color: white;">
                    <h5 class="modal-title" id="composeEmailModalLabel">New Email</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="composeEmailForm">
                        <div class="mb-3">
                            <label for="emailTo" class="form-label">To</label>
                            <input type="email" class="form-control" id="emailTo" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="emailSubject">
                        </div>
                        <div class="mb-3">
                            <label for="emailBody" class="form-label">Message</label>
                            <textarea class="form-control" id="emailBody" rows="10"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="emailAttachment" class="form-label">Attachment</label>
                            <input type="file" class="form-control" id="emailAttachment">
                            <small class="text-muted">Maximum file size: 3MB</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn text-white d-inline-flex align-items-center gap-2"
                        style="background-color: #349C55;" onclick="sendComposedEmail()">
                        <span class="material-symbols-outlined">send</span>
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        const inboxBtn = document.getElementById('inboxBtn');
        const unreadMailBtn = document.getElementById('unreadMailBtn');
        const emails = document.getElementById('emails');
        const sideMenu = document.getElementById('sideMenu');
        const mainContent = document.getElementById('mainContent');
        const sendReplyButton = document.getElementById('sendReplyButton');
        let emailsData = [];

        function inboxClick() {
            inboxBtn.style.backgroundColor = '#D9D9D9';
            unreadMailBtn.style.backgroundColor = 'white';
            fetchEmails();
        }

        function unreadMailClick() {
            unreadMailBtn.style.backgroundColor = '#D9D9D9';
            inboxBtn.style.backgroundColor = 'white';
            fetchUnreadEmails();
        }

        async function expandEmail(index) {
            mainContent.innerHTML = `
                <div class="row">
                    <div class="col text-center py-5">
                        <div class="spinner-grow" role="status" style="color: #398250;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-secondary">Loading email...</p>
                    </div>
                </div>
            `;
            
            const email = emailsData[index];
            
            if (email.isUnread) {
                try {
                    await fetch('../api/markEmailAsRead.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ emailIndex: index })
                    });
                    emailsData[index].isUnread = false;
                } catch (error) {
                    console.error('Error marking email as read:', error);
                }
            }
            
            mainContent.innerHTML = `
                <div class="row">
                    <div class="col">
                        <div class="row" style="background-color: #F5F5F5;">
                            <div class="col-1 py-4 d-flex flex-row justify-content-between">
                                <span class="material-symbols-outlined" style="cursor: pointer;" onclick="location.reload();">
                                    arrow_back
                                </span>
                                <span class="material-symbols-outlined">
                                    archive
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="h4 p-4">`+ email.subject + `</div>
                                <div class="d-flex flex-row align-items-start gap-3 p-3">
                                    <img src="../assets/placeholder.png" alt="Email Icon"
                                        class="rounded-circle img-fluid" style="max-height: 40px;">
                                    <div>
                                        <h6 class="mb-0">`+ email.sender + `</h6>
                                        <p class="text-muted mb-0">`+ email.email + `</p>
                                    </div>
                                </div>
                                <div class="p-3 mx-2 rounded-4 fw-normal" style="background-color: #D9D9D9; overflow-wrap: break-word;">
                                    `+ (email.htmlBody || email.body) + `
                                </div>
                            </div>
                        </div>
                        <div class="row" id="replyButtonRow">
                            <div class="col text-end p-5">
                                <button class="btn rounded-pill text-white d-inline-flex align-items-center gap-2" style="background-color: #349C55;" onclick="toggleReply(${index})">
                                    <span class="material-symbols-outlined">
                                        reply
                                    </span>
                                    Reply
                                </button>
                            </div>
                        </div>

                        <!-- reply form -->
                        <div class="row d-none mt-4" id="replyForm">
                            <div class="col">
                                <div class="p-4 mx-2 rounded-4" style="background-color: #F5F5F5;">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="material-symbols-outlined" style="cursor: pointer;" onclick="toggleReply(${index})">arrow_back</span>
                                        <div>
                                            <h6 class="mb-0">`+ email.sender + `</h6>
                                            <p class="text-muted mb-0 small">`+ email.email + `</p>
                                        </div>
                                    </div>
                                    <textarea class="form-control mb-3" id="replyBody" rows="8" placeholder="Type your reply..."></textarea>
                                    <div class="text-end">
                                        <button class="btn text-white d-inline-flex align-items-center gap-2" id="sendReplyButton" style="background-color: #349C55;" onclick="sendReplyEmail(${index})">
                                            <span class="material-symbols-outlined">send</span>
                                            Send
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        async function fetchEmails() {
            try {
                sideMenu.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border" role="status" style="color: #398250;">
                                <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-secondary">Loading users...</p>
                    </div>
                `;

                emails.innerHTML = `
                    <tr id="loading">
                        <td colspan="3" class="text-center py-5">
                            <div class="spinner-border" role="status" style="color: #398250;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-secondary">Loading emails...</p>
                        </td>
                    </tr>
                `;

                const response = await fetch('../api/getMails.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                emailsData = data.emails;
                sideMenu.innerHTML = '';
                emails.innerHTML = '';

                data.emails.forEach((email, index) => {
                    const checkUnread = email.isUnread === true ? 'fw-bold' : 'fw-normal';
                    sideMenu.innerHTML += `
                        <li class="my-3">
                            <a href="#" onclick="expandEmail(${index}); return false;" class="text-decoration-none text-dark ` + checkUnread + `">` + email.sender + `</a>
                        </li>

                    `;
                });

                data.emails.forEach((email, index) => {
                    const checkUnread = email.isUnread === true ? 'fw-bold' : 'fw-normal';
                    emails.innerHTML += `
                        <tr onclick="expandEmail(${index});" style="cursor: pointer;" class="` + checkUnread + `">
                            <td>`+ email.sender + `</td>
                            <td>`+ email.subject + `</td>
                            <td>`+ email.email + `</td>
                        </tr>
                    `;
                });


            } catch (error) {
                console.error('Error fetching emails:', error);
                emails.innerHTML = `<tr>
                        <td colspan="3" class="text-center text-danger py-4">
                            Error loading emails. Please try again.
                        </td>
                    </tr>
                `;
            }
        }

        fetchEmails();

        async function fetchUnreadEmails() {
            try {
                sideMenu.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border" role="status" style="color: #398250;">
                                <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-secondary">Loading users...</p>
                    </div>
                `;

                emails.innerHTML = `
                    <tr id="loading">
                        <td colspan="3" class="text-center py-5">
                            <div class="spinner-border" role="status" style="color: #398250;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-secondary">Loading emails...</p>
                        </td>
                    </tr>
                `;

                const response = await fetch('../api/getUnreadMails.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                emailsData = data.emails;
                sideMenu.innerHTML = '';
                emails.innerHTML = '';

                data.emails.forEach((email, index) => {
                    sideMenu.innerHTML += `
                        <li class="my-3">
                            <a href="#" onclick="expandEmail(${index}); return false;" class="text-decoration-none text-dark">` + email.sender + `</a>
                        </li>

                    `;
                });

                data.emails.forEach((email, index) => {
                    emails.innerHTML += `
                        <tr onclick="expandEmail(${index});" style="cursor: pointer;">
                            <td>`+ email.sender + `</td>
                            <td>`+ email.subject + `</td>
                            <td>`+ email.email+ `</td>
                        </tr>
                    `;
                });


            } catch (error) {
                console.error('Error fetching unread emails:', error);
            }
        }

        function toggleComposeEmail() {
            const modal = new bootstrap.Modal(document.getElementById('composeEmailModal'));
            modal.show();
        }

        async function sendComposedEmail() {
            try {
                const emailTo = document.getElementById('emailTo').value;
                const emailSubject = document.getElementById('emailSubject').value;
                const emailBody = document.getElementById('emailBody').value;

                if (!emailTo) {
                    showMessage('error', 'Error', 'Please enter a recipient email address.');
                    return;
                }

                if (!emailSubject || !emailBody) {
                    if (!confirm('Send email with blank subject or body?')) {
                        return;
                    }
                }

                const formData = new FormData();
                formData.append('email', emailTo);
                formData.append('subject', emailSubject);
                formData.append('message', emailBody);

                // add attachment if selected
                const attachmentInput = document.getElementById('emailAttachment');
                if (attachmentInput.files.length > 0) {
                    formData.append('attachment', attachmentInput.files[0]);
                } else {
                    formData.append('attachment', null);
                }

                const response = await fetch('../api/sendAdminEmail.php', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    showMessage('success', 'Success', 'Email sent successfully!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('composeEmailModal'));
                    modal.hide();
                    document.getElementById('composeEmailForm').reset();
                } else {
                    showMessage('error', 'Error', 'Failed to send email');
                }
            } catch (error) {
                console.error('Error composing email:', error);
                showMessage('error', 'Error', 'Error sending email. Please try again.');
            }
        }

        function toggleReply(index){
            const replyButtonRow = document.getElementById('replyButtonRow');
            const replyForm = document.getElementById('replyForm');
            
            if (replyForm.classList.contains('d-none')) {
                replyForm.classList.remove('d-none');
                replyButtonRow.classList.add('d-none');
            } else {
                replyForm.classList.add('d-none');
                replyButtonRow.classList.remove('d-none');
            }
        }

        async function sendReplyEmail(index){
            try {
                const email = emailsData[index];
                const replyBody = document.getElementById('replyBody').value;

                if (!replyBody.trim()) {
                    showMessage('error', 'Error', 'Please enter a reply message.');
                    return;
                }

                const formData = new FormData();
                formData.append('email', email.email);
                formData.append('subject', email.subject.startsWith('Re:') ? email.subject : 'Re: ' + email.subject);
                formData.append('message', replyBody);

                const response = await fetch('../api/sendAdminEmail.php', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    showMessage('success', 'Success', 'Reply sent successfully!');
                    document.getElementById('replyBody').value = '';
                    toggleReply(index);
                } else {
                    showMessage('error', 'Error', 'Failed to send reply');
                }
            } catch (error) {
                console.error('Error sending reply:', error);
                showMessage('error', 'Error', 'Error sending reply. Please try again.');
            }
        }

    </script>
</body>

</html>