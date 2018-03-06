###
WebSocket Notification class.
Refer to the layout view.
###
class @WsNotif
  constructor: (wsUri, topicUri) ->
    if window.location.protocol is 'http:' 
      protocol = 'ws:'
    else
      protocol = 'wss:'
    wsUri = "#{protocol}//#{wsUri}"
    ctx = @
    conn = new ab.Session wsUri, 
      -> 
        conn.subscribe topicUri, (topic, data) ->
          ctx.onSub topic, data
      ,
      -> 
        console.log('something went wrong.')
      ,
      {'skipSubprotocolCheck': true}

  onSub: (topic, data) ->
    #title = data.title
    title = 'Git Test'
    value = 1
    url = 'http://www.google.com'
    @render title, value, url

  ###
  @param  string  title  The string to compare to, in order
  to render a new notification.
  @param  string  value  The string to render. This will be
  used by the title once there's a new notification to be 
  rendered.
  @param  string  url
  ###
  render: (title, value, url) ->
    sProj = '.sidebar-menu .treeview-menu.projects'
    sTitle = "#{sProj} li > a > span"
    i = $("#{sTitle}:contains('#{title}')").length
    if i > 0
      oNotif = $("#{sProj} #notif.project a")
      oNotif.html value

$(document).ready -> 
  new WsNotif $('#build-notif').data('host'), 
    $('#build-notif').data('topic')