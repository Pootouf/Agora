console.log(urlSock);
urlWS = urlSock.split("/")[2];

urlWS = urlWS.split(":")[0];

var conn = new WebSocket('ws://' + urlWS +':8095')

//for each action : factorise
//suppress the calcul

document.getElementsByName("action").forEach(doc => doc.onclick = function() {
    if(!data['ready']){
        data = {
            'playerId': 1,
            'ready':true,
            'type' : 'jouer',
            'action':'rail',
            'actionId': doc.id,
            'chemin' : null,
            'nb' : -273
        }
    }
})

document.getElementById("stPeter").onclick= function(){
    console.log("debug : ");
    console.log(data);
    console.log("ready : " + data['ready']);
    if(data['ready']){
        data['chemin'] = 'stPeter';
        conn.send(JSON.stringify(data));
        console.log("sended");
    }
    
}

document.getElementById("kiev").onclick= function(){
    console.log("debug : ");
    console.log(data);
    console.log("ready : " + data['ready']);
    if(data['ready']){
        data['chemin'] = 'kiev';
        conn.send(JSON.stringify(data));
        console.log("sended");
    }
    
}

document.getElementById("trans").onclick= function(){
    console.log("debug : " );
    console.log(data);
    console.log("ready : " + data['ready']);
    if(data['ready']){
        data['chemin'] = 'trans';
        conn.send(JSON.stringify(data));
        console.log("sended");
    }
    
}

conn.onmessage = function(e){
    var receivedData = JSON.parse(e.data);
    //console.log("received:");
    //console.log(receivedData);

    if(receivedData.type== 'refresh'){
        //console.log("receive");
        if(receivedData.line == "kiev"){

            for(r = 0; r < 9; r++){

                if(r == receivedData.lineContent[3]){
                    document.getElementById("kiev_" + r).innerHTML = "M";   
                } else if(r == receivedData.lineContent[2]){
                    document.getElementById("kiev_" + r).innerHTML = "G";
                } else if(r == receivedData.lineContent[1]){
                    document.getElementById("kiev_" + r).innerHTML = "N";
                } else {
                    document.getElementById("kiev_" + r).innerHTML = "=";
                }
            }
        } else if(receivedData.line == "trans"){
            for(r = 0; r < 15; r++){

                if(r == receivedData.lineContent[5]){
                    document.getElementById("trans_" + r).innerHTML = "B";
                } else if(r == receivedData.lineContent[4]){
                    document.getElementById("trans_" + r).innerHTML = "b";
                } else if(r == receivedData.lineContent[3]){
                    document.getElementById("trans_" + r).innerHTML = "M";   
                } else if(r == receivedData.lineContent[2]){
                    document.getElementById("trans_" + r).innerHTML = "G";
                } else if(r == receivedData.lineContent[1]){
                    document.getElementById("trans_" + r).innerHTML = "N";
                } else {
                    document.getElementById("trans_" + r).innerHTML = "=";
                }
            }
        } else if(receivedData.line == "stPeter"){
            for(r = 0; r < 9; r++){

                if(r == receivedData.lineContent[4]){
                    document.getElementById("stPeter_" + r).innerHTML = "b";
                } else if(r == receivedData.lineContent[3]){
                    document.getElementById("stPeter_" + r).innerHTML = "M";   
                } else if(r == receivedData.lineContent[2]){
                    document.getElementById("stPeter_" + r).innerHTML = "G";
                } else if(r == receivedData.lineContent[1]){
                    document.getElementById("stPeter_" + r).innerHTML = "N";
                } else {
                    document.getElementById("stPeter_" + r).innerHTML = "=";
                }
            }
        }

        document.getElementsByName("action").forEach(doc => {
            if(receivedData.actionReserved[doc.id]){
                doc.innerHTML = "<b>" + receivedData.actionInfo[doc.id] + "</br>"; 
            } else {
                doc.innerHTML = receivedData.actionInfo[doc.id];
            }
        } );

        

        if(receivedData.nb > 0){
            // on fait juste la suppression
            if(receivedData.actionType == "rail"){
                data = {
                    'playerId' : receivedData.playerId,
                    'ready' : true,
                    'action' : receivedData.actionType,
                    'type' : 'jouer',
                    'actionId': receivedData.actionId,
                    'chemin' : null,
                    'nb' : receivedData.nb
                }
            }
            
        } else {
            data = {
                'ready' : false
            }
        }

    }

    //console.log(data);

    
}
