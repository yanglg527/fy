function goback(){
    window.history.back()
}

function onStatus(id, action, txt){
    var result = window.confirm("您确认"+txt+"这条任务吗？");
    if (result) {
        let status = 0
        switch (action) {
            case 'end':
            status = 1
            break;
            case 'stop':
            status = 4
            break;

            case 'del':
            status = -1
            break;
        }
        if(status === 0) return false
        $.post(url+"/Mob/Tasks/statusAction", { "id": id, "status": status},
        function(data){
            if (data.status === 0) {
                window.location.reload()
            }else {
                alert(data.msg)
                console.log(data);
            }
        }, "json");
    }
}
