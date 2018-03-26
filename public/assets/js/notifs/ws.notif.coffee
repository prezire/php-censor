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
          ctx.onSub new WsBuild(data.projectTitle, data.type)
      ,
      -> 
        console.log "Something went wrong. Check if the Build Notification Server is running by executing nohup './bin/console php-censor:build-notification &'
          in the Terminal."
      ,
      {'skipSubprotocolCheck': true}

  onSub: (wsBuild) ->
    @render wsBuild.projectTitle, wsBuild.type

  #Determines the color of the container using the theme's 
  #class name based on the build notification type.
  labelClassName: (type) ->
    className = ''
    switch type
      when 'Create' then className           = 'label-success'
      when 'Create Duplicate' then className = 'label-primary'
      when 'Delete' then className           = 'label-danger'
    className

  #@param  string  projectTitle  The string to compare to, 
  #in order to render the new notification.  
  render: (projectTitle, type) ->
    ctx = @
    sProj  = '.sidebar-menu .treeview-menu.projects'
    sTitle = "#{sProj} li > a > span"
    $("#{sTitle}:contains('#{projectTitle}')").filter ->
      if $.trim($(this).text()) is $.trim(projectTitle)
        li     = $(this).closest 'li'
        oNotif = li.find '#notif.project'
        oNotif.addClass ctx.labelClassName(type)
        n = parseInt oNotif.html()
        n = if isNaN(n) or n < 0 then 1 else n + 1
        oNotif.html n
        ctx.renderUi title, type

  renderUi: (projectTitle, type) ->
    title = 'PHP Censor - Build Notification'
    if !Notify.needsPermission
      msg = "A new build has been created for project #{projectTitle}."
      new Notify(title, {body: msg}).show()
    else if Notify.isSupported()
      Notify.requestPermission null, ->
        console.warn "#{title}: Permission has been denied by the user."

$(document).ready -> 
  b = $('#build-notif')
  if b.length > 0
    new WsNotif b.data('host'), b.data('topic')