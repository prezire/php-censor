###
WebSocket Message class.
@param  string  @title  The title that 
appears in the Notification popup UI.
@param  string  @body  The details that 
appears in the Notification popup UI.
@param  string  @uri  The landing page 
when the message is clicked.
###
class @WsMessage
  constructor: (@title, @body, @uri) -> #