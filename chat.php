<?php
session_start();

if (!isset($_SESSION['user']) && empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$user_token = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link href="./assets/css/chat.css" rel="stylesheet" type="text/css">
    <!-- Fonts and Icons -->
    <link href="assets/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Hidden Chat | Talk freely!</title>
</head>

<body class="container-fluid m-0 p-0 g-0 chat-room">
    <div class="row g-0 p-0 m-0">
        <div class="col">
            <div class="row g-0 p-0 m-0">
                <div class="col-12 d-flex justify-content-between align-items-center chat-actions-area">
                    <div>
                        <h2>Chat room title</h2>
                    </div>
                    <div class="actions d-flex align-items-center">
                        <!--
                        <button class="btn" type="button"><i class="fas fa-phone"></i></button>
                        -->
                        <button class="btn" type="button" id="settings-btn" data-bs-toggle="modal" data-bs-target="#settingsModal">
                            <i class="fas fa-cog"></i>
                        </button>
                        <button class="btn" type="button" id="mute-conversation">
                            <i class="fas fa-volume-up"></i>
                        </button>
                        <form action="./logout.php" method="GET">
                            <button class="btn" type="submit"><i class="fas fa-sign-out-alt"></i></button>
                        </form>
                    </div>
                </div>
                <div class="col-12 messages-area" id="chat-history">
                    <!-- Start of messages wrapper -->
                    <?php require_once('./php/load.php'); ?>
                    <a href="javascript:void(0)" id="scroll-btn" class="position-fixed bottom-50 end-0 translate-middle-y">
                        <i class="fas fa-arrow-down"></i>
                    </a>
                    <!-- Messages wrapper end -->
                </div>
                <!-- Form Start -->
                <form class="col-12 messages-form" action="#" method="POST" id="chat-form">
                    <div class="mb-3 position-relative">
                        <textarea name="message" class="form-control" placeholder="Type your message here... "></textarea>
                        <div class="actions-btn-group position-absolute top-50 end-0 translate-middle-y d-flex m-0 p-0">
                            <button class="btn shadow m-0" type="submit">
                                <i class="fas fa-paper-plane "></i>
                            </button>
                            <button class="btn shadow m-0" type="button" id="attachmentBtn">
                                <i class="fas fa-file"></i>
                            </button>
                            <input type="file" name="upload" id="attachment" class="d-none" accept="video/*,image/*" multiple>  
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Show File Attachments Modal -->
    <div class="file-modal col-12" id="modal">
        <button type="button" class="btn btn-lg modal-close-btn" id="close-modal-btn">Close</button>
        <div class="content">
            <video id="video-showoff" src="" controls></video>
            <img id="image-showoff" src="" alt="...">
        </div>
    </div>
    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
                    <button type="button" class="btn btn-lg" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST" onsubmit="return false;">
                        <div class="mb-3">
                            <input class="form-control" type="text" name="username" placeholder="Username">
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="text" name="roomName" placeholder="Room Name">
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="password" name="roomPassword" placeholder="Room Password">
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="password" name="roomRepeatPassword" placeholder="Repeat Room Password">
                        </div>
                        <div class="mb-3 d-flex justify-content-end align-items-center">
                            <button type="submit" class="btn">
                                Apply changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Effects Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <!-- Chat Script -->
    <script>
        $(document).ready(async function() {
            //Elements
            const doc = document;
            const history = $('#chat-history');
            const muteConversationBtn = $('#mute-conversation');
            const scrollBtn = $('#scroll-btn');
            const attachmentBtn = $('#attachmentBtn');
            const fileInput = $('#attachment');
            const modal = $('#modal');
            const closeModalBtn = $('#close-modal-btn');
            const video = $('#video-showoff');
            const image = $('#image-showoff');

            //Chat muted
            let muted = false;

            const timeout = 200;

            //Networking
            const host = '127.0.0.1';
            const port = 8088;
            
            //Sounds
            let ding = new Audio('./assets/sounds/ding.wav');

            //User details
            const username = '<?php echo $username ?>';
            const userToken = '<?php echo $user_token ?>';

            //Upload buffer
            let fileBuffer = [];

            let attachments = {};

            //Get file buttons event handler
            function attachEventHandlers(){
                $('.view-file').on('click', async function(event){
                    await fetchAttachment(event);
                });
            }

            attachEventHandlers();

            async function fetchAttachment(ev){
                let token = ev.target.getAttribute('data-token');

                if(!attachments[token]){
                    let request = await fetch(`./php/get_file.php?ft=${token}`, {
                        method: 'GET',
                    });

                    let result = await request.json();

                    attachments[token]=result;

                    showAttachment(attachments[token]);
                }else{
                    showAttachment(attachments[token]);
                }
            }

            function showAttachment(attachment){
                //console.log(attachment);
                switch(attachment.data){
                    case 'video':
                        video.attr('src', attachment.message);
                        video.show();
                        showModal();
                        break;
                    case 'image':
                        image.attr('src', attachment.message);
                        image.show();
                        showModal();
                        break;
                    default:
                        alert('Unsupported attachment!');
                        console.log(attachment);
                        break;
                }
            }

            function showModal(){
                modal.show(timeout);
            }

            function hideModal(){
                modal.hide(timeout);
                image.attr('src', '');
                video.attr('src', '');

                image.hide();
                video.hide();
            }

            closeModalBtn.click(function(){
                hideModal();
            });

            function scrollBottom(){
                let scroll = doc.getElementById('chat-history').scrollHeight;

                history.scrollTop(scroll);
            }

            muteConversationBtn.click(function(){
                if(!muted && confirm('Are you sure to mute this conversation?')){
                    muteConversationBtn.html('<i class="fas fa-volume-mute"></i>');
                }else{
                    muteConversationBtn.html('<i class="fas fa-volume-up"></i>');
                }

                muted = !muted;
            });

            scrollBtn.click(function(){
                scrollBottom();
            });

            history.scroll(function(){
                const chat = doc.getElementById('chat-history');
                let scrollH = chat.scrollHeight;
                let scrollTop = Math.abs(chat.scrollTop) + chat.clientHeight;

                if(scrollH == scrollTop){
                    scrollBtn.hide(timeout, 'swing');
                }else{
                    scrollBtn.show(timeout, 'swing');
                }
            });

            $('#chat-form').submit(async function(e) {
                e.preventDefault();

                let form = $(this)[0];

                let formData = new FormData(form);

                let msg = formData.get('message').trim();

                if (msg) {
                    form.reset();

                    let d = new Date();

                    let timestamp = `${d.toLocaleDateString()} - ${d.toLocaleTimeString()}`;

                    try {
                        let request = await sendMessage({
                            t: Date.now(),
                            type: 1,
                            username: username,
                            message: msg,
                            time: timestamp,
                            token: userToken,
                        });
                    } catch (err) {
                        console.log(err);
                    }
                }
            });

            async function sendMessage(msg) {
                return new Promise(function(resolve, reject) {
                    let http = new XMLHttpRequest();

                    http.open('POST', `http://${host}:${port}`, true);

                    http.setRequestHeader("Content-type", "application/json; charset=utf-8");

                    http.send(JSON.stringify(msg));

                    http.onreadystatechange = function() {
                        if (this.readyState == 4) {
                            if (this.status == 200 || this.status == 204) {
                                resolve(this.responseText);
                            } else {
                                reject(this.status, this.readyState);
                            }
                        }
                    }
                });
            }

            async function requestMessages() {
                return new Promise(function(resolve, reject) {
                    let http = new XMLHttpRequest();

                    http.open('POST', `php/client.php`, true);

                    http.onreadystatechange = function() {
                        if (this.readyState == 4) {
                            if (this.status == 200) {
                                resolve(this.responseText);
                            } else {
                                reject(this.response);
                            }
                        }
                    }

                    http.send();
                });
            }

            let request = async function() {
                try {
                    let msgs = await requestMessages();

                    let response = msgs.split("\r\n");

                    for (let msg of response) {
                        if (msg) {
                            let parsed = JSON.parse(msg);

                            showMessage(parsed);
                        }
                    }

                    //console.log(msgs);

                    return request();
                } catch (err) {
                    console.log(err);
                }
            };

            function showMessage(msg){
                let messageTemplate;

                if(msg.token != userToken){
                    if(msg.type == 1){
                        messageTemplate = `<div class="message-row receiver">
                            <div class="message">
                                <div class="avatar rounded d-inline-flex align-items-center">
                                    <img src="./assets/img/user.svg" width="40" height="40" alt="...">
                                    <span class="px-2">${msg.username}</span>
                                </div>
                                <p>${msg.message}</p>
                                <div class="message-timestamp">
                                    ${msg.time}
                                </div>
                            </div>
                        </div>`;
                    }else{
                        messageTemplate = `<div class="message-row receiver">
                            <div class="message">
                                <div class="avatar rounded d-inline-flex align-items-center">
                                    <img src="./assets/img/user.svg" width="40" height="40" alt="...">
                                    <span class="px-2">${msg.username}</span>
                                </div>
                                <p class="file-message"><a href="javascript:void(0)" data-token="${msg.file_token}" class="view-file">View file message</a></p>
                                <div class="message-timestamp">
                                    ${msg.time}
                                </div>
                            </div>
                        </div>`;
                    }

                    if(!muted){
                        ding.play();
                    }
                }else{
                    if(msg.type == 1){
                        messageTemplate = `<div class="message-row sender">
                            <div class="message">
                                <div class="avatar rounded d-inline-flex align-items-center">
                                    <img src="./assets/img/user.svg" width="40" height="40" alt="...">
                                    <span class="px-2">${msg.username}</span>
                                </div>
                                <p>${msg.message}</p>
                                <div class="message-timestamp">
                                    ${msg.time}
                                </div>
                            </div>
                        </div>`;
                    }else{
                        messageTemplate = `<div class="message-row sender">
                            <div class="message">
                                <div class="avatar rounded d-inline-flex align-items-center">
                                    <img src="./assets/img/user.svg" width="40" height="40" alt="...">
                                    <span class="px-2">${msg.username}</span>
                                </div>
                                <p class="file-message"><a href="javascript:void(0)" data-token="${msg.file_token}" class="view-file">View file message</a></p>
                                <div class="message-timestamp">
                                    ${msg.time}
                                </div>
                            </div>
                        </div>`;
                    }
                }

                history.append(messageTemplate);

                attachEventHandlers();

                scrollBottom();
            }

            //Trigger
            request();

            (function(){
                scrollBottom();
            })();

            attachmentBtn.click(function(){
                fileInput.click();
            });

            fileInput.on('change', async function(){
                let files = $(this).prop('files');

                for(let file of files){
                    let size = Math.round((file.size / 1024) / 1024);
                    
                    if(size > 40){
                        console.log(`${file.name} exceeds filesize limits!`);
                    }else{
                        try{
                            await appendFileBuffer(file);
                        }catch(err){
                            console.log(err);
                        }
                    }
                }

                $(this).val('');

                startUpload();
            });

            function appendFileBuffer(file){
                return new Promise(function(res, rej){
                    let fileType = file.type;
                    let fileName = file.name;

                    let reader = new FileReader();

                    reader.readAsDataURL(file);

                    reader.onload = function(){
                        fileBuffer.push({type: fileType, name: fileName, data: reader.result});
                        res();
                    };

                    reader.onerror = function(){
                        rej(reader.error);
                    };
                });
            }

            async function startUpload(){
                for(let file of fileBuffer){
                    let fileType = file.type.split('/')[0];

                    let d = new Date();

                    let timestamp = `${d.toLocaleDateString()} - ${d.toLocaleTimeString()}`;
                    
                    try {
                        let request = await sendFile({
                            t: Date.now(),
                            type: 2,
                            username: username,
                            data: fileType,
                            message: file.data,
                            time: timestamp,
                            token: userToken,
                        });
                    } catch (err) {
                        console.log(err);
                    }
                }
            }

            async function sendFile(fileData){
                let formData = new FormData();

                formData.append('file', JSON.stringify(fileData));

                let upload = await fetch('./php/file.php', {
                    method: 'POST',
                    body: formData,
                });

                let result = await upload.text();

                console.log(result);
            }
        });
    </script>
</body>

</html>
