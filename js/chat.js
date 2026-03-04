document.addEventListener('DOMContentLoaded', function() {
    var chatFab = document.getElementById('chatFab');
    var chatWidget = document.getElementById('chatWidget');
    var chatClose = document.getElementById('chatClose');
    var chatMinimize = document.getElementById('chatMinimize');
    var chatInput = document.getElementById('chatInput');
    var chatSendBtn = document.getElementById('chatSendBtn');
    var chatMessages = document.getElementById('chatMessages');
    var typingIndicator = document.getElementById('typingIndicator');
    var chatSuggestions = document.getElementById('chatSuggestions');

    if (!chatFab || !chatWidget) return;

    var isOpen = false;
    var hasBeenOpened = false;
    var langEl = document.getElementById('siteLang');
    var isAr = (langEl && langEl.value === 'ar');

    chatFab.addEventListener('click', function() {
        isOpen = !isOpen;
        chatWidget.classList.toggle('open', isOpen);
        chatFab.classList.toggle('active', isOpen);

        if (isOpen && !hasBeenOpened) {
            hasBeenOpened = true;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        if (isOpen && chatInput) {
            setTimeout(function() { chatInput.focus(); }, 300);
        }
    });

    if (chatClose) {
        chatClose.addEventListener('click', function() {
            isOpen = false;
            chatWidget.classList.remove('open');
            chatFab.classList.remove('active');
        });
    }

    if (chatMinimize) {
        chatMinimize.addEventListener('click', function() {
            isOpen = false;
            chatWidget.classList.remove('open');
            chatFab.classList.remove('active');
        });
    }

    // Suggestion chips
    if (chatSuggestions) {
        var chips = chatSuggestions.querySelectorAll('.suggestion-chip');
        chips.forEach(function(chip) {
            chip.addEventListener('click', function() {
                var msg = chip.getAttribute('data-msg');
                if (msg) {
                    chatInput.value = msg;
                    sendMessage();
                }
            });
        });
    }

    if (chatSendBtn) {
        chatSendBtn.addEventListener('click', sendMessage);
    }

    if (chatInput) {
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    function sendMessage() {
        var message = chatInput.value.trim();
        if (!message) return;

        // Hide suggestions after first user message
        if (chatSuggestions) {
            chatSuggestions.style.display = 'none';
        }

        appendMessage(message, 'user');
        chatInput.value = '';
        chatInput.focus();

        showTyping(true);

        fetch('api/chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            showTyping(false);
            if (data.success) {
                appendMessage(data.response, 'bot');
            } else {
                appendMessage(isAr ? 'عذراً، لم أتمكن من معالجة طلبك الآن. يرجى المحاولة مرة أخرى.' : 'Sorry, I could not process your request right now. Please try again.', 'bot');
            }
        })
        .catch(function() {
            showTyping(false);
            appendMessage(isAr ? 'خطأ في الاتصال. يرجى التحقق من الإنترنت والمحاولة مرة أخرى.' : 'Connection error. Please check your internet and try again.', 'bot');
        });
    }

    function appendMessage(text, sender) {
        var wrapper = document.createElement('div');
        wrapper.className = 'chat-message ' + sender;

        if (sender === 'bot') {
            var avatarDiv = document.createElement('div');
            avatarDiv.className = 'bot-avatar';
            avatarDiv.innerHTML = '<i class="fas fa-user-tie"></i>';
            wrapper.appendChild(avatarDiv);
        }

        var bubble = document.createElement('div');
        bubble.className = 'msg-bubble';

        // Format markdown-like responses
        var formattedText = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
        bubble.innerHTML = formattedText;

        var timeSpan = document.createElement('div');
        timeSpan.className = 'msg-time';
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        timeSpan.textContent = (sender === 'bot' ? (isAr ? 'أبو محمود' : 'Abu Mahmoud') : (isAr ? 'أنت' : 'You')) + ' · ' + hours + ':' + minutes + ' ' + ampm;
        bubble.appendChild(timeSpan);

        wrapper.appendChild(bubble);
        chatMessages.appendChild(wrapper);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTyping(show) {
        if (typingIndicator) {
            typingIndicator.classList.toggle('show', show);
        }
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    // Auto-open after 5 seconds on first visit
    if (!sessionStorage.getItem('chatShown')) {
        setTimeout(function() {
            if (!isOpen) {
                chatFab.classList.add('bounce-attention');
                setTimeout(function() {
                    chatFab.classList.remove('bounce-attention');
                }, 2000);
            }
        }, 5000);
        sessionStorage.setItem('chatShown', '1');
    }
});
