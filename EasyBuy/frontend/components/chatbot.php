<style>
    .chatbot-window {
        bottom: 80px;
        right: 10px;
        left: auto;
    }

    .chatbot-logo {
        filter: drop-shadow(0 12px 36px rgba(0, 0, 0, 0.30));
        -webkit-filter: drop-shadow(0 12px 36px rgba(0, 0, 0, 0.30));
        transition: filter .18s ease, transform .18s ease;
    }

    .chatbot-logo:hover {
        filter: drop-shadow(0 18px 48px rgba(0, 0, 0, 0.36));
        -webkit-filter: drop-shadow(0 18px 48px rgba(0, 0, 0, 0.36));
        transform: translateY(-2px);
    }

    .chatbot-logo-sm {
        filter: drop-shadow(0 6px 18px rgba(0, 0, 0, 0.20));
        -webkit-filter: drop-shadow(0 6px 18px rgba(0, 0, 0, 0.20));
    }

    @media (min-width: 768px) {
        .chatbot-window {
            right: 10px;
            width: 380px;
        }
    }

    @media (max-width: 767px) {
        .chatbot-container {
            bottom: 70px !important;
        }

        .chatbot-window {
            bottom: 130px;
        }
    }
</style>

<div class="chatbot-container position-fixed bottom-0 end-0 p-3 p-md-4 z-3">
    <button class="btn rounded-circle shadow-lg p-0" id="chatbotToggle">
        <img src="/EasyBuy-x-PackIT/EasyBuy/assets/chatbot_logo.svg" alt="" class="img-fluid chatbot-logo">
    </button>

    <div class="chatbot-window card shadow-lg border-0 d-none rounded-4 d-flex flex-column position-fixed position-md-absolute mw-100"
        id="chatbotWindow" style="max-width: 380px; height: 70vh; max-height: 600px;">

        <div class="card-header border-0 rounded-top-4" style="background-color: var(--chatbot-primary);">
            <div class="row align-items-center g-2 g-md-3 p-1 p-md-2 mx-0">
                <div class="col-auto">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center p-2"
                        style="width: 40px; height: 40px;">
                        <img src="/EasyBuy-x-PackIT/EasyBuy/assets/chatbot_logo.svg" alt=""
                            class="img-fluid w-75 chatbot-logo">
                    </div>
                </div>
                <div class="col text-truncate">
                    <h5 class="mb-0 fs-6 fw-semibold text-dark">Ebby</h5>
                    <small class="text-white-50 d-none d-md-block">EasyBuy AI Chatbot Assistant</small>
                    <small class="text-white-50 d-md-none" style="font-size: 0.7rem;">AI Assistant</small>
                </div>
                <div class="col-auto">
                    <button
                        class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center material-symbols-rounded text-white border-0 p-2"
                        id="minimizeBtn" style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.2);">
                        keyboard_arrow_down
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body bg-light p-2 p-md-3 overflow-auto flex-grow-1" id="chatMessages">
            <div class="row g-2 mb-2 mx-0">
                <div class="col-auto">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px; background-color: var(--chatbot-primary);">
                        <img src="/EasyBuy-x-PackIT/EasyBuy/assets/chatbot_logo.svg" alt=""
                            class="img-fluid chatbot-logo-sm" style="width: 20px;">
                    </div>
                </div>
                <div class="col-auto" style="max-width: 85%;">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-2 p-md-3 rounded-4">
                            <small class="text-dark">Hello! How can I help you?</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white border-top p-2 p-md-3 rounded-bottom-4">
            <div class="row g-2 align-items-center mx-0">
                <div class="col">
                    <input type="text" id="chatInput"
                        class="form-control rounded-pill border shadow-none px-3 py-2 small"
                        placeholder="Send your question...">
                </div>
                <div class="col-auto">
                    <button
                        class="btn text-white rounded-circle p-0 material-symbols-rounded d-flex align-items-center justify-content-center flex-shrink-0"
                        id="sendBtn"
                        style="width: 36px; height: 36px; background-color: var(--chatbot-primary);">send</button>
                </div>
            </div>
        </div>
    </div>
</div>