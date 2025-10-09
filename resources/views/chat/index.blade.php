@extends('layouts.app')

@php
    $title = $title ?? 'Chat';
    $subtitle = $subtitle ?? 'Converse with the assistant';
@endphp

@push('styles')
<style>
    .chat-container {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        height: calc(100vh - 12rem);
        display: grid;
        grid-template-rows: auto 1fr auto;
    }

    .chat-header {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
    }

    .message-list {
        background: var(--bg-primary);
    }

    .message-item {
        max-width: 80%;
    }

    .message-user {
        background: #3b82f6;
        color: white;
    }

    .message-assistant {
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        background: currentColor;
        border-radius: 9999px;
        animation: typing 1.2s infinite ease-in-out;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.15s; }
    .typing-dot:nth-child(3) { animation-delay: 0.3s; }

    @keyframes typing {
        0%, 80%, 100% { opacity: 0.2; transform: translateY(0); }
        40% { opacity: 1; transform: translateY(-3px); }
    }

    .chat-input {
        background: var(--bg-secondary);
        border-top: 1px solid var(--border-color);
    }

    .icon-button {
        background: var(--hover-bg);
        transition: transform .15s ease, opacity .15s ease;
    }

    .icon-button:hover { transform: translateY(-1px); opacity: .9; }
</style>
@endpush

@section('content')
<div class="chat-container">
    <!-- Header -->
    <div class="chat-header px-5 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fas fa-robot"></i>
            </div>
            <div>
                <div class="font-semibold">Assistant</div>
                <div class="text-xs opacity-80">Single chat — history will be saved</div>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button class="icon-button w-9 h-9 rounded-lg flex items-center justify-center text-white/90">
                <i class="fas fa-ellipsis"></i>
            </button>
        </div>
    </div>

    <!-- Messages -->
    <div id="chatMessages" class="message-list overflow-y-auto p-4 custom-scrollbar">
        <!-- Example assistant welcome message -->
        <div class="flex items-end mb-4">
            <div class="message-item message-assistant rounded-2xl px-4 py-3 shadow-sm">
                <div class="text-sm">Hi! How can I help you today?</div>
            </div>
        </div>
        <!-- Example user message -->
        <div class="flex items-end justify-end mb-4">
            <div class="message-item message-user rounded-2xl px-4 py-3 shadow-sm">
                <div class="text-sm">Show me available rooms for next month.</div>
            </div>
        </div>
    </div>

    <!-- Composer -->
    <div class="chat-input p-4">
        <form id="chatForm" class="flex items-end space-x-3" onsubmit="return false;">
            <div class="flex-1">
                <div class="relative">
                    <textarea id="chatInput" class="w-full resize-none rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 p-3 pr-24 bg-transparent text-sm" rows="1" placeholder="Type your message..."></textarea>
                    <div class="absolute right-2 bottom-2 flex items-center space-x-2">
                        <button type="button" class="icon-button w-9 h-9 rounded-lg flex items-center justify-center text-gray-600" title="Attach">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button id="sendBtn" type="submit" class="w-9 h-9 rounded-lg flex items-center justify-center text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <div id="typingIndicator" class="mt-3 hidden">
            <div class="flex items-center space-x-2 text-gray-500 text-xs">
                <span>Assistant is typing</span>
                <div class="flex items-center space-x-1">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const messagesEl = document.getElementById('chatMessages');
        const form = document.getElementById('chatForm');
        const input = document.getElementById('chatInput');
        const typing = document.getElementById('typingIndicator');

        function autoGrow(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        }

        function appendMessage(content, role) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex items-end mb-4 ' + (role === 'user' ? 'justify-end' : '');

            const bubble = document.createElement('div');
            bubble.className = 'message-item rounded-2xl px-4 py-3 shadow-sm ' + (role === 'user' ? 'message-user' : 'message-assistant');
            bubble.innerHTML = `<div class="text-sm"></div>`;
            bubble.firstChild.textContent = content;

            wrapper.appendChild(bubble);
            messagesEl.appendChild(wrapper);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        input.addEventListener('input', function () { autoGrow(input); });

        form.addEventListener('submit', function () {
            const text = (input.value || '').trim();
            if (!text) return;
            appendMessage(text, 'user');
            input.value = '';
            autoGrow(input);

            // Fake typing and response (no backend yet)
            typing.classList.remove('hidden');
            setTimeout(() => {
                typing.classList.add('hidden');
                appendMessage('This is a placeholder response. Backend integration pending.', 'assistant');
            }, 900);
        });

        // Initialize autosize
        autoGrow(input);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    })();
</script>
@endpush


