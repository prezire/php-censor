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
    ctx   = @
    conn  = new ab.Session wsUri, 
      -> 
        conn.subscribe topicUri, (topic, data) ->
          ctx.onSub new WsBuild(data.title, data.type)
      ,
      -> 
        console.log('Something went wrong. Check if the 
          Push Notification Server is still running.')
      ,
      {'skipSubprotocolCheck': true}

  onSub: (wsBuild) -> @render wsBuild.title, wsBuild.type

  #Determins the color of container 
  #based on the build notification type.
  label: (type) ->
    lbl = ''
    switch type
      when 'Create' then lbl           = 'label-success'
      when 'Create Duplicate' then lbl = 'label-primary'
      when 'Delete' then lbl           = 'label-danger'
    lbl

  #@param  string  title  The string to compare to, 
  #in order to render the new notification.  
  render: (title, type) ->
    lbl    = @label type
    sProj  = '.sidebar-menu .treeview-menu.projects'
    sTitle = "#{sProj} li > a > span"
    $("#{sTitle}:contains('#{title}')").filter ->
      if $.trim($(this).text()) is $.trim(title)
        li     = $(this).closest 'li'
        oNotif = li.find('#notif.project')
        oNotif.addClass lbl
        n = parseInt oNotif.html()
        n = if isNaN(n) or n < 0 then 1 else n + 1
        oNotif.html n

$(document).ready -> 
  b = $('#build-notif')
  new WsNotif b.data('host'), b.data('topic')