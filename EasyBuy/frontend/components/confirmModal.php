<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body text-center py-5 px-4">
                <div id="confirmModalIcon" class="mb-3"></div>
                <h3 class="fw-bold mb-3" id="confirmModalLabel" style="color: #2c3e50;">Confirm Action</h3>
                <p id="confirmModalText" class="text-muted mb-4"></p>
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" id="confirmModalCancelBtn" class="btn btn-lg px-5 py-3 rounded-pill fw-bold" 
                            data-bs-dismiss="modal" 
                            style="background-color: #6c757d; color: white; border: none; font-size: 1.1rem;">
                        Cancel
                    </button>
                    <button type="button" id="confirmModalConfirmBtn" class="btn btn-lg px-5 py-3 rounded-pill text-white fw-bold" 
                            style="border: none; font-size: 1.1rem;">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var confirmModalCallback = null;

    function showConfirm(type, title, message, confirmLabel, cancelLabel) {
        return new Promise(function(resolve) {
            var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            var modalTitle = document.getElementById('confirmModalLabel');
            var modalText = document.getElementById('confirmModalText');
            var confirmBtn = document.getElementById('confirmModalConfirmBtn');
            var cancelBtn = document.getElementById('confirmModalCancelBtn');
            var modalIcon = document.getElementById('confirmModalIcon');

            modalTitle.textContent = title || 'Confirm Action';
            modalText.textContent = message || '';
            confirmBtn.textContent = confirmLabel || 'Confirm';
            cancelBtn.textContent = cancelLabel || 'Cancel';

            modalIcon.innerHTML = '';
            confirmBtn.style.backgroundColor = '#28a745';
            confirmBtn.style.color = '#fff';

            if (type === 'success') {
                modalIcon.innerHTML = '<span class="material-symbols-rounded" style="font-size: 4rem; color: #28a745;">check_circle</span>';
                confirmBtn.style.backgroundColor = '#28a745';
            } else if (type === 'error') {
                modalIcon.innerHTML = '<span class="material-symbols-rounded" style="font-size: 4rem; color: #dc3545;">error</span>';
                confirmBtn.style.backgroundColor = '#dc3545';
            } else if (type === 'warning' || type === 'delete') {
                modalIcon.innerHTML = '<span class="material-symbols-rounded" style="font-size: 4rem; color: #ffc107;">warning</span>';
                confirmBtn.style.backgroundColor = '#dc3545';
            } else if (type === 'info') {
                modalIcon.innerHTML = '<span class="material-symbols-rounded" style="font-size: 4rem; color: #17a2b8;">info</span>';
                confirmBtn.style.backgroundColor = '#17a2b8';
            }

            confirmModalCallback = function(confirmed) {
                resolve(confirmed);
                confirmModalCallback = null;
            };

            confirmBtn.onclick = function() {
                modal.hide();
                if (confirmModalCallback) confirmModalCallback(true);
            };

            cancelBtn.onclick = function() {
                modal.hide();
                if (confirmModalCallback) confirmModalCallback(false);
            };

            document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
                if (confirmModalCallback) confirmModalCallback(false);
            }, { once: true });

            modal.show();
        });
    }
</script>
