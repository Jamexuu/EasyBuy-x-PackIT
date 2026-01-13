<?php
session_start();

// 1. Login Check
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
  header('Location: login.php');
  exit;
}

// 2. CSRF Token
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

// 3. Fetch Fresh User Data
require_once __DIR__ . '/../api/classes/User.php';
$userObj = new User();
$userDetails = $userObj->getUserDetails($_SESSION['user']['id']);

// If user not found (deleted?), redirect
if (!$userDetails) {
  header('Location: login.php');
  exit;
}

// 4. Map Data for Display
$displayName  = trim(($userDetails['first_name'] ?? '') . ' ' . ($userDetails['last_name'] ?? ''));
$email        = $userDetails['email'] ?? '';
$contact      = $userDetails['contact_number'] ?? '';
$profileImage = $userDetails['profile_image'] ?? null;

function formatAddress($u)
{
  if (!$u) return '--';
  return implode(', ', array_filter([
    $u['house_number'] ?? null,
    $u['street'] ?? null,
    $u['subdivision'] ?? null,
    $u['barangay'] ?? null,
    $u['city'] ?? null,
    $u['province'] ?? null,
    $u['postal_code'] ?? null,
  ]));
}
$displayAddress = formatAddress($userDetails);

// Default SVG Avatar
$defaultAvatar = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode(
  '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
        <circle cx="100" cy="100" r="100" fill="#e5e8ec"/>
        <circle cx="100" cy="78" r="40" fill="#6c757d"/>
        <path d="M35 170c0-36 29-55 65-55s65 19 65 55" fill="#6c757d"/>
    </svg>'
);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profile Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-white">

  <?php include("components/navbar.php"); ?>

  <main class="container my-5 flex-grow-1">
    <div class="row g-4 align-items-center">

      <div class="col-12 col-lg-4">
        <div class="shadow-sm p-5 text-center position-relative h-100 d-flex flex-column align-items-center justify-content-center"
          style="background:#fce354; border-radius:40px;">

          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success w-100 py-2 small mb-2"><?= htmlspecialchars($_SESSION['success']);
                                                                    unset($_SESSION['success']); ?></div>
          <?php endif; ?>
          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger w-100 py-2 small mb-2"><?= htmlspecialchars($_SESSION['error']);
                                                                  unset($_SESSION['error']); ?></div>
          <?php endif; ?>

          <div class="position-relative mb-3">
            <div class="rounded-circle overflow-hidden bg-light mx-auto"
              style="width:180px;height:180px;border:5px solid #fff;">
              <img id="profileDisplay"
                src="<?= htmlspecialchars($profileImage ?: $defaultAvatar) ?>"
                alt="Profile"
                class="w-100 h-100 object-fit-cover"
                onerror="this.src='<?= $defaultAvatar ?>'">
            </div>

            <div class="position-absolute bottom-0 end-0 bg-white border border-2 border-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm"
              style="width:40px;height:40px; cursor:pointer;"
              onclick="document.getElementById('fileInput').click()">
              <i class="bi bi-camera-fill text-dark"></i>
            </div>

            <div id="avatarSpinner" class="position-absolute top-50 start-50 translate-middle d-none">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>

          <form id="avatarForm" class="d-none" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="file" id="fileInput" name="avatar" accept="image/*">
          </form>

          <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($displayName) ?></h2>
          <p class="text-secondary mb-2"><?= htmlspecialchars($email) ?></p>
          <h5 class="fw-medium text-dark mb-1"><?= htmlspecialchars($contact ?: '--') ?></h5>
          <small class="text-secondary d-block mb-3"><?= htmlspecialchars($displayAddress) ?></small>
        </div>
      </div>

      <div class="col-12 col-lg-8">
        <div class="p-4 p-md-5 bg-white h-100"
          style="border:3px solid #fce354; border-radius:35px;">

          <!-- Account & Security -->
          <div class="mb-3 border-bottom pb-2">
            <a class="d-flex justify-content-between align-items-center w-100 py-2 text-decoration-none text-dark"
              data-bs-toggle="collapse" href="#accountCollapse" role="button" aria-expanded="false">
              <span class="fw-medium">Account & Security</span>
              <i class="bi bi-chevron-down small"></i>
            </a>
            <div class="collapse mt-2" id="accountCollapse">
              <ul class="list-unstyled ms-3 mb-0">
                <li>
                  <a href="#" class="text-decoration-none text-secondary d-block py-1" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    Edit Profile Details
                  </a>
                </li>
                <li>
                  <a href="changePassword.php" class="text-decoration-none text-secondary d-block py-1">
                    Change Password
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <!-- Accessibility -->
          <div class="mb-3 border-bottom pb-2">
            <a class="d-flex justify-content-between align-items-center w-100 py-2 text-decoration-none text-dark"
              data-bs-toggle="collapse" href="#accessCollapse" role="button" aria-expanded="false">
              <span class="fw-medium">Accessibility</span>
              <i class="bi bi-chevron-down small"></i>
            </a>
            <div class="collapse mt-2" id="accessCollapse">
              <p class="text-muted ms-3 small mb-0 py-1">Accessibility settings coming soon.</p>
            </div>
          </div>

          <!-- ✅ Anchor for notifications "View feedback" -->
          <div id="feedback"></div>

          <!-- Feedback -->
          <div class="mb-3 border-bottom pb-2">
            <a id="feedbackToggle"
              class="d-flex justify-content-between align-items-center w-100 py-2 text-decoration-none text-dark"
              data-bs-toggle="collapse" href="#feedbackCollapse" role="button" aria-expanded="false">
              <span class="fw-medium">Feedback</span>
              <i class="bi bi-chevron-down small"></i>
            </a>

            <div class="collapse mt-2" id="feedbackCollapse">
              <ul class="list-unstyled ms-3 mb-0">
                <li>
                  <a href="#"
                    id="openFeedbackBtn"
                    class="text-decoration-none text-secondary d-block py-1">
                    Create Feedback
                  </a>
                </li>
                <li>
                  <a href="myFeedback.php"
                    class="text-decoration-none text-secondary d-block py-1">
                    My Feedback
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <!-- About -->
          <div class="mb-4">
            <a class="d-flex justify-content-between align-items-center w-100 py-2 text-decoration-none text-dark"
              data-bs-toggle="collapse" href="#aboutCollapse" role="button" aria-expanded="false">
              <span class="fw-medium">About</span>
              <i class="bi bi-chevron-down small"></i>
            </a>
            <div class="collapse mt-2" id="aboutCollapse">
              <ul class="list-unstyled ms-3 mb-0">
                <li><a href="#" class="text-decoration-none text-secondary d-block py-1">App Version 1.0</a></li>
                <li><a href="#" class="text-decoration-none text-secondary d-block py-1">Privacy Policy</a></li>
              </ul>
            </div>
          </div>

          <a href="logout.php" class="btn w-100 fw-bold py-2 rounded-pill shadow-sm text-dark" style="background:#fce354;">Logout</a>
        </div>
      </div>

    </div>
  </main>

  <!-- Edit Profile Modal (unchanged / kept) -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-4 border-0">
        <form action="editProfileProcess.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

          <div class="modal-header border-bottom-0 pb-0">
            <h5 class="modal-title fw-bold" id="editProfileLabel">Edit Profile Information</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <h6 class="text-uppercase text-muted small fw-bold mb-3">Personal Details</h6>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label small">First Name</label>
                <input type="text" class="form-control" name="firstName" value="<?= htmlspecialchars($userDetails['first_name'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Last Name</label>
                <input type="text" class="form-control" name="lastName" value="<?= htmlspecialchars($userDetails['last_name'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Mobile Number</label>
                <div class="input-group">
                  <span class="input-group-text bg-white text-muted">+63</span>
                  <input type="text" class="form-control" name="contact" value="<?= htmlspecialchars($userDetails['contact_number'] ?? '') ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Email (Cannot be changed)</label>
                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($userDetails['email'] ?? '') ?>" disabled>
              </div>
            </div>

            <h6 class="text-uppercase text-muted small fw-bold mb-3">Address Information</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small">House/Unit No.</label>
                <input type="text" class="form-control" name="houseNumber" value="<?= htmlspecialchars($userDetails['house_number'] ?? '') ?>">
              </div>
              <div class="col-md-8">
                <label class="form-label small">Street</label>
                <input type="text" class="form-control" name="street" value="<?= htmlspecialchars($userDetails['street'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Subdivision</label>
                <input type="text" class="form-control" name="subdivision" value="<?= htmlspecialchars($userDetails['subdivision'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Barangay</label>
                <input type="text" class="form-control" name="barangay" value="<?= htmlspecialchars($userDetails['barangay'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label small">City/Municipality</label>
                <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($userDetails['city'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Province</label>
                <input type="text" class="form-control" name="province" value="<?= htmlspecialchars($userDetails['province'] ?? '') ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label small">Postal Code</label>
                <input type="text" class="form-control" name="postal" value="<?= htmlspecialchars($userDetails['postal_code'] ?? '') ?>">
              </div>
              <div class="col-md-8">
                <label class="form-label small">Landmark</label>
                <input type="text" class="form-control" name="landmark" value="<?= htmlspecialchars($userDetails['landmark'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Feedback Modal (styled) -->
  <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

        <div class="modal-header border-0 px-4 py-3" style="background:#fce354;">
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
              style="width:44px;height:44px;background:rgba(0,0,0,.08);">
              <i class="bi bi-chat-square-text fs-5 text-dark"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-0 text-dark" id="feedbackModalLabel">Create Feedback</h5>
              <div class="small text-dark" style="opacity:.75;">Help us improve PackIT</div>
            </div>
          </div>

          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form id="feedbackForm" method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

          <div class="modal-body p-4">
            <div id="feedbackAlert" class="alert d-none mb-3" role="alert"></div>

            <div class="p-3 rounded-4 mb-4 border bg-light">
              <div class="d-flex gap-2 align-items-start">
                <i class="bi bi-info-circle mt-1 text-secondary"></i>
                <div class="small text-secondary">
                  Please avoid sharing passwords or sensitive data. Include clear details so we can help faster.
                </div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-12 col-md-5">
                <label class="form-label fw-semibold small text-uppercase text-secondary mb-1">Category</label>
                <select name="category" id="feedbackCategory" class="form-select">
                  <option value="bug">Bug / Error</option>
                  <option value="question">Question</option>
                  <option value="suggestion">Suggestion</option>
                  <option value="other">Other</option>
                </select>
                <div class="form-text">Choose what best matches your concern.</div>
              </div>

              <div class="col-12 col-md-7">
                <label class="form-label fw-semibold small text-uppercase text-secondary mb-1">Subject</label>
                <input type="text" name="subject" id="feedbackSubject" class="form-control" maxlength="255"
                  placeholder="Short summary (optional)">
                <div class="form-text">Example: “App crashes on booking screen”.</div>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold small text-uppercase text-secondary mb-1">Your Feedback</label>
                <textarea name="message" id="feedbackMessage" class="form-control" rows="6"
                  placeholder="Describe what happened, what you expected, and steps to reproduce (if a bug)..."
                  required></textarea>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <div class="form-text">Be specific. 5+ characters required.</div>
                  <span class="badge rounded-pill text-bg-light border" id="feedbackCharCount">0 / 2000</span>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer border-0 px-4 pb-4 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>

            <button type="submit" id="feedbackSubmitBtn"
              class="btn rounded-pill px-4 fw-bold text-dark" style="background:#fce354;">
              <span id="feedbackSubmitText">Submit Feedback</span>
              <span id="feedbackSpinner" class="spinner-border spinner-border-sm ms-2 d-none"
                role="status" aria-hidden="true"></span>
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <?php include("components/footer.php"); ?>
  <?php include("../frontend/components/chat.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ✅ Rotate Chevron Icons on Collapse (JavaScript Implementation)
    document.addEventListener('DOMContentLoaded', function() {
      // Select all collapsible content divs
      const collapses = document.querySelectorAll('.collapse');

      collapses.forEach(function(collapseEl) {
        // Find the specific trigger link that controls this collapse
        // (This matches the link with href="#idOfCollapse")
        const trigger = document.querySelector('a[href="#' + collapseEl.id + '"]');
        
        if (!trigger) return; // specific trigger not found

        const icon = trigger.querySelector('.bi-chevron-down');
        
        if (icon) {
          // Set an initial smooth transition via JS
          icon.style.transition = 'transform 0.3s ease';

          // When the section starts opening
          collapseEl.addEventListener('show.bs.collapse', function() {
            icon.style.transform = 'rotate(180deg)';
          });

          // When the section starts closing
          collapseEl.addEventListener('hide.bs.collapse', function() {
            icon.style.transform = 'rotate(0deg)';
          });
        }
      });
    });
    // Avatar upload (kept)
    (function() {
      const fileInput = document.getElementById('fileInput');
      const profileDisplay = document.getElementById('profileDisplay');
      const avatarSpinner = document.getElementById('avatarSpinner');

      if (!fileInput) return;

      fileInput.addEventListener('change', async function() {
        if (!this.files || !this.files[0]) return;

        profileDisplay.style.opacity = '0.5';
        avatarSpinner.classList.remove('d-none');

        const formData = new FormData();
        formData.append('avatar', this.files[0]);
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

        try {
          const response = await fetch('../api/user/update_avatar.php', {
            method: 'POST',
            body: formData
          });

          const result = await response.json();

          if (response.ok && result.ok) {
            profileDisplay.src = result.path + '?t=' + new Date().getTime();
          } else {
            alert(result.error || 'Failed to upload image');
          }
        } catch (error) {
          console.error('Error:', error);
          alert('An error occurred while uploading.');
        } finally {
          profileDisplay.style.opacity = '1';
          avatarSpinner.classList.add('d-none');
          fileInput.value = '';
        }
      });
    })();

    // Feedback modal interactions + submit (kept)
    (function() {
      const openFeedbackBtn = document.getElementById('openFeedbackBtn');
      const feedbackModalEl = document.getElementById('feedbackModal');
      const feedbackForm = document.getElementById('feedbackForm');
      const feedbackAlert = document.getElementById('feedbackAlert');
      const feedbackSubmitBtn = document.getElementById('feedbackSubmitBtn');
      const feedbackSpinner = document.getElementById('feedbackSpinner');
      const feedbackSubmitText = document.getElementById('feedbackSubmitText');

      function showAlert(type, text) {
        feedbackAlert.className = 'alert alert-' + type;
        feedbackAlert.textContent = text;
        feedbackAlert.classList.remove('d-none');
      }

      function hideAlert() {
        feedbackAlert.classList.add('d-none');
        feedbackAlert.textContent = '';
      }

      if (openFeedbackBtn && feedbackModalEl) {
        openFeedbackBtn.addEventListener('click', function(e) {
          e.preventDefault();
          hideAlert();
          feedbackForm.reset();
          const bs = new bootstrap.Modal(feedbackModalEl);
          bs.show();
        });
      }

      feedbackForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        hideAlert();

        const subject = document.getElementById('feedbackSubject').value.trim();
        const category = document.getElementById('feedbackCategory').value;
        const message = document.getElementById('feedbackMessage').value.trim();
        const csrf = document.querySelector('input[name="csrf_token"]').value;

        if (!message || message.length < 5) {
          showAlert('warning', 'Please enter your feedback (at least 5 characters).');
          return;
        }
        if (message.length > 2000) {
          showAlert('warning', 'Feedback is too long (max 2000 characters).');
          return;
        }

        feedbackSubmitBtn.disabled = true;
        feedbackSpinner.classList.remove('d-none');
        feedbackSubmitText.textContent = 'Submitting...';

        try {
          const fd = new FormData();
          fd.append('subject', subject);
          fd.append('category', category);
          fd.append('message', message);
          fd.append('csrf_token', csrf);

          const res = await fetch('submitFeedback.php', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          });

          const data = await res.json().catch(() => null);

          if (!res.ok || !data) {
            showAlert('danger', 'Unable to submit feedback. Please try again later.');
          } else if (!data.success) {
            showAlert('danger', data.message || 'Submission failed.');
          } else {
            showAlert('success', data.message || 'Feedback submitted. Thank you!');
            setTimeout(function() {
              try {
                bootstrap.Modal.getInstance(feedbackModalEl).hide();
              } catch (e) {}
            }, 900);
          }
        } catch (err) {
          console.error(err);
          showAlert('danger', 'Network error. Please try again.');
        } finally {
          feedbackSubmitBtn.disabled = false;
          feedbackSpinner.classList.add('d-none');
          feedbackSubmitText.textContent = 'Submit Feedback';
        }
      });
    })();

    // Character counter (kept)
    (function() {
      const msg = document.getElementById('feedbackMessage');
      const badge = document.getElementById('feedbackCharCount');
      if (!msg || !badge) return;

      const max = 2000;

      function update() {
        const len = (msg.value || '').length;
        badge.textContent = `${len} / ${max}`;
        badge.className = 'badge rounded-pill ' + (len > max ? 'text-bg-danger' : 'text-bg-light border');
      }
      msg.addEventListener('input', update);
      update();
    })();

    // ✅ Auto-open Feedback section when coming from notifications (profile.php#feedback) (kept)
    (function() {
      if (window.location.hash === '#feedback' && window.bootstrap) {
        const anchor = document.getElementById('feedback');
        if (anchor) anchor.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });

        const feedbackCollapseEl = document.getElementById('feedbackCollapse');
        if (feedbackCollapseEl) {
          const c = bootstrap.Collapse.getOrCreateInstance(feedbackCollapseEl, {
            toggle: false
          });
          c.show();
        }
      }
    })();
  </script>

</body>

</html>