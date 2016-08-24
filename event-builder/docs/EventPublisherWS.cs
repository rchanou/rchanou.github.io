Imports WebSocket4Net
Imports Interfaces
Imports Newtonsoft.Json
Imports PubSubContracts
Imports System.Threading.Tasks
Imports System.Threading
Imports Quobject.SocketIoClientDotNet.Client
Imports System.Linq
Imports Newtonsoft.Json.Linq


Public NotInheritable Class EventPublisherWS


    ' Private object with lazy instantiation
    'thread safety first
    Private Shared ReadOnly m_instance As New Lazy(Of EventPublisherWS)(Function() New EventPublisherWS(), System.Threading.LazyThreadSafetyMode.ExecutionAndPublication)

    Private Shared m_ReconnectLogTime As Date
    Private Shared m_ConnectTime As Date
    Private oLockConnection As Object = New Object
    Private oLockPublish As Object = New Object

    ' no public default constructor
    Private publishEventsToPubSub As Boolean = False

    Private mServiceStarted As Boolean = False



    Private Sub New()

    End Sub

    ' static instance property
    Public Shared ReadOnly Property Instance() As EventPublisherWS
        Get
            Return m_instance.Value
        End Get
    End Property



    Private webSocket As Socket = Nothing
    Private tmrReconnect As System.Threading.Timer = Nothing



    Private Sub setup()
        Try
            mServiceStarted = CBool(Interfaces.BusinessLogic.Setting.ControlPanelBL.Instance.GetServerControlPanelSetting("PublishEventsToPubSub").SettingValue)
            publishEventsToPubSub = mServiceStarted
        Catch ex As Exception
            publishEventsToPubSub = False
        End Try
    End Sub
    Public Sub Start()
        setup()

        If publishEventsToPubSub Then
            tmrReconnect = New System.Threading.Timer(AddressOf handleReconnect)

            Dim pubSubURI As String = CSCommon.BusinessLogic.ControlPanel.ControlPanelBL.Instance.GetServerControlPanelSettingValue("PubSubURI")


            If String.IsNullOrWhiteSpace(pubSubURI) Then
                pubSubURI = "ws://192.168.111.197:11137"
                HelperLog.Instance.LogError("Was not able to find pubSubURI setting in Clubspeed\MainEngine\BusinessLogic\EventPublisher\EventPublisherWS.vb")
            End If


            webSocket = IO.Socket(pubSubURI) ' JsonWebSocket(pubSubURI)

            webSocket.On(Socket.EVENT_CONNECT_ERROR, Sub(err)
                                                         LogSomeMessage("MainEngine websocket connection error:  " & err.ToString(), "", "MainEngine")
                                                     End Sub)

            webSocket.On(Socket.EVENT_CONNECT_TIMEOUT, Sub(err)
                                                           LogSomeMessage("MainEngine websocket connection timeout:  " & err.ToString(), "", "MainEngine")
                                                       End Sub)
            webSocket.On(Socket.EVENT_ERROR, Sub(err)
                                                 LogSomeMessage("MainEngine websocket error:  " & err.ToString(), "", "MainEngine")
                                             End Sub)

            webSocket.On(Socket.EVENT_RECONNECT_ERROR, Sub(err)
                                                           LogSomeMessage("MainEngine websocket reconnect error:  " & err.ToString(), "", "MainEngine")
                                                       End Sub)

            webSocket.On(Socket.EVENT_RECONNECT_FAILED, Sub(err)
                                                            LogSomeMessage("MainEngine websocket reconnect failure:  " & err.ToString(), "", "MainEngine")
                                                        End Sub)

            webSocket.On(Socket.EVENT_DISCONNECT, Sub(err)
                                                      LogSomeMessage("MainEngine websocket closed", "", "MainEngine")
                                                      Reconnect()
                                                  End Sub)

            webSocket.On(Socket.EVENT_CONNECT, Sub(err)
                                                   Interfaces.HelperLog.Instance.LogError("MainEngine websocket opened", "", "MainEngine")
                                                   LoginToPubSub()
                                               End Sub)

            ' webSocket.Open()
        End If

    End Sub

    Private Shared Sub LogSomeMessage(ByVal ErrorMessage As String, ByVal UserName As String, ByVal TerminalName As String)
        Interfaces.HelperLog.Instance.LogError(ErrorMessage, UserName, TerminalName)
    End Sub

    Private Sub LoginToPubSub()

        ' Dim loginInfo As New LoginParameter() With {.Username = "support", .Password = "c0d3red"}
        ' Dim publishParameter As New PubSubContracts.Parameters.PublishParameter
        ' Dim dataToSend As String

        'publishParameter.EventName = "Login"
        ' publishParameter.EventData = loginInfo
        'dataToSend = JsonConvert.SerializeObject(publishParameter)
        Dim authenticateModel As New AuthenticateModel
        authenticateModel.Key = "cs-dev"
        PublishEvent("publisher", authenticateModel)

        'Try
        '    Interfaces.HelperLog.Instance.LogError("MainEngine websocket attempting to log in.")
        '    webSocket.Emit("authenticate", dataToSend)
        'Catch ex As Exception
        '    Interfaces.HelperLog.Instance.LogError("MainEngine websocket could not log in: " & dataToSend & ". Exception: " & ex.ToString(), "", "MainEngine")
        'End Try

    End Sub

    Private Sub Reconnect()
        SetTimer(5000, 1000)
    End Sub

    Private Sub handleReconnect()
        SyncLock oLockConnection
            SetTimer(Timeout.Infinite, Timeout.Infinite)
            webSocket.Open()
        End SyncLock
    End Sub

    Private Sub SetTimer(ByVal DueTime As Integer, ByVal Period As Integer)
        tmrReconnect.Change(DueTime, Period)
    End Sub

    'Public Sub PublishEvent(ByVal eventToPublish As ClubSpeedEvents.Responses.Response)

    '    If publishEventsToPubSub Then
    '        If Not eventToPublish Is Nothing Then
    '            SyncLock oLockPublish

    '                'Dim publishParameter As New PubSubContracts.Parameters.PublishParameter
    '                'publishParameter.EventName = eventToPublish.EventName
    '                'publishParameter.EventData = eventToPublish

    '                Dim dataToSend As String = JsonConvert.SerializeObject(eventToPublish)

    '                Interfaces.HelperLog.Instance.LogError("PublishEvent():  " & eventToPublish.EventName)


    '                Interfaces.HelperLog.Instance.LogError("dataToSend():  " & dataToSend)
    '                webSocket.Emit(eventToPublish.EventName, dataToSend)
    '            End SyncLock
    '        End If
    '    End If

    'End Sub



    Public Sub PublishEvent(Of T)(evt As String, ParamArray data As T())
        Dim stuff As Object() = New Object(data.Length - 1) {}
        stuff = data.Select(Of Object)(Function(x) JsonConvert.DeserializeObject(Of JObject)(JsonConvert.SerializeObject(x))).ToArray()
        webSocket.Emit(evt, stuff)
        Interfaces.HelperLog.Instance.LogError("PublishEvent():  " & JsonConvert.SerializeObject(stuff))
    End Sub




    Private Sub handlePublish(ByVal response As PubSubContracts.Result.ResponseMsg)
        If Not response.Success Then
            Interfaces.HelperLog.Instance.LogError("MainEngine websocket couldn't publish: " & response.Message, "", "MainEngine")
        End If
    End Sub

    Private Sub handleLogin(ByVal response As PubSubContracts.Result.ResponseMsg)
        If Not response.Success Then
            Interfaces.HelperLog.Instance.LogError("MainEngine websocket couldn't Login: " & response.Message, "", "MainEngine")
        End If
    End Sub



End Class
