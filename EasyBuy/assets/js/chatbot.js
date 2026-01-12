const chatbotToggle = document.getElementById("chatbotToggle");
const chatbotWindow = document.getElementById("chatbotWindow");
const minimizeBtn = document.getElementById("minimizeBtn");
const chatMessages = document.getElementById("chatMessages");
const chatInput = document.getElementById("chatInput");
const sendBtn = document.getElementById("sendBtn");
const chatAvatar = `<img src="/EasyBuy-x-PackIT/EasyBuy/assets/chatbot_logo.svg" alt="" class="img-fluid" style="width: 24px; height: 24px;">`;
const avatarBootstrap = "chat-avatar rounded-circle d-flex align-items-center justify-content-center flex-shrink-0";
const avatarStyle = "width: 40px; height: 40px; background-color: #6EC064;";

chatbotToggle.addEventListener("click", () => {
    console.log("Toggle clicked!");
    chatbotWindow.classList.toggle("d-none");
    chatbotWindow.classList.toggle("d-flex");
});

minimizeBtn.addEventListener("click", () => {
    chatbotWindow.classList.add("d-none");
    chatbotWindow.classList.remove("d-flex");
});

function addMessage(text, isUser) {
    const wrapper = document.createElement("div");
    wrapper.className = `d-flex mb-3 gap-2 ${isUser ? "justify-content-end" : "justify-content-start"}`;

    if (!isUser) {
        const avatar = document.createElement("div");
        avatar.className = avatarBootstrap;
        avatar.style.cssText = avatarStyle;
        avatar.innerHTML = chatAvatar;
        wrapper.appendChild(avatar);
    }

    const bubble = document.createElement("div");
    bubble.className = "message-bubble card border-0 shadow-sm rounded-4";
    bubble.style.maxWidth = "70%";

    const cardBody = document.createElement("div");
    cardBody.className = `card-body p-3 rounded-4 ${isUser ? "text-white" : ""}`;
    cardBody.style.backgroundColor = isUser ? "#6EC064" : "white";

    const messageText = document.createElement("small");
    messageText.className = isUser ? "text-white" : "text-dark";
    messageText.textContent = text;

    cardBody.appendChild(messageText);
    bubble.appendChild(cardBody);
    wrapper.appendChild(bubble);
    chatMessages.appendChild(wrapper);

    chatMessages.scrollTop = chatMessages.scrollHeight;
}

sendBtn.addEventListener("click", () => {
    const text = chatInput.value.trim();
    if (text) {
        addMessage(text, true);
        chatInput.value = "";

        const replyId = `reply-${Date.now()}`;
        const tempWrapper = document.createElement("div");
        tempWrapper.className = "d-flex justify-content-start mb-3 gap-2";

        const avatar = document.createElement("div");
        avatar.className = avatarBootstrap;
        avatar.style.cssText = avatarStyle;
        avatar.innerHTML = chatAvatar;

        tempWrapper.appendChild(avatar);

        const tempBubble = document.createElement("div");
        tempBubble.className = "message-bubble card border-0 shadow-sm rounded-4";
        tempBubble.style.maxWidth = "70%";
        tempBubble.innerHTML = `<div class="card-body p-3 rounded-4" style="background-color: white;"><small class="text-dark" id="${replyId}">...</small></div>`;

        tempWrapper.appendChild(tempBubble);
        chatMessages.appendChild(tempWrapper);

        fetch("/EasyBuy-x-PackIT/EasyBuy/api/ai/response.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ prompt: text }),
        })
        .then((res) => res.text())
        .then((reply) => {
            document.getElementById(replyId).textContent = reply;
        })
        .catch((error) => {
            console.error("Error:", error);
            document.getElementById(replyId).textContent = `Error: ${error.message}`;
        });
    }
});

chatInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
        sendBtn.click();
    }
});