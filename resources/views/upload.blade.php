<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PDF Generator with Chat</title>
    <style>
        body { font-family: sans-serif; text-align: center; margin: 50px; }
        h1 { color: #2563eb; }

        /* Original Image Upload */
        .drop-area {
            border: 2px dashed #2563eb;
            border-radius: 10px;
            padding: 40px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .drop-area.hover { background-color: #f0f8ff; }
        input[type="file"] { display: none; }
        img.preview { max-width: 200px; margin: 10px; border: 1px solid #ccc; }

        button { background-color: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }

        /* Chat Section */
        #chat-container {
            width: 400px;
            margin: 0 auto 20px auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            height: 300px;
            overflow-y: auto;
            border: 1px solid #ccc;
        }
        .bubble {
            max-width: 80%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 10px;
            word-wrap: break-word;
            display: flex;
            flex-direction: column;
        }
        .bubble.sent { background: #dcf8c6; align-self: flex-end; }
        .bubble.received { background: #fff; border: 1px solid #ccc; align-self: flex-start; }
        .bubble img { max-width: 200px; margin-top: 5px; border-radius: 10px; }
        #input-area { display: flex; margin-top: 10px; gap: 5px; width: 400px; margin-left: auto; margin-right: auto; }
        #input-text { flex: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
    </style>
</head>
<body>

    <!-- Logo -->
<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ asset('images/gmi logo 2.png') }}" 
         alt="Logo" 
         style="width: 200px; max-width: 100%; height: auto;">
</div>



<h1>Generate PDF Report</h1>

<form action="{{ route('generate.pdf') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Davinci Image -->
    <div class="drop-area" id="davinci-drop">
        <p>Drag & drop Davinci image here, click, or paste (Ctrl+V)</p>
        <input type="file" name="davinci_img" id="davinci-input" accept="image/*" required>
        <img id="davinci-preview" class="preview" style="display:none;">
    </div>

    <!-- Jibble Image -->
    <div class="drop-area" id="jibble-drop">
        <p>Drag & drop Jibble image here, click, or paste (Ctrl+V)</p>
        <input type="file" name="jibble_img" id="jibble-input" accept="image/*" required>
        <img id="jibble-preview" class="preview" style="display:none;">
    </div>

    <!-- Paste and comment Section -->
    <h2>Paste Section ( Paste Here )</h2>
    <div id="chat-container" onpaste="handlePaste(event)"></div>
    <textarea name="comments" rows="3" style="width: 400px;" placeholder="Add comment here if needed"></textarea>
    <div>
    <input type="text" name="file_name" placeholder="Enter file name e.g Jibble and Davinci week" style="width: 400px;">
    </div>
    <div id="input-area">
        


        
    </div>

    <!-- Hidden inputs for chat -->
    <div id="hidden-fields"></div>

    <button type="submit" name="type" value="pdf">Generate PDF</button>
    <button type="submit" name="type" value="word">Generate Word</button>
    <button type="button" style="margin-top:10px; background-color:#f56565;" onclick="clearAll()">Clear All</button>
    <div style="text-align: center; margin: 20px 0;">

    <a href="https://online.pylajar.my/" target="_blank">
        <button type="button" 
                style="background-color: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            Report Class Online
        </button>
    </a>
</div>

</form>

<script>

function addHiddenInput(kind, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = `messages[${messageCount}][${kind}]`;
    input.value = value;
    hiddenFields.appendChild(input);
    messageCount++;
}

// ==== Resize Function for Pasted or Dragged Images ====
function resizeImage(file, maxWidth =800, callback) {
    const img = new Image();
    const reader = new FileReader();

    reader.onload = e => img.src = e.target.result;

    img.onload = () => {
        const canvas = document.createElement('canvas');
        const scale = Math.min(maxWidth / img.width, 1);
        canvas.width = img.width * scale;
        canvas.height = img.height * scale;
        canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
        callback(canvas.toDataURL(file.type));
    };

    reader.readAsDataURL(file);
}

// ==== Original Image Upload ====
function setupDropArea(dropAreaId, inputId, previewId) {
    const dropArea = document.getElementById(dropAreaId);
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    dropArea.addEventListener('click', () => input.click());
    input.addEventListener('change', e => showPreview(e.target.files[0], preview));

    dropArea.addEventListener('dragover', e => { e.preventDefault(); dropArea.classList.add('hover'); });
    dropArea.addEventListener('dragleave', e => dropArea.classList.remove('hover'));

    dropArea.addEventListener('drop', e => {
        e.preventDefault();
        dropArea.classList.remove('hover');
        const file = e.dataTransfer.files[0];
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        showPreview(file, preview);
    });

    dropArea.addEventListener('paste', e => {
        const items = e.clipboardData.items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.startsWith('image/')) {
                const file = items[i].getAsFile();
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                showPreview(file, preview);
            }
        }
    });
}

function showPreview(file, preview) {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display = 'inline-block'; };
    reader.readAsDataURL(file);
}

setupDropArea('davinci-drop', 'davinci-input', 'davinci-preview');
setupDropArea('jibble-drop', 'jibble-input', 'jibble-preview');

// ==== Chat Section ====
const chatContainer = document.getElementById('chat-container');
const inputText = document.getElementById('input-text');
const inputFile = document.getElementById('input-file');
const hiddenFields = document.getElementById('hidden-fields');
let messageCount = 0;

function addTextMessage() {
    const text = inputText.value.trim();
    if (!text) return;
    addMessageToChat('sent', text);
    addHiddenInput('text', text);
    inputText.value = '';
}

function addFileMessage() {
    inputFile.click();
    inputFile.onchange = () => {
        const file = inputFile.files[0];
        if (!file) return;
        resizeImage(file, 800, resizedData => {
            addMessageToChat('sent', '', resizedData);
            addHiddenInput('image', resizedData);
        });
        inputFile.value = '';
    };
}

function addMessageToChat(type, text = '', imgSrc = '') {
    const bubble = document.createElement('div');
    bubble.className = `bubble ${type}`;
    if (text) bubble.textContent = text;
    if (imgSrc) {
        const img = document.createElement('img');
        img.src = imgSrc;
        bubble.appendChild(img);
    }
    chatContainer.appendChild(bubble);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function addHiddenInput(kind, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = `messages[${messageCount}][${kind}]`;
    input.value = value;
    hiddenFields.appendChild(input);
    messageCount++;
}

function handlePaste(e) {
    const items = e.clipboardData.items;
    for (let i = 0; i < items.length; i++) {
        if (items[i].type.startsWith('image/')) {
            const file = items[i].getAsFile();
            resizeImage(file, 800, resizedData => {
                addMessageToChat('sent', '', resizedData);
                addHiddenInput('image', resizedData);
            });
        }
    }
}

function clearAll() {
    // 1. Clear Davinci
    document.getElementById('davinci-input').value = '';
    const davinciPreview = document.getElementById('davinci-preview');
    davinciPreview.src = '';
    davinciPreview.style.display = 'none';

    // 2. Clear Jibble
    document.getElementById('jibble-input').value = '';
    const jibblePreview = document.getElementById('jibble-preview');
    jibblePreview.src = '';
    jibblePreview.style.display = 'none';

    // 3. Clear chat messages
    const chatContainer = document.getElementById('chat-container');
    chatContainer.innerHTML = '';

    // 4. Clear hidden inputs
    const hiddenFields = document.getElementById('hidden-fields');
    hiddenFields.innerHTML = '';

    // 5. Reset message counter
    messageCount = 0;
}

</script>

</body>
</html>
