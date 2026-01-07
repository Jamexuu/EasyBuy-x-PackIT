<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="messageModalHeader">
                <h5 class="modal-title" id="messageModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showMessage(type, title, message) {
        const modal = new bootstrap.Modal(document.getElementById('messageModal'));
        const modalHeader = document.getElementById('messageModalHeader');
        const modalTitle = document.getElementById('messageModalLabel');
        const modalBody = document.getElementById('messageModalBody');

        modalTitle.textContent = title;
        modalBody.textContent = message;

        if (type === 'success') {
            modalHeader.style.backgroundColor = '#349C55';
            modalHeader.style.color = 'white';
            modalHeader.querySelector('.btn-close').classList.add('btn-close-white');
        } else if (type === 'error') {
            modalHeader.style.backgroundColor = '#dc3545';
            modalHeader.style.color = 'white';
            modalHeader.querySelector('.btn-close').classList.add('btn-close-white');
        } else {
            modalHeader.style.backgroundColor = '#f8f9fa';
            modalHeader.style.color = '#212529';
            modalHeader.querySelector('.btn-close').classList.remove('btn-close-white');
        }

        modal.show();
    }
</script>
