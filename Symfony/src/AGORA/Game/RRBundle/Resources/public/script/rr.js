console.log(urlSock);
var urlWS = urlSock.split("/")[2];

urlWS = urlWS.split(":")[0];

var conn = new WebSocket('ws://' + urlWS +':8095')

$(".action").click(function() {
    console.log($(this).attr('id').split("action")[1]);
})