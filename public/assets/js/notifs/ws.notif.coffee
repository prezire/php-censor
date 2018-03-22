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
        console.log('Something went wrong. Check if the 
          Push Notification Server is still running.')
      ,
      {'skipSubprotocolCheck': true}

  onSub: (topic, data) ->
    #TODO: Do anything needed here...
    @render data.title, data.buildNotifType

  #Determins the color of container 
  #based on the build notification type.
  label: (buildNotifType) ->
    lbl = ''
    switch buildNotifType
      when 'Create' then lbl = 'label-success'
      when 'Create Duplicate' then lbl = 'label-primary'
      when 'Delete' then lbl = 'label-danger'
    lbl

  #@param  string  title  The string to compare to, 
  #in order to render a new notification.  
  render: (title, buildNotifType) ->
    lbl = @label buildNotifType
    console.log 'lbl ', lbl
    sProj = '.sidebar-menu .treeview-menu.projects'
    sTitle = "#{sProj} li > a > span"
    oTitle = $("#{sTitle}:contains('#{title}')")
    oTitle.filter(->
      if $.trim($(this).text()) is $.trim(title)
        p = $(this).closest 'li'
        oNotif = p.find('#notif.project')
        oNotif.addClass lbl
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