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
          data = {title: 'Git Test'}
          console.log 'on data', data
          ctx.onSub topic, data
      ,
      -> 
        console.log('Something went wrong. Check if the server is still running')
      ,
      {'skipSubprotocolCheck': true}

  onSub: (topic, data) ->
    #TODO: topic.
    @render data.title, data.url

  ###
  @param  string  title  The string to compare to, in order
  to render a new notification.
  ###
  render: (title) ->
    sProj = '.sidebar-menu .treeview-menu.projects'
    sTitle = "#{sProj} li > a > span"
    oTitle = $("#{sTitle}:contains('#{title}')")
    oTitle.filter(->
      if $.trim($(this).text()) is $.trim(title)
        p = $(this).closest 'li'
        oNotif = p.find('#notif.project')
        n = parseInt(oNotif.html())
        if isNaN(n) or n < 0
          n = 1
        else
          n = n + 1
        oNotif.html n
    )

$(document).ready -> 
  new WsNotif $('#build-notif').data('host'), 
    $('#build-notif').data('topic')