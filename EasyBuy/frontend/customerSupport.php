<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyBuy - Customer Support</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    
    <?php include 'components/navbar.php'; ?>
    
    <button class="back-btn mx-3 my-3" onclick="window.history.back()"
        style="background: none; border: none; color: #6EC064; font-size: 2rem; cursor: pointer;">
        <span class="material-symbols-rounded">arrow_back</span>
    </button>
    <div class="container">
        <div class="row m-3">
            <div class="col-md-8 col-lg-6 mx-md-auto px-1">
                <div class="h1" style="color: #28a745">
                    Customer Support
                </div>
                <p class="text-secondary fw-normal">Send us your concerns or questions, and our customer support team will get back to you as soon as possible.</p>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-8 col-lg-6 mx-md-auto">
                <form action="">
                    
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control rounded-3 mb-3" id="concernSubject" name="subject" placeholder="Enter the subject">
                    <label for="message">Message</label>
                    <textarea class="form-control rounded-3 mb-3" id="concernMessage" name="message" rows="5" placeholder="Type your message here..."></textarea>
                    <button onclick="submitConcern(); return false;" class="btn btn-success px-4 py-2 mb-5">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
    <?php include 'components/messageModal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>

        const subject = document.getElementById('concernSubject');
        const message = document.getElementById('concernMessage');

        async function submitConcern(){
            try {
                const emailRes = await fetch('../api/getUserDetails.php', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin'
                });
                const emailData = await emailRes.json();
                const userEmail = emailData.email || '';

                const sendEmail = await fetch('../api/sendEmailCustomerSupport.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        email: userEmail,
                        subject: subject.value,
                        message: message.value
                    })
                });

                const result = await sendEmail.json();

                if (result.success){
                    showMessage('success', 'Message Sent', 'Your message has been sent successfully. Our customer support team will get back to you shortly.', 'OK');
                    subject.value = '';
                    message.value = '';
                } else {
                    showMessage('error', 'Error', result.error || 'Failed to send your message. Please try again.', 'OK');
                }

            } catch (error) {
                console.error('Error submitting concern:', error);
            }
        }

    </script>
</body>

</html>