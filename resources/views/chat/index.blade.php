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
        const sendBtn = document.getElementById('sendBtn');
        
        let conversationId = 'conv_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        let isLoading = false;

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function autoGrow(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        }

        function appendMessage(content, role, timestamp = null) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex items-end mb-4 ' + (role === 'user' ? 'justify-end' : '');

            const bubble = document.createElement('div');
            bubble.className = 'message-item rounded-2xl px-4 py-3 shadow-sm ' + (role === 'user' ? 'message-user' : 'message-assistant');
            
            const messageContent = document.createElement('div');
            messageContent.className = 'text-sm';
            messageContent.textContent = content;
            
            const timeElement = document.createElement('div');
            timeElement.className = 'text-xs mt-1 ' + (role === 'user' ? 'text-blue-100' : 'text-gray-500');
            timeElement.textContent = timestamp || new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            
            bubble.appendChild(messageContent);
            bubble.appendChild(timeElement);
            wrapper.appendChild(bubble);
            messagesEl.appendChild(wrapper);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function showTypingIndicator() {
            typing.classList.remove('hidden');
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function hideTypingIndicator() {
            typing.classList.add('hidden');
        }

        async function sendMessage(message) {
            if (isLoading) return;
            
            isLoading = true;
            sendBtn.disabled = true;
            sendBtn.style.opacity = '0.5';
            
            // Add user message
            appendMessage(message, 'user');
            
            // Show typing indicator
            showTypingIndicator();
            
            try {
                const response = await fetch('/api/v1/chat/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: message,
                        conversation_id: conversationId
                    })
                });

                const data = await response.json();
                
                // Hide typing indicator
                hideTypingIndicator();
                
                if (data.success) {
                    // Add AI response
                    if (data.data.ai_response && data.data.ai_response.message) {
                        appendMessage(data.data.ai_response.message, 'assistant');
                    } else {
                        appendMessage('I received your message but couldn\'t generate a response. Please try again.', 'assistant');
                    }
                } else {
                    appendMessage('Sorry, I encountered an error. Please try again.', 'assistant');
                    console.error('Chat error:', data);
                }
            } catch (error) {
                hideTypingIndicator();
                appendMessage('Sorry, I couldn\'t connect to the server. Please check your connection and try again.', 'assistant');
                console.error('Network error:', error);
            } finally {
                isLoading = false;
                sendBtn.disabled = false;
                sendBtn.style.opacity = '1';
                input.focus();
            }
        }

        input.addEventListener('input', function () { 
            autoGrow(input); 
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = (input.value || '').trim();
            if (!text || isLoading) return;
            
            input.value = '';
            autoGrow(input);
            sendMessage(text);
        });

        // Handle Enter key (but allow Shift+Enter for new lines)
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        });

        // Initialize autosize
        autoGrow(input);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    })();
</script>
@endpush


