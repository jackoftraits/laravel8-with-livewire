/**
 * Initilize a web socket client
 */
function clientSocket(config = {}) {
    let route = config.route || "127.0.0.1";
    let port = config.port || "3280";
    window.Websocket = window.WebSocket || window.MozWebSocket;
    return new WebSocket("ws://" + route + ":" + port);
}
