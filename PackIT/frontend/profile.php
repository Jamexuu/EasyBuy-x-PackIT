<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --brand-yellow: #fce354;
        }

        body {
            background-color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .profile-card {
            background-color: var(--brand-yellow);
            border-radius: 40px;
            padding: 50px 20px;
            text-align: center;
        }

        .profile-img-container {
            position: relative;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid white;
            background: #eee;
            margin: 0 auto;
        }

        #profileDisplay {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-icon-button {
            position: absolute;
            bottom: 5px;
            right: 15px;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid var(--brand-yellow);
            transition: transform 0.2s;
            z-index: 10;
        }

        #userName[contenteditable="true"] {
            background-color: transparent !important;
            outline: none;
            border-bottom: 2px dashed rgba(0, 0, 0, 0.2);
            padding: 0 5px;
        }

        #userName:empty:not(:focus):before {
            content: "Enter Name";
            color: rgba(0, 0, 0, 0.3);
            font-weight: normal;
        }

        /* --- Updated Status Styles --- */
        .status-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.2s ease;
        }

        .status-link:hover {
            transform: translateY(-5px);
            /* Lift effect on hover */
            cursor: pointer;
        }

        .status-bubble {
            background-color: var(--brand-yellow);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .menu-outline {
            border: 3px solid var(--brand-yellow);
            border-radius: 35px;
            padding: 30px;
            margin-top: 30px;
        }

        .menu-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            color: #212529;
            text-decoration: none;
            font-size: 1.25rem;
        }

        .dropdown-item {
            font-size: 1rem;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
            padding-left: 30px !important;
        }

        .menu-link[aria-expanded="true"] span:last-child {
            transform: rotate(180deg);
            display: inline-block;
        }

        .menu-link span:last-child {
            transition: transform 0.3s;
        }
    </style>
</head>

<body>
    <?php include("components/navbar.php"); ?>

    <div class="container my-5">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="profile-card shadow-sm">
                    <div class="position-relative mx-auto" style="width: 180px;">
                        <div class="profile-img-container shadow-sm">
                            <img id="profileDisplay" src="https://via.placeholder.com/200" alt="Profile">
                        </div>
                        <div class="camera-icon-button shadow" onclick="document.getElementById('fileInput').click();">
                            <span>📷</span>
                        </div>
                    </div>

                    <input type="file" id="fileInput" style="display: none;" accept="image/*"
                        onchange="previewImage(event)">

                    <div class="d-flex align-items-center justify-content-center mt-3 mb-1">
                        <h2 id="userName" class="fw-bold mb-0" contenteditable="false" spellcheck="false"></h2>
                        <button class="btn btn-link btn-sm text-dark p-0 ms-2 text-decoration-none"
                            onclick="toggleEdit()">
                            <span id="editIcon">✎</span>
                        </button>
                    </div>

                    <p id="userEmail" class="text-muted small mb-4">MIEL.GANDA@GMAIL.COM</p>

                    <div class="mt-4">
                        <h4 class="mb-1 fw-normal">+63 01823918</h4>
                        <h4 class="fw-normal">SAN JUAN BATS</h4>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row text-center mb-4">
                    <div class="col-4">
                        <a href="#" class="status-link">
                            <div class="status-bubble">0</div>
                            <div class="status-label small fw-bold">To Receive</div>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="status-link">
                            <div class="status-bubble">0</div>
                            <div class="status-label small fw-bold">To Deliver</div>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="status-link">
                            <div class="status-bubble">0</div>
                            <div class="status-label small fw-bold">Cancelled</div>
                        </a>
                    </div>
                </div>

                <div class="menu-outline">
                    <a href="#collapseAccount" class="menu-link" data-bs-toggle="collapse" role="button"
                        aria-expanded="false">
                        <span>Account & Security</span> <span>V</span>
                    </a>
                    <div class="collapse" id="collapseAccount">
                        <ul class="list-unstyled ps-4 pb-2 border-bottom mb-2">
                            <li><a class="dropdown-item py-2" href="#">Password Settings</a></li>
                            <li><a class="dropdown-item py-2" href="#">Two-Factor Auth</a></li>
                        </ul>
                    </div>

                    <a href="#collapseAccessibility" class="menu-link" data-bs-toggle="collapse" role="button"
                        aria-expanded="false">
                        <span>Accessibility</span> <span>V</span>
                    </a>
                    <div class="collapse" id="collapseAccessibility">
                        <ul class="list-unstyled ps-4 pb-2 border-bottom mb-2">
                            <li><a class="dropdown-item py-2" href="#">Regular link</a></li>
                            <li><a class="dropdown-item py-2 active bg-warning text-dark rounded" href="#"
                                    aria-current="true">Active link</a></li>
                            <li><a class="dropdown-item py-2" href="#">Another link</a></li>
                        </ul>
                    </div>

                    <a href="#collapseFeedback" class="menu-link" data-bs-toggle="collapse" role="button"
                        aria-expanded="false">
                        <span>Feedback</span> <span>V</span>
                    </a>
                    <div class="collapse" id="collapseFeedback">
                        <ul class="list-unstyled ps-4 pb-2 border-bottom mb-2">
                            <li><a class="dropdown-item py-2" href="#">Report a Bug</a></li>
                            <li><a class="dropdown-item py-2" href="#">Suggest a Feature</a></li>
                        </ul>
                    </div>

                    <a href="#collapseAbout" class="menu-link" data-bs-toggle="collapse" role="button"
                        aria-expanded="false">
                        <span>About</span> <span>V</span>
                    </a>
                    <div class="collapse" id="collapseAbout">
                        <ul class="list-unstyled ps-4 pb-2 border-bottom mb-2">
                            <li><a class="dropdown-item py-2" href="#">App Version 1.0</a></li>
                            <li><a class="dropdown-item py-2" href="#">Privacy Policy</a></li>
                        </ul>
                    </div>

                    <a href="#" class="menu-link mt-4 text-secondary">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <?php include("components/footer.php"); ?>
    <?php include("../frontend/components/chat.php"); ?>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function () {
                    document.getElementById('profileDisplay').src = reader.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function toggleEdit() {
            const nameField = document.getElementById('userName');
            const editIcon = document.getElementById('editIcon');
            const isEditing = nameField.contentEditable === "true";

            if (!isEditing) {
                nameField.contentEditable = "true";
                nameField.focus();
                editIcon.innerText = "✔";
                editIcon.style.color = "green";
            } else {
                nameField.contentEditable = "false";
                editIcon.innerText = "✎";
                editIcon.style.color = "black";
            }
        }

        document.getElementById('userName').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                toggleEdit();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>