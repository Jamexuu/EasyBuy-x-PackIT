<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body text-center py-5 px-4">
                <div id="messageModalIcon" class="mb-3"></div>
                <h3 class="fw-bold mb-3" id="messageModalLabel" style="color: #2c3e50;">Message</h3>
                <p id="messageModalText" class="text-muted mb-4"></p>
                <button type="button" id="messageModalBtn" class="btn btn-lg px-5 py-3 rounded-pill text-white fw-bold" 
                        data-bs-dismiss="modal" 
                        style="border: none; font-size: 1.1rem;">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showMessage(type, title, message, buttonLabel) {
        var modal = new bootstrap.Modal(document.getElementById('messageModal'));
        var modalTitle = document.getElementById('messageModalLabel');
        var modalText = document.getElementById('messageModalText');
        var modalBtn = document.getElementById('messageModalBtn');
        var modalIcon = document.getElementById('messageModalIcon');

        modalTitle.textContent = title || 'Message';
        modalText.textContent = message || '';
        modalBtn.textContent = buttonLabel || 'OK';

        modalIcon.innerHTML = '';
        modalBtn.style.backgroundColor = '#6EC064';

        if (type === 'success') {
            modalIcon.innerHTML = '<span class="material-symbols-rounded" style="font-size: 4rem; color: #28a745;">check_circle</span>';
            modalBtn.style.backgroundColor = '#28a745';
        } else if (type === 'error') {
            modalIcon.innerHTML = '<span class="material-symbols-rounded" style="font-size: 4rem; color: #dc3545;">error</span>';
            modalBtn.style.backgroundColor = '#dc3545';
        } else if (type === 'warning' || type === 'delete') {
            modalIcon.innerHTML = '<span class="material-symbols-rounded" style="font-size: 4rem; color: #ffc107;">warning</span>';
            modalBtn.style.backgroundColor = '#ffc107';
            modalBtn.style.color = '#000';
        }

        modal.show();
    }
</script>
