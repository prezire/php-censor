###
WebSocket Notification class.
Refer to the layout view.
###
class @WsNotif
  constructor: (wsUri, topicUri) ->
    protocol = if window.location.protocol is 'http:' then 'ws:' else 'wss:'
    wsUri = "#{protocol}//#{wsUri}"
    ctx = @
    conn = new ab.Session wsUri, 
      -> 
        conn.subscribe topicUri, ctx.onSub
      ,
      -> console.log('something went wrong.'),
      {'skipSubprotocolCheck': true}

  onSub: (topic, data) ->
    console.log topic, data
    #@render()

  ###
  @param  string  label  The string to compare to, in order
  to render a new notification.
  @param  string  value  The string to render. This will be
  used by the label once there's a new notification to be 
  rendered.
  @param  string  url
  ###
  render: (label, value, url) ->
    sProj = '.sidebar-menu .treeview-menu.projects #notif.project'
    $("#{sProj}:contains('#{label}')").html value

$(document).ready -> 
  new WsNotif $('#push-notif').data('host'), $('#push-notif').data('topic')