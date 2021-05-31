/*
const hdFormat = { width: 1280, height: 720 };

async function getMedia() {
    try {
        return await navigator.mediaDevices.getUserMedia({ audio: true, video: hdFormat });
    } catch (err) {
        console.log(err);
        //Block the app in case the user hasn't enabled video and audio
        blockApp();
    }
}

let canvas = document.getElementById('webcam');

let streamHandler = null;

try {
    streamHandler = await getMedia();
} catch (err) {
    console.log(err);
}

window.stream = streamHandler;

window.stream.getTracks().forEach(function (track) {
    console.log(track);
});

canvas.srcObject = streamHandler;

console.log(canvas.srcObject);

canvas.oncanplay = function (ev) {
    //show in the video element what is being captured by the webcam
    canvas.play();
};

$('#hideWebcam').click(function () {
    $('#video-wrapper').fadeToggle(hideTiming);
})
*/