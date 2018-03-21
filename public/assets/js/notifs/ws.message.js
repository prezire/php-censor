// Generated by CoffeeScript 1.12.5

/*
WebSocket Message class.
@param  string  @title  The title that 
appears in the Notification popup UI.
@param  string  @body  The details that 
appears in the Notification popup UI.
@param  string  @uri  The landing page 
when the message is clicked.
 */

(function() {
  this.WsMessage = (function() {
    function WsMessage(title, body, uri) {
      this.title = title;
      this.body = body;
      this.uri = uri;
    }

    return WsMessage;

  })();

}).call(this);