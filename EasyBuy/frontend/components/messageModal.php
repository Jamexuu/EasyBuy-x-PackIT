<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body text-center py-5 px-4">
                <h3 class="fw-bold mb-4" id="messageModalLabel" style="color: #2c3e50;">Thank you for your feedback!</h3>
                <button type="button" class="btn btn-lg px-5 py-3 rounded-pill text-white fw-bold" 
                        data-bs-dismiss="modal" 
                        style="background-color: #6EC064; border: none; font-size: 1.1rem;">
                    CONTINUE SHOPPING
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showMessage(type, title, message) {
        const modal = new bootstrap.Modal(document.getElementById('messageModal'));
        const modalTitle = document.getElementById('messageModalLabel');

        modalTitle.textContent = title || 'Thank you for your feedback!';

        modal.show();
    }
</script>
