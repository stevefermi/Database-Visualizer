function getAjax(_method,_url,_data,callback){
        $.ajax({
            url:_url,
            type:_method,
            data:_data,
            async:false,
            success:callback,
            error:function(xhr,ajaxOptions,thrownError){
                console.log(xhr);
            }
        });
}

function login(){
    var username = $('#username').val();
    var password = $('#password').val();
    if(username == "" || password == ""){
        alert("请输入账号或密码");
    }
    var data = {"account":username,"password":password};
    console.log(data);
    getAjax("POST",'http://159.203.251.186/users/authentication',data,function(msg){
        if(msg.code ==1){
            console.log(msg);
            var uid = msg.uid;
            localStorage.setItem("uid",uid);
            window.location.href="index.html";
        }
        else{
        }
        
    })
}

function register(){
    var username = $('#username').val();
    var password = $('#password').val();
    if(username == "" || password == ""){
        alert("请输入账号或密码");
    }
    var data = {"account":username,"password":password};
    getAjax( "POST",'http://159.203.251.186/users',data,function(msg){
        console.log(msg);
        if(msg.code ==1){
            var uid = msg.uid;
            localStorage.setItem("uid",uid);
            window.location.href="index.html";
        }
        else{
        }
        
    })  
}

function showResource(uid){
    console.log('http://159.203.251.186/dbhost?uid='+uid);
    getAjax( "GET",'http://159.203.251.186/dbhost?uid='+uid,'',function(msg){
        var data = msg.result;
        //var url = 'dblist.html?dbid=';
       var html="<ul>";
        for(var tmp in data)
        {
            html+="<li>";
            html+='<a href="#">';
            html+='<span data-dbid="';
            html+=data[tmp]['id'];
            html+='">';
            html+=data[tmp]['host'];
            html+=data[tmp]['dbname'];
            html+='</span>';
            html+="</a>";
            html+="</li>";
            var key = 'dbid'+ data[tmp]['id'];
            localStorage.setItem(key,data[tmp]['id']);
            console.log(key); 
        }
        html+="</ul>";
        document.getElementById('resources').innerHTML=html;
    });
}

function add(){
    var host = $('#host').val();
    var dbname = $('#dbname').val();
    var dbport = $('#dbport').val();
    var username = $('#username').val();
    var password = $('#pw').val();
    if(username == "" || pw == "" || host == "" 
        || dbname == "" || dbport == ""){
        alert("请填写完整！");
    }
    else{
    var uid = localStorage.getItem("uid");
    var data = {"host":host,
                "dbname":dbname,
                "dbport":dbport,
                "username":username,
                "password":password, 
                "uid":uid};
        getAjax( "POST",'http://159.203.251.186/dbhost',data,function(msg){
                console.log(msg );
            if(msg.code != "0"){
                var dbid = msg.dbid;
                console.log(msg.dbid);
                localStorage.setItem("dbid",dbid);
            }
            else{
                alert(msg.message);

            }
            
        }) 
    } 
}

 function turnback(){
    window.location.href="index.html";
 }  

  function showContent(url) {
    $.get(url,function (callback) {
      // console.log(callback)
      $('#content').html(callback)
    })
  }   