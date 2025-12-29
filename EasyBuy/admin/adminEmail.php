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
            <div class="col-2 d-none d-md-block py-5" style="background-color: #F5F5F5; height: 100vh;">
                <div class="btn rounded-pill text-center py-2" style="background-color: #D9D9D9;">
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
            <div class="col-12 col-md-10">
                <div class="row">
                    <div class="col-6 p-0">
                        <div class="btn w-100 h-100 p-4 rounded-0" id="inboxBtn" onclick="inboxClick();" style="background-color: #D9D9D9;">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        const inboxBtn = document.getElementById('inboxBtn');
        const unreadMailBtn = document.getElementById('unreadMailBtn');
        const emails = document.getElementById('emails');
        const sideMenu = document.getElementById('sideMenu');

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

        function redirectToEmail(emailId) {
            window.location.href = `adminEmailView.php?id=${emailId}`;
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

                sideMenu.innerHTML = '';
                emails.innerHTML = '';

                data.emails.forEach(email => {
                    const checkUnread = email.isUnread === true ? 'fw-bold' : 'fw-normal';
                    sideMenu.innerHTML += `
                        <li class="my-3">
                            <a href="#" onclick="redirectToEmail('`+ email.email + `'); return false;" class="text-decoration-none text-dark `+ checkUnread +`">`+ email.sender + `</a>
                        </li>

                    `;
                });

                data.emails.forEach(email => {
                    const checkUnread = email.isUnread === true ? 'fw-bold' : 'fw-normal';
                    emails.innerHTML += `
                        <tr onclick="redirectToEmail('`+ email.email + `')" style="cursor: pointer;" class="`+ checkUnread + `">
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

                sideMenu.innerHTML = '';
                emails.innerHTML = '';

                data.emails.forEach(email => {
                    sideMenu.innerHTML += `
                        <li class="my-3">
                            <a href="#" onclick="redirectToEmail('`+ email.email + `'); return false;" class="text-decoration-none text-dark">`+ email.sender + `</a>
                        </li>

                    `;
                });

                data.emails.forEach(email => {
                    emails.innerHTML += `
                        <tr onclick="redirectToEmail('`+ email.email + `')" style="cursor: pointer;">
                            <td>`+ email.sender + `</td>
                            <td>`+ email.subject + `</td>
                            <td>`+ email.email + `</td>
                        </tr>
                    `;
                });


            } catch (error) {
                console.error('Error fetching unread emails:', error);
            }
        }
    </script>
</body>

</html>